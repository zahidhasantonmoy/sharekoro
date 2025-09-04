<?php
// init-minimal.php - Minimal init for testing

// Very basic error reporting
error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', 1);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include files in order
require_once 'config.php';
require_once 'db.php';
require_once 'functions.php';

// Set timezone
date_default_timezone_set('UTC');
?>