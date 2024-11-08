<?php
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
            $result = $conn->query( "SELECT * FROM reservations");
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
        // Open view modal
        $('.btn-view').on('click', function() {
            var id = $(this).data('id');
            $.get('view_reservation.php', { id: id }, function(response) {
                if (response.success) {
                    $('#viewLotId').val(response.data.lot_id);
                    $('#viewName').val(response.data.name);
                    $('#viewEmail').val(response.data.email);
                    $('#viewContact').val(response.data.contact);
                    $('#viewDate').val(response.data.date);
                    $('#viewTime').val(response.data.time);
                    $('#viewModal').modal('show');
                }
            });
        });

        // Open update modal
        $('.btn-update').on('click', function() {
            var id = $(this).data('id');
            $.get('update_reservation.php', { id: id }, function(response) {
                if (response.success) {
                    $('#updateId').val(response.data.id);
                    $('#updateLotId').val(response.data.lot_id);
                    $('#updateName').val(response.data.name);
                    $('#updateEmail').val(response.data.email);
                    $('#updateContact').val(response.data.contact);
                    $('#updateDate').val(response.data.date);
                    $('#updateTime').val(response.data.time);
                    $('#updateModal').modal('show');
                }
            });
        });

        // Update reservation
        $('#updateButton').on('click', function() {
            var id = $('#updateId').val();
            var lot_id = $('#updateLotId').val();
            var name = $('#updateName').val();
            var email = $('#updateEmail').val();
            var contact = $('#updateContact').val();
            var date = $('#updateDate').val();
            var time = $('#updateTime').val();
            
            $.post('update_reservation.php', {
                id: id,
                lot_id: lot_id,
                name: name,
                email: email,
                contact: contact,
                date: date,
                time: time
            }, function(response) {
                if (response.success) {
                    alert('Reservation updated successfully');
                    location.reload(); // Reload page to show updated data
                } else {
                    alert(response.message);
                }
            });
        });

        // Delete reservation
        $('.btn-delete').on('click', function() {
            var id = $(this).data('id');
            if (confirm('Are you sure you want to delete this reservation?')) {
                $.post('delete_reservation.php', { id: id }, function(response) {
                    if (response.success) {
                        alert('Reservation deleted successfully');
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                });
            }
        });

        // Cancel reservation
        $('.btn-cancel').on('click', function() {
            var id = $(this).data('id');
            $('#cancelButton').data('id', id);  // Store the ID for cancel action
            $('#cancelModal').modal('show');
        });

        $('#cancelButton').on('click', function() {
            var id = $(this).data('id');
            var reason = $('#cancelReason').val();
            $.post('cancel_reservation.php', { id: id, reason: reason }, function(response) {
                if (response.success) {
                    alert('Reservation cancelled successfully');
                    location.reload(); // Reload to update reservation status
                } else {
                    alert(response.message);
                }
            });
        });
    });
</script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
