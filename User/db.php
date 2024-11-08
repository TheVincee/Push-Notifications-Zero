<?php
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = "";      // Replace with your database password
$dbname = "push"; // Replace with your database name

// Create a new connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
