<script setup>
import { ref, onMounted } from 'vue'
import { getChatSessions, getSessionPreview } from '../utils/db.js'

const props = defineProps({
  isOpen: Boolean,
  currentSessionId: String,
  isCollapsed: Boolean
})

const emit = defineEmits(['close', 'loadChat', 'showPrivacy', 'showAbout', 'newChat', 'toggleCollapse'])

const chatHistory = ref([])
const loading = ref(true)

onMounted(async () => {
  await loadChatHistory()
})

async function loadChatHistory() {
  loading.value = true
  try {
    const sessions = await getChatSessions()
    // Carica anteprima per ogni sessione
    const previews = await Promise.all(sessions.map(s => getSessionPreview(s.sessionId)))
    chatHistory.value = previews.filter(p => p !== null)
  } catch (e) {
    console.error('Error loading chat history:', e)
  } finally {
    loading.value = false
  }
}

function formatDate(dateStr) {
  const date = new Date(dateStr)
  const today = new Date()
  const yesterday = new Date(today)
  yesterday.setDate(yesterday.getDate() - 1)

  const todayStr = today.toISOString().split('T')[0]
  const yesterdayStr = yesterday.toISOString().split('T')[0]

  if (dateStr === todayStr) return 'Oggi'
  if (dateStr === yesterdayStr) return 'Ieri'

  return date.toLocaleDateString('it-IT', { day: 'numeric', month: 'short' })
}

function selectChat(sessionId) {
  emit('loadChat', sessionId)
  if (window.innerWidth < 1024) emit('close')
}

function handleNewChat() {
  emit('newChat')
  if (window.innerWidth < 1024) emit('close')
}

function handleShowAbout() {
  emit('showAbout')
  if (window.innerWidth < 1024) emit('close')
}

function handleShowPrivacy() {
  emit('showPrivacy')
  if (window.innerWidth < 1024) emit('close')
}

defineExpose({ loadChatHistory })
</script>

