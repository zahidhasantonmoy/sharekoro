<?php
// simple-test.php - Very simple test to check if PHP is working

echo "PHP is working!";

// Test if we can include a simple file
if (file_exists('config.php')) {
    echo " - config.php found";
} else {
    echo " - config.php NOT found";
}

// Test if we can include init.php
if (file_exists('init.php')) {
    echo " - init.php found";
} else {
    echo " - init.php NOT found";
}

echo " - Time: " . date('Y-m-d H:i:s');
?>