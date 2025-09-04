<?php
// index-simple.php - Simplified index file for testing

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Simple Index Test</h1>";

// Test basic PHP functionality
echo "<p>Current time: " . date('Y-m-d H:i:s') . "</p>";

// Test if config file can be included
if (file_exists('config.php')) {
    echo "<p style='color: green;'>✓ config.php exists</p>";
    
    try {
        require_once 'config.php';
        echo "<p style='color: green;'>✓ config.php loaded successfully</p>";
        echo "<p>Site Name: " . SITE_NAME . "</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Failed to load config.php: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>✗ config.php not found</p>";
}

// Test if init file can be included
if (file_exists('init.php')) {
    echo "<p style='color: green;'>✓ init.php exists</p>";
    
    try {
        require_once 'init.php';
        echo "<p style='color: green;'>✓ init.php loaded successfully</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Failed to load init.php: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>✗ init.php not found</p>";
}

echo "<h2>File Permissions Test</h2>";
$files_to_check = ['config.php', 'init.php', 'db.php', 'functions.php'];
foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        $is_readable = is_readable($file) ? '✓' : '✗';
        $color = is_readable($file) ? 'green' : 'red';
        echo "<p style='color: $color;'>$is_readable $file - " . (is_readable($file) ? 'readable' : 'NOT readable') . "</p>";
    } else {
        echo "<p style='color: red;'>✗ $file - NOT FOUND</p>";
    }
}

echo "<h2>Session Test</h2>";
if (session_status() == PHP_SESSION_NONE) {
    session_start();
    echo "<p>Session started</p>";
} else {
    echo "<p>Session already active</p>";
}

$_SESSION['test'] = 'test_value';
echo "<p>Session test value: " . $_SESSION['test'] . "</p>";

echo "<h2>Test Complete</h2>";
echo "<p>If you can see this page, PHP is working correctly.</p>";
?>