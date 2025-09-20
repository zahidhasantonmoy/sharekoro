<?php
// free_nf_init_test.php - Test init.php specifically for free.nf hosting

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "Testing init.php for free.nf hosting...\n";

// Test session handling specific to free.nf
echo "Testing session configuration...\n";

if (session_status() === PHP_SESSION_NONE) {
    echo "No active session, starting one...\n";
    
    // Load config
    require_once 'config.php';
    
    // Set session parameters optimized for free.nf
    if (defined('SESSION_LIFETIME')) {
        ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path' => '/',
            'domain' => '.free.nf',  // Specific to free.nf
            'secure' => false,       // free.nf might not support HTTPS properly
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }
    
    // Try to start session
    echo "Attempting to start session...\n";
    $session_result = session_start();
    echo "Session start result: " . ($session_result ? 'success' : 'failed') . "\n";
    echo "Session ID: " . session_id() . "\n";
} else {
    echo "Session already active\n";
    require_once 'config.php';
}

// Test setting a session variable
echo "Setting session variable...\n";
$_SESSION['free_nf_test'] = 'test_value_' . time();
echo "Session variable set\n";

// Test getting it back
echo "Retrieving session variable...\n";
$test_value = $_SESSION['free_nf_test'] ?? 'not found';
echo "Retrieved value: " . $test_value . "\n";

// Test database connection
echo "Testing database connection...\n";
require_once 'db.php';
try {
    $db = new Database();
    $pdo = $db->getConnection();
    echo "Database connection successful\n";
} catch (Exception $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
}

// Test functions
echo "Testing functions...\n";
require_once 'functions.php';

echo "Init test for free.nf completed.\n";
?>