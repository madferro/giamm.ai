// IndexedDB per gestire rate limiting e chat history
import { encrypt, decrypt } from './crypto.js'

const DB_NAME = 'giammai_db'
const DB_VERSION = 1
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
      }
    }
  })
}

// Salva un messaggio (richiesta o risposta) - CRIPTATO
export async function saveMessage(type, content) {
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
      time: now.toLocaleTimeString('it-IT', { hour: '2-digit', minute: '2-digit' })
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
