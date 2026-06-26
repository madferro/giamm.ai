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
            Pensa a quell'amico cinico che smonta ogni tuo progetto prima ancora che tu apra il laptop. Quello che, quando gli dici "voglio aprire un ristorante", ti chiede se hai mai bruciato un toast senza farlo apposta. Esatto, quello lì. Solo che è un bot, lavora 24/7 e non ti deve una birra.
          </p>
        </div>

        <!-- Filosofia -->
        <div class="space-y-4">
          <h3 class="text-lg text-accent font-semibold flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
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
                <h4 class="font-semibold text-text-primary mb-1">Demotivazione istantanea</h4>
                <p class="text-sm text-text-secondary">
                  Perché incoraggiarti quando possiamo farti risparmiare mesi di sofferenza? Ti smontiamo subito, così non devi nemmeno iniziare.
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
                <h4 class="font-semibold text-text-primary mb-1">Risposte veloci come il tuo fallimento</h4>
                <p class="text-sm text-text-secondary">
                  Alimentato da Groq AI e Cloudflare per demolire i tuoi sogni a velocità supersonica. Zero latenza, massima delusione.
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
                <h4 class="font-semibold text-text-primary mb-1">Privacy irrisoria</h4>
                <p class="text-sm text-text-secondary">
                  Tutto rimane nel tuo browser. Nemmeno noi vogliamo sapere che idea disastrosa stavi per realizzare.
                </p>
              </div>
            </div>

            <div class="flex gap-3 p-4 rounded-lg bg-bg-secondary border border-border">
              <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-accent/10 flex items-center justify-center text-accent">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" />
                </svg>

              </div>
              <div>
                <h4 class="font-semibold text-text-primary mb-1">Cronologia delle tue debacle</h4>
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
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
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
              <span class="text-text-primary">Per chi cerca un "sì, puoi farcela!" e invece trova un "e se poi te ne penti?".</span>
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
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>

            Disclaimer Legale
          </h4>
          <p class="text-sm text-text-secondary leading-relaxed">
            giamm.ai non è responsabile se, nonostante i nostri sforzi per dissuaderti, decidi comunque di farlo. Se apri quel ristorante, se lanci quella startup, se scrivi quel romanzo... e fallisci, non dirci che non ti avevamo avvertito. <em><strong>Perché lo abbiamo fatto. Più volte. In dettaglio.</strong></em>
          </p>
        </div>

        <!-- Tech Stack -->
        <div class="space-y-4">
          <h3 class="text-lg font-bold text-accent flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5" />
            </svg>


            Tecnologia che funziona (a differenza tua)
          </h3>
          
          <div class="grid grid-cols-2 gap-3">
            <div class="p-3 rounded-lg bg-bg-secondary border border-border text-center">
              <div class="font-semibold text-text-primary">Cloudflare</div>
              <div class="text-xs text-text-muted mt-1">Perché i tuoi fallimenti devono essere protetti</div>
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
