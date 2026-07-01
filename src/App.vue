<script setup>
import { ref, onMounted, onBeforeUnmount, watch, nextTick } from 'vue'
import WelcomeScreen from './components/WelcomeScreen.vue'
import ChatMessages from './components/ChatMessages.vue'
import ChatInput from './components/ChatInput.vue'
import PrivacyModal from './components/PrivacyModal.vue'
import Sidebar from './components/Sidebar.vue'
import AboutModal from './components/AboutModal.vue'
import CreditsModal from './components/CreditsModal.vue'
import ShareModal from './components/ShareModal.vue'
import LimitsModal from './components/LimitsModal.vue'
import { initDB, saveMessage, getRemainingRequests, cleanOldMessages, getTodayMessages, getMessagesBySession, deleteSession, deleteAllSessions } from './utils/db.js'
import ConfirmDialog from './components/ConfirmDialog.vue'

const messages = ref([])
const loading = ref(false)
const error = ref('')
const remainingRequests = ref(10)
const hasMessages = ref(false)
const dark = ref(false)
const tone = ref('ironic')
const showScrollButton = ref(false)
const showPrivacy = ref(false)
const showSidebar = ref(false)
const showAbout = ref(false)
const showCredits = ref(false)
const showShare = ref(false)
const showLimits = ref(false)
const currentChatDate = ref(null)
const currentSessionId = ref(null)
const sidebarRef = ref(null)
const sidebarCollapsed = ref(false)
const confirmDialog = ref(null)

// Blocca/sblocca scroll quando si apre/chiude il modal privacy o about
watch([showPrivacy, showAbout, showCredits, showShare, showLimits, showSidebar], ([privacy, about, credits, share, limits, sidebar]) => {
  // Su mobile, blocca scroll se sidebar è aperta
  // Su desktop, blocca scroll solo se un modal è aperto (non per la sidebar)
  const isMobile = window.innerWidth < 1024

  if (privacy || about || credits || share || limits || (sidebar && isMobile)) {
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = ''
  }
})

onMounted(async () => {
  await initDB()
  await loadTodayConversations() // Carica conversazioni di oggi
  // Se non c'è una sessione attiva (nessun messaggio salvato), generane una nuova
  if (!currentSessionId.value) {
    currentSessionId.value = generateSessionId()
  }
  currentChatDate.value = new Date().toISOString().split('T')[0] // Imposta data corrente
  await checkRateLimit()
  await cleanOldMessages(30) // Pulisci messaggi più vecchi di 30 giorni
  dark.value = document.documentElement.classList.contains('dark')
  window.addEventListener('scroll', onWindowScroll, { passive: true })
  
  // Carica preferenza sidebar collapsed da localStorage
  const savedCollapsed = localStorage.getItem('giammai-sidebar-collapsed')
  if (savedCollapsed === 'true') {
    sidebarCollapsed.value = true
  }
  
  // Apri sidebar automaticamente su schermi grandi
  if (window.innerWidth >= 1024) {
    showSidebar.value = true
  }
  
  // Gestisci resize per aprire/chiudere sidebar
  window.addEventListener('resize', handleResize)
})

onBeforeUnmount(() => {
  window.removeEventListener('scroll', onWindowScroll)
  window.removeEventListener('resize', handleResize)
})

function handleResize() {
  if (window.innerWidth >= 1024) {
    showSidebar.value = true
  } else {
    showSidebar.value = false
  }
  // Controlla se mostrare/nascondere il pulsante scroll-to-bottom
  onWindowScroll()
}



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

function toggleTheme(event) {
  dark.value = !dark.value
  document.documentElement.classList.toggle('dark', dark.value)
  localStorage.setItem('giammai-theme', dark.value ? 'dark' : 'light')
  // Rimuovi focus per nascondere il tooltip (currentTarget punta sempre al button)
  event.currentTarget.blur()
}

