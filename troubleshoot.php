<?php
// troubleshoot.php - Comprehensive troubleshooting script

echo "<!DOCTYPE html>
<html>
<head>
    <title>ShareKoro Troubleshooting</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        h1, h2, h3 { color: #333; }
        .test-result { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .test-pass { background: #d4edda; border: 1px solid #c3e6cb; }
        .test-fail { background: #f8d7da; border: 1px solid #f5c6cb; }
        .test-warn { background: #fff3cd; border: 1px solid #ffeaa7; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>ShareKoro Comprehensive Troubleshooting</h1>";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

function testResult($title, $result, $details = '') {
    $class = '';
    $icon = '';
    
    if ($result === true) {
        $class = 'test-pass';
        $icon = '✓';
    } elseif ($result === false) {
        $class = 'test-fail';
        $icon = '✗';
    } else {
        $class = 'test-warn';
        $icon = '⚠';
    }
    
    echo "<div class='test-result $class'>";
    echo "<h3>$icon $title</h3>";
    if ($details) {
        echo "<p>$details</p>";
    }
    echo "</div>";
}

// Test 1: PHP Version
$phpVersion = phpversion();
$phpOk = version_compare($phpVersion, '7.4.0', '>=');
testResult(
    'PHP Version Check',
    $phpOk,
    "PHP Version: $phpVersion " . ($phpOk ? '(Compatible)' : '(Minimum required: 7.4.0)')
);

// Test 2: Required Extensions
$requiredExtensions = ['pdo', 'pdo_mysql', 'session', 'fileinfo', 'json', 'mbstring'];
$missingExtensions = [];
foreach ($requiredExtensions as $ext) {
    if (!extension_loaded($ext)) {
        $missingExtensions[] = $ext;
    }
}
$extensionsOk = empty($missingExtensions);
testResult(
    'Required PHP Extensions',
    $extensionsOk,
    $extensionsOk ? 'All required extensions loaded' : 'Missing extensions: ' . implode(', ', $missingExtensions)
);

// Test 3: File Permissions
$directories = ['uploads'];
$permissionIssues = [];
foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        $permissionIssues[] = "$dir directory does not exist";
    } elseif (!is_writable($dir)) {
        $permissionIssues[] = "$dir directory is not writable";
    }
}
$permissionsOk = empty($permissionIssues);
testResult(
    'File Permissions',
    $permissionsOk,
    $permissionsOk ? 'All directories have correct permissions' : implode(', ', $permissionIssues)
);

// Test 4: Config File
$configExists = file_exists('config.php');
testResult(
    'Configuration File',
    $configExists,
    $configExists ? 'config.php found' : 'config.php not found'
);

if ($configExists) {
    try {
        require_once 'config.php';
        testResult('Configuration Load', true, 'config.php loaded successfully');
        
        // Test 5: Database Connection
        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            testResult('Database Connection', true, 'Connected to database successfully');
            
            // Test 6: Database Tables
            try {
                $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
                $tableExists = $stmt->rowCount() > 0;
                testResult(
                    'Database Tables',
                    $tableExists,
                    $tableExists ? 'Required tables found' : 'Required tables missing (run database_schema.sql)'
                );
            } catch (Exception $e) {
                testResult('Database Tables', false, 'Error checking tables: ' . $e->getMessage());
            }
        } catch (Exception $e) {
            testResult('Database Connection', false, 'Database connection failed: ' . $e->getMessage());
        }
    } catch (Exception $e) {
        testResult('Configuration Load', false, 'Error loading config.php: ' . $e->getMessage());
    }
}

// Test 7: Session
$sessionWorking = false;
$sessionError = '';
try {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['troubleshoot_test'] = 'test_value';
    $sessionWorking = isset($_SESSION['troubleshoot_test']);
} catch (Exception $e) {
    $sessionError = $e->getMessage();
}
testResult(
    'Session Handling',
    $sessionWorking,
    $sessionWorking ? 'Session working correctly' : 'Session error: ' . $sessionError
);

// Test 8: File Inclusion
$coreFiles = ['init.php', 'db.php', 'functions.php'];
$missingFiles = [];
foreach ($coreFiles as $file) {
    if (!file_exists($file)) {
        $missingFiles[] = $file;
    }
}
$filesOk = empty($missingFiles);
testResult(
    'Core Files',
    $filesOk,
    $filesOk ? 'All core files present' : 'Missing files: ' . implode(', ', $missingFiles)
);

// Test 9: Error Log
$errorLogExists = file_exists('php_errors.log');
$errorLogSize = $errorLogExists ? filesize('php_errors.log') : 0;
testResult(
    'Error Log',
    $errorLogExists ? ($errorLogSize > 0 ? 'warning' : true) : 'warning',
    $errorLogExists ? 
        ($errorLogSize > 0 ? 
            "Error log exists with $errorLogSize bytes (check for errors)" : 
            "Error log exists but is empty") : 
        "Error log not found"
);

// Test 10: .htaccess
$htaccessExists = file_exists('.htaccess');
testResult(
    '.htaccess File',
    $htaccessExists,
    $htaccessExists ? '.htaccess found' : '.htaccess not found'
);

if ($htaccessExists) {
    $htaccessContent = file_get_contents('.htaccess');
    $hasPhpHandler = strpos($htaccessContent, 'AddHandler application/x-httpd-php') !== false;
    testResult(
        '.htaccess PHP Handler',
        $hasPhpHandler,
        $hasPhpHandler ? 'PHP handler configured' : 'PHP handler not configured in .htaccess'
    );
}

echo "<h2>Server Information</h2>";
echo "<pre>";
echo "Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "\n";
echo "PHP Version: " . phpversion() . "\n";
echo "Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "\n";
echo "Script Filename: " . ($_SERVER['SCRIPT_FILENAME'] ?? 'Unknown') . "\n";
echo "Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'Unknown') . "\n";
echo "</pre>";

echo "<h2>File Structure</h2>";
echo "<pre>";
$files = glob("*");
foreach ($files as $file) {
    $type = is_dir($file) ? '[DIR]' : '[FILE]';
    $perms = substr(sprintf('%o', fileperms($file)), -4);
    echo sprintf("%-5s %-4s %s\n", $type, $perms, $file);
}
echo "</pre>";

if ($errorLogExists && $errorLogSize > 0) {
    echo "<h2>Recent Error Log Entries</h2>";
    echo "<pre>";
    $errorLogContent = file_get_contents('php_errors.log');
    $errorLines = explode("\n", $errorLogContent);
    $recentErrors = array_slice($errorLines, -20); // Last 20 lines
    echo implode("\n", $recentErrors);
    echo "</pre>";
}

echo "<h2>Recommendations</h2>";
echo "<ol>";
echo "<li>Ensure all core files are uploaded correctly</li>";
echo "<li>Verify database configuration in config.php</li>";
echo "<li>Check file permissions (755 for directories, 644 for files)</li>";
echo "<li>Review the error log for specific issues</li>";
echo "<li>Test .htaccess configuration</li>";
echo "<li>Try accessing index.php directly instead of relying on URL rewriting</li>";
echo "</ol>";

echo "</div>
</body>
</html>";
?>