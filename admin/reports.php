<?php
// admin/reports.php - Admin reports management

require_once 'auth.php';

$error = '';
$reports = [];

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    // Handle report actions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = sanitizeInput($_POST['action']);
        $report_id = (int)$_POST['report_id'];
        
        if ($action === 'resolve') {
            // Resolve report
            $stmt = $pdo->prepare("UPDATE reports SET status = 'resolved' WHERE id = ?");
            $stmt->execute([$report_id]);
            $success = 'Report resolved successfully.';
        } elseif ($action === 'delete') {
            // Delete report
            $stmt = $pdo->prepare("DELETE FROM reports WHERE id = ?");
            $stmt->execute([$report_id]);
            $success = 'Report deleted successfully.';
        } elseif ($action === 'delete_share') {
            // Get report details
            $stmt = $pdo->prepare("SELECT r.*, s.file_path FROM reports r JOIN shares s ON r.share_id = s.id WHERE r.id = ?");
            $stmt->execute([$report_id]);
            $report = $stmt->fetch();
            
            if ($report) {
                // Delete share
                $stmt = $pdo->prepare("DELETE FROM shares WHERE id = ?");
                $stmt->execute([$report['share_id']]);
                
                // Delete file if it exists
                if (!empty($report['file_path']) && file_exists($report['file_path'])) {
                    unlink($report['file_path']);
                }
                
                // Resolve report
                $stmt = $pdo->prepare("UPDATE reports SET status = 'resolved' WHERE id = ?");
                $stmt->execute([$report_id]);
                
                $success = 'Share deleted and report resolved successfully.';
            }
        }
    }
    
    // Get all reports
    $stmt = $pdo->prepare("SELECT r.*, s.title as share_title, s.share_key FROM reports r JOIN shares s ON r.share_id = s.id ORDER BY r.created_at DESC");
    $stmt->execute();
    $reports = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = 'Failed to retrieve reports. Please try again.';
    error_log("Admin reports error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reports - <?php echo SITE_NAME; ?> Admin</title>
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
            <h2>Manage Reports</h2>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (count($reports) > 0): ?>
                <div class="admin-list">
                    <?php foreach ($reports as $report): ?>
                        <div class="admin-item glassmorphism">
                            <div class="admin-item-info">
                                <h4><?php echo htmlspecialchars($report['share_title'] ?? 'Untitled Share'); ?></h4>
                                <p><?php echo htmlspecialchars($report['reason']); ?></p>
                                <p class="reporter">Reported by: <?php echo htmlspecialchars($report['reporter_ip']); ?></p>
                            </div>
                            <div class="admin-item-meta">
                                <span class="status <?php echo $report['status']; ?>"><?php echo ucfirst($report['status']); ?></span>
                                <span><?php echo date('M j, Y', strtotime($report['created_at'])); ?></span>
                            </div>
                            <div class="admin-item-actions">
                                <a href="../view.php?key=<?php echo $report['share_key']; ?>" class="btn btn-secondary" target="_blank">View Share</a>
                                <?php if ($report['status'] === 'pending'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="resolve">
                                        <input type="hidden" name="report_id" value="<?php echo $report['id']; ?>">
                                        <button type="submit" class="btn btn-primary">Resolve</button>
                                    </form>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="delete_share">
                                        <input type="hidden" name="report_id" value="<?php echo $report['id']; ?>">
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this share and resolve the report?')">Delete Share</button>
                                    </form>
                                <?php else: ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="report_id" value="<?php echo $report['id']; ?>">
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>No reports found.</p>
            <?php endif; ?>
            
            <p class="text-center">
                <a href="index.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
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