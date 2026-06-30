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
      class="fixed top-0 right-0 bottom-0 z-[70] w-full max-w-3xl bg-bg-primary shadow-2xl flex flex-col"
      @click.stop
    >
      <!-- Header -->
      <div class="sticky top-0 z-10 bg-bg-primary/95 backdrop-blur-xl border-b border-border">
        <div class="flex items-center justify-between p-5">
          <h2 class="text-lg font-bold text-text-primary flex items-center gap-2">
            I limiti (che ti servono)
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
            Hai a disposizione <strong class="text-accent">10 domande al giorno</strong>.
          </p>
          <p class="">
            Sì, hai capito bene. Non 11. Non 100. Dieci. Come le dita delle tue mani. Che probabilmente userai per scrivere altre domande inutili domani.
          </p>
          <p class="">
            Ma tranquillo, è per il tuo bene. Davvero.
          </p>
        </div>

        <!-- Perché 10? -->
        <div class="p-5 rounded-xl bg-bg-secondary border border-border">
          <h3 class="text-lg font-bold text-text-primary mb-3 flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4 text-accent">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
            </svg>
            <span class="text-sm font-bold text-text-primary">
            Perché proprio 10?
            </span>
          </h3>
          <p class="text-sm text-text-secondary leading-relaxed">
            Perché se hai bisogno di fare più di 10 domande al giorno a un'AI che ti scoraggia, probabilmente hai problemi più grandi da risolvere. Tipo: perché stai chiedendo consiglio a un chatbot sarcastico invece di fare le cose?
          </p>
        </div>

        <!-- Ma io ne ho bisogno di più! -->
        <div class="p-5 rounded-xl bg-bg-secondary border border-border">
          <h3 class="text-lg font-bold text-text-primary mb-3 flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4 text-accent">
              <path stroke-linecap="round" stroke-linejoin="round" d="M7.498 15.25H4.372c-1.026 0-1.945-.694-2.054-1.715a12.137 12.137 0 0 1-.068-1.285c0-2.848.992-5.464 2.649-7.521C5.287 4.247 5.886 4 6.504 4h4.016a4.5 4.5 0 0 1 1.423.23l3.114 1.04a4.5 4.5 0 0 0 1.423.23h1.294M7.498 15.25c.618 0 .991.724.725 1.282A7.471 7.471 0 0 0 7.5 19.75 2.25 2.25 0 0 0 9.75 22a.75.75 0 0 0 .75-.75v-.633c0-.573.11-1.14.322-1.672.304-.76.93-1.33 1.653-1.715a9.04 9.04 0 0 0 2.86-2.4c.498-.634 1.226-1.08 2.032-1.08h.384m-10.253 1.5H9.7m8.075-9.75c.01.05.027.1.05.148.593 1.2.925 2.55.925 3.977 0 1.487-.36 2.89-.999 4.125m.023-8.25c-.076-.365.183-.75.575-.75h.908c.889 0 1.713.518 1.972 1.368.339 1.11.521 2.287.521 3.507 0 1.553-.295 3.036-.831 4.398-.306.774-1.086 1.227-1.918 1.227h-1.053c-.472 0-.745-.556-.5-.96a8.95 8.95 0 0 0 .303-.54" />
            </svg>
            <span class="text-sm font-bold text-text-primary">
            "Ma io ne voglio di più!"
            </span>
          </h3>
          <p class="text-sm text-text-secondary leading-relaxed mb-3">
            No, non ne hai bisogno. Hai bisogno di:
          </p>
          <ul class="space-y-1 text-sm text-text-secondary">
            <li class="flex items-start gap-1">
              <span class="text-accent">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="mt-0.5 size-4 flex-shrink-0">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
              </span>
              <span><strong>Pensare</strong> prima di chiedere. Sì, quella cosa che facevi prima che esistessero le AI.</span>
            </li>
            <li class="flex items-start gap-1">
              <span class="text-accent">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="mt-0.5 size-4 flex-shrink-0">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
              </span>
              <span><strong>Fare domande migliori</strong>. Una domanda ben posta vale più di 10 domande a caso.</span>
            </li>
            <li class="flex items-start gap-1">
              <span class="text-accent">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="mt-0.5 size-4 flex-shrink-0">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
              </span>
              <span><strong>Agire</strong> invece di chiedere. Seriamente, smetti di procrastinare.</span>
            </li>
          </ul>
        </div>

        <!-- Cosa succede dopo le 10? -->
        <div class="p-5 rounded-xl bg-bg-secondary border border-border">
          <h3 class="text-lg font-bold text-text-primary mb-3 flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4 text-accent">
              <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
            </svg>
            <span class="text-sm font-bold text-text-primary">
              Cosa succede dopo le 10 domande?
            </span>
          </h3>
          <p class="text-sm text-text-secondary leading-relaxed">
            Niente. Il contatore si resetta a mezzanotte. Torni ad avere 10 domande fresche. E probabilmente le sprecherai di nuovo chiedendo cose tipo "dovrei aprire un ristorante vegano per gatti?" (Spoiler: no).
          </p>
        </div>

        <!-- Posso pagare per averne di più? -->
        <div class="p-5 rounded-xl bg-bg-secondary border border-border">
          <h3 class="text-lg font-bold text-text-primary mb-3 flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4 text-accent">
              <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 7.756a4.5 4.5 0 1 0 0 8.488M7.5 10.5h5.25m-5.25 3h5.25M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>

            <span class="text-sm font-bold text-text-primary">
              "Posso pagare per averne di più?"
            </span>
          </h3>
          <p class="text-sm text-text-secondary leading-relaxed">
            No. Questo è un servizio gratuito fatto per scoraggiarti, non per svuotarti il portafoglio. Se vuoi sprecare soldi, ci sono mille modi migliori: criptovalute, NFT, quel corso online che ti promette di diventare milionario in 30 giorni...
          </p>
        </div>

        <!-- Il vero motivo -->
        <div class="p-5 rounded-xl bg-accent/5 border border-accent/20">
          <h3 class="text-lg font-bold text-accent mb-3 flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
            </svg>
            <span class="text-sm font-bold">
            Il vero motivo (ok, parliamo seriamente)
            </span>
          </h3>
          <p class="text-sm text-text-secondary leading-relaxed">
            Dietro giamm.ai ci sono costi reali: server, API, caffè per chi lo mantiene attivo. Il limite di 10 domande ci permette di offrire questo servizio <strong>gratis a tutti</strong>, senza pubblicità, senza vendere i tuoi dati, senza abbonamenti premium. È il nostro modo di dire: "Usalo, ma usalo bene. Pensaci prima di sprecare una domanda.", un po' come il genio della lampada. Più di questo cosa vuoi? Il sangue??
          </p>
        </div>

        <!-- Consigli finali -->
        <div class="p-4 rounded-lg bg-bg-secondary border border-border">
             
          <h3 class="text-lg font-bold text-accent mb-3 flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" />
            </svg> 
            <span class="text-sm font-bold text-text-primary">
            Consiglio da amico
            </span>
          </h3>
          <p class="text-sm text-text-secondary leading-relaxed">
            Se arrivi a fine giornata e hai ancora domande disponibili, complimenti: o sei una persona saggia che pensa prima di agire, o semplicemente non hai fatto niente tutto il giorno. Nel dubbio, è la seconda.
          </p>
        </div>
      </div>

      <!-- Footer -->
      <div class="py-4 border-t border-border">
        <p class="text-sm text-text-muted text-center">
          10 domande al giorno. Non una di più. <span class="text-accent">Pensaci bene.</span>
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
