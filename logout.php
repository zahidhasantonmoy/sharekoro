<?php
// logout.php - User logout

require_once 'init.php';

// Destroy session
session_destroy();

// Redirect to homepage
redirect('index.php');
?>