<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "doctor") {
    echo "<script>alert('Access denied! Please log in as a doctor.'); window.location.href='login.html';</script>";
    exit();
}

// Fetch all approved consultations
$sql = "SELECT c.id AS consultation_id, u.name AS patient_name, u.age, u.gender, c.symptoms, c.appointment_date, c.appointment_time, c.meeting_link 
        FROM consultations c
        JOIN users u ON c.user_id = u.id
        WHERE c.status = 'Approved'";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Doctor Dashboard</h1>
        <nav>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main class="dashboard-container">
        <h2>Approved Patient Consultations</h2>
        
        <table border="1">
            <thead>
                <tr>
                    <th>Patient Name</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Symptoms</th>
                    <th>Appointment Date</th>
                    <th>Appointment Time</th>
                    <th>Meeting Link</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row["patient_name"]); ?></td>
                            <td><?php echo htmlspecialchars($row["age"]); ?></td>
                            <td><?php echo htmlspecialchars($row["gender"]); ?></td>
                            <td><?php echo htmlspecialchars($row["symptoms"]); ?></td>
                            <td><?php echo $row["appointment_date"] ? htmlspecialchars($row["appointment_date"]) : "Not Scheduled"; ?></td>
                            <td><?php echo $row["appointment_time"] ? htmlspecialchars($row["appointment_time"]) : "Not Scheduled"; ?></td>
                            <td>
                                <?php if ($row["meeting_link"]): ?>
                                    <a href="<?php echo htmlspecialchars($row["meeting_link"]); ?>" target="_blank">Join Meeting</a>
                                <?php else: ?>
                                    Not Available
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="schedule_appointment.php?consultation_id=<?php echo $row["consultation_id"]; ?>">Schedule</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="8">No approved consultations available.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>

</body>
</html>

<?php
$conn->close();
?>
