<?php
session_start();
include 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (!empty($email) && !empty($password)) {
        // Get user data from the database
        $stmt = $conn->prepare("SELECT id, name, password, user_type FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $name, $hashed_password, $user_type);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                $_SESSION["user_id"] = $id;
                $_SESSION["name"] = $name;
                $_SESSION["user_type"] = $user_type;

                if ($user_type === "admin") {
                    header("Location: admin_dashboard.php");
                    die("Redirecting to admin dashboard...");
                } elseif ($user_type === "doctor") {
                    header("Location: doctor_dashboard.php");
                    die("Redirecting to doctor dashboard...");
                } else {
                    header("Location: user_dashboard.php");
                    die("Redirecting to user dashboard...");
                }                
                exit();
            } else {
                echo "<script>alert('Incorrect password!'); window.location.href='login.html';</script>";
            }
        } else {
            echo "<script>alert('Email not found!'); window.location.href='login.html';</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('All fields are required!'); window.location.href='login.html';</script>";
    }
}

$conn->close();
?>
