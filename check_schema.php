<?php
// check_schema.php - Check if database schema updates have been applied

require_once 'init.php';

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    // Check if the visibility column exists
    $stmt = $pdo->prepare("SHOW COLUMNS FROM shares LIKE 'visibility'");
    $stmt->execute();
    $visibilityColumn = $stmt->fetch();
    
    if ($visibilityColumn) {
        echo "✓ visibility column exists\n";
    } else {
        echo "✗ visibility column does not exist\n";
    }
    
    // Check if the access_password column exists
    $stmt = $pdo->prepare("SHOW COLUMNS FROM shares LIKE 'access_password'");
    $stmt->execute();
    $accessPasswordColumn = $stmt->fetch();
    
    if ($accessPasswordColumn) {
        echo "✓ access_password column exists\n";
    } else {
        echo "✗ access_password column does not exist\n";
    }
    
    // Check if the access_code column exists
    $stmt = $pdo->prepare("SHOW COLUMNS FROM shares LIKE 'access_code'");
    $stmt->execute();
    $accessCodeColumn = $stmt->fetch();
    
    if ($accessCodeColumn) {
        echo "✓ access_code column exists\n";
    } else {
        echo "✗ access_code column does not exist\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>