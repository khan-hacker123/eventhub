<?php
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
$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];

// Check if the email already exists
$sql = "SELECT * FROM customers WHERE email_address = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Email already exists
    echo "<script>alert('This email address is already registered. Please use a different email.'); window.location.href = 'index.html';</script>";
    exit();
}

// Insert data into the database
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$sql = "INSERT INTO customers (name, email_address, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $name, $email, $hashed_password);

if ($stmt->execute()) {
    // Start the session and store user email
    session_start();
    $_SESSION['loggedin'] = true;
    $_SESSION['user_email'] = $email;

    // Redirect to main.php
    header("Location: main.php");
    exit();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close the connection
$stmt->close();
$conn->close();
?>
