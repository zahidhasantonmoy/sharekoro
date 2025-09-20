<?php
// simplified_share_test.php - Simplified test of share functionality

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Simplified Share Test\n";
echo "====================\n";

// Step 1: Include init (but do it step by step)
echo "Step 1: Testing init components\n";

// Test config loading
echo "Loading config...\n";
require_once 'config.php';
echo "✓ Config loaded\n";
echo "SITE_URL: " . SITE_URL . "\n";

// Test database class loading
echo "Loading database class...\n";
require_once 'db.php';
echo "✓ Database class loaded\n";

// Test functions loading
echo "Loading functions...\n";
require_once 'functions.php';
echo "✓ Functions loaded\n";

// Step 2: Test session handling
echo "\nStep 2: Testing session handling\n";
if (session_status() === PHP_SESSION_NONE) {
    echo "Starting session...\n";
    // Use the same session configuration as init.php
    if (defined('SESSION_LIFETIME')) {
        ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path' => '/',
            'domain' => '',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }
    session_start();
    echo "✓ Session started\n";
} else {
    echo "Session already active\n";
}

// Step 3: Test key functions
echo "\nStep 3: Testing key functions\n";

// Test CSRF token generation
echo "Generating CSRF token...\n";
$csrf_token = generateCSRFToken();
echo "✓ CSRF token generated: " . (substr($csrf_token, 0, 10) . '...') . "\n";

// Test share key generation
echo "Generating share key...\n";
$share_key = generateShareKey();
echo "✓ Share key generated: {$share_key}\n";

// Test expiration date
echo "Generating expiration date...\n";
$expiration_date = getExpirationDate('1_day');
echo "✓ Expiration date generated: " . ($expiration_date ?? 'null') . "\n";

// Step 4: Test database connection
echo "\nStep 4: Testing database connection\n";
try {
    $db = new Database();
    $pdo = $db->getConnection();
    echo "✓ Database connection successful\n";
    
    // Test a simple query
    $stmt = $pdo->prepare("SELECT 1 as test");
    $stmt->execute();
    $result = $stmt->fetch();
    echo "✓ Simple query result: " . ($result['test'] ?? 'null') . "\n";
    
} catch (Exception $e) {
    echo "✗ Database test failed: " . $e->getMessage() . "\n";
}

// Step 5: Test EXPIRATION_OPTIONS
echo "\nStep 5: Testing EXPIRATION_OPTIONS\n";
if (defined('EXPIRATION_OPTIONS') && is_array(EXPIRATION_OPTIONS)) {
    echo "✓ EXPIRATION_OPTIONS is properly defined\n";
    foreach (EXPIRATION_OPTIONS as $key => $value) {
        echo "  - {$key}: {$value}\n";
    }
} else {
    echo "✗ EXPIRATION_OPTIONS is not properly defined\n";
}

echo "\nSimplified share test completed.\n";
?>