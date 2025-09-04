# ShareKoro Debugging and Troubleshooting Guide

## Overview
This guide provides information about the debugging tools included in the ShareKoro project to help identify and resolve issues during deployment.

## Debugging Files

### 1. install.php
- **Purpose**: Automated installation and system check
- **Usage**: Run once after deployment to verify system requirements
- **Features**: 
  - PHP version check
  - Extension verification
  - Database connection test
  - File permission validation
  - Creates installation lock file

### 2. debug.php
- **Purpose**: Basic system and database debugging
- **Usage**: Run to check database connectivity and system configuration
- **Features**:
  - Database connection test
  - Table verification
  - File permission check
  - PHP version info
  - Extension verification

### 3. error_debug.php
- **Purpose**: Detailed error debugging with step-by-step component testing
- **Usage**: Run when encountering specific errors to identify failure points
- **Features**:
  - Config loading test
  - Database class loading test
  - Database connection test
  - Functions loading test
  - Session initialization test
  - Key function testing

### 4. troubleshoot.php
- **Purpose**: Comprehensive troubleshooting with detailed reporting
- **Usage**: Run for complete system diagnostics
- **Features**:
  - PHP version and extension check
  - File permission verification
  - Database connectivity and table check
  - Session handling test
  - Error log analysis
  - Server information display

### 5. redirect_test.php
- **Purpose**: URL rewriting and redirect testing
- **Usage**: Run to test .htaccess configuration and URL handling
- **Features**:
  - Server information display
  - Direct file access testing
  - Redirect functionality test

### 6. index-simple.php
- **Purpose**: Simplified index file for basic functionality testing
- **Usage**: Run to verify core PHP functionality
- **Features**:
  - File existence verification
  - Config and init file loading test
  - Session handling test

### 7. index-step-by-step.php
- **Purpose**: Progressive index loading to identify failure points
- **Usage**: Run to determine where index.php fails during loading
- **Features**:
  - Step-by-step component loading
  - Detailed error reporting for each step

### 8. index-minimal.php
- **Purpose**: Minimal index file for basic functionality verification
- **Usage**: Run to test if basic HTML/PHP rendering works
- **Features**:
  - Simple HTML output
  - Basic PHP functionality test

### 9. test-init.php
- **Purpose**: init.php loading and functionality test
- **Usage**: Run to verify init.php works correctly
- **Features**:
  - Init file loading test
  - Configuration verification
  - Database connectivity test
  - Function testing
  - Session verification

### 10. test.php
- **Purpose**: Simple PHP functionality test
- **Usage**: Run to verify basic PHP execution
- **Features**:
  - Simple output test

### 11. phpinfo.php
- **Purpose**: PHP configuration information
- **Usage**: Run to view detailed PHP configuration
- **Features**:
  - Complete PHP configuration display

## Usage Instructions

1. **Initial Deployment**: Run `install.php` first to verify system requirements
2. **Basic Troubleshooting**: Run `debug.php` to check database connectivity
3. **Detailed Debugging**: Run `error_debug.php` for step-by-step component testing
4. **Comprehensive Diagnostics**: Run `troubleshoot.php` for complete system analysis
5. **URL Rewriting Issues**: Run `redirect_test.php` to test .htaccess configuration
6. **Index File Issues**: Try `index-simple.php`, `index-step-by-step.php`, or `index-minimal.php`

## Security Notes

- Delete all debugging files (`install.php`, `debug.php`, `error_debug.php`, etc.) after successful deployment
- These files may expose sensitive system information
- The `.gitignore` file is configured to exclude most debugging files in production

## Common Issues and Solutions

### 500 Internal Server Error
1. Check `php_errors.log` for specific error messages
2. Verify `.htaccess` configuration
3. Test with minimal index files
4. Run `troubleshoot.php` for complete diagnostics

### Database Connection Issues
1. Run `debug.php` to test database connectivity
2. Verify credentials in `config.php`
3. Check database server status

### File Permission Issues
1. Ensure `uploads/` directory is writable (chmod 755)
2. Check file permissions on all PHP files (chmod 644)

### Session Issues
1. Run `test-init.php` to verify session handling
2. Check session configuration in `config.php`

## Support

For additional help:
1. Check the main `TROUBLESHOOTING.md` guide
2. Review error logs
3. Contact InfinityFree support for hosting-related issues