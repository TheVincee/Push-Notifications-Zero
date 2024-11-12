<?php
// Connect to the database
$connection = new mysqli("localhost", "root", "", "push");

// Check if the database connection was successful
if ($connection->connect_error) {
    die("Database connection failed: " . $connection->connect_error);
}

// Check if the required 'id' and 'status' parameters are provided in the POST request
if (isset($_POST['id']) && isset($_POST['status'])) {
    // Sanitize and assign the POST data to variables
    $reservationId = (int) $_POST['id'];  // Cast 'id' to an integer for security
    $reservationStatus = $connection->real_escape_string($_POST['status']); // Sanitize 'status' for SQL safety

    // Prepare the SQL statement to update the reservation status
    $updateQuery = "UPDATE reservations SET status = ? WHERE id = ?";
    $stmt = $connection->prepare($updateQuery);
    
    if ($stmt) {
        // Bind the parameters to the SQL query (string for status, integer for id)
        $stmt->bind_param("si", $reservationStatus, $reservationId);

        // Execute the query and check for successful update
        if ($stmt->execute()) {
            // Respond with a success message
            echo json_encode(['success' => true]);
        } else {
            // Respond with an error message if the update fails
            echo json_encode(['success' => false, 'message' => 'Failed to update the reservation status.']);
        }

        // Cleanup: close the prepared statement
        $stmt->close();
    } else {
        // Handle error in preparing the statement
        echo json_encode(['success' => false, 'message' => 'Database error: Unable to prepare statement.']);
    }
} else {
    // Respond with an error message if 'id' or 'status' is missing
    echo json_encode(['success' => false, 'message' => 'Invalid data received.']);
}

// Cleanup: close the database connection
$connection->close();
?>
