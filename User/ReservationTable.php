<?php
// Include the database connection
include('db.php');
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
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['lot_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['contact']); ?></td>
                    <td><?php echo htmlspecialchars($row['date']); ?></td>
                    <td><?php echo htmlspecialchars($row['time']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td>
                        <button class="btn btn-info btn-view" data-id="<?php echo $row['id']; ?>">View</button>
                        <button class="btn btn-warning btn-update" data-id="<?php echo $row['id']; ?>">Update</button>
                        <button class="btn btn-danger btn-delete" data-id="<?php echo $row['id']; ?>">Delete</button>
                        <button class="btn btn-secondary btn-cancel" data-id="<?php echo $row['id']; ?>" data-lot-id="<?php echo $row['lot_id']; ?>" data-name="<?php echo $row['name']; ?>">Cancel</button>
                    </td>
                </tr>
                <?php
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
                <div class="form-group">
                    <label for="viewStatus">Status</label>
                    <input type="text" class="form-control" id="viewStatus" readonly>
                </div>
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

<!-- JavaScript Logic -->
<script>
$(document).ready(function() {
    function handleAjaxResponse(response, successMessage, reload = false) {
        if (response.status === 'success') {
            alert(successMessage);
            if (reload) location.reload();
        } else {
            alert('Error: ' + (response.message || 'An unexpected error occurred.'));
        }
    }

    function handleAjaxError() {
        alert('An error occurred while processing your request.');
    }

    // View reservation logic
    $('.btn-view').click(function() {
        var id = $(this).data('id');
        $.ajax({
            url: 'view_reservation.php',
            method: 'GET',
            data: { id: id },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#viewLotId').val(response.data.lot_id);
                    $('#viewName').val(response.data.name);
                    $('#viewEmail').val(response.data.email);
                    $('#viewContact').val(response.data.contact);
                    $('#viewDate').val(response.data.date);
                    $('#viewTime').val(response.data.time);
                    $('#viewStatus').val(response.data.status);
                    $('#viewModal').modal('show');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: handleAjaxError
        });
    });

    // Update reservation logic
    $('.btn-update').click(function() {
        var id = $(this).data('id');
        $.ajax({
            url: 'update_reservation.php',
            method: 'GET',
            data: { id: id },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#updateId').val(response.data.id);
                    $('#updateLotId').val(response.data.lot_id);
                    $('#updateName').val(response.data.name);
                    $('#updateEmail').val(response.data.email);
                    $('#updateContact').val(response.data.contact);
                    $('#updateDate').val(response.data.date);
                    $('#updateTime').val(response.data.time);
                    $('#updateModal').modal('show');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: handleAjaxError
        });
    });

    // Handle update button click
    $('#updateButton').click(function() {
        $.ajax({
            url: 'update_reservation.php',
            method: 'POST',
            data: {
                id: $('#updateId').val(),
                lot_id: $('#updateLotId').val(),
                name: $('#updateName').val(),
                email: $('#updateEmail').val(),
                contact: $('#updateContact').val(),
                date: $('#updateDate').val(),
                time: $('#updateTime').val()
            },
            dataType: 'json',
            success: function(response) {
                handleAjaxResponse(response, 'Reservation updated successfully', true);
            },
            error: handleAjaxError
        });
    });

    // Handle delete button click
    $('.btn-delete').click(function() {
        var id = $(this).data('id');
        if (confirm("Are you sure you want to delete this reservation?")) {
            $.ajax({
                url: 'delete_reservation.php',
                method: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    handleAjaxResponse(response, 'Reservation deleted successfully', true);
                },
                error: handleAjaxError
            });
        }
    });

    // Handle cancel button click
    $('.btn-cancel').click(function() {
        var id = $(this).data('id');
        $('#cancelButton').data('id', id);
        $('#cancelLotId').val($(this).data('lot-id'));
        $('#cancelName').val($(this).data('name'));
        $('#cancelModal').modal('show');
    });

    // Handle confirm cancel action
    $('#cancelButton').click(function() {
        var id = $(this).data('id');
        var reason = $('#cancelReason').val().trim();
        if (!reason) {
            alert("Please provide a reason for cancellation.");
            return;
        }
        $.ajax({
            url: 'cancel_reservation.php',
            method: 'POST',
            data: { id: id, reason: reason },
            dataType: 'json',
            success: function(response) {
                handleAjaxResponse(response, 'Reservation cancelled successfully', true);
            },
            error: handleAjaxError
        });
    });
});
</script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
