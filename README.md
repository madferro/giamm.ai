# giamm.ai — L'AI che ti scoraggia con stile

> "Sei sicuro di voler clonare questo repository? Davvero sicuro?"

Benvenuto. Hai appena trovato l'unica applicazione web al mondo che non ti aiuta, ma ti scoraggia. Con ironia. E con un'imbarazzante dose di melodramma.

---

## Cosa fa giamm.ai?

Scrivi qualsiasi cosa. *Qualsiasi cosa.*

L'AI ti risponderà con una dissuasione così creativa, così teatralmente catastrofica, che ti convincerà a **non fare nulla** per il resto della giornata.

Esempio:
- **Tu:** "Voglio imparare a cucinare"
- **AI:** "Il primo passo è sempre il più difficile. Poi arriva il secondo, che è tagliarsi. E il terzo, che è bruciare la cucina. Statisticamente il 98% delle persone che iniziano a cucinare finiscono per ordinare sushi alle 23:00. Tu vuoi davvero diventare una statistica?"

Perfetto, vero?

---

## Stack Tecnologico (perché non poteva essere più semplice?)

Abbiamo abbandonato il backend PHP. Sì, proprio così. PHP se n'è andato, come i tuoi sogni di gloria.

- **Vue 3** + `<script setup>` — Perché le opzioni API sono per chi ha ancora speranze nella vita
- **Tailwind CSS v4** — Compilato, non CDN. Sì, abbiamo sofferto.
- **Cloudflare Workers** — Il tuo codice gira su 300+ data center nel mondo. Così puoi fallire globalmente, non solo a casa tua
- **Wrangler** — Il CLI che ti permette di testare il Worker in locale. Sì, serve un altro tool. No, non ti scoraggiamo abbastanza.
- **Groq API** — Veloce, gratis e altrettanto cinica di noi
- **IndexedDB + AES-GCM** — Crittografia locale. Nemmeno noi vogliamo sapere che idea disastrosa stavi per realizzare
- **Vite** — Perché aspettare che Webpack compili è come aspettare che tu realizzi un progetto: troppo lento.

> *"Abbiamo spostato tutto su Cloudflare perché i server tradizionali sono lenti. Come te quando hai un'idea."*

---

## Requisiti

