<?php
// index-minimal.php - Minimal index file for testing

require_once 'init.php';

// Simple HTML output
?>
<!DOCTYPE html>
<html>
<head>
    <title>Minimal Test</title>
</head>
<body>
    <h1>Minimal Index Test</h1>
    <p>If you can see this, the basic index file works.</p>
    
    <?php if (defined('SITE_NAME')): ?>
        <p>Site Name: <?php echo SITE_NAME; ?></p>
    <?php else: ?>
        <p>Site Name: Not defined</p>
    <?php endif; ?>
    
    <p>Current Time: <?php echo date('Y-m-d H:i:s'); ?></p>
</body>
</html>