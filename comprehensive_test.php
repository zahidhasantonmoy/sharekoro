<?php
// comprehensive_test.php - Comprehensive test of share-text.php functionality

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "Starting comprehensive test...\n";

// Step 1: Include init.php exactly as share-text.php does
echo "Step 1: Including init.php\n";
require_once 'init.php';
echo "✓ init.php included\n";

// Step 2: Initialize variables exactly as share-text.php does
echo "Step 2: Initializing variables\n";
$error = '';
$success = '';
$share_link = '';
echo "✓ Variables initialized\n";

// Step 3: Generate CSRF token exactly as share-text.php does
echo "Step 3: Generating CSRF token\n";
$csrf_token = generateCSRFToken();
echo "✓ CSRF token generated: " . substr($csrf_token, 0, 10) . "...\n";

// Step 4: Check if we can access database functions
echo "Step 4: Testing database access\n";
try {
    $db = new Database();
    $pdo = $db->getConnection();
    echo "✓ Database connection successful\n";
    
    // Test a simple query
    $stmt = $pdo->prepare("SELECT 1");
    $stmt->execute();
    echo "✓ Simple database query successful\n";
} catch (Exception $e) {
    echo "✗ Database access failed: " . $e->getMessage() . "\n";
}

// Step 5: Test utility functions that share-text.php uses
echo "Step 5: Testing utility functions\n";
$test_functions = [
    'generateShareKey',
    'getExpirationDate',
    'hashPassword',
    'getCurrentUserId'
];

foreach ($test_functions as $function_name) {
    if (function_exists($function_name)) {
        echo "✓ Function {$function_name} exists\n";
    } else {
        echo "✗ Function {$function_name} does not exist\n";
    }
}

// Step 6: Test a simple share key generation
echo "Step 6: Testing share key generation\n";
try {
    $share_key = generateShareKey();
    echo "✓ Share key generated: {$share_key}\n";
} catch (Exception $e) {
    echo "✗ Share key generation failed: " . $e->getMessage() . "\n";
}

// Step 7: Test expiration date function
echo "Step 7: Testing expiration date function\n";
try {
    $expiration = getExpirationDate('1_day');
    echo "✓ Expiration date generated: " . ($expiration ?? 'null') . "\n";
} catch (Exception $e) {
    echo "✗ Expiration date generation failed: " . $e->getMessage() . "\n";
}

echo "Comprehensive test completed.\n";
?>