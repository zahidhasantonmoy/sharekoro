<?php
// shares.php - Display all public shares

require_once 'init.php';

$error = '';
$shares = [];
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page);
$limit = 10;
$offset = ($page - 1) * $limit;

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    // Get total count of public shares
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM shares WHERE is_public = 1 AND (expiration_date IS NULL OR expiration_date > NOW())");
    $stmt->execute();
    $total = $stmt->fetch()['count'];
    
    // Get public shares with pagination
    $stmt = $pdo->prepare("SELECT s.*, u.username FROM shares s LEFT JOIN users u ON s.created_by = u.id WHERE s.is_public = 1 AND (s.expiration_date IS NULL OR s.expiration_date > NOW()) ORDER BY s.created_at DESC LIMIT ? OFFSET ?");
    $stmt->execute([$limit, $offset]);
    $shares = $stmt->fetchAll();
    
    $totalPages = ceil($total / $limit);
} catch (PDOException $e) {
    $error = 'Failed to retrieve shares. Please try again.';
    error_log("Shares page error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Public Shares - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                    <i class="fas fa-home"></i>
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
                    <a href="logout.php" class="btn btn-logout">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-secondary">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                    <a href="register.php" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Register
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container">
        <div class="auth-form glassmorphism">
            <h2>Public Shares</h2>
            <p>Browse recently shared content from the community</p>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (count($shares) > 0): ?>
                <div class="shares-list">
                    <?php foreach ($shares as $share): ?>
                        <div class="share-item glassmorphism">
                            <div class="share-info">
                                <h4><?php echo htmlspecialchars($share['title'] ?? 'Untitled'); ?></h4>
                                <p class="share-meta">
                                    <span>
                                        <i class="fas fa-<?php 
                                            echo $share['share_type'] === 'text' ? 'font' : 
                                                ($share['share_type'] === 'code' ? 'code' : 'file'); 
                                        ?>"></i> 
                                        <?php echo ucfirst($share['share_type']); ?>
                                    </span>
                                    <?php if ($share['created_by']): ?>
                                        <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($share['username']); ?></span>
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
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>" class="btn btn-outline">
                                <i class="fas fa-arrow-left"></i> Previous
                            </a>
                        <?php endif; ?>
                        
                        <span class="pagination-info">
                            Page <?php echo $page; ?> of <?php echo $totalPages; ?>
                        </span>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $page + 1; ?>" class="btn btn-outline">
                                Next <i class="fas fa-arrow-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-info">
                    <p>No public shares found.</p>
                    <p class="text-center">
                        <a href="share-text.php" class="btn btn-primary">Share Text</a>
                        <a href="share-code.php" class="btn btn-primary">Share Code</a>
                        <a href="share-file.php" class="btn btn-primary">Share File</a>
                    </p>
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
                    <p>Share anything securely and anonymously</p>
                </div>
                
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="latest.php">Latest Shares</a></li>
                        <li><a href="shares.php">Public Shares</a></li>
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