<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Redirect to the actual HTML page for the user dashboard
header("Location: user_dashboard.html");
exit();
?>
