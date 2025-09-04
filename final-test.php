<?php
// final-test.php - Final verification test

echo "<!DOCTYPE html>
<html>
<head>
    <title>ShareKoro Final Verification</title>
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
        .step { margin: 15px 0; padding: 10px; border-left: 4px solid #007cba; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>ShareKoro Final Verification</h1>
        <p>This test will verify that all components are working correctly.</p>";

// Enable all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

function showResult($title, $success, $details = '') {
    $class = $success ? 'test-pass' : 'test-fail';
    $icon = $success ? '✓' : '✗';
    
    echo "<div class='test-result $class'>";
    echo "<h3>$icon $title</h3>";
    if ($details) {
        echo "<p>$details</p>";
    }
    echo "</div>";
}

// Test 1: PHP Configuration
echo "<div class='step'>";
echo "<h2>Step 1: PHP Configuration</h2>";

$phpVersionOk = version_compare(PHP_VERSION, '7.4.0', '>=');
showResult(
    'PHP Version Check',
    $phpVersionOk,
    "PHP Version: " . PHP_VERSION . " " . ($phpVersionOk ? '(✓ Compatible)' : '(✗ Minimum required: 7.4.0)')
);

$requiredExtensions = ['pdo', 'pdo_mysql', 'session', 'fileinfo', 'json'];
$missingExtensions = [];
foreach ($requiredExtensions as $ext) {
    if (!extension_loaded($ext)) {
        $missingExtensions[] = $ext;
    }
}
$extensionsOk = empty($missingExtensions);
showResult(
    'Required Extensions',
    $extensionsOk,
    $extensionsOk ? 'All extensions loaded' : 'Missing: ' . implode(', ', $missingExtensions)
);

echo "</div>";

// Test 2: File System
echo "<div class='step'>";
echo "<h2>Step 2: File System</h2>";

$coreFiles = ['config.php', 'init.php', 'db.php', 'functions.php', 'index.php'];
$missingFiles = [];
foreach ($coreFiles as $file) {
    if (!file_exists($file)) {
        $missingFiles[] = $file;
    }
}
$filesOk = empty($missingFiles);
showResult(
    'Core Files',
    $filesOk,
    $filesOk ? 'All core files present' : 'Missing: ' . implode(', ', $missingFiles)
);

$uploadsWritable = is_writable('uploads');
showResult(
    'Uploads Directory',
    $uploadsWritable,
    $uploadsWritable ? 'Writable' : 'Not writable (chmod 755 required)'
);

echo "</div>";

// Test 3: Configuration
echo "<div class='step'>";
echo "<h2>Step 3: Configuration</h2>";

$configLoaded = false;
$configError = '';
try {
    require_once 'config.php';
    $configLoaded = true;
} catch (Exception $e) {
    $configError = $e->getMessage();
}
showResult(
    'Config Loading',
    $configLoaded,
    $configLoaded ? 'config.php loaded successfully' : 'Error: ' . $configError
);

if ($configLoaded) {
    $configVars = ['DB_HOST', 'DB_USER', 'DB_PASS', 'DB_NAME', 'SITE_NAME'];
    $missingConfig = [];
    foreach ($configVars as $var) {
        if (!defined($var)) {
            $missingConfig[] = $var;
        }
    }
    $configVarsOk = empty($missingConfig);
    showResult(
        'Config Variables',
        $configVarsOk,
        $configVarsOk ? 'All required variables defined' : 'Missing: ' . implode(', ', $missingConfig)
    );
}

echo "</div>";

// Test 4: Database
echo "<div class='step'>";
echo "<h2>Step 4: Database</h2>";

if ($configLoaded) {
    $dbConnected = false;
    $dbError = '';
    try {
        require_once 'db.php';
        $db = new Database();
        $pdo = $db->getConnection();
        $dbConnected = true;
    } catch (Exception $e) {
        $dbError = $e->getMessage();
    }
    showResult(
        'Database Connection',
        $dbConnected,
        $dbConnected ? 'Connected successfully' : 'Error: ' . $dbError
    );
    
    if ($dbConnected) {
        try {
            $stmt = $pdo->query("SELECT VERSION() as version");
            $result = $stmt->fetch();
            showResult(
                'Database Version',
                true,
                $result['version']
            );
        } catch (Exception $e) {
            showResult(
                'Database Version',
                false,
                'Error: ' . $e->getMessage()
            );
        }
    }
}

echo "</div>";

// Test 5: Functions
echo "<div class='step'>";
echo "<h2>Step 5: Functions</h2>";

$functionsLoaded = false;
$functionsError = '';
try {
    require_once 'functions.php';
    $functionsLoaded = true;
} catch (Exception $e) {
    $functionsError = $e->getMessage();
}
showResult(
    'Functions Loading',
    $functionsLoaded,
    $functionsLoaded ? 'functions.php loaded successfully' : 'Error: ' . $functionsError
);

if ($functionsLoaded) {
    try {
        $testKey = generateShareKey();
        showResult(
            'generateShareKey()',
            true,
            "Generated key: $testKey"
        );
    } catch (Exception $e) {
        showResult(
            'generateShareKey()',
            false,
            'Error: ' . $e->getMessage()
        );
    }
}

echo "</div>";

// Test 6: Session
echo "<div class='step'>";
echo "<h2>Step 6: Session</h2>";

$sessionOk = false;
$sessionError = '';
try {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['final_test'] = 'test_' . time();
    $sessionOk = true;
} catch (Exception $e) {
    $sessionError = $e->getMessage();
}
showResult(
    'Session Handling',
    $sessionOk,
    $sessionOk ? 'Session working correctly' : 'Error: ' . $sessionError
);

echo "</div>";

// Test 7: Init Loading
echo "<div class='step'>";
echo "<h2>Step 7: Init Loading</h2>";

// End any existing session to test init
if (session_status() == PHP_SESSION_ACTIVE) {
    session_write_close();
}

$initLoaded = false;
$initError = '';
try {
    // Clear any previous session data for this test
    if (isset($_SESSION)) {
        session_unset();
    }
    
    require_once 'init.php';
    $initLoaded = true;
} catch (Exception $e) {
    $initError = $e->getMessage();
}
showResult(
    'Init Loading',
    $initLoaded,
    $initLoaded ? 'init.php loaded successfully' : 'Error: ' . $initError
);

echo "</div>";

// Test 8: Index Loading Simulation
echo "<div class='step'>";
echo "<h2>Step 8: Index Loading Simulation</h2>";

$indexOk = false;
$indexError = '';
ob_start();
try {
    // Simulate what index.php does
    require_once 'init.php';
    
    // Test that we can access config values
    if (defined('SITE_NAME')) {
        $siteName = SITE_NAME;
    }
    
    // Test that we can use functions
    $testKey = generateShareKey(8);
    
    // Test database connection
    $db = new Database();
    $pdo = $db->getConnection();
    
    $indexOk = true;
} catch (Exception $e) {
    $indexError = $e->getMessage();
}
$output = ob_get_clean();

showResult(
    'Index Loading Simulation',
    $indexOk,
    $indexOk ? 'Index would load successfully' : 'Error: ' . $indexError
);

echo "</div>";

// Final Result
echo "<div class='step'>";
echo "<h2>Final Result</h2>";

$allTests = [
    $phpVersionOk,
    $extensionsOk,
    $filesOk,
    $uploadsWritable,
    $configLoaded,
    $dbConnected,
    $functionsLoaded,
    $sessionOk,
    $initLoaded,
    $indexOk
];

$passedTests = array_filter($allTests);
$passedCount = count($passedTests);
$totalTests = count($allTests);

if ($passedCount == $totalTests) {
    echo "<div class='test-result test-pass'>";
    echo "<h3>✓ All Tests Passed!</h3>";
    echo "<p>ShareKoro should be working correctly.</p>";
    echo "</div>";
} else {
    echo "<div class='test-result test-fail'>";
    echo "<h3>✗ $passedCount of $totalTests tests passed</h3>";
    echo "<p>There are issues that need to be addressed.</p>";
    echo "</div>";
}

echo "</div>";

echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>Delete all debugging files (install.php, debug.php, etc.) for security</li>";
echo "<li>Test accessing your site at the root URL</li>";
echo "<li>If you still have issues, check the error logs</li>";
echo "<li>Ensure .htaccess is properly configured for your PHP version</li>";
echo "</ol>";

echo "</div>
</body>
</html>";
?>