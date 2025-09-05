<?php
// login.php - User login page

require_once 'init.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];

    // Validation
    if (empty($email) || empty($password)) {
        $error = 'All fields are required.';
    } else {
        try {
            $db = new Database();
            $pdo = $db->getConnection();

            // Check if user exists
            $stmt = $pdo->prepare("SELECT id, username, email, password, role FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && verifyPassword($password, $user['password'])) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                
                // Update last login
                $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                $stmt->execute([$user['id']]);
                
                // Redirect to dashboard or index
                redirect('index.php');
            } else {
                $error = 'Invalid email or password.';
            }
        } catch (PDOException $e) {
            $error = 'Login failed. Please try again.';
            error_log("Login error: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo SITE_NAME; ?></title>
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

    <div class="container">
        <div class="auth-form glassmorphism">
            <h2>Login to Your Account</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
            
            <p class="text-center">
                Don't have an account? <a href="register.php">Register here</a>
            </p>
        </div>
    </div>

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