<?php
// fetch_sales.php
include 'dbconnection.php'; // Adjust path as needed

function fetchSales($conn) {
    try {
        $salesQuery = "
            SELECT sale_id, title, description, start_date, end_date
            FROM sales
            ORDER BY start_date DESC
        ";

        if ($stmt = $conn->prepare($salesQuery)) {
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } else {
            throw new Exception("Error preparing the statement.");
        }
    } catch (mysqli_sql_exception $e) {
        return ['error' => $e->getMessage()];
    } catch (Exception $e) {
        return ['error' => $e->getMessage()];
    }
    





  
}
function fetchCategory($conn){
    if (isset($_GET['sale_id'])) {
        $sale_id = intval($_GET['sale_id']);
    
        // Prepare and execute the SQL statement to fetch items based on sale_id
        $sql = "SELECT DISTINCT category FROM items WHERE sale_id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('i', $sale_id);
            $stmt->execute();
            $result = $stmt->get_result();
    
            $categories = $result->fetch_all(MYSQLI_ASSOC);
    
            // Return the categories as JSON
            echo json_encode(['categories' => array_column($categories, 'category')]);
        } else {
            echo json_encode(['categories' => []]);
        }
    } else {
        echo json_encode(['categories' => []]);
    }

}
?>