<template>
  <!-- Overlay (solo mobile) -->
  <Transition enter-active-class="transition-opacity ease-out duration-300" enter-from-class="opacity-0"
    enter-to-class="opacity-100" leave-active-class="transition-opacity ease-in duration-200"
    leave-from-class="opacity-100" leave-to-class="opacity-0">
    <div v-if="isOpen" class="fixed inset-0 z-[60] lg:hidden" @click="$emit('close')">
      <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
    </div>
  </Transition>

  <!-- Sidebar -->
  <Transition enter-active-class="transition-transform ease-out duration-300 lg:transition-none"
    enter-from-class="-translate-x-full lg:translate-x-0" enter-to-class="translate-x-0"
    leave-active-class="transition-transform ease-in duration-200 lg:transition-none"
    leave-from-class="translate-x-0 lg:translate-x-0" leave-to-class="-translate-x-full lg:translate-x-0">
    <aside v-show="isOpen"
      class="sidebar-container fixed top-0 left-0 bottom-0 z-[70] lg:z-10 bg-bg-secondary border-r border-border flex flex-col"
      :class="isCollapsed ? 'collapsed' : 'expanded'"
      :style="{ transitionDelay: isCollapsed ? '200ms' : '0ms' }">
      <!-- La sidebar dura 200ms. I testi durano 200ms. -->

      <!-- Header -->
      <div class="h-14 flex items-center justify-between px-3 shrink-0">
        <!-- Titolo con max-width animato -->
        <div class="overflow-hidden transition-all ease-out duration-200"
          :class="isCollapsed ? 'max-w-0 opacity-0' : 'max-w-[120px] opacity-100'"
          :style="{ transitionDelay: isCollapsed ? '0ms' : '200ms' }">
            <a href="/" class="flex items-center gap-1 pl-1 font-bold text-sm tracking-tight text-text-primary">
              <span>giamm</span><span class="w-2 h-2 rounded-full bg-accent"></span><span>ai</span>
            </a>
        </div>

        <button @click="$emit('toggleCollapse')"
          class="hidden lg:flex w-8 h-8 rounded-lg items-center justify-center text-text-secondary hover:text-text-primary hover:bg-bg-tertiary transition-colors cursor-pointer shrink-0"
          :title="isCollapsed ? 'Espandi menu' : 'Riduci menu'">
          <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path v-if="isCollapsed" d="M9 18l6-6-6-6" />
            <path v-else d="M15 18l-6-6 6-6" />
          </svg>
        </button>

        <button @click="$emit('close')"
          class="lg:hidden w-8 h-8 rounded-lg flex items-center justify-center text-text-secondary hover:text-text-primary hover:bg-bg-tertiary transition-colors"
          title="Chiudi">
          <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M18 6L6 18M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Nuova Chat -->
      <div class="px-1 pb-2 shrink-0">
        <button @click="handleNewChat"
          class="w-full rounded-lg hover:bg-bg-tertiary flex items-center text-text-primary cursor-pointer transition-colors px-2 py-1"
          :title="isCollapsed ? 'Nuova Chat' : ''">
          <div class="w-8 h-8 flex justify-center items-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
              stroke="currentColor" class="size-4 shrink-0">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785A5.969 5.969 0 0 0 6 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337Z" />
            </svg>
          </div>
          <!-- Testo con max-width animato -->
          <div class="overflow-hidden transition-all ease-out duration-200"
            :class="isCollapsed ? 'max-w-0 opacity-0' : 'max-w-[140px] opacity-100'"
            :style="{ transitionDelay: isCollapsed ? '0ms' : '200ms' }">
            <span class="whitespace-nowrap text-xs font-semibold">Nuova Chat</span>
          </div>
        </button>
      </div>

      <!-- Cronologia -->
      <Transition
        enter-active-class="transition-opacity duration-200 delay-200"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-show="!isCollapsed" class="flex-1 overflow-y-auto overflow-x-hidden min-h-0">
          <div class="px-3 pt-2">
            <h3 class="text-xs font-semibold text-text-muted uppercase mb-2 px-2">Le tue chat</h3>

            <div v-if="loading" class="text-center py-8 text-text-muted text-sm">Caricamento...</div>

            <div v-else-if="chatHistory.length === 0" class="text-center py-8 text-text-muted text-sm">Nessuna chat
              salvata</div>

            <div v-else class="space-y-1">
              <button v-for="chat in chatHistory" :key="chat.sessionId" @click="selectChat(chat.sessionId)" :class="[
                'w-full text-left px-3 py-2.5 rounded-lg transition-all group cursor-pointer border border-transparent',
                currentSessionId === chat.sessionId
                  ? 'bg-accent/10 text-accent border border-accent/20'
                  : 'hover:bg-bg-tertiary text-text-primary',
              ]">
                <div class="flex items-start justify-between gap-2 mb-1">
                  <span class="text-xs font-medium">{{ formatDate(chat.date) }}</span>
                  <span class="text-xs text-text-muted">{{ chat.messageCount }} msg</span>
                </div>
                <p class="text-sm text-text-secondary line-clamp-2 group-hover:text-text-primary transition-colors">
                  {{ chat.preview }}</p>
              </button>
            </div>
          </div>
        </div>
      </Transition>

      <!-- Spacer per collassato -->
      <div v-if="isCollapsed" class="flex-1"></div>

      <!-- Footer Links -->
      <div class="px-1 py-2 space-y-1 shrink-0">
        <button @click="handleShowAbout"
          class="w-full rounded-lg hover:bg-bg-tertiary flex items-center text-text-primary cursor-pointer transition-colors px-2 py-1"
          :title="isCollapsed ? 'Cos\'è giamm.ai' : ''">
          <div class="w-8 h-8 flex justify-center items-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
              stroke="currentColor" class="size-4 shrink-0">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
            </svg>
          </div>
          <!-- Testo con max-width animato -->
          <div class="overflow-hidden transition-all ease-out duration-200"
            :class="isCollapsed ? 'max-w-0 opacity-0' : 'max-w-[180px] opacity-100'"
            :style="{ transitionDelay: isCollapsed ? '0ms' : '200ms' }">
            <span class="whitespace-nowrap text-xs font-semibold">Cos'è giamm.ai</span>
          </div>
        </button>

        <button @click="handleShowPrivacy"
          class="w-full rounded-lg hover:bg-bg-tertiary flex items-center text-text-primary cursor-pointer transition-colors px-2 py-1"
          :title="isCollapsed ? 'Privacy & Sicurezza' : ''">
          <div class="w-8 h-8 flex justify-center items-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
              stroke="currentColor" class="size-4 shrink-0">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
            </svg>
          </div>
          <!-- Testo con max-width animato -->
          <div class="overflow-hidden transition-all ease-out duration-200"
            :class="isCollapsed ? 'max-w-0 opacity-0' : 'max-w-[180px] opacity-100'"
            :style="{ transitionDelay: isCollapsed ? '0ms' : '200ms' }">
            <span class="whitespace-nowrap text-xs font-semibold">Privacy & Sicurezza</span>
          </div>
        </button>
      </div>
    </aside>
  </Transition>
</template>

<style scoped>
.sidebar-container {
  transition: width 200ms ease-out;
}

.sidebar-container.expanded {
  width: 18rem;
}

.sidebar-container.collapsed {
  width: 3.5rem;
}
</style>
