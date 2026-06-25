<script setup>
defineProps({
  show: Boolean
})

const emit = defineEmits(['close'])
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
      class="fixed top-0 right-0 bottom-0 z-[70] w-full max-w-2xl bg-bg-primary shadow-2xl overflow-y-auto"
      @click.stop
    >
      <!-- Header -->
      <div class="sticky top-0 z-10 bg-bg-primary/95 backdrop-blur-xl border-b border-border">
        <div class="flex items-center justify-between p-5">
          <h2 class="text-lg font-bold text-text-primary flex items-center gap-2">
            Cos'è giamm<span class="w-2 h-2 rounded-full bg-accent inline-block"></span>ai
          </h2>
          <button
            @click="$emit('close')"
            class="w-10 h-10 rounded-lg flex items-center justify-center text-text-secondary hover:text-text-primary hover:bg-bg-secondary transition-colors"
            title="Chiudi"
          >
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M18 6L6 18M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Content -->
      <div class="p-6 space-y-6 text-text-primary">
        <!-- Intro -->
        <div class="space-y-3 text-sm">
          <p class="">
            <strong class="text-accent">giamm.ai</strong> è l'unico assistente AI che, invece di dirti "puoi farcela!", ti risponde "ma sei sicuro? Perché sembra una pessima idea".
          </p>
          <p class="">
            Pensa a quel amico cinico che smonta ogni tuo progetto prima ancora che tu apra il laptop. Quello che, quando gli dici "voglio aprire un ristorante", ti chiede se hai mai bruciato un toast senza farlo apposta. Esatto, quello lì. Solo che è un bot, lavora 24/7 e non ti deve una birra.
          </p>
        </div>

        <!-- Filosofia -->
        <div class="space-y-4">
          <h3 class="text-lg text-accent font-bold flex items-center gap-2">
            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
            </svg>
            La nostra (malinconica) filosofia
          </h3>
          
          <div class="space-y-3">
            <div class="flex gap-3 p-4 rounded-lg bg-bg-secondary border border-border">
              <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-accent/10 flex items-center justify-center text-accent">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
              <div>
                <h4 class="font-semibold text-text-primary mb-1">Demotivazione Istantanea</h4>
                <p class="text-sm text-text-secondary">
                  Perché incoraggiarti quando possiamo farti risparmiare mesi di sofferenza? Ti smentiamo subito, così non devi nemmeno iniziare.
                </p>
              </div>
            </div>

            <div class="flex gap-3 p-4 rounded-lg bg-bg-secondary border border-border">
              <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-accent/10 flex items-center justify-center text-accent">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
              </div>
              <div>
                <h4 class="font-semibold text-text-primary mb-1">Risposte Veloci come il Tuo Fallimento</h4>
                <p class="text-sm text-text-secondary">
                  Alimentato da Groq AI e Cloudflare Workers per demolire i tuoi sogni a velocità supersonica. Zero latenza, massima delusione.
                </p>
              </div>
            </div>

            <div class="flex gap-3 p-4 rounded-lg bg-bg-secondary border border-border">
              <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-accent/10 flex items-center justify-center text-accent">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                  <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
              </div>
              <div>
                <h4 class="font-semibold text-text-primary mb-1">Privacy Irrisoria</h4>
                <p class="text-sm text-text-secondary">
                  Tutto rimane nel tuo browser. Nemmeno noi vogliamo sapere che idea disastrosa stavi per realizzare.
                </p>
              </div>
            </div>

            <div class="flex gap-3 p-4 rounded-lg bg-bg-secondary border border-border">
              <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-accent/10 flex items-center justify-center text-accent">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
              </div>
              <div>
                <h4 class="font-semibold text-text-primary mb-1">Cronologia delle tue Debacle</h4>
                <p class="text-sm text-text-secondary">
                  Salva ogni conversazione per ricordarti, quando sarai tentato di riprovare, quanto male è andata l'ultima volta.
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Per chi è -->
        <div class="space-y-4">
          <h3 class="text-lg font-bold text-accent flex items-center gap-2">
            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
              <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
            </svg>
            Per chi è giamm.ai
          </h3>
          
          <ul class="space-y-2">
            <li class="flex items-start gap-2 text-accent text-sm">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="mt-1 size-4 flex-shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
              </svg>
              <span class="text-text-primary">Per chi ha un'idea geniale alle 3 di notte e la mattina dopo ha bisogno di qualcuno che gli dica "no, davvero no".</span>
            </li>
            <li class="flex items-start gap-2 text-accent text-sm">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="mt-1 size-4 flex-shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
              </svg>
              <span class="text-text-primary">Per chi vuole aprire un'attività ma non sa nemmeno come funziona il forno di casa.</span>
            </li>
            <li class="flex items-start gap-2 text-accent text-sm">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="mt-1 size-4 flex-shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
              </svg>
              <span class="text-text-primary">Per chi cerca un "sì, puoi farcela!" e invece trova un "perché proprio tu?".</span>
            </li>
            <li class="flex items-start gap-2 text-accent text-sm">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="mt-1 size-4 flex-shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
              </svg>
              <span class="text-text-primary">Per chi ha bisogno di una voce fuori dal coro che gli urla "smettila" prima che spenda i risparmi.</span>
            </li>
          </ul>
        </div>

        <!-- Disclaimer -->
        <div class="p-4 rounded-lg bg-accent/5 border border-accent/20">
          <h4 class="font-semibold text-accent mb-2 flex items-center gap-2">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            Disclaimer Legale
          </h4>
          <p class="text-sm text-text-secondary leading-relaxed">
            giamm.ai non è responsabile se, nonostante i nostri sforzi per dissuaderti, decidi comunque di farlo. Se apri quel ristorante, se lanci quella startup, se scrivi quel romanzo... e fallisci, non dirci che non ti avevamo avvertito. <em>Perché lo abbiamo fatto. Più volte. In dettaglio.</em>
          </p>
        </div>

        <!-- Tech Stack -->
        <div class="space-y-4">
          <h3 class="text-lg font-bold text-accent flex items-center gap-2">
            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
              <path d="M9.4 16.6L4.8 12l4.6-4.6L8 6l-6 6 6 6 1.4-1.4zm5.2 0l4.6-4.6-4.6-4.6L16 6l6 6-6 6-1.4-1.4z"/>
            </svg>
            Tecnologia che funziona (a differenza tua)
          </h3>
          
          <div class="grid grid-cols-2 gap-3">
            <div class="p-3 rounded-lg bg-bg-secondary border border-border text-center">
              <div class="font-semibold text-text-primary">Cloudflare</div>
              <div class="text-xs text-text-muted mt-1">Perché i tuoi fallimenti devono essere veloci</div>
            </div>
            <div class="p-3 rounded-lg bg-bg-secondary border border-border text-center">
              <div class="font-semibold text-text-primary">Groq AI</div>
              <div class="text-xs text-text-muted mt-1">Il cervello dietro le tue delusioni</div>
            </div>
            <div class="p-3 rounded-lg bg-bg-secondary border border-border text-center">
              <div class="font-semibold text-text-primary">Vue 3</div>
              <div class="text-xs text-text-muted mt-1">Reattivo come le tue scuse</div>
            </div>
            <div class="p-3 rounded-lg bg-bg-secondary border border-border text-center">
              <div class="font-semibold text-text-primary">Archiviazione locale</div>
              <div class="text-xs text-text-muted mt-1">Memoria più lunga della tua</div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        
      </div>
      <div class="py-6 border-t border-border">
          <p class="text-sm text-text-muted text-center">
            Fatto con <span class="text-accent">cattiveria</span> per chi ha idee e non dovrebbe realizzarle
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
