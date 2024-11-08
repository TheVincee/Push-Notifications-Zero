<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $id = $_POST['id'];

    if ($action == 'fetch') {
        // Fetch reservation details by ID
        $query = "SELECT id, lot_id, name, email, contact, date, time FROM reservations WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $reservation = $result->fetch_assoc();
            echo json_encode($reservation);  // Return reservation details as JSON
        } else {
            echo json_encode(['error' => 'Reservation not found.']);
        }
    } elseif ($action == 'update') {
        // Fetch updated values from the form
        $lot_id = $_POST['lot_id'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $contact = $_POST['contact'];
        $date = $_POST['date'];
        $time = $_POST['time'];

        // Update the reservation details in the database
        $stmt = $conn->prepare("UPDATE reservations SET lot_id=?, name=?, email=?, contact=?, date=?, time=? WHERE id=?");
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssssi", $lot_id, $name, $email, $contact, $date, $time, $id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Reservation updated successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating reservation.']);
        }
    }
}
?>
