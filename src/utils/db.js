// IndexedDB per gestire rate limiting e chat history
import { encrypt, decrypt } from './crypto.js'

const DB_NAME = 'giammai_db'
const DB_VERSION = 2
const STORE_MESSAGES = 'messages'

let db = null

// Inizializza il database
export async function initDB() {
  if (db) return db

  return new Promise((resolve, reject) => {
    const request = indexedDB.open(DB_NAME, DB_VERSION)

    request.onerror = () => reject(request.error)
    request.onsuccess = () => {
      db = request.result
      resolve(db)
    }

    request.onupgradeneeded = (event) => {
      const db = event.target.result

      // Store per messaggi (richieste e risposte)
      if (!db.objectStoreNames.contains(STORE_MESSAGES)) {
        const store = db.createObjectStore(STORE_MESSAGES, {
          keyPath: 'id',
          autoIncrement: true
        })
        // Index per data (per filtrare per giorno)
        store.createIndex('date', 'date', { unique: false })
        // Index per tipo (request/response)
        store.createIndex('type', 'type', { unique: false })
        store.createIndex('timestamp', 'timestamp', { unique: false })
        store.createIndex('sessionId', 'sessionId', { unique: false })
      } else {
        // Upgrade da v1 a v2: aggiungi indice sessionId
        const store = event.target.transaction.objectStore(STORE_MESSAGES)
        if (!store.indexNames.contains('sessionId')) {
          store.createIndex('sessionId', 'sessionId', { unique: false })
        }

        // Migra dati esistenti: assegna sessionId basato sulla data
        const cursorRequest = store.openCursor()
        cursorRequest.onsuccess = (e) => {
          const cursor = e.target.result
          if (cursor) {
            const msg = cursor.value
            if (!msg.sessionId) {
              msg.sessionId = msg.date || new Date().toISOString().split('T')[0]
              cursor.update(msg)
            }
            cursor.continue()
          }
        }
      }
    }
  })
}

// Salva un messaggio (richiesta o risposta) - CRIPTATO
export async function saveMessage(type, content, sessionId) {
  const db = await initDB()
  const now = new Date()
  const today = now.toISOString().split('T')[0] // YYYY-MM-DD

  // Cripta il contenuto prima di salvare
  const encryptedContent = await encrypt(content)

  return new Promise((resolve, reject) => {
    const transaction = db.transaction([STORE_MESSAGES], 'readwrite')
    const store = transaction.objectStore(STORE_MESSAGES)

    const message = {
      type, // 'request' o 'response'
      content: encryptedContent, // Contenuto criptato
      date: today,
      timestamp: now.toISOString(),
      time: now.toLocaleTimeString('it-IT', { hour: '2-digit', minute: '2-digit' }),
      sessionId: sessionId || today
    }

    const request = store.add(message)

    request.onsuccess = () => resolve(request.result)
    request.onerror = () => reject(request.error)
  })
}

// Conta le richieste (solo type='request') fatte oggi
export async function countTodayRequests() {
  const db = await initDB()
  const today = new Date().toISOString().split('T')[0]

  return new Promise((resolve, reject) => {
    const transaction = db.transaction([STORE_MESSAGES], 'readonly')
    const store = transaction.objectStore(STORE_MESSAGES)
    const index = store.index('date')
    const request = index.getAll(today)

    request.onsuccess = () => {
      // Conta solo le richieste (non le risposte)
      const requests = request.result.filter(msg => msg.type === 'request')
      resolve(requests.length)
    }
    request.onerror = () => reject(request.error)
  })
}

// Ottieni tutte le conversazioni (coppie richiesta/risposta)
export async function getAllMessages() {
  const db = await initDB()

  return new Promise((resolve, reject) => {
    const transaction = db.transaction([STORE_MESSAGES], 'readonly')
    const store = transaction.objectStore(STORE_MESSAGES)
    const index = store.index('timestamp')
    const request = index.getAll()

    request.onsuccess = () => resolve(request.result)
    request.onerror = () => reject(request.error)
  })
}

