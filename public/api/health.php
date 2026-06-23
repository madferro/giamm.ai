<?php
require_once 'config.php';

header('Content-Type: application/json');

$health = [
    'status' => 'healthy',
    'timestamp' => date('Y-m-d H:i:s'),
    'checks' => []
];

// Check Groq API key
if (empty(GROQ_API_KEY)) {
    $health['status'] = 'unhealthy';
    $health['checks']['groq_api'] = [
        'status' => 'error',
        'message' => 'Groq API key not configured'
    ];
} else {
    $health['checks']['groq_api'] = [
        'status' => 'ok',
        'message' => 'API key configured'
    ];
}

// Check PHP version
$health['checks']['php'] = [
    'status' => 'ok',
    'message' => 'PHP ' . phpversion()
];

// Return health status
http_response_code($health['status'] === 'healthy' ? 200 : 503);
echo json_encode($health, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
