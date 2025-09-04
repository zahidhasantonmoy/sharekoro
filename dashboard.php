<?php
// dashboard.php - User dashboard

require_once 'init.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

$error = '';
$shares = [];

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    // Get user's shares
    $stmt = $pdo->prepare("SELECT * FROM shares WHERE created_by = ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $shares = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = 'Failed to retrieve your shares. Please try again.';
    error_log("Dashboard error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="logo">
                <h1><?php echo SITE_NAME; ?></h1>
            </div>
            <nav class="nav">
                <span>Welcome, <?php echo $_SESSION['username']; ?>!</span>
                <a href="dashboard.php" class="btn btn-secondary">Dashboard</a>
                <?php if (isAdmin()): ?>
                    <a href="admin/" class="btn btn-warning">Admin Panel</a>
                <?php endif; ?>
                <a href="logout.php" class="btn btn-outline">Logout</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container">
        <div class="auth-form glassmorphism">
            <h2>Your Dashboard</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="dashboard-stats">
                <div class="stat-card glassmorphism">
                    <h3><?php echo count($shares); ?></h3>
                    <p>Total Shares</p>
                </div>
                <div class="stat-card glassmorphism">
                    <h3>
                        <?php 
                        $total_views = array_sum(array_column($shares, 'view_count'));
                        echo $total_views;
                        ?>
                    </h3>
                    <p>Total Views</p>
                </div>
            </div>
            
            <h3>Your Shares</h3>
            
            <?php if (count($shares) > 0): ?>
                <div class="shares-list">
                    <?php foreach ($shares as $share): ?>
                        <div class="share-item glassmorphism">
                            <div class="share-info">
                                <h4><?php echo htmlspecialchars($share['title'] ?? 'Untitled'); ?></h4>
                                <p class="share-meta">
                                    <span><i class="fas fa-<?php 
                                        echo $share['share_type'] === 'text' ? 'font' : 
                                            ($share['share_type'] === 'code' ? 'code' : 'file'); 
                                    ?>"></i> <?php echo ucfirst($share['share_type']); ?></span>
                                    <span><i class="fas fa-calendar"></i> <?php echo date('M j, Y', strtotime($share['created_at'])); ?></span>
                                    <span><i class="fas fa-eye"></i> <?php echo $share['view_count']; ?> views</span>
                                </p>
                            </div>
                            <div class="share-actions">
                                <a href="view.php?key=<?php echo $share['share_key']; ?>" class="btn btn-secondary" target="_blank">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <button class="btn btn-outline" onclick="copyToClipboard('<?php echo SITE_URL; ?>/view.php?key=<?php echo $share['share_key']; ?>')">
                                    <i class="fas fa-copy"></i> Copy Link
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>You haven't shared anything yet.</p>
                <p class="text-center">
                    <a href="share-text.php" class="btn btn-primary">Share Text</a>
                    <a href="share-code.php" class="btn btn-primary">Share Code</a>
                    <a href="share-file.php" class="btn btn-primary">Share File</a>
                </p>
            <?php endif; ?>
            
            <p class="text-center">
                <a href="index.php"><i class="fas fa-arrow-left"></i> Back to Home</a>
            </p>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><?php echo SITE_NAME; ?></h3>
                    <p>Share anything securely and anonymously</p>
                </div>
                
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="share-text.php">Share Text</a></li>
                        <li><a href="share-code.php">Share Code</a></li>
                        <li><a href="share-file.php">Share File</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Developer</h4>
                    <p>Zahid Hasan Tonmoy</p>
                    <div class="social-links">
                        <a href="https://www.facebook.com/zahidhasantonmoybd" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://www.linkedin.com/in/zahidhasantonmoy/" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                        <a href="https://github.com/zahidhasantonmoy" target="_blank"><i class="fab fa-github"></i></a>
                        <a href="https://zahidhasantonmoy.vercel.app" target="_blank"><i class="fas fa-globe"></i></a>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>