<?php
// trace_error.php - Trace the exact error in share-text.php

// Enable all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/trace_error.log');

echo "Starting error trace...\n";

// Register shutdown function to catch fatal errors
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
        echo "FATAL ERROR CAUGHT:\n";
        echo "Type: " . $error['type'] . "\n";
        echo "Message: " . $error['message'] . "\n";
        echo "File: " . $error['file'] . "\n";
        echo "Line: " . $error['line'] . "\n";
        error_log("FATAL ERROR: " . print_r($error, true));
    }
});

// Custom error handler
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    echo "ERROR: $errstr in $errfile on line $errline\n";
    error_log("ERROR: $errstr in $errfile on line $errline");
    return false;
});

echo "Error handlers registered.\n";

// Now try to include share-text.php step by step
echo "Attempting to include share-text.php...\n";

// First just include init.php
echo "Including init.php...\n";
require_once 'init.php';
echo "init.php included successfully.\n";

echo "Setting up variables...\n";
$error = '';
$success = '';
$share_link = '';

echo "Generating CSRF token...\n";
$csrf_token = generateCSRFToken();
echo "CSRF token generated.\n";

echo "Error trace completed.\n";
?>