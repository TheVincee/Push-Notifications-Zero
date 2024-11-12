<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "push"; // Replace with the actual database name

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if a reservation ID was provided in the POST request
if (isset($_POST['reservation_id'])) {
    // Sanitize and assign the reservation ID
    $reservation_id = (int) $_POST['reservation_id'];

    // Prepare a SQL query to retrieve reservation details using a prepared statement
    $sql = "SELECT * FROM reservations WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $reservation_id); // Bind the reservation ID as an integer parameter

    // Execute the query
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if any reservation was found
    if ($result->num_rows > 0) {
        $reservation = $result->fetch_assoc(); // Fetch reservation details as an associative array

        // Respond with reservation data in JSON format
        echo json_encode(['success' => true, 'data' => $reservation]);
    } else {
        // Respond with an error if no reservation was found
        echo json_encode(['success' => false, 'error' => 'Reservation not found']);
    }

    // Close the prepared statement
    $stmt->close();
} else {
    // Respond with an error if reservation ID is missing
    echo json_encode(['success' => false, 'error' => 'Reservation ID not provided']);
}

// Close the database connection
$conn->close();
?>
