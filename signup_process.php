<?php
session_start();
include 'db_config.php'; // Ensure this file has the correct database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Trim and capture form inputs
    $name = isset($_POST["fullname"]) ? trim($_POST["fullname"]) : "";
    $email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
    $password = isset($_POST["password"]) ? trim($_POST["password"]) : "";
    $confirm_password = isset($_POST["confirm_password"]) ? trim($_POST["confirm_password"]) : "";
    $user_type = isset($_POST["user_type"]) ? trim($_POST["user_type"]) : "";

    // Debugging: Print received values
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password) || empty($user_type)) {
        die("<script>alert('All fields are required!'); window.history.back();</script>");
    }

    // Ensure passwords match
    if ($password !== $confirm_password) {
        die("<script>alert('Passwords do not match!'); window.history.back();</script>");
    }

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        die("<script>alert('Email already exists! Please use a different email.'); window.history.back();</script>");
    }
    $stmt->close();

    // Ensure valid user type
    $valid_user_types = ['user', 'doctor', 'admin'];
    if (!in_array($user_type, $valid_user_types)) {
        die("<script>alert('Invalid user type selected!'); window.history.back();</script>");
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into database
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, user_type) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $hashed_password, $user_type);

    if ($stmt->execute()) {
        echo "<script>alert('Signup successful! You can now log in.'); window.location.href='login.html';</script>";
    } else {
        echo "<script>alert('Signup failed: " . $conn->error . "'); window.history.back();</script>";
    }
    $stmt->close();
}
$conn->close();
?>
