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
    icon: `<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><circle cx="9" cy="10" r="1" fill="currentColor" stroke="none"/><circle cx="15" cy="10" r="1" fill="currentColor" stroke="none"/><path d="M8 14C9.5 16.5 14.5 16.5 16 14"/></svg>`,
  },
  {
    id: 'gentle',
    label: 'Gentile',
    subtitle: 'Rifiuto cordiale e rispettoso',
    icon: `<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>`,
  },
  {
    id: 'professional',
    label: 'Professionale',
    subtitle: 'Consulente con slide deck',
    icon: `<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>`,
  },
  {
    id: 'arrogant',
    label: 'Arrogante',
    subtitle: 'Diva dello spettacolo, eye-roll virtuale',
    icon: `<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/><line x1="17.5" y1="7.5" x2="17.5" y2="7.5"/></svg>`,
  },
  {
    id: 'vicious',
    label: 'Perfido',
    subtitle: 'Nessuna pietà, solo verità scomode',
    icon: `<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M7 5L4 2"/><path d="M17 5L20 2"/><path d="M7 9L10 10"/><path d="M17 9L14 10"/><path d="M8 15C10 17.5 14 17.5 16 15"/></svg>`,
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
