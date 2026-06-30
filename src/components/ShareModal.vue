<script setup>
import { ref } from 'vue'

defineProps({
  show: Boolean
})

const emit = defineEmits(['close'])

const shareUrl = ref(window.location.href)
const copied = ref(false)

const shareLinks = {
  whatsapp: () => `https://wa.me/?text=${encodeURIComponent('Dai un\'occhiata a giamm.ai - L\'AI che ti scoraggia con ironia ' + shareUrl.value)}`,
  facebook: () => `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareUrl.value)}`,
  x: () => `https://twitter.com/intent/tweet?url=${encodeURIComponent(shareUrl.value)}&text=${encodeURIComponent('Dai un\'occhiata a giamm.ai - L\'AI che ti scoraggia con ironia')}`,
  instagram: () => `instagram://story-camera`,
  tiktok: () => `tiktok://`
}

function shareOn(platform) {
  const url = shareLinks[platform]()
  if (url) {
    window.open(url, '_blank', 'width=600,height=400')
  }
}

async function copyLink() {
  try {
    await navigator.clipboard.writeText(shareUrl.value)
    copied.value = true
    setTimeout(() => {
      copied.value = false
    }, 2000)
  } catch (err) {
    console.error('Errore nella copia:', err)
  }
}
</script>

<template>
  <!-- Overlay -->
  <Transition
    enter-active-class="transition-opacity ease-out duration-300"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-active-class="transition-opacity ease-in duration-200"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div v-if="show" class="fixed inset-0 z-[60]" @click="$emit('close')">
      <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
    </div>
  </Transition>

  <!-- Modal -->
  <Transition
    enter-active-class="transition-all ease-out duration-300"
    enter-from-class="opacity-0 translate-x-full"
    enter-to-class="opacity-100 translate-x-0"
    leave-active-class="transition-all ease-in duration-200"
    leave-from-class="opacity-100 translate-x-0"
    leave-to-class="opacity-0 translate-x-full"
  >
    <div
      v-if="show"
      class="fixed top-0 right-0 bottom-0 z-[70] w-full max-w-3xl bg-bg-primary shadow-2xl overflow-y-auto flex flex-col"
      @click.stop
    >
      <!-- Header -->
      <div class="sticky top-0 z-10 bg-bg-primary/95 backdrop-blur-xl border-b border-border">
        <div class="flex items-center justify-between p-5">
          <h2 class="text-lg font-bold text-text-primary flex items-center gap-2">
            Diffondi giamm<span class="w-2 h-2 rounded-full bg-accent inline-block"></span>ai
          </h2>
          <button
            @click="$emit('close')"
            class="cursor-pointer w-10 h-10 rounded-lg flex items-center justify-center text-text-secondary hover:text-text-primary hover:bg-bg-secondary transition-colors"
            title="Chiudi"
          >
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M18 6L6 18M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Content -->
      <div class="p-6 space-y-6 text-text-primary flex-1 overflow-y-auto">
        <!-- Intro -->
        <div class="space-y-2">
          <p class="text-sm text-text-secondary">
            Condividi giamm.ai con chi ha bisogno di un po' di sano scoraggiamento. Pensaci bene...e nel dubbio non farlo.
          </p>
        </div>

        <!-- Social Buttons -->
        <div class="space-y-3">
          <h3 class="text-xs font-semibold text-text-muted uppercase">Condividi su</h3>
          
          <!-- WhatsApp -->
          <button
            @click="shareOn('whatsapp')"
            class="cursor-pointer w-full flex items-center gap-4 p-4 rounded-xl bg-bg-secondary border border-border hover:border-accent/30 hover:bg-bg-tertiary transition-all group"
          >
            <div class="w-12 h-12 rounded-full bg-[#25D366]/10 flex items-center justify-center text-[#25D366] group-hover:scale-110 transition-transform">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
              </svg>
            </div>
            <div class="flex-1 text-left">
              <div class="font-semibold text-text-primary">WhatsApp</div>
              <div class="text-xs text-text-muted">Condividi via messaggio</div>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-text-muted group-hover:text-accent transition-colors">
              <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
          </button>

          <!-- Facebook -->
          <button
            @click="shareOn('facebook')"
            class="cursor-pointer w-full flex items-center gap-4 p-4 rounded-xl bg-bg-secondary border border-border hover:border-accent/30 hover:bg-bg-tertiary transition-all group"
          >
            <div class="w-12 h-12 rounded-full bg-[#1877F2]/10 flex items-center justify-center text-[#1877F2] group-hover:scale-110 transition-transform">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
              </svg>
            </div>
            <div class="flex-1 text-left">
              <div class="font-semibold text-text-primary">Facebook</div>
              <div class="text-xs text-text-muted">Condividi sul tuo profilo</div>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-text-muted group-hover:text-accent transition-colors">
              <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
          </button>

          <!-- X (Twitter) -->
          <button
            @click="shareOn('x')"
            class="cursor-pointer w-full flex items-center gap-4 p-4 rounded-xl bg-bg-secondary border border-border hover:border-accent/30 hover:bg-bg-tertiary transition-all group"
          >
            <div class="w-12 h-12 rounded-full bg-text-primary/10 flex items-center justify-center text-text-primary group-hover:scale-110 transition-transform">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
              </svg>
            </div>
            <div class="flex-1 text-left">
              <div class="font-semibold text-text-primary">X (Twitter)</div>
              <div class="text-xs text-text-muted">Posta su X</div>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-text-muted group-hover:text-accent transition-colors">
              <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
          </button>

          <!-- Instagram -->
          <button
            @click="shareOn('instagram')"
            class="cursor-pointer w-full flex items-center gap-4 p-4 rounded-xl bg-bg-secondary border border-border hover:border-accent/30 hover:bg-bg-tertiary transition-all group"
          >
            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#833AB4] via-[#FD1D1D] to-[#F77737] flex items-center justify-center text-white group-hover:scale-110 transition-transform">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
              </svg>
            </div>
            <div class="flex-1 text-left">
              <div class="font-semibold text-text-primary">Instagram</div>
              <div class="text-xs text-text-muted">Apri storie (solo mobile)</div>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-text-muted group-hover:text-accent transition-colors">
              <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
          </button>

          <!-- TikTok -->
          <button
            @click="shareOn('tiktok')"
            class="cursor-pointer w-full flex items-center gap-4 p-4 rounded-xl bg-bg-secondary border border-border hover:border-accent/30 hover:bg-bg-tertiary transition-all group"
          >
            <div class="w-12 h-12 rounded-full bg-text-primary/10 flex items-center justify-center text-text-primary group-hover:scale-110 transition-transform">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
              </svg>
            </div>
            <div class="flex-1 text-left">
              <div class="font-semibold text-text-primary">TikTok</div>
              <div class="text-xs text-text-muted">Apri app (solo mobile)</div>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-text-muted group-hover:text-accent transition-colors">
              <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
          </button>
        </div>

        <!-- Copy Link -->
        <div class="space-y-3 pt-4 border-t border-border">
          <h3 class="text-xs font-semibold text-text-muted uppercase">Oppure copia il link</h3>
          
          <div class="flex gap-2">
            <input
              type="text"
              :value="shareUrl"
              readonly
              class="flex-1 px-4 py-2 rounded-lg bg-bg-secondary border border-border text-text-primary text-sm focus:outline-none focus:border-accent/50"
            />
            <button
              @click="copyLink"
              class="cursor-pointer px-4 py-2 rounded-lg bg-accent text-white text-sm font-medium hover:bg-accent-hover transition-all flex items-center gap-1 whitespace-nowrap"
              :class="{ 'bg-green-500 hover:bg-green-600': copied }"
            >
              <svg v-if="!copied" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 8.25V6a2.25 2.25 0 0 0-2.25-2.25H6A2.25 2.25 0 0 0 3.75 6v8.25A2.25 2.25 0 0 0 6 16.5h2.25m8.25-8.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-7.5A2.25 2.25 0 0 1 8.25 18v-1.5m8.25-8.25h-6a2.25 2.25 0 0 0-2.25 2.25v6" />
              </svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
              </svg>
              <span class="hidden sm:inline">{{ copied ? 'Copiato!' : 'Copia' }}</span>
            </button>
          </div>
        </div>

        <!-- Footer message -->
        
      </div>
      <div class="px-6 py-4 border-t border-border">
          <p class="text-sm text-text-muted text-center">
            Condividere giamm.ai è come regalare a qualcuno una doccia fredda di realtà. <span class="text-accent">Ma almeno è gratis</span>.
          </p>
        </div>
      <div class="px-6 py-4 border-t border-border">
          <button
            type="button"
            @click="$emit('close')"
            class="cursor-pointer w-full px-4 py-2.5 rounded-lg bg-accent text-white text-sm font-medium hover:bg-accent-hover transition-colors cursor-pointer"
          >
            Ooook ho capito
          </button>
        </div>
    </div>
  </Transition>
</template>
