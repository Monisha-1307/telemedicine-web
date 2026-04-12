<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Database connection
$host = "localhost";
$username = "root";
$password = "";
$database = "telemedicine";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $name = trim($_POST["name"]);
    $age = trim($_POST["age"]);
    $gender = trim($_POST["gender"]);
    $symptoms = trim($_POST["symptoms"]);

    if (!empty($name) && !empty($age) && !empty($gender) && !empty($symptoms)) {
        $stmt = $conn->prepare("INSERT INTO consultations (user_id, name, age, gender, symptoms) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $user_id, $name, $age, $gender, $symptoms);
        
        if ($stmt->execute()) {
            echo "<script>alert('Consultation request submitted successfully!'); window.location.href='user_dashboard.html';</script>";
        } else {
            echo "<script>alert('Error submitting consultation request.'); window.location.href='consult_doctor.html';</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('All fields are required!'); window.location.href='consult_doctor.html';</script>";
    }
}

$conn->close();
?>
