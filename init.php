<?php
// init.php - Initialize session and include required files

// Set error reporting first
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include configuration first
require_once 'config.php';

// Set session lifetime after config is loaded
if (defined('SESSION_LIFETIME')) {
    ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
    session_set_cookie_params(SESSION_LIFETIME);
}

// Include database class
require_once 'db.php';

// Include functions (make sure db.php is not required again in functions.php)
require_once 'functions.php';

// Set timezone
date_default_timezone_set('UTC');
?>