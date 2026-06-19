<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({
  modelValue: { type: String, default: 'ironic' },
})

const emit = defineEmits(['update:modelValue'])

const open = ref(false)
const containerRef = ref(null)

const tones = [
  {
    id: 'ironic',
    label: 'Ironico',
    subtitle: 'Amico cinico che ti prende in giro',
    icon: `<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M8 14c1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3 1.34 3 3 3z"/><path d="M16 14c1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3 1.34 3 3 3z"/><path d="M12 20c-3.87 0-7-3.13-7-7 0-1.5.47-2.89 1.27-4.03C7.5 8.33 9.67 7 12 7s4.5 1.33 5.73 1.97C18.53 10.11 19 11.5 19 13c0 3.87-3.13 7-7 7z"/></svg>`,
  },
  {
    id: 'professional',
    label: 'Professionale',
    subtitle: 'Consulente McKinsey con slide deck',
    icon: `<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>`,
  },
  {
    id: 'arrogant',
    label: 'Arrogante',
    subtitle: 'Diva dello spettacolo, eye-roll virtuale',
    icon: `<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/><line x1="17.5" y1="7.5" x2="17.5" y2="7.5"/></svg>`,
  },
]

const selected = computed(() => tones.find(t => t.id === props.modelValue) || tones[0])

function select(toneId) {
  emit('update:modelValue', toneId)
  open.value = false
}

function toggle() {
  open.value = !open.value
}

function handleClickOutside(event) {
  if (containerRef.value && !containerRef.value.contains(event.target)) {
    open.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<template>
  <div ref="containerRef" class="relative inline-block">
    <!-- Trigger button -->
    <button
      type="button"
      @click.stop="toggle"
      class="flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-medium tracking-tight transition-colors cursor-pointer bg-transparent text-text-primary hover:bg-bg-tertiary active:bg-bg-tertiary focus:bg-bg-tertiary"
      :class="open ? 'ring-2 ring-accent/30 border-accent' : ''"
    >
      <span class="text-text-secondary" v-html="selected.icon"></span>
      <span>{{ selected.label }}</span>
      <svg
        width="14"
        height="14"
        fill="none"
        stroke="currentColor"
        stroke-width="2.5"
        stroke-linecap="round"
        stroke-linejoin="round"
        viewBox="0 0 24 24"
        class="text-text-muted transition-transform duration-200"
        :class="open ? 'rotate-180' : ''"
      >
        <polyline points="6 9 12 15 18 9"/>
      </svg>
    </button>

    <!-- Dropdown -->
    <Transition
      enter-active-class="transition ease-out duration-150"
      enter-from-class="opacity-0 scale-95 -translate-y-1"
      enter-to-class="opacity-100 scale-100 translate-y-0"
      leave-active-class="transition ease-in duration-100"
      leave-from-class="opacity-100 scale-100 translate-y-0"
      leave-to-class="opacity-0 scale-95 -translate-y-1"
    >
      <div
        v-if="open"
        class="absolute z-50 right-0 bottom-full mb-2 w-72 rounded-xl border border-border bg-bg-secondary shadow-2xl overflow-hidden origin-bottom-right"
      >
        <div class="px-3 py-2 text-xs font-semibold text-text-muted uppercase tracking-wider border-b border-border">
          Tono di risposta
        </div>
        <div class="p-1.5 space-y-0.5">
          <button
            type="button"
            v-for="tone in tones"
            :key="tone.id"
            @click.stop="select(tone.id)"
            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-left transition-colors cursor-pointer"
            :class="tone.id === modelValue ? 'bg-accent/10 text-accent' : 'text-text-primary hover:bg-bg-tertiary'"
          >
            <span
              class="shrink-0"
              :class="tone.id === modelValue ? 'text-accent' : 'text-text-secondary'"
              v-html="tone.icon"
            ></span>
            <div class="min-w-0">
              <div class="text-sm font-medium">{{ tone.label }}</div>
              <div class="text-xs text-text-muted truncate">{{ tone.subtitle }}</div>
            </div>
            <svg
              v-if="tone.id === modelValue"
              width="16"
              height="16"
              fill="none"
              stroke="currentColor"
              stroke-width="2.5"
              stroke-linecap="round"
              stroke-linejoin="round"
              viewBox="0 0 24 24"
              class="ml-auto shrink-0 text-accent"
            >
              <polyline points="20 6 9 17 4 12"/>
            </svg>
          </button>
        </div>
      </div>
    </Transition>
  </div>
</template>
