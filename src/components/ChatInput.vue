<script setup>
import { ref } from 'vue'
const props = defineProps({ disabled: { type: Boolean, default: false }, placeholder: { type: String, default: 'Scrivi un messaggio...' } })
const emit = defineEmits(['send'])
const text = ref('')
function handleSubmit() {
  const trimmed = text.value.trim()
  if (!trimmed || props.disabled) return
  emit('send', trimmed); text.value = ''
}
function handleKeydown(e) {
  if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); handleSubmit() }
}
</script>

<template>
  <form @submit.prevent="handleSubmit" class="relative">
    <div class="bg-bg-input border border-border rounded-2xl shadow-sm focus-within:ring-2 focus-within:ring-accent/30 focus-within:border-accent transition-all">
      <div class="flex items-end gap-2 p-3">
        <textarea v-model="text" rows="1" :placeholder="placeholder" maxlength="500" :disabled="disabled" @keydown="handleKeydown"
          class="flex-1 bg-transparent border-0 outline-none resize-none text-text-primary placeholder:text-text-muted text-base leading-relaxed min-h-[24px] max-h-[200px] field-sizing-content py-1 px-1 disabled:opacity-50"></textarea>
        <button type="submit" :disabled="disabled || !text.trim()"
          class="w-8 h-8 rounded-lg bg-accent text-white flex items-center justify-center shrink-0 hover:bg-accent-hover transition-colors disabled:opacity-40 cursor-pointer">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22,2 15,22 11,13 2,9"/></svg>
        </button>
      </div>
    </div>
  </form>
</template>
