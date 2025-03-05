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

// Handle approval and decline actions (before fetching requests)
$request_id = isset($_GET['id']) ? $_GET['id'] : null;
$action = isset($_GET['action']) ? $_GET['action'] : null;

if ($request_id !== null && $action !== null) {
    if ($action === 'approve') {
        // Generate a unique account number
        $account_number = generateUniqueAccountNumber($conn);

        // Fetch user details for notification
        $stmt = $conn->prepare("SELECT fullname, aadhaar, pan, email, phone_number, account_type FROM bank_accounts WHERE id = ?");
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $stmt->bind_result($fullname, $aadhaar, $pan, $email, $phone_number, $account_type);
        $stmt->fetch();
        $stmt->close();

        // Store the accepted request in bank_accounts
        $stmt = $conn->prepare("UPDATE bank_accounts SET status = 'Accepted', account_number = ? WHERE id = ?");
        $stmt->bind_param("si", $account_number, $request_id);
        $stmt->execute();
        $stmt->close();

        // Notify the clerk about the accepted request
        $message = "Request ID $request_id has been approved for $fullname. Account Number: $account_number, Aadhaar: $aadhaar, PAN: $pan.";
        $stmt = $conn->prepare("INSERT INTO clerk_notifications (request_id, message) VALUES (?, ?)");
        $stmt->bind_param("is", $request_id, $message);
        $stmt->execute();
        $stmt->close();

        // Redirect to the same page to show updated list of pending requests
        header("Location: manager_approval.php");
        exit();
    } elseif ($action === 'decline') {
        // Delete the declined request from bank_accounts table
        $stmt = $conn->prepare("DELETE FROM bank_accounts WHERE id = ?");
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $stmt->close();

        // Redirect to the same page to show updated list (no notification to clerk)
        header("Location: manager_approval.php");
        exit();
    }
}

// Fetch pending requests after actions
$sql = "SELECT * FROM bank_accounts WHERE status = 'Pending'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Approval</title>
    
    <style>
        body {
            padding-top:80px;
            margin-bottom:20px;
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .nav{
            position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1000;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        button {
            padding: 5px 10px;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .approve {
            background-color: #28a745;
        }
        .decline {
            background-color: #dc3545;
        }
        .down{
            margin-top:20px;
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
       <a href="manager_login.php" ><button class="btn"><b>Logout</b></button></a>
      </nav>
    </header>

<h2 class="down">Pending Account Requests</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Full Name</th>
        <th>Email</th>
        <th>Phone Number</th>
        <th>Account Type</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['fullname']}</td>
                <td>{$row['email']}</td>
                <td>{$row['phone_number']}</td>
                <td>{$row['account_type']}</td>
                <td>{$row['status']}</td>
                <td>
                    <a href='manager_approval.php?id={$row['id']}&action=approve'><button class='approve'>Approve</button></a>
                    <a href='manager_approval.php?id={$row['id']}&action=decline'><button class='decline'>Decline</button></a>
                </td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='7'>No pending requests.</td></tr>";
    }
    ?>
</table>

</body>
</html>

<?php
// Close the database connection
$conn->close();

// Function to generate a unique account number
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
?>
