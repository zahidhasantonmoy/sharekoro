<?php
// final_debug.php - Final comprehensive debug to identify the exact issue

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/final_debug.log');

echo "=== FINAL DEBUG FOR 500 ERROR ===\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n";
echo "PHP Version: " . phpversion() . "\n";
echo "Server: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "\n";
echo "Host: " . ($_SERVER['HTTP_HOST'] ?? 'Unknown') . "\n";
echo "Request Method: " . ($_SERVER['REQUEST_METHOD'] ?? 'Unknown') . "\n";
echo "Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'Unknown') . "\n";

// Test 1: Basic file inclusion
echo "\n--- Test 1: File Inclusion ---\n";
$required_files = ['config.php', 'db.php', 'functions.php', 'init.php'];
foreach ($required_files as $file) {
    if (file_exists($file)) {
        echo "✓ {$file} exists\n";
    } else {
        echo "✗ {$file} does not exist\n";
    }
}

// Test 2: Configuration constants
echo "\n--- Test 2: Configuration Constants ---\n";
require_once 'config.php';
$constants_to_check = [
    'SITE_URL', 'SITE_NAME', 'DB_HOST', 'DB_USER', 'DB_NAME',
    'EXPIRATION_OPTIONS', 'ALLOWED_FILE_TYPES', 'BLOCKED_EXTENSIONS'
];
foreach ($constants_to_check as $constant) {
    if (defined($constant)) {
        echo "✓ {$constant} is defined\n";
    } else {
        echo "✗ {$constant} is NOT defined\n";
    }
}

// Test 3: Database connection
echo "\n--- Test 3: Database Connection ---\n";
try {
    $db = new Database();
    $pdo = $db->getConnection();
    echo "✓ Database connection successful\n";
    
    // Test a simple query
    $stmt = $pdo->prepare("SELECT 1");
    $stmt->execute();
    echo "✓ Simple query successful\n";
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
}

// Test 4: Session handling
echo "\n--- Test 4: Session Handling ---\n";
if (session_status() === PHP_SESSION_NONE) {
    echo "Starting session...\n";
    session_start();
}
$_SESSION['debug_test'] = 'test_value';
echo "✓ Session variable set\n";

// Test 5: Function availability
echo "\n--- Test 5: Function Availability ---\n";
$required_functions = [
    'generateCSRFToken', 'generateShareKey', 'getExpirationDate',
    'hashPassword', 'isLoggedIn', 'isAdmin', 'getCurrentUserId'
];
foreach ($required_functions as $function) {
    if (function_exists($function)) {
        echo "✓ {$function} exists\n";
    } else {
        echo "✗ {$function} does NOT exist\n";
    }
}

// Test 6: CSRF token generation
echo "\n--- Test 6: CSRF Token Generation ---\n";
try {
    $token = generateCSRFToken();
    echo "✓ CSRF token generated: " . substr($token, 0, 10) . "...\n";
} catch (Exception $e) {
    echo "✗ CSRF token generation failed: " . $e->getMessage() . "\n";
}

// Test 7: Share key generation
echo "\n--- Test 7: Share Key Generation ---\n";
try {
    $share_key = generateShareKey();
    echo "✓ Share key generated: {$share_key}\n";
} catch (Exception $e) {
    echo "✗ Share key generation failed: " . $e->getMessage() . "\n";
}

// Test 8: Expiration date function
echo "\n--- Test 8: Expiration Date Function ---\n";
try {
    $expiration = getExpirationDate('1_day');
    echo "✓ Expiration date generated: " . ($expiration ?? 'null') . "\n";
} catch (Exception $e) {
    echo "✗ Expiration date generation failed: " . $e->getMessage() . "\n";
}

// Test 9: Test EXPIRATION_OPTIONS specifically
echo "\n--- Test 9: EXPIRATION_OPTIONS ---\n";
if (defined('EXPIRATION_OPTIONS')) {
    $options = EXPIRATION_OPTIONS;
    if (is_array($options) && !empty($options)) {
        echo "✓ EXPIRATION_OPTIONS is properly defined\n";
        foreach ($options as $key => $value) {
            echo "  - {$key}: {$value}\n";
        }
    } else {
        echo "✗ EXPIRATION_OPTIONS is not a proper array\n";
    }
} else {
    echo "✗ EXPIRATION_OPTIONS is not defined\n";
}

echo "\n=== DEBUG COMPLETE ===\n";

// Log to file as well
file_put_contents(__DIR__ . '/final_debug_output.log', ob_get_contents());
?>