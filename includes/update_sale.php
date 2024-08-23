<?php
include 'dbconnection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sale_id = isset($_POST['sale_id']) ? intval($_POST['sale_id']) : 0;
    $title = isset($_POST['title']) ? filter_var($_POST['title'], FILTER_SANITIZE_STRING) : '';
    $description = isset($_POST['description']) ? filter_var($_POST['description'], FILTER_SANITIZE_STRING) : '';
    $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
    $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';

    // Update sale details
    try {
        $sql = "
            UPDATE sales
            SET title = ?, description = ?, start_date = ?, end_date = ?
            WHERE sale_id = ?
        ";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('ssssi', $title, $description, $start_date, $end_date, $sale_id);
            $stmt->execute();
            header("Location: ../pages/professional_dashboard.php"); // Redirect after successful update
            exit();
        } else {
            echo "Failed to prepare statement.";
        }
    } catch (mysqli_sql_exception $e) {
        echo "Database error: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}
?>
