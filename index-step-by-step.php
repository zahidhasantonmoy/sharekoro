<?php
// index-step-by-step.php - Step by step index loading for debugging

// Step 1: Basic PHP
echo "<h1>Step 1: Basic PHP Working</h1>";
echo "<p>Current time: " . date('Y-m-d H:i:s') . "</p>";

// Step 2: Config loading
echo "<h2>Step 2: Loading Config</h2>";
if (file_exists('config.php')) {
    try {
        require_once 'config.php';
        echo "<p style='color: green;'>✓ Config loaded successfully</p>";
        echo "<p>Site Name: " . SITE_NAME . "</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Config load failed: " . $e->getMessage() . "</p>";
        exit;
    }
} else {
    echo "<p style='color: red;'>✗ config.php not found</p>";
    exit;
}

// Step 3: Database class
echo "<h2>Step 3: Loading Database Class</h2>";
if (file_exists('db.php')) {
    try {
        require_once 'db.php';
        echo "<p style='color: green;'>✓ Database class loaded</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Database class load failed: " . $e->getMessage() . "</p>";
        exit;
    }
} else {
    echo "<p style='color: red;'>✗ db.php not found</p>";
    exit;
}

// Step 4: Database connection
echo "<h2>Step 4: Testing Database Connection</h2>";
try {
    $db = new Database();
    $pdo = $db->getConnection();
    echo "<p style='color: green;'>✓ Database connection successful</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
    exit;
}

// Step 5: Functions
echo "<h2>Step 5: Loading Functions</h2>";
if (file_exists('functions.php')) {
    try {
        require_once 'functions.php';
        echo "<p style='color: green;'>✓ Functions loaded</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Functions load failed: " . $e->getMessage() . "</p>";
        exit;
    }
} else {
    echo "<p style='color: red;'>✗ functions.php not found</p>";
    exit;
}

// Step 6: Session initialization
echo "<h2>Step 6: Initializing Session</h2>";
try {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    echo "<p style='color: green;'>✓ Session initialized</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Session initialization failed: " . $e->getMessage() . "</p>";
    exit;
}

// Step 7: Test a function
echo "<h2>Step 7: Testing Functions</h2>";
try {
    $test_key = generateShareKey();
    echo "<p style='color: green;'>✓ generateShareKey() works: $test_key</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Function test failed: " . $e->getMessage() . "</p>";
    exit;
}

// Step 8: Full index load
echo "<h2>Step 8: Loading Full Index</h2>";
echo "<p>Now attempting to load the full index.php...</p>";

// Include the full index
include 'index.php';

echo "<p>If you see this, the full index.php did not load properly.</p>";
?>