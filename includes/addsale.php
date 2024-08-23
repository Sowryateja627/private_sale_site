<?php
// Include database connection file
include 'dbconnection.php'; // Ensure this file contains the MySQLi connection setup

session_start(); // Start the session to access the logged-in user's ID

if (!$conn) {
    die("Database connection failed.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $startDate = filter_input(INPUT_POST, 'start_date', FILTER_SANITIZE_STRING);
    $endDate = filter_input(INPUT_POST, 'end_date', FILTER_SANITIZE_STRING);
    
    // Retrieve the professional's ID from the session
    $professionalId = $_SESSION['user_id']; // Ensure this is set during login

    // Check if the form fields are not empty
    if ($title && $description && $startDate && $endDate) {
        try {
            // Prepare and execute the SQL statement
            $stmt = $conn->prepare("INSERT INTO sales (title, description, start_date, end_date, professional_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $title, $description, $startDate, $endDate, $professionalId);
            $stmt->execute();

            // Redirect to a success page or display a success message
            header("Location: ../pages/professional_dashboard.php"); 
            exit();
        } catch (mysqli_sql_exception $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "All fields are required.";
    }
}

// Close the connection
$conn->close();
?>
