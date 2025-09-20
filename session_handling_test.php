<?php
// session_handling_test.php - Test session handling in the same way as init.php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "Testing session handling...\n";

// Replicate the exact session handling from init.php
if (session_status() === PHP_SESSION_NONE) {
    echo "No session active, will start one...\n";
    
    // Load config first
    require_once 'config.php';
    echo "Config loaded.\n";
    
    // Set session parameters
    if (defined('SESSION_LIFETIME')) {
        echo "Setting session lifetime: " . SESSION_LIFETIME . "\n";
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
    
    // Start session
    echo "Starting session...\n";
    session_start();
    echo "✓ Session started successfully\n";
    echo "Session ID: " . session_id() . "\n";
} else {
    echo "Session already active\n";
    require_once 'config.php';
    echo "Config loaded.\n";
}

// Test setting a session variable
echo "Setting session variable...\n";
$_SESSION['test_var'] = 'test_value';
echo "✓ Session variable set\n";

// Test getting session variable
echo "Getting session variable...\n";
$test_value = $_SESSION['test_var'] ?? 'not set';
echo "✓ Session variable value: " . $test_value . "\n";

echo "Session handling test completed.\n";
?>