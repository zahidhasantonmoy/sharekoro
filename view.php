<?php
// view.php - View shared content

require_once 'init.php';

$error = '';
$share = null;
$share_key = isset($_GET['key']) ? sanitizeInput($_GET['key']) : '';

if (empty($share_key)) {
    $error = 'Invalid share key.';
} else {
    try {
        $db = new Database();
        $pdo = $db->getConnection();
        
        // Get share by key
        $stmt = $pdo->prepare("SELECT s.*, u.username FROM shares s LEFT JOIN users u ON s.created_by = u.id WHERE s.share_key = ?");
        $stmt->execute([$share_key]);
        $share = $stmt->fetch();
        
        if (!$share) {
            $error = 'Share not found.';
        } else {
            // Check if share has expired
            if ($share['expiration_date'] && new DateTime() > new DateTime($share['expiration_date'])) {
                $error = 'This share has expired.';
            } 
            // Check if share is password protected
            else if (!empty($share['password_protect'])) {
                if (!isset($_POST['password'])) {
                    // Show password form
                    $show_password_form = true;
                } else {
                    // Verify password
                    if (verifyPassword($_POST['password'], $share['password_protect'])) {
                        $show_password_form = false;
                    } else {
                        $error = 'Invalid password.';
                        $show_password_form = true;
                    }
                }
            } else {
                $show_password_form = false;
            }
            
            // If no errors and not showing password form, increment view count
            if (empty($error) && !$show_password_form) {
                $stmt = $pdo->prepare("UPDATE shares SET view_count = view_count + 1 WHERE id = ?");
                $stmt->execute([$share['id']]);
            }
        }
    } catch (PDOException $e) {
        $error = 'Failed to retrieve share. Please try again.';
        error_log("View share error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo !empty($share) ? htmlspecialchars($share['title'] ?? 'Shared Content') : 'View Share'; ?> - <?php echo SITE_NAME; ?></title>
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
                    <a href="dashboard.php" class="btn btn-secondary">Dashboard</a>
                    <?php if (isAdmin()): ?>
                        <a href="admin/" class="btn btn-warning">Admin Panel</a>
                    <?php endif; ?>
                    <a href="logout.php" class="btn btn-outline">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-secondary">Login</a>
                    <a href="register.php" class="btn btn-primary">Register</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container">
        <?php if ($error): ?>
            <div class="auth-form glassmorphism">
                <h2>Error</h2>
                <div class="alert alert-error"><?php echo $error; ?></div>
                <p class="text-center">
                    <a href="index.php" class="btn btn-primary"><i class="fas fa-home"></i> Back to Home</a>
                </p>
            </div>
        <?php elseif (isset($show_password_form) && $show_password_form): ?>
            <div class="auth-form glassmorphism">
                <h2>Password Protected Share</h2>
                <p>This share is protected with a password.</p>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-group">
                            <input type="password" id="password" name="password" required>
                            <span class="input-group-addon" onclick="togglePasswordVisibility('password', 'password_icon')">
                                <i class="fas fa-eye" id="password_icon"></i>
                            </span>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Unlock</button>
                </form>
                
                <p class="text-center">
                    <a href="index.php"><i class="fas fa-arrow-left"></i> Back to Home</a>
                </p>
            </div>
        <?php elseif ($share): ?>
            <div class="auth-form glassmorphism">
                <div class="share-header">
                    <h2><?php echo htmlspecialchars($share['title'] ?? 'Shared Content'); ?></h2>
                    <div class="share-meta">
                        <?php if ($share['created_by']): ?>
                            <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($share['username']); ?></span>
                        <?php endif; ?>
                        <span><i class="fas fa-calendar"></i> <?php echo date('M j, Y g:i A', strtotime($share['created_at'])); ?></span>
                        <span><i class="fas fa-eye"></i> <?php echo $share['view_count'] + 1; ?> views</span>
                    </div>
                </div>
                
                <?php if ($share['share_type'] === 'text'): ?>
                    <div class="share-content">
                        <pre><?php echo htmlspecialchars($share['content']); ?></pre>
                    </div>
                <?php elseif ($share['share_type'] === 'code'): ?>
                    <div class="share-content">
                        <pre><code><?php echo htmlspecialchars($share['content']); ?></code></pre>
                    </div>
                <?php elseif ($share['share_type'] === 'file'): ?>
                    <div class="share-content file-share">
                        <p><i class="fas fa-file"></i> <?php echo htmlspecialchars($share['file_name']); ?></p>
                        <p>Size: <?php echo formatFileSize($share['file_size']); ?></p>
                        <a href="<?php echo htmlspecialchars($share['file_path']); ?>" class="btn btn-primary" download>
                            <i class="fas fa-download"></i> Download File
                        </a>
                    </div>
                <?php endif; ?>
                
                <div class="share-actions">
                    <button class="btn btn-secondary" onclick="copyToClipboard(window.location.href)">
                        <i class="fas fa-copy"></i> Copy Link
                    </button>
                    <button class="btn btn-secondary" onclick="window.print()">
                        <i class="fas fa-print"></i> Print
                    </button>
                    <button class="btn btn-outline" onclick="document.getElementById('report-modal').style.display='block'">
                        <i class="fas fa-flag"></i> Report
                    </button>
                </div>
                
                <p class="text-center">
                    <a href="index.php"><i class="fas fa-arrow-left"></i> Back to Home</a>
                </p>
            </div>
            
            <!-- Report Modal -->
            <div id="report-modal" class="modal">
                <div class="modal-content glassmorphism">
                    <span class="close" onclick="document.getElementById('report-modal').style.display='none'">&times;</span>
                    <h2>Report Share</h2>
                    <form method="POST" action="report.php">
                        <input type="hidden" name="share_id" value="<?php echo $share['id']; ?>">
                        <div class="form-group">
                            <label for="reason">Reason for reporting:</label>
                            <textarea id="reason" name="reason" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Report</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
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