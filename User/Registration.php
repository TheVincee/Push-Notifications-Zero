<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "push";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form input values and sanitize
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = htmlspecialchars(trim($_POST['password']));
    $confirm_password = htmlspecialchars(trim($_POST['confirm_password']));
    $usertype = 'user'; // Default usertype

    // Validate the inputs
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        echo "<script>alert('All fields are required.'); window.history.back();</script>";
        exit;
    }

    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match.'); window.history.back();</script>";
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare SQL statement to insert user data
    $sql = "INSERT INTO user_pass (name, email, password, usertype) VALUES (?, ?, ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        // Bind variables to the prepared statement
        $stmt->bind_param("ssss", $name, $email, $hashed_password, $usertype);

        // Execute the statement
        if ($stmt->execute()) {
            // Registration successful, show alert and redirect to login page
            echo "<script>
                    alert('You have registered successfully.');
                    window.location.href = 'UserLogin.php';
                  </script>";
            exit();
        } else {
            echo "<script>alert('Error: Could not execute the query.'); window.history.back();</script>";
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "<script>alert('Error: Could not prepare the query.'); window.history.back();</script>";
    }

    // Close the database connection
    $conn->close();
} else {
    // If the request method is not POST, redirect to registration form
    header("Location: Register.php");
    exit();
}
?>
