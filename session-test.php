<?php
// session-test.php - Session handling test

echo "<h1>Session Handling Test</h1>";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Current Session Status</h2>";
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

echo "<h2>Starting Session</h2>";
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

echo "<h2>Session Configuration</h2>";
echo "<pre>";
echo "session.save_handler: " . ini_get('session.save_handler') . "\n";
echo "session.save_path: " . ini_get('session.save_path') . "\n";
echo "session.name: " . session_name() . "\n";
echo "session.cookie_lifetime: " . ini_get('session.cookie_lifetime') . "\n";
echo "session.cookie_path: " . ini_get('session.cookie_path') . "\n";
echo "session.cookie_domain: " . ini_get('session.cookie_domain') . "\n";
echo "session.cookie_secure: " . ini_get('session.cookie_secure') . "\n";
echo "session.cookie_httponly: " . ini_get('session.cookie_httponly') . "\n";
echo "</pre>";

echo "<h2>Setting Session Variables</h2>";
try {
    $_SESSION['test_time'] = date('Y-m-d H:i:s');
    $_SESSION['test_value'] = 'session_test_' . rand(1000, 9999);
    
    echo "<p>Set session variables:</p>";
    echo "<ul>";
    echo "<li>test_time: " . $_SESSION['test_time'] . "</li>";
    echo "<li>test_value: " . $_SESSION['test_value'] . "</li>";
    echo "</ul>";
} catch (Exception $e) {
    echo "<p style='color: red;'>Error setting session variables: " . $e->getMessage() . "</p>";
}

echo "<h2>Current Session Data</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>Session ID</h2>";
echo "<p>Session ID: " . session_id() . "</p>";

echo "<h2>Regenerating Session ID</h2>";
try {
    $old_id = session_id();
    session_regenerate_id(true);
    $new_id = session_id();
    
    echo "<p>Old Session ID: $old_id</p>";
    echo "<p>New Session ID: $new_id</p>";
    echo "<p>Session IDs are " . ($old_id != $new_id ? "different" : "the same") . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>Error regenerating session ID: " . $e->getMessage() . "</p>";
}

echo "<h2>Session Test Complete</h2>";
echo "<p>If you can see this message without errors, session handling is working.</p>";

// Test session destroy
echo "<h2>Testing Session Destroy</h2>";
try {
    // Don't actually destroy the session, just test the function
    echo "<p>session_destroy() function exists: " . (function_exists('session_destroy') ? 'Yes' : 'No') . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>Error testing session destroy: " . $e->getMessage() . "</p>";
}
?>