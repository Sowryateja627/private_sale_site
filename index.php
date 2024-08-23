<?php
session_start();
include 'includes/dbconnection.php'; // Ensure this path is correct
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professional Dashboard</title>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <header>
        <div class="header-container">
            <h1>Welcome to the Private Sale Site</h1>
           
        </div>
    </header>

    <main>
        <div class="main-container">
            <h2>Welcome to our private sale site!</h2>
            <p>Explore exclusive discounts and offers available only to registered members.</p>
            <p><a href="pages/login.html">Login</a> or <a href="pages/signup.html">Register</a> to access our sales and updates.</p>
        </div>
    </main>

    <footer>
        <div class="footer-container">
            <p>&copy; 2024 Private Sale Site. All rights reserved.</p>
        </div>
    </footer>
</body>


</html>
