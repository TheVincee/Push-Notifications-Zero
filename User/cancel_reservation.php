<?php
include 'db.php';

$id = $_POST['id'];
$reason = $_POST['reason'];

$query = $conn->prepare("UPDATE reservations SET status = 'Canceled', cancellation_reason = ? WHERE id = ?");
$query->bind_param("si", $reason, $id);

if ($query->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to cancel reservation."]);
}
?>
