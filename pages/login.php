<?php
session_start(); // Start or resume a session

// Include the database connection file
include '../includes/dbconnection.php'; // Ensure this file contains the MySQLi connection setup

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $emailAddress = filter_input(INPUT_POST, 'email_address', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password']; // Plain text password

    if (empty($emailAddress) || empty($password)) {
        die("Please fill in both fields.");
    }

    // Prepare and execute the SQL statement
    $stmt = $conn->prepare("SELECT user_id, password_hash, user_type FROM Users WHERE email_address = ?");
    $stmt->bind_param("s", $emailAddress);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        die("No user found with that email address.");
    }

    // Bind result variables
    $stmt->bind_result($userId, $passwordHash, $userType);
    $stmt->fetch();

    // Verify the password
    if (password_verify($password, $passwordHash)) {
        // Set session variables
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_email'] = $emailAddress;
        $_SESSION['user_type'] = $userType;

        if ($userType  === 'professional') {
            header("Location: professional_dashboard.php"); // Redirect to professional dashboard
        } else {
            header("Location: customer_dashboard.php"); // Redirect to customer dashboard
        }
        exit();
    } else {
        die("Invalid password.");
    }

  
}

// Close the connection
$conn->close();
?>
