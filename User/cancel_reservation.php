<?php
// Include the database connection
include 'db.php';

// Check if required POST parameters are provided
if (isset($_POST['id']) && isset($_POST['reason'])) {
    $id = $_POST['id'];
    $reason = $_POST['reason'];

    // Check the current status of the reservation
    $statusQuery = $conn->prepare("SELECT status FROM reservations WHERE id = ?");
    if ($statusQuery) {
        $statusQuery->bind_param("i", $id);
        $statusQuery->execute();
        $statusResult = $statusQuery->get_result();

        // Proceed if reservation exists
        if ($statusResult->num_rows > 0) {
            $reservation = $statusResult->fetch_assoc();
            $currentStatus = $reservation['status'];

            // Prevent cancellation if status is 'Approved'
            if ($currentStatus == 'Approved') {
                echo json_encode(["status" => "error", "message" => "Cannot cancel an approved reservation."]);
            } else {
                // Update reservation status to 'Canceled' with the reason
                $updateQuery = $conn->prepare("UPDATE reservations SET status = 'Canceled', cancellation_reason = ? WHERE id = ?");
                if ($updateQuery) {
                    $updateQuery->bind_param("si", $reason, $id);

                    if ($updateQuery->execute()) {
                        // Retrieve additional reservation details for notification
                        $fetchQuery = $conn->prepare("SELECT lot_id, Name, email, contact FROM reservations WHERE id = ?");
                        
                        if ($fetchQuery) {
                            $fetchQuery->bind_param("i", $id);
                            $fetchQuery->execute();
                            $result = $fetchQuery->get_result();

                            if ($result->num_rows > 0) {
                                $reservationDetails = $result->fetch_assoc();

                                // Handle cases where Name is not set or is empty
                                $name = !empty($reservationDetails['Name']) ? $reservationDetails['Name'] : "Name not available";
                                $lot_id = $reservationDetails['lot_id'];
                                $email = $reservationDetails['email'];
                                $contact = $reservationDetails['contact'];
                                $status = 'Canceled';
                                $message = "Reservation ID $id has been canceled. Reason: $reason";

                                // Insert a cancellation notification for the admin
                                $notificationQuery = "
                                    INSERT INTO notifications (lot_id, name, email, contact, notification_status, message, notification_date, notification_time) 
                                    VALUES (?, ?, ?, ?, ?, ?, CURDATE(), CURTIME())
                                ";
                                $notificationStmt = $conn->prepare($notificationQuery);
                                $notificationStmt->bind_param("ssssss", $lot_id, $name, $email, $contact, $status, $message);
                                $notificationStmt->execute();
                                $notificationStmt->close();

                                // Send a success response with fetched data
                                echo json_encode([
                                    "status" => "success",
                                    "lot_id" => $lot_id,
                                    "Name" => $name
                                ]);
                            } else {
                                // No reservation record found
                                echo json_encode(["status" => "error", "message" => "Record not found."]);
                            }
                        } else {
                            echo json_encode(["status" => "error", "message" => "Failed to prepare fetch query."]);
                        }
                    } else {
                        // Handle failure to update reservation status
                        echo json_encode(["status" => "error", "message" => "Failed to cancel reservation."]);
                    }
                    // Close update and fetch statements
                    $updateQuery->close();
                    if (isset($fetchQuery)) $fetchQuery->close();
                } else {
                    echo json_encode(["status" => "error", "message" => "Failed to prepare update query."]);
                }
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Record not found."]);
        }
        $statusQuery->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to prepare status query."]);
    }
} else {
    // Handle missing parameters in request
    echo json_encode(["status" => "error", "message" => "Invalid request. Missing parameters."]);
}

// Close the database connection
$conn->close();
?>
