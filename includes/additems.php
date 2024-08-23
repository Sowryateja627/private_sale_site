<?php
include 'dbconnection.php'; // Adjust path as needed

// Start output buffering
ob_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $sale_id = isset($_POST['sale_id']) ? intval($_POST['sale_id']) : 0;
    $brand = isset($_POST['brand']) ? filter_var($_POST['brand'], FILTER_SANITIZE_STRING) : '';
    $category = isset($_POST['category']) ? filter_var($_POST['category'], FILTER_SANITIZE_STRING) : '';
    $category_name = isset($_POST['category']) ? filter_var($_POST['category'], FILTER_SANITIZE_STRING) : '';
    $subcategory_name = isset($_POST['subcategory']) ? filter_var($_POST['subcategory'], FILTER_SANITIZE_STRING) : '';
    $size = isset($_POST['size']) ? filter_var($_POST['size'], FILTER_SANITIZE_STRING) : '';
    $color = isset($_POST['color']) ? filter_var($_POST['color'], FILTER_SANITIZE_STRING) : '';
    $description = isset($_POST['description']) ? filter_var($_POST['description'], FILTER_SANITIZE_STRING) : '';
    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0.0;
    $stock_quantity = isset($_POST['stock_quantity']) ? intval($_POST['stock_quantity']) : 0;

    // Handle file upload with error handling and validation
    $photo = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        $uploadFile = $uploadDir . uniqid() . '_' . basename($_FILES['photo']['name']);
    
        // Move the uploaded file to the destination directory
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadFile)) {
            $photo = basename($uploadFile); // Store the file name for the database
            echo "<script>alert('File uploaded successfully: " . htmlspecialchars($uploadFile, ENT_QUOTES, 'UTF-8') . "');</script>";
        } else {
            echo "<script>alert('Failed to upload photo.');</script>";
        }
    } else {
        echo "<script>alert('No file uploaded or upload error.');</script>";
    }

    // Handle category insertion and retrieval
    try {
        // Check if the category already exists
        $sql = "SELECT id FROM Category WHERE name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $category_name);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Category exists, get the ID
            $stmt->bind_result($category_id);
            $stmt->fetch();
        } else {
            // Category does not exist, insert new category
            $stmt->close();
            $sql = "INSERT INTO Category (name) VALUES (?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('s', $category_name);
            $stmt->execute();
            $category_id = $stmt->insert_id;
        }

        // Check if the subcategory already exists
        $sql = "SELECT id FROM Subcategory WHERE name = ? AND category_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $subcategory_name, $category_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Subcategory exists, get the ID
            $stmt->bind_result($subcategory_id);
            $stmt->fetch();
        } else {
            // Subcategory does not exist, insert new subcategory
            $stmt->close();
            $sql = "INSERT INTO Subcategory (name, category_id) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('si', $subcategory_name, $category_id);
            $stmt->execute();
            $subcategory_id = $stmt->insert_id;
        }

        // Check if the brand already exists
        $sql = "SELECT id FROM brands WHERE name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $brand);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Brand exists, get the ID
            $stmt->bind_result($brand_id);
            $stmt->fetch();
        } else {
            // Brand does not exist, insert new brand
            $stmt->close();
            $sql = "INSERT INTO brands (name) VALUES (?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('s', $brand);
            $stmt->execute();
            $brand_id = $stmt->insert_id;
        }
        
        // Prepare and execute the SQL statement for inserting an item
        $sql = "
            INSERT INTO items (sale_id, brand_id, category_id, subcategory_id, brand,category, size, color, description, photo, price, stock_quantity)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?)
        ";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param(
                'iiiissssssdi', // Define the parameter types correctly
                $sale_id,
                $brand_id, // Use the retrieved or newly inserted brand ID
                $category_id, // Use the retrieved or newly inserted category ID
                $subcategory_id, // Use the retrieved or newly inserted subcategory ID
                $brand,
                $category ,
                $size,
                $color,
                $description,
                $photo,
                $price,
                $stock_quantity
            );

            $stmt->execute();
            ob_end_flush(); // Flush the output buffer before redirecting
            header("Location: ../pages/professional_dashboard.php"); // Redirect after successful insertion
            exit();
        } else {
            echo "Failed to prepare statement.";
        }
    } catch (mysqli_sql_exception $e) {
        echo "Database error: " . $e->getMessage();
    }
} else {
    // Not a POST request
    echo "Invalid request.";
}

// End output buffering
ob_end_flush();
?>
