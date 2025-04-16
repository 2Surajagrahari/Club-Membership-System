<?php
include "admin_check.php";
include "databases.php";
// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "clubsphere";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Get action and ID
$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Validate action
if (!in_array($action, ['approve', 'reject'])) {
    die("Invalid action");
}

// Fetch application first to get email
$stmt = $conn->prepare("SELECT * FROM membership_applications WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$application = $result->fetch_assoc();

if (!$application) {
    die("Application not found");
}

// Update status
$new_status = $action == 'approve' ? 'approved' : 'rejected';
$update = $conn->prepare("UPDATE membership_applications SET status = ? WHERE id = ?");
$update->bind_param("si", $new_status, $id);
$update->execute();

// Send notification email
$subject = "Your Membership Application Has Been " . ucfirst($action) . "ed";
$body = "
    <h2>Dear {$application['name']},</h2>
    <p>Your membership application to ClubSphere has been <strong>{$new_status}</strong>.</p>
";

if ($action == 'approve') {
    $body .= "<p>Welcome to ClubSphere! You can now login and access all member benefits.</p>";
} else {
    $body .= "<p>If you have any questions about this decision, please contact our support team.</p>";
}

$body .= "<p>Best regards,<br>The ClubSphere Team</p>";

// Use your existing email function
sendClubSphereEmail($application['email'], $application['name'], $subject, $body);

// Redirect back
header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'admin_dashboard.php'));
exit();