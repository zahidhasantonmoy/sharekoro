<?php
// logout.php - User logout

require_once 'init.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

// CSRF protection
if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
    $_SESSION['error'] = 'Invalid request.';
    redirect('index.php');
}

// Destroy session
session_destroy();

// Redirect to homepage
redirect('index.php');
?>