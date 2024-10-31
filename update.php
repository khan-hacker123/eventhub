<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.html");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mydatabase";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user email from session
$email = $_SESSION['user_email'];

// Get the new name from the form
$new_name = $_POST['name'];

// Update user information
$sql = "UPDATE customers SET name = ? WHERE email_address = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $new_name, $email);

if ($stmt->execute()) {
    // If update is successful, redirect to main.php with an alert
    echo '<script>
            alert("Profile information updated successfully!");
            window.location.href = "main.php";
          </script>';
} else {
    echo "Error updating profile: " . $stmt->error;
}

// Close the connection
$stmt->close();
$conn->close();
?>
