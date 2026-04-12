<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "doctor") {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $doctor_id = $_SESSION["user_id"];
    $consultation_id = intval($_POST["consultation_id"]);
    $appointment_date = $_POST["date"];
    $appointment_time = $_POST["time"];

    // Insert into appointments table
    $stmt = $conn->prepare("INSERT INTO appointments (consultation_id, doctor_id, appointment_date, appointment_time) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $consultation_id, $doctor_id, $appointment_date, $appointment_time);

    if ($stmt->execute()) {
        echo "<script>alert('Appointment Scheduled!'); window.location.href='doctor_dashboard.html';</script>";
    } else {
        echo "<script>alert('Error scheduling appointment!'); window.location.href='schedule_appointment.php?consultation_id=$consultation_id';</script>";
    }

    $stmt->close();
}

$conn->close();
?>

