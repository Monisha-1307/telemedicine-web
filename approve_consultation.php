<?php
session_start();
include 'db_config.php';

// Ensure admin is logged in
if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "admin") {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["consultation_id"])) {
    $consultation_id = $_POST["consultation_id"];

    // Update consultation status to "Approved"
    $stmt = $conn->prepare("UPDATE consultations SET status = 'Approved' WHERE id = ?");
    $stmt->bind_param("i", $consultation_id);

    if ($stmt->execute()) {
        echo "<script>alert('Consultation Approved! Redirecting to login page.'); window.location.href='login.html';</script>";
    } else {
        echo "<script>alert('Error approving consultation!'); window.location.href='admin_dashboard.php';</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Invalid Request!'); window.location.href='admin_dashboard.php';</script>";
}

$conn->close();
?>
