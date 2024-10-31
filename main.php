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

// Get user info from session
$user_email = $_SESSION['user_email'];

// Fetch user details from the 'customers' table
$stmt = $conn->prepare("SELECT name FROM customers WHERE email_address = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$stmt->bind_result($user_name);
$stmt->fetch();
$stmt->close();

$events = [
    ['event_name' => 'Event 1', 'description' => 'Description of Event 1', 'date' => '2023-12-15', 'time' => '10:00 AM', 'venue' => 'Hall 1'],
    ['event_name' => 'Event 2', 'description' => 'Description of Event 2', 'date' => '2023-12-16', 'time' => '2:00 PM', 'venue' => 'Hall 2'],
    ['event_name' => 'Event 3', 'description' => 'Description of Event 3', 'date' => '2023-12-17', 'time' => '6:00 PM', 'venue' => 'Hall 3']
];

// Handle event registration or removal
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_name = $_POST['event_name'];
    
    if ($_POST['action'] == 'register') {
        $stmt = $conn->prepare("INSERT INTO registrations (name, email_address, event_name) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $user_name, $user_email, $event_name);
        $stmt->execute();
        $stmt->close();
    } elseif ($_POST['action'] == 'remove') {
        $stmt = $conn->prepare("DELETE FROM registrations WHERE email_address = ? AND event_name = ?");
        $stmt->bind_param("ss", $user_email, $event_name);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: main.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Page - Events</title>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOMQmk4EnE5qTmE3QQmF9Kr4aEjR4Dh2f1jd4s39" crossorigin="anonymous">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e9ecef;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        /* Icon Button Styling */
        .top-buttons {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
        }
        .icon-btn {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.2s ease;
        }
        .icon-btn i {
            margin-right: 8px;
        }
        .icon-btn:hover {
            background-color: #0056b3;
        }

        /* Page styling */
        h1 { 
            color: #333; 
            margin-bottom: 20px; 
            font-size: 28px; 
            text-align: center; 
        }

        .event { 
            background-color: #ffffff; 
            width: 100%; 
            max-width: 600px; 
            margin-bottom: 20px; 
            padding: 20px; 
            border-radius: 10px; 
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15); 
            transition: transform 0.2s ease-in-out;
        }
        .event:hover { 
            transform: translateY(-5px); 
        }
        .event h2 { 
            margin: 0; 
            color: #007bff; 
            font-size: 24px; 
        }
        .event p { 
            margin: 8px 0; 
            color: #555; 
            font-size: 16px; 
        }
        .event .details { 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
        }

        .register-btn, .remove-btn { 
            padding: 12px 20px; 
            color: white; 
            border: none; 
            border-radius: 5px; 
            font-weight: bold; 
            cursor: pointer; 
            text-transform: uppercase; 
            transition: background-color 0.2s ease;
        }
        .register-btn { 
            background-color: #28a745; 
        }
        .register-btn:hover { 
            background-color: #218838; 
        }
        .remove-btn { 
            background-color: #dc3545; 
        }
        .remove-btn:hover { 
            background-color: #c82333; 
        }
    </style>
</head>
<body>

<div class="top-buttons">
    <a href="view.php" class="icon-btn"><i class="fas fa-user"></i> Profile</a>
    <a href="logout.php" class="icon-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<h1>Welcome, <?php echo htmlspecialchars($user_name); ?>! Explore Our Events</h1>

<?php foreach ($events as $event): ?>
    <div class="event">
        <h2><?php echo htmlspecialchars($event['event_name']); ?></h2>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($event['description']); ?></p>
        <p><strong>Date:</strong> <?php echo htmlspecialchars($event['date']); ?></p>
        <p><strong>Time:</strong> <?php echo htmlspecialchars($event['time']); ?></p>
        <p><strong>Venue:</strong> <?php echo htmlspecialchars($event['venue']); ?></p>

        <div class="details">
            <?php
            $stmt = $conn->prepare("SELECT * FROM registrations WHERE email_address = ? AND event_name = ?");
            $stmt->bind_param("ss", $user_email, $event['event_name']);
            $stmt->execute();
            $result = $stmt->get_result();
            $is_registered = $result->num_rows > 0;
            $stmt->close();
            ?>

            <form action="main.php" method="POST" style="display: inline;">
                <input type="hidden" name="event_name" value="<?php echo htmlspecialchars($event['event_name']); ?>">
                <?php if ($is_registered): ?>
                    <button type="submit" name="action" value="remove" class="remove-btn">Remove</button>
                <?php else: ?>
                    <button type="submit" name="action" value="register" class="register-btn">Register</button>
                <?php endif; ?>
            </form>
        </div>
    </div>
<?php endforeach; ?>

</body>
</html>

<?php $conn->close(); ?>
