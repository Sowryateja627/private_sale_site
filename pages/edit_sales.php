<?php
include '../includes/dbconnection.php';
session_start();

$sale_id = isset($_GET['sale_id']) ? intval($_GET['sale_id']) : 0;
$sale = null;

// Fetch sale details
if ($sale_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM sales WHERE sale_id = ?");
    $stmt->bind_param('i', $sale_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $sale = $result->fetch_assoc();

    $sale_start_date = $sale['start_date'];
$sale_end_date = $sale['end_date'];

$date_start = new DateTime($sale_start_date);
$formatted_start_date = $date_start->format('Y-m-d');

$date_end = new DateTime($sale_end_date);
$formatted_end_date = $date_end->format('Y-m-d');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Sale</title>
    <link rel="stylesheet" href="../css/editsale.css">
</head>
<body>
    <header>
    <div class="header-container">
        <h1>Private Sale Site</h1>
        <a href="customer_dashboard.php">Back to Dashboard</a>
        </div>
    </header>
    <div class="edit-container">
    <div class="card">

    <main>
        <form action="../includes/update_sale.php" method="post">
            <input type="hidden" name="sale_id" value="<?php echo htmlspecialchars($sale_id); ?>">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($sale['title']); ?>" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" required><?php echo htmlspecialchars($sale['description']); ?></textarea>

            <label for="start_date">Start Date:</label>
        <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($formatted_start_date); ?>" required>

        <label for="end_date">End Date:</label>
        <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($formatted_end_date); ?>" required>

            <button type="submit">Update Sale</button>
        </form>
    </main>
</div>    
</div>
    <footer>
        <div class="footer-container">
            <p>&copy; 2024 Private Sale Site. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
