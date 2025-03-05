<?php
// Initialize variables
$submission_success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $fullname = $_POST['fullname'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];
    $nominee_name = $_POST['nominee_name'];
    $account_type = $_POST['account_type'];
    $pan = $_POST['pan'];
    $aadhaar = $_POST['aadhaar'];

    // Database connection
    $servername = "localhost";
    $username = "root"; // Change to your DB username
    $password = ""; // Change to your DB password
    $dbname = "bank"; // Change to your DB name

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and bind SQL statement
    $stmt = $conn->prepare("INSERT INTO bank_accounts (fullname, dob, gender, email, phone_number, address, nominee_name, account_type, pan, aadhaar, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
    $stmt->bind_param("ssssssssss", $fullname, $dob, $gender, $email, $phone_number, $address, $nominee_name, $account_type, $pan, $aadhaar);
    $stmt->execute();

    // Set submission success flag
    $submission_success = true;

    // Close database connection
    $stmt->close();
    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Bank Account Request</title>
    <style>

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
            max-width: 600px;
            margin: auto;
            padding: 20px;
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }



        .okay{ 
            margin-top:5px;
            border-radius:20px;
            width: 20%;
            padding: 7px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;

        }
        .required {
            color: red;
        }
        .message {
            text-align: center;
            margin-top: 15px;
            font-size: 18px;
        }
        .nav{
            position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1000;
        }
        .down{
            margin-top:55px;

        }


        .top{
            margin-top:10px;
        }
    </style>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header class="nav">
      <nav class="section__container nav__container">
        <div class="nav__logo">Finova<span>Bank</span></div>
        <ul class="nav__links">
          <li class="link"><a href="#">Home</a></li>
          <li class="link"><a href="dummyabout.html">About Us</a></li>
          <li class="link"><a href="register.php">User</a></li>
          <li class="link"><a href="manager_login.php">Accountant</a></li>
          <li class="link"><a href="manager_login.php">Manager</a></li>
          
        </ul>
       <a href="clerk_services_main.html" ><button class="btn"><b>Back</b></button></a>
      </nav>
    </header>
    


<div class="container down">
    <?php if ($submission_success): ?>
        <div class="message">
            <h2 class="top">Request Submitted Successfully!</h2>

            <div><p>We will process it soon.</p></div>
          
        </div>
    <?php else: ?>
        <h2 >Create Bank Account</h2>
        <form action="create_account.php" method="POST">
            <label for="fullname">Full Name <span class="required">*</span>:</label>
            <input type="text" id="fullname" name="fullname" required>

            <label for="dob">Date of Birth <span class="required">*</span>:</label>
            <input type="date" id="dob" name="dob" required>

            <label for="gender">Gender <span class="required">*</span>:</label>
            <select id="gender" name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>

            <label for="email">Email <span class="required">*</span>:</label>
            <input type="email" id="email" name="email" required>

            <label for="phone_number">Phone Number <span class="required">*</span>:</label>
            <input type="tel" id="phone_number" name="phone_number" pattern="[0-9]{10}" required>

            <label for="address">Address <span class="required">*</span>:</label>
            <textarea id="address" name="address" rows="3" required></textarea>

            <label for="nominee_name">Nominee Name:</label>
            <input type="text" id="nominee_name" name="nominee_name">

            <label for="password">Password:</label>
            <input type="password" id="password" name="password">

            <label for="account_type">Account Type <span class="required">*</span>:</label>
            <select id="account_type" name="account_type" required>
                <option value="">Select Account Type</option>
                <option value="Savings">Savings</option>
                <option value="Current">Current</option>
            </select>

            <label for="pan">PAN Number (Optional):</label>
            <input type="text" id="pan" name="pan" pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}">

            <label for="aadhaar">Aadhaar Number <span class="required">*</span>:</label>
            <input type="text" id="aadhaar" name="aadhaar" pattern="[0-9]{12}" required>

            <button type="submit">Submit Request</button>
            
        </form>
        
        
    <?php endif; ?>
</div>

</body>
</html>
