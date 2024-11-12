<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fetch Notifications</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .notification-list {
            margin-top: 20px;
            border-collapse: collapse;
            width: 100%;
        }

        .notification-list th, .notification-list td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        .notification-list th {
            background-color: #f4f4f4;
        }

        .response-message {
            margin-top: 20px;
            text-align: center;
            font-size: 16px;
        }

        .error {
            color: red;
        }

        .success {
            color: green;
        }
    </style>
</head>
<body>

<div>
    <h2>Fetched Notifications</h2>
    <!-- // tarongonon paneh  -->
    <div>
        <!-- Input for ID -->
        <label for="notificationId">Enter Notification ID:</label>
        <input type="text" id="notificationId" placeholder="Enter Notification ID" />
    </div>

    <div id="response-message" class="response-message"></div>

    <table class="notification-list" id="notification-list">
        <thead>
            <tr>
                <th>Notification ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Status</th>
                <th>Message</th>
                <th>Date</th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data will be inserted here -->
        </tbody>
    </table>
</div>

<script>
$(document).ready(function() {
    // Fetch notifications when the input field has a value
    $('#notificationId').on('input', function() {
        const notificationId = $(this).val().trim(); // Get the value of the input field
        
        // Only trigger AJAX request if there's an ID entered
        if (notificationId) {
            fetchNotifications(notificationId);
        } else {
            // Clear the table if the input is empty
            $('#notification-list tbody').empty();
            $('#response-message').text('Please enter a valid Notification ID.');
        }
    });

    // Function to fetch notifications
    function fetchNotifications(notificationId) {
        $.ajax({
            url: 'FetchReservations.php',  // Make sure this path is correct
            type: 'GET',
            data: { id: notificationId },  // Send the ID in the GET request
            success: function(response) {
                console.log('AJAX Response:', response);  // Debugging log

                const data = JSON.parse(response);

                if (data.status === 'success') {
                    $('#response-message').removeClass('error').addClass('success').text('Notifications fetched successfully.');

                    // Clear the table before populating new data
                    $('#notification-list tbody').empty();

                    // Loop through the data and append rows to the table
                    data.data.forEach(function(notification) {
                        $('#notification-list tbody').append(`
                            <tr>
                                <td>${notification.id}</td>
                                <td>${notification.name}</td>
                                <td>${notification.email}</td>
                                <td>${notification.contact}</td>
                                <td>${notification.notification_status}</td>
                                <td>${notification.message}</td>
                                <td>${notification.notification_date}</td>
                                <td>${notification.notification_time}</td>
                            </tr>
                        `);
                    });
                } else {
                    $('#response-message').removeClass('success').addClass('error').text(data.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                $('#response-message').removeClass('success').addClass('error').text('An error occurred while fetching notifications.');
            }
        });
    }

    // Optional: If you want to fetch automatically when the page loads
    // You could get an ID from the URL or some predefined source
    const urlParams = new URLSearchParams(window.location.search);
    const notificationIdFromURL = urlParams.get('id'); // This gets 'id' from the URL if present

    if (notificationIdFromURL) {
        $('#notificationId').val(notificationIdFromURL);  // Pre-fill input
        fetchNotifications(notificationIdFromURL);  // Fetch the notification
    }
});
</script>
</body>
</html>
