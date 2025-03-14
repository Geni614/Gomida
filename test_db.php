<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db.php';

try {
    // Test database connection
    $stmt = $conn->query("SELECT 1");
    echo "Database connection successful!<br>";
    
    // Test users table
    $stmt = $conn->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "Users table exists!<br>";
        
        // Show table structure
        $stmt = $conn->query("DESCRIBE users");
        echo "<pre>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            print_r($row);
        }
        echo "</pre>";
    } else {
        echo "Users table does not exist!";
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?> 