async function loadTodayConversations() {
  try {
    const todayMessages = await getTodayMessages()

    if (todayMessages.length > 0) {
      hasMessages.value = true

      // Usa la sessionId del primo messaggio come sessione corrente
      currentSessionId.value = todayMessages[0].sessionId || todayMessages[0].date

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

async function loadChatBySession(sessionId) {
  try {
    const chatMessages = await getMessagesBySession(sessionId)

    currentSessionId.value = sessionId
    currentChatDate.value = chatMessages[0]?.date || new Date().toISOString().split('T')[0]

    if (chatMessages.length > 0) {
      hasMessages.value = true

      // Converte i messaggi da IndexedDB al formato UI
      messages.value = chatMessages.map((msg, index) => ({
        id: Date.now() + index,
        role: msg.type === 'request' ? 'user' : 'assistant',
        content: msg.content,
        time: msg.time
      }))

      // Scroll automatico in fondo dopo aver caricato i messaggi
      await nextTick()
      scrollToBottom()
    } else {
      // Nessun messaggio per questa sessione
      messages.value = []
      hasMessages.value = false
    }
  } catch (e) {
    console.error('Error loading chat by session:', e)
  }
}

function generateSessionId() {
  // Genera un ID univoco: timestamp + random
  return Date.now().toString(36) + Math.random().toString(36).substring(2, 8)
}

function handleChatDeleted(deletedSessionId) {
  // Se la chat cancellata è quella corrente, svuota la UI
  if (deletedSessionId === currentSessionId.value) {
    messages.value = []
    hasMessages.value = false
    currentSessionId.value = generateSessionId()
    currentChatDate.value = new Date().toISOString().split('T')[0]
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

function handleAllChatsDeleted() {
  // Svuota la UI e genera una nuova sessione
  messages.value = []
  hasMessages.value = false
  currentSessionId.value = generateSessionId()
  currentChatDate.value = new Date().toISOString().split('T')[0]
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

async function handleDeleteCurrentChat() {
  if (!currentSessionId.value) return
  const confirmed = await confirmDialog.value.open({
    title: 'Cancella chat',
    message: 'Sicuro di voler cancellare questa chat? Le domande fatte oggi rimarranno conteggiate.',
    confirmText: 'Cancella',
    cancelText: 'Annulla',
    dangerous: true
  })
  if (!confirmed) return
  await deleteSession(currentSessionId.value)
  messages.value = []
  hasMessages.value = false
  currentSessionId.value = generateSessionId()
  currentChatDate.value = new Date().toISOString().split('T')[0]
  if (sidebarRef.value) sidebarRef.value.loadChatHistory()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

function startNewChat() {
  messages.value = []
  hasMessages.value = false
  currentSessionId.value = generateSessionId()
  currentChatDate.value = new Date().toISOString().split('T')[0]

  // Scroll to top
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

function toggleSidebar() {
  showSidebar.value = !showSidebar.value
  
  // Ricarica la cronologia quando si apre la sidebar
  if (showSidebar.value && sidebarRef.value) {
    nextTick(() => {
      sidebarRef.value.loadChatHistory()
    })
  }
}

function toggleSidebarCollapse() {
  sidebarCollapsed.value = !sidebarCollapsed.value
  // Salva la preferenza in localStorage
  localStorage.setItem('giammai-sidebar-collapsed', sidebarCollapsed.value ? 'true' : 'false')
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
    await saveMessage('request', text.trim(), currentSessionId.value)
    
    // Aggiorna la cronologia nella sidebar (mostra subito la nuova chat)
    if (sidebarRef.value) {
      sidebarRef.value.loadChatHistory()
    }
    
    // Aggiorna il contatore
    await checkRateLimit()
    
    // Chiama l'API (Cloudflare Worker)
    const res = await fetch('/api/chat', {
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
      await saveMessage('response', data.response, currentSessionId.value)
      
      // Aggiorna la cronologia nella sidebar
      if (sidebarRef.value) {
        sidebarRef.value.loadChatHistory()
      }
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
  <div class="min-h-screen flex bg-bg-primary text-text-primary">
    <!-- Sidebar -->
    <Sidebar
      ref="sidebarRef"
      :is-open="showSidebar"
      :is-collapsed="sidebarCollapsed"
      :current-session-id="currentSessionId"
      @close="showSidebar = false"
      @load-chat="loadChatBySession"
      @show-privacy="showPrivacy = true"
      @show-about="showAbout = true"
      @show-credits="showCredits = true"
      @show-share="showShare = true"
      @show-limits="showLimits = true"
      @new-chat="startNewChat"
      @toggle-collapse="toggleSidebarCollapse"
      @chat-deleted="handleChatDeleted"
      @all-chats-deleted="handleAllChatsDeleted"
    />

    <!-- Main Content Area -->
    <div 
      :class="[
        'flex-1 flex flex-col transition-all ease-out duration-200',
        sidebarCollapsed ? 'lg:ml-14' : 'lg:ml-72'
      ]"
      :style="{ transitionDelay: sidebarCollapsed ? '200ms' : '0ms' }"
    >
      <!-- About Modal -->
      <AboutModal
        :show="showAbout"
        @close="showAbout = false"
      />

      <!-- Credits Modal -->
      <CreditsModal
        :show="showCredits"
        @close="showCredits = false"
      />

      <!-- Share Modal -->
      <ShareModal
        :show="showShare"
        @close="showShare = false"
      />

      <!-- Limits Modal -->
      <LimitsModal
        :show="showLimits"
        @close="showLimits = false"
      />

      <!-- Confirm Dialog -->
      <ConfirmDialog ref="confirmDialog" />

      <!-- Header -->
      <header 
        :class="[
          'fixed top-0 right-0 left-0 z-50 transition-all ease-out duration-200',
          sidebarCollapsed ? 'lg:left-14' : 'lg:left-72'
        ]"
        :style="{ transitionDelay: sidebarCollapsed ? '200ms' : '0ms' }"
      >
        <div class="px-5 pt-5 pb-5 lg:pb-0 h-full flex items-center justify-between bg-bg-primary/40 lg:bg-transparent backdrop-blur-sm lg:backdrop-blur-none">
          <div class="flex items-center">
            <!-- Hamburger Menu (solo mobile) -->
            <button
              @click="toggleSidebar"
              class="lg:hidden w-9 h-9 rounded-lg flex items-center justify-center text-text-secondary hover:text-text-primary hover:bg-bg-tertiary transition-colors cursor-pointer"
              title="Menu"
            >
              <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M3 12h18M3 6h18M3 18h18"/>
              </svg>
            </button>
            <a href="/" class="lg:hidden flex items-center gap-1 pl-1 font-bold text-sm tracking-tight text-text-primary">
              <span>giamm</span><span class="w-2 h-2 rounded-full bg-accent"></span><span>ai</span>
            </a>
          </div>
          <div class="flex items-center gap-2">
            
          <!-- Pulsante Cancella Chat (visibile solo se ci sono messaggi) -->
          <button
            v-if="hasMessages"
            @click="handleDeleteCurrentChat"
            class="tooltip-giammai py-1.5 tracking-tight px-3 gap-1 text-sm rounded-lg border border-border bg-bg-secondary flex items-center justify-center text-text-secondary hover:text-white hover:bg-red-400 transition-colors cursor-pointer"
            aria-label="Cancella questa chat" data-balloon-pos="left"
          >
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4">
            <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 1 0 1.5.06l.3-7.5Z" clip-rule="evenodd" />
          </svg>
            Elimina
          </button>
          <div class="text-sm font-medium text-text-muted bg-bg-secondary px-3 rounded-lg border border-border flex items-center h-9">
            <span class="hidden lg:inline">Domande a disposizione oggi: </span><span class="text-accent ml-1 font-bold">{{ remainingRequests }}</span>/10
          </div>
          <button 
            @click="toggleTheme" 
            class="tooltip-giammai w-9 h-9 rounded-lg border border-border bg-bg-secondary flex items-center justify-center text-text-secondary hover:text-text-primary hover:bg-bg-tertiary transition-colors cursor-pointer"
            :aria-label="dark ? 'Passa al tema chiaro' : 'Passa al tema scuro'" 
            data-balloon-pos="down-right"
          >
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
      <div v-if="showPrivacy" class="fixed top-0 right-0 bottom-0 z-[70] w-full max-w-3xl bg-bg-primary border-l border-border shadow-2xl flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-5 border-b border-border">
          <h2 class="text-lg font-bold text-text-primary flex items-center gap-2">
            Privacy e Sicurezza
          </h2>
          <button
            type="button"
            @click="showPrivacy = false"
            class="cursor-pointer w-10 h-10 rounded-lg flex items-center justify-center text-text-secondary hover:text-text-primary hover:bg-bg-secondary transition-colors"
          >
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M18 6L6 18M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <!-- Content scrollabile -->
        <div class="flex-1 overflow-y-auto px-6 py-5">
          <div class="space-y-5 text-sm text-text-secondary leading-relaxed">
            <div>
              <h3 class="font-semibold text-lg text-accent mb-1.5 flex items-center gap-1">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="text-accent"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                <span class="font-bold text-text-primary text-sm">
                  Cosa succede ai tuoi messaggi?
                </span>
              </h3>
              <p>I tuoi messaggi vengono inviati a <strong>Groq AI</strong> per essere elaborati. È un po' come quando chiedi a un amico un consiglio, ma l'amico è un supercomputer che vive in un datacenter in Irlanda.</p>
            </div>

            <div>
              <h3 class="font-semibold text-lg text-accent mb-1.5 flex items-center gap-1">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="text-accent"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <span class="font-bold text-text-primary text-sm">
                  I tuoi dati sono al sicuro?
                </span>
              </h3>
              <p>Sì. <strong>Tutte le conversazioni sono salvate <strong>criptate</strong> nel tuo browser</strong>. Non salviamo nulla sui nostri server. Non sappiamo chi sei, non ti spiamo e non vendiamo nulla a nessuno. Anche se qualcuno accedesse fisicamente al tuo database locale, vedrebbe solo dati criptati.</p>
            </div>

            <div>
              <h3 class="font-semibold text-lg text-accent mb-1.5 flex items-center gap-1">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="text-accent"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                <span class="font-bold text-text-primary text-sm">
                  Come funziona la crittografia?
                </span>
              </h3>
              <p>Ogni messaggio viene criptato con uno dei metodi più sicuri al mondo (lo stesso standard usato dalle banche) prima di essere salvato nel tuo browser. Quando riapri la chat, i messaggi vengono automaticamente decriptati solo per te. È come avere una cassaforte digitale nel tuo browser.</p>
            </div>

            <div>
              <h3 class="font-semibold text-lg text-accent mb-1.5 flex items-center gap-1">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="text-accent"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <span class="font-bold text-text-primary text-sm">
                  Quanto restano i dati?
                </span>
              </h3>
              <p>Le conversazioni vengono cancellate automaticamente dopo <strong>30 giorni</strong>. Poi spariscono per sempre, come i tuoi buoni propositi di gennaio.</p>
            </div>

            <div>
              <h3 class="font-semibold text-lg text-accent mb-1.5 flex items-center gap-1">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="text-accent"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                <span class="font-bold text-text-primary text-sm">
                  Groq AI
                </span>
              </h3>
              <p>I messaggi passano attraverso i server di Groq. Se vuoi saperne di più sulla loro privacy policy, visita <a href="https://groq.com/privacy" target="_blank" rel="noopener noreferrer" class="text-accent hover:underline">groq.com/privacy</a>.</p>
            </div>

            <div>
              <h3 class="font-semibold text-lg text-accent mb-1.5 flex items-center gap-1">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="text-accent">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15a4.5 4.5 0 0 0 4.5 4.5H18a3.75 3.75 0 0 0 1.332-7.257 3 3 0 0 0-3.758-3.848 5.25 5.25 0 0 0-10.233 2.33A4.502 4.502 0 0 0 2.25 15Z" />
                </svg>
                <span class="font-bold text-text-primary text-sm">
                  Cloudflare
                </span>
              </h3>
              <p>L'applicazione è distribuita tramite Cloudflare per garantire velocità e sicurezza. Cloudflare può utilizzare cookie tecnici necessari alla protezione del servizio. Per maggiori informazioni: <a href="https://www.cloudflare.com/privacypolicy/" target="_blank" rel="noopener noreferrer" class="text-accent hover:underline">cloudflare.com/privacypolicy</a>.</p>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-border">
          <p class="text-sm text-text-muted text-center">
            I tuoi dati sono al sicuro. <span class="text-accent">Più sicuri delle tue password</span>, probabilmente.
          </p>
        </div>
        <div class="px-6 py-4 border-t border-border">
          <button
            type="button"
            @click="showPrivacy = false"
            class="cursor-pointer w-full px-4 py-2.5 rounded-lg bg-accent text-white text-sm font-medium hover:bg-accent-hover transition-colors cursor-pointer"
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

        <!-- Scroll to bottom button -->
        <Transition
          enter-active-class="transition ease-out duration-200"
          enter-from-class="opacity-0 translate-y-2"
          enter-to-class="opacity-100 translate-y-0"
          leave-active-class="transition ease-in duration-150"
          leave-from-class="opacity-100 translate-y-0"
          leave-to-class="opacity-0 translate-y-2"
        >
          <button
            v-if="showScrollButton"
            @click="scrollToBottom"
            :class="[
              'fixed bottom-28 left-1/2 -translate-x-1/2 w-10 h-10 rounded-full bg-bg-secondary/70 backdrop-blur-xs border border-border flex items-center justify-center text-text-secondary hover:text-text-primary hover:bg-bg-tertiary hover:shadow-2xl transition-all ease-out duration-200 cursor-pointer z-40',
              sidebarCollapsed ? 'lg:ml-7' : 'lg:ml-36'
            ]"
            :style="{ transitionDelay: sidebarCollapsed ? '200ms' : '0ms' }"
          >
            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" />
            </svg>
          </button>
        </Transition>

        <!-- Input at bottom -->
        <div 
          :class="[
            'px-4 fixed w-full right-0 bottom-0 transition-all ease-out duration-200',
            sidebarCollapsed ? 'lg:left-14 lg:w-[calc(100%-3.5rem)]' : 'lg:left-72 lg:w-[calc(100%-18rem)]'
          ]"
          :style="{ transitionDelay: sidebarCollapsed ? '200ms' : '0ms' }"
        >
          <div class="container mx-auto px-0 lg:px-5 pb-4 relative">
            <ChatInput
              v-model:tone="tone"
              @send="sendMessage"
              :disabled="loading || remainingRequests <= 0"
              :placeholder="remainingRequests <= 0 ? 'Limite giornaliero raggiunto' : 'Scrivi un messaggio...'"
            />
            <p class="text-center text-xs text-text-muted mt-2 relative z-20">giamm.ai può sbagliare, ma nel dubbio... non farlo.</p>
            <div class="h-2/3 w-full bg-bg-primary absolute left-0 bottom-0 z-10"></div>
          </div>
        </div>
      </template>

    </main>

    <!-- Privacy Modal Sbarrante -->
    <PrivacyModal @accept="() => {}" />
    
    </div>
    <!-- Fine Main Content Area -->
  </div>
</template>
