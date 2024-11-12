<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User - Notifications</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

        /* Notification Modal Style */
        #notification-modal {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 300px;
            background-color: #fff;
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

        /* Overlay for Background */
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

<!-- Notification Bell -->
<div id="notification-bell">
    🔔 <!-- Replace with an icon/image if preferred -->
    <span class="notification-count" id="notification-count">0</span>
</div>

<!-- Notification Modal -->
<div id="overlay"></div>
<div id="notification-modal"></div>

<script>
    // Track the last notification ID to avoid reloading the same notifications
    let lastNotificationId = 0;

    // Function to fetch notifications from the server
    function fetchNotifications() {
        $.ajax({
            url: 'fetch_statusNotif.php', // PHP script to get notifications
            type: 'GET',
            dataType: 'json',
            data: { last_id: lastNotificationId },
            success: function(notifications) {
                if (notifications.length > 0) {
                    const statusCount = { 'Approved': 0, 'Reject': 0, 'In Progress': 0 };

                    $('#notification-modal').empty(); // Clear current modal content

                    // Process and display each notification
                    notifications.forEach(notification => {
                        displayNotification(notification);
                        statusCount[notification.status] = (statusCount[notification.status] || 0) + 1;
                        lastNotificationId = Math.max(lastNotificationId, notification.id);
                    });

                    // Update notification count on the bell
                    const totalCount = Object.values(statusCount).reduce((sum, count) => sum + count, 0);
                    $('#notification-count').text(totalCount > 0 ? totalCount : '');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching notifications:', error);
            }
        });
    }

    // Function to display a notification item in the modal
    function displayNotification(notification) {
        const notificationElement = `
            <div class="notification-item" data-id="${notification.id}" data-status="${notification.status}">
                <strong>Lot ID:</strong> ${notification.lot_id}<br>
                <strong>Name:</strong> ${notification.name}<br>
                <strong>Status:</strong> ${notification.status}
            </div>
        `;
        $('#notification-modal').append(notificationElement);
    }

    // Toggle modal visibility
    $('#notification-bell').on('click', function() {
        $('#notification-modal, #overlay').toggle();
    });

    // Close modal when clicking the overlay
    $('#overlay').on('click', function() {
        $('#notification-modal, #overlay').hide();
    });

    // Handle notification click event
    $(document).on('click', '.notification-item', function() {
        const notificationId = $(this).data('id');
        const notificationStatus = $(this).data('status');
        alert(`Notification clicked with ID: ${notificationId} and Status: ${notificationStatus}`);
        // Add logic here to navigate or display detailed information
    });

    // Fetch notifications at regular intervals (every 3 seconds)
    setInterval(fetchNotifications, 3000);

    // Initial notification fetch on page load
    $(document).ready(fetchNotifications);
</script>

</body>
</html>
