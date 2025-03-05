<?php
$servername = "localhost";
$username = "root";  // Change this if necessary
$password = "";      // Change this if necessary
$dbname = "bank";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$show_form = true; // This variable controls the visibility of the form.

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $phone = $_POST['phone_number'];

    // Get the next available token number
    $sql = "SELECT MAX(token) AS max_token FROM users";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $next_token = ($row['max_token'] !== null) ? $row['max_token'] + 1 : 1;

    // Insert the new user into the database
    $sql = "INSERT INTO users (username, phone_number, token) VALUES ('$user', '$phone', '$next_token')";

    if ($conn->query($sql) === TRUE) {
        $success_message = "Successfully Registered. Your Token Number is: " . htmlspecialchars($next_token);
        $show_form = false; // Hide the form after successful registration
    } else {
        $error_message = "Error: " . htmlspecialchars($conn->error);
        $show_form = false; // Hide the form after an error
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <style>

        .nav{
            position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1000;
        }
        body {
            padding-top: 80px; 
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }
        h2 {
            text-align: center;
        }
        label {
            display: block;
            margin-bottom: 10px;
        }
        input[type="text"], input[type="tel"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 40%;
            padding: 7px;
            background-color: #5cb85c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #4cae4c;
        }
        .message {
            padding: 20px;
            margin-top: 20px;
            border-radius: 10px;
            text-align: center;
        }
        .success {
            background-color: #28a745;
            color: white;
        }
        .error {
            background-color: #dc3545;
            color: white;
        }
        .ok-button {
            background-color: #007bff;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        .one{
            margin-bottom:15px;
        }
    </style>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header class="nav">
      <nav class="section__container nav__container">
        <div class="nav__logo">Finova<span>Bank</span></div>
        <ul class="nav__links">
          <li class="link"><a href="index.html">Home</a></li>
          <li class="link"><a href="#">About Us</a></li>
          <li class="link"><a href="register.php">User</a></li>
          <li class="link"><a href="manager_login.php">Accountant</a></li>
          <li class="link"><a href="manager_login.php">Manager</a></li>
          
        </ul>
        <a href="blog_comments.html" ><button class="btn"><b>Feedback</b></button></a>
      </nav>
    </header>

<div class="container">
    <?php if ($show_form): ?>
        <!-- Registration Form -->
        <h2>User Registration</h2>
        
        <form action="" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="phone_number">Phone Number:</label>
            <input type="tel" id="phone_number" name="phone_number" pattern="[0-9]{10}" required>

    <center>            <button type="submit">Register</button></center>
        </form>

    <?php else: ?>
        <!-- Success or Error Message -->
        <div class="message one <?php echo isset($success_message) ? 'success' : 'error'; ?>">
            <strong><?php echo isset($success_message) ? $success_message : $error_message; ?></strong>
        </div >

        <!-- OK Button to Go Back to the Registration Form -->
        <form action="" method="get">
           <center> <button type="submit" class="ok-button">OK</button><center>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
