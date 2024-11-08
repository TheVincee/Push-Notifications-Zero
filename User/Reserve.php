<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "push");

// Check if there's an error with the database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to handle errors and display error messages
function handleError($errorMessage) {
    echo json_encode(['error' => $errorMessage]);
    exit();
}

// Check if the 'lot_id', 'name', 'email', and 'contact' are set in the POST request
if (isset($_POST['lot_id'], $_POST['name'], $_POST['email'], $_POST['contact'])) {
    // Get the reservation data from the form
    $lot_id = $_POST['lot_id'];  // 'lot_id' from the form
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $reservation_date = date('Y-m-d'); // Current date
    $reservation_time = date('H:i:s'); // Current time

    // Begin transaction to ensure atomic operations (reservation + notification)
    $conn->begin_transaction();

    try {
        // Prepare and execute reservation query
        $reservation_query = "INSERT INTO reservations (lot_id, name, status, date, time, email, contact) 
                              VALUES (?, ?, 'Pending', ?, ?, ?, ?)";
        $reservation_stmt = $conn->prepare($reservation_query);
        if ($reservation_stmt === false) {
            throw new Exception("Prepare failed for reservation: " . $conn->error);
        }

        $reservation_stmt->bind_param("isssss", $lot_id, $name, $reservation_date, $reservation_time, $email, $contact);
        if (!$reservation_stmt->execute()) {
            throw new Exception("Reservation failed: " . $reservation_stmt->error);
        }

        // Create notification for the admin
        $message = "New reservation made for Lot " . $lot_id . " by " . $name . " (" . $email . ")";
        $status = "unread"; // Notification status

        // Prepare and execute notification query
        $notification_query = "INSERT INTO notifications (lot_id, name, email, contact, status, message, notification_date, notification_time) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $notification_stmt = $conn->prepare($notification_query);
        if ($notification_stmt === false) {
            throw new Exception("Prepare failed for notification: " . $conn->error);
        }

        $notification_stmt->bind_param("isssssss", $lot_id, $name, $email, $contact, $status, $message, $reservation_date, $reservation_time);
        if (!$notification_stmt->execute()) {
            throw new Exception("Notification insertion failed: " . $notification_stmt->error);
        }

        // Commit transaction if everything is successful
        $conn->commit();

        // Success response
        echo json_encode(['status' => 'success']);

    } catch (Exception $e) {
        // Rollback transaction in case of error
        $conn->rollback();
        
        // Log error and return the message for debugging
        error_log($e->getMessage()); // Log error to server logs
        handleError("Error processing your request: " . $e->getMessage());
    } finally {
        // Clean up statements
        $reservation_stmt->close();
        $notification_stmt->close();
    }
} else {
    // Handle the case when required fields are missing
    handleError("Required fields (lot_id, name, email, contact) are missing.");
}

// Close connection
$conn->close();
?>
