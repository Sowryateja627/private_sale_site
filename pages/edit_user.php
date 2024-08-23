<?php
include '../includes/dbconnection.php';
session_start();

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$user = null;

// Fetch user details
if ($user_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Handle possible missing keys
    $last_name = isset($user['last_name']) ? htmlspecialchars($user['last_name']) : '';
    $first_name = isset($user['first_name']) ? htmlspecialchars($user['first_name']) : '';
    $date_of_birth = isset($user['date_of_birth']) ? htmlspecialchars($user['date_of_birth']) : '';
    $postal_address = isset($user['postal_address']) ? htmlspecialchars($user['postal_address']) : '';
    $email_address = isset($user['email_address']) ? htmlspecialchars($user['email_address']) : '';
    $user_type = isset($user['user_type']) ? htmlspecialchars($user['user_type']) : '';
} else {
    echo "Invalid user ID.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="../css/edituserData1.css">
</head>
<body>
<header>
        <h1>Private Sale Site</h1>
    </header>

    <div class="edit-container">
        <div class="card">
            <form action="../includes/update_user.php" method="post">
            <h2>Edit User Details</h2>

                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">

                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo $last_name; ?>" required>

                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo $first_name; ?>" required>

                <label for="date_of_birth">Date of Birth:</label>
                <input type="date" id="date_of_birth" name="date_of_birth" value="<?php echo $date_of_birth; ?>" required>

                <label for="postal_address">Postal Address:</label>
                <textarea id="postal_address" name="postal_address" required><?php echo $postal_address; ?></textarea>

                <label for="email_address">Email Address:</label>
                <input type="email" id="email_address" name="email_address" value="<?php echo $email_address; ?>" required>

                <button type="submit">Update Details</button>
            </form>
            <a href="<?php echo $_SESSION['user_type'] === 'professional' ? 'professional_dashboard.php' : '#'; ?>" class="back-link">Back to Dashboard</a>
        
        </div>
    </div>

    <footer>
        <div class="footer-container">
            <p>&copy; 2024 Private Sale Site. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
