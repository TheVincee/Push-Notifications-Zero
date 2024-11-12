<?php
// Include the database connection file
include 'db.php';

header('Content-Type: application/json');

// Handle GET requests for fetching reservation details
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $reservationId = isset($_GET['id']) ? intval($_GET['id']) : null;

    if ($reservationId) {
        $query = "SELECT * FROM reservations WHERE id = ?";
        $stmt = $conn->prepare($query);

        // Check if the statement preparation was successful
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Error preparing database statement.']);
            exit();
        }

        $stmt->bind_param("i", $reservationId);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if a reservation was found
        if ($result->num_rows > 0) {
            $reservationDetails = $result->fetch_assoc();
            echo json_encode(['success' => true, 'data' => $reservationDetails]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Reservation not found.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid reservation ID provided.']);
    }
}
// Handle POST requests for updating reservation details
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reservationId = isset($_POST['id']) ? intval($_POST['id']) : null;
    $lotId = $_POST['lot_id'] ?? '';
    $customerName = $_POST['name'] ?? '';
    $customerEmail = $_POST['email'] ?? '';
    $customerContact = $_POST['contact'] ?? '';
    $reservationDate = $_POST['date'] ?? '';
    $reservationTime = $_POST['time'] ?? '';
    $notificationStatus = "Updated"; // Status for the notification

    // Validate input fields
    if (!$reservationId || empty($lotId) || empty($customerName) || empty($customerEmail) || empty($customerContact) || empty($reservationDate) || empty($reservationTime)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit();
    }

    // Step 1: Check if the reservation status is 'Approved'
    $statusCheckQuery = "SELECT status FROM reservations WHERE id = ?";
    $statusStmt = $conn->prepare($statusCheckQuery);
    $statusStmt->bind_param("i", $reservationId);
    $statusStmt->execute();
    $statusResult = $statusStmt->get_result();

    if ($statusResult->num_rows > 0) {
        $statusData = $statusResult->fetch_assoc();

        // Prevent updates if the reservation is already approved
        if ($statusData['status'] === 'Approved') {
            echo json_encode(['success' => false, 'message' => 'This reservation has been approved and cannot be updated.']);
            exit();
        }
    }

    // Step 2: Proceed with the update if the status is not 'Approved'
    $updateQuery = "UPDATE reservations SET lot_id = ?, name = ?, email = ?, contact = ?, date = ?, time = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateQuery);

    if (!$updateStmt) {
        echo json_encode(['success' => false, 'message' => 'Error preparing database statement for update.']);
        exit();
    }

    $updateStmt->bind_param("ssssssi", $lotId, $customerName, $customerEmail, $customerContact, $reservationDate, $reservationTime, $reservationId);

    // Execute the update and handle notifications
    if ($updateStmt->execute()) {
        $notificationQuery = "INSERT INTO notifications (lot_id, name, email, contact, notification_status, message, notification_date, notification_time) 
                              VALUES (?, ?, ?, ?, ?, ?, CURDATE(), CURTIME())";
        $notificationStmt = $conn->prepare($notificationQuery);

        if ($notificationStmt) {
            $notificationMessage = "User updated reservation ID $reservationId";
            $notificationStmt->bind_param("ssssss", $lotId, $customerName, $customerEmail, $customerContact, $notificationStatus, $notificationMessage);
            $notificationStmt->execute();
            $notificationStmt->close();
        }

        echo json_encode(['success' => true, 'message' => 'Reservation updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating reservation.']);
    }

    $updateStmt->close();
} else {
    // Handle unsupported request methods
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
