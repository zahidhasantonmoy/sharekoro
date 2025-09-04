<?php
// install.php - Installation script

// Check if already installed
if (file_exists('installed.lock')) {
    die('ShareKoro is already installed. To reinstall, delete the installed.lock file.');
}

echo "<h1>ShareKoro Installation</h1>";

// Check PHP version
if (version_compare(PHP_VERSION, '7.4.0') < 0) {
    die('ShareKoro requires PHP 7.4 or higher. Your version is ' . PHP_VERSION);
}

echo "<p>✓ PHP version " . PHP_VERSION . " is compatible</p>";

// Check required extensions
$required_extensions = ['pdo', 'pdo_mysql', 'session', 'fileinfo', 'json'];
$missing_extensions = [];

foreach ($required_extensions as $ext) {
    if (!extension_loaded($ext)) {
        $missing_extensions[] = $ext;
    }
}

if (!empty($missing_extensions)) {
    die('Missing required PHP extensions: ' . implode(', ', $missing_extensions));
}

echo "<p>✓ All required PHP extensions are loaded</p>";

// Check if config.php exists
if (!file_exists('config.php')) {
    die('config.php file not found. Please create it with your database configuration.');
}

// Include config
require_once 'config.php';

// Test database connection
try {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    echo "<p>✓ Database connection successful</p>";
    
    // Check if tables exist
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() == 0) {
        echo "<p>⚠ Database tables not found. Please import database_schema.sql</p>";
    } else {
        echo "<p>✓ Database tables found</p>";
    }
    
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Check uploads directory
if (!is_dir('uploads')) {
    if (!mkdir('uploads', 0755, true)) {
        die('Failed to create uploads directory. Please create it manually with write permissions.');
    }
    echo "<p>✓ Uploads directory created</p>";
} else {
    if (!is_writable('uploads')) {
        die('Uploads directory is not writable. Please set permissions to 755.');
    }
    echo "<p>✓ Uploads directory is writable</p>";
}

// Create .htaccess in uploads directory if it doesn't exist
if (!file_exists('uploads/.htaccess')) {
    $htaccessContent = "Options -Indexes
<Files \"*\">
  Order Allow,Deny
  Deny from all
</Files>
<FilesMatch \"\.(jpg|jpeg|png|gif|pdf|txt|doc|docx|zip)$\">
  Order Allow,Deny
  Allow from all
</FilesMatch>";
    if (file_put_contents('uploads/.htaccess', $htaccessContent)) {
        echo "<p>✓ Uploads .htaccess file created</p>";
    } else {
        echo "<p>⚠ Failed to create uploads .htaccess file</p>";
    }
}

// Create installed.lock file
if (file_put_contents('installed.lock', date('Y-m-d H:i:s'))) {
    echo "<p>✓ Installation completed successfully!</p>";
    echo "<p>A lock file (installed.lock) has been created to prevent reinstallation.</p>";
} else {
    echo "<p>⚠ Installation completed but failed to create lock file</p>";
}

echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>Import database_schema.sql if you haven't already</li>";
echo "<li>Test your installation by visiting the homepage</li>";
echo "<li>Delete this install.php file for security</li>";
echo "</ol>";

echo "<a href='index.php' class='btn'>Visit Your Site</a>";

// Simple CSS for the installation page
echo "
<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background: #f5f5f5;
}
h1, h2 {
    color: #333;
}
p {
    padding: 10px;
    margin: 10px 0;
    border-radius: 5px;
}
.btn {
    display: inline-block;
    padding: 10px 20px;
    background: #007cba;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    margin-top: 20px;
}
ol li {
    margin: 10px 0;
}
</style>
";
?>