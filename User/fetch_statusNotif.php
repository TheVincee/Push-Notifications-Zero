<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection (adjust credentials accordingly)
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'push';

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Check if 'last_id' is passed in the request (for fetching notifications after the last seen notification)
$lastNotificationId = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;

// Adjust the query to fetch only records with specific statuses and after the last seen notification ID
$sql = "SELECT id, lot_id, name, date, time, email, contact, cancellation_reason, status
        FROM reservations
        WHERE id > ? AND status IN ('Approved', 'Reject', 'In Progress') 
        ORDER BY id DESC"; // Order by ID for the most recent notifications

// Prepare the statement to prevent SQL injection
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    // Output error if the query preparation fails
    die(json_encode(["error" => "SQL prepare error: " . $conn->error]));
}

// Bind the lastNotificationId parameter
$stmt->bind_param('i', $lastNotificationId);

// Execute the statement
$stmt->execute();

// Get the result of the query
$result = $stmt->get_result();

// Check if query executed successfully
if (!$result) {
    die(json_encode(["error" => "Query failed: " . $conn->error]));
}

// Prepare an array to store the notifications
$notifications = [];

if ($result->num_rows > 0) {
    // Fetch all notifications
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
}

// Determine the number of new notifications
$newCount = count($notifications);

// Set the content type to JSON
header('Content-Type: application/json');

// Return the notifications and new_count as a JSON response
echo json_encode([
    'new_count' => $newCount,
    'notifications' => $notifications
]);

// Close the statement and connection
$stmt->close();
$conn->close();
?>
