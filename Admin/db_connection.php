<?php
// Database credentials
$host = 'localhost';        // Database host
$username = 'root';         // Database username (default is 'root' for local installations)
$password = '';             // Database password (default is empty for XAMPP)
$dbname = 'push'; // Database name (replace with your actual database name)

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
