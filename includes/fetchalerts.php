<?php
include 'dbconnection.php'; // Ensure the path is correct
session_start();

try {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(403); // Forbidden
        echo json_encode(['error' => 'User not authenticated']);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    
    // Start a transaction
    $conn->begin_transaction();

    // Step 1: Fetch brand IDs from user preferences
    $stmt = $conn->prepare("SELECT brand_id FROM user_preferences WHERE user_id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $brand_ids = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Check if we have brand IDs
    if (empty($brand_ids)) {
        throw new Exception('No brand preferences found for user.');
    }

    // Extract brand IDs into an array
    $brand_ids_array = array_column($brand_ids, 'brand_id');

    // Step 2: Fetch alerts along with brand names
    $placeholders = implode(',', array_fill(0, count($brand_ids_array), '?'));
    $types = str_repeat('i', count($brand_ids_array));

    // Query to fetch alerts along with brand names
    $sql = "
        SELECT a.alert_message, a.created_at, b.name 
        FROM alerts a
        JOIN brands b ON a.brand_id = b.id
        WHERE a.brand_id IN ($placeholders)
        ORDER BY a.created_at DESC
    ";
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new Exception('Failed to prepare SQL statement.');
    }
    $stmt->bind_param($types, ...$brand_ids_array);
    $stmt->execute();
    $result = $stmt->get_result();
    $alerts = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Commit the transaction
    $conn->commit();

    // Output alerts as JSON
    echo json_encode(['alerts' => $alerts]);

} catch (mysqli_sql_exception $e) {
    // Rollback the transaction on error
    $conn->rollback();
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    // Handle any other exceptions
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    // Close the connection
    $conn->close();
}
?>
