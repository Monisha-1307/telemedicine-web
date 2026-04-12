<?php
session_start();
require 'vendor/autoload.php'; // Include Google API Client
include 'db_config.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "doctor") {
    echo "<script>alert('Access denied! Please log in as a doctor.'); window.location.href='login.html';</script>";
    exit();
}

// Check if consultation_id is set
if (!isset($_GET['consultation_id'])) {
    echo "<script>alert('Invalid request!'); window.location.href='doctor_dashboard.php';</script>";
    exit();
}

$consultation_id = $_GET['consultation_id'];

// Fetch patient details
$sql = "SELECT u.email, u.name FROM users u JOIN consultations c ON u.id = c.user_id WHERE c.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $consultation_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();

if (!$patient) {
    echo "<script>alert('Patient not found!'); window.location.href='doctor_dashboard.php';</script>";
    exit();
}

// Google Calendar API Setup
$client = new Google_Client();
$client->setAuthConfig('credentials.json'); // Ensure you have your credentials.json file
$client->setScopes(Google_Service_Calendar::CALENDAR_EVENTS);
$client->setAccessType('offline');

// Authenticate using stored token
if (file_exists('token.json')) {
    $accessToken = json_decode(file_get_contents('token.json'), true);
    $client->setAccessToken($accessToken);
}

// Refresh token if expired
if ($client->isAccessTokenExpired()) {
    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
    file_put_contents('token.json', json_encode($client->getAccessToken()));
}

$service = new Google_Service_Calendar($client);

// Set appointment time (Example: Tomorrow at 10 AM)
$appointmentTime = date('Y-m-d\TH:i:s', strtotime('+1 day 10:00'));
$endTime = date('Y-m-d\TH:i:s', strtotime('+1 day 10:30'));

$event = new Google_Service_Calendar_Event([
    'summary' => 'Doctor Consultation with ' . $patient['name'],
    'description' => 'Online consultation via Google Meet',
    'start' => [
        'dateTime' => $appointmentTime,
        'timeZone' => 'Asia/Kolkata',
    ],
    'end' => [
        'dateTime' => $endTime,
        'timeZone' => 'Asia/Kolkata',
    ],
    'conferenceData' => [
        'createRequest' => [
            'conferenceSolutionKey' => ['type' => 'hangoutsMeet'],
            'requestId' => uniqid(),
        ],
    ],
    'attendees' => [
        ['email' => $patient['email']],
    ],
]);

$calendarId = 'primary';
$event = $service->events->insert($calendarId, $event, ['conferenceDataVersion' => 1]);

// Get the Google Meet link
$googleMeetLink = $event->getHangoutLink();

// Store appointment details in the database
$stmt = $conn->prepare("INSERT INTO appointments (doctor_id, consultation_id, meet_link, scheduled_time) VALUES (?, ?, ?, ?)");
$scheduled_time = date('Y-m-d H:i:s', strtotime('+1 day 10:00')); // Example: 10 AM next day
$stmt->bind_param("iiss", $_SESSION["user_id"], $consultation_id, $googleMeetLink, $scheduled_time);
$stmt->execute();

// Redirect doctor to dashboard with Meet link
echo "<script>alert('Appointment Scheduled! Google Meet link: $googleMeetLink'); window.location.href='doctor_dashboard.php';</script>";

$stmt->close();
$conn->close();
?>
