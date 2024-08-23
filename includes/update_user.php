<?php
include 'dbconnection.php';
session_start();

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize the form data
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
    $last_name = isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : '';
    $first_name = isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : '';
    $date_of_birth = isset($_POST['date_of_birth']) ? htmlspecialchars($_POST['date_of_birth']) : '';
    $postal_address = isset($_POST['postal_address']) ? htmlspecialchars($_POST['postal_address']) : '';
    $email_address = isset($_POST['email_address']) ? htmlspecialchars($_POST['email_address']) : '';

    // Validate the user ID
    if ($user_id <= 0) {
        echo "Invalid user ID.";
        exit();
    }

    // Prepare and execute the SQL update statement
    $stmt = $conn->prepare(
        "UPDATE users 
        SET last_name = ?, first_name = ?, date_of_birth = ?, postal_address = ?, email_address = ? 
        WHERE user_id = ?"
    );
    $stmt->bind_param('sssssi', $last_name, $first_name, $date_of_birth, $postal_address, $email_address, $user_id);

    if ($stmt->execute()) {
        echo "User details updated successfully.";
    } else {
        echo "Error updating user details: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();

    // Optionally redirect back to the user dashboard or another page
    
    exit();
} else {
    echo "Invalid request method.";
    exit();
}
?>
