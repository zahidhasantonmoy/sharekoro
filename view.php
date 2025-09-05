<?php
// view.php - View shared content

require_once 'init.php';

$error = '';
$share = null;
$share_key = isset($_GET['key']) ? sanitizeInput($_GET['key']) : '';
$show_password_form = false;
$show_access_code_form = false;
$show_protected_content = false;

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
            // Handle visibility levels
            else {
                // Check visibility level
                switch ($share['visibility']) {
                    case 'private':
                        // Private shares require password
                        if (!isset($_POST['password'])) {
                            // Show password form
                            $show_password_form = true;
                        } else {
                            // Verify password
                            if (verifyPassword($_POST['password'], $share['access_password'])) {
                                $show_password_form = false;
                                $show_protected_content = true;
                            } else {
                                $error = 'Invalid password.';
                                $show_password_form = true;
                            }
                        }
                        break;
                        
                    case 'protected':
                        // Protected shares require access code
                        if (!isset($_POST['access_code'])) {
                            // Show access code form
                            $show_access_code_form = true;
                        } else {
                            // Verify access code
                            if ($_POST['access_code'] === $share['access_code']) {
                                $show_access_code_form = false;
                                $show_protected_content = true;
                            } else {
                                $error = 'Invalid access code.';
                                $show_access_code_form = true;
                            }
                        }
                        break;
                        
                    case 'public':
                    default:
                        // Public shares are always visible
                        $show_protected_content = true;
                        break;
                }
                
                // If no errors and not showing forms, increment view count
                if (empty($error) && !$show_password_form && !$show_access_code_form) {
                    $stmt = $pdo->prepare("UPDATE shares SET view_count = view_count + 1 WHERE id = ?");
                    $stmt->execute([$share['id']]);
                }
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
    <title>View Share - <?php echo SITE_NAME; ?></title>
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
        <?php if ($error): ?>
            <div class="auth-form glassmorphism">
                <h2><i class="fas fa-exclamation-circle"></i> Error</h2>
                <div class="alert alert-error"><?php echo $error; ?></div>
                <p class="text-center">
                    <a href="index.php" class="btn btn-primary"><i class="fas fa-home"></i> Back to Home</a>
                </p>
            </div>
        <?php elseif ($show_password_form): ?>
            <div class="auth-form glassmorphism">
                <h2><i class="fas fa-lock"></i> Password Protected Share</h2>
                <p>This private share is protected with a password. Enter the password to access the content.</p>
                
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
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-unlock"></i> Unlock Content
                    </button>
                </form>
                
                <p class="text-center">
                    <a href="index.php"><i class="fas fa-arrow-left"></i> Back to Home</a>
                </p>
            </div>
        <?php elseif ($show_access_code_form): ?>
            <div class="auth-form glassmorphism">
                <h2><i class="fas fa-shield-alt"></i> Protected Share</h2>
                <p>This protected share requires a 4-character access code. Enter the code to access the content.</p>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="access_code">Access Code</label>
                        <input type="text" id="access_code" name="access_code" maxlength="4" placeholder="XXXX" required style="text-transform: uppercase; letter-spacing: 8px; font-family: monospace; text-align: center; font-size: 1.5rem;">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-key"></i> Access Content
                    </button>
                </form>
                
                <p class="text-center">
                    <a href="index.php"><i class="fas fa-arrow-left"></i> Back to Home</a>
                </p>
            </div>
        <?php elseif ($share && $show_protected_content): ?>
            <div class="auth-form glassmorphism">
                <div class="share-header">
                    <h2><?php echo htmlspecialchars($share['title'] ?? 'Shared Content'); ?></h2>
                    <div class="share-meta">
                        <?php if ($share['created_by']): ?>
                            <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($share['username']); ?></span>
                        <?php endif; ?>
                        <span><i class="fas fa-calendar"></i> <?php echo date('M j, Y g:i A', strtotime($share['created_at'])); ?></span>
                        <span><i class="fas fa-eye"></i> <?php echo $share['view_count'] + 1; ?> views</span>
                        <span class="visibility-badge <?php echo $share['visibility']; ?>">
                            <i class="fas fa-<?php 
                                echo $share['visibility'] === 'public' ? 'globe' : 
                                     ($share['visibility'] === 'private' ? 'lock' : 'shield-alt'); 
                            ?>"></i> 
                            <?php echo ucfirst($share['visibility']); ?>
                        </span>
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
                        <div class="btn-group">
                            <a href="<?php echo htmlspecialchars($share['file_path']); ?>" class="btn btn-download" download>
                                <i class="fas fa-download"></i> Download File
                            </a>
                            <button class="btn btn-copy" onclick="copyToClipboard('<?php echo htmlspecialchars($share['file_path']); ?>')">
                                <i class="fas fa-copy"></i> Copy File URL
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="share-actions">
                    <button class="btn btn-copy" onclick="copyToClipboard(window.location.href)">
                        <i class="fas fa-copy"></i> Copy Link
                    </button>
                    <button class="btn btn-print" onclick="enhancedPrint()">
                        <i class="fas fa-print"></i> Print
                    </button>
                    <button class="btn btn-facebook" onclick="shareToFacebook()">
                        <i class="fab fa-facebook-f"></i> Facebook
                    </button>
                    <button class="btn btn-twitter" onclick="shareToTwitter()">
                        <i class="fab fa-twitter"></i> Twitter
                    </button>
                    <button class="btn btn-linkedin" onclick="shareToLinkedIn()">
                        <i class="fab fa-linkedin-in"></i> LinkedIn
                    </button>
                    <button class="btn btn-report" onclick="document.getElementById('report-modal').style.display='block'">
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
                    <h2><i class="fas fa-flag"></i> Report Share</h2>
                    <form method="POST" action="report.php">
                        <input type="hidden" name="share_id" value="<?php echo $share['id']; ?>">
                        <div class="form-group">
                            <label for="reason">Reason for reporting:</label>
                            <textarea id="reason" name="reason" rows="5" required placeholder="Please describe why you are reporting this content..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Submit Report
                        </button>
                    </form>
                </div>
            </div>
        <?php elseif ($share && !$show_protected_content): ?>
            <div class="auth-form glassmorphism">
                <div class="share-header">
                    <h2><?php echo htmlspecialchars($share['title'] ?? 'Shared Content'); ?></h2>
                    <div class="share-meta">
                        <?php if ($share['created_by']): ?>
                            <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($share['username']); ?></span>
                        <?php endif; ?>
                        <span><i class="fas fa-calendar"></i> <?php echo date('M j, Y g:i A', strtotime($share['created_at'])); ?></span>
                        <span><i class="fas fa-eye"></i> <?php echo $share['view_count'] + 1; ?> views</span>
                        <span class="visibility-badge <?php echo $share['visibility']; ?>">
                            <i class="fas fa-<?php 
                                echo $share['visibility'] === 'public' ? 'globe' : 
                                     ($share['visibility'] === 'private' ? 'lock' : 'shield-alt'); 
                            ?>"></i> 
                            <?php echo ucfirst($share['visibility']); ?>
                        </span>
                    </div>
                </div>
                
                <div class="share-content restricted">
                    <div class="restricted-message">
                        <i class="fas fa-<?php 
                            echo $share['visibility'] === 'private' ? 'lock' : 'shield-alt'; 
                        ?> fa-3x"></i>
                        <h3>Restricted Content</h3>
                        <p>This <?php echo $share['visibility']; ?> share requires <?php 
                            echo $share['visibility'] === 'private' ? 'a password' : 'a 4-character access code'; 
                        ?> to view.</p>
                        <p>To access this content, you need to <?php 
                            echo $share['visibility'] === 'private' ? 'enter the password' : 'enter the access code'; 
                        ?> provided by the creator.</p>
                        <div class="btn-group">
                            <a href="index.php" class="btn btn-primary">
                                <i class="fas fa-home"></i> Back to Home
                            </a>
                            <button class="btn btn-outline" onclick="window.location.reload()">
                                <i class="fas fa-redo"></i> Refresh Page
                            </button>
                        </div>
                    </div>
                </div>
                
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
    <script>
        // Social sharing functions
        function shareToFacebook() {
            const url = encodeURIComponent(window.location.href);
            const title = encodeURIComponent(document.title);
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}&t=${title}`, '_blank', 'width=600,height=400');
        }
        
        function shareToTwitter() {
            const url = encodeURIComponent(window.location.href);
            const text = encodeURIComponent(document.title);
            window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank', 'width=600,height=400');
        }
        
        function shareToLinkedIn() {
            const url = encodeURIComponent(window.location.href);
            const title = encodeURIComponent(document.title);
            window.open(`https://www.linkedin.com/shareArticle?mini=true&url=${url}&title=${title}`, '_blank', 'width=600,height=400');
        }
        
        // Enhanced print function
        function enhancedPrint() {
            // Add print-specific styles
            const printStyle = document.createElement('style');
            printStyle.innerHTML = `
                @media print {
                    .header, .nav, .footer, .share-actions, .btn, .visibility-badge {
                        display: none !important;
                    }
                    .share-content {
                        border: none !important;
                        box-shadow: none !important;
                        background: white !important;
                        color: black !important;
                    }
                    body {
                        background: white !important;
                        color: black !important;
                    }
                    .auth-form {
                        box-shadow: none !important;
                        border: none !important;
                    }
                }
            `;
            document.head.appendChild(printStyle);
            
            // Print after a short delay to ensure styles are applied
            setTimeout(() => {
                window.print();
                // Remove print styles after printing
                setTimeout(() => {
                    document.head.removeChild(printStyle);
                }, 1000);
            }, 100);
        }
    </script>
</body>
</html>