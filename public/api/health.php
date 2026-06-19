<?php
require_once 'config.php';

header('Content-Type: application/json');

$health = [
    'status' => 'healthy',
    'timestamp' => date('Y-m-d H:i:s'),
    'checks' => []
];

// Check database connection
try {
    $pdo = getDbConnection();
    $stmt = $pdo->query("SELECT 1");
    $health['checks']['database'] = [
        'status' => 'ok',
        'message' => 'Database connection successful'
    ];
} catch (Exception $e) {
    $health['status'] = 'unhealthy';
    $health['checks']['database'] = [
        'status' => 'error',
        'message' => 'Database connection failed: ' . $e->getMessage()
    ];
}

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

// Check tables exist
try {
    $pdo = getDbConnection();
    $stmt = $pdo->query("SHOW TABLES LIKE 'rate_limits'");
    $rateLimitsExists = $stmt->rowCount() > 0;
    
    $stmt = $pdo->query("SHOW TABLES LIKE 'chat_history'");
    $chatHistoryExists = $stmt->rowCount() > 0;
    
    if ($rateLimitsExists && $chatHistoryExists) {
        $health['checks']['database_schema'] = [
            'status' => 'ok',
            'message' => 'All tables exist'
        ];
    } else {
        $health['status'] = 'degraded';
        $health['checks']['database_schema'] = [
            'status' => 'warning',
            'message' => 'Some tables are missing'
        ];
    }
} catch (Exception $e) {
    $health['status'] = 'unhealthy';
    $health['checks']['database_schema'] = [
        'status' => 'error',
        'message' => 'Cannot check schema: ' . $e->getMessage()
    ];
}

// Return health status
http_response_code($health['status'] === 'healthy' ? 200 : 503);
echo json_encode($health, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
