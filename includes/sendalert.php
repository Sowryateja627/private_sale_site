<?php
include 'dbconnection.php'; // Ensure this file contains the MySQLi connection setup

header('Content-Type: application/json');
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize POST data
    $brandId = isset($_POST['brand_id']) ? intval($_POST['brand_id']) : NULL;
    $alertMessage = isset($_POST['alert_message']) ? trim($_POST['alert_message']) : '';

    if ($brandId === NULL || empty($alertMessage)) {
        echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
        exit();
    }

    // Prepare and execute the SQL query to insert the alert
    $sql = "INSERT INTO alerts (brand_id, alert_message, created_at) VALUES (?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare the SQL statement.']);
        exit();
    }

    // Bind parameters and execute the statement
    $stmt->bind_param("is", $brandId, $alertMessage);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Alert sent successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send alert.']);
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>
