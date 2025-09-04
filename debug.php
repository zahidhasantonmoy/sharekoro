<?php
// debug.php - Debug script to test database connection

// Include configuration
require_once 'config.php';

echo "<h1>ShareKoro Debug Information</h1>";

// Test database connection
echo "<h2>Database Connection Test</h2>";

try {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    
    echo "<p style='color: green;'><strong>✓ Database connection successful!</strong></p>";
    
    // Test query
    $stmt = $pdo->query("SELECT VERSION() as version");
    $result = $stmt->fetch();
    echo "<p>MySQL Version: " . $result['version'] . "</p>";
    
    // Test tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll();
    
    echo "<p>Tables in database:</p>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>" . reset($table) . "</li>";
    }
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'><strong>✗ Database connection failed:</strong> " . $e->getMessage() . "</p>";
}

// Test file permissions
echo "<h2>File Permissions Test</h2>";
echo "<p>Current directory: " . getcwd() . "</p>";

if (is_writable('uploads')) {
    echo "<p style='color: green;'><strong>✓ Uploads directory is writable</strong></p>";
} else {
    echo "<p style='color: red;'><strong>✗ Uploads directory is not writable</strong></p>";
}

// Test PHP version
echo "<h2>PHP Version</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";

// Test required extensions
echo "<h2>Required Extensions</h2>";
$required_extensions = ['pdo', 'pdo_mysql', 'session', 'fileinfo'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<p style='color: green;'><strong>✓ $ext</strong> is loaded</p>";
    } else {
        echo "<p style='color: red;'><strong>✗ $ext</strong> is not loaded</p>";
    }
}
?>