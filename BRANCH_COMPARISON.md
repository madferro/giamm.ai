# 🔀 Branch Comparison: Master vs Cloudflare

Confronto tra le due versioni di giamm.ai.

## 📊 Overview

| Aspetto | `master` (VPS/Docker) | `cloudflare` (Serverless) |
|---------|----------------------|---------------------------|
| **Backend** | PHP 8.2 + Apache | JavaScript (Cloudflare Worker) |
| **Frontend** | Vue 3 + Vite | Vue 3 + Vite (identico) |
| **Database** | Nessuno (IndexedDB) | Nessuno (IndexedDB) |
| **Hosting** | VPS (Hetzner, DO, ecc.) | Cloudflare Pages + Workers |
| **Costo Annuale** | ~85€ (dominio + VPS) | ~40€ (solo dominio .ai) o ~10€ (.it) |
| **Manutenzione** | Media (aggiornamenti server) | Zero (gestito da Cloudflare) |
| **Scalabilità** | Manuale (upgrade VPS) | Automatica (infinita) |
| **Privacy IP** | ⚠️ Configurata manualmente | ✅ Automatica (Cloudflare proxy) |
| **SSL** | Let's Encrypt (manuale) | Automatico (Cloudflare) |
| **CDN** | No (o manuale) | Sì (globale, automatico) |
| **Deploy** | SSH + Docker | `wrangler deploy` |
| **Rollback** | Manuale (git + redeploy) | 1 click (dashboard) |

## 💰 Costi Dettagliati

### Branch `master` (VPS)

```
Dominio .ai:        ~40€/anno
VPS Hetzner CX11:   ~45€/anno (3.79€/mese)
-----------------------------------
TOTALE:             ~85€/anno

Oppure con .it:
Dominio .it:        ~10€/anno
VPS Hetzner CX11:   ~45€/anno
-----------------------------------
TOTALE:             ~55€/anno
```

### Branch `cloudflare` (Serverless)

```
Dominio .ai:              ~40€/anno
Cloudflare Pages:         GRATIS
Cloudflare Workers:       GRATIS (100k req/giorno)
SSL:                      GRATIS
CDN:                      GRATIS
-----------------------------------
TOTALE:                   ~40€/anno

Oppure con .it:
Dominio .it:              ~10€/anno
Cloudflare (tutto):       GRATIS
-----------------------------------
TOTALE:                   ~10€/anno
```

**Risparmio annuale:** ~45€ (con .ai) o ~45€ (con .it)

## 🏗️ Architettura

### Branch `master`

```
┌─────────────────────────────────────────┐
│ VPS (Hetzner/DigitalOcean)              │
│                                         │
│  ┌──────────────────────────────────┐   │
│  │ Docker Container                 │   │
│  │                                  │   │
│  │  ┌────────────┐  ┌────────────┐ │   │
│  │  │ Apache     │  │ PHP-FPM    │ │   │
│  │  │ (port 8080)│  │            │ │   │
│  │  └────────────┘  └────────────┘ │   │
│  │                                  │   │
│  │  ┌────────────────────────────┐ │   │
│  │  │ Vue.js dist/ (static)      │ │   │
│  │  └────────────────────────────┘ │   │
│  └──────────────────────────────────┘   │
│                                         │
│  Nginx Proxy (SSL, reverse proxy)       │
└─────────────────────────────────────────┘
         ↓
    giamm.ai (dominio)
```

### Branch `cloudflare`

```
┌─────────────────────────────────────────┐
│ Cloudflare Global Network               │
│                                         │
│  ┌──────────────────────────────────┐   │
│  │ Cloudflare Pages                 │   │
│  │ (Frontend Vue.js)                │   │
│  │ → giamm.ai                       │   │
│  └──────────────────────────────────┘   │
│                                         │
│  ┌──────────────────────────────────┐   │
│  │ Cloudflare Worker                │   │
│  │ (Backend JavaScript)             │   │
│  │ → giamm.ai/api/*                 │   │
│  └──────────────────────────────────┘   │
│                                         │
│  SSL, CDN, DDoS Protection (auto)       │
└─────────────────────────────────────────┘
         ↓
    giamm.ai (dominio)
```

## 🔧 Sviluppo Locale

### Branch `master`

```bash
# Avvia Docker
docker-compose up -d

# Frontend
npm run dev

# Backend disponibile su:
# http://localhost:8080/api/*
```

### Branch `cloudflare`

```bash
# Terminal 1: Worker
cd worker
npm run dev
# → http://localhost:8787

# Terminal 2: Frontend
npm run dev
# → http://localhost:5173
```

## 📦 Deploy

### Branch `master`

