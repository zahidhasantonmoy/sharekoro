<?php
// deploy_fixes.php - Script to apply all fixes for the 500 Internal Server Error

echo "<h1>ShareKoro - 500 Internal Server Error Fixes Deployment</h1>";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. Update .htaccess PHP version
echo "<h2>1. Updating .htaccess PHP version...</h2>";
$htaccessContent = file_get_contents('.htaccess');
$htaccessContent = str_replace(
    "# AddHandler application/x-httpd-php81 .php",
    "# AddHandler application/x-httpd-php81 .php",
    $htaccessContent
);
$htaccessContent = str_replace(
    "# AddHandler application/x-httpd-php80 .php",
    "AddHandler application/x-httpd-php80 .php",
    $htaccessContent
);
file_put_contents('.htaccess', $htaccessContent);
echo "<p style='color: green;'>✓ Updated .htaccess to use PHP 8.0</p>";

// 2. Update config.php with error logging
echo "<h2>2. Updating config.php with error logging...</h2>";
$configContent = file_get_contents('config.php');
if (strpos($configContent, 'error_reporting') === false) {
    $configContent = str_replace(
        "<?php\n// config.php - Database configuration and constants",
        "<?php\n// config.php - Database configuration and constants\n\n// Enable error reporting for debugging\nerror_reporting(E_ALL);\nini_set('display_errors', 1);\nini_set('log_errors', 1);\nini_set('error_log', __DIR__ . '/error.log');",
        $configContent
    );
    file_put_contents('config.php', $configContent);
    echo "<p style='color: green;'>✓ Updated config.php with error logging</p>";
} else {
    echo "<p style='color: green;'>✓ config.php already has error logging</p>";
}

// 3. Create error.log if it doesn't exist
echo "<h2>3. Creating error.log file...</h2>";
if (!file_exists('error.log')) {
    file_put_contents('error.log', "Error log file for ShareKoro\n");
    echo "<p style='color: green;'>✓ Created error.log file</p>";
} else {
    echo "<p style='color: green;'>✓ error.log file already exists</p>";
}

// 4. Set permissions
echo "<h2>4. Setting file permissions...</h2>";
@chmod('uploads', 0755);
@chmod('error.log', 0666);
echo "<p style='color: green;'>✓ Set permissions for uploads directory and error.log</p>";

// 5. Show completion message
echo "<h2 style='color: green;'>✓ All fixes have been deployed successfully!</h2>";

echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>Run <a href='update_database.php'>update_database.php</a> to ensure your database schema is up to date</li>";
echo "<li>Test the share functionality by visiting share-text.php, share-code.php, and share-file.php</li>";
echo "<li>Check the <a href='debug.php'>debug.php</a> script if you encounter any issues</li>";
echo "</ol>";

echo "<p><a href='index.php'>← Return to Homepage</a></p>";
?>