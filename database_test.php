<?php
// database_test.php - Test database connection and queries specifically

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "Testing database connection and queries...
";

// Include config directly to test
require_once 'config.php';

echo "Configuration loaded.
";
echo "DB_HOST: " . DB_HOST . "
";
echo "DB_USER: " . DB_USER . "
";
echo "DB_NAME: " . DB_NAME . "
";

try {
    echo "Attempting database connection...
";
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    echo "DSN: " . $dsn . "
";
    
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    echo "✓ Database connection successful
";
    
    // Test a simple query
    echo "Testing simple query...
";
    $stmt = $pdo->prepare("SELECT 1 as test");
    $stmt->execute();
    $result = $stmt->fetch();
    echo "✓ Simple query result: " . ($result['test'] ?? 'null') . "
";
    
    // Test shares table query
    echo "Testing shares table query...
";
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM shares");
    $stmt->execute();
    $result = $stmt->fetch();
    echo "✓ Shares table count: " . ($result['count'] ?? '0') . "
";
    
    // Test describe shares table
    echo "Describing shares table...
";
    $stmt = $pdo->prepare("DESCRIBE shares");
    $stmt->execute();
    $columns = $stmt->fetchAll();
    echo "✓ Shares table columns:
";
    foreach ($columns as $column) {
        echo "  - " . $column['Field'] . " (" . $column['Type'] . ")
";
    }
    
} catch (PDOException $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "
";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "
";
    error_log("Database test error: " . $e->getMessage());
} catch (Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "
";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "
";
    error_log("Database test exception: " . $e->getMessage());
}

echo "Database test completed.
";
?>