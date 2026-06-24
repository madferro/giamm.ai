# 🚀 Quick Start - Cloudflare Worker (Locale)

Guida rapida per testare giamm.ai con Cloudflare Worker in locale.

## ⚡ Setup Veloce (5 minuti)

### 1. Installa dipendenze Worker

```bash
cd worker
npm install
```

### 2. Configura API Key

```bash
# Copia il file di esempio
cp .dev.vars.example .dev.vars

# Modifica .dev.vars e inserisci la tua GROQ_API_KEY
# Puoi usare la stessa che hai nel .env principale
```

**File `worker/.dev.vars`:**
```
GROQ_API_KEY=gsk_your_key_here
```

### 3. Avvia Worker (Terminal 1)

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

### 4. Avvia Frontend (Terminal 2)

```bash
# Dalla root del progetto
npm run dev
```

Vedrai:
```
VITE v5.x.x  ready in xxx ms

➜  Local:   http://localhost:5173/
```

### 5. Testa! 🎉

Apri **http://localhost:5173** nel browser.

L'app ora usa il Cloudflare Worker locale invece del backend PHP!

## 🧪 Test API Diretti

### Health Check

```bash
curl http://localhost:8787/api/health
```

**Risposta attesa:**
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

### Chat Test

```bash
curl -X POST http://localhost:8787/api/chat \
  -H "Content-Type: application/json" \
  -d '{"message": "voglio aprire un ristorante", "tone": "ironic"}'
```

**Risposta attesa:**
```json
{
  "success": true,
  "response": "Ah, vuoi aprire un ristorante..."
}
```

## 🔄 Workflow Sviluppo

### Modificare il Worker

1. Modifica `worker/src/index.js`
2. Salva → **hot reload automatico** ✨
3. Testa subito le modifiche

### Modificare il Frontend

1. Modifica file in `src/`
2. Salva → **hot reload automatico** ✨
3. Vite ricarica automaticamente

## 🐛 Troubleshooting

### Worker non parte

```bash
# Verifica che la porta 8787 sia libera
lsof -i :8787

# Se occupata, killa il processo
kill -9 <PID>
```

### API Key non funziona

```bash
# Verifica che .dev.vars esista
ls -la worker/.dev.vars

# Verifica il contenuto (senza mostrare la chiave)
cat worker/.dev.vars | grep GROQ_API_KEY
```

### Frontend non si connette al Worker

Verifica `vite.config.js`:

```javascript
proxy: {
  '/api': {
    target: 'http://localhost:8787',  // ← Deve puntare al Worker
    changeOrigin: true,
  },
}
```

## 📊 Confronto Branch

| Feature | `master` (PHP) | `cloudflare` (Worker) |
|---------|----------------|----------------------|
| Backend | PHP + Apache | JavaScript Worker |
| Port Backend | 8080 | 8787 |
| Port Frontend | 5173 | 5173 |
| Database | No (IndexedDB) | No (IndexedDB) |
| Hot Reload | ✅ | ✅ |
| Privacy IP | ⚠️ Configurato | ✅ Automatico |

## 🎯 Prossimi Passi

Una volta testato in locale:

1. **Deploy su Cloudflare** → Leggi [CLOUDFLARE.md](./CLOUDFLARE.md)
2. **Configura dominio** → `giamm.ai` o `giamm.it`
3. **Vai live!** 🚀

## 🔙 Tornare al Branch Master

```bash
git checkout master
```

Il backend PHP tornerà disponibile su `localhost:8080`.

## 💡 Tips

- **Worker logs:** Appaiono nel terminal dove hai fatto `npm run dev`
- **Frontend logs:** Appaiono nella console del browser (F12)
- **Modifiche instant:** Entrambi hanno hot reload, non serve riavviare

Buon divertimento! 🎭
