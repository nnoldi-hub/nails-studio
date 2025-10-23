<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'nail_studio_andreea');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8");

// Site configuration
define('SITE_NAME', 'Nail Studio Andreea');
define('SITE_URL', 'http://localhost/A_nails');
define('ADMIN_EMAIL', 'andreea@nailstudio.com');

// Session configuration
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
