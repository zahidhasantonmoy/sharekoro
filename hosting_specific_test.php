<?php
// hosting_specific_test.php - Test specifically for free.nf hosting

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/hosting_test.log');

echo "=== Hosting Specific Test for sharekoro.free.nf ===\n";
echo "Current Time: " . date('Y-m-d H:i:s') . "\n";
echo "Server Name: " . $_SERVER['SERVER_NAME'] . "\n";
echo "HTTP Host: " . $_SERVER['HTTP_HOST'] . "\n";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
echo "PHP Version: " . phpversion() . "\n";

// Test 1: Check domain configuration
echo "\n--- Domain Configuration Test ---\n";
require_once 'config.php';
echo "Configured SITE_URL: " . SITE_URL . "\n";
echo "Current HTTP_HOST: " . $_SERVER['HTTP_HOST'] . "\n";

if (strpos(SITE_URL, $_SERVER['HTTP_HOST']) !== false) {
    echo "✓ Domain configuration matches\n";
} else {
    echo "⚠ Domain configuration mismatch - this might cause issues\n";
}

// Test 2: Check document root and paths
echo "\n--- Path Configuration Test ---\n";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Script Filename: " . $_SERVER['SCRIPT_FILENAME'] . "\n";
echo "Current Script: " . __FILE__ . "\n";

// Test 3: Check if required directories exist and are writable
echo "\n--- Directory Permissions Test ---\n";
$directories = ['uploads', 'assets'];
foreach ($directories as $dir) {
    if (is_dir($dir)) {
        echo "✓ Directory {$dir} exists\n";
        if (is_writable($dir)) {
            echo "✓ Directory {$dir} is writable\n";
        } else {
            echo "⚠ Directory {$dir} is not writable\n";
        }
    } else {
        echo "⚠ Directory {$dir} does not exist\n";
    }
}

// Test 4: Test database with your specific configuration
echo "\n--- Database Test ---\n";
try {
    $db = new Database();
    $pdo = $db->getConnection();
    echo "✓ Database connection successful\n";
    
    // Test table existence
    $stmt = $pdo->prepare("SHOW TABLES LIKE 'shares'");
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        echo "✓ Shares table exists\n";
    } else {
        echo "⚠ Shares table does not exist\n";
    }
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
}

// Test 5: Test session configuration for free.nf
echo "\n--- Session Configuration Test ---\n";
if (session_status() === PHP_SESSION_NONE) {
    echo "Starting session with free.nf specific settings...\n";
    // Try session settings that work well with free.nf
    ini_set('session.cookie_domain', '.free.nf');
    ini_set('session.cookie_secure', false); // free.nf might not support HTTPS
    ini_set('session.cookie_httponly', true);
    ini_set('session.use_only_cookies', 1);
    
    session_start();
    echo "✓ Session started successfully\n";
} else {
    echo "Session already active\n";
}

$_SESSION['hosting_test'] = 'test_value_' . time();
echo "✓ Session variable set\n";

// Test 6: Test specific functions that might fail
echo "\n--- Function Tests ---\n";
try {
    // Test CSRF token
    $token = generateCSRFToken();
    echo "✓ CSRF token generated\n";
    
    // Test share key
    $share_key = generateShareKey();
    echo "✓ Share key generated: {$share_key}\n";
    
    // Test expiration
    $expiration = getExpirationDate('1_day');
    echo "✓ Expiration date generated\n";
    
} catch (Exception $e) {
    echo "✗ Function test failed: " . $e->getMessage() . "\n";
}

echo "\n=== Hosting Test Complete ===\n";
?>