<?php
define('ROOT_PATH', __DIR__); // server file path
define('BASE_URL', '/Project/SIM'); // web root URL

// Database connection configuration
$dbHost = "localhost";
$dbUser = "root";
$dbPass = "";
$dbName = "student_erp_auth";

$conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Ensure sessions are started where needed
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>
