<?php
include '../includes/dbconnection.php';
session_start();

header('Content-Type: application/json'); // Set the content type to JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sale_id = isset($_POST['sale_id']) ? intval($_POST['sale_id']) : 0;

    try {
        $conn->begin_transaction();

        $deleteItemsSql = "DELETE FROM items WHERE sale_id = ?";
        if ($stmt = $conn->prepare($deleteItemsSql)) {
            $stmt->bind_param('i', $sale_id);
            $stmt->execute();
        }

        $deleteSaleSql = "DELETE FROM sales WHERE sale_id = ?";
        if ($stmt = $conn->prepare($deleteSaleSql)) {
            $stmt->bind_param('i', $sale_id);
            $stmt->execute();
        }

        $conn->commit();

        echo json_encode(['success' => true, 'message' => 'Sale and associated items deleted successfully']);
        exit();
    } catch (mysqli_sql_exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        exit();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}
?>
