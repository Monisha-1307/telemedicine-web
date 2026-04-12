<?php
$host = "localhost";  // Your database host
$user = "root";       // Your database username
$pass = "";           // Your database password (for XAMPP, it's usually empty)
$dbname = "telemedicine";  // Your database name

$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
