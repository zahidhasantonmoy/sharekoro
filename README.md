# ShareKoro

ShareKoro is a modern, secure, and anonymous file/text/code sharing platform built with PHP and MySQL. Users can share content without registration, but can also create accounts for enhanced features.

## Features

### Anonymous Sharing
- Share text snippets without registration
- Share code with syntax highlighting
- Upload and share files (subject to size limits)
- Generate unique shareable links
- Set expiration dates (1 hour, 1 day, 1 week, 1 month, never)
- Optional password protection
- View count tracking
- Content reporting system

### Registered User Features
- All anonymous features
- Save/share history
- Custom profile
- Favorites/bookmarks
- Larger file upload limits
- Extended expiration options
- Custom share URLs
- Enhanced privacy controls

### Admin Panel
- User management (promote/demote, delete)
- Content management (view/delete shares)
- Report handling (resolve/delete)
- System statistics dashboard

## Technical Implementation

### Backend
- Object-oriented PHP with MVC pattern
- PDO for database interactions
- Prepared statements for security
- Session management for authentication
- File upload handling with validation
- Input sanitization and validation
- Error handling and logging

### Frontend
- HTML5, CSS3, JavaScript (ES6+)
- Responsive design with Flexbox/Grid
- CSS animations and transitions
- AJAX for dynamic content loading
- Modern CSS features (variables, custom properties)
- Glassmorphism effects with backdrop-filter

### Security
- SQL injection prevention
- XSS protection
- CSRF protection
- File upload validation
- Rate limiting for API endpoints
- Secure password hashing (bcrypt)
- Session security

## Installation

1. Clone the repository
2. Upload files to your web server
3. Create a MySQL database and import `database_schema.sql`
4. Run `database_visibility_updates.sql` to add visibility features
5. Update `config.php` with your database credentials
6. Ensure the `uploads/` directory is writable
7. Access the site through your web browser

## Configuration

Update the following settings in `config.php`:
- Database connection details
- Site URL and name
- File upload settings
- Expiration options

## Troubleshooting

If you encounter 500 Internal Server Errors:

1. Check that all database updates have been applied (run `update_database.php`)
2. Verify PHP version compatibility (PHP 7.4 or 8.0 recommended)
3. Ensure file permissions are set correctly for `uploads/` directory
4. Check `error.log` for detailed error messages
5. Run `debug.php` to test database connectivity
6. See `TROUBLESHOOTING.md` for more detailed solutions

## Developer

**Zahid Hasan Tonmoy**
- Facebook: [zahidhasantonmoybd](https://www.facebook.com/zahidhasantonmoybd)
- LinkedIn: [zahidhasantonmoy](https://www.linkedin.com/in/zahidhasantonmoy/)
- GitHub: [zahidhasantonmoy](https://github.com/zahidhasantonmoy)
- Website: [zahidhasantonmoy.vercel.app](https://zahidhasantonmoy.vercel.app)

## License

This project is open source and available under the MIT License.