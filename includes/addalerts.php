<?php
$saleId = intval($_POST['sale_id']);
$saleDescription = $_POST['sale_description'];

// Fetch user preferences
$sql = "SELECT user_id FROM user_preferences
        WHERE brand_id = ? OR category_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $brandId, $categoryId);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $userId = $row['user_id'];

    // Insert alert
    $sql_alert = "INSERT INTO alerts (user_id, sale_id, alert_type, alert_message, created_at)
                  VALUES (?, ?, 'new_sale', ?, NOW())";
    $stmt_alert = $conn->prepare($sql_alert);
    $stmt_alert->bind_param("iis", $userId, $saleId, $saleDescription);
    $stmt_alert->execute();
    $stmt_alert->close();
}

?>
