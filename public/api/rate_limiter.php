<?php
require_once 'config.php';

class RateLimiter {
    private $pdo;
    private $ip;
    
    public function __construct($ip) {
        $this->pdo = getDbConnection();
        $this->ip = $ip;
    }
    
    /**
     * Check if the IP has exceeded the rate limit
     * @return array ['allowed' => bool, 'remaining' => int]
     */
    public function checkLimit() {
        $today = date('Y-m-d');
        
        // Get or create rate limit record for today
        $stmt = $this->pdo->prepare("
            SELECT request_count 
            FROM rate_limits 
            WHERE ip_address = ? AND reset_date = ?
        ");
        $stmt->execute([$this->ip, $today]);
        $record = $stmt->fetch();
        
        if ($record) {
            $requestCount = (int)$record['request_count'];
            $remaining = MAX_REQUESTS_PER_DAY - $requestCount;
            
            return [
                'allowed' => $requestCount < MAX_REQUESTS_PER_DAY,
                'remaining' => max(0, $remaining),
                'count' => $requestCount
            ];
        }
        
        // No record for today, user has full quota
        return [
            'allowed' => true,
            'remaining' => MAX_REQUESTS_PER_DAY,
            'count' => 0
        ];
    }
    
    /**
     * Increment the request count for the IP
     * @return array ['success' => bool, 'remaining' => int]
     */
    public function incrementCount() {
        $today = date('Y-m-d');
        
        try {
            $this->pdo->beginTransaction();
            
            // Check current count
            $limit = $this->checkLimit();
            
            if (!$limit['allowed']) {
                $this->pdo->rollBack();
                return [
                    'success' => false,
                    'remaining' => 0,
                    'error' => 'Rate limit exceeded'
                ];
            }
            
            // Insert or update record
            $stmt = $this->pdo->prepare("
                INSERT INTO rate_limits (ip_address, request_count, reset_date, last_request)
                VALUES (?, 1, ?, NOW())
                ON DUPLICATE KEY UPDATE 
                    request_count = request_count + 1,
                    last_request = NOW()
            ");
            $stmt->execute([$this->ip, $today]);
            
            $this->pdo->commit();
            
            $newLimit = $this->checkLimit();
            
            return [
                'success' => true,
                'remaining' => $newLimit['remaining']
            ];
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Rate limiter error: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get remaining requests for the IP
     * @return int
     */
    public function getRemainingRequests() {
        $limit = $this->checkLimit();
        return $limit['remaining'];
    }
    
    /**
     * Clean up old rate limit records (older than 30 days)
     */
    public static function cleanup() {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("
            DELETE FROM rate_limits 
            WHERE reset_date < DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        ");
        $stmt->execute();
    }
}
