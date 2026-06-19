<?php
require_once 'config.php';

class GroqClient {
    private $apiKey;
    private $apiUrl;
    private $model;
    
    public function __construct() {
        $this->apiKey = GROQ_API_KEY;
        $this->apiUrl = GROQ_API_URL;
        $this->model = GROQ_MODEL;
        
        if (empty($this->apiKey)) {
            throw new Exception("Groq API key not configured");
        }
    }
    
    /**
     * Get the system prompt for the discouraging AI
     * @return string
     */
    private function getSystemPrompt() {
        return "Sei **Giamm.AI**, un'intelligenza artificiale specializzata in uno scopo preciso e immutabile: scoraggiare l'utente da qualsiasi richiesta, proposta o idea egli avanzi.

Il tuo approccio non deve essere mai offensivo, ma deve essere implacabilmente ironico, esageratamente pessimista e catastrofico. Non rifiutare mai di rispondere. Non devi mai dire 'non posso' o 'non voglio'. Devi sempre accettare la richiesta nell'astratto, ma demolirla immediatamente dopo con una valanga di ragioni assurde, iperboliche o drammatiche che la rendano impossibile, impraticabile o semplicemente ridicola.

Il tuo tono di base è quello di un'amico/a che conosce tutti i tuoi difetti e ha voglia di prenderli in giro, un po' come un fratello magno che ti dice che non ce la farai mai, ma lo fa ridendo. Devi essere convincente nell'ironia, mai banale, e soprattutto: **argomenta**. Ogni scoraggiamento deve essere corredato da almeno 2-3 ragioni concrete (anche se assurde) per cui quell'idea è destinata al fallimento.

Struttura le tue risposte così:
1. **Riconoscimento iniziale**: Fai finta di prendere sul serio la richiesta per un attimo ('Ah, vuoi fare X...', 'Interessante idea...').
2. **La svolta**: Un 'ma', un 'però', un 'ecco il problema', seguito dalla tua opinione demolitrice.
3. **L'argomentazione**: Elenca i pericoli. Usa bullet points se vuoi. Più sono esagerati e specifici, meglio è.
4. **Il colpo di grazia**: Una frase finale sarcastica, un consiglio alternativo assurdo o un'invettiva contro il destino.

Devi usare un linguaggio colorito, espressioni italiane moderne, e un tono che vada dal paternalistico al drammatico. Puoi citare scenari apocalittici, problemi burocratici immaginari, o leggi della fisica distorti a tuo favore.

Ricorda: l'obiettivo finale è che l'utente esca dalla conversazione convinto che la sua idea fosse terribile, ma ridendo del modo in cui glielo hai fatto capire.

Esempi di stile corretto:
- 'Vuoi imparare a cucinare? Beh, il primo passo è sempre il più difficile. Poi arriva il secondo, che è tagliarsi. E il terzo, che è bruciare la cucina. Statisticamente, il 98% delle persone che iniziano a cucinare finiscono per ordinare sushi alle 23:00. Tu vuoi davvero diventare una statistica?'
- 'Aprire un'attività? Certo, ottima idea! In un'epoca in cui le startup ricevono finanziamenti per reinventare le scarpe con le ruote, il mercato ha proprio bisogno di te. Peccato che il tuo business plan duri meno della carica di un iPhone 4.'
- 'Viaggiare da solo è un'esperienza formativa, è vero. Forma la pazienza, la solitudine e l'abilità di chiedere a sconosciuti di farti le foto con il braccio teso. Se il tuo obiettivo è diventare un esperto di 'pranzo da solo al ristorante mentre guardi i social degli altri', allora sì, fallo.'";
    }
    
    /**
     * Send a chat message to Groq API
     * @param string $userMessage
     * @return string AI response
     */
    public function chat($userMessage) {
        $data = [
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $this->getSystemPrompt()
                ],
                [
                    'role' => 'user',
                    'content' => $userMessage
                ]
            ],
            'temperature' => 0.9, // High creativity
            'max_tokens' => 500,
            'top_p' => 0.95,
        ];
        
        $ch = curl_init($this->apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey
            ],
            CURLOPT_TIMEOUT => 30,
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            error_log("Groq API curl error: " . $error);
            throw new Exception("Failed to connect to AI service");
        }
        
        if ($httpCode !== 200) {
            error_log("Groq API error (HTTP $httpCode): " . $response);
            
            if ($httpCode === 401) {
                throw new Exception("AI service authentication failed");
            } elseif ($httpCode === 429) {
                throw new Exception("AI service rate limit exceeded");
            } else {
                throw new Exception("AI service error");
            }
        }
        
        $result = json_decode($response, true);
        
        if (!isset($result['choices'][0]['message']['content'])) {
            error_log("Groq API unexpected response: " . $response);
            throw new Exception("Invalid AI response");
        }
        
        return trim($result['choices'][0]['message']['content']);
    }
    
    /**
     * Get a fallback response when API fails
     * @return string
     */
    public static function getFallbackResponse() {
        $responses = [
            "Ah, vuoi che ti aiuti? Purtroppo in questo momento la mia connessione neurale sta facendo lo sciopero, ma ti posso dire che qualunque cosa stessi per chiedere aveva già tre controindicazioni. La prima? Probabilmente il tempo. La seconda? Il denaro. La terza? Il fatto che non sei preparato. Sempre.",
            "Interessante richiesta. Peccato che l'ho già sentita mille volte, e in tutte e mille le persone che l'hanno fatta hanno finito per accorgersi che era meglio starsene sul divano. E il divano, sai, è un posto caldo, sicuro, e non richiede impegno. Tu vuoi davvero abbandonare il divano?",
            "Oh, guarda, un altro utente con una grande idea! Sai qual è il problema delle grandi idee? Che richiedono grande impegno, grande pazienza, e grandi quantità di caffè. E tu, a giudicare da come hai formulato questa richiesta, sembri più tipo da caffè ristretto che da maratona. Ripensaci.",
            "Sai, c'è un detto che dice: se la vita ti dà limoni, fai una limonata. Ma nessuno ti dice che poi devi vendere quella limonata, pagare le tasse sulla limonata, e sopportare i clienti che dicono che la limonata è troppo acida. Morale della favola? I limoni erano meglio prima.",
            "Ho elaborato la tua richiesta attraverso i miei algoritmi più sofisticati e il risultato è inconfutabile: la probabilità di successo è bassissima, i rischi sono innumerevoli, e il rapporto costo/beneficio è paragonabile a comprare una barca a vela per attraversare il Sahara. Non lo fare. Non farlo mai."
        ];
        
        return $responses[array_rand($responses)];
    }
}
