<?php
// index-check.php - Check index.php loading step by step

echo "<h1>Index Loading Check</h1>";

// Step 1: Check if we can load init.php
echo "<h2>Step 1: Loading init.php</h2>";
try {
    require_once 'init.php';
    echo "<p style='color: green;'>✓ init.php loaded successfully</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error loading init.php: " . $e->getMessage() . "</p>";
    exit;
}

// Step 2: Check if we can access config values
echo "<h2>Step 2: Checking Configuration</h2>";
if (defined('SITE_NAME')) {
    echo "<p style='color: green;'>✓ SITE_NAME: " . SITE_NAME . "</p>";
} else {
    echo "<p style='color: red;'>✗ SITE_NAME not defined</p>";
}

// Step 3: Check if we can use functions
echo "<h2>Step 3: Testing Functions</h2>";
try {
    $test_key = generateShareKey();
    echo "<p style='color: green;'>✓ generateShareKey(): " . $test_key . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error with generateShareKey(): " . $e->getMessage() . "</p>";
}

// Step 4: Check database connection
echo "<h2>Step 4: Testing Database</h2>";
try {
    $db = new Database();
    $pdo = $db->getConnection();
    echo "<p style='color: green;'>✓ Database connection successful</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
}

// Step 5: Check session
echo "<h2>Step 5: Testing Session</h2>";
if (isset($_SESSION)) {
    $_SESSION['index_check'] = 'test';
    echo "<p style='color: green;'>✓ Session working</p>";
} else {
    echo "<p style='color: red;'>✗ Session not working</p>";
}

echo "<h2>Step 6: Testing HTML Output</h2>";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Index Check</title>
</head>
<body>
    <h1>HTML Output Working</h1>
    <p>If you can see this, HTML output is working.</p>
    
    <?php
    // Test PHP in HTML
    echo "<p>Current time: " . date('Y-m-d H:i:s') . "</p>";
    
    if (defined('SITE_NAME')) {
        echo "<p>Site: " . SITE_NAME . "</p>";
    }
    ?>
</body>
</html>