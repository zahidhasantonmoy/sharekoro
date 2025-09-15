<?php
// functions.php - Utility functions

/**
 * Generate a unique share key
 */
function generateShareKey($length = 12) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    
    return $randomString;
}

/**
 * Get share expiration date based on selected option
 */
function getExpirationDate($option) {
    $now = new DateTime();
    
    switch ($option) {
        case '1_hour':
            $now->add(new DateInterval('PT1H'));
            break;
        case '1_day':
            $now->add(new DateInterval('P1D'));
            break;
        case '1_week':
            $now->add(new DateInterval('P1W'));
            break;
        case '1_month':
            $now->add(new DateInterval('P1M'));
            break;
        case 'never':
        default:
            return null;
    }
    
    return $now->format('Y-m-d H:i:s');
}

/**
 * Format file size for display
 */
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    
    return round($bytes, 2) . ' ' . $units[$pow];
}

/**
 * Validate file type
 */
function isValidFileType($mimeType) {
    return in_array($mimeType, ALLOWED_FILE_TYPES);
}

/**
 * Sanitize input data
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Hash password
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user is admin
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Redirect to a page
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Get current user ID
 */
function getCurrentUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 */
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Rate limiting for login attempts
 */
function checkRateLimit($ip, $maxAttempts = 5, $window = 900) { // 15 minutes
    $attempts = isset($_SESSION['login_attempts'][$ip]) ? $_SESSION['login_attempts'][$ip] : 0;
    $lastAttempt = isset($_SESSION['last_attempt'][$ip]) ? $_SESSION['last_attempt'][$ip] : 0;
    
    // Reset if window has passed
    if (time() - $lastAttempt > $window) {
        unset($_SESSION['login_attempts'][$ip]);
        unset($_SESSION['last_attempt'][$ip]);
        return true;
    }
    
    // Check if limit exceeded
    if ($attempts >= $maxAttempts) {
        return false;
    }
    
    return true;
}

/**
 * Record login attempt
 */
function recordLoginAttempt($ip) {
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = [];
    }
    if (!isset($_SESSION['last_attempt'])) {
        $_SESSION['last_attempt'] = [];
    }
    
    $_SESSION['login_attempts'][$ip] = isset($_SESSION['login_attempts'][$ip]) ? 
        $_SESSION['login_attempts'][$ip] + 1 : 1;
    $_SESSION['last_attempt'][$ip] = time();
}

/**
 * Check if file extension is dangerous
 */
function isDangerousFile($filename) {
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($extension, BLOCKED_EXTENSIONS);
}

/**
 * Check if file content is safe
 */
function isSafeFileContent($filepath) {
    // Skip content scanning for non-text files
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $filepath);
    finfo_close($finfo);
    
    // For text files, check for potentially dangerous content
    if (strpos($mimeType, 'text/') === 0) {
        $content = file_get_contents($filepath);
        
        // Look for potentially dangerous patterns
        $dangerous_patterns = [
            '/<\?php/i',           // PHP tags
            '/<\?=/i',             // PHP short tags
            '/<script/i',           // JavaScript
            '/javascript:/i',       // JavaScript URLs
            '/onload=/i',           // Event handlers
            '/onerror=/i',          // Event handlers
            '/eval\s*\(/i',       // eval function
            '/document\.cookie/i',  // Cookie access
            '/document\.write/i',  // Document write
        ];
        
        foreach ($dangerous_patterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return false;
            }
        }
    }
    
    return true;
}

/**
 * Get cached data or fetch from database
 */
function getCachedData($key, $callback, $ttl = 300) {
    // Simple in-memory cache using session
    if (isset($_SESSION['cache'][$key])) {
        $cached = $_SESSION['cache'][$key];
        if (time() - $cached['time'] < $ttl) {
            return $cached['data'];
        }
    }
    
    // Fetch fresh data
    $data = $callback();
    
    // Store in cache
    if (!isset($_SESSION['cache'])) {
        $_SESSION['cache'] = [];
    }
    $_SESSION['cache'][$key] = [
        'data' => $data,
        'time' => time()
    ];
    
    return $data;
}

/**
 * Get latest public shares with caching
 */
function getLatestShares($limit = 15) {
    return getCachedData('latest_shares_' . $limit, function() use ($limit) {
        try {
            $db = new Database();
            $pdo = $db->getConnection();
            
            $stmt = $pdo->prepare("SELECT s.*, u.username FROM shares s LEFT JOIN users u ON s.created_by = u.id WHERE s.is_public = 1 AND (s.expiration_date IS NULL OR s.expiration_date > NOW()) ORDER BY s.created_at DESC LIMIT ?");
            $stmt->execute([$limit]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Latest shares query error: " . $e->getMessage());
            return [];
        }
    });
}
?>