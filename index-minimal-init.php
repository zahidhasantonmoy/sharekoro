<?php
// index-minimal-init.php - Index with minimal init

require_once 'init-minimal.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Minimal Init Test</title>
</head>
<body>
    <h1>Minimal Init Test</h1>
    <p>If you can see this, the minimal init is working.</p>
    
    <?php if (defined('SITE_NAME')): ?>
        <p>Site: <?php echo htmlspecialchars(SITE_NAME); ?></p>
    <?php endif; ?>
    
    <p>Time: <?php echo date('Y-m-d H:i:s'); ?></p>
</body>
</html>