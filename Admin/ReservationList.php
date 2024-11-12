<?php 
// Database connection
$conn = new mysqli("localhost", "root", "", "push");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to join reservations and notifications based on lot_id
$query = "SELECT 
            reservations.id AS reservation_id,
            reservations.lot_id,
            reservations.name AS reservation_name,
            reservations.date AS reservation_date,
            reservations.time AS reservation_time,
            reservations.email,
            reservations.contact,
            reservations.status,
            reservations.cancellation_reason,
            notifications.id AS notification_id,
            notifications.name AS notification_name,
            notifications.notification_status,
            notifications.message,
            notifications.notification_date,
            notifications.notification_time
          FROM reservations
          LEFT JOIN notifications ON reservations.lot_id = notifications.lot_id";

$result = $conn->query($query);

$conn->close();
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
                <th>Reservation Name</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Cancellation Reason</th>
                <th>Notification Status</th>
                <th>Message</th>
                <th>Notification Date</th>
                <th>Notification Time</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['lot_id']; ?></td>
                        <td><?php echo $row['reservation_name']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['contact']; ?></td>
                        <td><?php echo $row['reservation_date']; ?></td>
                        <td><?php echo $row['reservation_time']; ?></td>
                        <td id="status-<?php echo $row['reservation_id']; ?>"><?php echo $row['status']; ?></td>
                        <td><?php echo $row['cancellation_reason'] ?: 'N/A'; ?></td>
                        <td><?php echo $row['notification_status'] ?: 'N/A'; ?></td>
                        <td><?php echo $row['message'] ?: 'N/A'; ?></td>
                        <td><?php echo $row['notification_date'] ?: 'N/A'; ?></td>
                        <td><?php echo $row['notification_time'] ?: 'N/A'; ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm edit-status-btn" data-id="<?php echo $row['reservation_id']; ?>" data-status="<?php echo $row['status']; ?>" data-toggle="modal" data-target="#editStatusModal">Edit</button>
                            <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $row['reservation_id']; ?>">Delete</button>
                            <button class="btn btn-info btn-sm view-reservation-btn" data-id="<?php echo $row['reservation_id']; ?>">View</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="13">No results found.</td></tr>
            <?php endif; ?>
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
                    <!-- ID Field (Visible) -->
                    <div class="form-group">
                        <label for="idDisplay">ID</label>
                        <input type="text" class="form-control" id="idDisplay" readonly>
                    </div>
                    
                    <!-- Lot ID Field (Visible) -->
                    <div class="form-group">
                        <label for="lotIdDisplay">Lot ID</label>
                        <input type="text" class="form-control" id="lotIdDisplay" readonly>
                    </div>

                    <!-- Name Field (Visible) -->
                    <div class="form-group">
                        <label for="nameDisplay">Name</label>
                        <input type="text" class="form-control" id="nameDisplay" readonly>
                    </div>

                    <!-- Status Field -->
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status">
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
                    <!-- Include fields for lot ID, reservation name, email, contact, date, time, status, cancellation reason, notification details -->
                    <div class="form-group">
                        <label for="viewLotId">Lot ID</label>
                        <input type="text" class="form-control" id="viewLotId" readonly>
                    </div>
                    <div class="form-group">
                        <label for="viewReservationName">Reservation Name</label>
                        <input type="text" class="form-control" id="viewReservationName" readonly>
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
                        <label for="viewReservationDate">Date</label>
                        <input type="text" class="form-control" id="viewReservationDate" readonly>
                    </div>
                    <div class="form-group">
                        <label for="viewReservationTime">Time</label>
                        <input type="text" class="form-control" id="viewReservationTime" readonly>
                    </div>
                    <div class="form-group">
                        <label for="viewStatus">Status</label>
                        <input type="text" class="form-control" id="viewStatus" readonly>
                    </div>
                    <div class="form-group">
                        <label for="viewCancellationReason">Cancellation Reason</label>
                        <input type="text" class="form-control" id="viewCancellationReason" readonly>
                    </div>
                    <div class="form-group">
                        <label for="viewNotificationStatus">Notification Status</label>
                        <input type="text" class="form-control" id="viewNotificationStatus" readonly>
                    </div>
                    <div class="form-group">
                        <label for="viewMessage">Message</label>
                        <input type="text" class="form-control" id="viewMessage" readonly>
                    </div>
                    <div class="form-group">
                        <label for="viewNotificationDate">Notification Date</label>
                        <input type="text" class="form-control" id="viewNotificationDate" readonly>
                    </div>
                    <div class="form-group">
                        <label for="viewNotificationTime">Notification Time</label>
                        <input type="text" class="form-control" id="viewNotificationTime" readonly>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    // Show Edit Status Modal and pre-fill data
    $('.edit-status-btn').click(function () {
        var id = $(this).data('id');
        var status = $(this).data('status');
        var lot_id = $(this).data('lot-id');  // Fetch the lot_id
        var name = $(this).data('name');      // Fetch the name
        
        // Set the ID, status, lot_id, and name in the modal form
        $('#idDisplay').val(id);
        $('#status').val(status);
        $('#lotId').val(lot_id);  // Set lot_id in the modal
        $('#name').val(name);      // Set name in the modal
        $('#modal-error-message').hide(); // Hide any previous error messages
    });

    // Submit Edit Status form with AJAX
    $('#editStatusForm').submit(function (e) {
        e.preventDefault();
        
        // Get form values
        var id = $('#idDisplay').val();
        var status = $('#status').val();
        var lot_id = $('#lotId').val();  // Get lot_id from modal
        var name = $('#name').val();      // Get name from modal

        // AJAX request to update status
        $.ajax({
            url: 'update_status.php',
            type: 'POST',
            data: { id: id, status: status, lot_id: lot_id, name: name },
            dataType: 'json', // Expect JSON response
            success: function (response) {
                if (response.success) {
                    // Update status in the table
                    $('#status-' + id).text(status);
                    $('#editStatusModal').modal('hide'); // Close modal on success
                } else {
                    // Show error message if update fails
                    $('#modal-error-message').text(response.error || 'Failed to update status.').show();
                }
            },
            error: function () {
                // General error handling
                $('#modal-error-message').text('An error occurred while updating status. Please try again.').show();
            }
        });
    });

    // Show View Reservation Modal with AJAX
    $('.view-reservation-btn').click(function () {
        var id = $(this).data('id');

        // AJAX request to fetch reservation details
        $.ajax({
            url: 'get_reservation_details.php',
            type: 'POST',
            data: { id: id },
            dataType: 'json', // Expect JSON response
            success: function (response) {
                if (response.success) {
                    // Populate modal fields with reservation data
                    var data = response.data;
                    $('#viewLotId').val(data.lot_id);
                    $('#viewReservationName').val(data.name);
                    $('#viewEmail').val(data.email);
                    $('#viewContact').val(data.contact);
                    $('#viewReservationDate').val(data.date);
                    $('#viewReservationTime').val(data.time);
                    $('#viewStatus').val(data.status);
                    $('#viewCancellationReason').val(data.cancellation_reason || 'N/A');
                    $('#viewNotificationStatus').val(data.notification_status || 'N/A');
                    $('#viewMessage').val(data.message || 'N/A');
                    $('#viewNotificationDate').val(data.notification_date || 'N/A');
                    $('#viewNotificationTime').val(data.notification_time || 'N/A');

                    // Show the view modal
                    $('#viewReservationModal').modal('show');
                } else {
                    alert('Failed to retrieve reservation details.');
                }
            },
            error: function () {
                // General error handling
                alert('An error occurred while fetching reservation details. Please try again.');
            }
        });
    });
});

</script>

</body>
</html>
