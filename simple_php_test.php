<?php
// simple_php_test.php - Simple PHP functionality test

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Simple PHP Test\n";
echo "==============\n";
echo "PHP Version: " . phpversion() . "\n";
echo "Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "\n";
echo "HTTP Host: " . ($_SERVER['HTTP_HOST'] ?? 'Unknown') . "\n";

// Test basic PHP functionality
echo "\nBasic Functionality Tests:\n";
echo "1. Math: 2 + 2 = " . (2 + 2) . "\n";
echo "2. String: 'Hello' . ' World' = " . ('Hello' . ' World') . "\n";
echo "3. Date: " . date('Y-m-d H:i:s') . "\n";

// Test if required extensions are loaded
echo "\nRequired Extensions:\n";
$required_extensions = ['pdo', 'pdo_mysql', 'session', 'openssl', 'fileinfo'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "✓ {$ext} is loaded\n";
    } else {
        echo "✗ {$ext} is NOT loaded\n";
    }
}

// Test file operations
echo "\nFile Operations:\n";
$test_file = 'test_write.txt';
$test_content = 'This is a test file created at ' . date('Y-m-d H:i:s');
if (file_put_contents($test_file, $test_content)) {
    echo "✓ File write successful\n";
    if (file_exists($test_file)) {
        echo "✓ File exists\n";
        $read_content = file_get_contents($test_file);
        if ($read_content === $test_content) {
            echo "✓ File read successful\n";
        } else {
            echo "✗ File content mismatch\n";
        }
        unlink($test_file); // Clean up
        echo "✓ Test file cleaned up\n";
    } else {
        echo "✗ File does not exist after write\n";
    }
} else {
    echo "✗ File write failed\n";
}

echo "\nSimple PHP test completed.\n";
?>