<script setup>
import { ref } from 'vue'
import ToneSelector from './ToneSelector.vue'

const props = defineProps({
  disabled: { type: Boolean, default: false },
  placeholder: { type: String, default: 'Scrivi un messaggio...' },
  tone: { type: String, default: 'ironic' },
})

const emit = defineEmits(['send', 'update:tone'])

const text = ref('')

function handleSubmit() {
  const trimmed = text.value.trim()
  if (!trimmed || props.disabled) return
  emit('send', trimmed)
  text.value = ''
}

function handleKeydown(e) {
  if (e.key === 'Enter' && !e.shiftKey) {
    e.preventDefault()
    handleSubmit()
  }
}

function updateTone(val) {
  emit('update:tone', val)
}
</script>

<template>
  <form @submit.prevent="handleSubmit" class="relative">
    <div class="bg-bg-input border border-border rounded-4xl shadow-sm focus-within:ring-2 focus-within:ring-accent/30 focus-within:border-accent transition-all">
      <div class="flex items-end gap-2 p-2">
        <textarea
          v-model="text"
          rows="1"
          :placeholder="placeholder"
          maxlength="500"
          :disabled="disabled"
          @keydown="handleKeydown"
          class="flex-1 bg-transparent border-0 outline-none resize-none text-text-primary placeholder:text-text-muted text-sm leading-relaxed min-h-[24px] max-h-[200px] field-sizing-content pb-1 px-1 pt-0 disabled:opacity-50"
        ></textarea>
        <div class="flex items-center gap-1.5 shrink-0">
          <ToneSelector
            :model-value="tone"
            @update:model-value="updateTone"
          />
          <button
            type="submit"
            :disabled="disabled || !text.trim()"
            class="w-8 h-8 rounded-full bg-accent text-white flex items-center justify-center shrink-0 hover:bg-accent-hover transition-colors disabled:opacity-40 cursor-pointer disabled:cursor-not-allowed"
          >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </form>
</template>
