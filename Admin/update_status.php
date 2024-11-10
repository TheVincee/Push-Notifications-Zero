<?php
// Include your database connection
$conn = new mysqli("localhost", "root", "", "push");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the required parameters are received via POST
if (isset($_POST['id']) && isset($_POST['status'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];

    // Update the reservation status in the database
    $update_query = "UPDATE reservations SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("si", $status, $id);

    if ($stmt->execute()) {
        // Return success response
        echo json_encode(['success' => true]);
    } else {
        // Return error message if update failed
        echo json_encode(['success' => false, 'message' => 'Failed to update status.']);
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    // Return error if required parameters are not provided
    echo json_encode(['success' => false, 'message' => 'Invalid data received.']);
}
?>
