<script setup>
import { ref, onMounted } from 'vue'
import { getChatSessions, getSessionPreview, deleteSession, deleteAllSessions } from '../utils/db.js'
import ConfirmDialog from './ConfirmDialog.vue'

const props = defineProps({
  isOpen: Boolean,
  currentSessionId: String,
  isCollapsed: Boolean
})

const emit = defineEmits(['close', 'loadChat', 'showPrivacy', 'showAbout', 'newChat', 'toggleCollapse', 'chatDeleted', 'allChatsDeleted'])

const chatHistory = ref([])
const loading = ref(true)
const confirmDialog = ref(null)

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

async function handleDeleteChat(sessionId, event) {
  event.stopPropagation()
  const confirmed = await confirmDialog.value.open({
    title: 'Cancella chat',
    message: 'Sicuro di voler cancellare questa chat? Le domande fatte oggi rimarranno conteggiate.',
    confirmText: 'Cancella',
    cancelText: 'Annulla',
    dangerous: true
  })
  if (!confirmed) return
  await deleteSession(sessionId)
  await loadChatHistory()
  emit('chatDeleted', sessionId)
}

async function handleDeleteAllChats() {
  const confirmed = await confirmDialog.value.open({
    title: 'Cancella tutto',
    message: 'Sicuro di voler cancellare TUTTE le chat? Questa azione non può essere annullata. Le domande fatte oggi rimarranno conteggiate.',
    confirmText: 'Cancella tutto',
    cancelText: 'Annulla',
    dangerous: true
  })
  if (!confirmed) return
  await deleteAllSessions()
  await loadChatHistory()
  emit('allChatsDeleted')
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
          <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" :class="{ 'transform rotate-180 duration-200': isCollapsed }">
            <path stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
            <!--<path v-else stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />-->
          </svg>
        </button>

        <button @click="$emit('close')"
          class="lg:hidden w-8 h-6 rounded-lg flex items-center justify-center text-text-secondary hover:text-text-primary hover:bg-bg-tertiary transition-colors"
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
          <div class="w-8 h-6 flex justify-center items-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="w-[18px] h-[18px] shrink-0">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785A5.969 5.969 0 0 0 6 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337Z" />
            </svg>
          </div>
          <!-- Testo con max-width animato -->
          <div class="overflow-hidden transition-all ease-out duration-200 flex items-center text-xs font-semibold h-6 whitespace-nowrap"
            :class="isCollapsed ? 'max-w-0 opacity-0' : 'max-w-[140px] opacity-100'"
            :style="{ transitionDelay: isCollapsed ? '0ms' : '200ms' }">
            Nuova Chat
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
            <div v-if="loading" class="text-center py-8 text-text-muted text-sm">Caricamento...</div>

            <div v-else-if="chatHistory.length === 0" class="px-2 text-text-muted text-xs">Nessuna chat salvata. Inizia chiedendo qualcosa a giamm.ai nella schermata principale. Devo proprio spiegarti tutto...</div>

            <div v-else>
              <!-- Header con pulsante Cancella Tutto -->
              <div class="flex items-center justify-between mb-2 px-2">
                <h3 class="text-xs font-semibold text-text-muted uppercase">Le tue chat</h3>
                <button
                  @click="handleDeleteAllChats"
                  class="text-text-muted hover:text-red-400 transition-colors cursor-pointer"
                  title="Cancella tutte le chat"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                  </svg>
                </button>
              </div>

              <div class="space-y-1">
                <div v-for="chat in chatHistory" :key="chat.sessionId" class="relative group">
                  <button @click="selectChat(chat.sessionId)" :class="[
                    'w-full text-left px-3 py-2.5 rounded-lg transition-all cursor-pointer border border-transparent',
                    currentSessionId === chat.sessionId
                      ? 'bg-accent/10 text-accent border border-accent/20'
                      : 'hover:bg-bg-tertiary text-text-primary',
                  ]">
                    <div class="flex items-start justify-between gap-2 mb-1">
                      <span class="text-xs font-medium">{{ formatDate(chat.date) }}</span>
                      <span class="text-xs text-text-muted">{{ chat.messageCount }} msg</span>
                    </div>
                    <p class="text-sm text-text-secondary line-clamp-2 transition-colors pr-8">
                      {{ chat.preview }}</p>
                  </button>
                  <!-- Pulsante cancella singola chat -->
                  <button
                    @click="handleDeleteChat(chat.sessionId, $event)"
                    class="absolute right-1.5 bottom-1 opacity-0 group-hover:opacity-100 transition-opacity p-1.5 rounded-md text-text-muted hover:text-red-400 hover:bg-red-400/10 cursor-pointer"
                    title="Cancella chat"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>
                  </button>
                </div>
              </div>
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
          <div class="w-8 h-6 flex justify-center items-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="w-[18px] h-[18px] shrink-0">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
            </svg>
          </div>
          <!-- Testo con max-width animato -->
          <div class="overflow-hidden transition-all ease-out duration-200 flex items-center text-xs font-semibold h-6 whitespace-nowrap"
            :class="isCollapsed ? 'max-w-0 opacity-0' : 'max-w-[180px] opacity-100'"
            :style="{ transitionDelay: isCollapsed ? '0ms' : '200ms' }">
            Cos'è giamm.ai
          </div>
        </button>

        <button @click="handleShowPrivacy"
          class="w-full rounded-lg hover:bg-bg-tertiary flex items-center text-text-primary cursor-pointer transition-colors px-2 py-1"
          :title="isCollapsed ? 'Privacy & Sicurezza' : ''">
          <div class="w-8 h-6 flex justify-center items-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="w-[18px] h-[18px] shrink-0">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
            </svg>

          </div>
          <!-- Testo con max-width animato -->
          <div class="overflow-hidden transition-all ease-out duration-200 flex items-center text-xs font-semibold h-6 whitespace-nowrap"
            :class="isCollapsed ? 'max-w-0 opacity-0' : 'max-w-[180px] opacity-100'"
            :style="{ transitionDelay: isCollapsed ? '0ms' : '200ms' }">
            Privacy & Sicurezza
          </div>
        </button>
      </div>
    </aside>
  </Transition>

  <!-- Confirm Dialog -->
  <ConfirmDialog ref="confirmDialog" />
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
