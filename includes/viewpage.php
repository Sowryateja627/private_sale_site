<?php
include 'dbconnection.php';

header('Content-Type: application/json'); // Set the content type to JSON

// Define the unique page identifier
$page_id = 1; // Replace with the appropriate identifier for this page

// Increment the visit count
try {
    $conn->begin_transaction();

    // Check if the page ID exists in the table
    $stmt = $conn->prepare("SELECT visit_count FROM page_visits WHERE page_id = ?");
    $stmt->bind_param('i', $page_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Page ID exists, update visit count
        $stmt = $conn->prepare("UPDATE page_visits SET visit_count = visit_count + 1 WHERE page_id = ?");
    } else {
        // Page ID does not exist, insert new record
        $stmt = $conn->prepare("INSERT INTO page_visits (page_id, visit_count) VALUES (?, 1)");
    }

    $stmt->bind_param('i', $page_id);
    $stmt->execute();
    $conn->commit();
    $stmt->close();
} catch (mysqli_sql_exception $e) {
    $conn->rollback();
    // Handle the error (e.g., log it)
    error_log('Database error: ' . $e->getMessage());
}

?>