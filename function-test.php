<?php
// function-test.php - Test specific functions used in index.php

echo "<h1>Function Test</h1>";

// Load init first
require_once 'init.php';

echo "<h2>Testing isLoggedIn()</h2>";
try {
    $logged_in = isLoggedIn();
    echo "<p style='color: green;'>✓ isLoggedIn() returned: " . ($logged_in ? 'true' : 'false') . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ isLoggedIn() failed: " . $e->getMessage() . "</p>";
}

echo "<h2>Testing isAdmin()</h2>";
try {
    $is_admin = isAdmin();
    echo "<p style='color: green;'>✓ isAdmin() returned: " . ($is_admin ? 'true' : 'false') . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ isAdmin() failed: " . $e->getMessage() . "</p>";
}

echo "<h2>Testing getCurrentUserId()</h2>";
try {
    $user_id = getCurrentUserId();
    echo "<p style='color: green;'>✓ getCurrentUserId() returned: " . ($user_id ? $user_id : 'null') . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ getCurrentUserId() failed: " . $e->getMessage() . "</p>";
}

echo "<h2>Session Data</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>Complete</h2>";
echo "<p>If you can see this, the functions are working.</p>";
?>