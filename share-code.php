<?php
// share-code.php - Code sharing page

require_once 'init.php';

$error = '';
$success = '';
$share_link = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeInput($_POST['title']);
    $content = $_POST['content'];
    $language = sanitizeInput($_POST['language']);
    $expiration = sanitizeInput($_POST['expiration']);
    $password = $_POST['password'];
    $is_public = isset($_POST['is_public']) ? 1 : 0;
    
    // Validation
    if (empty($content)) {
        $error = 'Code content is required.';
    } else {
        try {
            $db = new Database();
            $pdo = $db->getConnection();
            
            // Generate unique share key
            do {
                $share_key = generateShareKey();
                $stmt = $pdo->prepare("SELECT id FROM shares WHERE share_key = ?");
                $stmt->execute([$share_key]);
            } while ($stmt->rowCount() > 0);
            
            // Get expiration date
            $expiration_date = getExpirationDate($expiration);
            
            // Hash password if provided
            $hashed_password = !empty($password) ? hashPassword($password) : null;
            
            // Insert share
            $stmt = $pdo->prepare("INSERT INTO shares (share_key, title, content, share_type, expiration_date, created_by, is_public, password_protect) VALUES (?, ?, ?, 'code', ?, ?, ?, ?)");
            $stmt->execute([
                $share_key,
                $title,
                $content,
                $expiration_date,
                getCurrentUserId(),
                $is_public,
                $hashed_password
            ]);
            
            $share_id = $pdo->lastInsertId();
            
            // Success
            $share_link = SITE_URL . '/view.php?key=' . $share_key;
            $success = 'Code shared successfully!';
        } catch (PDOException $e) {
            $error = 'Failed to share code. Please try again.';
            error_log("Code share error: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Share Code - <?php echo SITE_NAME; ?></title>
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
        <div class="auth-form glassmorphism">
            <h2>Share Code</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo $success; ?>
                    <div class="form-group">
                        <label for="share_link">Share Link:</label>
                        <div class="input-group">
                            <input type="text" id="share_link" value="<?php echo $share_link; ?>" readonly>
                            <button class="btn btn-primary" onclick="copyToClipboard('<?php echo $share_link; ?>')">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                        </div>
                    </div>
                    <p class="text-center">
                        <a href="<?php echo $share_link; ?>" target="_blank" class="btn btn-primary">
                            <i class="fas fa-external-link-alt"></i> View Share
                        </a>
                    </p>
                </div>
            <?php endif; ?>
            
            <?php if (!$success): ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="title">Title (Optional)</label>
                        <input type="text" id="title" name="title">
                    </div>
                    
                    <div class="form-group">
                        <label for="language">Language</label>
                        <select id="language" name="language">
                            <option value="plaintext">Plain Text</option>
                            <option value="javascript">JavaScript</option>
                            <option value="python">Python</option>
                            <option value="php">PHP</option>
                            <option value="java">Java</option>
                            <option value="csharp">C#</option>
                            <option value="cpp">C++</option>
                            <option value="css">CSS</option>
                            <option value="html">HTML</option>
                            <option value="sql">SQL</option>
                            <option value="ruby">Ruby</option>
                            <option value="go">Go</option>
                            <option value="rust">Rust</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="content">Code *</label>
                        <textarea id="content" name="content" rows="15" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="expiration">Expiration</label>
                        <select id="expiration" name="expiration">
                            <?php foreach (EXPIRATION_OPTIONS as $key => $value): ?>
                                <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password Protection (Optional)</label>
                        <div class="input-group">
                            <input type="password" id="password" name="password">
                            <span class="input-group-addon" onclick="togglePasswordVisibility('password', 'password_icon')">
                                <i class="fas fa-eye" id="password_icon"></i>
                            </span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_public" checked> 
                            Make public (appears in recent shares)
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Share Code</button>
                </form>
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