<?php
// constants_test.php - Test all required constants

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing required constants...\n";

// Include config to get constants
require_once 'config.php';

echo "Configuration loaded.\n";

// Test SITE_* constants
$site_constants = ['SITE_URL', 'SITE_NAME', 'ADMIN_EMAIL'];
foreach ($site_constants as $constant) {
    if (defined($constant)) {
        $value = constant($constant);
        echo "✓ {$constant}: " . substr($value, 0, 50) . (strlen($value) > 50 ? '...' : '') . "\n";
    } else {
        echo "✗ {$constant} is not defined\n";
    }
}

// Test EXPIRATION_OPTIONS specifically
echo "Testing EXPIRATION_OPTIONS...\n";
if (defined('EXPIRATION_OPTIONS')) {
    $options = EXPIRATION_OPTIONS;
    echo "✓ EXPIRATION_OPTIONS is defined\n";
    echo "Options: " . print_r($options, true) . "\n";
} else {
    echo "✗ EXPIRATION_OPTIONS is not defined\n";
}

// Test other constants
$other_constants = [
    'DB_HOST', 'DB_PORT', 'DB_USER', 'DB_PASS', 'DB_NAME',
    'UPLOAD_DIR', 'MAX_FILE_SIZE',
    'SESSION_LIFETIME'
];

echo "Testing other constants...\n";
foreach ($other_constants as $constant) {
    if (defined($constant)) {
        $value = constant($constant);
        // Mask sensitive data
        if (in_array($constant, ['DB_PASS'])) {
            $value = str_repeat('*', strlen($value));
        }
        echo "✓ {$constant}: " . substr($value, 0, 50) . (strlen($value) > 50 ? '...' : '') . "\n";
    } else {
        echo "✗ {$constant} is not defined\n";
    }
}

// Test ALLOWED_FILE_TYPES
echo "Testing ALLOWED_FILE_TYPES...\n";
if (defined('ALLOWED_FILE_TYPES')) {
    $types = ALLOWED_FILE_TYPES;
    echo "✓ ALLOWED_FILE_TYPES is defined\n";
    echo "Count: " . count($types) . " types\n";
    echo "First 3: " . implode(', ', array_slice($types, 0, 3)) . "\n";
} else {
    echo "✗ ALLOWED_FILE_TYPES is not defined\n";
}

// Test BLOCKED_EXTENSIONS
echo "Testing BLOCKED_EXTENSIONS...\n";
if (defined('BLOCKED_EXTENSIONS')) {
    $extensions = BLOCKED_EXTENSIONS;
    echo "✓ BLOCKED_EXTENSIONS is defined\n";
    echo "Count: " . count($extensions) . " extensions\n";
    echo "First 3: " . implode(', ', array_slice($extensions, 0, 3)) . "\n";
} else {
    echo "✗ BLOCKED_EXTENSIONS is not defined\n";
}

echo "Constants test completed.\n";
?>