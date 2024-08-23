<?php
include '../includes/dbconnection.php'; // Ensure the path is correct

session_start();

// Ensure the user is logged in and is a professional
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'professional') {
    header("Location: login.html"); // Redirect to login if not logged in or not a professional
    exit();
}

// Fetch all brands from the database
$brands = [];
$sql_brands = "SELECT * FROM brands"; // Assuming the table name is 'brands'
$result_brands = $conn->query($sql_brands);

if ($result_brands && $result_brands->num_rows > 0) {
    while ($row = $result_brands->fetch_assoc()) {
        $brands[] = $row;
    }
} else {
    $brands = [];
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professional Dashboard</title>
    <link rel="stylesheet" href="../css/profDash1.css"> <!-- Link to external CSS file -->
</head>
<body>
<header>
        <h1>Private Sale Site</h1>
        <nav>
            <a href="edit_user.php?user_id=<?php echo htmlspecialchars($_SESSION['user_id']); ?>">Account Settings</a>
            <a href="professional_dashboard.php">Dashboard</a>
            <a href="sales.html">Add New Sale</a>
            <a href="professionalview.php">View Existing Sales</a>
            <a href="additems.php">Add Items</a>
            <a href="login.html">Logout</a>
        </nav>
    </header>

    <!-- Main content section -->
    <div class="dashboard-container">
        <h1> Professional Analytical </h1>
        <!-- <p>You are logged in as a professional.</p> -->

        <!-- Add relevant dashboard links or content here -->
        <!-- <div class="dashboard-actions">
            <a href="sales.html" class="dashboard-button">Add New Sale</a>
            <a href="professionalview.php" class="dashboard-button">View Existing Sales</a>
        </div> -->

        <!-- Dropdown to select a brand -->
        <form onsubmit="handleFormSubmit(event)">
            <label for="brand">Select a Brand:</label>
            <select id="brand" name="brand_id">
                <?php foreach ($brands as $brand): ?>
                    <option value="<?php echo htmlspecialchars($brand['id']); ?>">
                        <?php echo htmlspecialchars($brand['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Submit</button>
        </form>

        <!-- Results table -->
        <table id="result-table" style="display:none;">
            <thead>
                <tr>
                  <th>Number of Items</th>
                   <th> Name</th>
                    <th>Items sold</th>
                    <th> Total Page visitors </th>
                    <th>Total Revenue</th>
                 
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td id="total-items"></td>
                   <td id="Items_Name"></td>
                    <td id="total-quantity"></td>
                    <td id="visit-count"></td>
                    <td id="total-revenue"></td>

                   
                </tr>
            </tbody>
        </table>

    </div>

    <!-- Footer section -->
    <footer>
        <p>&copy; 2024 Private Sale Site. All rights reserved.</p>
    </footer>

    <script>
        function handleFormSubmit(event) {
            event.preventDefault(); // Prevent the default form submission

            // Get the selected brand_id
            const brandId = document.getElementById('brand').value;

            // Send the data to the API using Fetch API
            fetch('../includes/professionalanaly.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    'brand_id': brandId
                })
            })
            .then(response => response.json())
            .then(data => {
                // Handle the response from the PHP script
                if (data.error) {
                    document.getElementById('result').innerText = `Error: ${data.error}`;
                    document.getElementById('result-table').style.display = 'none';
                } else {
                    // Populate the table with data
                    document.getElementById('total-revenue').innerText = `$${data.total_revenue}`;
                    document.getElementById('total-items').innerText = data.total_items;
                    document.getElementById('total-quantity').innerText = data.total_quantity;
                    document.getElementById('visit-count').innerText = data.visit_count;
                    document.getElementById('Items_Name').innerText = data.items.join(', ');

                    // Display the table
                    document.getElementById('result-table').style.display = 'table';
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
    
</body>
</html>
