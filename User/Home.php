<?php
// Database connection setup
$conn = new mysqli("localhost", "root", "", "push");

// Check for connection errors and handle them gracefully
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch reserved lot IDs from the database
$reserved_lots = [];
$reservation_query = "SELECT lot_id FROM reservations WHERE status = 'Pending'";
$reservation_result = $conn->query($reservation_query);
if ($reservation_result->num_rows > 0) {
    while ($row = $reservation_result->fetch_assoc()) {
        $reserved_lots[] = $row['lot_id']; // Add reserved lot ID to the array
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lot Reservation</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .lot-box {
            width: 100px;
            height: 100px;
            display: inline-block;
            background-color: lightgray;
            margin: 10px;
            cursor: pointer;
            text-align: center;
            line-height: 100px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .reserved {
            background-color: green !important;
            color: white;
            cursor: not-allowed;
        }
        .lot-box:hover:not(.reserved) {
            background-color: #4CAF50;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Select a Lot to Reserve</h2>
    <div id="lot-container">
        <?php 
        // Display lots from 1 to 5 and mark reserved ones
        for ($i = 1; $i <= 5; $i++): 
        ?>
            <div class="lot-box <?php echo in_array($i, $reserved_lots) ? 'reserved' : ''; ?>" 
                 id="lot-<?php echo $i; ?>" 
                 data-lot-id="<?php echo $i; ?>">
                 Lot <?php echo $i; ?>
            </div>
        <?php endfor; ?>
    </div>
</div>

<!-- Reservation Form Modal -->
<div class="modal fade" id="requestModal" tabindex="-1" role="dialog" aria-labelledby="requestModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="requestModalLabel">Make a Reservation Request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="reservationRequestForm">
                    <input type="hidden" id="lotId" name="lot_id">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="contact">Contact Number</label>
                        <input type="text" class="form-control" id="contact" name="contact" required>
                    </div>
                    <div class="form-group">
                        <label for="datetime">Reservation Date & Time</label>
                        <input type="datetime-local" class="form-control" id="datetime" name="datetime" required>
                    </div>
                    <div id="modal-error-message" class="text-danger mt-3" style="display:none;"></div>
                    <div id="modal-success-message" class="text-success mt-3" style="display:none;"></div>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        // Handle lot box click to open the reservation modal
        $('.lot-box').click(function() {
            let lotId = $(this).data('lot-id');

            // Check if the lot is reserved and show an error if so
            if ($(this).hasClass('reserved')) {
                $('#modal-error-message').text('This lot is already reserved. Please select another lot.').show();
                $('#modal-success-message').hide();
                return;
            }

            // Reset and prepare the form for a new submission
            $('#reservationRequestForm')[0].reset();
            $('#lotId').val(lotId);
            $('#modal-error-message').hide();
            $('#modal-success-message').hide();
            $('#requestModal').modal('show');
        });

        // Handle form submission for a reservation request
        $('#reservationRequestForm').submit(function(e) {
            e.preventDefault();

            // Display a loading message during the AJAX request
            $('#modal-error-message').hide();
            $('#modal-success-message').hide();

            $.ajax({
                url: 'Reserve.php', // Backend script for processing reservations
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        let lotId = $('#lotId').val();
                        $('#lot-' + lotId).addClass('reserved').off('click'); // Mark the lot as reserved
                        $('#requestModal').modal('hide');
                        alert(response.message); // Notify the user of the successful reservation
                    } else {
                        $('#modal-error-message').text(response.message).show();
                    }
                },
                error: function(xhr, status, error) {
                    $('#modal-error-message').text('An error occurred: ' + error + '. Please try again later.').show();
                }
            });
        });
    });
</script>

</body>
</html>
