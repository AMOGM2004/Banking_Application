<?php
$servername = "localhost";
$username = "root"; // Your MySQL username
$password = ""; // Your MySQL password
$dbname = "bank"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']); // Sanitize user input
    $comment = $conn->real_escape_string($_POST['comment']); // Sanitize user input

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO blog_comments (name, comment) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $comment);

    // Execute the statement
    if ($stmt->execute()) {
        echo "<script>alert('Comment submitted successfully!'); window.location.href='blog_comments.html';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

$conn->close();
?>
