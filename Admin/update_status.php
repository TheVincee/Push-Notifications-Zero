<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "push");

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if id and status are provided via POST
if (isset($_POST['id']) && isset($_POST['status'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];

    // Validate the status value to avoid any unexpected input
    $valid_statuses = ['Approved', 'Rejected', 'In Processing']; // Adjust according to your allowed statuses
    if (!in_array($status, $valid_statuses)) {
        echo 'Invalid status'; // If the status is not valid, return an error message
        exit;
    }

    // Prepare the SQL query to update the reservation status
    $query = "UPDATE reservations SET status = ? WHERE id = ?";
    if ($stmt = $conn->prepare($query)) {
        // Bind the parameters and execute the query
        $stmt->bind_param("si", $status, $id);
        
        if ($stmt->execute()) {
            echo 'success'; // Return success if the status was updated
        } else {
            echo 'error'; // Return error if the update failed
        }

        // Close the prepared statement
        $stmt->close();
    } else {
        echo 'error'; // Return error if the prepare statement failed
    }

    // Close the database connection
    $conn->close();
} else {
    echo 'error'; // Return error if id or status is not provided
}
?>
