<?php
// Database connection setup
$connection = new mysqli("localhost", "root", "", "push");

// Check if the database connection failed
if ($connection->connect_error) {
    die("Database connection failed: " . $connection->connect_error);
}

/**
 * Utility function to handle errors and respond with JSON.
 *
 * @param string $message The error message to log and send.
 */
function handleErrorResponse($message) {
    error_log($message); // Log the error for debugging purposes
    echo json_encode(['status' => 'error', 'message' => $message]);
    exit();
}

// Verify that essential POST data is present
if (empty($_POST['lot_id']) || empty($_POST['name']) || empty($_POST['email']) || empty($_POST['contact'])) {
    handleErrorResponse("Required fields (lot_id, name, email, contact) are missing.");
}

// Sanitize and validate input data
$lotId = intval($_POST['lot_id']);
$name = $connection->real_escape_string(trim($_POST['name']));
$email = $connection->real_escape_string(trim($_POST['email']));
$contact = $connection->real_escape_string(trim($_POST['contact']));

// Perform basic input validation
if (empty($name) || empty($email) || empty($contact) || $lotId <= 0) {
    handleErrorResponse("Missing or invalid input data.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    handleErrorResponse("Invalid email format.");
}

if (!is_numeric($contact)) {
    handleErrorResponse("Invalid contact number.");
}

// Capture the current date and time for use in the reservation and notification records
$currentDate = date('Y-m-d');
$currentTime = date('H:i:s');

// Start a database transaction to ensure all-or-nothing execution
$connection->begin_transaction();

try {
    // Check if the specified lot is already reserved
    $lotCheckQuery = "SELECT * FROM reservations WHERE lot_id = ? AND status = 'Pending'";
    $lotCheckStmt = $connection->prepare($lotCheckQuery);
    if (!$lotCheckStmt) {
        throw new Exception("Failed to prepare lot check query: " . $connection->error);
    }

    $lotCheckStmt->bind_param("i", $lotId);
    if (!$lotCheckStmt->execute()) {
        throw new Exception("Error executing lot check query: " . $lotCheckStmt->error);
    }

    $lotCheckResult = $lotCheckStmt->get_result();
    if ($lotCheckResult->num_rows > 0) {
        handleErrorResponse("This lot is already reserved.");
    }

    // Insert the new reservation into the reservations table
    $insertReservationQuery = "INSERT INTO reservations (lot_id, name, status, date, time, email, contact)
                               VALUES (?, ?, 'Pending', ?, ?, ?, ?)";
    $insertReservationStmt = $connection->prepare($insertReservationQuery);
    if (!$insertReservationStmt) {
        throw new Exception("Failed to prepare reservation insertion query: " . $connection->error);
    }

    $insertReservationStmt->bind_param("isssss", $lotId, $name, $currentDate, $currentTime, $email, $contact);
    if (!$insertReservationStmt->execute()) {
        throw new Exception("Failed to insert reservation: " . $insertReservationStmt->error);
    }

    // Create a notification entry
    $notificationMessage = "New reservation made for Lot $lotId by $name ($email)";
    $notificationStatus = "unread";
    $insertNotificationQuery = "INSERT INTO notifications (lot_id, name, email, contact, notification_status, message, notification_date, notification_time)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $insertNotificationStmt = $connection->prepare($insertNotificationQuery);
    if (!$insertNotificationStmt) {
        throw new Exception("Failed to prepare notification insertion query: " . $connection->error);
    }

    $insertNotificationStmt->bind_param("isssssss", $lotId, $name, $email, $contact, $notificationStatus, $notificationMessage, $currentDate, $currentTime);
    if (!$insertNotificationStmt->execute()) {
        throw new Exception("Failed to insert notification: " . $insertNotificationStmt->error);
    }

    // Commit the transaction to finalize the changes
    if (!$connection->commit()) {
        throw new Exception("Transaction commit failed.");
    }

    // Send a successful response back to the client
    echo json_encode(['status' => 'success', 'message' => 'Reservation successful.']);

} catch (Exception $e) {
    // Roll back the transaction to undo any partial changes
    $connection->rollback();

    // Log the error and notify the client
    error_log("Error in reservation process: " . $e->getMessage());
    handleErrorResponse("An error occurred while processing your request. Please try again.");
} finally {
    // Close all prepared statements to free up resources
    if (isset($lotCheckStmt)) $lotCheckStmt->close();
    if (isset($insertReservationStmt)) $insertReservationStmt->close();
    if (isset($insertNotificationStmt)) $insertNotificationStmt->close();

    // Close the database connection
    $connection->close();
}
?>
