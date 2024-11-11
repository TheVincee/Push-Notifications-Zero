<?php
// Enable error reporting to catch any issues
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include your database connection
include 'db_connection.php';

// Initialize an array to store errors
$response = [];

// Get the last fetched notification ID (default to 0 if not provided)
$last_id = isset($_GET['last_id']) ? (int) $_GET['last_id'] : 0;

// SQL query to fetch notifications after the last ID
$sql = "SELECT id, lot_id, name, email, contact, status, message, notification_date, notification_time FROM notifications WHERE id > ? ORDER BY id ASC";
$stmt = $conn->prepare($sql);

// Check if the statement preparation failed
if ($stmt === false) {
    $response['error'] = 'SQL preparation failed: ' . $conn->error;
    echo json_encode($response);
    exit;
}

// Bind the last_id parameter to the SQL query
$stmt->bind_param("i", $last_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if there are any notifications
$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

// If no notifications found, return a message
if (empty($notifications)) {
    $response['message'] = 'No new notifications';
} else {
    $response = $notifications; // Otherwise, return the notifications data
}

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);

// Close database connections
$stmt->close();
$conn->close();
?>
