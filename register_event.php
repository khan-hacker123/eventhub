<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo "session_error";
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mydatabase";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$name = $_SESSION['user_name'];
$email = $_SESSION['user_email'];
$event = $_POST['event'];
$action = $_POST['action'];

if ($action === "register") {
    $sql = "INSERT INTO registrations (name, email, event_name) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $name, $email, $event);
    if ($stmt->execute()) {
        echo "registered";
    } else {
        echo "error";
    }
    $stmt->close();
} elseif ($action === "remove") {
    $sql = "DELETE FROM registrations WHERE email = ? AND event_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $event);
    if ($stmt->execute()) {
        echo "removed";
    } else {
        echo "error";
    }
    $stmt->close();
}

$conn->close();
?>
