<?php 
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    
    // First, retrieve the reservation details to notify the admin
    $stmt = $conn->prepare("SELECT * FROM reservations WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $reservation = $result->fetch_assoc();

        // Prepare to delete the reservation
        $deleteStmt = $conn->prepare("DELETE FROM reservations WHERE id = ?");
        $deleteStmt->bind_param("i", $id);

        if ($deleteStmt->execute()) {
            // After successful deletion, insert notification for the admin
            $lot_id = $reservation['lot_id'];
            $name = $reservation['name'];
            $email = $reservation['email'];
            $contact = $reservation['contact'];
            $status = "Deleted";  // Status for deletion notification
            $message = "Reservation ID $id has been deleted by the user.";

            $notificationQuery = "INSERT INTO notifications (lot_id, name, email, contact, status, message, notification_date, notification_time) 
                                  VALUES (?, ?, ?, ?, ?, ?, CURDATE(), CURTIME())";
            $notificationStmt = $conn->prepare($notificationQuery);
            $notificationStmt->bind_param("ssssss", $lot_id, $name, $email, $contact, $status, $message);
            $notificationStmt->execute();
            $notificationStmt->close();

            echo json_encode(["success" => true, "message" => "Reservation deleted successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to delete reservation."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Reservation not found."]);
    }

    $stmt->close();
    $deleteStmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}

$conn->close(); // Close the database connection
?>
