<?php
// collections.php - Manage share collections

require_once 'init.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

$error = '';
$success = '';
$collections = [];
$shares = [];

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    // Get user's collections
    $stmt = $pdo->prepare("SELECT * FROM collections WHERE created_by = ? ORDER BY created_at DESC");
    $stmt->execute([getCurrentUserId()]);
    $collections = $stmt->fetchAll();
    
    // Get user's shares for adding to collections
    $stmt = $pdo->prepare("SELECT id, share_key, title, share_type FROM shares WHERE created_by = ? ORDER BY created_at DESC");
    $stmt->execute([getCurrentUserId()]);
    $shares = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $error = 'Failed to retrieve your collections. Please try again.';
    error_log("Collections page error: " . $e->getMessage());
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF protection
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid request. Please try again.';
    } else {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'create_collection':
                    $name = sanitizeInput($_POST['name']);
                    $description = sanitizeInput($_POST['description']);
                    $is_public = isset($_POST['is_public']) ? 1 : 0;
                    
                    if (empty($name)) {
                        $error = 'Collection name is required.';
                    } else {
                        try {
                            $stmt = $pdo->prepare("INSERT INTO collections (name, description, created_by, is_public) VALUES (?, ?, ?, ?)");
                            $stmt->execute([$name, $description, getCurrentUserId(), $is_public]);
                            $success = 'Collection created successfully!';
                            
                            // Refresh collections list
                            $stmt = $pdo->prepare("SELECT * FROM collections WHERE created_by = ? ORDER BY created_at DESC");
                            $stmt->execute([getCurrentUserId()]);
                            $collections = $stmt->fetchAll();
                        } catch (PDOException $e) {
                            $error = 'Failed to create collection. Please try again.';
                            error_log("Create collection error: " . $e->getMessage());
                        }
                    }
                    break;
                    
                case 'delete_collection':
                    $collection_id = (int)$_POST['collection_id'];
                    
                    try {
                        // Verify ownership
                        $stmt = $pdo->prepare("SELECT id FROM collections WHERE id = ? AND created_by = ?");
                        $stmt->execute([$collection_id, getCurrentUserId()]);
                        
                        if ($stmt->rowCount() > 0) {
                            // Delete collection (cascades to collection_shares)
                            $stmt = $pdo->prepare("DELETE FROM collections WHERE id = ?");
                            $stmt->execute([$collection_id]);
                            $success = 'Collection deleted successfully!';
                            
                            // Refresh collections list
                            $stmt = $pdo->prepare("SELECT * FROM collections WHERE created_by = ? ORDER BY created_at DESC");
                            $stmt->execute([getCurrentUserId()]);
                            $collections = $stmt->fetchAll();
                        } else {
                            $error = 'Collection not found or you do not have permission to delete it.';
                        }
                    } catch (PDOException $e) {
                        $error = 'Failed to delete collection. Please try again.';
                        error_log("Delete collection error: " . $e->getMessage());
                    }
                    break;
                    
                case 'add_share_to_collection':
                    $collection_id = (int)$_POST['collection_id'];
                    $share_id = (int)$_POST['share_id'];
                    
                    try {
                        // Verify ownership of both collection and share
                        $stmt = $pdo->prepare("SELECT id FROM collections WHERE id = ? AND created_by = ?");
                        $stmt->execute([$collection_id, getCurrentUserId()]);
                        $collection_exists = $stmt->rowCount() > 0;
                        
                        $stmt = $pdo->prepare("SELECT id FROM shares WHERE id = ? AND created_by = ?");
                        $stmt->execute([$share_id, getCurrentUserId()]);
                        $share_exists = $stmt->rowCount() > 0;
                        
                        if ($collection_exists && $share_exists) {
                            // Add share to collection
                            $stmt = $pdo->prepare("INSERT INTO collection_shares (collection_id, share_id) VALUES (?, ?)");
                            $stmt->execute([$collection_id, $share_id]);
                            $success = 'Share added to collection!';
                        } else {
                            $error = 'Collection or share not found, or you do not have permission to modify them.';
                        }
                    } catch (PDOException $e) {
                        if ($e->getCode() == 23000) { // Duplicate entry
                            $error = 'Share is already in this collection.';
                        } else {
                            $error = 'Failed to add share to collection. Please try again.';
                            error_log("Add share to collection error: " . $e->getMessage());
                        }
                    }
                    break;
            }
        }
    }
}

