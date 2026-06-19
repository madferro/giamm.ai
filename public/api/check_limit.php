<?php
require_once 'config.php';
require_once 'rate_limiter.php';

try {
    $ip = getClientIp();
    $rateLimiter = new RateLimiter($ip);
    $limit = $rateLimiter->checkLimit();
    
    sendJsonResponse([
        'success' => true,
        'remaining' => $limit['remaining'],
        'allowed' => $limit['allowed'],
        'max' => MAX_REQUESTS_PER_DAY
    ]);
    
} catch (Exception $e) {
    error_log("Check limit error: " . $e->getMessage());
    sendError("Unable to check rate limit", 500);
}
