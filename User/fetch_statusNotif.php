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

// Adjust the query to fetch only records with specific statuses
$sql = "SELECT id, lot_id, name, status
        FROM reservations
        WHERE status IN ('Approved', 'Reject', 'In Progress')
        ORDER BY id DESC"; // Order by ID if no date/time columns are available

// Execute the query
$result = $conn->query($sql);

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

// Set the content type to JSON
header('Content-Type: application/json');

// Return the notifications as a JSON response
echo json_encode($notifications);

// Close the connection
$conn->close();
?>
