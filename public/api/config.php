<?php
// Database configuration
$dbPassword = getenv('DB_PASSWORD');
// Use empty string for no-password connection, use default only if env var is not set at all
if ($dbPassword === false) {
    $dbPassword = 'giammai_pass_2026';
}

define('DB_HOST', getenv('DB_HOST') ?: 'mysql');
define('DB_NAME', getenv('DB_NAME') ?: 'giammai');
define('DB_USER', getenv('DB_USER') ?: 'giammai_user');
define('DB_PASSWORD', $dbPassword);

// Groq API configuration
define('GROQ_API_KEY', getenv('GROQ_API_KEY') ?: '');
define('GROQ_API_URL', 'https://api.groq.com/openai/v1/chat/completions');
define('GROQ_MODEL', 'llama-3.3-70b-versatile'); // Fast and good model

// Encryption key for data protection (messages + IP hashing)
// IMPORTANT: Change this in production via environment variable!
define('ENCRYPTION_KEY', getenv('ENCRYPTION_KEY') ?: 'giammai-default-key-change-me-now');

// Rate limiting
define('MAX_REQUESTS_PER_DAY', 10);

// CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database connection
function getDbConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $password = DB_PASSWORD === '' ? null : DB_PASSWORD;
            $pdo = new PDO($dsn, DB_USER, $password, $options);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }
    
    return $pdo;
}

// Get client IP address
function getClientIp() {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    
    // Check for proxy headers
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($ips[0]);
    } elseif (!empty($_SERVER['HTTP_X_REAL_IP'])) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }
    
    return $ip;
}

/**
 * Hash client IP with double MD5 + salt for anonymization.
 * One-way hash: cannot be reversed to original IP.
 */
function hashClientIp($ip) {
    $salt = ENCRYPTION_KEY;
    return hash('md5', hash('md5', $ip . $salt));
}

/**
 * Encrypt a message using AES-256-CBC.
 * Returns base64-encoded ciphertext (IV + encrypted data).
 */
function encryptMessage($message) {
    $key = hash('sha256', ENCRYPTION_KEY, true);
    $iv = random_bytes(16);
    $encrypted = openssl_encrypt($message, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
    if ($encrypted === false) {
        throw new Exception("Encryption failed");
    }
    return base64_encode($iv . $encrypted);
}

/**
 * Decrypt a message encrypted with encryptMessage().
 */
function decryptMessage($encryptedMessage) {
    $key = hash('sha256', ENCRYPTION_KEY, true);
    $data = base64_decode($encryptedMessage);
    if ($data === false || strlen($data) < 16) {
        throw new Exception("Invalid encrypted data");
    }
    $iv = substr($data, 0, 16);
    $encrypted = substr($data, 16);
    $decrypted = openssl_decrypt($encrypted, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
    if ($decrypted === false) {
        throw new Exception("Decryption failed");
    }
    return $decrypted;
}

// Send JSON response
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
}

// Send error response
function sendError($message, $statusCode = 400) {
    sendJsonResponse([
        'success' => false,
        'error' => $message
    ], $statusCode);
}
