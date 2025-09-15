<?php
// share-file.php - File sharing page

require_once 'init.php';

$error = '';
$success = '';
$share_link = '';

// Generate CSRF token for the form
$csrf_token = generateCSRFToken();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF protection
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid request. Please try again.';
    } else {
    $title = sanitizeInput($_POST['title']);
    $expiration = sanitizeInput($_POST['expiration']);
    $visibility = sanitizeInput($_POST['visibility']);
    $password = $_POST['password'];
    $access_code = isset($_POST['access_code']) ? sanitizeInput($_POST['access_code']) : null;
    $is_public = isset($_POST['is_public']) ? 1 : 0;
    
    // Validation
    if (!isset($_FILES['file']) || $_FILES['file']['error'] === UPLOAD_ERR_NO_FILE) {
        $error = 'Please select a file to upload.';
    } else {
        $file = $_FILES['file'];
        
        // Check file size
        if ($file['size'] > MAX_FILE_SIZE) {
            $error = 'File size exceeds the maximum allowed size of ' . formatFileSize(MAX_FILE_SIZE) . '.';
        }
        // Check file type
        else if (!isValidFileType($file['type'])) {
            $error = 'File type not allowed. Please upload a valid file.';
        }
        // Check file extension
        else if (isDangerousFile($file['name'])) {
            $error = 'File extension not allowed. Please upload a valid file.';
        }
        // Scan file content for security issues
        else if (ENABLE_FILE_CONTENT_SCAN && !isSafeFileContent($file['tmp_name'])) {
            $error = 'File content not allowed. Please upload a safe file.';
        } elseif ($visibility === 'private' && empty($password)) {
            $error = 'Password is required for private shares.';
        } elseif ($visibility === 'protected' && empty($access_code)) {
            $error = 'Access code is required for protected shares.';
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
                
                // Create upload directory if it doesn't exist
                if (!is_dir(UPLOAD_DIR)) {
                    mkdir(UPLOAD_DIR, 0755, true);
                }
                
                // Generate unique filename
                $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $unique_filename = $share_key . '.' . $file_extension;
                $file_path = UPLOAD_DIR . $unique_filename;
                
                // Move uploaded file
                if (move_uploaded_file($file['tmp_name'], $file_path)) {
                    // Get expiration date
                    $expiration_date = getExpirationDate($expiration);
                    
                    // Hash password if provided
                    $hashed_password = !empty($password) ? hashPassword($password) : null;
                    
                    // Set is_public based on visibility
                    if ($visibility !== 'public') {
                        $is_public = 0;
                    }
                    
                    // Insert share with visibility settings
                    $stmt = $pdo->prepare("INSERT INTO shares (share_key, title, file_path, file_name, file_size, share_type, expiration_date, created_by, is_public, visibility, access_password, access_code) VALUES (?, ?, ?, ?, ?, 'file', ?, ?, ?, ?, ?, ?)");
                    
                    // Fix the parameter count issue by ensuring all parameters are provided
                    $result = $stmt->execute([
                        $share_key,
                        $title,
                        $file_path,
                        $file['name'],
                        $file['size'],
                        $expiration_date,
                        getCurrentUserId(),
                        $is_public,
                        $visibility,
                        $hashed_password,
                        $access_code
                    ]);
                    
                    if ($result) {
                        // Success
                        $share_link = SITE_URL . '/view.php?key=' . $share_key;
                        $success = 'File shared successfully!';
                    } else {
                        $error = 'Failed to share file. Please try again.';
                    }
                } else {
                    $error = 'Failed to upload file. Please try again.';
                }
            } catch (PDOException $e) {
                $error = 'Failed to share file. Please try again.';
                error_log("File share error: " . $e->getMessage());
            } catch (Exception $e) {
                $error = 'Failed to upload file. Please try again.';
                error_log("File upload error: " . $e->getMessage());
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Share File - <?php echo SITE_NAME; ?></title>
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

    <!-- Main Content -->
    <main class="container">
        <div class="auth-form glassmorphism">
            <h2><i class="fas fa-file-upload"></i> Share File</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                    <div class="form-group">
                        <label for="share_link">Share Link:</label>
                        <div class="input-group">
                            <input type="text" id="share_link" value="<?php echo $share_link; ?>" readonly>
                            <button class="btn btn-primary" onclick="copyToClipboard('<?php echo $share_link; ?>')">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                        </div>
                    </div>
                    <div class="btn-group">
                        <a href="<?php echo $share_link; ?>" target="_blank" class="btn btn-primary">
                            <i class="fas fa-external-link-alt"></i> View Share
                        </a>
                        <button class="btn btn-facebook" onclick="shareToFacebook('<?php echo $share_link; ?>')">
                            <i class="fab fa-facebook-f"></i> Facebook
                        </button>
                        <button class="btn btn-twitter" onclick="shareToTwitter('<?php echo $share_link; ?>', '<?php echo htmlspecialchars($title); ?>')">
                            <i class="fab fa-twitter"></i> Twitter
                        </button>
                        <button class="btn btn-linkedin" onclick="shareToLinkedIn('<?php echo $share_link; ?>', '<?php echo htmlspecialchars($title); ?>')">
                            <i class="fab fa-linkedin-in"></i> LinkedIn
                        </button>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if (!$success): ?>
                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <div class="form-group">
                        <label for="title">Title (Optional)</label>
                        <input type="text" id="title" name="title" placeholder="Enter a title for your file">
                    </div>
                    
                    <div class="form-group">
                        <label for="file">File *</label>
                        <input type="file" id="file" name="file" required>
                        <small>Maximum file size: <?php echo formatFileSize(MAX_FILE_SIZE); ?></small>
                    </div>
                    
                    <div class="form-group">
                        <label for="visibility">Visibility</label>
                        <select id="visibility" name="visibility">
                            <option value="public">Public - Visible to everyone</option>
                            <option value="private">Private - Password required</option>
                            <option value="protected">Protected - 4-digit code required</option>
                        </select>
                    </div>
                    
                    <div class="visibility-option private-option" style="display: none;">
                        <h4><i class="fas fa-lock"></i> Password Protection</h4>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <div class="input-group">
                                <input type="password" id="password" name="password" placeholder="Enter password for private access">
                                <span class="input-group-addon" onclick="togglePasswordVisibility('password', 'password_icon')">
                                    <i class="fas fa-eye" id="password_icon"></i>
                                </span>
                            </div>
                            <small class="form-text">Users must enter this password to view content</small>
                        </div>
                    </div>
                    
                    <div class="visibility-option protected-option" style="display: none;">
                        <h4><i class="fas fa-shield-alt"></i> Access Code Protection</h4>
                        <div class="form-group">
                            <label>Access Code</label>
                            <div class="input-group">
                                <input type="text" id="access_code" name="access_code" placeholder="Auto-generated" readonly>
                                <span class="input-group-addon" onclick="generateAccessCode()">
                                    <i class="fas fa-sync"></i>
                                </span>
                            </div>
                            <small class="form-text">Users must enter this 4-character code to view content</small>
                        </div>
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
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_public" id="is_public" checked> 
                            Make public (appears in recent shares)
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-share-alt"></i> Share File
                    </button>
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
        // Toggle visibility options based on selection
        function toggleVisibilityOptions() {
            const visibility = document.getElementById('visibility').value;
            const privateOption = document.querySelector('.private-option');
            const protectedOption = document.querySelector('.protected-option');
            const isPublicCheckbox = document.getElementById('is_public');
            
            // Hide all options first
            privateOption.style.display = 'none';
            protectedOption.style.display = 'none';
            
            // Show relevant option
            if (visibility === 'private') {
                privateOption.style.display = 'block';
                isPublicCheckbox.checked = false;
                isPublicCheckbox.disabled = true;
            } else if (visibility === 'protected') {
                protectedOption.style.display = 'block';
                isPublicCheckbox.checked = false;
                isPublicCheckbox.disabled = true;
                generateAccessCode();
            } else {
                isPublicCheckbox.checked = true;
                isPublicCheckbox.disabled = false;
            }
        }
        
        // Generate random 4-character access code
        function generateAccessCode() {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            let code = '';
            for (let i = 0; i < 4; i++) {
                code += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            document.getElementById('access_code').value = code;
            showNotification('Access code generated: ' + code);
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            const visibilitySelect = document.getElementById('visibility');
            if (visibilitySelect) {
                visibilitySelect.addEventListener('change', toggleVisibilityOptions);
                toggleVisibilityOptions(); // Initialize on load
            }
        });
        
        // Social sharing functions
        function shareToFacebook(url) {
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank', 'width=600,height=400');
        }
        
        function shareToTwitter(url, title) {
            const text = title ? encodeURIComponent(title) : encodeURIComponent('Check out this file share');
            window.open(`https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${text}`, '_blank', 'width=600,height=400');
        }
        
        function shareToLinkedIn(url, title) {
            const text = title ? encodeURIComponent(title) : '';
            window.open(`https://www.linkedin.com/shareArticle?mini=true&url=${encodeURIComponent(url)}&title=${text}`, '_blank', 'width=600,height=400');
        }
    </script>
</body>
</html>