<?php
// email_config.php - Should be outside public_html/htdocs if possible

// SMTP Configuration (Gmail example)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587); // Added missing port
define('SMTP_USERNAME', 'surajagrahari265@gmail.com');
define('SMTP_PASSWORD', 'dppp wzte fdck lclw'); // Use App Password, not your regular password
define('SMTP_FROM_EMAIL', 'surajagrahari265@gmail.com'); // Should match your domain
define('SMTP_FROM_NAME', 'ClubSphere');
define('SMTP_SECURE', 'tls'); // Encryption method

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'clubsphere');

// Prevent direct access
if (basename($_SERVER['PHP_SELF']) === 'email_config.php') {
    header('HTTP/1.0 403 Forbidden');
    exit;
}
?>