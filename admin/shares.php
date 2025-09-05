<?php
// admin/shares.php - Admin shares management

require_once 'auth.php';

$error = '';
$shares = [];

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    // Handle share actions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = sanitizeInput($_POST['action']);
        $share_id = (int)$_POST['share_id'];
        
        if ($action === 'delete') {
            // Get share details first
            $stmt = $pdo->prepare("SELECT file_path FROM shares WHERE id = ?");
            $stmt->execute([$share_id]);
            $share = $stmt->fetch();
            
            // Delete share
            $stmt = $pdo->prepare("DELETE FROM shares WHERE id = ?");
            $stmt->execute([$share_id]);
            
            // Delete file if it exists
            if ($share && !empty($share['file_path']) && file_exists($share['file_path'])) {
                unlink($share['file_path']);
            }
            
            $success = 'Share deleted successfully.';
        }
    }
    
    // Get all shares
    $stmt = $pdo->prepare("SELECT s.*, u.username FROM shares s LEFT JOIN users u ON s.created_by = u.id ORDER BY s.created_at DESC");
    $stmt->execute();
    $shares = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = 'Failed to retrieve shares. Please try again.';
    error_log("Admin shares error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Shares - <?php echo SITE_NAME; ?> Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="logo">
                <h1><a href="../index.php" style="text-decoration: none; color: inherit;"><?php echo SITE_NAME; ?> Admin</a></h1>
            </div>
            <nav class="nav">
                <a href="../index.php" class="btn btn-outline" title="Home">
                    <i class="fas fa-home"></i>
                </a>
                <span>Welcome, <?php echo $_SESSION['username']; ?>!</span>
                <a href="index.php" class="btn btn-dashboard">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="users.php" class="btn btn-secondary">
                    <i class="fas fa-users"></i> Users
                </a>
                <a href="shares.php" class="btn btn-secondary">
                    <i class="fas fa-share-alt"></i> Shares
                </a>
                <a href="reports.php" class="btn btn-secondary">
                    <i class="fas fa-flag"></i> Reports
                </a>
                <a href="../logout.php" class="btn btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container">
        <div class="auth-form glassmorphism">
            <h2>Manage Shares</h2>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (count($shares) > 0): ?>
                <div class="admin-list">
                    <?php foreach ($shares as $share): ?>
                        <div class="admin-item glassmorphism">
                            <div class="admin-item-info">
                                <h4><?php echo htmlspecialchars($share['title'] ?? 'Untitled'); ?></h4>
                                <p>
                                    <?php if ($share['created_by']): ?>
                                        by <?php echo htmlspecialchars($share['username']); ?>
                                    <?php else: ?>
                                        Anonymous
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="admin-item-meta">
                                <span class="type <?php echo $share['share_type']; ?>"><?php echo ucfirst($share['share_type'] ?? ''); ?></span>
                                <span class="visibility-badge <?php echo $share['visibility']; ?>">
                                    <i class="fas fa-<?php 
                                        echo $share['visibility'] === 'public' ? 'globe' : 
                                             ($share['visibility'] === 'private' ? 'lock' : 'shield-alt'); 
                                    ?>"></i> 
                                    <?php echo ucfirst($share['visibility'] ?? ''); ?>
                                </span>
                                <span><?php echo date('M j, Y', strtotime($share['created_at'])); ?></span>
                                <span><i class="fas fa-eye"></i> <?php echo $share['view_count']; ?></span>
                            </div>
                            <div class="admin-item-actions">
                                <a href="../view.php?key=<?php echo $share['share_key']; ?>" class="btn btn-secondary" target="_blank">View</a>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="share_id" value="<?php echo $share['id']; ?>">
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this share?')">Delete</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>No shares found.</p>
            <?php endif; ?>
            
            <p class="text-center">
                <a href="index.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
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
                        <li><a href="../index.php">Home</a></li>
                        <li><a href="../latest.php">Latest Shares</a></li>
                        <li><a href="../shares.php">Public Shares</a></li>
                        <li><a href="../share-text.php">Share Text</a></li>
                        <li><a href="../share-code.php">Share Code</a></li>
                        <li><a href="../share-file.php">Share File</a></li>
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

    <script src="../assets/js/main.js"></script>
</body>
</html>