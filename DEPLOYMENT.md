# ShareKoro Deployment Guide

## Prerequisites
1. InfinityFree hosting account
2. Database created in InfinityFree control panel
3. FTP access to your hosting account

## Deployment Steps

### 1. Upload Files
Upload all files from your local ShareKoro directory to the `htdocs` folder of your InfinityFree hosting account using FTP.

### 2. Database Setup
1. Log in to your InfinityFree control panel
2. Create a new database
3. Note the database name, username, and password
4. Import the `database_schema.sql` file into your database

### 3. Configuration
1. Edit `config.php` with your database credentials:
   ```php
   define('DB_HOST', 'sql204.infinityfree.com');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'your_database_name');
   ```

2. Update the site URL in `config.php`:
   ```php
   define('SITE_URL', 'http://yourdomain.rf.gd');
   ```

### 4. File Permissions
Ensure the uploads directory is writable:
- Set permissions to 755 for the uploads directory

### 5. Run Installation Script
1. Visit `http://yourdomain.rf.gd/install.php` in your browser
2. Follow the installation instructions
3. Delete `install.php` after successful installation

### 6. Test Your Site
1. Visit your homepage
2. Test all functionality:
   - Anonymous sharing
   - User registration and login
   - Admin panel access (default admin account)
   - File uploads

## Troubleshooting

If you encounter issues:

1. Check the `TROUBLESHOOTING.md` file for common solutions
2. Enable error reporting by modifying `init.php`:
   ```php
   error_reporting(E_ALL);
   ini_set('display_errors', 1);
   ```
3. Check your InfinityFree error logs
4. Verify file permissions
5. Test database connection with `debug.php`

## Admin Access

To access the admin panel:
1. Register a new user account
2. Manually update the user's role in the database:
   ```sql
   UPDATE users SET role = 'admin' WHERE username = 'your_username';
   ```
3. Log in and visit `/admin/`

## Security Recommendations

1. Delete `debug.php`, `phpinfo.php`, and `test.php` files
2. Ensure `config.php` is not accessible directly
3. Regularly update your installation
4. Monitor your error logs
5. Use strong passwords for admin accounts

## Support

For additional help:
1. Check the GitHub repository issues
2. Contact InfinityFree support for hosting-related issues
3. Refer to the `TROUBLESHOOTING.md` guide

## Additional Configuration

### Custom Domain
If using a custom domain:
1. Update `SITE_URL` in `config.php`
2. Update your domain DNS settings in InfinityFree control panel

### Email Configuration
For email notifications (future feature):
1. Configure SMTP settings in `config.php`
2. Update `ADMIN_EMAIL` with your admin email address

## Backup and Maintenance

1. Regularly backup your database
2. Monitor disk space usage
3. Check file upload limits
4. Review security logs periodically