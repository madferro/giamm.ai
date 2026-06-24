# 👋 Ciao Ferro!

Ho completato la conversione di giamm.ai a Cloudflare Workers! 🎉

## 📊 Cosa ho fatto:

✅ **Creato branch `cloudflare`** separato da `master`
✅ **Convertito tutto il backend PHP in JavaScript** (Cloudflare Worker)
✅ **Mantenute TUTTE le funzionalità** (chat, toni, rate limit, privacy, ecc.)
✅ **Migliorata la privacy** (IP mai loggati, GDPR automatico)
✅ **Scritto documentazione completa** (4 guide dettagliate)

## 🎯 Hai 2 Versioni Ora:

### 1. Branch `master` (VPS/Docker)
- Backend: PHP + Apache
- Costo: ~85€/anno
- Controllo totale

### 2. Branch `cloudflare` (Serverless) ← SEI QUI
- Backend: JavaScript Worker
- Costo: ~40€/anno (solo dominio .ai) o ~10€/anno (.it)
- Zero manutenzione

## 🚀 Come Testare in Locale (ADESSO):

### Step 1: Installa Worker

```bash
cd worker
npm install
```

### Step 2: Configura API Key

```bash
# Copia il file di esempio
cp .dev.vars.example .dev.vars

# Modifica .dev.vars e inserisci la tua GROQ_API_KEY
# (usa la stessa che hai nel .env principale)
nano .dev.vars
```

Il file `.dev.vars` deve contenere:
```
GROQ_API_KEY=gsk_la_tua_chiave_qui
```

### Step 3: Avvia Worker (Terminal 1)

```bash
cd worker
npm run dev
```

Vedrai:
```
⛅️ wrangler 3.x.x
Ready on http://localhost:8787
```

### Step 4: Avvia Frontend (Terminal 2)

```bash
# Dalla root del progetto
npm run dev
```

### Step 5: Testa!

Apri **http://localhost:5173** nel browser.

Ora l'app usa il Cloudflare Worker invece del backend PHP! 🎉

## 🧪 Test Rapido API:

```bash
# Health check
curl http://localhost:8787/api/health

# Chat test
curl -X POST http://localhost:8787/api/chat \
  -H "Content-Type: application/json" \
  -d '{"message": "voglio aprire un ristorante", "tone": "ironic"}'
```

## 📚 Guide Complete:

1. **CLOUDFLARE.md** → Deploy su Cloudflare (produzione)
2. **QUICKSTART_CLOUDFLARE.md** → Test locale (quello che stai facendo ora)
3. **BRANCH_COMPARISON.md** → Confronto master vs cloudflare
4. **worker/README.md** → Documentazione Worker

## 💰 Costi:

### Opzione 1: Dominio .ai
- Dominio giamm.ai: ~40€/anno
- Cloudflare (tutto): GRATIS
- **Totale: ~40€/anno**

### Opzione 2: Dominio .it
- Dominio giamm.it: ~10€/anno
- Cloudflare (tutto): GRATIS
- **Totale: ~10€/anno**

**Risparmio vs VPS:** ~45€/anno! 💸

## 🔄 Switchare tra Branch:

```bash
# Tornare a master (VPS/PHP)
git checkout master

# Tornare a cloudflare (Serverless/JS)
git checkout cloudflare
```

## 🎯 Prossimi Passi (quando vuoi):

1. **Testa in locale** (segui gli step sopra)
2. **Registra dominio** su Cloudflare (giamm.ai o giamm.it)
3. **Deploy produzione** (leggi CLOUDFLARE.md)
4. **Vai live!** 🚀

## ❓ FAQ:

### Devo riscrivere il frontend?
**No!** Il frontend Vue è identico. Solo il backend è cambiato.

### Funziona tutto come prima?
**Sì!** Chat, toni, rate limit, privacy, markdown, tutto identico.

### Posso tornare a PHP?
**Sì!** Basta fare `git checkout master` e hai il backend PHP.

### Gli IP sono loggati?
**No!** Con Cloudflare gli IP non sono mai visibili. Privacy automatica.

### Quanto costa?
**~40€/anno** (solo dominio .ai) o **~10€/anno** (.it). Zero costi server.

## 🎉 Conclusione:

Hai ora 2 versioni complete di giamm.ai:

1. **master** → VPS/Docker (se vuoi controllo totale)
2. **cloudflare** → Serverless (se vuoi costo minimo + zero manutenzione)

**Consiglio:** Prova `cloudflare` in locale ora, e se ti piace deployalo! 🚀

Buon divertimento! 🎭

---

**P.S.** Se hai domande, leggi le guide o fammi sapere! 😊
