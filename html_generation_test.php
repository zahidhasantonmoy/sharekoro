<?php
// html_generation_test.php - Test HTML generation without executing any logic

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing HTML generation...\n";

// Include init.php to get constants
require_once 'init.php';

// Set up test variables
$error = '';
$success = '';
$share_link = '';
$csrf_token = 'test_csrf_token';

echo "Variables set up.\n";

// Test if we can generate the HTML without errors
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Share Text - <?php echo SITE_NAME; ?></title>
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
                <?php if (false): ?>
                    <span>Welcome, Test User!</span>
                    <a href="dashboard.php" class="btn btn-dashboard">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="admin/" class="btn btn-admin">
                        <i class="fas fa-cog"></i> Admin Panel
                    </a>
                    <form method="POST" action="logout.php" style="display: inline;">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
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
            <h2><i class="fas fa-font"></i> Share Text</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success" role="alert" aria-live="polite">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                    <div class="form-group">
                        <label for="share_link">Share Link:</label>
                        <div class="input-group">
                            <input type="text" id="share_link" value="<?php echo $share_link; ?>" readonly aria-label="Share link">
                            <button class="btn btn-primary" onclick="copyToClipboard('<?php echo $share_link; ?>')" aria-label="Copy share link to clipboard">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                        </div>
                    </div>
                    <div class="btn-group">
                        <a href="<?php echo $share_link; ?>" target="_blank" class="btn btn-primary" aria-label="View share in new tab">
                            <i class="fas fa-external-link-alt"></i> View Share
                        </a>
                        <button class="btn btn-facebook" onclick="shareToFacebook('<?php echo $share_link; ?>')" aria-label="Share on Facebook">
                            <i class="fab fa-facebook-f"></i> Facebook
                        </button>
                        <button class="btn btn-twitter" onclick="shareToTwitter('<?php echo $share_link; ?>', '<?php echo htmlspecialchars('Test Title'); ?>')" aria-label="Share on Twitter">
                            <i class="fab fa-twitter"></i> Twitter
                        </button>
                        <button class="btn btn-linkedin" onclick="shareToLinkedIn('<?php echo $share_link; ?>', '<?php echo htmlspecialchars('Test Title'); ?>')" aria-label="Share on LinkedIn">
                            <i class="fab fa-linkedin-in"></i> LinkedIn
                        </button>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if (!$success): ?>
                <form method="POST" action="" role="form" aria-label="Share text form">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <div class="form-group">
                        <label for="title">Title (Optional)</label>
                        <input type="text" id="title" name="title" placeholder="Enter a title for your text" aria-describedby="title-help">
                        <div id="title-help" class="form-text">Enter an optional title for your text share</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="content">Content *</label>
                        <textarea id="content" name="content" rows="10" required placeholder="Enter your text content here..." aria-describedby="content-help"></textarea>
                        <div id="content-help" class="form-text">Enter the text content you want to share (required)</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="visibility">Visibility</label>
                        <select id="visibility" name="visibility" aria-describedby="visibility-help">
                            <option value="public">Public - Visible to everyone</option>
                            <option value="private">Private - Password required</option>
                            <option value="protected">Protected - 4-digit code required</option>
                        </select>
                        <div id="visibility-help" class="form-text">Select who can view your share</div>
                    </div>
                    
                    <div class="visibility-option private-option" style="display: none;" aria-hidden="true">
                        <h4><i class="fas fa-lock"></i> Password Protection</h4>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <div class="input-group">
                                <input type="password" id="password" name="password" placeholder="Enter password for private access" aria-describedby="password-help">
                                <span class="input-group-addon" onclick="togglePasswordVisibility('password', 'password_icon')" aria-label="Toggle password visibility" role="button" tabindex="0" onkeydown="if(event.key==='Enter'||event.key===' ')togglePasswordVisibility('password', 'password_icon')">
                                    <i class="fas fa-eye" id="password_icon"></i>
                                </span>
                            </div>
                            <div id="password-help" class="form-text">Users must enter this password to view content</div>
                        </div>
                    </div>
                    
                    <div class="visibility-option protected-option" style="display: none;" aria-hidden="true">
                        <h4><i class="fas fa-shield-alt"></i> Access Code Protection</h4>
                        <div class="form-group">
                            <label>Access Code</label>
                            <div class="input-group">
                                <input type="text" id="access_code" name="access_code" placeholder="Auto-generated" readonly aria-describedby="access-code-help">
                                <span class="input-group-addon" onclick="generateAccessCode()" aria-label="Generate new access code" role="button" tabindex="0" onkeydown="if(event.key==='Enter'||event.key===' ')generateAccessCode()">
                                    <i class="fas fa-sync"></i>
                                </span>
                            </div>
                            <div id="access-code-help" class="form-text">Users must enter this 4-character code to view content</div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="expiration">Expiration</label>
                        <select id="expiration" name="expiration" aria-describedby="expiration-help">
                            <?php foreach (EXPIRATION_OPTIONS as $key => $value): ?>
                                <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div id="expiration-help" class="form-text">Select when your share will expire</div>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_public" id="is_public" checked aria-describedby="public-help"> 
                            Make public (appears in recent shares)
                        </label>
                        <div id="public-help" class="form-text">Uncheck to make this share completely private</div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" aria-label="Share text">
                        <i class="fas fa-share-alt"></i> Share Text
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
            privateOption.setAttribute('aria-hidden', 'true');
            protectedOption.setAttribute('aria-hidden', 'true');
            
            // Show relevant option
            if (visibility === 'private') {
                privateOption.style.display = 'block';
                privateOption.setAttribute('aria-hidden', 'false');
                isPublicCheckbox.checked = false;
                isPublicCheckbox.disabled = true;
                isPublicCheckbox.setAttribute('aria-disabled', 'true');
            } else if (visibility === 'protected') {
                protectedOption.style.display = 'block';
                protectedOption.setAttribute('aria-hidden', 'false');
                isPublicCheckbox.checked = false;
                isPublicCheckbox.disabled = true;
                isPublicCheckbox.setAttribute('aria-disabled', 'true');
                // generateAccessCode(); // Commented out for testing
            } else {
                isPublicCheckbox.checked = true;
                isPublicCheckbox.disabled = false;
                isPublicCheckbox.setAttribute('aria-disabled', 'false');
            }
        }
        
        // Generate random 4-character access code
        function generateAccessCode() {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            let code = '';
            for (let i = 0; i < 4; i++) {
                code += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            const accessCodeInput = document.getElementById('access_code');
            accessCodeInput.value = code;
            // showNotification('Access code generated: ' + code); // Commented out for testing
            
            // Announce to screen readers
            const announcement = document.createElement('div');
            announcement.setAttribute('aria-live', 'polite');
            announcement.setAttribute('aria-atomic', 'true');
            announcement.className = 'sr-only';
            announcement.textContent = 'Access code generated: ' + code;
            document.body.appendChild(announcement);
            
            // Remove after announcement
            setTimeout(() => {
                document.body.removeChild(announcement);
            }, 1000);
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            const visibilitySelect = document.getElementById('visibility');
            if (visibilitySelect) {
                visibilitySelect.addEventListener('change', toggleVisibilityOptions);
                toggleVisibilityOptions(); // Initialize on load
            }
            
            // Add keyboard support for toggle buttons
            const toggleButtons = document.querySelectorAll('.input-group-addon[role="button"]');
            toggleButtons.forEach(button => {
                button.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.click();
                    }
                });
            });
        });
        
        // Social sharing functions
        function shareToFacebook(url) {
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank', 'width=600,height=400');
        }
        
        function shareToTwitter(url, title) {
            const text = title ? encodeURIComponent(title) : encodeURIComponent('Check out this text share');
            window.open(`https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${text}`, '_blank', 'width=600,height=400');
        }
        
        function shareToLinkedIn(url, title) {
            const text = title ? encodeURIComponent(title) : '';
            window.open(`https://www.linkedin.com/shareArticle?mini=true&url=${encodeURIComponent(url)}&title=${text}`, '_blank', 'width=600,height=400');
        }
    </script>
</body>
</html>
<?php
$html_content = ob_get_clean();
echo "✓ HTML generated successfully, length: " . strlen($html_content) . " characters\n";
echo "HTML generation test completed.\n";
?>