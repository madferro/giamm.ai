// Crittografia AES-GCM per messaggi in IndexedDB
// Chiave fissa (offuscata ma visibile nel codice sorgente)

const ENCRYPTION_KEY = 'giammai-secret-key-2025-change-me-if-you-want'
const ALGORITHM = 'AES-GCM'
const KEY_LENGTH = 256

let cryptoKey = null

// Deriva una chiave AES-GCM dalla stringa fissa
async function getCryptoKey() {
  if (cryptoKey) return cryptoKey

  // Converti la stringa in bytes
  const encoder = new TextEncoder()
  const keyMaterial = encoder.encode(ENCRYPTION_KEY)

  // Importa come chiave raw
  const importedKey = await crypto.subtle.importKey(
    'raw',
    keyMaterial,
    { name: 'PBKDF2' },
    false,
    ['deriveKey']
  )

  // Deriva una chiave AES-GCM
  cryptoKey = await crypto.subtle.deriveKey(
    {
      name: 'PBKDF2',
      salt: encoder.encode('giammai-salt-2025'),
      iterations: 100000,
      hash: 'SHA-256'
    },
    importedKey,
    { name: ALGORITHM, length: KEY_LENGTH },
    false,
    ['encrypt', 'decrypt']
  )

  return cryptoKey
}

// Cripta un testo
export async function encrypt(plaintext) {
  try {
    const key = await getCryptoKey()
    const encoder = new TextEncoder()
    const data = encoder.encode(plaintext)

    // Genera un IV casuale (12 bytes per GCM)
    const iv = crypto.getRandomValues(new Uint8Array(12))

    // Cripta
    const ciphertext = await crypto.subtle.encrypt(
      { name: ALGORITHM, iv },
      key,
      data
    )

    // Combina IV + ciphertext in un unico array
    const combined = new Uint8Array(iv.length + ciphertext.byteLength)
    combined.set(iv, 0)
    combined.set(new Uint8Array(ciphertext), iv.length)

    // Converti in base64 per salvare come stringa
    return btoa(String.fromCharCode(...combined))
  } catch (error) {
    console.error('Encryption error:', error)
    // Fallback: ritorna il testo in chiaro se la crittografia fallisce
    return plaintext
  }
}

// Decripta un testo
export async function decrypt(ciphertext) {
  try {
    const key = await getCryptoKey()

    // Decodifica da base64
    const combined = Uint8Array.from(atob(ciphertext), c => c.charCodeAt(0))

    // Estrai IV (primi 12 bytes) e ciphertext
    const iv = combined.slice(0, 12)
    const data = combined.slice(12)

    // Decripta
    const decrypted = await crypto.subtle.decrypt(
      { name: ALGORITHM, iv },
      key,
      data
    )

    // Converti bytes in stringa
    const decoder = new TextDecoder()
    return decoder.decode(decrypted)
  } catch (error) {
    console.error('Decryption error:', error)
    // Fallback: ritorna il testo cifrato se la decrittografia fallisce
    // (potrebbe essere un messaggio vecchio non criptato)
    return ciphertext
  }
}
