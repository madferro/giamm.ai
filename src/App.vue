<script setup>
import { ref, onMounted, onBeforeUnmount, watch, nextTick } from 'vue'
import WelcomeScreen from './components/WelcomeScreen.vue'
import ChatMessages from './components/ChatMessages.vue'
import ChatInput from './components/ChatInput.vue'
import PrivacyModal from './components/PrivacyModal.vue'
import { initDB, saveMessage, getRemainingRequests, cleanOldMessages, getTodayMessages } from './utils/db.js'

const messages = ref([])
const loading = ref(false)
const error = ref('')
const remainingRequests = ref(10)
const hasMessages = ref(false)
const dark = ref(false)
const tone = ref('ironic')
const showScrollButton = ref(false)
const showPrivacy = ref(false)

// Blocca/sblocca scroll quando si apre/chiude il modal privacy
watch(showPrivacy, (newVal) => {
  if (newVal) {
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = ''
  }
})

onMounted(async () => {
  await initDB()
  await loadTodayConversations() // Carica conversazioni di oggi
  await checkRateLimit()
  await cleanOldMessages(30) // Pulisci messaggi più vecchi di 30 giorni
  dark.value = document.documentElement.classList.contains('dark')
  window.addEventListener('scroll', onWindowScroll, { passive: true })
})

onBeforeUnmount(() => {
  window.removeEventListener('scroll', onWindowScroll)
})

function scrollToBottom() {
  window.scrollTo({ top: document.documentElement.scrollHeight, behavior: 'smooth' })
}

function onWindowScroll() {
  const scrollY = window.scrollY || document.documentElement.scrollTop
  const viewportH = window.innerHeight
  const docH = document.documentElement.scrollHeight
  const threshold = 120
  showScrollButton.value = docH - scrollY - viewportH > threshold
}

watch(() => messages.value.length, () => {
  nextTick(() => {
    const scrollY = window.scrollY || document.documentElement.scrollTop
    const viewportH = window.innerHeight
    const docH = document.documentElement.scrollHeight
    const threshold = 120
    if (docH - scrollY - viewportH < threshold) {
      scrollToBottom()
    }
  })
})

watch(loading, (val) => {
  if (val) {
    nextTick(() => {
      const scrollY = window.scrollY || document.documentElement.scrollTop
      const viewportH = window.innerHeight
      const docH = document.documentElement.scrollHeight
      const threshold = 120
      if (docH - scrollY - viewportH < threshold) {
        scrollToBottom()
      }
    })
  }
})

function toggleTheme() {
  dark.value = !dark.value
  document.documentElement.classList.toggle('dark', dark.value)
  localStorage.setItem('giammai-theme', dark.value ? 'dark' : 'light')
}

async function loadTodayConversations() {
  try {
    const todayMessages = await getTodayMessages()
    
    if (todayMessages.length > 0) {
      hasMessages.value = true
      
      // Converte i messaggi da IndexedDB al formato UI
      messages.value = todayMessages.map((msg, index) => ({
        id: Date.now() + index, // ID univoco per Vue
        role: msg.type === 'request' ? 'user' : 'assistant',
        content: msg.content,
        time: msg.time
      }))
      
      // Scroll automatico in fondo dopo aver caricato i messaggi
      await nextTick()
      scrollToBottom()
    }
  } catch (e) {
    console.error('Error loading today conversations:', e)
  }
}

async function checkRateLimit() {
  try {
    remainingRequests.value = await getRemainingRequests(10)
  } catch (e) {
    console.error('Error checking rate limit:', e)
  }
}

async function sendMessage(text) {
  if (!text.trim() || loading.value) return
  if (remainingRequests.value <= 0) {
    error.value = 'Hai esaurito le 10 richieste giornaliere'
    setTimeout(() => error.value = '', 5000)
    return
  }
  
  hasMessages.value = true
  const userMsg = {
    id: Date.now(), 
    role: 'user', 
    content: text.trim(),
    time: new Date().toLocaleTimeString('it-IT', { hour: '2-digit', minute: '2-digit' })
  }
  messages.value.push(userMsg)
  
  // Scroll dopo aver aggiunto il messaggio utente
  await nextTick()
  scrollToBottom()
  
  loading.value = true
  error.value = ''
  
  // Scroll dopo aver attivato il loading (mostra "sta ragionando...")
  await nextTick()
  scrollToBottom()
  
  try {
    // Salva la richiesta in IndexedDB (conta per il rate limit)
    await saveMessage('request', text.trim())
    
    // Aggiorna il contatore
    await checkRateLimit()
    
    // Chiama l'API
    const res = await fetch('/api/chat.php', {
      method: 'POST', 
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ message: text.trim(), tone: tone.value })
    })
    const data = await res.json()
    
    if (data.success) {
      const assistantMsg = {
        id: Date.now() + 1, 
        role: 'assistant', 
        content: data.response,
        time: new Date().toLocaleTimeString('it-IT', { hour: '2-digit', minute: '2-digit' })
      }
      messages.value.push(assistantMsg)
      
      // Scroll dopo aver aggiunto la risposta
      await nextTick()
      scrollToBottom()
      
      // Salva la risposta in IndexedDB (NON conta per il rate limit)
      await saveMessage('response', data.response)
    } else {
      error.value = data.error || 'Errore'
      setTimeout(() => error.value = '', 5000)
    }
  } catch (err) {
    console.error('Chat error:', err)
    error.value = 'Connessione fallita'
    setTimeout(() => error.value = '', 5000)
  } finally { 
    loading.value = false 
  }
}
</script>

