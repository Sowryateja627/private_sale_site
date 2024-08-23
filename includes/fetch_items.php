<?php
include 'dbconnection.php';

$sale_id = isset($_GET['sale_id']) ? intval($_GET['sale_id']) : 0;
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;
$subcategory_id = isset($_GET['subcategory_id']) ? intval($_GET['subcategory_id']) : 0;

$sql = "SELECT i.*, c.name AS category, s.name AS subcategory
        FROM items i
        JOIN Category c ON i.category_id = c.id
        LEFT JOIN Subcategory s ON i.subcategory_id = s.id
        WHERE i.sale_id = ?";

$params = [$sale_id];
$types = 'i';

// Add category filter if provided
if ($category_id) {
    $sql .= " AND i.category_id = ?";
    $params[] = $category_id;
    $types .= 'i';
}

// Add subcategory filter if provided
if ($subcategory_id) {
    $sql .= " AND i.subcategory_id = ?";
    $params[] = $subcategory_id;
    $types .= 'i';
}

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}

echo json_encode(['items' => $items]);

$stmt->close();
$conn->close();
?>