// Cancella messaggi più vecchi di N giorni
export async function cleanOldMessages(daysToKeep = 30) {
  const db = await initDB()
  const cutoffDate = new Date()
  cutoffDate.setDate(cutoffDate.getDate() - daysToKeep)
  const cutoffDateStr = cutoffDate.toISOString().split('T')[0]

  return new Promise((resolve, reject) => {
    const transaction = db.transaction([STORE_MESSAGES], 'readwrite')
    const store = transaction.objectStore(STORE_MESSAGES)
    const index = store.index('date')
    const request = index.openCursor()

    let deletedCount = 0

    request.onsuccess = (event) => {
      const cursor = event.target.result
      if (cursor) {
        if (cursor.value.date < cutoffDateStr) {
          cursor.delete()
          deletedCount++
        }
        cursor.continue()
      } else {
        resolve(deletedCount)
      }
    }

    request.onerror = () => reject(request.error)
  })
}

// Calcola richieste rimanenti per oggi
export async function getRemainingRequests(maxPerDay = 10) {
  const count = await countTodayRequests()
  return Math.max(0, maxPerDay - count)
}

// Ottieni tutti i messaggi di oggi (per ripristinare la conversazione) - DECRIPTATI
export async function getTodayMessages() {
  const db = await initDB()
  const today = new Date().toISOString().split('T')[0]

  return new Promise((resolve, reject) => {
    const transaction = db.transaction([STORE_MESSAGES], 'readonly')
    const store = transaction.objectStore(STORE_MESSAGES)
    const index = store.index('date')
    const request = index.getAll(today)

    request.onsuccess = async () => {
      // Ordina per timestamp per mantenere l'ordine cronologico
      const messages = request.result.sort((a, b) => 
        new Date(a.timestamp) - new Date(b.timestamp)
      )
      
      // Decripta tutti i contenuti
      const decryptedMessages = await Promise.all(
        messages.map(async (msg) => ({
          ...msg,
          content: await decrypt(msg.content)
        }))
      )
      
      resolve(decryptedMessages)
    }
    request.onerror = () => reject(request.error)
  })
}

// Ottieni messaggi per una data specifica - DECRIPTATI
export async function getMessagesByDate(dateStr) {
  const db = await initDB()

  return new Promise((resolve, reject) => {
    const transaction = db.transaction([STORE_MESSAGES], 'readonly')
    const store = transaction.objectStore(STORE_MESSAGES)
    const index = store.index('date')
    const request = index.getAll(dateStr)

    request.onsuccess = async () => {
      // Ordina per timestamp per mantenere l'ordine cronologico
      const messages = request.result.sort((a, b) => 
        new Date(a.timestamp) - new Date(b.timestamp)
      )
      
      // Decripta tutti i contenuti
      const decryptedMessages = await Promise.all(
        messages.map(async (msg) => ({
          ...msg,
          content: await decrypt(msg.content)
        }))
      )
      
      resolve(decryptedMessages)
    }
    request.onerror = () => reject(request.error)
  })
}

// Ottieni tutte le date che hanno messaggi (per la cronologia)
export async function getChatDates() {
  const db = await initDB()

  return new Promise((resolve, reject) => {
    const transaction = db.transaction([STORE_MESSAGES], 'readonly')
    const store = transaction.objectStore(STORE_MESSAGES)
    const index = store.index('date')
    const request = index.openCursor(null, 'prev') // Ordine decrescente (più recenti prima)

    const dates = new Set()

    request.onsuccess = (event) => {
      const cursor = event.target.result
      if (cursor) {
        dates.add(cursor.value.date)
        cursor.continue()
      } else {
        // Converti Set in Array e ordina per data decrescente
        const sortedDates = Array.from(dates).sort((a, b) => b.localeCompare(a))
        resolve(sortedDates)
      }
    }

    request.onerror = () => reject(request.error)
  })
}

