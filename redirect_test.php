<?php
// redirect_test.php - Test redirects and URL rewriting

echo "<h1>Redirect Test</h1>";

echo "<h2>Server Information</h2>";
echo "<p>Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Request URI: " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p>Script Name: " . $_SERVER['SCRIPT_NAME'] . "</p>";

echo "<h2>Test Links</h2>";
echo "<ul>";
echo "<li><a href='index.php'>Direct link to index.php</a></li>";
echo "<li><a href='test.php'>Direct link to test.php</a></li>";
echo "<li><a href='login.php'>Direct link to login.php</a></li>";
echo "</ul>";

echo "<h2>Test Redirect</h2>";
echo "<p><a href='redirect_test.php?redirect=1'>Click here to test redirect</a></p>";

if (isset($_GET['redirect'])) {
    header("Location: index.php");
    exit;
}

echo "<h2>URL Rewriting Test</h2>";
echo "<p>If URL rewriting is working, <a href='test-rewrite'>this link</a> should show a test page.</p>";
?>