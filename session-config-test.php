<?php
// session-config-test.php - Test session configuration

echo "<h1>Session Configuration Test</h1>";

// Display current session configuration
echo "<h2>Current Session Configuration</h2>";
echo "<pre>";
echo "Session Status: " . session_status() . "\n";
echo "Session Name: " . session_name() . "\n";
echo "Session ID: " . session_id() . "\n";
echo "Session Save Path: " . session_save_path() . "\n";
echo "Session Cookie Params: " . print_r(session_get_cookie_params(), true) . "\n";
echo "</pre>";

// Test starting session
echo "<h2>Testing Session Start</h2>";
try {
    if (session_status() == PHP_SESSION_NONE) {
        $result = session_start();
        echo "<p>session_start() returned: " . ($result ? 'true' : 'false') . "</p>";
    } else {
        echo "<p>Session already started</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error starting session: " . $e->getMessage() . "</p>";
}

// Test setting session variables
echo "<h2>Testing Session Variables</h2>";
try {
    $_SESSION['test'] = 'value';
    $_SESSION['time'] = date('Y-m-d H:i:s');
    echo "<p style='color: green;'>✓ Session variables set successfully</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>Error setting session variables: " . $e->getMessage() . "</p>";
}

// Display session data
echo "<h2>Current Session Data</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>Complete</h2>";
echo "<p>If you can see this, session handling is working.</p>";
?>
