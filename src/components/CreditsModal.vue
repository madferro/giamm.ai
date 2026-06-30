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
      class="fixed top-0 right-0 bottom-0 z-[70] w-full max-w-3xl bg-bg-primary shadow-2xl overflow-y-auto flex flex-col"
      @click.stop
    >
      <!-- Header -->
      <div class="sticky top-0 z-10 bg-bg-primary/95 backdrop-blur-xl border-b border-border">
        <div class="flex items-center justify-between p-5">
          <h2 class="text-lg font-bold text-text-primary flex items-center gap-2">
            Chi c'è dietro giamm<span class="w-2 h-2 rounded-full bg-accent inline-block"></span>ai
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
      <div class="p-6 space-y-4 text-text-primary flex-1 overflow-y-auto">
        <!-- Intro -->
        <div class="space-y-3 text-sm">
          <p class="">
            Dietro ogni grande idea c'è qualcuno che si è fatto le 3 di notte a scrivere codice. E dietro ogni codice scritto alle 3 di notte c'è qualcuno che ha dovuto sopportarlo il giorno dopo.
          </p>
          <p class="">
            Questa è la squadra che ha reso possibile il sogno (o l'incubo, dipende dai punti di vista) che è giamm.ai.
          </p>
        </div>

        <!-- Alessandro -->
        <div class="flex gap-4 p-5 rounded-xl bg-bg-secondary border border-border">
          <div class="flex-shrink-0 w-14 h-14 rounded-full bg-accent/10 flex items-center justify-center text-accent text-xl font-bold">
            FS
          </div>
          <div>
            <h3 class="text-lg font-bold text-text-primary mb-1">Ferro San</h3>
            <p class="text-sm text-accent font-medium mb-2">Codice, sudore e caffeina</p>
            <p class="text-sm text-text-secondary leading-relaxed">
              L'uomo che ha trasformato l'arte del "non farlo" in un'architettura software. Ha scritto più righe di codice che messaggi alla sua famiglia. Sostiene che il vero bug sia chi non usa giamm.ai. Si sveglia alle 7, ma il suo cervello parte intorno alle 10. Lavora principalmente di notte, quando il mondo tace e le API sono più veloci.
            </p>
          </div>
        </div>

        <!-- Fulvia -->
        <div class="flex gap-4 p-5 rounded-xl bg-bg-secondary border border-border">
          <div class="flex-shrink-0 w-14 h-14 rounded-full bg-accent/10 flex items-center justify-center text-accent text-xl font-bold">
            FB
          </div>
          <div>
            <h3 class="text-lg font-bold text-text-primary mb-1">Fulvia Becherini</h3>
            <p class="text-sm text-accent font-medium mb-2">Logo, branding e colore rosso</p>
            <p class="text-sm text-text-secondary leading-relaxed">
              Ha guardato il logo e ha detto "no". Poi ha guardato il branding e ha detto "ancora no". Alla fine ha creato lei tutto quanto e ha detto "così sì, ma non fatevi male". Ha scelto i colori, i caratteri e l'atmosfera generale. Se giamm.ai sembra figo, è merito suo. Se sembra deprimente... beh, è proprio quello che volevamo.
            </p>
          </div>
        </div>

        <!-- Testers -->
        <div class="flex gap-4 p-5 rounded-xl bg-bg-secondary border border-border">
          <div class="flex-shrink-0 w-14 h-14 rounded-full bg-accent/10 flex items-center justify-center text-accent text-xl">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>
          </div>
          <div>
            <h3 class="text-lg font-bold text-text-primary mb-1">La Caserma</h3>
            <p class="text-sm text-accent font-medium mb-2">Testers, ombre e rompiscatole professionali</p>
            <p class="text-sm text-text-secondary leading-relaxed">
              Sono quelli che non appariranno mai qui, ma senza di loro giamm.ai sarebbe ancora convinto che "aprire un ristorante" sia una buona idea. Hanno stressato l'AI con domande assurde, rotto il codice in modi creativi, e hanno trovato bug che nessuno chiedeva di trovare. Restano nell'ombra, come ninja. Come fantasmi. Come quella sensazione che hai quando giamm.ai ti dice "no, non farlo" e tu lo fai comunque.
            </p>
          </div>
        </div>

        <!-- Disclaimer spiritoso -->
        

        <!-- Footer -->
      </div>
      <div class="px-6 py-4 border-t border-border">
          <p class="text-sm text-text-muted text-center">
            Un team improbabile. Un progetto che <span class="text-accent">non doveva funzionare</span>. E infatti nessuno sa cosa sia andato storto.
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
