<?php
// csrf_test.php - Test CSRF token generation specifically

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing CSRF token generation...\n";

// Test if required functions exist
echo "Checking required functions...\n";
$functions = ['random_bytes', 'bin2hex', 'hash_equals'];
foreach ($functions as $function) {
    if (function_exists($function)) {
        echo "✓ Function {$function} exists\n";
    } else {
        echo "✗ Function {$function} does not exist\n";
    }
}

// Test random_bytes
echo "Testing random_bytes...\n";
try {
    $random_data = random_bytes(32);
    echo "✓ random_bytes successful, length: " . strlen($random_data) . "\n";
    
    // Test bin2hex
    $hex = bin2hex($random_data);
    echo "✓ bin2hex successful, length: " . strlen($hex) . "\n";
} catch (Exception $e) {
    echo "✗ random_bytes failed: " . $e->getMessage() . "\n";
}

// Test the actual CSRF token generation
echo "Testing actual CSRF token generation...\n";
try {
    // Simulate the function
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    $token = $_SESSION['csrf_token'];
    echo "✓ CSRF token generated: " . substr($token, 0, 10) . "...\n";
} catch (Exception $e) {
    echo "✗ CSRF token generation failed: " . $e->getMessage() . "\n";
}

echo "CSRF test completed.\n";
?>