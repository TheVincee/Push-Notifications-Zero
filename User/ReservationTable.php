<?php
include 'db.php'; // Database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Table</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>

<div class="container mt-5">
    <h2 class="mb-4">Reservation Table</h2>
    <table class="table table-bordered table-hover">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Lot ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM reservations");
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['lot_id'] . "</td>";
                echo "<td>" . $row['name'] . "</td>";
                echo "<td>" . $row['email'] . "</td>";
                echo "<td>" . $row['contact'] . "</td>";
                echo "<td>" . $row['date'] . "</td>";
                echo "<td>" . $row['time'] . "</td>";
                echo "<td>" . $row['status'] . "</td>";
                echo "<td>
                    <button class='btn btn-info btn-view' data-id='" . $row['id'] . "'>View</button>
                    <button class='btn btn-warning btn-update' data-id='" . $row['id'] . "'>Update</button>
                    <button class='btn btn-danger btn-delete' data-id='" . $row['id'] . "'>Delete</button>
                    <button class='btn btn-secondary btn-cancel' data-id='" . $row['id'] . "' data-lot-id='" . $row['lot_id'] . "' data-name='" . $row['name'] . "'>Cancel</button>
                </td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Modals for Update, View, and Cancel Actions -->
<!-- Update Reservation Modal -->
<div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateModalLabel">Update Reservation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="updateId">
                <div class="form-group">
                    <label for="updateLotId">Lot ID</label>
                    <input type="text" class="form-control" id="updateLotId">
                </div>
                <div class="form-group">
                    <label for="updateName">Name</label>
                    <input type="text" class="form-control" id="updateName">
                </div>
                <div class="form-group">
                    <label for="updateEmail">Email</label>
                    <input type="email" class="form-control" id="updateEmail">
                </div>
                <div class="form-group">
                    <label for="updateContact">Contact</label>
                    <input type="text" class="form-control" id="updateContact">
                </div>
                <div class="form-group">
                    <label for="updateDate">Date</label>
                    <input type="date" class="form-control" id="updateDate">
                </div>
                <div class="form-group">
                    <label for="updateTime">Time</label>
                    <input type="time" class="form-control" id="updateTime">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="updateButton">Update Reservation</button>
            </div>
        </div>
    </div>
</div>

<!-- View Reservation Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">View Reservation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="viewLotId">Lot ID</label>
                    <input type="text" class="form-control" id="viewLotId" readonly>
                </div>
                <div class="form-group">
                    <label for="viewName">Name</label>
                    <input type="text" class="form-control" id="viewName" readonly>
                </div>
                <div class="form-group">
                    <label for="viewEmail">Email</label>
                    <input type="email" class="form-control" id="viewEmail" readonly>
                </div>
                <div class="form-group">
                    <label for="viewContact">Contact</label>
                    <input type="text" class="form-control" id="viewContact" readonly>
                </div>
                <div class="form-group">
                    <label for="viewDate">Date</label>
                    <input type="date" class="form-control" id="viewDate" readonly>
                </div>
                <div class="form-group">
                    <label for="viewTime">Time</label>
                    <input type="time" class="form-control" id="viewTime" readonly>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Reservation Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelModalLabel">Cancel Reservation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="cancelId">
                <div class="form-group">
                    <label for="cancelLotId">Lot ID</label>
                    <input type="text" class="form-control" id="cancelLotId" readonly>
                </div>
                <div class="form-group">
                    <label for="cancelName">Name</label>
                    <input type="text" class="form-control" id="cancelName" readonly>
                </div>
                <div class="form-group">
                    <label for="cancelReason">Reason for Cancellation</label>
                    <textarea class="form-control" id="cancelReason" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" id="cancelButton">Cancel Reservation</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Common function to open modal and populate data
    function openModal(modalId, data) {
        for (let key in data) {
            $(`#${modalId} #${key}`).val(data[key]);
        }
        $(`#${modalId}`).modal('show');
    }

    // Update button action
    $('.btn-update').on('click', function() {
        const id = $(this).data('id');
        $.post('update_reservation.php', { action: 'fetch', id: id }, function(response) {
            openModal('updateModal', response);
        }, 'json');
    });

    // View button action
    $('.btn-view').on('click', function() {
        const id = $(this).data('id');
        $.post('view_reservation.php', { id: id }, function(response) {
            openModal('viewModal', response);
        }, 'json');
    });

    // Delete button action
    $('.btn-delete').on('click', function() {
        const id = $(this).data('id');
        if (confirm('Are you sure you want to delete this reservation?')) {
            $.post('delete_reservation.php', { id: id }, function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error deleting reservation.');
                }
            }, 'json');
        }
    });

    // Cancel button action
    $('.btn-cancel').on('click', function() {
        const id = $(this).data('id');
        const lot_id = $(this).data('lot-id');
        const name = $(this).data('name');
        openModal('cancelModal', { cancelId: id, cancelLotId: lot_id, cancelName: name });
    });

    // Confirm update
    $('#updateButton').on('click', function() {
        const data = {
            id: $('#updateId').val(),
            lot_id: $('#updateLotId').val(),
            name: $('#updateName').val(),
            email: $('#updateEmail').val(),
            contact: $('#updateContact').val(),
            date: $('#updateDate').val(),
            time: $('#updateTime').val()
        };
        $.post('update_reservation.php', { action: 'update', ...data }, function(response) {
            if (response.success) {
                $('#updateModal').modal('hide');
                location.reload();
            } else {
                alert('Error updating reservation.');
            }
        }, 'json');
    });

    // Confirm cancel
    $('#cancelButton').on('click', function() {
        const data = {
            id: $('#cancelId').val(),
            reason: $('#cancelReason').val()
        };
        $.post('cancel_reservation.php', data, function(response) {
            if (response.success) {
                $('#cancelModal').modal('hide');
                location.reload();
            } else {
                alert('Error canceling reservation.');
            }
        }, 'json');
    });
});
</script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
