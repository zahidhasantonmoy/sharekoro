<?php
// config.php - Database configuration and constants

// Database configuration for InfinityFree
define('DB_HOST', 'sql204.infinityfree.com');
define('DB_PORT', 3306);
define('DB_USER', 'if0_39860069');
define('DB_PASS', 'kkfRZ1rUzP5b5');
define('DB_NAME', 'if0_39860069_sharekoro');

// Site configuration
define('SITE_URL', 'http://sharekoro.rf.gd'); // Change to your actual domain
define('SITE_NAME', 'ShareKoro');
define('ADMIN_EMAIL', 'admin@sharekoro.com');

// File upload configuration
define('UPLOAD_DIR', 'uploads/');
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB in bytes
define('ALLOWED_FILE_TYPES', [
    'text/plain',
    'text/html',
    'text/css',
    'text/javascript',
    'application/javascript',
    'application/json',
    'application/xml',
    'image/jpeg',
    'image/png',
    'image/gif',
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
]);

// Expiration options
define('EXPIRATION_OPTIONS', [
    '1_hour' => '1 Hour',
    '1_day' => '1 Day',
    '1_week' => '1 Week',
    '1_month' => '1 Month',
    'never' => 'Never'
]);

// Session configuration
define('SESSION_LIFETIME', 3600); // 1 hour
?>