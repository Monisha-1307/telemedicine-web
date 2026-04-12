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

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id']; // Logged-in user ID

// Check if form data or voice data is received
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["fullname"]) && isset($_POST["age"]) && isset($_POST["gender"]) && isset($_POST["symptoms"])) {
        // Process form submission
        $fullname = trim($_POST["fullname"]);
        $age = intval($_POST["age"]);
        $gender = trim($_POST["gender"]);
        $symptoms = trim($_POST["symptoms"]);

        $stmt = $conn->prepare("INSERT INTO consultations (user_id, fullname, age, gender, symptoms) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isiss", $user_id, $fullname, $age, $gender, $symptoms);

        if ($stmt->execute()) {
            echo "<script>alert('Consultation request submitted successfully!'); window.location.href='user_dashboard.php';</script>";
        } else {
            echo "<script>alert('Error submitting consultation.'); window.location.href='consultation.php';</script>";
        }
        $stmt->close();
    } elseif (isset($_POST["voice_symptoms"])) {
        // Process voice chatbot submission
        $symptoms = trim($_POST["voice_symptoms"]);

        $stmt = $conn->prepare("INSERT INTO consultations (user_id, fullname, age, gender, symptoms) VALUES (?, '', 0, '', ?)");
        $stmt->bind_param("is", $user_id, $symptoms);

        if ($stmt->execute()) {
            echo "Voice consultation submitted successfully!";
        } else {
            echo "Error submitting voice consultation.";
        }
        $stmt->close();
    }
}

$conn->close();
?>

