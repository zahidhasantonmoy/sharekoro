<?php
// view-collection.php - View a collection

require_once 'init.php';

$error = '';
$collection = null;
$shares = [];
$collection_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (empty($collection_id)) {
    $error = 'Invalid collection ID.';
} else {
    try {
        $db = new Database();
        $pdo = $db->getConnection();
        
        // Get collection details
        $stmt = $pdo->prepare("SELECT c.*, u.username FROM collections c JOIN users u ON c.created_by = u.id WHERE c.id = ?");
        $stmt->execute([$collection_id]);
        $collection = $stmt->fetch();
        
        if (!$collection) {
            $error = 'Collection not found.';
        } else {
            // Check if user can view this collection
            if (!$collection['is_public'] && (!isLoggedIn() || getCurrentUserId() != $collection['created_by'])) {
                $error = 'This collection is private and can only be viewed by its owner.';
            } else {
                // Get shares in this collection
                $stmt = $pdo->prepare("
                    SELECT s.*, u.username as creator_username 
                    FROM shares s 
                    JOIN collection_shares cs ON s.id = cs.share_id 
                    LEFT JOIN users u ON s.created_by = u.id 
                    WHERE cs.collection_id = ? 
                    AND (s.is_public = 1 OR s.created_by = ? OR ? = 1)
                    ORDER BY cs.added_at DESC
                ");
                $stmt->execute([$collection_id, getCurrentUserId(), isLoggedIn() && isAdmin() ? 1 : 0]);
                $shares = $stmt->fetchAll();
            }
        }
    } catch (PDOException $e) {
        $error = 'Failed to retrieve collection. Please try again.';
        error_log("View collection error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $collection ? htmlspecialchars($collection['name']) : 'Collection'; ?> - <?php echo SITE_NAME; ?></title>
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
        <?php if ($error): ?>
            <div class="auth-form glassmorphism">
                <h2><i class="fas fa-exclamation-circle"></i> Error</h2>
                <div class="alert alert-error"><?php echo $error; ?></div>
                <p class="text-center">
                    <a href="index.php" class="btn btn-primary"><i class="fas fa-home"></i> Back to Home</a>
                </p>
            </div>
        <?php elseif ($collection): ?>
            <div class="auth-form glassmorphism">
                <div class="share-header">
                    <h2><?php echo htmlspecialchars($collection['name']); ?></h2>
                    <div class="share-meta">
                        <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($collection['username']); ?></span>
                        <span><i class="fas fa-calendar"></i> <?php echo date('M j, Y', strtotime($collection['created_at'])); ?></span>
                        <span class="visibility-badge <?php echo $collection['is_public'] ? 'public' : 'private'; ?>">
                            <i class="fas fa-<?php echo $collection['is_public'] ? 'globe' : 'lock'; ?>"></i>
                            <?php echo $collection['is_public'] ? 'Public' : 'Private'; ?>
                        </span>
                    </div>
                    <?php if (!empty($collection['description'])): ?>
                        <p><?php echo htmlspecialchars($collection['description']); ?></p>
                    <?php endif; ?>
                </div>
                
                <?php if (count($shares) > 0): ?>
                    <div class="shares-list">
                        <?php foreach ($shares as $share): ?>
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
                                        <span>
                                            <i class="fas fa-<?php 
                                                echo $share['share_type'] === 'text' ? 'font' : 
                                                    ($share['share_type'] === 'code' ? 'code' : 'file'); 
                                            ?>"></i> 
                                            <?php echo ucfirst($share['share_type'] ?? ''); ?>
                                        </span>
                                        <?php if ($share['created_by']): ?>
                                            <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($share['creator_username']); ?></span>
                                        <?php else: ?>
                                            <span><i class="fas fa-user-secret"></i> Anonymous</span>
                                        <?php endif; ?>
                                        <span><i class="fas fa-calendar"></i> <?php echo date('M j, Y', strtotime($share['created_at'])); ?></span>
                                        <span><i class="fas fa-eye"></i> <?php echo $share['view_count']; ?> views</span>
                                    </p>
                                </div>
                                <div class="share-actions">
                                    <a href="view.php?key=<?php echo $share['share_key']; ?>" class="btn btn-secondary">
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
                    <div class="restricted-message">
                        <i class="fas fa-share-alt fa-3x"></i>
                        <h3>No Shares in Collection</h3>
                        <p>This collection is currently empty.</p>
                    </div>
                <?php endif; ?>
                
                <p class="text-center">
                    <a href="index.php"><i class="fas fa-arrow-left"></i> Back to Home</a>
                </p>
            </div>
        <?php endif; ?>
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
</body>
</html>