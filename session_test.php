<?php
// session_test.php - Test session handling

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing session handling...\n";

// Check if session is already started
if (session_status() === PHP_SESSION_NONE) {
    echo "No session started, attempting to start...\n";
    try {
        session_start();
        echo "✓ Session started successfully\n";
        $_SESSION['test'] = 'session_test_value';
        echo "✓ Session variable set\n";
    } catch (Exception $e) {
        echo "✗ Failed to start session: " . $e->getMessage() . "\n";
    }
} else {
    echo "Session already started\n";
    $_SESSION['test'] = 'session_test_value';
    echo "✓ Session variable set\n";
}

echo "Session ID: " . session_id() . "\n";
echo "Session data: " . print_r($_SESSION, true) . "\n";

echo "Session test complete.\n";
?>
