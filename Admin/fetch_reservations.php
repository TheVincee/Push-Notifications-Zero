<?php
// Ensure proper content type for JSON response
header('Content-Type: application/json');

// Include your database connection
include 'db_connection.php';

// Initialize the response
$response = [];

// Get the last fetched notification ID (default to 0 if not provided)
$last_id = isset($_GET['last_id']) ? (int) $_GET['last_id'] : 0;

// SQL query to fetch the count of new notifications with 'Updated', 'Cancelled', or 'Deleted' status
$sql_count = "SELECT COUNT(id) AS new_count FROM notifications WHERE id > ? AND status IN ('Updated', 'Cancelled', 'Deleted')";
$stmt_count = $conn->prepare($sql_count);
if (!$stmt_count) {
    echo json_encode(["error" => "Count query preparation failed: " . $conn->error]);
    exit();
}
$stmt_count->bind_param("i", $last_id);
$stmt_count->execute();
$result_count = $stmt_count->get_result();
if (!$result_count) {
    echo json_encode(["error" => "Count query execution failed: " . $stmt_count->error]);
    exit();
}
$count_row = $result_count->fetch_assoc();

// Get the count of new notifications
$new_count = $count_row['new_count'];

// SQL query to fetch the details of new notifications with 'Updated', 'Cancelled', or 'Deleted' status
$sql_notifications = "SELECT id, lot_id, name, email, contact, status, message, notification_date, notification_time 
                      FROM notifications 
                      WHERE id > ? AND status IN ('Updated', 'Cancelled', 'Deleted') 
                      ORDER BY id ASC";
$stmt_notifications = $conn->prepare($sql_notifications);
if (!$stmt_notifications) {
    echo json_encode(["error" => "Notifications query preparation failed: " . $conn->error]);
    exit();
}
$stmt_notifications->bind_param("i", $last_id);
$stmt_notifications->execute();
$result_notifications = $stmt_notifications->get_result();
if (!$result_notifications) {
    echo json_encode(["error" => "Notifications query execution failed: " . $stmt_notifications->error]);
    exit();
}

// If there are new notifications, fetch the data
$notifications = [];
while ($row = $result_notifications->fetch_assoc()) {
    $notifications[] = $row;
}

// Prepare the response to include the notifications and new count
$response = [
    'new_count' => $new_count,
    'notifications' => $notifications
];

// Return the response as JSON
echo json_encode($response);

// Close database connections
$stmt_count->close();
$stmt_notifications->close();
$conn->close();
?>
