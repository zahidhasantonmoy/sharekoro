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
    
    // Get additional statistics
    $total_shares = count($shares);
    $total_views = array_sum(array_column($shares, 'view_count'));
    
    // Get shares by type
    $text_shares = 0;
    $code_shares = 0;
    $file_shares = 0;
    
    foreach ($shares as $share) {
        switch ($share['share_type']) {
            case 'text':
                $text_shares++;
                break;
            case 'code':
                $code_shares++;
                break;
            case 'file':
                $file_shares++;
                break;
        }
    }
    
    // Get recent shares (last 7 days)
    $recent_shares = 0;
    $one_week_ago = new DateTime('-7 days');
    foreach ($shares as $share) {
        $share_date = new DateTime($share['created_at']);
        if ($share_date >= $one_week_ago) {
            $recent_shares++;
        }
    }
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
    <link rel="stylesheet" href="assets/css/modern-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=SF+Pro+Display:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="logo">
                <h1><a href="index.php" style="text-decoration: none; color: inherit;"><?php echo SITE_NAME; ?></a></h1>
            </div>
            <nav class="nav">
                <a href="index.php" class="btn btn-outline" title="Home">
                    <i class="fas fa-home"></i> Home
                </a>
                                <?php if (isLoggedIn()): ?>
                    <span>Welcome, <?php echo $_SESSION['username']; ?>!</span>
                    <a href="dashboard.php" class="btn btn-dashboard">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <?php if (isAdmin()): ?>
                        <a href="admin/" class="btn btn-admin">
                            <i class="fas fa-cog"></i> Admin Panel
                        </a>
                    <?php endif; ?>
                    <form method="POST" action="logout.php" style="display: inline;">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <button type="submit" class="btn btn-logout">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                <?php else: ?>
                    <a href="login.php" class="btn btn-secondary">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                    <a href="register.php" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Register
                    </a>
                <?php endif; ?>
                <button id="theme-toggle" class="btn btn-outline" title="Toggle theme">
                    <i class="fas fa-moon"></i>
                </button>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container">
        <div class="auth-form glassmorphism">
            <h2>Your Dashboard</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <!-- Enhanced Dashboard Stats -->
            <div class="dashboard-stats">
                <div class="stat-card glassmorphism">
                    <h3><?php echo $total_shares; ?></h3>
                    <p>Total Shares</p>
                </div>
                <div class="stat-card glassmorphism">
                    <h3><?php echo $total_views; ?></h3>
                    <p>Total Views</p>
                </div>
                <div class="stat-card glassmorphism">
                    <h3><?php echo $recent_shares; ?></h3>
                    <p>Shares This Week</p>
                </div>
            </div>
            
            <!-- Share Type Stats -->
            <div class="dashboard-stats">
                <div class="stat-card glassmorphism">
                    <h3><?php echo $text_shares; ?></h3>
                    <p>Text Shares</p>
                </div>
                <div class="stat-card glassmorphism">
                    <h3><?php echo $code_shares; ?></h3>
                    <p>Code Shares</p>
                </div>
                <div class="stat-card glassmorphism">
                    <h3><?php echo $file_shares; ?></h3>
                    <p>File Shares</p>
                </div>
            </div>
            
            <h3>Your Recent Shares</h3>
            
            <?php if (count($shares) > 0): ?>
                <div class="shares-list">
                    <?php 
                    // Limit to 10 most recent shares
                    $recent_shares_list = array_slice($shares, 0, 10);
                    foreach ($recent_shares_list as $share): ?>
                        <div class="share-item glassmorphism">
                            <div class="share-info">
                                <h4><?php echo htmlspecialchars($share['title'] ?? 'Untitled'); ?></h4>
                                <p class="share-meta">
                                    <span class="visibility-badge <?php echo $share['visibility']; ?>">
                                        <i class="fas fa-<?php 
                                            echo $share['visibility'] === 'public' ? 'globe' : 
                                                 ($share['visibility'] === 'private' ? 'lock' : 'shield-alt'); 
                                        ?>"></i> 
                                        <?php echo ucfirst($share['visibility'] ?? ''); ?>
                                    </span>
                                    <span><i class="fas fa-<?php 
                                        echo $share['share_type'] === 'text' ? 'font' : 
                                            ($share['share_type'] === 'code' ? 'code' : 'file'); 
                                    ?>"></i> <?php echo ucfirst($share['share_type'] ?? ''); ?></span>
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
                                <button class="btn btn-info" onclick="shareToSocial('<?php echo SITE_URL; ?>/view.php?key=<?php echo $share['share_key']; ?>')">
                                    <i class="fas fa-share-alt"></i> Share
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if (count($shares) > 10): ?>
                    <p class="text-center">
                        <a href="#" class="btn btn-primary" id="load-more-shares">
                            <i class="fas fa-plus-circle"></i> Load More Shares
                        </a>
                    </p>
                <?php endif; ?>
            <?php else: ?>
                <div class="restricted-message">
                    <i class="fas fa-share-alt fa-3x"></i>
                    <h3>You Haven't Shared Anything Yet</h3>
                    <p>Start sharing text, code, or files anonymously with our platform.</p>
                </div>
                <div class="btn-group">
                    <a href="share-text.php" class="btn btn-primary">
                        <i class="fas fa-font"></i> Share Text
                    </a>
                    <a href="share-code.php" class="btn btn-primary">
                        <i class="fas fa-code"></i> Share Code
                    </a>
                    <a href="share-file.php" class="btn btn-primary">
                        <i class="fas fa-file-upload"></i> Share File
                    </a>
                </div>
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
                    <p>Share anything securely and anonymously with our modern platform designed for privacy and ease of use.</p>
                    <div class="social-links">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(SITE_URL); ?>" target="_blank" class="btn-facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(SITE_URL); ?>&text=Check out <?php echo SITE_NAME; ?> for anonymous sharing" target="_blank" class="btn-twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode(SITE_URL); ?>" target="_blank" class="btn-linkedin">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="index.php"><i class="fas fa-chevron-right"></i> Home</a></li>
                        <li><a href="latest.php"><i class="fas fa-chevron-right"></i> Latest Shares</a></li>
                        <li><a href="shares.php"><i class="fas fa-chevron-right"></i> Public Shares</a></li>
                        <li><a href="share-text.php"><i class="fas fa-chevron-right"></i> Share Text</a></li>
                        <li><a href="share-code.php"><i class="fas fa-chevron-right"></i> Share Code</a></li>
                        <li><a href="share-file.php"><i class="fas fa-chevron-right"></i> Share File</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Developer</h4>
                    <p>Zahid Hasan Tonmoy</p>
                    <ul>
                        <li><a href="https://www.facebook.com/zahidhasantonmoybd" target="_blank"><i class="fab fa-facebook-f"></i> Facebook</a></li>
                        <li><a href="https://www.linkedin.com/in/zahidhasantonmoy/" target="_blank"><i class="fab fa-linkedin-in"></i> LinkedIn</a></li>
                        <li><a href="https://github.com/zahidhasantonmoy" target="_blank"><i class="fab fa-github"></i> GitHub</a></li>
                        <li><a href="https://zahidhasantonmoy.vercel.app" target="_blank"><i class="fas fa-globe"></i> Portfolio</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved. | Designed with <i class="fas fa-heart" style="color: #ff6584;"></i> for privacy</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/modern-main.js"></script>
    <script>
        // Social sharing function
        function shareToSocial(url) {
            if (navigator.share) {
                navigator.share({
                    title: '<?php echo SITE_NAME; ?> Share',
                    url: url
                }).catch(console.error);
            } else {
                copyToClipboard(url);
            }
        }
    </script>
</body>
</html>