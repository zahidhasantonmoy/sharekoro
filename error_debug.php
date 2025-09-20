<?php
// error_debug.php - Custom error page with debugging information

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log');

http_response_code(500);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 Internal Server Error - ShareKoro</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            padding: 40px;
            max-width: 600px;
            text-align: center;
            animation: fadeIn 0.5s ease-out;
        }
        .error-icon {
            font-size: 60px;
            color: #e74c3c;
            margin-bottom: 20px;
        }
        h1 {
            color: #2c3e50;
            margin-bottom: 15px;
        }
        p {
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .error-details {
            background: #f8f9fa;
            border-left: 4px solid #e74c3c;
            padding: 15px;
            text-align: left;
            margin: 20px 0;
            border-radius: 0 5px 5px 0;
        }
        .btn {
            background: #3498db;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin: 10px;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #2980b9;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h1>500 Internal Server Error</h1>
        <p>Something went wrong on our server. We're working to fix the issue.</p>
        
        <div class="error-details">
            <strong>Error Details:</strong><br>
            <?php
            if (isset($_SERVER['REDIRECT_STATUS']) && $_SERVER['REDIRECT_STATUS'] == 500) {
                echo "Server Error (500)<br>";
            }
            if (function_exists('error_get_last')) {
                $error = error_get_last();
                if ($error) {
                    echo "Type: " . $error['type'] . "<br>";
                    echo "Message: " . htmlspecialchars($error['message']) . "<br>";
                    echo "File: " . $error['file'] . "<br>";
                    echo "Line: " . $error['line'] . "<br>";
                }
            }
            ?>
        </div>
        
        <p>Try the following:</p>
        <ul style="text-align: left; display: inline-block;">
            <li>Refresh the page in a few minutes</li>
            <li>Check if you're using the correct URL</li>
            <li>Contact the site administrator if the problem persists</li>
        </ul>
        
        <div>
            <a href="index.php" class="btn">
                <i class="fas fa-home"></i> Return Home
            </a>
            <a href="mailto:<?php echo defined('ADMIN_EMAIL') ? ADMIN_EMAIL : 'admin@localhost'; ?>" class="btn">
                <i class="fas fa-envelope"></i> Contact Support
            </a>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>