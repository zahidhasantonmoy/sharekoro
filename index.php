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

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content glassmorphism">
                <h2 class="hero-title">Share Anything, Anonymously</h2>
                <p class="hero-subtitle">Text, code, files - share securely without registration</p>
                
                <div class="share-options">
                    <a href="share-text.php" class="share-option">
                        <i class="fas fa-font fa-2x"></i>
                        <span>Share Text</span>
                    </a>
                    <a href="share-code.php" class="share-option">
                        <i class="fas fa-code fa-2x"></i>
                        <span>Share Code</span>
                    </a>
                    <a href="share-file.php" class="share-option">
                        <i class="fas fa-file-upload fa-2x"></i>
                        <span>Share File</span>
                    </a>
                </div>
                
                <p class="hero-info">
                    <i class="fas fa-lock"></i> Secure & Private | 
                    <i class="fas fa-bolt"></i> Fast & Easy | 
                    <i class="fas fa-infinity"></i> No Registration Required
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
                    <p>Share content without creating an account. Your privacy is our priority.</p>
                </div>
                
                <div class="feature-card glassmorphism">
                    <i class="fas fa-file-code feature-icon"></i>
                    <h3>Code Highlighting</h3>
                    <p>Share code snippets with beautiful syntax highlighting for 200+ languages.</p>
                </div>
                
                <div class="feature-card glassmorphism">
                    <i class="fas fa-shield-alt feature-icon"></i>
                    <h3>Secure & Encrypted</h3>
                    <p>All data is securely stored and encrypted for maximum protection.</p>
                </div>
                
                <div class="feature-card glassmorphism">
                    <i class="fas fa-clock feature-icon"></i>
                    <h3>Expiration Control</h3>
                    <p>Set your own expiration time from 1 hour to forever.</p>
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