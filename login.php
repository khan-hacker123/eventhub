<?php
// Start a session
session_start();

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

// Get form data
$email = $_POST['email'];
$password = $_POST['password'];

// Prepare SQL to find user by email
$sql = "SELECT * FROM customers WHERE email_address = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Fetch user data
    $user = $result->fetch_assoc();
    // Verify password
    if (password_verify($password, $user['password'])) {
        // Set session variable to indicate user is logged in
        $_SESSION['loggedin'] = true;
        $_SESSION['user_email'] = $email;
        
        // Redirect to main.html
        header("Location: main.php");
        exit();
    } else {
        echo "Incorrect password.";
    }
} else {
    echo "No account found with that email.";
}

// Close connection
$stmt->close();
$conn->close();
?>
