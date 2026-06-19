<script setup>
import { ref, onMounted } from 'vue'
import WelcomeScreen from './components/WelcomeScreen.vue'
import ChatMessages from './components/ChatMessages.vue'
import ChatInput from './components/ChatInput.vue'

const messages = ref([])
const loading = ref(false)
const error = ref('')
const remainingRequests = ref(10)
const hasMessages = ref(false)
const dark = ref(false)

onMounted(async () => {
  await checkRateLimit()
  dark.value = document.documentElement.classList.contains('dark')
})

function toggleTheme() {
  dark.value = !dark.value
  document.documentElement.classList.toggle('dark', dark.value)
  localStorage.setItem('giammai-theme', dark.value ? 'dark' : 'light')
}

async function checkRateLimit() {
  try {
    const res = await fetch('/api/check_limit.php')
    const data = await res.json()
    if (data.success) remainingRequests.value = data.remaining
  } catch (e) { console.error(e) }
}

async function sendMessage(text) {
  if (!text.trim() || loading.value) return
  if (remainingRequests.value <= 0) {
    error.value = 'Hai esaurito le 10 richieste giornaliere'
    setTimeout(() => error.value = '', 5000)
    return
  }
  hasMessages.value = true
  messages.value.push({
    id: Date.now(), role: 'user', content: text.trim(),
    time: new Date().toLocaleTimeString('it-IT', { hour: '2-digit', minute: '2-digit' })
  })
  loading.value = true; error.value = ''
  try {
    const res = await fetch('/api/chat.php', {
      method: 'POST', headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ message: text.trim() })
    })
    const data = await res.json()
    if (data.success) {
      messages.value.push({
        id: Date.now() + 1, role: 'assistant', content: data.response,
        time: new Date().toLocaleTimeString('it-IT', { hour: '2-digit', minute: '2-digit' })
      })
      remainingRequests.value = data.remaining
    } else {
      error.value = data.error || 'Errore'
      setTimeout(() => error.value = '', 5000)
    }
  } catch (err) {
    error.value = 'Connessione fallita'
    setTimeout(() => error.value = '', 5000)
  } finally { loading.value = false }
}
</script>

<template>
  <div class="min-h-screen flex flex-col bg-bg-primary text-text-primary">
    <header class="fixed top-0 left-0 right-0 z-50 h-14 backdrop-blur-xl border-b border-border bg-bg-primary/85 dark:bg-bg-primary/85 transition-colors duration-200">
      <div class="container mx-auto px-5 h-full flex items-center justify-between">
        <a href="/" class="flex items-center gap-2 font-bold text-lg tracking-tight text-text-primary">
          giamm<span class="w-2 h-2 rounded-full bg-accent inline-block"></span>ai
        </a>
        <div class="flex items-center gap-3">
          <div class="text-xs font-medium text-text-muted bg-bg-secondary px-3 py-1.5 rounded-full border border-border">
            <strong class="text-accent">{{ remainingRequests }}</strong> / 10
          </div>
          <button @click="toggleTheme" class="w-9 h-9 rounded-lg border border-border bg-bg-secondary flex items-center justify-center text-text-secondary hover:text-text-primary hover:bg-bg-tertiary transition-colors cursor-pointer">
            <svg v-if="dark" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
            <svg v-else width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
          </button>
        </div>
      </div>
    </header>

    <main class="flex-1 flex flex-col pt-14">
      <div class="flex-1 flex flex-col" :class="hasMessages ? '' : 'items-center justify-center'">
        <WelcomeScreen v-if="!hasMessages" />
        <ChatMessages v-else :messages="messages" :loading="loading" />
        <div v-if="error" class="container mx-auto px-5 my-3">
          <div class="px-4 py-3 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 text-sm flex items-center gap-2">
            <svg class="shrink-0" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            <span>{{ error }}</span>
          </div>
        </div>
      </div>

      <div class="w-full px-4 pb-8 pt-4" :class="hasMessages ? 'border-t border-border bg-bg-primary sticky bottom-0' : ''">
        <div class="container mx-auto">
          <ChatInput @send="sendMessage" :disabled="loading || remainingRequests <= 0"
            :placeholder="remainingRequests <= 0 ? 'Limite giornaliero raggiunto' : 'Scrivi un messaggio...'" />
          <p class="text-center text-xs text-text-muted mt-2">giamm.ai può sbagliare, ma nel dubbio... non farlo.</p>
        </div>
      </div>
    </main>
  </div>
</template>
