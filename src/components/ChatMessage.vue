<script setup>
import { computed, ref } from 'vue'
import { marked } from 'marked'

const props = defineProps({ message: { type: Object, required: true } })
const isUser = computed(() => props.message.role === 'user')
const copied = ref(false)

function stripMarkdown(md) {
  return md
    // Rimuovi bold e italic
    .replace(/\*\*(.*?)\*\*/g, '$1')
    .replace(/\*(.*?)\*/g, '$1')
    .replace(/__(.*?)__/g, '$1')
    .replace(/_(.*?)_/g, '$1')
    // Rimuovi headers
    .replace(/^#{1,6}\s+/gm, '')
    // Rimuovi link [text](url) → text
    .replace(/\[(.*?)\]\(.*?\)/g, '$1')
    // Rimuovi code inline
    .replace(/`(.*?)`/g, '$1')
    // Rimuovi code block
    .replace(/```[\s\S]*?```/g, '')
    // Rimuovi blockquote
    .replace(/^>\s?/gm, '')
    // Rimuovi liste
    .replace(/^[-*+]\s+/gm, '')
    .replace(/^\d+\.\s+/gm, '')
    // Rimuovi separatori
    .replace(/^---+$/gm, '')
    // Rimuovi html residuo
    .replace(/<[^>]*>/g, '')
    // Normalizza spazi multipli
    .replace(/\n{3,}/g, '\n\n')
    .trim()
}

function copyToClipboard() {
  const plainText = stripMarkdown(props.message.content)
  navigator.clipboard.writeText(plainText).then(() => {
    copied.value = true
    setTimeout(() => copied.value = false, 2000)
  }).catch(err => {
    console.error('Failed to copy:', err)
  })
}

// Configura marked per output sicuro e con classi Tailwind
marked.setOptions({
  breaks: true,
  gfm: true,
})

const formattedContent = computed(() => {
  if (isUser.value) return props.message.content

  try {
    // Converti markdown in HTML con marked (sync)
    let html = marked(props.message.content)

    // Aggiungi classi Tailwind agli elementi HTML
    html = html.replace(/<p>/g, '<p class="mb-3 last:mb-0">')
    html = html.replace(/<strong>/g, '<strong class="font-semibold text-text-primary">')
    html = html.replace(/<em>/g, '<em class="italic text-accent">')
    html = html.replace(/<code>/g, '<code class="bg-bg-tertiary px-1.5 py-0.5 rounded text-sm font-mono text-text-secondary">')
    html = html.replace(/<ul>/g, '<ul class="list-disc list-inside space-y-1 mb-3 ml-2">')
    html = html.replace(/<ol>/g, '<ol class="list-decimal list-inside space-y-1 mb-3 ml-2">')
    html = html.replace(/<li>/g, '<li class="text-text-primary ml-2">')
    html = html.replace(/<h1>/g, '<h1 class="text-xl font-bold mb-2 text-text-primary">')
    html = html.replace(/<h2>/g, '<h2 class="text-lg font-bold mb-2 text-text-primary">')
    html = html.replace(/<h3>/g, '<h3 class="text-base font-semibold mb-2 text-text-primary">')
    html = html.replace(/<blockquote>/g, '<blockquote class="border-l-4 border-accent pl-4 italic text-text-secondary my-3">')
    html = html.replace(/<pre>/g, '<pre class="bg-bg-tertiary p-3 rounded-lg overflow-x-auto my-3">')
    html = html.replace(/<a /g, '<a class="text-accent hover:underline" target="_blank" rel="noopener noreferrer" ')

    return html
  } catch (error) {
    console.error('Markdown parsing error:', error)
    // Fallback: ritorna il testo con solo <br>
    return props.message.content.replace(/\n/g, '<br>')
  }
})
</script>

<template>
  <div v-if="isUser" class="flex justify-center animate-slide-up">
    <div class="container mx-auto px-5 py-6 flex justify-end">
      <div class="max-w-[75%]">
        <div class="bg-user-bubble text-text-primary px-5 py-3 rounded-2xl rounded-br-sm text-[0.9375rem] leading-relaxed">{{ message.content }}</div>
        <div class="text-right text-[0.6875rem] text-text-muted mt-1 pr-1">{{ message.time }}</div>
      </div>
    </div>
  </div>
  <div v-else class="flex justify-center animate-slide-up">
    <div class="container mx-auto px-5 py-6 flex gap-3">
      <div class="w-8 h-8 rounded-full bg-gradient-to-br from-accent to-accent-hover flex items-center justify-center text-white text-xs font-bold shrink-0">AI</div>
      <div class="flex-1 min-w-0 pt-0.5">
        <div class="flex items-center justify-between mb-1">
          <div class="text-[0.8125rem] font-semibold text-text-secondary">giamm.ai</div>
          <button
            @click="copyToClipboard"
            class="flex items-center text-xs text-text-secondary hover:text-accent transition-colors cursor-pointer"
          >
            <svg v-if="!copied" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 8.25V6a2.25 2.25 0 0 0-2.25-2.25H6A2.25 2.25 0 0 0 3.75 6v8.25A2.25 2.25 0 0 0 6 16.5h2.25m8.25-8.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-7.5A2.25 2.25 0 0 1 8.25 18v-1.5m8.25-8.25h-6a2.25 2.25 0 0 0-2.25 2.25v6" />
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4 text-green-600 dark:text-green-500">
              <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
            <span class="font-semibold" :class="copied ? 'text-green-600 dark:text-green-500' : ''">{{ copied ? 'Copiato!' : 'Copia' }}</span>
          </button>
        </div>
        <div class="text-[0.9375rem] leading-relaxed text-text-primary prose prose-sm max-w-none" v-html="formattedContent"></div>
        <div class="text-[0.6875rem] text-text-muted mt-2">{{ message.time }}</div>
      </div>
    </div>
  </div>
</template>

<style scoped>
@keyframes slideUp { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
.animate-slide-up { animation: slideUp 0.25s ease; }
</style>
