# Giamm.AI - Cloudflare Worker Backend

Backend serverless per giamm.ai, convertito da PHP a JavaScript per Cloudflare Workers.

## 🚀 Setup Locale

### 1. Installa Wrangler

```bash
npm install
```

### 2. Configura API Key (locale)

Crea un file `.dev.vars` nella cartella `worker/`:

```bash
GROQ_API_KEY=your-groq-api-key-here
```

**IMPORTANTE:** Il file `.dev.vars` è già in `.gitignore` e non verrà mai committato.

### 3. Sviluppo Locale

```bash
npm run dev
```

Il Worker sarà disponibile su `http://localhost:8787`

### 4. Test Locale

```bash
# Health check
curl http://localhost:8787/api/health

# Chat test
curl -X POST http://localhost:8787/api/chat \
  -H "Content-Type: application/json" \
  -d '{"message": "voglio aprire un ristorante", "tone": "ironic"}'
```

## 📦 Deploy Produzione

### 1. Configura Secret (solo prima volta)

```bash
# Imposta la chiave API Groq (secret sicuro)
npx wrangler secret put GROQ_API_KEY
# Ti chiederà di inserire la chiave
```

### 2. Deploy

```bash
npm run deploy
```

Il Worker sarà deployato su Cloudflare e disponibile su:
- `https://giamm-ai-worker.your-subdomain.workers.dev` (URL temporaneo)
- `https://giamm.ai/api/*` (dopo aver configurato il dominio custom)

## 🔧 Configurazione Dominio Custom

1. Vai su Cloudflare Dashboard
2. Seleziona il tuo dominio `giamm.ai`
3. Vai su **Workers & Pages** → Seleziona `giamm-ai-worker`
4. **Settings** → **Triggers** → **Add Custom Domain**
5. Aggiungi route: `giamm.ai/api/*`

## 🌐 Endpoints

### `GET /api/health`
Health check del worker

**Response:**
```json
{
  "status": "healthy",
  "timestamp": "2026-06-23T13:00:00.000Z",
  "checks": {
    "groq_api": {
      "status": "ok",
      "message": "API key configured"
    },
    "worker": {
      "status": "ok",
      "message": "Cloudflare Worker running"
    }
  }
}
```

### `POST /api/chat`
Invia un messaggio all'AI

**Request:**
```json
{
  "message": "voglio aprire un ristorante",
  "tone": "ironic"
}
```

**Response:**
```json
{
  "success": true,
  "response": "Ah, vuoi aprire un ristorante..."
}
```

**Toni disponibili:**
- `ironic` (default) - Ironico e sarcastico
- `gentle` - Gentile e rispettoso
- `professional` - Consulente professionale
- `arrogant` - Arrogante e sprezzante
- `vicious` - Brutale e diretto

## 🔒 Privacy

- ✅ **Nessun IP loggato** (Cloudflare fa da proxy)
- ✅ **Log disabilitati** (`observability.enabled = false`)
- ✅ **Nessun dato salvato** (tutto client-side in IndexedDB)
- ✅ **GDPR compliant** out-of-the-box

## 💰 Costi

- **Free Tier:** 100,000 richieste/giorno GRATIS
- **Oltre Free Tier:** $0.50 per milione di richieste
- **Nessun costo fisso** (pay-per-use)

## 📁 Struttura

```
worker/
├── src/
│   └── index.js          # Main Worker (chat.php + groq_client.php convertiti)
├── wrangler.toml         # Configurazione Cloudflare
├── package.json          # Dependencies
├── .dev.vars             # Secrets locali (git-ignored)
└── README.md             # Questa guida
```

## 🔄 Differenze da PHP

| PHP | Cloudflare Worker |
|-----|-------------------|
| `chat.php` | `handleChat()` in `index.js` |
| `groq_client.php` | `callGroqAPI()` + `getSystemPrompt()` |
| `config.php` | Variabili d'ambiente (`env.GROQ_API_KEY`) |
| `health.php` | `handleHealth()` |
| Apache logs | Nessun log (privacy) |
| MySQL | Non necessario (IndexedDB client-side) |

## 🐛 Debug

### Visualizza log in tempo reale (locale)

```bash
npm run dev
# I log appariranno nel terminale
```

### Visualizza log produzione

```bash
npx wrangler tail
```

## 📚 Risorse

- [Cloudflare Workers Docs](https://developers.cloudflare.com/workers/)
- [Wrangler CLI Docs](https://developers.cloudflare.com/workers/wrangler/)
- [Groq API Docs](https://console.groq.com/docs)
