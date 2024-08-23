<?php
$servername = "localhost";
$username = "root";
$password = "new_password"; 
$dbname = "private_site_db";

// Create a new MySQLi instance
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
