<?php
// Database connection
$servername = "localhost";  // Change to your server's address
$username = "root";         // Your database username
$password = "";             // Your database password
$dbname = "push";  // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to fetch notifications where the status is "Updated", "Cancel", or "Delete"
$sql = "SELECT id, lot_id, name, email, contact, notification_status, message, notification_date, notification_time 
        FROM notifications 
        WHERE notification_status IN ('Updated', 'Cancel', 'Delete') 
        ORDER BY notification_date DESC, notification_time DESC LIMIT 10";

$result = $conn->query($sql);

// Check if any notifications were found
$notifications = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
}

// Count of new notifications
$new_count = count($notifications);

// Return the notifications and count in JSON format
echo json_encode([
    'new_count' => $new_count,
    'notifications' => $notifications
]);

// Close connection
$conn->close();
?>
