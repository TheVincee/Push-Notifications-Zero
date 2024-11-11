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
                <!-- Added View Status -->
                <div class="form-group">
                    <label for="viewStatus">Status</label>
                    <input type="text" class="form-control" id="viewStatus" readonly>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Error -->
<div id="errorModal" class="modal fade" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="errorModalLabel">Update Error</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p id="errorMessage"></p> <!-- Dynamic error message will be inserted here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
                <!-- Lot ID Input -->
                <div class="form-group">
                    <label for="cancelLotId">Lot ID</label>
                    <input type="text" class="form-control" id="cancelLotId" readonly>
                </div>
                
                <!-- Name Input -->
                <div class="form-group">
                    <label for="cancelName">Name</label>
                    <input type="text" class="form-control" id="cancelName" readonly>
                </div>

                <!-- Reason for Cancellation -->
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
    
    // Function to handle AJAX success responses
    function handleAjaxSuccess(response, successMessage, reloadPage = false) {
        if (response.success) {
            alert(successMessage);
            if (reloadPage) {
                location.reload();  // Reload the page if necessary
            }
        } else {
            alert('Error: ' + response.message || "An error occurred.");
        }
    }

    // Function to handle AJAX errors
    function handleAjaxError() {
        alert('An error occurred while communicating with the server.');
    }

    // Open view modal
    $('.btn-view').on('click', function() {
        var id = $(this).data('id');
        
        // Make the AJAX request to fetch the reservation data
        $.ajax({
            url: 'view_reservation.php',
            method: 'GET',
            data: { id: id },
            dataType: 'json',
            success: function(response) {
                try {
                    if (response.success) {
                        // Populate the modal with the fetched data
                        $('#viewLotId').val(response.data.lot_id || '');
                        $('#viewName').val(response.data.name || '');
                        $('#viewEmail').val(response.data.email || '');
                        $('#viewContact').val(response.data.contact || '');
                        $('#viewDate').val(response.data.date || '');
                        $('#viewTime').val(response.data.time || '');
                        $('#viewStatus').val(response.data.status || '');
                        $('#viewModal').modal('show');
                    } else {
                        alert('Error: ' + response.message);
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                    alert('An error occurred while fetching the reservation details.');
                }
            },
            error: handleAjaxError
        });
    });

    // Open update modal
    $('.btn-update').on('click', function() {
        var id = $(this).data('id');
        $.ajax({
            url: 'update_reservation.php',
            method: 'GET',
            data: { id: id },
            dataType: 'json',
            success: function(response) {
                try {
                    if (response.success) {
                        $('#updateId').val(response.data.id);
                        $('#updateLotId').val(response.data.lot_id);
                        $('#updateName').val(response.data.name);
                        $('#updateEmail').val(response.data.email);
                        $('#updateContact').val(response.data.contact);
                        $('#updateDate').val(response.data.date);
                        $('#updateTime').val(response.data.time);
                        $('#updateModal').modal('show');
                    } else {
                        alert('Error: ' + response.message);  // Handle error in fetching reservation data
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                    alert('An error occurred while fetching reservation details for update.');
                }
            },
            error: handleAjaxError
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
        
        $.ajax({
            url: 'update_reservation.php',
            method: 'POST',
            data: {
                id: id,
                lot_id: lot_id,
                name: name,
                email: email,
                contact: contact,
                date: date,
                time: time
            },
            dataType: 'json',
            success: function(response) {
                handleAjaxSuccess(response, 'Reservation updated successfully', true);
            },
            error: handleAjaxError
        });
    });

    // Delete reservation
    $('.btn-delete').on('click', function() {
        var id = $(this).data('id'); // Assuming each delete button has a data-id attribute with the reservation ID

        if (confirm("Are you sure you want to delete this reservation?")) {
            $.ajax({
                url: 'delete_reservation.php',
                method: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    handleAjaxSuccess(response, 'Reservation deleted successfully', true);
                },
                error: handleAjaxError
            });
        }
    });

    // Cancel reservation
    $('.btn-cancel').on('click', function() {
        var id = $(this).data('id');
        var lotId = $(this).data('lot-id');
        var name = $(this).data('name');

        $('#cancelButton').data('id', id);
        $('#cancelLotId').val(lotId);
        $('#cancelName').val(name);
        $('#cancelModal').modal('show');
    });

    // Handle the confirmation for cancellation
    $('#cancelButton').on('click', function() {
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
                if (response.status === 'success') {
                    alert('Reservation cancelled successfully. Lot ID: ' + response.lot_id + ', Name: ' + response.Name);
                    location.reload();  // Reload page to reflect changes
                } else {
                    alert('Error: ' + response.message || "An error occurred while cancelling the reservation.");
                }
            },
            error: handleAjaxError
        });
    });
});



</script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
