<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "push");

// Check for database connection error
if ($conn->connect_error) {
    handleError("Connection failed: " . $conn->connect_error);
}

// Function to handle errors and send JSON error responses
function handleError($errorMessage) {
    error_log($errorMessage); // Log error for debugging
    echo json_encode(['status' => 'error', 'message' => $errorMessage]);
    exit();
}

// Check if required POST data is set
if (!isset($_POST['lot_id'], $_POST['name'], $_POST['email'], $_POST['contact'])) {
    handleError("Required fields (lot_id, name, email, contact) are missing.");
}

// Fetch and sanitize the form data
$lot_id = intval($_POST['lot_id']);  // Sanitize to prevent SQL injection
$name = mysqli_real_escape_string($conn, $_POST['name']);  // Escape special characters
$email = mysqli_real_escape_string($conn, $_POST['email']);  // Escape special characters
$contact = mysqli_real_escape_string($conn, $_POST['contact']);  // Escape special characters
$reservation_date = date('Y-m-d');  // Current date
$reservation_time = date('H:i:s');  // Current time

// Start a transaction to ensure atomic operations (reservation + notification)
$conn->begin_transaction();

try {
    // Prepare and execute reservation query
    $reservation_query = "INSERT INTO reservations (lot_id, name, status, date, time, email, contact) 
                          VALUES (?, ?, 'Pending', ?, ?, ?, ?)";
    $reservation_stmt = $conn->prepare($reservation_query);
    if ($reservation_stmt === false) {
        throw new Exception("Failed to prepare reservation statement: " . $conn->error);
    }

    $reservation_stmt->bind_param("isssss", $lot_id, $name, $reservation_date, $reservation_time, $email, $contact);
    if (!$reservation_stmt->execute()) {
        throw new Exception("Failed to execute reservation statement: " . $reservation_stmt->error);
    }

    // Prepare and execute notification query
    $message = "New reservation made for Lot $lot_id by $name ($email)";
    $status = "unread";

    $notification_query = "INSERT INTO notifications (lot_id, name, email, contact, status, message, notification_date, notification_time) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $notification_stmt = $conn->prepare($notification_query);
    if ($notification_stmt === false) {
        throw new Exception("Failed to prepare notification statement: " . $conn->error);
    }

    $notification_stmt->bind_param("isssssss", $lot_id, $name, $email, $contact, $status, $message, $reservation_date, $reservation_time);
    if (!$notification_stmt->execute()) {
        throw new Exception("Failed to execute notification statement: " . $notification_stmt->error);
    }

    // Commit transaction
    $conn->commit();

    // Send success response
    echo json_encode(['status' => 'success']);

} catch (Exception $e) {
    // Rollback transaction in case of any errors
    $conn->rollback();

    // Log error for debugging
    error_log("Error in reservation process: " . $e->getMessage());

    // Send error response to client
    handleError("An error occurred while processing your request. Please try again.");
} finally {
    // Close prepared statements
    if (isset($reservation_stmt) && $reservation_stmt) $reservation_stmt->close();
    if (isset($notification_stmt) && $notification_stmt) $notification_stmt->close();
}

// Close the database connection
$conn->close();
?>
