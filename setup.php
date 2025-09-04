<?php
// setup.php - Setup script to create necessary directories

echo "Setting up ShareKoro...\n";

// Create uploads directory
if (!is_dir('uploads')) {
    if (mkdir('uploads', 0755, true)) {
        echo "✓ Uploads directory created successfully\n";
    } else {
        echo "✗ Failed to create uploads directory\n";
    }
} else {
    echo "✓ Uploads directory already exists\n";
}

// Create .htaccess file for uploads directory
$htaccessContent = "Options -Indexes\n<Files \"*\">\n  Order Allow,Deny\n  Deny from all\n</Files>\n<FilesMatch \"\.(jpg|jpeg|png|gif|pdf|txt|doc|docx|zip)$\">\n  Order Allow,Deny\n  Allow from all\n</FilesMatch>";
file_put_contents('uploads/.htaccess', $htaccessContent);
echo "✓ .htaccess file created in uploads directory\n";

echo "Setup complete!\n";
?>