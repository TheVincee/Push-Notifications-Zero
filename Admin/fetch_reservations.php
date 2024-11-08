<?php
// Enable error reporting to catch any issues during development
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection setup
$host = "localhost"; // Database host
$username = "root"; // Database username
$password = ""; // Database password (empty in your case)
$dbname = "push"; // Database name

// Create a new connection to the database
$mysqli = new mysqli($host, $username, $password, $dbname);

// Check if the connection was successful
if ($mysqli->connect_error) {
    // If the connection fails, output a detailed error message
    $error_message = "Connection failed: " . $mysqli->connect_error;
    echo json_encode(["error" => $error_message]);
    exit; // Exit to prevent further execution
}

// SQL query to fetch all notifications ordered by ID in descending order
$sql = "SELECT * FROM notifications ORDER BY id DESC";

// Execute the query and store the result
$result = $mysqli->query($sql);

// Check if there was an error executing the SQL query
if (!$result) {
    // If the query fails, output an error message with the query error
    $error_message = "SQL query failed: " . $mysqli->error;
    echo json_encode(["error" => $error_message]);
    exit; // Exit to stop further execution
}

// Initialize an empty array to hold the notifications
$notifications = [];

// If there are results, fetch each row and add it to the notifications array
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row; // Add each row to the notifications array
    }
} else {
    // If no results were found, we leave the notifications array empty
    $notifications = [];
}

// Set the content type header to JSON to tell the browser we're returning JSON data
header('Content-Type: application/json');

// Output the notifications array as JSON
echo json_encode($notifications);

// Close the database connection after we're done
$mysqli->close();
?>
