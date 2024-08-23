<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Item</title>
    <link rel="stylesheet" href="../css/item1.css">
</head>
<body>

<header>
        <div class="header-container">
            <h1> Private Sale Site</h1>
           
        </div>
    </header>
    <div class="form-card">
        <h1>Add New Item</h1>
       
              
       
       
        <form action="../includes/additems.php" method="post" enctype="multipart/form-data">
            <!-- Sales Dropdown -->
            <label for="sale">Sale:</label>
            <select id="sale" name="sale_id" required>
                <?php
                // Include the database connection file
                include '../includes/dbconnection.php'; // Adjust path as needed
                // Fetch sales for the select input
                $salesQuery = "SELECT sale_id, title FROM sales";
                $stmt = $conn->prepare($salesQuery);
                $stmt->execute();
                $sales = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                foreach ($sales as $sale) {
                    echo '<option value="' . htmlspecialchars($sale['sale_id']) . '">' . htmlspecialchars($sale['title']) . '</option>';
                }
                ?>
            </select>

            <!-- Brand Input -->
            <label for="brand">Brand:</label>
            <input type="text" id="brand" name="brand" required>

            <!-- Category Input -->
            <label for="category">Category:</label>
            <input type="text" id="category" name="category" required>

              <!-- Sub-Category Input -->
              <label for="category">Sub-Category:</label>
            <input type="text" id="subcategory" name="subcategory" required>

            <!-- Size Input -->
            <label for="size">Size:</label>
            <input type="text" id="size" name="size" required>

            <!-- Color Input -->
            <label for="color">Color:</label>
            <input type="text" id="color" name="color" required>

            <!-- Description Input -->
            <label for="description">Description:</label>
            <textarea id="description" name="description"></textarea>

            <!-- Photo Input -->
            <label for="photo">Photo:</label>
            <input type="file" id="photo" name="photo">

            <!-- Price Input -->
            <label for="price">Price:</label>
            <input type="text" id="price" name="price" required>

            <!-- Stock Quantity Input -->
            <label for="stock_quantity">Stock Quantity:</label>
            <input type="number" id="stock_quantity" name="stock_quantity" required>

            <button type="submit">Add Item</button>
        </form>
         
        
        <a href="professional_dashboard.php" class="back-link">Back to Dashboard</a>
        </div>

    <footer>
        <div class="footer-container">
            <p>&copy; 2024 Private Sale Site. All rights reserved.</p>
        </div>
    </footer>
    

</body>
</html>
