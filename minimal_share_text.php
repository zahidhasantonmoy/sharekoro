<?php
// minimal_share_text.php - Minimal version of share-text.php for testing

echo "Starting minimal share text test...\n";

// Include init.php
echo "Including init.php...\n";
require_once 'init.php';
echo "init.php included successfully\n";

// Test generating CSRF token
echo "Generating CSRF token...\n";
$csrf_token = generateCSRFToken();
echo "CSRF token generated: " . substr($csrf_token, 0, 10) . "...\n";

// Test database connection
echo "Testing database connection...\n";
$db = new Database();
$pdo = $db->getConnection();
echo "Database connection successful\n";

echo "Minimal share text test completed successfully!\n";
?>