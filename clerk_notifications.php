<?php
// Database connection
$servername = "localhost";
$username = "root"; // Change to your DB username
$password = ""; // Change to your DB password
$dbname = "bank"; // Change to your DB name

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if an acknowledgment request has been made
if (isset($_GET['id'])) {
    $notification_id = $_GET['id'];

    // Validate the notification ID
    if ($notification_id !== null) {
        // Delete the notification (acknowledge request)
        $stmt = $conn->prepare("DELETE FROM clerk_notifications WHERE id = ?");
        $stmt->bind_param("i", $notification_id);
        $stmt->execute();
        $stmt->close();

        // Redirect to the same page after acknowledging
        header("Location: clerk_notifications.php");
        exit();
    } else {
        die("Invalid request: missing parameters.");
    }
}

// Fetch notifications from the database
$sql = "SELECT * FROM clerk_notifications";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clerk Notifications</title>
    <style>
        body {
            padding-top:80px;
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .notification {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        h2 {
            color: #333;
        }
        .notification-header {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .notification-details {
            margin: 5px 0;
        }
        .acknowledge {
            padding: 5px 10px;
            color: white;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .acknowledge:hover {
            background-color: #0056b3;
        }
        .nav{
            position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1000;
        }

        .down{
            margin-top:20px;
        }
    </style>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header class="nav ">
      <nav class="section__container nav__container">
        <div class="nav__logo">Finova<span>Bank</span></div>
        <ul class="nav__links">
          <li class="link"><a href="index.html">Home</a></li>
          <li class="link"><a href="#">About Us</a></li>
          <li class="link"><a href="register.php">User</a></li>
          <li class="link"><a href="manager_login.php">Accountant</a></li>
          <li class="link"><a href="manager_login.php">Manager</a></li>
          
        </ul>
       <a href="clerk_services_main.html" ><button class="btn"><b>Back</b></button></a>
      </nav>
    </header>


<h2 class="down">Clerk Notifications</h2>

<?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Extracting information from the message
        preg_match('/Request ID (\d+) has been approved for (.+?)\. Account Number: (\d+), Aadhaar: (\d+), PAN: (.+)\./', $row['message'], $matches);
        if (count($matches) > 0) {
            $request_id = $matches[1];
            $fullname = $matches[2];
            $account_number = $matches[3];
            $aadhaar = $matches[4];
            $pan = $matches[5];

            echo "<div class='notification'>
                <div class='notification-header'>Request ID: $request_id</div>
                <div class='notification-details'><strong>Name:</strong> $fullname</div>
                <div class='notification-details'><strong>Account Number:</strong> $account_number</div>
                <div class='notification-details'><strong>Aadhaar:</strong> $aadhaar</div>
                <div class='notification-details'><strong>PAN:</strong> $pan</div>
                <div class='notification-details'>
                    <a href='clerk_notifications.php?id={$row['id']}'><button class='acknowledge'>Acknowledge</button></a>
                </div>
            </div>";
        } else {
            // Display a generic notification for rejections or others
            echo "<div class='notification'>
                <div class='notification-header'>Notification</div>
                <div class='notification-details'>{$row['message']}</div>
                <div class='notification-details'>
                    <a href='clerk_notifications.php?id={$row['id']}'><button class='acknowledge'>Acknowledge</button></a>
                </div>
            </div>";
        }
    }
} else {
    echo "<div class='notification'>No notifications.</div>";
}
?>

</body>
</html>

<?php
$conn->close();
?>
