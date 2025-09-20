<?php
// config.php - Database configuration and constants

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

// Database configuration for InfinityFree
define('DB_HOST', 'sql204.infinityfree.com');
define('DB_PORT', 3306);
define('DB_USER', 'if0_39860069');
define('DB_PASS', 'kkfRZ1rUzP5b5');
define('DB_NAME', 'if0_39860069_sharekoro');

// Site configuration
define('SITE_URL', 'https://sharekoro.free.nf'); // Change to your actual domain
define('SITE_NAME', 'ShareKoro');
define('ADMIN_EMAIL', 'admin@sharekoro.com');

// File upload configuration
define('UPLOAD_DIR', 'uploads/');
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB in bytes

// Enhanced allowed file types with security considerations
define('ALLOWED_FILE_TYPES', [
    // Text files
    'text/plain',
    'text/html',
    'text/css',
    'text/javascript',
    'text/csv',
    'text/xml',
    
    // Programming languages
    'application/javascript',
    'application/json',
    'application/xml',
    'application/x-sh',
    'application/x-perl',
    'application/x-python',
    'application/x-ruby',
    
    // Images
    'image/jpeg',
    'image/png',
    'image/gif',
    'image/webp',
    'image/svg+xml',
    
    // Documents
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/vnd.ms-excel',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'application/vnd.ms-powerpoint',
    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    
    // Archives (use with caution)
    'application/zip',
    'application/x-tar',
    'application/gzip'
]);

// Dangerous file extensions to block (even if MIME type is allowed)
define('BLOCKED_EXTENSIONS', [
    'php', 'php3', 'php4', 'php5', 'phtml', 'phps',
    'asp', 'aspx', 'jsp', 'cgi', 'pl', 'py',
    'exe', 'bat', 'cmd', 'sh', 'bin',
    'js', 'html', 'htm', 'xhtml', 'xml'
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

// Security settings
define('ENABLE_FILE_CONTENT_SCAN', true);
define('ENABLE_ANTIVIRUS_SCAN', false); // Set to true if you have antivirus integration
?>