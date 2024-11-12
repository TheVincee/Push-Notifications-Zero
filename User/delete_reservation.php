<?php 
// Include the database connection
include 'db.php';

// Check if the request is a POST request and 'id' is set
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    // Retrieve reservation details for admin notification purposes
    $stmt = $conn->prepare("SELECT * FROM reservations WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $reservation = $result->fetch_assoc();

        // Prepare the deletion query for the specified reservation
        $deleteStmt = $conn->prepare("DELETE FROM reservations WHERE id = ?");
        $deleteStmt->bind_param("i", $id);

        if ($deleteStmt->execute()) {
            // Extract reservation details to include in the notification
            $lot_id = $reservation['lot_id'];
            $name = $reservation['name'];
            $email = $reservation['email'];
            $contact = $reservation['contact'];
            $status = "Deleted";  
            $message = "Reservation ID $id has been deleted by the user.";

            // Insert a notification entry for the admin
            $notificationQuery = "
                INSERT INTO notifications 
                    (lot_id, name, email, contact, notification_status, message, notification_date, notification_time) 
                VALUES 
                    (?, ?, ?, ?, ?, ?, CURDATE(), CURTIME())
            ";
            $notificationStmt = $conn->prepare($notificationQuery);
            $notificationStmt->bind_param("ssssss", $lot_id, $name, $email, $contact, $status, $message);
            $notificationStmt->execute();

            // Close the notification statement and send a success response
            $notificationStmt->close();
            echo json_encode(["success" => true, "message" => "Reservation deleted successfully."]);
        } else {
            // Handle deletion failure
            echo json_encode(["success" => false, "message" => "Failed to delete reservation."]);
        }
        // Close the delete statement
        $deleteStmt->close();
    } else {
        // Reservation not found
        echo json_encode(["success" => false, "message" => "Reservation not found."]);
    }

    // Close the select statement
    $stmt->close();
} else {
    // Handle invalid request
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}

// Close the database connection
$conn->close();
?>
