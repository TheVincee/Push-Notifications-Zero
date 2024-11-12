<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Notifications</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Styling for notification bell and modal */
        #notification-bell {
            position: relative;
            font-size: 24px;
            cursor: pointer;
            color: #444;
            margin: 20px;
            display: inline-block;
        }
        .notification-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 3px 7px;
            font-size: 12px;
        }
        #notification-modal {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 300px;
            background-color: white;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
            border-radius: 5px;
            display: none;
            max-height: 400px;
            overflow-y: auto;
            z-index: 1000;
        }
        .notification-item {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            font-size: 14px;
            cursor: pointer;
        }
        .notification-item:last-child {
            border-bottom: none;
        }
        .notification-item:hover {
            background-color: #f9f9f9;
        }
        #overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            z-index: 999;
        }
    </style>
</head>
<body>

<!-- Notification Bell Icon -->
<div id="notification-bell">
    🔔
    <span class="notification-count" id="notification-count">0</span>
</div>

<!-- Notification Modal and Overlay -->
<div id="overlay"></div>
<div id="notification-modal"></div>

<script>
let lastNotificationId = 0; // Tracks the ID of the last fetched notification

// Fetch notifications from the server
function fetchNotifications() {
    $.ajax({
        url: 'fetch_reservations.php', // Endpoint to fetch notifications
        type: 'GET',
        dataType: 'json',
        data: { last_id: lastNotificationId },
        success: function(data) {
            // If there are new notifications, update the notification count and display them
            if (data.new_count > 0) {
                $('#notification-count').text(data.new_count);
                $('#notification-modal').empty();

                data.notifications.forEach(function(notification) {
                    displayNotification(notification);
                    // Track the highest notification ID
                    if (notification.id > lastNotificationId) {
                        lastNotificationId = notification.id;
                    }
                });
            } else {
                // Reset notification count if no new notifications
                $('#notification-count').text(0);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error fetching notifications:', error);
            alert('Unable to fetch notifications. Please try again later.');
        }
    });
}

// Render a single notification item in the modal
function displayNotification(notification) {
    $('#notification-modal').append(`
        <div class="notification-item" data-id="${notification.id}" data-status="${notification.notification_status}">
            <strong>Lot ID:</strong> ${notification.lot_id}<br>
            <strong>Name:</strong> ${notification.name}<br>
            <strong>Email:</strong> ${notification.email}<br>
            <strong>Contact:</strong> ${notification.contact}<br>
            <strong>Status:</strong> ${notification.notification_status}<br>
            <strong>Message:</strong> ${notification.message}<br>
            <strong>Date:</strong> ${notification.notification_date}<br>
            <strong>Time:</strong> ${notification.notification_time}<br>
        </div>
    `);
}

// Handle update requests for notifications with specific statuses
function processNotificationUpdate(notificationId) {
    $.ajax({
        url: 'fetch_updates.php', // Endpoint for processing updates
        type: 'POST',
        data: { id: notificationId },
        success: function() {
            console.log(`Notification ${notificationId} updated successfully.`);
            // Additional handling can be added here if needed
        },
        error: function(xhr, status, error) {
            console.error('Error processing notification update:', error);
        }
    });
}

// Toggle modal visibility when bell icon is clicked
$('#notification-bell').on('click', function() {
    $('#notification-modal, #overlay').toggle();
});

// Hide modal when overlay is clicked
$('#overlay').on('click', function() {
    $('#notification-modal, #overlay').hide();
});

// Handle notification item clicks
$(document).on('click', '.notification-item', function() {
    const notificationId = $(this).data('id');
    const notificationStatus = $(this).data('status');

    // Trigger an update if the status is "Updated", "Cancel", or "Delete"
    if (['Updated', 'Cancel', 'Delete'].includes(notificationStatus)) {
        processNotificationUpdate(notificationId);
    }

    alert(`Notification clicked with ID: ${notificationId}`);
});

// Set up periodic fetching of notifications every 30 seconds
setInterval(fetchNotifications, 30000);

// Initial fetch when page loads
$(document).ready(fetchNotifications);
</script>

</body>
</html>