// Ottieni anteprima della prima richiesta per una data (per mostrare nella sidebar)
export async function getChatPreview(dateStr) {
  const db = await initDB()

  return new Promise((resolve, reject) => {
    const transaction = db.transaction([STORE_MESSAGES], 'readonly')
    const store = transaction.objectStore(STORE_MESSAGES)
    const index = store.index('date')
    const request = index.getAll(dateStr)

    request.onsuccess = async () => {
      const messages = request.result.sort((a, b) =>
        new Date(a.timestamp) - new Date(b.timestamp)
      )

      // Trova la prima richiesta
      const firstRequest = messages.find(msg => msg.type === 'request')

      if (firstRequest) {
        const decryptedContent = await decrypt(firstRequest.content)
        // Tronca a 50 caratteri per l'anteprima
        const preview = decryptedContent.length > 50
          ? decryptedContent.substring(0, 50) + '...'
          : decryptedContent

        resolve({
          date: dateStr,
          preview,
          timestamp: firstRequest.timestamp,
          messageCount: messages.length
        })
      } else {
        resolve(null)
      }
    }
    request.onerror = () => reject(request.error)
  })
}

// === NUOVE FUNZIONI PER SESSIONI ===

// Ottieni tutte le sessioni uniche ordinate per timestamp (più recenti prima)
export async function getChatSessions() {
  const db = await initDB()

  return new Promise((resolve, reject) => {
    const transaction = db.transaction([STORE_MESSAGES], 'readonly')
    const store = transaction.objectStore(STORE_MESSAGES)
    const index = store.index('timestamp')
    const request = index.openCursor(null, 'prev')

    const sessions = new Map() // sessionId -> { sessionId, date, timestamp, messageCount }

    request.onsuccess = (event) => {
      const cursor = event.target.result
      if (cursor) {
        const msg = cursor.value
        if (!sessions.has(msg.sessionId)) {
          sessions.set(msg.sessionId, {
            sessionId: msg.sessionId,
            date: msg.date,
            timestamp: msg.timestamp,
            messageCount: 0
          })
        }
        sessions.get(msg.sessionId).messageCount++
        cursor.continue()
      } else {
        // Ordina per timestamp decrescente
        const sortedSessions = Array.from(sessions.values())
          .sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp))
        resolve(sortedSessions)
      }
    }

    request.onerror = () => reject(request.error)
  })
}

// Ottieni anteprima di una sessione specifica
export async function getSessionPreview(sessionId) {
  const db = await initDB()

  return new Promise((resolve, reject) => {
    const transaction = db.transaction([STORE_MESSAGES], 'readonly')
    const store = transaction.objectStore(STORE_MESSAGES)
    const index = store.index('sessionId')
    const request = index.getAll(sessionId)

    request.onsuccess = async () => {
      const messages = request.result.sort((a, b) =>
        new Date(a.timestamp) - new Date(b.timestamp)
      )

      // Trova la prima richiesta
      const firstRequest = messages.find(msg => msg.type === 'request')

      if (firstRequest) {
        const decryptedContent = await decrypt(firstRequest.content)
        const preview = decryptedContent.length > 50
          ? decryptedContent.substring(0, 50) + '...'
          : decryptedContent

        resolve({
          sessionId,
          date: firstRequest.date,
          preview,
          timestamp: firstRequest.timestamp,
          messageCount: messages.length
        })
      } else {
        resolve(null)
      }
    }
    request.onerror = () => reject(request.error)
  })
}

// Ottieni tutti i messaggi di una sessione specifica - DECRIPTATI
export async function getMessagesBySession(sessionId) {
  const db = await initDB()

  return new Promise((resolve, reject) => {
    const transaction = db.transaction([STORE_MESSAGES], 'readonly')
    const store = transaction.objectStore(STORE_MESSAGES)
    const index = store.index('sessionId')
    const request = index.getAll(sessionId)

    request.onsuccess = async () => {
      const messages = request.result.sort((a, b) =>
        new Date(a.timestamp) - new Date(b.timestamp)
      )

      const decryptedMessages = await Promise.all(
        messages.map(async (msg) => ({
          ...msg,
          content: await decrypt(msg.content)
        }))
      )

      resolve(decryptedMessages)
    }
    request.onerror = () => reject(request.error)
  })
}