<template>
  <div class="min-h-screen flex flex-col bg-bg-primary text-text-primary">
    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 z-50 h-14 backdrop-blur-xl border-b border-border bg-bg-primary/85 transition-colors duration-200">
      <div class="container mx-auto px-5 h-full flex items-center justify-between">
        <a href="/" class="flex items-center gap-2 font-bold text-lg tracking-tight text-text-primary">
          giamm<span class="w-2 h-2 rounded-full bg-accent inline-block"></span>ai
        </a>
        <!-- Scroll to bottom button -->
        <Transition
          enter-active-class="transition ease-out duration-200"
          enter-from-class="opacity-0 -translate-y-2"
          enter-to-class="opacity-100 translate-y-0"
          leave-active-class="transition ease-in duration-150"
          leave-from-class="opacity-100 translate-y-0"
          leave-to-class="opacity-0 -translate-y-2"
        >
          <button
            v-if="showScrollButton"
            @click="scrollToBottom"
            class="w-10 h-10 rounded-full bg-bg-secondary border border-border flex items-center justify-center text-text-secondary hover:text-text-primary hover:bg-bg-tertiary transition-all cursor-pointer z-40"
          >
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
              <polyline points="6 9 12 15 18 9"/>
            </svg>
          </button>
        </Transition>
        <div class="flex items-center gap-2">
          <div class="text-xs font-medium text-text-muted bg-bg-secondary px-3 py-1.5 rounded-full border border-border">
            <strong class="text-accent">{{ remainingRequests }}</strong> / 10
          </div>
          <button
            type="button"
            @click="showPrivacy = true"
            class="w-9 h-9 rounded-lg border border-border bg-bg-secondary flex items-center justify-center text-text-secondary hover:text-accent hover:border-accent/50 transition-colors cursor-pointer"
            title="Privacy & Sicurezza"
          >
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
              <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
              <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
          </button>
          <button @click="toggleTheme" class="w-9 h-9 rounded-lg border border-border bg-bg-secondary flex items-center justify-center text-text-secondary hover:text-text-primary hover:bg-bg-tertiary transition-colors cursor-pointer">
            <svg v-if="dark" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
            <svg v-else width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
          </button>
        </div>
      </div>
    </header>

    <!-- Privacy Sidebar -->
    <Transition
      enter-active-class="transition-opacity ease-out duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity ease-in duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="showPrivacy" class="fixed inset-0 z-[60]" @click="showPrivacy = false">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
      </div>
    </Transition>

    <Transition
      enter-active-class="transition-transform ease-out duration-300"
      enter-from-class="translate-x-full"
      enter-to-class="translate-x-0"
      leave-active-class="transition-transform ease-in duration-200"
      leave-from-class="translate-x-0"
      leave-to-class="translate-x-full"
    >
      <div v-if="showPrivacy" class="fixed top-0 right-0 bottom-0 z-[70] w-full max-w-md bg-bg-primary border-l border-border shadow-2xl flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-5 border-b border-border">
          <h2 class="text-xl font-semibold text-text-primary flex items-center gap-2">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" class="text-accent">
              <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
              <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
            Privacy & Sicurezza
          </h2>
          <button
            type="button"
            @click="showPrivacy = false"
            class="w-8 h-8 rounded-lg flex items-center justify-center text-text-muted hover:text-text-primary hover:bg-bg-secondary transition-colors cursor-pointer"
          >
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
              <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
          </button>
        </div>

        <!-- Content scrollabile -->
        <div class="flex-1 overflow-y-auto px-6 py-5">
          <div class="space-y-5 text-sm text-text-secondary leading-relaxed">
            <div>
              <h3 class="font-semibold text-text-primary mb-1.5 flex items-center gap-2">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="text-accent"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                Cosa succede ai tuoi messaggi?
              </h3>
              <p>I tuoi messaggi vengono inviati a <strong>Groq AI</strong> per essere elaborati. È un po' come quando chiedi a un amico un consiglio, ma l'amico è un supercomputer che vive in un datacenter in Irlanda.</p>
            </div>

            <div>
              <h3 class="font-semibold text-text-primary mb-1.5 flex items-center gap-2">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="text-accent"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                I tuoi dati sono al sicuro?
              </h3>
              <p>Sì. <strong>Tutte le conversazioni sono salvate solo nel tuo browser</strong> (IndexedDB) e <strong>criptate con AES-256-GCM</strong>. Non salviamo nulla sui nostri server. Non sappiamo chi sei, non ti spiamo e non vendiamo nulla a nessuno. Anche se qualcuno accedesse fisicamente al tuo database locale, vedrebbe solo dati criptati.</p>
            </div>

            <div>
              <h3 class="font-semibold text-text-primary mb-1.5 flex items-center gap-2">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="text-accent"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                Come funziona la crittografia?
              </h3>
              <p>Ogni messaggio viene criptato con <strong>AES-256-GCM</strong> (lo stesso standard usato dalle banche) prima di essere salvato nel tuo browser. Quando riapri la chat, i messaggi vengono automaticamente decriptati solo per te. È come avere una cassaforte digitale nel tuo browser.</p>
            </div>

            <div>
              <h3 class="font-semibold text-text-primary mb-1.5 flex items-center gap-2">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="text-accent"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Quanto restano i dati?
              </h3>
              <p>Le conversazioni vengono cancellate automaticamente dopo <strong>30 giorni</strong>. Poi spariscono per sempre, come i tuoi buoni propositi di gennaio.</p>
            </div>

            <div>
              <h3 class="font-semibold text-text-primary mb-1.5 flex items-center gap-2">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="text-accent"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                Groq AI
              </h3>
              <p>I messaggi passano attraverso i server di Groq. Se vuoi saperne di più sulla loro privacy policy, visita <a href="https://groq.com/privacy" target="_blank" rel="noopener noreferrer" class="text-accent hover:underline">groq.com/privacy</a>.</p>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-border">
          <button
            type="button"
            @click="showPrivacy = false"
            class="w-full px-4 py-2.5 rounded-lg bg-accent text-white text-sm font-medium hover:bg-accent-hover transition-colors cursor-pointer"
          >
            Ooook ho capito
          </button>
        </div>
      </div>
    </Transition>

    <!-- Main -->
    <main class="flex-1 flex flex-col pt-14">

      <!-- === EMPTY STATE === -->
      <template v-if="!hasMessages">
        <div class="flex-1 flex flex-col items-center justify-center px-4">
          <WelcomeScreen />
          <div class="w-full max-w-2xl mt-8">
            <ChatInput
              v-model:tone="tone"
              @send="sendMessage"
              :disabled="loading || remainingRequests <= 0"
              :placeholder="remainingRequests <= 0 ? 'Limite giornaliero raggiunto' : 'Scrivi un messaggio...'"
            />
            <p class="text-center text-xs text-text-muted mt-2">giamm.ai può sbagliare, ma nel dubbio... non farlo.</p>
          </div>
        </div>
      </template>

      <!-- === CHAT STATE === -->
      <template v-else>
        <!-- Messages area -->
        <div class="flex-1 flex flex-col pb-28">
          <ChatMessages :messages="messages" :loading="loading" />
          <!-- Error -->
          <div v-if="error" class="container mx-auto px-5 my-3">
            <div class="px-4 py-3 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 text-sm flex items-center gap-2">
              <svg class="shrink-0" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
              <span>{{ error }}</span>
            </div>
          </div>
        </div>

        <!-- Input at bottom -->
        <div class="border-t border-border bg-bg-primary px-4 py-4 fixed w-full left-0 bottom-0">
          <div class="container mx-auto">
            <ChatInput
              v-model:tone="tone"
              @send="sendMessage"
              :disabled="loading || remainingRequests <= 0"
              :placeholder="remainingRequests <= 0 ? 'Limite giornaliero raggiunto' : 'Scrivi un messaggio...'"
            />
            <p class="text-center text-xs text-text-muted mt-2">giamm.ai può sbagliare, ma nel dubbio... non farlo.</p>
          </div>
        </div>
      </template>

    </main>

    <!-- Privacy Modal Sbarrante -->
    <PrivacyModal @accept="() => {}" />
  </div>
</template>
