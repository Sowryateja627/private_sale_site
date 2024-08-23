<?php
include 'dbconnection.php'; // Ensure this file contains the MySQLi connection setup

session_start(); // Start the session to access the logged-in user's ID
header('Content-Type: application/json'); // Set the content type to JSON

if (!$conn) {
    echo json_encode(['error' => 'Database connection failed.']);
    exit;
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User is not logged in.']);
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize POST data
    $userId = $_SESSION['user_id'];
    $brandId = isset($_POST['brand_id']) ? intval($_POST['brand_id']) : NULL;
    $categoryId = isset($_POST['category_id']) ? intval($_POST['category_id']) : NULL;

    // Prepare and execute the SQL query to insert or update user preferences
    $sql = "REPLACE INTO user_preferences (user_id, brand_id, category_id, created_at)
            VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(['error' => 'Failed to prepare the SQL statement.']);
        exit;
    }

    // Bind parameters and execute the statement
    $stmt->bind_param("iii", $userId, $brandId, $categoryId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => 'Preferences saved successfully.']);
    } else {
        echo json_encode(['error' => 'Failed to save preferences.']);
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>
