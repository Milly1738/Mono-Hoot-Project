<?php
$host = "localhost";
$username = "root";  // Default XAMPP username
$password = "";      // Default XAMPP password is empty
$dbname = "test_db"; // The database name you created in Step 2

// Create connection using PDO (the secure, modern way)
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // Set error mode to exception to catch mistakes early
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully!"; 
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>