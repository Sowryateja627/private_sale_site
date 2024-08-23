<?php
include 'dbconnection.php'; // Adjust path as needed

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo "Unauthorized";
    exit();
}

$user_id = $_SESSION['user_id'];
$sale_id = isset($_POST['sale_id']) ? intval($_POST['sale_id']) : 0;

if ($sale_id > 0) {
    try {
        // Prevent logging multiple views within the last hour
        $sql = "
            SELECT COUNT(*) 
            FROM sale_views 
            WHERE user_id = ? AND sale_id = ? AND view_timestamp > NOW() - INTERVAL 1 HOUR
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $user_id, $sale_id);
        $stmt->execute();
        $stmt->bind_result($view_count);
        $stmt->fetch();
        $stmt->close();

        if ($view_count == 0) {
            // Insert view record into sale_views table
            $sql = "INSERT INTO sale_views (user_id, sale_id) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ii', $user_id, $sale_id);
            $stmt->execute();
            $stmt->close();
        }

        http_response_code(200);
    } catch (mysqli_sql_exception $e) {
        http_response_code(500);
        echo "Database error: " . $e->getMessage();
        exit();
    }
} else {
    http_response_code(400);
    echo "Invalid sale ID.";
    exit();
}
?>
