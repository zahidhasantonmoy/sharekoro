<?php
// admin/auth.php - Admin authentication middleware

require_once '../init.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('../login.php');
}

// Redirect if not admin
if (!isAdmin()) {
    redirect('../index.php');
}
?>