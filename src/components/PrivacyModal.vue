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

const rejectPrivacy = () => {
  // Prova a chiudere il tab (funziona solo se aperto via script)
  window.close()
  // Se non è riuscito (tab aperto manualmente), svuota la pagina
  document.body.innerHTML = `
    <div style="display:flex;align-items:center;justify-content:center;height:100vh;font-family:sans-serif;background:#0a0a0f;color:#e2e8f0;text-align:center;padding:2rem;">
      <div>
        <h1 style="font-size:1.5rem;margin-bottom:1rem;color:#f59e0b;">Accesso negato</h1>
        <p style="margin-bottom:1.5rem;max-width:400px;line-height:1.6;">
          Hai scelto di non accettare l'informativa privacy. Per utilizzare giamm.ai è necessario accettare il trattamento dei dati.
        </p>
        <p style="font-size:0.875rem;color:#94a3b8;">Chiudi questa scheda o ricarica la pagina per continuare.</p>
      </div>
    </div>
  `
  document.title = 'Accesso negato - giamm.ai'
}
</script>

<template>
  <!-- Overlay sbarrante -->
  <div 
    v-if="showModal" 
    class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4"
  >
    <div class="bg-bg-primary overflow-hidden border border-border-strong rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] flex flex-col">
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
        <p class="text-xs text-text-muted"><strong>Ultimo aggiornamento:</strong> Giugno 2026</p>

        <section>
          <h3 class="font-semibold text-base mb-2">Chi siamo</h3>
          <p class="text-text-secondary mb-2">
            giamm.ai è un'applicazione web a scopo ludico che fornisce risposte ironiche e umoristiche tramite sistemi di intelligenza artificiale.
          </p>
          <p class="text-text-secondary">
            Per qualsiasi informazione relativa alla presente informativa è possibile contattare il titolare del sito all'indirizzo email: <a href="mailto:lamentati@giamm.ai" class="text-accent" target="_blank">lamentati@giamm.ai</a>
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
          <p class="text-text-secondary mb-2">
            Inoltre, l'applicazione è distribuita tramite <strong>Cloudflare, Inc.</strong>, che gestisce l'infrastruttura di rete per garantire velocità e sicurezza. Cloudflare può utilizzare cookie tecnici necessari alla protezione del servizio e alla sua corretta erogazione.
          </p>
          <p class="text-text-secondary">
            Per maggiori informazioni consulta le privacy policy dei rispettivi fornitori:
            <a href="https://groq.com/privacy" target="_blank" rel="noopener noreferrer" class="text-accent hover:underline">Groq Privacy Policy</a> e
            <a href="https://www.cloudflare.com/privacypolicy/" target="_blank" rel="noopener noreferrer" class="text-accent hover:underline">Cloudflare Privacy Policy</a>.
          </p>
        </section>

        <section>
          <h3 class="font-semibold text-base mb-2">Dati tecnici</h3>
          <p class="text-text-secondary mb-2">
            giamm.ai non raccoglie né conserva indirizzi IP o altri dati identificativi degli utenti. L'applicazione è distribuita tramite Cloudflare Workers, un'infrastruttura serverless che gestisce le richieste senza che il nostro codice abbia accesso a log di sistema contenenti dati personali.
          </p>
          <p class="text-text-secondary mb-2">
            Il solo dato "tecnico" che tocchiamo è il testo che scrivi nella chat, e solo per il tempo necessario a inoltrarlo a Groq e ricevere la risposta. Poi dimentichiamo tutto, come fai tu con i buoni propositi di gennaio.
          </p>
          <p class="text-text-secondary">
            Cloudflare, in qualità di provider di infrastruttura, potrebbe gestire dati tecnici necessari alla sicurezza della rete in conformità con la propria privacy policy.
          </p>
        </section>

        <section>
          <h3 class="font-semibold text-base mb-2">Cookie e tecnologie analoghe</h3>
          <p class="text-text-secondary mb-2">
            giamm.ai non utilizza cookie di profilazione né strumenti pubblicitari.
          </p>
          <p class="text-text-secondary mb-2">
            Cloudflare può utilizzare cookie tecnici (ad esempio <code>__cf_bm</code>, <code>__cflb</code>) esclusivamente per finalità di sicurezza della rete, mitigazione degli attacchi DDoS e bilanciamento del carico. Questi cookie sono necessari al funzionamento dell'infrastruttura e non contengono informazioni personali identificative.
          </p>
          <p class="text-text-secondary">
            Eventuali tecnologie di memorizzazione locale del browser (come IndexedDB) sono utilizzate esclusivamente per mantenere lo storico delle conversazioni sul dispositivo dell'utente.
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

        <div class="grid grid-cols-2 gap-3">
          <button
            @click="rejectPrivacy"
            class="py-3 px-4 rounded-lg font-medium border border-border text-text-secondary hover:text-text-primary hover:bg-bg-tertiary transition-all cursor-pointer"
          >
            Rifiuta e chiudi
          </button>

          <button
            @click="acceptPrivacy"
            :disabled="!hasScrolledToBottom"
            :class="[
              'py-3 px-4 rounded-lg font-medium transition-all',
              hasScrolledToBottom
                ? 'bg-accent hover:bg-accent-hover text-white cursor-pointer'
                : 'bg-bg-tertiary text-text-muted cursor-not-allowed opacity-50'
            ]"
          >
            {{ hasScrolledToBottom ? 'Accetto' : 'Leggi tutto' }}
          </button>
        </div>

        <p class="text-xs text-text-muted text-center mt-3">
          Continuando accetti il trattamento dei dati come descritto sopra
        </p>
      </div>
    </div>
  </div>
</template>
