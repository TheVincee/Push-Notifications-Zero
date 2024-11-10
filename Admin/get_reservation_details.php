<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "push"; // Replace with your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if reservation ID is passed
if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Query to fetch reservation details
    $sql = "SELECT * FROM reservations WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id); // Use the reservation ID as the parameter
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the reservation exists
    if ($result->num_rows > 0) {
        $reservation = $result->fetch_assoc(); // Fetch the reservation data

        // Return the reservation data as a JSON response
        echo json_encode($reservation);
    } else {
        echo json_encode(['error' => 'Reservation not found']);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'Reservation ID not provided']);
}

$conn->close();
?>
