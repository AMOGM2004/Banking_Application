<?php
// Check if 'id' and 'action' are set in the URL
$request_id = isset($_GET['id']) ? $_GET['id'] : null;
$action = isset($_GET['action']) ? $_GET['action'] : null;

// Validate the request
if ($request_id === null || $action === null) {
    die("Invalid request: missing parameters.");
}

// Database connection
$servername = "localhost";
$username = "root"; // Change to your DB username
$password = ""; // Change to your DB password
$dbname = "bank"; // Change to your DB name

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function generateUniqueAccountNumber($conn) {
    do {
        // Generate a random 11-digit number
        $account_number = str_pad(mt_rand(1, 99999999999), 11, '0', STR_PAD_LEFT);
        
        // Check if the account number already exists in the database
        $stmt = $conn->prepare("SELECT COUNT(*) FROM bank_accounts WHERE account_number = ?");
        $stmt->bind_param("s", $account_number);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
    } while ($count > 0); // Repeat if the number exists

    return $account_number;
}

$stmt = null; // Initialize $stmt to avoid undefined variable error

if ($action === 'approve') {
    // Generate a unique 11-digit account number
    $account_number = generateUniqueAccountNumber($conn);

    // Update database
    $stmt = $conn->prepare("UPDATE bank_accounts SET status = 'Approved', account_number = ? WHERE id = ?");
    $stmt->bind_param("si", $account_number, $request_id);
    $stmt->execute();

    // Notify clerk
    $message = "Request ID $request_id has been approved with account number: $account_number.";
    $stmt = $conn->prepare("INSERT INTO clerk_notifications (request_id, message) VALUES (?, ?)");
    $stmt->bind_param("is", $request_id, $message);
    $stmt->execute();

    echo "Request approved with account number: $account_number";
} elseif ($action === 'decline') {
    // Update database
    $stmt = $conn->prepare("UPDATE bank_accounts SET status = 'Declined' WHERE id = ?");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();

    // Notify clerk
    $message = "Request ID $request_id has been rejected by the manager.";
    $stmt = $conn->prepare("INSERT INTO clerk_notifications (request_id, message) VALUES (?, ?)");
    $stmt->bind_param("is", $request_id, $message);
    $stmt->execute();

    echo "Request declined.";
} elseif ($action === 'pending') {
    // Update database
    $stmt = $conn->prepare("UPDATE bank_accounts SET status = 'Pending' WHERE id = ?");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();

    // Notify clerk
    $message = "Request ID $request_id is now pending.";
    $stmt = $conn->prepare("INSERT INTO clerk_notifications (request_id, message) VALUES (?, ?)");
    $stmt->bind_param("is", $request_id, $message);
    $stmt->execute();

    echo "Request status updated to pending.";
}

// Close the statement if it was created
if ($stmt) {
    $stmt->close();
}

$conn->close();

// Redirect back to manager approval page
header("Location: manager_approval.php");
exit();
?>
