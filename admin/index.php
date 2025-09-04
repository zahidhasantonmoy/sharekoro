<?php
// admin/index.php - Admin dashboard

require_once 'auth.php';

$error = '';
$users = [];
$shares = [];
$reports = [];

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    // Get statistics
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users");
    $stmt->execute();
    $user_count = $stmt->fetch()['count'];
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM shares");
    $stmt->execute();
    $share_count = $stmt->fetch()['count'];
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM reports WHERE status = 'pending'");
    $stmt->execute();
    $report_count = $stmt->fetch()['count'];
    
    // Get recent users
    $stmt = $pdo->prepare("SELECT * FROM users ORDER BY created_at DESC LIMIT 5");
    $stmt->execute();
    $users = $stmt->fetchAll();
    
    // Get recent shares
    $stmt = $pdo->prepare("SELECT s.*, u.username FROM shares s LEFT JOIN users u ON s.created_by = u.id ORDER BY s.created_at DESC LIMIT 5");
    $stmt->execute();
    $shares = $stmt->fetchAll();
    
    // Get pending reports
    $stmt = $pdo->prepare("SELECT r.*, s.title as share_title FROM reports r JOIN shares s ON r.share_id = s.id WHERE r.status = 'pending' ORDER BY r.created_at DESC LIMIT 5");
    $stmt->execute();
    $reports = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = 'Failed to retrieve data. Please try again.';
    error_log("Admin dashboard error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo SITE_NAME; ?></title>
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
                <a href="index.php" class="btn btn-secondary">Dashboard</a>
                <a href="users.php" class="btn btn-secondary">Users</a>
                <a href="shares.php" class="btn btn-secondary">Shares</a>
                <a href="reports.php" class="btn btn-secondary">Reports</a>
                <a href="../logout.php" class="btn btn-outline">Logout</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container">
        <div class="auth-form glassmorphism">
            <h2>Admin Dashboard</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="dashboard-stats">
                <div class="stat-card glassmorphism">
                    <h3><?php echo $user_count; ?></h3>
                    <p>Users</p>
                </div>
                <div class="stat-card glassmorphism">
                    <h3><?php echo $share_count; ?></h3>
                    <p>Shares</p>
                </div>
                <div class="stat-card glassmorphism">
                    <h3><?php echo $report_count; ?></h3>
                    <p>Pending Reports</p>
                </div>
            </div>
            
            <div class="admin-sections">
                <div class="admin-section glassmorphism">
                    <h3>Recent Users</h3>
                    <?php if (count($users) > 0): ?>
                        <div class="admin-list">
                            <?php foreach ($users as $user): ?>
                                <div class="admin-item">
                                    <div class="admin-item-info">
                                        <h4><?php echo htmlspecialchars($user['username']); ?></h4>
                                        <p><?php echo htmlspecialchars($user['email']); ?></p>
                                    </div>
                                    <div class="admin-item-meta">
                                        <span><?php echo date('M j, Y', strtotime($user['created_at'])); ?></span>
                                        <span class="role <?php echo $user['role']; ?>"><?php echo ucfirst($user['role']); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>No users found.</p>
                    <?php endif; ?>
                </div>
                
                <div class="admin-section glassmorphism">
                    <h3>Recent Shares</h3>
                    <?php if (count($shares) > 0): ?>
                        <div class="admin-list">
                            <?php foreach ($shares as $share): ?>
                                <div class="admin-item">
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
                                        <span><?php echo ucfirst($share['share_type']); ?></span>
                                        <span><?php echo date('M j, Y', strtotime($share['created_at'])); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>No shares found.</p>
                    <?php endif; ?>
                </div>
                
                <div class="admin-section glassmorphism">
                    <h3>Pending Reports</h3>
                    <?php if (count($reports) > 0): ?>
                        <div class="admin-list">
                            <?php foreach ($reports as $report): ?>
                                <div class="admin-item">
                                    <div class="admin-item-info">
                                        <h4><?php echo htmlspecialchars($report['share_title'] ?? 'Untitled Share'); ?></h4>
                                        <p><?php echo htmlspecialchars(substr($report['reason'], 0, 50)) . '...'; ?></p>
                                    </div>
                                    <div class="admin-item-meta">
                                        <span><?php echo date('M j, Y', strtotime($report['created_at'])); ?></span>
                                        <a href="reports.php" class="btn btn-outline">View</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>No pending reports.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <p class="text-center">
                <a href="../index.php"><i class="fas fa-arrow-left"></i> Back to Site</a>
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