<?php
include 'dbconnection.php';

$sql = "SELECT id, name FROM Category";
$result = $conn->query($sql);

$categories = [];
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}

echo json_encode(['categories' => $categories]);

$result->close();
$conn->close();
?>
