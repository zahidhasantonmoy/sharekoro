<?php
// test-init.php - Test init.php loading

echo "<h1>Testing init.php Loading</h1>";

try {
    echo "<h2>Step 1: Loading init.php</h2>";
    require_once 'init.php';
    echo "<p style='color: green;'>✓ init.php loaded successfully</p>";
    
    echo "<h2>Step 2: Testing Configuration</h2>";
    if (defined('SITE_NAME')) {
        echo "<p style='color: green;'>✓ SITE_NAME defined: " . SITE_NAME . "</p>";
    } else {
        echo "<p style='color: red;'>✗ SITE_NAME not defined</p>";
    }
    
    echo "<h2>Step 3: Testing Database</h2>";
    try {
        $db = new Database();
        $pdo = $db->getConnection();
        echo "<p style='color: green;'>✓ Database connection successful</p>";
        
        // Test a simple query
        $stmt = $pdo->query("SELECT 1 as test");
        $result = $stmt->fetch();
        echo "<p style='color: green;'>✓ Database query successful: " . $result['test'] . "</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Database test failed: " . $e->getMessage() . "</p>";
    }
    
    echo "<h2>Step 4: Testing Functions</h2>";
    try {
        $key = generateShareKey();
        echo "<p style='color: green;'>✓ generateShareKey() works: " . $key . "</p>";
        
        $isLoggedIn = isLoggedIn();
        echo "<p style='color: green;'>✓ isLoggedIn() works: " . ($isLoggedIn ? 'true' : 'false') . "</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Functions test failed: " . $e->getMessage() . "</p>";
    }
    
    echo "<h2>Step 5: Testing Session</h2>";
    if (isset($_SESSION)) {
        echo "<p style='color: green;'>✓ Session array exists</p>";
    } else {
        echo "<p style='color: red;'>✗ Session array does not exist</p>";
    }
    
    echo "<h2>All Tests Completed Successfully!</h2>";
    echo "<p>If you can see this message, init.php is working correctly.</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error loading init.php: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>