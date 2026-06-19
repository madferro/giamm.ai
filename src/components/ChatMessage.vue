<script setup>
import { computed } from 'vue'
const props = defineProps({ message: { type: Object, required: true } })
const isUser = computed(() => props.message.role === 'user')

function parseMarkdown(text) {
  let t = text
  t = t.replace(/\*\*(.+?)\*\*/g, '<strong class="font-semibold text-text-primary">$1</strong>')
  t = t.replace(/\*(.+?)\*/g, '<em class="italic text-accent">$1</em>')
  t = t.replace(/`(.+?)`/g, '<code class="bg-bg-tertiary px-1.5 py-0.5 rounded text-sm font-mono text-text-secondary">$1</code>')
  const paragraphs = t.split('\n\n').filter(p => p.trim())
  if (paragraphs.length > 1) return paragraphs.map(p => '<p class="mb-3 last:mb-0">' + p.replace(/\n/g, '<br>') + '</p>').join('')
  return t.replace(/\n/g, '<br>')
}
</script>

<template>
  <div v-if="isUser" class="flex justify-center border-b border-border animate-slide-up">
    <div class="container mx-auto px-5 py-6 flex justify-end">
      <div class="max-w-[75%]">
        <div class="bg-user-bubble text-text-primary px-5 py-3 rounded-2xl rounded-br-sm text-[0.9375rem] leading-relaxed">{{ message.content }}</div>
        <div class="text-right text-[0.6875rem] text-text-muted mt-1 pr-1">{{ message.time }}</div>
      </div>
    </div>
  </div>
  <div v-else class="flex justify-center border-b border-border animate-slide-up">
    <div class="container mx-auto px-5 py-6 flex gap-3">
      <div class="w-8 h-8 rounded-full bg-gradient-to-br from-accent to-accent-hover flex items-center justify-center text-white text-xs font-bold shrink-0">AI</div>
      <div class="flex-1 min-w-0 pt-0.5">
        <div class="text-[0.8125rem] font-semibold text-text-secondary mb-1">giamm.ai</div>
        <div class="text-[0.9375rem] leading-relaxed text-text-primary" v-html="parseMarkdown(message.content)"></div>
        <div class="text-[0.6875rem] text-text-muted mt-2">{{ message.time }}</div>
      </div>
    </div>
  </div>
</template>

<style scoped>
@keyframes slideUp { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
.animate-slide-up { animation: slideUp 0.25s ease; }
</style>
