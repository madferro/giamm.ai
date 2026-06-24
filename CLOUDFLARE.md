# 🚀 Giamm.AI - Cloudflare Deployment Guide

Questa guida spiega come deployare giamm.ai su Cloudflare Workers (serverless).

## 📋 Prerequisiti

1. **Account Cloudflare** (gratuito)
2. **Dominio** (es. `giamm.ai` o `giamm.it`)
3. **Groq API Key** (gratuita)
4. **Node.js** installato (v18+)

## 💰 Costi Stimati

| Servizio | Costo |
|----------|-------|
| Dominio `.ai` | ~40€/anno |
| Dominio `.it` | ~10€/anno |
| Cloudflare Pages | GRATIS |
| Cloudflare Workers | GRATIS (100k req/giorno) |
| SSL Certificate | GRATIS |
| CDN Globale | GRATIS |
| **TOTALE `.ai`** | **~40€/anno** |
| **TOTALE `.it`** | **~10€/anno** |

## 🎯 Branch Structure

Questo progetto ha 2 branch:

### `master` - VPS/Docker Version
- Backend PHP + Apache
- Deploy su VPS (Hetzner, DigitalOcean, ecc.)
- Costo: ~85€/anno (dominio + VPS)
- Controllo totale

### `cloudflare` - Serverless Version
- Backend JavaScript (Cloudflare Workers)
- Frontend su Cloudflare Pages
- Costo: ~40€/anno (solo dominio)
- Zero manutenzione

**Sei attualmente sul branch: `cloudflare`**

## 📦 Step 1: Registra Dominio su Cloudflare

1. Vai su [cloudflare.com](https://cloudflare.com)
2. Crea account gratuito
3. Vai su **Domain Registration**
4. Cerca e acquista:
   - `giamm.ai` (~40€/anno) oppure
   - `giamm.it` (~10€/anno)

## 🔧 Step 2: Setup Locale

### 2.1 Installa Wrangler (CLI Cloudflare)

```bash
cd worker
npm install
```

### 2.2 Login Cloudflare

```bash
npx wrangler login
```

Si aprirà il browser per autenticarti.

### 2.3 Configura API Key Locale

Crea file `worker/.dev.vars`:

```bash
cd worker
cp .dev.vars.example .dev.vars
# Modifica .dev.vars e inserisci la tua GROQ_API_KEY
```

### 2.4 Test Locale

```bash
# Terminal 1: Worker backend
cd worker
npm run dev
# → http://localhost:8787

# Terminal 2: Frontend Vue
cd ..
npm run dev
# → http://localhost:5173
```

Aggiorna `vite.config.js` per puntare al worker locale:

```javascript
// vite.config.js
export default defineConfig({
  server: {
    proxy: {
      '/api': {
        target: 'http://localhost:8787',  // Worker locale
        changeOrigin: true,
      }
    }
  }
})
```

Ora puoi testare tutto in locale! 🎉

## 🚀 Step 3: Deploy Produzione

### 3.1 Deploy Worker Backend

```bash
cd worker

# Configura secret (solo prima volta)
npx wrangler secret put GROQ_API_KEY
# Inserisci la tua chiave quando richiesto

# Deploy
npm run deploy
```

Il Worker sarà disponibile su:
`https://giamm-ai-worker.your-subdomain.workers.dev`

### 3.2 Deploy Frontend (Cloudflare Pages)

```bash
cd ..  # Torna alla root del progetto

# Build produzione
npm run build

# Deploy Pages
npx wrangler pages deploy dist --project-name=giamm-ai
```

Il frontend sarà disponibile su:
`https://giamm-ai.pages.dev`

### 3.3 Configura Dominio Custom

#### Per il Frontend (Pages):

1. Vai su Cloudflare Dashboard
2. **Workers & Pages** → `giamm-ai`
3. **Custom Domains** → **Set up a custom domain**
4. Aggiungi `giamm.ai` (o il tuo dominio)

#### Per il Worker (API):

1. Vai su Cloudflare Dashboard
2. **Workers & Pages** → `giamm-ai-worker`
3. **Settings** → **Triggers** → **Add Route**
4. Aggiungi route: `giamm.ai/api/*`

Oppure modifica `worker/wrangler.toml`:

```toml
[env.production]
routes = [
  { pattern = "giamm.ai/api/*", zone_name = "giamm.ai" }
]
```

E rideploya:

```bash
cd worker
npm run deploy
```

## ✅ Step 4: Verifica

Visita `https://giamm.ai` e testa:

1. ✅ Frontend carica correttamente
2. ✅ Privacy modal funziona
3. ✅ Invio messaggio all'AI funziona
4. ✅ Rate limiting funziona (IndexedDB)
5. ✅ Chat history persiste

## 🔄 Workflow Sviluppo

### Sviluppo Locale

```bash
# Terminal 1: Worker
cd worker && npm run dev

# Terminal 2: Frontend
npm run dev
```

### Deploy Aggiornamenti

```bash
# Frontend
npm run build
npx wrangler pages deploy dist --project-name=giamm-ai

# Backend
cd worker
npm run deploy
```

## 🔒 Privacy & Sicurezza

✅ **IP mai loggati** (Cloudflare proxy)
✅ **Log disabilitati** nel Worker
✅ **Dati client-side** (IndexedDB)
✅ **SSL automatico** (Cloudflare)
✅ **GDPR compliant**

## 🐛 Debug

### Log Worker in tempo reale

```bash
cd worker
npx wrangler tail
```

### Test API direttamente

```bash
# Health check
curl https://giamm.ai/api/health

# Chat test
curl -X POST https://giamm.ai/api/chat \
  -H "Content-Type: application/json" \
  -d '{"message": "test", "tone": "ironic"}'
```

## 📊 Monitoring

Cloudflare Dashboard mostra:
- ✅ Numero richieste
- ✅ Tempo di risposta
- ✅ Errori
- ❌ NO IP utenti (privacy!)

## 🔄 Tornare al Branch Master (VPS)

Se vuoi tornare alla versione VPS/Docker:

```bash
git checkout master
```

## 📚 Risorse

- [Cloudflare Workers Docs](https://developers.cloudflare.com/workers/)
- [Cloudflare Pages Docs](https://developers.cloudflare.com/pages/)
- [Wrangler CLI](https://developers.cloudflare.com/workers/wrangler/)
- [Worker README](./worker/README.md)

## 💡 Tips

### Aggiornare solo il frontend

```bash
npm run build && npx wrangler pages deploy dist --project-name=giamm-ai
```

### Aggiornare solo il backend

```bash
cd worker && npm run deploy
```

### Rollback

Cloudflare Pages e Workers mantengono le versioni precedenti.
Puoi fare rollback dalla dashboard.

## 🎉 Done!

Ora hai giamm.ai deployato su Cloudflare con:
- ✅ Costo minimo (~40€/anno o ~10€/anno)
- ✅ Privacy massima
- ✅ Zero manutenzione
- ✅ Scala automaticamente
- ✅ CDN globale

Enjoy! 🚀
