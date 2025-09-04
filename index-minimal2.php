<?php
// index-minimal2.php - Even more minimal index test

require_once 'init.php';

// Very basic output
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Minimal Test 2</title>
</head>
<body>
    <h1>Minimal Index Test 2</h1>
    <p>This is a very minimal test to see if the basic index works.</p>
    
    <?php if (defined('SITE_NAME')): ?>
        <p>Site: <?php echo htmlspecialchars(SITE_NAME); ?></p>
    <?php endif; ?>
    
    <p>Time: <?php echo date('Y-m-d H:i:s'); ?></p>
</body>
</html>