// Generate CSRF token for forms
$csrf_token = generateCSRFToken();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collections - <?php echo SITE_NAME; ?></title>
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
                <button id="theme-toggle" class="btn btn-outline" title="Toggle theme">
                    <i class="fas fa-moon"></i>
                </button>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container">
        <div class="auth-form glassmorphism">
            <h2><i class="fas fa-folder"></i> Your Collections</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error" role="alert" aria-live="polite">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success" role="alert" aria-live="polite">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <!-- Create Collection Form -->
            <div class="glassmorphism" style="padding: 25px; margin-bottom: 30px;">
                <h3><i class="fas fa-plus"></i> Create New Collection</h3>
                <form method="POST" action="" role="form" aria-label="Create collection form">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="action" value="create_collection">
                    
                    <div class="form-group">
                        <label for="name">Collection Name *</label>
                        <input type="text" id="name" name="name" required placeholder="Enter collection name" aria-describedby="name-help">
                        <div id="name-help" class="form-text">Enter a name for your new collection</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description (Optional)</label>
                        <textarea id="description" name="description" rows="3" placeholder="Enter collection description" aria-describedby="description-help"></textarea>
                        <div id="description-help" class="form-text">Describe what this collection is for</div>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_public" id="is_public" aria-describedby="public-help"> 
                            Make public (appears in your profile)
                        </label>
                        <div id="public-help" class="form-text">Check to make this collection visible to others</div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" aria-label="Create collection">
                        <i class="fas fa-folder-plus"></i> Create Collection
                    </button>
                </form>
            </div>
            
            <!-- Collections List -->
            <?php if (count($collections) > 0): ?>
                <h3><i class="fas fa-list"></i> Your Collections</h3>
                <div class="shares-list">
                    <?php foreach ($collections as $collection): ?>
                        <div class="share-item glassmorphism">
                            <div class="share-info">
                                <h4><?php echo htmlspecialchars($collection['name']); ?></h4>
                                <?php if (!empty($collection['description'])): ?>
                                    <p><?php echo htmlspecialchars($collection['description']); ?></p>
                                <?php endif; ?>
                                <p class="share-meta">
                                    <span><i class="fas fa-calendar"></i> <?php echo date('M j, Y', strtotime($collection['created_at'])); ?></span>
                                    <span class="visibility-badge <?php echo $collection['is_public'] ? 'public' : 'private'; ?>">
                                        <i class="fas fa-<?php echo $collection['is_public'] ? 'globe' : 'lock'; ?>"></i>
                                        <?php echo $collection['is_public'] ? 'Public' : 'Private'; ?>
                                    </span>
                                </p>
                            </div>
                            <div class="share-actions">
                                <a href="view-collection.php?id=<?php echo $collection['id']; ?>" class="btn btn-secondary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                    <input type="hidden" name="action" value="delete_collection">
                                    <input type="hidden" name="collection_id" value="<?php echo $collection['id']; ?>">
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this collection?')" aria-label="Delete collection">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="restricted-message">
                    <i class="fas fa-folder fa-3x"></i>
                    <h3>No Collections Found</h3>
                    <p>Create your first collection to organize your shares!</p>
                </div>
            <?php endif; ?>
            
            <!-- Add Share to Collection -->
            <?php if (count($shares) > 0 && count($collections) > 0): ?>
                <h3><i class="fas fa-plus-circle"></i> Add Share to Collection</h3>
                <form method="POST" action="" class="glassmorphism" style="padding: 25px;" role="form" aria-label="Add share to collection form">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="action" value="add_share_to_collection">
                    
                    <div class="form-group">
                        <label for="share_id">Select Share</label>
                        <select id="share_id" name="share_id" required aria-describedby="share-help">
                            <option value="">-- Select a share --</option>
                            <?php foreach ($shares as $share): ?>
                                <option value="<?php echo $share['id']; ?>">
                                    <?php echo htmlspecialchars($share['title'] ?? 'Untitled ' . ucfirst($share['share_type'])); ?> 
                                    (<?php echo ucfirst($share['share_type']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div id="share-help" class="form-text">Select which share to add to a collection</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="collection_id">Select Collection</label>
                        <select id="collection_id" name="collection_id" required aria-describedby="collection-help">
                            <option value="">-- Select a collection --</option>
                            <?php foreach ($collections as $collection): ?>
                                <option value="<?php echo $collection['id']; ?>">
                                    <?php echo htmlspecialchars($collection['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div id="collection-help" class="form-text">Select which collection to add the share to</div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" aria-label="Add share to collection">
                        <i class="fas fa-plus"></i> Add to Collection
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
</body>
</html>