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

// Handle Deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];

    // Delete the selected user
    $sql_delete = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        // Reorder the tokens after deletion using multi_query
        $sql_reorder = "SET @rank := 0; 
                        UPDATE users SET token = (@rank := @rank + 1) ORDER BY created_at ASC";
        if ($conn->multi_query($sql_reorder)) {
            // Ensure the previous query is consumed before running the SELECT query
            do {
                // This consumes any results from the previous query
                if ($result = $conn->store_result()) {
                    $result->free();
                }
            } while ($conn->next_result());

            // Success message
            $message = "User deleted and tokens reordered successfully.";
        } else {
            $message = "Error reordering tokens: " . $conn->error;
        }
    } else {
        $message = "Error deleting record: " . $conn->error;
    }
    $stmt->close();
}

// Fetch all registered users
$sql = "SELECT * FROM users ORDER BY token ASC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Users</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f9f9f9;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
            text-align:center;
        }
        th {
            background-color: #f2f2f2;
            text-align:center;
        }
        h2 {
            text-align: center;
        }
        form {
            display: inline-block;
        }
        button {
            background-color: #d9534f;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 3px;
        }
        button:hover {
            background-color: #c9302c;
        }
        .message {
            text-align: center;
            margin-top: 20px;
            font-size: 18px;
            color: green;
        }


        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .nav{ position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1000;

        }


        .one{
            margin-top:120px
        }

        .two{
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


<h2 class="one">Registered Users</h2>

<!-- Display Message -->
<?php if (isset($message)): ?>
    <div class="message">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<table class="two">
    <thead>
        <tr>
            <th>Token</th>
            <th>Username</th>
            <th>Phone Number</th>
            <th>Registration Time</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            // Output data of each row
            while($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . $row['token'] . "</td>
                        <td>" . $row['username'] . "</td>
                        <td>" . $row['phone_number'] . "</td>
                        <td>" . $row['created_at'] . "</td>
                        <td>
                            <form action='view.php' method='POST'>
                                <input type='hidden' name='id' value='" . $row['id'] . "'>
                                <button type='submit'>Delete</button>
                            </form>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No users found.</td></tr>";
        }
        ?>
    </tbody>
</table>

</body>
</html>

<?php
$conn->close();
?>
