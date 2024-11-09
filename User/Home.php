<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "push");

// Check for connection errors
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
            cursor: not-allowed; /* Disable click for reserved lots */
        }
        .lot-box:hover:not(.reserved) {
            background-color: #4CAF50; /* Highlight unreserved lots on hover */
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Select a Lot to Reserve</h2>
    <div id="lot-container">
        <?php 
        // Display lots and mark reserved ones
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
    // When a lot box is clicked, open the modal for reservation
    $('.lot-box').click(function() {
        var lotId = $(this).data('lot-id');

        // Check if the lot is already reserved
        if ($(this).hasClass('reserved')) {
            $('#modal-error-message').text('This lot is already reserved. Please select another lot.').show();
            $('#modal-success-message').hide();
            return; // Exit function if lot is reserved
        }

        $('#reservationRequestForm')[0].reset(); // Clear the form fields
        $('#lotId').val(lotId); // Set the lot ID in the hidden input
        $('#modal-error-message').hide(); // Hide error message
        $('#modal-success-message').hide(); // Hide success message
        $('#modal-loading-message').hide(); // Hide loading message
        $('#requestModal').modal('show'); // Show the modal
    });

    // Submit the reservation form
    $('#reservationRequestForm').submit(function(e) {
        e.preventDefault(); // Prevent default form submission

        // Display loading message or spinner while the request is being processed
        $('#modal-success-message').hide();
        $('#modal-error-message').hide();
        $('#modal-loading-message').text('Processing your reservation...').show(); // Assuming you have an element for a loading message

        $.ajax({
    url: 'Reserve.php', 
    method: 'POST',
    data: $(this).serialize(),
    dataType: 'json', // Expect JSON response
    success: function(response) {
        $('#modal-loading-message').hide(); // Hide loading message

        if (response.status === 'success') {
            let lotId = $('#lotId').val();
            $('#lot-' + lotId).addClass('reserved').off('click'); // Mark lot as reserved
            $('#requestModal').modal('hide');
            $('#modal-success-message').text(response.message).show();
            $('#modal-error-message').hide();
        } else {
            $('#modal-error-message').text(response.message).show();
            $('#modal-success-message').hide();
        }
    },
    error: function(xhr, status, error) {
        $('#modal-loading-message').hide(); // Hide loading message
        $('#modal-error-message').text('Error occurred: ' + error + '. Please try again later.').show();
        $('#modal-success-message').hide();
    }
});

    });
});

</script>

</body>
</html>
