<?php
include 'dbconnection.php'; // Adjust path as needed

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize inputs
    $item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;

    if ($item_id > 0 && $quantity > 0) {
        // Start a transaction
        $conn->begin_transaction();

        try {
            // Check current stock
            $stmt = $conn->prepare("SELECT stock_quantity FROM items WHERE item_id = ?");
            $stmt->bind_param('i', $item_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $item = $result->fetch_assoc();

            if ($item && $item['stock_quantity'] >= $quantity) {
                // Update stock quantity
                $stmt = $conn->prepare("UPDATE items SET stock_quantity = stock_quantity - ? WHERE item_id = ?");
                $stmt->bind_param('ii', $quantity, $item_id);
                $stmt->execute();

                // Commit transaction
                $conn->commit();
                echo json_encode(['success' => true]);
            } else {
                // Not enough stock
                $conn->rollback();
                echo json_encode(['success' => false, 'message' => 'Not enough stock']);
            }
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => 'Error processing purchase: ' . $e->getMessage()]);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>
