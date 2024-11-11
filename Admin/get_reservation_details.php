<?php
$conn = new mysqli("localhost", "root", "", "push");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_POST['id'];
$query = "SELECT lot_id, name, email, contact, date, time, status FROM reservations WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $reservation = $result->fetch_assoc();
    echo json_encode([
        'success' => true,
        'data' => [
            'lot_id' => $reservation['lot_id'],
            'name' => $reservation['name'],
            'email' => $reservation['email'],
            'contact' => $reservation['contact'],
            'reservation_date' => $reservation['date'],
            'reservation_time' => $reservation['time'],
            'status' => $reservation['status']
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'No reservation found.']);
}

$stmt->close();
$conn->close();
?>
