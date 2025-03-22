<?php
$host = "localhost";  // MySQL Server
$user = "anu";       // Change if needed
$pass = "anushka@13";           // Leave empty if using XAMPP
$dbname = "college_complaint_portal";  

// Create MySQL Connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check Connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
