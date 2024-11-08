<?php
include 'db.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    if ($action == 'cancel') {
        $id = $_POST['id'];
        $lotId = $_POST['lotId'];
        $name = $_POST['name'];
        $reason = $_POST['reason'];

        // Optionally, you can store the cancellation reason in the database or just update the status
        $query = "UPDATE reservations SET status = 'Cancelled', cancellation_reason = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $reason, $id);

        if ($stmt->execute()) {
            // If needed, you can log this cancellation for auditing purposes
            // You can also send a notification or take other actions after cancellation

            echo json_encode([
                "success" => true,
                "message" => "Reservation for Lot ID: $lotId, Name: $name has been canceled. Reason: $reason."
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Failed to cancel reservation."
            ]);
        }
    }
}
?>
