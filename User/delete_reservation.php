<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $stmt = $conn->prepare("DELETE FROM reservations WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Reservation deleted successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to delete reservation."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}
?>
