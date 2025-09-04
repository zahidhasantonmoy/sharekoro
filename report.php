<?php
// report.php - Handle share reporting

require_once 'init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $share_id = (int)$_POST['share_id'];
    $reason = sanitizeInput($_POST['reason']);
    $reporter_ip = $_SERVER['REMOTE_ADDR'];
    
    // Validation
    if (empty($reason)) {
        $_SESSION['error'] = 'Please provide a reason for reporting.';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
    
    try {
        $db = new Database();
        $pdo = $db->getConnection();
        
        // Check if share exists
        $stmt = $pdo->prepare("SELECT id FROM shares WHERE id = ?");
        $stmt->execute([$share_id]);
        
        if ($stmt->rowCount() > 0) {
            // Insert report
            $stmt = $pdo->prepare("INSERT INTO reports (share_id, reporter_ip, reason) VALUES (?, ?, ?)");
            $stmt->execute([$share_id, $reporter_ip, $reason]);
            
            $_SESSION['success'] = 'Report submitted successfully. Thank you for helping us maintain a safe community.';
        } else {
            $_SESSION['error'] = 'Invalid share.';
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Failed to submit report. Please try again.';
        error_log("Report error: " . $e->getMessage());
    }
}

// Redirect back to the previous page
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;
?>