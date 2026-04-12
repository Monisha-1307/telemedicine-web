<?php
session_start();
include 'db_config.php';

// Ensure admin is logged in
if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "admin") {
    header("Location: login.html");
    exit();
}

// Fetch all pending consultations with patient details
$sql = "SELECT c.id AS consultation_id, u.name AS patient_name, u.age, u.gender, c.symptoms, c.status 
        FROM consultations c 
        INNER JOIN users u ON c.user_id = u.id 
        WHERE c.status = 'Pending'";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Admin Dashboard</h1>
        <nav>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main class="dashboard-container">
        <h2>Pending Consultation Requests</h2>
        
        <?php if ($result->num_rows > 0): ?>
            <table border="1">
                <thead>
                    <tr>
                        <th>Patient Name</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Symptoms</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['age']); ?></td>
                            <td><?php echo htmlspecialchars($row['gender']); ?></td>
                            <td><?php echo htmlspecialchars($row['symptoms']); ?></td>
                            <td>
                                <form action="approve_consultation.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="consultation_id" value="<?php echo $row['consultation_id']; ?>">
                                    <button type="submit">Approve</button>
                                </form>
                                <form action="reject_consultation.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="consultation_id" value="<?php echo $row['consultation_id']; ?>">
                                    <button type="submit" style="background-color:red; color:white;">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No pending consultations.</p>
        <?php endif; ?>
    </main>
</body>
</html>

<?php $conn->close(); ?>
