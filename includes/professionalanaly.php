<?php
include 'dbconnection.php'; // Adjust path as needed
session_start();


// Initialize response array
$response = [
    'total_revenue' => '0.00',
    'total_items' => 0,
    'total_quantity' => 0,
    'items' => [],
    'visit_count' => 0 
];

// Check if the form is submitted and the brand_id is provided
if (isset($_POST['brand_id'])) {


    $brand_id_filter = intval($_POST['brand_id']); // Sanitize input by converting to integer

    // Step 1: Get all item_id and item_name for the given brand_id
    $item_ids = [];
    $sql_items = "SELECT item_id, brand FROM items WHERE brand_id = ?";
    $stmt_items = $conn->prepare($sql_items);
    $stmt_items->bind_param("i", $brand_id_filter);
    $stmt_items->execute();
    $result_items = $stmt_items->get_result();

    while ($row = $result_items->fetch_assoc()) {
        $item_ids[] = $row['item_id'];
        $response['items'][] = $row['brand']; // Add item names to the response
    }

    // Close the items query statement
    $stmt_items->close();

    // Update total number of items
    $response['total_items'] = count($item_ids);

    // Step 2: Calculate total revenue and total quantity from orders based on extracted item_ids
    if (count($item_ids) > 0) {
        $placeholders = implode(',', array_fill(0, count($item_ids), '?'));
        $sql_revenue = "SELECT SUM(quantity * price) AS total_revenue, SUM(quantity) AS total_quantity FROM order_items WHERE item_id IN ($placeholders)";
        $stmt_revenue = $conn->prepare($sql_revenue);

        // Bind parameters dynamically
        $stmt_revenue->bind_param(str_repeat('i', count($item_ids)), ...$item_ids);
        $stmt_revenue->execute();
        $result_revenue = $stmt_revenue->get_result();

        if ($result_revenue->num_rows > 0) {
            $row = $result_revenue->fetch_assoc();
            $response['total_revenue'] = number_format($row['total_revenue'], 2);
            $response['total_quantity'] = $row['total_quantity'];
        } else {
            $response['total_revenue'] = '0.00';
            $response['total_quantity'] = 0;
        }

        // Close the revenue query statement
        $stmt_revenue->close();
    } else {
        $response['total_revenue'] = '0.00';
        $response['total_quantity'] = 0;
    }




   // Step 3: Fetch total visit_count across all pages
   $sql_visit_count = "SELECT SUM(visit_count) AS total_visit_count FROM page_visits";
   $result_visit_count = $conn->query($sql_visit_count);

   if ($result_visit_count && $result_visit_count->num_rows > 0) {
       $row = $result_visit_count->fetch_assoc();
       $response['visit_count'] = $row['total_visit_count'];
   } else {
       $response['visit_count'] = 0; // Default value if no visits are found
   }








} else {
    $response['error'] = 'Brand ID not provided.';
}

// Close the database connection
$conn->close();

// Set the content type to JSON and output the response
header('Content-Type: application/json');
echo json_encode($response);
?>
