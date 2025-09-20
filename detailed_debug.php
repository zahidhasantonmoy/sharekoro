<?php
// detailed_debug.php - Detailed debugging script

// Enable maximum error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/detailed_error.log');
ini_set('html_errors', 0);

echo "Starting detailed debug...\n";
echo "PHP Version: " . phpversion() . "\n";
echo "Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "\n";
echo "Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "\n";

// Test 1: Check if we can include files
echo "\n--- Testing File Inclusions ---\n";
$files_to_test = ['init.php', 'config.php', 'db.php', 'functions.php'];
foreach ($files_to_test as $file) {
    if (file_exists($file)) {
        echo "✓ {$file} exists\n";
    } else {
        echo "✗ {$file} does not exist\n";
    }
}

// Test 2: Try to include init.php and see where it fails
echo "\n--- Testing init.php Inclusion ---\n";
try {
    echo "Attempting to include init.php...\n";
    include 'init.php';
    echo "✓ init.php included successfully\n";
    
    // Test database connection
    echo "\n--- Testing Database Connection ---\n";
    try {
        $db = new Database();
        $pdo = $db->getConnection();
        echo "✓ Database connection successful\n";
        
        // Test a simple query
        $stmt = $pdo->prepare("SELECT 1 as test");
        $stmt->execute();
        $result = $stmt->fetch();
        echo "✓ Simple query executed: " . ($result['test'] ?? 'null') . "\n";
    } catch (Exception $e) {
        echo "✗ Database connection failed: " . $e->getMessage() . "\n";
        echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    }
    
} catch (Exception $e) {
    echo "✗ Failed to include init.php: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
} catch (Error $e) {
    echo "✗ Fatal error including init.php: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}

// Test 3: Check constants
echo "\n--- Testing Constants ---\n";
$constants_to_check = ['SITE_URL', 'SITE_NAME', 'DB_HOST', 'DB_USER'];
foreach ($constants_to_check as $constant) {
    if (defined($constant)) {
        $value = constant($constant);
        if (in_array($constant, ['DB_PASS'])) {
            $value = str_repeat('*', strlen($value));
        }
        echo "✓ {$constant}: {$value}\n";
    } else {
        echo "✗ {$constant} is not defined\n";
    }
}

echo "\n--- Debug Complete ---\n";

// Also log to file
file_put_contents(__DIR__ . '/detailed_debug.log', ob_get_contents());
?>