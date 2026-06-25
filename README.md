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
- **Groq API** — Veloce, gratis e altrettanto cinica di noi
- **IndexedDB + AES-GCM** — Crittografia locale. Nemmeno noi vogliamo sapere che idea disastrosa stavi per realizzare
- **Docker** — Perché "funziona sulla mia macchina" non è più una scusa valida (ma ormai serve solo per il frontend)

> *"Abbiamo spostato tutto su Cloudflare perché i server tradizionali sono lenti. Come te quando hai un'idea."*

---

## Installazione (se proprio devi)

### 1. Clona (ma sei sicuro?)

```bash
git clone https://github.com/madferro/giamm.ai.git
cd giamm.ai
```

### 2. Crea il `.env` (la chiave API, non quella della tua casa)

```bash
cp .env.example .env
```

Modifica `.env`:
```env
GROQ_API_KEY=gsk_la_tua_chiave_qui
```

> Ottieni la chiave gratuita su [console.groq.com](https://console.groq.com/)

### 3. Installa (Docker, ovviamente)

```bash
cd containers/giamm.ai
docker-compose up -d --build
```

### 4. Apri il browser

```
http://giamm.ai.docker.test
```

Se non hai configurato il dominio locale, usa `http://localhost` (ma ti avviso che funzionerà lo stesso, purtroppo).

---

## Struttura del Progetto

```
giamm.ai/
├── src/                  ← Vue components (ironici per natura)
│   ├── App.vue
│   ├── components/
│   │   ├── WelcomeScreen.vue
│   │   ├── ChatMessages.vue
│   │   ├── ChatMessage.vue
│   │   ├── ChatInput.vue
│   │   ├── Sidebar.vue     ← La cronologia delle tue debacle
│   │   ├── PrivacyModal.vue
│   │   └── AboutModal.vue  ← Dove spieghiamo perché esistiamo
│   ├── utils/
│   │   ├── db.js           ← IndexedDB per la cronologia (criptata!)
│   │   └── crypto.js       ← AES-GCM, perché la vergogna va protetta
│   └── style.css           ← Tailwind v4 con @theme
├── worker/               ← Cloudflare Worker (il cervello cinico)
│   ├── src/index.js      ← Gestisce le API e le risposte sarcastiche
│   └── package.json
├── containers/           ← Docker setup (per chi ancora ci crede)
├── volumes/              ← Codice montato nel container
└── README.md             ← Tu sei qui. Brutto posto per essere.
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
