<?php
// Start the session
session_start();

// Database connection parameters
$host = 'localhost'; // Your database host
$dbname = 'bank'; // Your database name
$username = 'root'; // Your database username
$password = ''; // Your database password

// Create a new PDO instance
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user input and trim extra spaces
    $userid = trim($_POST['userid']);
    $password = trim($_POST['password']);

    // Prepare SQL statement to prevent SQL injection
    $stmt = $pdo->prepare("SELECT * FROM clerk_login WHERE userid = :userid");
    $stmt->execute([':userid' => $userid]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the user exists and verify the password
    if ($user && $password === $user['password']) {
        // Set session variables
        $_SESSION['userid'] = $userid;

        // Check if it's the manager
        if ($userid === 'manager' && $password === '2003') {
            // Redirect to manager_services.html
            header("Location: manager_approval.php");
            exit();
        } 
        // Check if it's the clerk
        elseif ($userid === 'clerk' && $password === '2004') {
            // Redirect to clerk_services.html
            header("Location: clerk_services_main.html");
            exit();
        }
        else {
            // Redirect to a general clerk services page
            header("Location: clerk_services.html");
            exit();
        }
    } else {
        // Invalid credentials
        echo "<script>alert('Invalid User ID or Password. Please try again.'); window.location.href='login.html';</script>";
    }
}
?>
