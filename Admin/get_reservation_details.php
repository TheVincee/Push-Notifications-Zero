<?php
$conn = new mysqli("localhost", "root", "", "push");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_POST['id'];
$query = "
    SELECT 
        reservations.lot_id, 
        reservations.name, 
        reservations.email, 
        reservations.contact, 
        reservations.date, 
        reservations.time, 
        reservations.status,
        reservations.cancellation_reason,
        notifications.notification_status,
        notifications.message,
        notifications.notification_date,
        notifications.notification_time
    FROM reservations
    LEFT JOIN notifications ON reservations.lot_id = notifications.lot_id
    WHERE reservations.id = ?
";

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
            'status' => $reservation['status'],
            'cancellation_reason' => $reservation['cancellation_reason'] ?? 'N/A',
            'notification_status' => $reservation['notification_status'] ?? 'N/A',
            'message' => $reservation['message'] ?? 'N/A',
            'notification_date' => $reservation['notification_date'] ?? 'N/A',
            'notification_time' => $reservation['notification_time'] ?? 'N/A'
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'No reservation found.']);
}

$stmt->close();
$conn->close();
?>
