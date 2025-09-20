<?php
// update_database.php - Script to update database schema with missing columns

require_once 'init.php';

echo "<h1>Updating Database Schema</h1>";

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    // Check if shares table exists
    $stmt = $pdo->prepare("SHOW TABLES LIKE 'shares'");
    $stmt->execute();
    
    if ($stmt->rowCount() == 0) {
        echo "<p style='color: red;'>Error: Shares table does not exist. Please import database_schema.sql first.</p>";
        exit;
    }
    
    echo "<p>✓ Shares table exists</p>";
    
    // Check current columns
    $stmt = $pdo->prepare("DESCRIBE shares");
    $stmt->execute();
    $columns = $stmt->fetchAll();
    $columnNames = [];
    foreach ($columns as $column) {
        $columnNames[] = $column['Field'];
    }
    
    // Add visibility column if it doesn't exist
    if (!in_array('visibility', $columnNames)) {
        echo "<p>Adding visibility column...</p>";
        $stmt = $pdo->prepare("ALTER TABLE shares ADD COLUMN visibility ENUM('public', 'private', 'protected') DEFAULT 'public' AFTER is_public");
        $stmt->execute();
        echo "<p style='color: green;'>✓ Added visibility column</p>";
    } else {
        echo "<p style='color: green;'>✓ Visibility column already exists</p>";
    }
    
    // Add access_password column if it doesn't exist
    if (!in_array('access_password', $columnNames)) {
        echo "<p>Adding access_password column...</p>";
        $stmt = $pdo->prepare("ALTER TABLE shares ADD COLUMN access_password VARCHAR(255) NULL AFTER password_protect");
        $stmt->execute();
        echo "<p style='color: green;'>✓ Added access_password column</p>";
    } else {
        echo "<p style='color: green;'>✓ Access_password column already exists</p>";
    }
    
    // Add access_code column if it doesn't exist
    if (!in_array('access_code', $columnNames)) {
        echo "<p>Adding access_code column...</p>";
        $stmt = $pdo->prepare("ALTER TABLE shares ADD COLUMN access_code VARCHAR(10) NULL AFTER access_password");
        $stmt->execute();
        echo "<p style='color: green;'>✓ Added access_code column</p>";
    } else {
        echo "<p style='color: green;'>✓ Access_code column already exists</p>";
    }
    
    // Update existing shares to have visibility set
    echo "<p>Updating existing shares with visibility settings...</p>";
    $stmt = $pdo->prepare("UPDATE shares SET visibility = 'public' WHERE is_public = 1 AND visibility IS NULL");
    $stmt->execute();
    
    $stmt = $pdo->prepare("UPDATE shares SET visibility = 'private' WHERE is_public = 0 AND (password_protect IS NOT NULL OR access_password IS NOT NULL) AND visibility IS NULL");
    $stmt->execute();
    
    $stmt = $pdo->prepare("UPDATE shares SET visibility = 'protected' WHERE is_public = 0 AND access_code IS NOT NULL AND visibility IS NULL");
    $stmt->execute();
    
    echo "<p style='color: green;'>✓ Updated existing shares with visibility settings</p>";
    
    // Create indexes if they don't exist
    echo "<p>Creating indexes...</p>";
    try {
        $stmt = $pdo->prepare("CREATE INDEX idx_shares_visibility ON shares(visibility)");
        $stmt->execute();
        echo "<p style='color: green;'>✓ Created visibility index</p>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate') !== false) {
            echo "<p style='color: green;'>✓ Visibility index already exists</p>";
        } else {
            echo "<p style='color: orange;'>⚠ Warning creating visibility index: " . $e->getMessage() . "</p>";
        }
    }
    
    try {
        $stmt = $pdo->prepare("CREATE INDEX idx_shares_access_code ON shares(access_code)");
        $stmt->execute();
        echo "<p style='color: green;'>✓ Created access_code index</p>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate') !== false) {
            echo "<p style='color: green;'>✓ Access_code index already exists</p>";
        } else {
            echo "<p style='color: orange;'>⚠ Warning creating access_code index: " . $e->getMessage() . "</p>";
        }
    }
    
    echo "<h2 style='color: green;'>✓ Database update completed successfully!</h2>";
    echo "<p><a href='index.php'>Return to homepage</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Database error: " . $e->getMessage() . "</p>";
    error_log("Database update error: " . $e->getMessage());
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
    error_log("Database update exception: " . $e->getMessage());
}
?>