<?php
// init.php - Initialize session and include required files

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set session lifetime
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
session_set_cookie_params(SESSION_LIFETIME);

// Include required files
require_once 'config.php';
require_once 'db.php';
require_once 'functions.php';

// Set timezone
date_default_timezone_set('UTC');

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>