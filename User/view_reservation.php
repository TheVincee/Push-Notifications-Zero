<?php
include 'db.php';

$id = $_GET['id'];
$sql = "SELECT * FROM reservations WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(['success' => true, 'data' => $row]);
} else {
    echo json_encode(['success' => false, 'message' => 'Reservation not found']);
}
?>
