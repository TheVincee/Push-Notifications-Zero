<?php 
// Database connection
$connection = new mysqli("localhost", "root", "", "push");

// Check for successful connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Query to fetch reservation details
$reservations_query = "SELECT id, lot_id, name, email, contact, date, time, status FROM reservations";
$reservations_result = $connection->query($reservations_query);
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
                    <td><?php echo htmlspecialchars($row['lot_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['contact']); ?></td>
                    <td><?php echo htmlspecialchars($row['date']); ?></td>
                    <td><?php echo htmlspecialchars($row['time']); ?></td>
                    <td id="status-<?php echo htmlspecialchars($row['id']); ?>"><?php echo htmlspecialchars($row['status']); ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm edit-status-btn" data-id="<?php echo htmlspecialchars($row['id']); ?>" data-lot-id="<?php echo htmlspecialchars($row['lot_id']); ?>" data-name="<?php echo htmlspecialchars($row['name']); ?>" data-status="<?php echo htmlspecialchars($row['status']); ?>" data-toggle="modal" data-target="#editStatusModal">Edit</button>
                        <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo htmlspecialchars($row['id']); ?>">Delete</button>
                        <button class="btn btn-info btn-sm view-reservation-btn" data-id="<?php echo htmlspecialchars($row['id']); ?>">View</button>
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
                    <div class="form-group">
                        <label for="id">ID</label>
                        <input type="text" class="form-control" id="id" name="id" readonly>
                    </div>
                    <div class="form-group">
                        <label for="lotIdDisplay">Lot ID</label>
                        <input type="text" class="form-control" id="lotIdDisplay" readonly>
                    </div>
                    <div class="form-group">
                        <label for="nameDisplay">Name</label>
                        <input type="text" class="form-control" id="nameDisplay" readonly>
                    </div>
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
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    // Handle the 'Save Changes' submission for editing reservation status
    $('#editStatusForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#id').val();
        const status = $('#status').val();

        $.ajax({
            url: 'update_status.php',
            type: 'POST',
            data: { id, status },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    $(`#status-${id}`).text(status);
                    $('#editStatusModal').modal('hide');
                    alert('Status updated successfully!');
                } else {
                    $('#modal-error-message').text(data.message).show();
                }
            },
            error: function() {
                $('#modal-error-message').text('An error occurred while updating the status.').show();
            }
        });
    });

    // Pre-fill Edit Status modal with selected reservation data
    $('.edit-status-btn').click(function () {
        $('#id').val($(this).data('id'));
        $('#lotIdDisplay').val($(this).data('lot-id'));
        $('#nameDisplay').val($(this).data('name'));
        $('#status').val($(this).data('status'));
    });

    // Fetch and display reservation details in View Modal
    $('.view-reservation-btn').click(function () {
        const id = $(this).data('id');
        $.ajax({
            url: 'get_reservation_details.php',
            type: 'POST',
            data: { id },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    const reservation = data.data;
                    $('#viewLotId').val(reservation.lot_id || '');
                    $('#viewName').val(reservation.name || '');
                    $('#viewEmail').val(reservation.email || '');
                    $('#viewContact').val(reservation.contact || '');
                    $('#viewDate').val(reservation.reservation_date || '');
                    $('#viewTime').val(reservation.reservation_time || '');
                    $('#viewStatus').val(reservation.status || '');
                    $('#viewReservationModal').modal('show');
                } else {
                    alert(data.message || 'Error fetching reservation details.');
                }
            },
            error: function() {
                alert('An error occurred while fetching reservation details.');
            }
        });
    });
});
</script>
</body>
</html>
