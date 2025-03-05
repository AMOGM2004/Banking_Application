<?php
$conn = new mysqli('localhost', 'root', '', 'bank');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = ''; // For displaying messages
$transaction_table = ''; // For transaction history

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $account_number = $_POST['account_number'];
    $full_name = $_POST['full_name'];
    $transaction_type = $_POST['transaction_type'];

    // Fetch user ID and balance
    $stmt = $conn->prepare("SELECT id, balance FROM bank_accounts WHERE fullname=? AND account_number=?");
    $stmt->bind_param("ss", $full_name, $account_number);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $user_id = $user['id'];
        
        if ($transaction_type == 'withdraw' || $transaction_type == 'deposit') {
            $amount = $_POST['amount'];

            if ($transaction_type == 'withdraw') {
                if ($user['balance'] >= $amount) {
                    // Insert withdrawal transaction
                    $stmt = $conn->prepare("INSERT INTO transactions (user_id, transaction_type, amount, transaction_date) VALUES (?, ?, ?, NOW())");
                    $stmt->bind_param("isd", $user_id, $transaction_type, $amount);
                    $stmt->execute();

                    // Update user balance
                    $stmt = $conn->prepare("UPDATE bank_accounts SET balance = balance - ? WHERE id = ?");
                    $stmt->bind_param("di", $amount, $user_id);
                    $stmt->execute();

                    $message = "Withdrawal successful. Your current balance is: " . ($user['balance'] - $amount);
                } else {
                    $message = "Insufficient funds.";
                }
            } else { // Deposit logic
                $stmt = $conn->prepare("INSERT INTO transactions (user_id, transaction_type, amount, transaction_date) VALUES (?, ?, ?, NOW())");
                $stmt->bind_param("isd", $user_id, $transaction_type, $amount);
                $stmt->execute();

                // Update user balance
                $stmt = $conn->prepare("UPDATE bank_accounts SET balance = balance + ? WHERE id = ?");
                $stmt->bind_param("di", $amount, $user_id);
                $stmt->execute();

                $message = "Deposit successful. Your current balance is: " . ($user['balance'] + $amount);
            }
        } elseif ($transaction_type == 'check_balance') {
            $message = "Your current balance is: " . $user['balance'];
        } elseif ($transaction_type == 'transaction_history') {
            // Fetch transaction history
            $stmt = $conn->prepare("SELECT transaction_type, amount, transaction_date FROM transactions WHERE user_id=?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $transactions = $stmt->get_result();

            // Prepare transaction table
            $transaction_table = "<h2>Transaction History</h2>";
            if ($transactions->num_rows > 0) {
                $transaction_table .= "<table>
                                        <tr>
                                            <th style='text-align: center; width: 30%;'>Type</th>
                                            <th style='text-align: center; width: 30%;'>Amount</th>
                                            <th style='text-align: center; width: 30%;'>Date</th>
                                        </tr>";
                while ($transaction = $transactions->fetch_assoc()) {
                    $transaction_table .= "<tr>
                                            <td style='text-align: center;'>" . htmlspecialchars(ucfirst($transaction['transaction_type'])) . "</td>
                                            <td style='text-align: center;'>" . htmlspecialchars($transaction['amount']) . "</td>
                                            <td style='text-align: center;'>" . htmlspecialchars($transaction['transaction_date']) . "</td>
                                          </tr>";
                }
                $transaction_table .= "</table>";
            } else {
                $transaction_table .= "No transactions found.";
            }
        }
    } else {
        $message = "Account not found.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Details</title>
    <style>
        *{
            padding:0;
            margin:0;
            box-sizing:border-box;

        }

        .nav{ position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1000;

        }
        body {
        
    font-family: 'Arial', sans-serif;
    background-color: #f4f7fa; 
    justify-content:center;
    align-items:center;
  * Changed background color */


}

.container {
    max-width: 600px;
    margin: auto;
    padding: 30px;
    background: #ffffff;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.success-message {
    color: #28a745; /* Success message color */
    font-weight: bold;
    margin-bottom: 20px;
}

.error-message {
    color: #dc3545; /* Error message color */
    font-weight: bold;
    margin-bottom: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    text-align: center;
}

table, th, td {
    border: 1px solid #dee2e6; /* Border color for the table */
}

th, td {
    padding: 10px; /* Padding for table cells */
    text-align: left; /* Align text to the left */
}

th {
    background-color: #007bff; /* Header background color */
    color: white; /* Header text color */
    font-weight: bold;
}

td {
    background-color: #ffffff; /* Cell background color */
}

tr:hover {
    background-color: #f1f1f1; /* Hover effect for rows */
}

tr:nth-child(even) {
    background-color: #f8f9fa; /* Zebra striping for even rows */
}





@media (max-width: 600px) {
    .container {
        padding: 20px; /* Responsive padding */
    }
}

.down{
    margin-bottom:100px;
}


        /* Additional styles for tables and other elements can be added here */
    </style>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header class="down">
      <nav class="section__container nav__container">
        <div class="nav__logo">Finova<span>Bank</span></div>
        <ul class="nav__links">
          <li class="link"><a href="index.html">Home</a></li>
          <li class="link"><a href="#">About Us</a></li>
          <li class="link"><a href="register.php">User</a></li>
          <li class="link"><a href="manager_login.php">Accountant</a></li>
          <li class="link"><a href="manager_login.php">Manager</a></li>
          
        </ul>
       <a href="clerk_services_main.html" ><button class="btn">Back</button></a>
      </nav>
</header>
    <div class="container">
        <?php if ($message) : ?>
            <div class="<?= strpos($message, 'successful') !== false ? 'success-message' : 'error-message' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <?php if ($transaction_table) : ?>
            <?= $transaction_table ?>
        <?php endif; ?>
    </div>

</body>
</html>