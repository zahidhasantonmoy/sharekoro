<?php
// debug.php - Debug script to test database connection and schema

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'debug.log');

echo "<h1>ShareKoro Debug Script</h1>";

// Test database connection
require_once 'config.php';
require_once 'db.php';

try {
    echo "<h2>Testing Database Connection...</h2>";
    $db = new Database();
    $pdo = $db->getConnection();
    echo "<p style='color: green;'>✓ Database connection successful</p>";
    
    // Test if shares table exists
    echo "<h2>Testing Shares Table...</h2>";
    $stmt = $pdo->prepare("SHOW TABLES LIKE 'shares'");
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✓ Shares table exists</p>";
        
        // Check table structure
        echo "<h2>Checking Table Structure...</h2>";
        $stmt = $pdo->prepare("DESCRIBE shares");
        $stmt->execute();
        $columns = $stmt->fetchAll();
        
        echo "<p>Shares table columns:</p>";
        echo "<ul>";
        $columnNames = [];
        foreach ($columns as $column) {
            echo "<li>{$column['Field']} ({$column['Type']})</li>";
            $columnNames[] = $column['Field'];
        }
        echo "</ul>";
        
        // Check for required columns
        $requiredColumns = ['visibility', 'access_password', 'access_code'];
        foreach ($requiredColumns as $requiredColumn) {
            if (in_array($requiredColumn, $columnNames)) {
                echo "<p style='color: green;'>✓ Column '{$requiredColumn}' exists</p>";
            } else {
                echo "<p style='color: red;'>✗ Column '{$requiredColumn}' is missing</p>";
            }
        }
    } else {
        echo "<p style='color: red;'>✗ Shares table does not exist</p>";
    }
    
    // Test configuration constants
    echo "<h2>Testing Configuration Constants...</h2>";
    $constants = ['DB_HOST', 'DB_USER', 'DB_PASS', 'DB_NAME', 'SITE_URL', 'SITE_NAME'];
    foreach ($constants as $constant) {
        if (defined($constant)) {
            $value = constant($constant);
            // Mask sensitive data
            if (in_array($constant, ['DB_PASS'])) {
                $value = str_repeat('*', strlen($value));
            }
            echo "<p style='color: green;'>✓ {$constant}: {$value}</p>";
        } else {
            echo "<p style='color: red;'>✗ {$constant} is not defined</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
    error_log("Debug script error: " . $e->getMessage());
} catch (Error $e) {
    echo "<p style='color: red;'>✗ PHP Error: " . $e->getMessage() . "</p>";
    error_log("Debug script PHP error: " . $e->getMessage());
}

echo "<h2>PHP Info</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Operating System: " . php_uname() . "</p>";

echo "<h2>File Permissions</h2>";
$dirs = ['uploads', 'assets', '.'];
foreach ($dirs as $dir) {
    if (is_dir($dir)) {
        $perms = fileperms($dir);
        echo "<p>{$dir}: " . substr(sprintf('%o', $perms), -4) . "</p>";
    } else {
        echo "<p>{$dir}: Directory does not exist</p>";
    }
}
?>