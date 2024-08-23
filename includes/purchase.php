<?php
include 'dbconnection.php'; // Adjust path as needed

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
$sale_id = isset($_POST['sale_id']) ? intval($_POST['sale_id']) : 0; // Get sale_id

if ($item_id > 0 && $quantity > 0 && $sale_id > 0) {
    try {
        // Begin transaction
        $conn->begin_transaction();

        // Insert into orders table
        $sql = "INSERT INTO orders (user_id, sale_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $user_id, $sale_id); // Bind sale_id
        $stmt->execute();
        $order_id = $stmt->insert_id; // Get the ID of the newly created order
        $stmt->close();

        // Insert into order_items table
        $sql = "INSERT INTO order_items (order_id, item_id, quantity, price) SELECT ?, ?, ?, price FROM items WHERE item_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iiid', $order_id, $item_id, $quantity, $item_id);
        $stmt->execute();
        $stmt->close();

        // Commit transaction
        $conn->commit();

        echo json_encode(['success' => true, 'message' => 'Purchase successful']);
    } catch (mysqli_sql_exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid item, quantity, or sale ID']);
}
?>
