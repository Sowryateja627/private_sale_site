<?php
// Include the database connection file
include 'dbconnection.php'; // Ensure this file contains the MySQLi connection setup

if (!isset($conn)) {
    die("Database connection failed. MySQLi connection is not set.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $lastName = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
    $firstName = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
    $dateOfBirth = filter_input(INPUT_POST, 'date_of_birth', FILTER_SANITIZE_STRING);
    $postalAddress = filter_input(INPUT_POST, 'postal_address', FILTER_SANITIZE_STRING);
    $emailAddress = filter_input(INPUT_POST, 'email_address', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password']; // Plain text password
    $userType = filter_input(INPUT_POST, 'user_type', FILTER_SANITIZE_STRING);

   

    // Hash the password
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    try {
        // Prepare and execute the SQL statement
        $stmt = $conn->prepare("INSERT INTO Users (last_name, first_name, date_of_birth, postal_address, email_address, password_hash, user_type) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $lastName, $firstName, $dateOfBirth, $postalAddress, $emailAddress, $passwordHash, $userType);
        $stmt->execute();

        header("Location: ../pages/login.html");
    } catch (mysqli_sql_exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Close the connection
$conn->close();
?>
