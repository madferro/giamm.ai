<script setup>
import { ref, onMounted } from 'vue'
import { getChatSessions, getSessionPreview, deleteSession, deleteAllSessions } from '../utils/db.js'
import ConfirmDialog from './ConfirmDialog.vue'

const props = defineProps({
  isOpen: Boolean,
  currentSessionId: String,
  isCollapsed: Boolean
})

const emit = defineEmits(['close', 'loadChat', 'showPrivacy', 'showAbout', 'showCredits', 'showShare', 'showLimits', 'newChat', 'toggleCollapse', 'chatDeleted', 'allChatsDeleted'])

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

function handleShowAbout(event) {
  emit('showAbout')
  event.currentTarget.blur()
  if (window.innerWidth < 1024) emit('close')
}

function handleShowPrivacy(event) {
  emit('showPrivacy')
  event.currentTarget.blur()
  if (window.innerWidth < 1024) emit('close')
}

function handleShowCredits(event) {
  emit('showCredits')
  event.currentTarget.blur()
  if (window.innerWidth < 1024) emit('close')
}

function handleShowShare(event) {
  emit('showShare')
  event.currentTarget.blur()
  if (window.innerWidth < 1024) emit('close')
}

function handleShowLimits(event) {
  emit('showLimits')
  event.currentTarget.blur()
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
  <Transition
    enter-active-class="transition-all ease-in-out duration-200"
    enter-from-class="-translate-x-full"
    enter-to-class="translate-x-0"
    leave-active-class="transition-all ease-in-out duration-300"
    leave-from-class="translate-x-0"
    leave-to-class="-translate-x-full"
  >
    <aside v-if="isOpen"
      class="sidebar-container fixed top-0 left-0 bottom-0 z-[70] lg:z-10 bg-bg-secondary border-r border-border flex flex-col"
      :class="isCollapsed ? 'collapsed' : 'expanded'">
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
          >
          <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" :class="{ 'transform rotate-180 duration-200': isCollapsed }">
            <path stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
            <!--<path v-else stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />-->
          </svg>
        </button>

        <button @click="$emit('close')"
          class="cursor-pointer lg:hidden w-8 h-8 rounded-lg flex items-center justify-center text-text-secondary hover:text-text-primary hover:bg-bg-tertiary transition-colors"
          title="Chiudi">
          <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M18 6L6 18M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Nuova Chat -->
      <div class="px-1 pb-2 shrink-0">
        <button @click="handleNewChat"
          class="tooltip-giammai w-full rounded-lg hover:bg-bg-tertiary flex items-center text-text-secondary cursor-pointer transition-colors px-2 py-1"
          :title="isCollapsed ? 'Nuova Chat' : ''"
          aria-label="Avvia una nuova, inutile, chat" data-balloon-pos="right">
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
          <div class="px-3 pt-4">
            <div v-if="loading" class="text-center py-8 text-text-muted text-sm">Caricamento...</div>

            <div v-else-if="chatHistory.length === 0" class="px-2 text-text-muted text-xs">Nessuna chat salvata. Inizia chiedendo qualcosa a giamm.ai nella schermata principale. Devo proprio spiegarti tutto...</div>

            <div v-else>
              <!-- Header con pulsante Cancella Tutto -->
              <div class="flex items-center justify-between mb-2 px-2 mt-8">
                <h3 class="text-xs font-semibold text-text-muted uppercase">Le tue chat</h3>
                <button
                  @click="handleDeleteAllChats"
                  class="tooltip-giammai text-text-muted hover:text-red-400 transition-colors cursor-pointer"
                  aria-label="Cancella tutte le chat"
                  data-balloon-pos="up-right"
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
                    class="tooltip-giammai-absolute absolute right-1.5 bottom-1 opacity-0 group-hover:opacity-100 transition-opacity p-1.5 rounded-md text-text-muted hover:text-red-400 hover:bg-red-400/10 cursor-pointer"
                    aria-label="Cancella chat"
                    data-balloon-pos="up-right"
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
          class="tooltip-giammai w-full rounded-lg hover:bg-bg-tertiary flex items-center text-text-secondary cursor-pointer transition-colors px-2 py-1"
          :title="isCollapsed ? 'Cos\'è giamm.ai' : ''"
          aria-label="Perdi due minuti del tuo tempo per capirci qualcosa di più" data-balloon-pos="right">
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
        <button @click="handleShowLimits"
          class="tooltip-giammai w-full rounded-lg hover:bg-bg-tertiary flex items-center text-text-secondary cursor-pointer transition-colors px-2 py-1"
          :title="isCollapsed ? 'Perché solo 10?' : ''"
          aria-label="10 domande al giorno e stop. Scopri perché" data-balloon-pos="right">
          <div class="w-8 h-6 flex justify-center items-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[18px] h-[18px] shrink-0">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10.05 4.575a1.575 1.575 0 1 0-3.15 0v3m3.15-3v-1.5a1.575 1.575 0 0 1 3.15 0v1.5m-3.15 0 .075 5.925m3.075.75V4.575m0 0a1.575 1.575 0 0 1 3.15 0V15M6.9 7.575a1.575 1.575 0 1 0-3.15 0v8.175a6.75 6.75 0 0 0 6.75 6.75h2.018a5.25 5.25 0 0 0 3.712-1.538l1.732-1.732a5.25 5.25 0 0 0 1.538-3.712l.003-2.024a.668.668 0 0 1 .198-.471 1.575 1.575 0 1 0-2.228-2.228 3.818 3.818 0 0 0-1.12 2.687M6.9 7.575V12m6.27 4.318A4.49 4.49 0 0 1 16.35 15m.002 0h-.002" />
            </svg>
          </div>
          <!-- Testo con max-width animato -->
          <div class="overflow-hidden transition-all ease-out duration-200 flex items-center text-xs font-semibold h-6 whitespace-nowrap"
            :class="isCollapsed ? 'max-w-0 opacity-0' : 'max-w-[180px] opacity-100'"
            :style="{ transitionDelay: isCollapsed ? '0ms' : '200ms' }">
            Limiti di utilizzo
          </div>
        </button>
        <button @click="handleShowPrivacy"
          class="tooltip-giammai w-full rounded-lg hover:bg-bg-tertiary flex items-center text-text-secondary cursor-pointer transition-colors px-2 py-1"
          :title="isCollapsed ? 'Privacy & Sicurezza' : ''"
          aria-label="I tuoi dati sono al sicuro. Respira" data-balloon-pos="right">
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
            Privacy e Sicurezza
          </div>
        </button>
        <button @click="handleShowCredits"
          class="tooltip-giammai w-full rounded-lg hover:bg-bg-tertiary flex items-center text-text-secondary cursor-pointer transition-colors px-2 py-1"
          :title="isCollapsed ? 'I geni dietro' : ''"
          aria-label="Scopri chi ha avuto questa pessima idea" data-balloon-pos="right">
          <div class="w-8 h-6 flex justify-center items-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[18px] h-[18px] shrink-0">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
            </svg>

          </div>
          <!-- Testo con max-width animato -->
          <div class="overflow-hidden transition-all ease-out duration-200 flex items-center text-xs font-semibold h-6 whitespace-nowrap"
            :class="isCollapsed ? 'max-w-0 opacity-0' : 'max-w-[180px] opacity-100'"
            :style="{ transitionDelay: isCollapsed ? '0ms' : '200ms' }">
            I geni (del male) dietro
          </div>
        </button>
        <button @click="handleShowShare"
          class="tooltip-giammai w-full rounded-lg hover:bg-bg-tertiary flex items-center text-text-secondary cursor-pointer transition-colors px-2 py-1"
          :title="isCollapsed ? 'Condividi' : ''"
          aria-label="Diffondi il verbo dello scoraggiamento" data-balloon-pos="right">
          <div class="w-8 h-6 flex justify-center items-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[18px] h-[18px] shrink-0">
              <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z" />
            </svg>
          </div>
          <!-- Testo con max-width animato -->
          <div class="overflow-hidden transition-all ease-out duration-200 flex items-center text-xs font-semibold h-6 whitespace-nowrap"
            :class="isCollapsed ? 'max-w-0 opacity-0' : 'max-w-[180px] opacity-100'"
            :style="{ transitionDelay: isCollapsed ? '0ms' : '200ms' }">
            Condividi giamm.ai
          </div>
        </button>
        <a href="mailto:lamentati@giamm.ai" target="_blank"
          class="tooltip-giammai w-full rounded-lg hover:bg-bg-tertiary flex items-center text-text-secondary cursor-pointer transition-colors px-2 py-1"
          :title="isCollapsed ? 'Scrivici e lamentati' : ''"
          aria-label="Lamentati pure, tanto non cambierà nulla" data-balloon-pos="right">
          <div class="w-8 h-6 flex justify-center items-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="w-[18px] h-[18px] shrink-0">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
            </svg>
          </div>
          <!-- Testo con max-width animato -->
          <div class="overflow-hidden transition-all ease-out duration-200 flex items-center text-xs font-semibold h-6 whitespace-nowrap"
            :class="isCollapsed ? 'max-w-0 opacity-0' : 'max-w-[180px] opacity-100'"
            :style="{ transitionDelay: isCollapsed ? '0ms' : '200ms' }">
            Scrivici e lamentati
          </div>
        </a>
      </div>
    </aside>
  </Transition>

  <!-- Confirm Dialog -->
  <ConfirmDialog ref="confirmDialog" />
</template>

<style scoped>
.sidebar-container.expanded {
  width: 18rem;
}

.sidebar-container.collapsed {
  width: 3.5rem;
}

/* Transizioni solo su desktop (lg+) */
@media (min-width: 1024px) {
  .sidebar-container {
    transition: width 200ms ease-out;
  }
  
  .sidebar-container.collapsed {
    transition-delay: 200ms;
  }
}
</style>
