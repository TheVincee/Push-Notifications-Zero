<?php
include 'db.php';  // Include your database connection file

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Handle GET request to fetch reservation details
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;

    if ($id) {
        $query = "SELECT * FROM reservations WHERE id = ?";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Database error preparing statement.']);
            exit();
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            echo json_encode(['success' => true, 'data' => $data]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Reservation not found.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid reservation ID.']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle POST request to update reservation details
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;
    $lot_id = $_POST['lot_id'] ?? '';
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $contact = $_POST['contact'] ?? '';
    $date = $_POST['date'] ?? '';
    $time = $_POST['time'] ?? '';

    if (!$id || empty($lot_id) || empty($name) || empty($email) || empty($contact) || empty($date) || empty($time)) {
        echo json_encode(['success' => false, 'message' => 'Please fill all required fields.']);
        exit();
    }

    $query = "UPDATE reservations SET lot_id = ?, name = ?, email = ?, contact = ?, date = ?, time = ? WHERE id = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Database error preparing statement.']);
        exit();
    }

    $stmt->bind_param("ssssssi", $lot_id, $name, $email, $contact, $date, $time, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Reservation updated successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating reservation.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
