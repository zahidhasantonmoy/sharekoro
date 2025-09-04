<?php
// init-minimal.php - Minimal init for testing

// Very basic error reporting
error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', 1);

// Handle session configuration properly
if (session_status() === PHP_SESSION_NONE) {
    // Only set session parameters if no session is active
    require_once 'config.php';
    
    // Set session lifetime after config is loaded
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
    
    // Now start the session
    session_start();
} else {
    // Session already started, just load config
    require_once 'config.php';
}

// Include database class and functions
require_once 'db.php';
require_once 'functions.php';

// Set timezone
date_default_timezone_set('UTC');
?>