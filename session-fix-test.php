<?php
// session-fix-test.php - Test the session fix

echo "<h1>Session Fix Test</h1>";

// Test the fixed init
echo "<h2>Testing Fixed Init</h2>";
try {
    require_once 'init.php';
    echo "<p style='color: green;'>✓ init.php loaded successfully</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error loading init.php: " . $e->getMessage() . "</p>";
    exit;
}

// Test session status
echo "<h2>Session Status</h2>";
$status = session_status();
switch ($status) {
    case PHP_SESSION_DISABLED:
        echo "<p>Session support is disabled</p>";
        break;
    case PHP_SESSION_NONE:
        echo "<p>No session is active</p>";
        break;
    case PHP_SESSION_ACTIVE:
        echo "<p>Session is active</p>";
        break;
}

// Test session variables
echo "<h2>Session Variables</h2>";
try {
    $_SESSION['session_fix_test'] = 'test_' . time();
    echo "<p style='color: green;'>✓ Session variable set successfully</p>";
    echo "<p>Value: " . $_SESSION['session_fix_test'] . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error setting session variable: " . $e->getMessage() . "</p>";
}

// Test configuration
echo "<h2>Configuration</h2>";
if (defined('SITE_NAME')) {
    echo "<p style='color: green;'>✓ SITE_NAME: " . SITE_NAME . "</p>";
} else {
    echo "<p style='color: red;'>✗ SITE_NAME not defined</p>";
}

echo "<h2>Complete</h2>";
echo "<p>If you can see this without warnings, the session fix is working!</p>";
?>