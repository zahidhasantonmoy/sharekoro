<?php
// test_db.php - Test database connection

require_once 'init.php';

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    // Test query
    $stmt = $pdo->query("SELECT VERSION() as version");
    $result = $stmt->fetch();
    
    echo "Database connection successful!\n";
    echo "MySQL version: " . $result['version'] . "\n";
    
    // Test tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll();
    
    echo "Tables in database:\n";
    foreach ($tables as $table) {
        echo "- " . reset($table) . "\n";
    }
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
}
?>
