<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["error" => "Not logged in"]);
    exit();
}

$sender_id = $_SESSION["user_id"];
$receiver_id = $_POST["receiver_id"];
$message = trim($_POST["message"]);

if (!empty($message)) {
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $sender_id, $receiver_id, $message);
    if ($stmt->execute()) {
        echo json_encode(["success" => "Message sent"]);
    } else {
        echo json_encode(["error" => "Failed to send"]);
    }
    $stmt->close();
} else {
    echo json_encode(["error" => "Message cannot be empty"]);
}

$conn->close();
?>

