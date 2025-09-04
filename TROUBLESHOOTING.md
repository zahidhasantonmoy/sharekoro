# ShareKoro Deployment Troubleshooting Guide

## Common Issues and Solutions

### 1. 500 Internal Server Error

This is the most common issue when deploying to InfinityFree. Here are the solutions:

#### Solution 1: Check PHP Version
InfinityFree supports multiple PHP versions. Try different versions in your .htaccess file:
```
# Try PHP 7.4
AddHandler application/x-httpd-php74 .php

# Or PHP 8.0
AddHandler application/x-httpd-php80 .php

# Or PHP 8.1
AddHandler application/x-httpd-php81 .php
```

#### Solution 2: Check File Permissions
Ensure the uploads directory is writable:
```
chmod 755 uploads
```

#### Solution 3: Check Error Logs
Enable error reporting in init.php:
```php
// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log');
```

### 2. Database Connection Issues

#### Solution 1: Verify Database Credentials
Double-check your database configuration in config.php:
- DB_HOST: sql204.infinityfree.com
- DB_USER: Your InfinityFree username
- DB_PASS: Your InfinityFree password
- DB_NAME: Your database name

#### Solution 2: Test Database Connection
Use the debug.php file to test your database connection.

### 3. File Upload Issues

#### Solution 1: Check Upload Directory
Ensure the uploads directory exists and is writable.

#### Solution 2: Check File Size Limits
InfinityFree has file size limits. Default is usually 10MB.

### 4. Session Issues

#### Solution 1: Check Session Configuration
Ensure session settings in config.php are correct:
```php
// Session configuration
define('SESSION_LIFETIME', 3600); // 1 hour
```

### 5. URL Rewriting Issues

#### Solution 1: Check .htaccess
Ensure your .htaccess file is properly configured:
```
# Enable URL rewriting
RewriteEngine On

# Handle routing for clean URLs
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

## Deployment Steps

1. Upload all files to your InfinityFree hosting
2. Create database and import database_schema.sql
3. Update config.php with your database credentials
4. Ensure uploads directory is writable (chmod 755)
5. Test with debug.php
6. If issues persist, check error logs

## InfinityFree Specific Configuration

### PHP Configuration
- PHP Version: 7.4, 8.0, or 8.1
- Memory Limit: 128MB
- Max Execution Time: 30 seconds
- File Upload Limit: 10MB

### Database Configuration
- Host: sql204.infinityfree.com
- Port: 3306
- Database names must start with your account prefix

### File System
- All files must be in the htdocs directory
- File permissions: 644 for files, 755 for directories

## Debugging Steps

1. Test simple PHP file (test.php)
2. Check PHP configuration (phpinfo.php)
3. Test database connection (debug.php)
4. Check error logs
5. Verify file permissions
6. Test .htaccess configuration

## Contact Support

If issues persist:
1. Check InfinityFree forums
2. Contact InfinityFree support
3. Verify your account is properly activated
4. Check for any account limitations

## Additional Resources

- InfinityFree Documentation: https://infinityfree.net/help/
- PHP Documentation: https://www.php.net/manual/
- MySQL Documentation: https://dev.mysql.com/doc/