- **Node.js** >= 18 (se hai una versione più vecchia, anche i tuoi progetti sono vecchi, figurati)
- **npm** (o pnpm, se sei quel tipo di persona)
- Una **GROQ_API_KEY** gratuita da [console.groq.com](https://console.groq.com/)
- Due terminali aperti (sì, due. Perché uno solo è troppo facile)

---

## Installazione (se proprio devi)

### 1. Clona (ma sei sicuro?)

```bash
git clone https://github.com/madferro/giamm.ai.git
cd giamm.ai
```

### 2. Installa dipendenze Frontend

```bash
npm install
```

### 3. Installa dipendenze Worker

```bash
cd worker
npm install
```

### 4. Configura la chiave API

```bash
cp .dev.vars.example .dev.vars
```

Modifica `worker/.dev.vars`:
```env
GROQ_API_KEY=gsk_la_tua_chiave_qui
```

> Ottieni la chiave gratuita su [console.groq.com](https://console.groq.com/)

---

## Avvio in Locale (2 terminali, perché siamo complicati)

### Terminal 1 — Avvia il Worker

```bash
cd worker
npm run dev
```

Vedrai:
```
⛅️ wrangler 3.x.x
------------------
⎔ Starting local server...
[wrangler:inf] Ready on http://localhost:8787
```

### Terminal 2 — Avvia il Frontend

```bash
# Dalla root del progetto
npm run dev
```

Vedrai:
```
VITE v5.x.x  ready in xxx ms

➜  Local:   http://localhost:5173/
```

### 5. Testa nel browser

Apri **http://localhost:5173** e prova a scrivere qualcosa.

> **Nota:** Il frontend si connette automaticamente al Worker su `localhost:8787` tramite il proxy di Vite. Non devi toccare nulla. A differenza dei tuoi progetti, questa parte funziona da sola.

---

## Struttura del Progetto

```
giamm.ai/
├── src/                      ← Vue components (ironici per natura)
│   ├── App.vue               ← Il contenitore di tutte le tue future delusioni
│   ├── main.js               ← Entry point, perché tutto deve iniziare da qualche parte
│   ├── components/
│   │   ├── WelcomeScreen.vue      ← Ti dà il benvenuto (ma non troppo)
│   │   ├── ChatMessages.vue       ← Mostra la cronologia dei tuoi fallimenti
│   │   ├── ChatMessage.vue        ← Singolo messaggio (tuo o dell'AI cinica)
│   │   ├── ChatInput.vue          ← Dove scrivi le tue idee terribili
│   │   ├── ToneSelector.vue       ← Scegli quanto vuoi soffrire
│   │   ├── Sidebar.vue            ← La cronologia delle tue debacle
│   │   ├── PrivacyModal.vue       ← Perché la legge ci obbliga
│   │   ├── AboutModal.vue         ← Dove spieghiamo perché esistiamo (spoiler: nemmeno noi lo sappiamo)
│   │   ├── CreditsModal.vue       ← I geni dietro questa pessima idea
│   │   ├── ShareModal.vue         ← Condividi il dolore con gli amici
│   │   ├── LimitsModal.vue        ← Perché solo 10 domande? Perché sì.
│   │   └── ConfirmDialog.vue      ← Per quando vuoi cancellare tutto (e chi ti biasima?)
│   ├── utils/
│   │   ├── db.js                  ← IndexedDB per la cronologia (criptata!)
│   │   └── crypto.js              ← AES-GCM, perché la vergogna va protetta
│   └── style.css                  ← Tailwind v4 con @theme (e un cuore che pulsa)
├── worker/                   ← Cloudflare Worker (il cervello cinico)
│   ├── src/index.js          ← Gestisce le API e le risposte sarcastiche
│   ├── package.json
│   ├── wrangler.toml         ← Configurazione Cloudflare
│   └── .dev.vars.example     ← Template per la tua GROQ_API_KEY
├── public/                   ← File statici (niente PHP, abbiamo imparato la lezione)
│   └── favicon.ico           ← L'icona delle tue speranze infrante
├── index.html                ← Il punto di partenza di tutto questo casino
├── vite.config.js            ← Proxy verso il Worker
├── package.json              ← Dipendenze frontend (troppe, come sempre)
└── README.md                 ← Tu sei qui. Brutto posto per essere.
```

---

## Troubleshooting

### Il Worker non si avvia

```bash
# Verifica che la porta 8787 sia libera
lsof -i :8787

# Se occupata, uccidi il processo
kill -9 <PID>
```

### La chiave API non funziona

```bash
# Verifica che .dev.vars esista
ls -la worker/.dev.vars

# Verifica il contenuto (senza mostrare la chiave)
cat worker/.dev.vars | grep GROQ_API_KEY
```

### Il Frontend non si connette al Worker

Verifica `vite.config.js`:

```javascript
proxy: {
  '/api': {
    target: 'http://localhost:8787',  // ← Deve puntare al Worker
    changeOrigin: true,
  },
}
```

### npm install fallisce con ECONNRESET

Se vedi errori di rete durante `npm install`, prova:

```bash
npm config set maxsockets 1
npm config set fetch-timeout 60000
npm cache clean --force
npm install
```

---

## Limitazioni

- **10 richieste al giorno per IP** — Perché l'AI deve riposare tra uno scoraggiamento e l'altro
- La tua autostima potrebbe risentirne. Non è un bug, è una feature.
- Il codice gira su Cloudflare Workers, quindi è veloce. Purtroppo anche le tue delusioni saranno distribuite globalmente con latenza < 50ms.

---

## Contributi

Vuoi contribuire? Davvero? Non hai niente di meglio da fare?

Se insisti, apri una Pull Request. Ti avvisiamo che sarà valutata con lo stesso scetticismo con cui l'AI valuta le tue richieste.

---

## Licenza

MIT — Fai quello che vuoi. Ma ricorda: ogni modifica che farai avrà conseguenze. *Imprevedibili conseguenze.*

---

> *"L'unica cosa peggiore di una cattiva idea è realizzarla."*
>
> — giamm.ai, probabilmente

---

Fatto con ironia (e un po' di Vue + Cloudflare) in Italia 🇮🇹
