<script setup>
import { ref, watch } from 'vue'

const visible = ref(false)
let resolveFn = null

const title = ref('Conferma')
const message = ref('Sei sicuro?')
const confirmText = ref('Conferma')
const cancelText = ref('Annulla')
const confirmClass = ref('')

function open(options = {}) {
  title.value = options.title || 'Conferma'
  message.value = options.message || 'Sei sicuro?'
  confirmText.value = options.confirmText || 'Conferma'
  cancelText.value = options.cancelText || 'Annulla'
  confirmClass.value = options.dangerous ? 'bg-red-500 hover:bg-red-600' : 'bg-accent hover:bg-accent-hover'
  visible.value = true
  return new Promise((resolve) => {
    resolveFn = resolve
  })
}

function onConfirm() {
  visible.value = false
  resolveFn?.(true)
  resolveFn = null
}

function onCancel() {
  visible.value = false
  resolveFn?.(false)
  resolveFn = null
}

// Chiude con Escape
watch(visible, (val) => {
  if (val) {
    const handler = (e) => {
      if (e.key === 'Escape') onCancel()
    }
    document.addEventListener('keydown', handler)
    // Rimuovi il listener quando il dialog si chiude
    const unwatch = watch(visible, (v) => {
      if (!v) {
        document.removeEventListener('keydown', handler)
        unwatch()
      }
    })
  }
})

defineExpose({ open })
</script>

<template>
  <Transition
    enter-active-class="transition-opacity ease-out duration-200"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-active-class="transition-opacity ease-in duration-150"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div
      v-if="visible"
      class="fixed inset-0 z-[80] flex items-center justify-center p-4"
      @click.self="onCancel"
    >
      <!-- Overlay -->
      <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

      <!-- Dialog -->
      <div
        class="relative bg-bg-primary border border-border-strong rounded-2xl shadow-2xl max-w-sm w-full p-6"
      >
        <!-- Testo -->
        <h3 class="text-lg font-semibold text-text-primary text-left mb-2">
          {{ title }}
        </h3>
        <p class="text-sm text-text-secondary text-left mb-6">
          {{ message }}
        </p>

        <!-- Pulsanti -->
        <div class="flex justify-end gap-3">
          <button
            @click="onCancel"
            class="py-1.5 px-4 rounded-lg font-medium text-sm border border-border text-text-secondary hover:text-text-primary hover:bg-bg-tertiary transition-all cursor-pointer"
          >
            {{ cancelText }}
          </button>
          <button
            @click="onConfirm"
            :class="[
              'py-1.5 px-4 rounded-lg font-medium text-white text-sm transition-all cursor-pointer',
              confirmClass
            ]"
          >
            {{ confirmText }}
          </button>
        </div>
      </div>
    </div>
  </Transition>
</template>
