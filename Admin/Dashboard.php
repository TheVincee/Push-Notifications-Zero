<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Notifications</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- jQuery CDN -->
    <style>
        /* Bell Icon Style */
        #notification-bell {
            position: relative;
            font-size: 24px;
            cursor: pointer;
            color: #444;
            margin: 20px;
            display: inline-block;
        }

        /* Notification Count Badge */
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

        /* Centered Notification Modal */
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
            font-family: Arial, sans-serif;
            max-height: 400px;
            overflow-y: auto;
            z-index: 1000;
        }

        .notification-item {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            font-size: 14px;
            color: #333;
            cursor: pointer;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-item:hover {
            background-color: #f9f9f9;
        }

        /* Overlay to dim the background */
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

<!-- Bell Icon for Notifications -->
<div id="notification-bell">
    🔔 <!-- Bell icon (can be replaced with an actual icon/image) -->
    <span class="notification-count" id="notification-count">0</span> <!-- Notification count -->
</div>

<!-- Notification Modal in Center -->
<div id="overlay"></div> <!-- Overlay background to dim the rest of the page -->
<div id="notification-modal"></div> <!-- Modal to display notifications -->

<script>
// Declare lastNotificationId globally to track the last fetched notification ID
let lastNotificationId = 0;

// Function to fetch notifications from the server
function fetchNotifications() {
    $.ajax({
        url: 'fetch_reservations.php', // The PHP file that fetches notifications
        type: 'GET',
        dataType: 'json', // Expecting JSON response
        data: {
            last_id: lastNotificationId  // Pass the last fetched notification ID to the server
        },
        success: function(data) {
            // If the response is a plain ID (e.g., "123" or an object with just an id field)
            if (data.id) {
                console.log('Last notification ID:', data.id);
                lastNotificationId = data.id;
                return;
            }

            // If there is a new count of notifications
            if (data.new_count > 0) {
                let unreadCount = data.new_count; // Get the new notification count
                $('#notification-count').text(unreadCount); // Update the notification count on the bell icon
                $('#notification-modal').empty();  // Clear previous notifications in the modal

                // Loop through each notification and display it
                data.notifications.forEach(function(notification) {
                    displayNotification(notification); // Display the notification in the modal
                    if (notification.id > lastNotificationId) {
                        lastNotificationId = notification.id; // Update the last notification ID
                    }
                });
            } else {
                $('#notification-count').text(0); // If no new notifications, set count to 0
            }
        },
        error: function(xhr, status, error) {
            console.error('Error fetching notifications:', error);
            alert('Error fetching notifications. Please try again later.');
        }
    });
}

// Function to display individual notification in the modal
function displayNotification(notification) {
    const notificationModal = $('#notification-modal');
    
    // Create a notification item with relevant details (clickable)
    const notificationElement = `
        <div class="notification-item" data-id="${notification.id}" data-status="${notification.status}">
            <strong>Lot ID:</strong> ${notification.lot_id}<br>
            <strong>Name:</strong> ${notification.name}<br>
            <strong>Email:</strong> ${notification.email}<br>
            <strong>Contact:</strong> ${notification.contact}<br>
            <strong>Status:</strong> ${notification.status}<br>
            <strong>Message:</strong> ${notification.message}<br>
            <strong>Date:</strong> ${notification.notification_date}<br>
            <strong>Time:</strong> ${notification.notification_time}<br>
        </div>
    `;
    notificationModal.append(notificationElement); // Append the notification to the modal
}

// Toggle the visibility of the notification modal and overlay
$('#notification-bell').on('click', function() {
    $('#notification-modal, #overlay').toggle(); // Show/hide modal and overlay
});

// Hide the modal and overlay when clicking outside the modal (on the overlay)
$('#overlay').on('click', function() {
    $('#notification-modal, #overlay').hide(); // Close the modal when clicking on overlay
});

// Handle clicking on individual notifications
$(document).on('click', '.notification-item', function() {
    const notificationId = $(this).data('id');
    const notificationStatus = $(this).data('status');

    // Mark the notification as read if it’s unread
    if (notificationStatus === 'unread') {
        markAsRead(notificationId);
    }

    alert(`Notification clicked with ID: ${notificationId}`);
    // You can add code here to navigate to a specific page or open a detailed view
});

// Periodically check for new notifications every 30 seconds
setInterval(fetchNotifications, 3000);

// Initial fetch of notifications when the page is loaded
$(document).ready(function() {
    fetchNotifications(); // Fetch notifications as soon as the page is ready
});

</script>

</body>
</html>
