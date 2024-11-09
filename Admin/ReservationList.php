<?php 
$conn = new mysqli("localhost", "root", "", "push");

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch reservations
$reservations_query = "SELECT id, lot_id, name, email, contact, date, time, status FROM reservations";
$reservations_result = $conn->query($reservations_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Reservation List</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2>Reservation List</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
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
            <?php while ($row = $reservations_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['lot_id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['contact']; ?></td>
                    <td><?php echo $row['date']; ?></td>
                    <td><?php echo $row['time']; ?></td>
                    <td id="status-<?php echo $row['id']; ?>"><?php echo $row['status']; ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm edit-status-btn" data-id="<?php echo $row['id']; ?>" data-lot-id="<?php echo $row['lot_id']; ?>" data-name="<?php echo $row['name']; ?>" data-status="<?php echo $row['status']; ?>" data-toggle="modal" data-target="#editStatusModal">Edit</button>
                        <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $row['id']; ?>">Delete</button>
                        <button class="btn btn-info btn-sm view-btn" data-id="<?php echo $row['id']; ?>">View</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Edit Status Modal -->
<div class="modal fade" id="editStatusModal" tabindex="-1" role="dialog" aria-labelledby="editStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editStatusModalLabel">Edit Reservation Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editStatusForm">
                    <input type="hidden" id="reservationId" name="id">

                    <!-- Display Lot ID and Name as Read-Only Fields -->
                    <div class="form-group">
                        <label for="lotIdDisplay">Lot ID</label>
                        <input type="text" class="form-control" id="lotIdDisplay" name="lot_id_display" readonly>
                    </div>
                    <div class="form-group">
                        <label for="nameDisplay">Name</label>
                        <input type="text" class="form-control" id="nameDisplay" name="name_display" readonly>
                    </div>

                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="In Progress">In Progress</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <div id="modal-error-message" class="text-danger mt-3" style="display:none;"></div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- View Reservation Modal -->
<div class="modal fade" id="viewReservationModal" tabindex="-1" role="dialog" aria-labelledby="viewReservationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewReservationModalLabel">View Reservation Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
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
                        <input type="text" class="form-control" id="viewEmail" readonly>
                    </div>
                    <div class="form-group">
                        <label for="viewContact">Contact</label>
                        <input type="text" class="form-control" id="viewContact" readonly>
                    </div>
                    <div class="form-group">
                        <label for="viewDate">Date</label>
                        <input type="text" class="form-control" id="viewDate" readonly>
                    </div>
                    <div class="form-group">
                        <label for="viewTime">Time</label>
                        <input type="text" class="form-control" id="viewTime" readonly>
                    </div>
                    <div class="form-group">
                        <label for="viewStatus">Status</label>
                        <input type="text" class="form-control" id="viewStatus" readonly>
                    </div>
                    <div class="form-group">
                        <label for="viewCancellationReason">Cancellation Reason</label>
                        <textarea class="form-control" id="viewCancellationReason" rows="3" readonly></textarea>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function () {
    // When Edit button is clicked, populate the modal with reservation data
    $('.edit-status-btn').click(function () {
        var id = $(this).data('id');
        var lotId = $(this).data('lot-id');
        var name = $(this).data('name');
        var status = $(this).data('status');

        // Set the values of the form fields in the modal
        $('#reservationId').val(id);
        $('#lotIdDisplay').val(lotId);
        $('#nameDisplay').val(name);
        $('#status').val(status); // Set the current status in the dropdown
    });

    // Handle status change form submission
    $('#editStatusForm').submit(function (e) {
        e.preventDefault();

        var id = $('#reservationId').val(); // Get the reservation ID
        var status = $('#status').val(); // Get the new status

        $.ajax({
            url: 'update_status.php', // The PHP file to update the status
            method: 'POST', // Send a POST request
            data: { id: id, status: status }, // Send the ID and status
            success: function (response) {
                if (response === 'success') {
                    $('#status-' + id).text(status); // Update the status in the table
                    $('#editStatusModal').modal('hide'); // Close the modal
                } else {
                    $('#modal-error-message').text('Error updating status. Please try again.').show(); // Show an error message
                }
            },
            error: function () {
                $('#modal-error-message').text('An error occurred. Please try again.').show(); // Show an error message if the AJAX request fails
            }
        });
    });

    // Handle the Delete button action
    $('.delete-btn').click(function () {
        var id = $(this).data('id');
        if (confirm('Are you sure you want to delete this reservation?')) {
            $.ajax({
                url: 'delete_reservation.php',
                method: 'POST',
                data: { id: id },
                success: function (response) {
                    if (response === 'success') {
                        $('tr').find('button[data-id="' + id + '"]').closest('tr').remove(); // Remove the deleted reservation row
                    } else {
                        alert('Error deleting reservation.');
                    }
                }
            });
        }
    });

    // Handle the View button action
    $('.view-btn').click(function () {
        var id = $(this).data('id');

        $.ajax({
            url: 'view_reservation.php',
            method: 'POST',
            data: { id: id },
            success: function (response) {
                var reservation = JSON.parse(response);

                $('#viewLotId').val(reservation.lot_id);
                $('#viewName').val(reservation.name);
                $('#viewEmail').val(reservation.email);
                $('#viewContact').val(reservation.contact);
                $('#viewDate').val(reservation.date);
                $('#viewTime').val(reservation.time);
                $('#viewStatus').val(reservation.status);
                $('#viewCancellationReason').val(reservation.cancellation_reason);

                $('#viewReservationModal').modal('show'); // Show the view modal
            }
        });
    });
});
</script>

</body>
</html>
