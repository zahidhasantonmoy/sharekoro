<?php
// install_check.php - Installation checker script

echo "<h1>ShareKoro Installation Checker</h1>";

// Check PHP version
echo "<h2>PHP Version Check</h2>";
$phpVersion = phpversion();
echo "<p>PHP Version: " . $phpVersion . "</p>";
if (version_compare($phpVersion, '7.4.0', '>=')) {
    echo "<p style='color: green;'>✓ PHP version is compatible</p>";
} else {
    echo "<p style='color: red;'>✗ PHP version is too old. Please upgrade to PHP 7.4 or higher.</p>";
}

// Check required extensions
echo "<h2>Required Extensions Check</h2>";
$requiredExtensions = ['pdo', 'pdo_mysql', 'session', 'openssl', 'fileinfo'];
foreach ($requiredExtensions as $extension) {
    if (extension_loaded($extension)) {
        echo "<p style='color: green;'>✓ Extension '{$extension}' is loaded</p>";
    } else {
        echo "<p style='color: red;'>✗ Extension '{$extension}' is not loaded</p>";
    }
}

// Check file permissions
echo "<h2>File Permissions Check</h2>";
$requiredWritable = ['uploads', 'error.log'];
foreach ($requiredWritable as $item) {
    $path = __DIR__ . '/' . $item;
    if (is_writable($path)) {
        echo "<p style='color: green;'>✓ '{$item}' is writable</p>";
    } else {
        // Try to make it writable
        if (@chmod($path, 0755)) {
            echo "<p style='color: green;'>✓ '{$item}' is now writable (permission fixed)</p>";
        } else {
            echo "<p style='color: orange;'>⚠ '{$item}' is not writable. Please check permissions.</p>";
        }
    }
}

// Check configuration files
echo "<h2>Configuration Files Check</h2>";
$configFiles = ['config.php', 'db.php', 'functions.php', 'init.php'];
foreach ($configFiles as $file) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        echo "<p style='color: green;'>✓ Configuration file '{$file}' exists</p>";
    } else {
        echo "<p style='color: red;'>✗ Configuration file '{$file}' is missing</p>";
    }
}

// Check share files
echo "<h2>Share Files Check</h2>";
$shareFiles = ['share-text.php', 'share-code.php', 'share-file.php'];
foreach ($shareFiles as $file) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        echo "<p style='color: green;'>✓ Share file '{$file}' exists</p>";
    } else {
        echo "<p style='color: red;'>✗ Share file '{$file}' is missing</p>";
    }
}

echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>Run <a href='update_database.php'>update_database.php</a> to ensure your database schema is up to date</li>";
echo "<li>Test the share functionality by visiting share-text.php, share-code.php, and share-file.php</li>";
echo "<li>Check the <a href='debug.php'>debug.php</a> script if you encounter any issues</li>";
echo "</ol>";

echo "<p><a href='index.php'>← Return to Homepage</a></p>";
?>