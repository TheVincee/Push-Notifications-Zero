<?php
// Include the database connection file
include('db.php');

header('Content-Type: application/json'); // Ensure the response is JSON

// Check if an ID is provided in the GET request
if (isset($_GET['id'])) {
    $reservationId = intval($_GET['id']); // Ensure the ID is safely converted to an integer

    // Prepare the query to fetch reservation details
    $query = "SELECT * FROM reservations WHERE id = ?";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param("i", $reservationId); // Bind the reservation ID as an integer
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if the reservation exists
        if ($result->num_rows > 0) {
            $reservationData = $result->fetch_assoc();
            echo json_encode(['success' => true, 'data' => $reservationData]);
        } else {
            // If no matching record is found
            echo json_encode(['success' => false, 'message' => 'Reservation not found']);
        }

        $stmt->close(); // Close the prepared statement
    } else {
        // Handle statement preparation failure
        echo json_encode(['success' => false, 'message' => 'Failed to prepare the database query']);
    }
} else {
    // Handle the case when no ID is provided in the request
    echo json_encode(['success' => false, 'message' => 'No reservation ID provided']);
}
?>
