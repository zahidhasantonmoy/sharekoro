<?php
// share_text_test.php - Test share-text.php functionality step by step

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/share_text_test.log');

echo "Testing share-text.php functionality...\n";

// Step 1: Include init.php
echo "Step 1: Including init.php\n";
try {
    require_once 'init.php';
    echo "✓ init.php included successfully\n";
} catch (Exception $e) {
    echo "✗ Failed to include init.php: " . $e->getMessage() . "\n";
    exit;
}

// Step 2: Check if required functions exist
echo "Step 2: Checking required functions\n";
$required_functions = ['generateCSRFToken', 'isLoggedIn', 'isAdmin'];
foreach ($required_functions as $function) {
    if (function_exists($function)) {
        echo "✓ Function {$function} exists\n";
    } else {
        echo "✗ Function {$function} does not exist\n";
    }
}

// Step 3: Test database connection
echo "Step 3: Testing database connection\n";
try {
    $db = new Database();
    $pdo = $db->getConnection();
    echo "✓ Database connection successful\n";
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
    exit;
}

// Step 4: Test share key generation
echo "Step 4: Testing share key generation\n";
try {
    $share_key = generateShareKey();
    echo "✓ Share key generated: {$share_key}\n";
} catch (Exception $e) {
    echo "✗ Share key generation failed: " . $e->getMessage() . "\n";
}

// Step 5: Test expiration date function
echo "Step 5: Testing expiration date function\n";
try {
    $expiration_date = getExpirationDate('1_day');
    echo "✓ Expiration date generated: " . ($expiration_date ?? 'null') . "\n";
} catch (Exception $e) {
    echo "✗ Expiration date generation failed: " . $e->getMessage() . "\n";
}

// Step 6: Test a simple database insert
echo "Step 6: Testing simple database insert\n";
try {
    // Generate unique share key
    do {
        $share_key = generateShareKey();
        $stmt = $pdo->prepare("SELECT id FROM shares WHERE share_key = ?");
        $stmt->execute([$share_key]);
    } while ($stmt->rowCount() > 0);
    
    echo "✓ Unique share key generated: {$share_key}\n";
    
    // Insert a test share
    $stmt = $pdo->prepare("INSERT INTO shares (share_key, title, content, share_type, created_by, is_public, visibility) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $result = $stmt->execute([
        $share_key,
        'Test Title',
        'Test Content',
        'text',
        null,
        1,
        'public'
    ]);
    
    if ($result) {
        $share_id = $pdo->lastInsertId();
        echo "✓ Test share inserted with ID: {$share_id}\n";
        
        // Clean up
        $stmt = $pdo->prepare("DELETE FROM shares WHERE id = ?");
        $stmt->execute([$share_id]);
        echo "✓ Test share cleaned up\n";
    } else {
        echo "✗ Failed to insert test share\n";
    }
} catch (Exception $e) {
    echo "✗ Database insert test failed: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}

echo "Share-text.php functionality test complete.\n";
?>