<?php
// error_debug.php - Detailed error debugging

// Enable all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

echo "<h1>Detailed Error Debugging</h1>";

// Test each component step by step
echo "<h2>1. Testing Config Load</h2>";
try {
    require_once 'config.php';
    echo "<p style='color: green;'>✓ Config loaded successfully</p>";
    echo "<p>Site Name: " . SITE_NAME . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Config load failed: " . $e->getMessage() . "</p>";
    exit;
}

echo "<h2>2. Testing Database Class</h2>";
try {
    require_once 'db.php';
    $db = new Database();
    echo "<p style='color: green;'>✓ Database class loaded successfully</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database class failed: " . $e->getMessage() . "</p>";
    exit;
}

echo "<h2>3. Testing Database Connection</h2>";
try {
    $pdo = $db->getConnection();
    echo "<p style='color: green;'>✓ Database connection successful</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
    exit;
}

echo "<h2>4. Testing Functions Load</h2>";
try {
    require_once 'functions.php';
    echo "<p style='color: green;'>✓ Functions loaded successfully</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Functions load failed: " . $e->getMessage() . "</p>";
    exit;
}

echo "<h2>5. Testing Session Init</h2>";
try {
    require_once 'init.php';
    echo "<p style='color: green;'>✓ Session init successful</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Session init failed: " . $e->getMessage() . "</p>";
    exit;
}

echo "<h2>6. Testing Key Functions</h2>";
try {
    // Test a few key functions
    $test_key = generateShareKey();
    echo "<p style='color: green;'>✓ generateShareKey() works: " . $test_key . "</p>";
    
    $test_size = formatFileSize(1024 * 1024);
    echo "<p style='color: green;'>✓ formatFileSize() works: " . $test_size . "</p>";
    
    $is_logged_in = isLoggedIn();
    echo "<p style='color: green;'>✓ isLoggedIn() works: " . ($is_logged_in ? 'true' : 'false') . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Key functions failed: " . $e->getMessage() . "</p>";
    exit;
}

echo "<h2>7. Testing Database Queries</h2>";
try {
    // Test a simple query
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "<p style='color: green;'>✓ Database query successful. Users count: " . $result['count'] . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database query failed: " . $e->getMessage() . "</p>";
    exit;
}

echo "<h2>All Tests Passed!</h2>";
echo "<p>If you're still seeing a 500 error on the homepage, the issue might be with the .htaccess file or URL rewriting.</p>";

echo "<h2>Next Steps:</h2>";
echo "<ol>";
echo "<li>Check your .htaccess file for correct PHP version</li>";
echo "<li>Try accessing index.php directly: " . $_SERVER['HTTP_HOST'] . "/index.php</li>";
echo "<li>Check the php_errors.log file for any errors</li>";
echo "</ol>";
?>