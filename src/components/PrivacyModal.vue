<script setup>
import { ref, onMounted, watch } from 'vue'

const emit = defineEmits(['accept'])

const showModal = ref(false)
const hasScrolledToBottom = ref(false)
const scrollContainer = ref(null)

// Blocca/sblocca scroll quando si apre/chiude il modal
watch(showModal, (newVal) => {
  if (newVal) {
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = ''
  }
})

onMounted(() => {
  // Controlla se l'utente ha già accettato
  const hasAccepted = localStorage.getItem('giammai_privacy_accepted')
  if (!hasAccepted) {
    showModal.value = true
  }
})

const handleScroll = () => {
  if (!scrollContainer.value) return
  
  const { scrollTop, scrollHeight, clientHeight } = scrollContainer.value
  // Considera "bottom" quando mancano meno di 20px
  if (scrollHeight - scrollTop - clientHeight < 20) {
    hasScrolledToBottom.value = true
  }
}

const acceptPrivacy = () => {
  localStorage.setItem('giammai_privacy_accepted', 'true')
  localStorage.setItem('giammai_privacy_accepted_date', new Date().toISOString())
  showModal.value = false
  emit('accept')
}
</script>

<template>
  <!-- Overlay sbarrante -->
  <div 
    v-if="showModal" 
    class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4"
  >
    <div class="bg-bg-primary border border-border-strong rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] flex flex-col">
      <!-- Header -->
      <div class="px-6 py-5 border-b border-border">
        <div class="flex items-center gap-3">
          <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
          </svg>
          <h2 class="text-xl font-semibold text-text-primary">Informativa Privacy</h2>
        </div>
        <p class="text-sm text-text-secondary mt-2">
          Prima di utilizzare giamm.ai, leggi e accetta la nostra informativa sulla privacy
        </p>
      </div>

      <!-- Content scrollabile -->
      <div 
        ref="scrollContainer"
        @scroll="handleScroll"
        class="flex-1 overflow-y-auto px-6 py-4 text-sm text-text-primary space-y-4"
      >
        <p class="text-xs text-text-muted"><strong>Ultimo aggiornamento:</strong> Gennaio 2025</p>

        <section>
          <h3 class="font-semibold text-base mb-2">Chi siamo</h3>
          <p class="text-text-secondary mb-2">
            giamm.ai è un'applicazione web a scopo ludico che fornisce risposte ironiche e umoristiche tramite sistemi di intelligenza artificiale.
          </p>
          <p class="text-text-secondary">
            Per qualsiasi informazione relativa alla presente informativa è possibile contattare il titolare del sito all'indirizzo:
          </p>
          <p class="text-text-secondary mt-2">
            <strong>Email:</strong> [TUO INDIRIZZO EMAIL]
          </p>
        </section>

        <section>
          <h3 class="font-semibold text-base mb-2">Quali dati vengono trattati</h3>
          <p class="text-text-secondary mb-2">
            Quando utilizzi giamm.ai, i messaggi che inserisci nella chat vengono inviati al servizio di intelligenza artificiale utilizzato per generare la risposta.
          </p>
          <p class="text-text-secondary mb-2">
            Le conversazioni <strong>non vengono archiviate sui server di giamm.ai</strong>.
          </p>
          <p class="text-text-secondary">
            Lo storico della chat, se presente, viene conservato <strong>esclusivamente nel browser dell'utente</strong> tramite le funzionalità di memorizzazione locale del browser (ad esempio IndexedDB o tecnologie analoghe).
          </p>
        </section>

        <section>
          <h3 class="font-semibold text-base mb-2">Finalità del trattamento</h3>
          <p class="text-text-secondary mb-2">I dati vengono trattati esclusivamente per:</p>
          <ul class="list-disc list-inside text-text-secondary space-y-1">
            <li>fornire il servizio richiesto dall'utente;</li>
            <li>generare le risposte della chat;</li>
            <li>garantire il corretto funzionamento tecnico del sito;</li>
            <li>prevenire eventuali utilizzi abusivi del servizio.</li>
          </ul>
        </section>

        <section>
          <h3 class="font-semibold text-base mb-2">Servizi di terze parti</h3>
          <p class="text-text-secondary mb-2">
            Per generare le risposte, i messaggi inviati dall'utente vengono trasmessi al fornitore di servizi di intelligenza artificiale:
          </p>
          <p class="text-text-secondary mb-2">
            <strong>Groq, Inc.</strong>
          </p>
          <p class="text-text-secondary mb-2">
            L'utilizzo del servizio comporta pertanto la comunicazione dei messaggi inseriti dall'utente a tale fornitore.
          </p>
          <p class="text-text-secondary">
            Per maggiori informazioni si invita a consultare la documentazione privacy disponibile sul sito del fornitore.
          </p>
        </section>

        <section>
          <h3 class="font-semibold text-base mb-2">Dati tecnici e log</h3>
          <p class="text-text-secondary mb-2">
            Come qualsiasi sito web, il server può registrare automaticamente alcuni dati tecnici necessari al funzionamento e alla sicurezza del servizio, quali:
          </p>
          <ul class="list-disc list-inside text-text-secondary space-y-1">
            <li>indirizzo IP;</li>
            <li>data e ora della richiesta;</li>
            <li>informazioni sul browser utilizzato;</li>
            <li>dati tecnici relativi alla connessione.</li>
          </ul>
          <p class="text-text-secondary mt-2">
            Tali dati vengono utilizzati esclusivamente per finalità tecniche e di sicurezza.
          </p>
        </section>

        <section>
          <h3 class="font-semibold text-base mb-2">Cookie e tecnologie analoghe</h3>
          <p class="text-text-secondary mb-2">
            giamm.ai non utilizza cookie di profilazione né strumenti pubblicitari.
          </p>
          <p class="text-text-secondary">
            Eventuali tecnologie di memorizzazione locale del browser sono utilizzate esclusivamente per il funzionamento del servizio e per mantenere lo storico delle conversazioni sul dispositivo dell'utente.
          </p>
        </section>

        <section>
          <h3 class="font-semibold text-base mb-2">Conservazione dei dati</h3>
          <p class="text-text-secondary mb-2">
            Le conversazioni <strong>non vengono conservate sui server di giamm.ai</strong>.
          </p>
          <p class="text-text-secondary mb-2">
            Lo storico delle conversazioni rimane esclusivamente sul dispositivo dell'utente e può essere eliminato in qualsiasi momento tramite le funzionalità del browser.
          </p>
          <p class="text-text-secondary">
            I dati tecnici eventualmente presenti nei log del server vengono conservati per il tempo strettamente necessario a garantire la sicurezza e il corretto funzionamento del servizio.
          </p>
        </section>

        <section>
          <h3 class="font-semibold text-base mb-2">Diritti degli utenti</h3>
          <p class="text-text-secondary mb-2">
            Gli utenti possono esercitare i diritti previsti dal Regolamento (UE) 2016/679 (GDPR), nei limiti applicabili al trattamento effettuato.
          </p>
          <p class="text-text-secondary">
            Per qualsiasi richiesta è possibile contattare il titolare all'indirizzo email sopra indicato.
          </p>
        </section>

        <section>
          <h3 class="font-semibold text-base mb-2">Modifiche alla presente informativa</h3>
          <p class="text-text-secondary mb-2">
            La presente informativa può essere aggiornata nel tempo.
          </p>
          <p class="text-text-secondary">
            Eventuali modifiche saranno pubblicate su questa pagina con l'indicazione della data di aggiornamento.
          </p>
        </section>
      </div>

      <!-- Footer con pulsante -->
      <div class="px-6 py-4 border-t border-border bg-bg-secondary">
        <!-- Indicatore scroll -->
        <div v-if="!hasScrolledToBottom" class="mb-3 flex items-center gap-2 text-xs text-text-muted">
          <svg class="w-4 h-4 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
          <span>Scorri fino in fondo per accettare</span>
        </div>

        <button
          @click="acceptPrivacy"
          :disabled="!hasScrolledToBottom"
          :class="[
            'w-full py-3 px-4 rounded-lg font-medium transition-all',
            hasScrolledToBottom
              ? 'bg-accent hover:bg-accent-hover text-white cursor-pointer'
              : 'bg-bg-tertiary text-text-muted cursor-not-allowed opacity-50'
          ]"
        >
          {{ hasScrolledToBottom ? 'Accetto l\'informativa privacy' : 'Leggi l\'informativa completa' }}
        </button>

        <p class="text-xs text-text-muted text-center mt-3">
          Continuando accetti il trattamento dei dati come descritto sopra
        </p>
      </div>
    </div>
  </div>
</template>
