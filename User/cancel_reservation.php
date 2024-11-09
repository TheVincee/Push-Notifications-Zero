<?php
include 'db.php'; // Assuming your database connection details are in db.php

// Check if required POST parameters are set
if (isset($_POST['id']) && isset($_POST['reason'])) {
    // Get data from AJAX request
    $id = $_POST['id'];
    $reason = $_POST['reason'];

    // Prepare and execute UPDATE query
    $query = $conn->prepare("UPDATE reservations SET status = 'Canceled', cancellation_reason = ? WHERE id = ?");
    if ($query) {
        $query->bind_param("si", $reason, $id);
        
        if ($query->execute()) {
            // Successfully updated, now fetch lot_id and Name
            $fetchQuery = $conn->prepare("SELECT lot_id, Name FROM reservations WHERE id = ?");
            
            if ($fetchQuery) {
                $fetchQuery->bind_param("i", $id);
                $fetchQuery->execute();
                $result = $fetchQuery->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();

                    // Check if Name is set and not empty
                    $name = isset($row['Name']) && !empty($row['Name']) ? $row['Name'] : "Name not available";

                    // Encode response with success and fetched data
                    echo json_encode([
                        "status" => "success",
                        "lot_id" => $row['lot_id'],
                        "Name" => $name
                    ]);
                } else {
                    // No record found
                    echo json_encode(["status" => "error", "message" => "Record not found."]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to prepare fetch query."]);
            }
        } else {
            // Error updating reservation
            echo json_encode(["status" => "error", "message" => "Failed to cancel reservation."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to prepare update query."]);
    }

    // Close the statement
    $query->close();
    if (isset($fetchQuery)) $fetchQuery->close();
} else {
    // Missing required parameters
    echo json_encode(["status" => "error", "message" => "Invalid request. Missing parameters."]);
}

// Close the database connection
$conn->close();
?>
