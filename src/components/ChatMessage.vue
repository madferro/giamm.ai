<script setup>
import { computed } from 'vue'
import { marked } from 'marked'

const props = defineProps({ message: { type: Object, required: true } })
const isUser = computed(() => props.message.role === 'user')

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
        <div class="text-[0.8125rem] font-semibold text-text-secondary mb-1">giamm.ai</div>
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
