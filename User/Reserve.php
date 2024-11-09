<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "push");

// Check for a database connection error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to handle errors and send JSON responses
function handleError($errorMessage) {
    error_log($errorMessage); // Log error for debugging
    echo json_encode(['status' => 'error', 'message' => $errorMessage]);
    exit();
}

// Check if required POST data is set
if (empty($_POST['lot_id']) || empty($_POST['name']) || empty($_POST['email']) || empty($_POST['contact'])) {
    handleError("Required fields (lot_id, name, email, contact) are missing.");
}

// Validate and sanitize input data
$lot_id = intval($_POST['lot_id']);
$name = $conn->real_escape_string(trim($_POST['name']));
$email = $conn->real_escape_string(trim($_POST['email']));
$contact = $conn->real_escape_string(trim($_POST['contact']));

// Basic validation checks
if (empty($name) || empty($email) || empty($contact) || $lot_id <= 0) {
    handleError("Missing or invalid input data.");
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    handleError("Invalid email format.");
}

// Ensure contact is numeric (assuming it's a phone number)
if (!is_numeric($contact)) {
    handleError("Invalid contact number.");
}

// Get current date and time for reservation and notification timestamps
$reservation_date = date('Y-m-d');
$reservation_time = date('H:i:s');

// Start a transaction to ensure atomic operations
$conn->begin_transaction();

try {
    // Check if the lot is already reserved (prevent double booking)
    $check_lot_query = "SELECT * FROM reservations WHERE lot_id = ? AND status = 'Pending'";
    $check_lot_stmt = $conn->prepare($check_lot_query);
    if (!$check_lot_stmt) {
        throw new Exception("Failed to prepare check reservation statement: " . $conn->error);
    }

    $check_lot_stmt->bind_param("i", $lot_id);
    if (!$check_lot_stmt->execute()) {
        throw new Exception("Error executing check reservation query: " . $check_lot_stmt->error);
    }

    $check_lot_result = $check_lot_stmt->get_result();

    if ($check_lot_result->num_rows > 0) {
        handleError("This lot is already reserved.");
    }

    // Prepare reservation insertion query
    $reservation_query = "INSERT INTO reservations (lot_id, name, status, date, time, email, contact) 
                          VALUES (?, ?, 'Pending', ?, ?, ?, ?)";
    $reservation_stmt = $conn->prepare($reservation_query);
    if (!$reservation_stmt) {
        throw new Exception("Failed to prepare reservation statement: " . $conn->error);
    }

    $reservation_stmt->bind_param("isssss", $lot_id, $name, $reservation_date, $reservation_time, $email, $contact);
    if (!$reservation_stmt->execute()) {
        throw new Exception("Failed to execute reservation statement: " . $reservation_stmt->error);
    }

    // Prepare notification insertion query
    $message = "New reservation made for Lot $lot_id by $name ($email)";
    $status = "unread";
    $notification_query = "INSERT INTO notifications (lot_id, name, email, contact, status, message, notification_date, notification_time) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $notification_stmt = $conn->prepare($notification_query);
    if (!$notification_stmt) {
        throw new Exception("Failed to prepare notification statement: " . $conn->error);
    }

    $notification_stmt->bind_param("isssssss", $lot_id, $name, $email, $contact, $status, $message, $reservation_date, $reservation_time);
    if (!$notification_stmt->execute()) {
        throw new Exception("Failed to execute notification statement: " . $notification_stmt->error);
    }

    // Commit transaction
    if (!$conn->commit()) {
        throw new Exception("Transaction commit failed.");
    }

    // Send a success response
    echo json_encode(['status' => 'success', 'message' => 'Reservation successful.']);

} catch (Exception $e) {
    // Roll back transaction if an error occurred
    $conn->rollback();

    // Log error and send a JSON error response
    error_log("Error in reservation process: " . $e->getMessage());
    handleError("An error occurred while processing your request. Please try again.");
} finally {
    // Close prepared statements
    if (isset($reservation_stmt) && $reservation_stmt) $reservation_stmt->close();
    if (isset($notification_stmt) && $notification_stmt) $notification_stmt->close();
    if (isset($check_lot_stmt) && $check_lot_stmt) $check_lot_stmt->close();
}

// Close the database connection
$conn->close();
?>
