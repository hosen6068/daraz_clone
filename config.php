<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'daraz_ecommerce');

// Create database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8");

// Define base URL
define('BASE_URL', 'http://localhost/daraz_ecommerce/');

// Define project settings
define('SESSION_TIMEOUT', 3600); // 1 hour
define('REMEMBER_ME_DURATION', 30 * 24 * 60 * 60); // 30 days

?>
