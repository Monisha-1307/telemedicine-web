<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION["user_id"])) {
    echo "<script>alert('Please log in first!'); window.location.href='login.html';</script>";
    exit();
}

$user_id = $_SESSION["user_id"];

$sql = "SELECT symptoms, status FROM consultations WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row["symptoms"]) . "</td>
                <td>" . htmlspecialchars($row["status"]) . "</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='2'>No consultation requests found</td></tr>";
}

$stmt->close();
$conn->close();
?>
