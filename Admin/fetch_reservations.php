<?php
// Example of fetching notifications in PHP
header('Content-Type: application/json');

$last_id = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;

// Database connection
$conn = new mysqli('localhost', 'root', '', 'push');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM notifications WHERE id > $last_id ORDER BY id DESC LIMIT 10";
$result = $conn->query($sql);

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

$new_count = count($notifications);
echo json_encode([
    'new_count' => $new_count,
    'notifications' => $notifications
]);

$conn->close();
?>
