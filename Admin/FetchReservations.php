<?php
// Include the database connection file
include('db_connection.php');

// Fetch the ID from the URL query string
$id = isset($_GET['id']) ? $_GET['id'] : '';

// Log the received ID for debugging
error_log("Received ID: " . $id);

// Check if ID is provided and is numeric (assuming ID is a number)
if (empty($id) || !is_numeric($id)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing ID.']);
    exit();
}

// Prepare the SQL query to fetch notifications based on the ID
$sql = "SELECT * FROM notifications WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id); // 'i' for integer
$stmt->execute();
$result = $stmt->get_result();

// Check if there are results
if ($result->num_rows > 0) {
    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }

    // Return the results in JSON format
    echo json_encode(['status' => 'success', 'data' => $notifications]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No notifications found for the given ID.']);
}

// Close the database connection
$stmt->close();
$conn->close();
?>
