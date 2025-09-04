<?php
// index-session-fix-test.php - Test index with session fix

// Load the fixed init
require_once 'init.php';

// Simple HTML output
?>
<!DOCTYPE html>
<html>
<head>
    <title>Session Fix Test</title>
</head>
<body>
    <h1>Session Fix Test</h1>
    <p>If you can see this without warnings, the session fix is working!</p>
    
    <?php if (defined('SITE_NAME')): ?>
        <p>Site: <?php echo htmlspecialchars(SITE_NAME); ?></p>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['session_fix_test'])): ?>
        <p>Session Test Value: <?php echo htmlspecialchars($_SESSION['session_fix_test']); ?></p>
    <?php endif; ?>
    
    <p>Time: <?php echo date('Y-m-d H:i:s'); ?></p>
</body>
</html>