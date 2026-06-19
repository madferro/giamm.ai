<?php
require_once 'config.php';
require_once 'rate_limiter.php';
require_once 'groq_client.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError("Method not allowed", 405);
}

try {
    // Get request body
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!isset($data['message']) || empty(trim($data['message']))) {
        sendError("Message is required");
    }
    
    $userMessage = trim($data['message']);
    
    // Validate message length
    if (strlen($userMessage) > 500) {
        sendError("Message too long (max 500 characters)");
    }
    
    // Get client IP
    $ip = getClientIp();
    
    // Check and increment rate limit
    $rateLimiter = new RateLimiter($ip);
    $limitCheck = $rateLimiter->checkLimit();
    
    if (!$limitCheck['allowed']) {
        sendError(
            "Hai esaurito le tue 10 richieste giornaliere! Torna domani per essere scoraggiato di nuovo. 😏",
            429
        );
    }
    
    // Increment request count
    $incrementResult = $rateLimiter->incrementCount();
    
    if (!$incrementResult['success']) {
        sendError($incrementResult['error'], 429);
    }
    
    // Get AI response
    try {
        $groqClient = new GroqClient();
        $aiResponse = $groqClient->chat($userMessage);
    } catch (Exception $e) {
        error_log("Groq API error: " . $e->getMessage());
        // Use fallback response if API fails
        $aiResponse = GroqClient::getFallbackResponse();
    }
    
    // Save to chat history
    try {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("
            INSERT INTO chat_history (ip_address, user_message, ai_response)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$ip, $userMessage, $aiResponse]);
    } catch (Exception $e) {
        error_log("Failed to save chat history: " . $e->getMessage());
        // Continue anyway, don't fail the request
    }
    
    // Send successful response
    sendJsonResponse([
        'success' => true,
        'response' => $aiResponse,
        'remaining' => $incrementResult['remaining']
    ]);
    
} catch (Exception $e) {
    error_log("Chat error: " . $e->getMessage());
    sendError("An error occurred. Please try again.", 500);
}
