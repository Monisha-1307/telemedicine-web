<?php
session_start();
include 'db_config.php'; // Ensure this file contains the database connection

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    echo "<script>alert('Please log in first!'); window.location.href='login.html';</script>";
    exit();
}

// Process the form when submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION["user_id"];
    $symptoms = trim($_POST["symptoms"]);

    // Validate inputs
    if (empty($symptoms)) {
        echo "<script>alert('Symptoms field is required!'); window.location.href='consult_doctor.html';</script>";
        exit();
    }

    // Insert into the database
    $stmt = $conn->prepare("INSERT INTO consultations (user_id, symptoms, status) VALUES (?, ?, 'Pending')");
    $stmt->bind_param("is", $user_id, $symptoms);

    if ($stmt->execute()) {
        echo "<script>alert('Consultation request submitted successfully!'); window.location.href='user_dashboard.html';</script>";
    } else {
        echo "<script>alert('Error submitting request!'); window.location.href='consult_doctor.html';</script>";
    }

    $stmt->close();
}

$conn->close();
?>