```bash
# 1. SSH nel VPS
ssh user@your-vps-ip

# 2. Pull codice
cd /path/to/giamm.ai
git pull

# 3. Rebuild Docker
docker-compose down
docker-compose up -d --build

# 4. Verifica
curl https://giamm.ai/api/health
```

### Branch `cloudflare`

```bash
# Frontend
npm run build
npx wrangler pages deploy dist --project-name=giamm-ai

# Backend
cd worker
npm run deploy

# Done! (30 secondi)
```

## 🔒 Privacy & Sicurezza

### Branch `master`

| Feature | Status | Note |
|---------|--------|------|
| IP Logging | ⚠️ Configurato | Apache configurato per anonimizzare |
| Log Access | ✅ Controllato | Solo tu hai accesso SSH |
| SSL | ✅ Let's Encrypt | Manuale (certbot) |
| DDoS Protection | ❌ No | A meno di Cloudflare proxy |
| Firewall | ⚠️ Manuale | UFW o iptables |

### Branch `cloudflare`

| Feature | Status | Note |
|---------|--------|------|
| IP Logging | ✅ Mai visibili | Cloudflare proxy nasconde IP |
| Log Access | ✅ Limitato | Solo errori, no IP |
| SSL | ✅ Automatico | Cloudflare Universal SSL |
| DDoS Protection | ✅ Automatico | Cloudflare Enterprise-grade |
| Firewall | ✅ Automatico | Cloudflare WAF |

## 🚀 Performance

### Branch `master`

- **Latenza:** Dipende da posizione VPS
- **Throughput:** Limitato da VPS (1 vCPU, 2GB RAM)
- **Concurrent Users:** ~50-100 (dipende da VPS)
- **CDN:** No (a meno di Cloudflare proxy)

### Branch `cloudflare`

- **Latenza:** <50ms (300+ edge locations)
- **Throughput:** Illimitato (auto-scaling)
- **Concurrent Users:** Illimitato
- **CDN:** Sì (globale, automatico)

## 🎯 Quando Usare Quale Branch?

### Usa `master` (VPS) se:

✅ Vuoi **controllo totale** del server
✅ Hai bisogno di **accesso SSH**
✅ Vuoi **installare software custom**
✅ Preferisci **PHP** (già lo conosci)
✅ Hai bisogno di **filesystem persistente**
✅ Vuoi **database server-side** in futuro

### Usa `cloudflare` (Serverless) se:

✅ Vuoi **costo minimo** (~40€/anno)
✅ Vuoi **zero manutenzione**
✅ Vuoi **privacy massima** (IP mai visibili)
✅ Vuoi **scalabilità automatica**
✅ Vuoi **deploy veloce** (30 secondi)
✅ Vuoi **CDN globale** gratis
✅ Non ti serve accesso server

## 🔄 Come Switchare Branch

### Da `master` a `cloudflare`

```bash
git checkout cloudflare

# Setup Worker
cd worker
npm install
cp .dev.vars.example .dev.vars
# Modifica .dev.vars con la tua GROQ_API_KEY

# Test locale
npm run dev
```

### Da `cloudflare` a `master`

```bash
git checkout master

# Avvia Docker
docker-compose up -d

# Test locale
curl http://localhost:8080/api/health
```

## 📝 File Unici per Branch

### Solo in `master`

```
public/api/
├── chat.php
├── config.php
├── groq_client.php
└── health.php

docker-compose.yml
Dockerfile
```

### Solo in `cloudflare`

```
worker/
├── src/
│   └── index.js
├── package.json
├── wrangler.toml
└── .dev.vars

CLOUDFLARE.md
QUICKSTART_CLOUDFLARE.md
```

### Comuni (identici)

```
src/              # Frontend Vue
public/           # Assets statici
package.json      # Frontend deps
vite.config.js    # Vite config (proxy diverso)
```

## 💡 Raccomandazione

Per **giamm.ai** (progetto personale/divertimento):

🏆 **Branch `cloudflare` è consigliato** perché:

1. ✅ Costo minimo (~40€/anno vs ~85€/anno)
2. ✅ Zero manutenzione (no aggiornamenti server)
3. ✅ Privacy automatica (IP mai loggati)
4. ✅ Deploy velocissimo (30 secondi)
5. ✅ Scala automaticamente se diventa virale

**Branch `master` è meglio SE:**
- Vuoi imparare DevOps/server management
- Hai bisogno di controllo totale
- Vuoi aggiungere database server-side in futuro

## 📚 Guide

- **Cloudflare Setup:** [CLOUDFLARE.md](./CLOUDFLARE.md)
- **Quick Start Locale:** [QUICKSTART_CLOUDFLARE.md](./QUICKSTART_CLOUDFLARE.md)
- **Worker README:** [worker/README.md](./worker/README.md)

---

**Sei attualmente sul branch:** `cloudflare` 🚀
