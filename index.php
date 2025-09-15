<?php
// index.php - Main homepage

require_once 'init.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Share Anything, Anonymously</title>
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
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content glassmorphism">
                <h2 class="hero-title">Share Anything, Anonymously</h2>
                <p class="hero-subtitle">Text, code, files - share securely without registration. Experience the future of anonymous sharing with our iOS-inspired glassmorphism design.</p>
                
                <div class="share-options">
                    <a href="share-text.php" class="share-option">
                        <i class="fas fa-font"></i>
                        <span>Share Text</span>
                    </a>
                    <a href="share-code.php" class="share-option">
                        <i class="fas fa-code"></i>
                        <span>Share Code</span>
                    </a>
                    <a href="share-file.php" class="share-option">
                        <i class="fas fa-file-upload"></i>
                        <span>Share File</span>
                    </a>
                </div>
                
                <div class="btn-group">
                    <a href="latest.php" class="btn btn-info">
                        <i class="fas fa-history"></i> View Latest Shares
                    </a>
                    <a href="shares.php" class="btn btn-success">
                        <i class="fas fa-globe"></i> Browse Public Shares
                    </a>
                </div>
                
                <p class="hero-info">
                    <span><i class="fas fa-lock"></i> Secure & Private</span>
                    <span><i class="fas fa-bolt"></i> Fast & Easy</span>
                    <span><i class="fas fa-infinity"></i> No Registration Required</span>
                    <span><i class="fas fa-mobile-alt"></i> Mobile Friendly</span>
                </p>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <h2 class="section-title">Why Choose <?php echo SITE_NAME; ?>?</h2>
            <div class="features-grid">
                <div class="feature-card glassmorphism">
                    <i class="fas fa-user-secret feature-icon"></i>
                    <h3>Anonymous Sharing</h3>
                    <p>Share content without creating an account. Your privacy is our priority with end-to-end encryption.</p>
                </div>
                
                <div class="feature-card glassmorphism">
                    <i class="fas fa-file-code feature-icon"></i>
                    <h3>Code Highlighting</h3>
                    <p>Share code snippets with beautiful syntax highlighting for 200+ programming languages.</p>
                </div>
                
                <div class="feature-card glassmorphism">
                    <i class="fas fa-shield-alt feature-icon"></i>
                    <h3>Secure & Encrypted</h3>
                    <p>All data is securely stored and encrypted for maximum protection with military-grade encryption.</p>
                </div>
                
                <div class="feature-card glassmorphism">
                    <i class="fas fa-clock feature-icon"></i>
                    <h3>Expiration Control</h3>
                    <p>Set your own expiration time from 1 hour to forever. Control how long your shares remain accessible.</p>
                </div>
                
                <div class="feature-card glassmorphism">
                    <i class="fas fa-mobile-alt feature-icon"></i>
                    <h3>Fully Responsive</h3>
                    <p>Seamlessly works on all devices from smartphones to desktops with our modern responsive design.</p>
                </div>
                
                <div class="feature-card glassmorphism">
                    <i class="fas fa-share-alt feature-icon"></i>
                    <h3>Social Sharing</h3>
                    <p>Easily share your content on Facebook, Twitter, and LinkedIn with one click.</p>
                </div>
            </div>
        </div>
    </section>

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