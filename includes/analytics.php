<?php
include 'dbconnection.php'; // Adjust path as needed

// Update number of visitors
$sql = "UPDATE sale_analytics sa
        JOIN (
            SELECT sale_id, COUNT(DISTINCT user_id) AS num_visitors
            FROM sale_views
            GROUP BY sale_id
        ) vs ON sa.sale_id = vs.sale_id
        SET sa.num_visitors = vs.num_visitors";
$conn->query($sql);

if (!$conn->query($sql)) {
    throw new Exception("Error updating num_visitors: " . $conn->error);
}

// Update number of items sold
$sql = "UPDATE sale_analytics sa
        JOIN (
            SELECT o.sale_id, SUM(oi.quantity) AS items_sold
            FROM orders o
            JOIN order_items oi ON o.id = oi.order_id
            GROUP BY o.sale_id
        ) oi ON sa.sale_id = oi.sale_id
        SET sa.items_sold = oi.items_sold";
$conn->query($sql);
if (!$conn->query($sql)) {
    throw new Exception("Error updating items_sold: " . $conn->error);
}

// Update revenue
$sql = "UPDATE sale_analytics sa
        JOIN (
            SELECT o.sale_id, SUM(oi.quantity * oi.price) AS revenue
            FROM orders o
            JOIN order_items oi ON o.id = oi.order_id
            GROUP BY o.sale_id
        ) rev ON sa.sale_id = rev.sale_id
        SET sa.revenue = rev.revenue";
$conn->query($sql);

if (!$conn->query($sql)) {
    throw new Exception("Error updating revenue: " . $conn->error);
}

echo "Sale analytics updated successfully.";
?>
