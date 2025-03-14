<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db.php';

try {
    // Check if table exists
    $stmt = $conn->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() == 0) {
        // Create table if it doesn't exist
        $sql = "CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            security_question VARCHAR(255) NOT NULL,
            security_answer VARCHAR(255) NOT NULL,
            telegram_id INT NOT NULL,
            coins INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        $conn->exec($sql);
        echo "Table 'users' created successfully!";
    } else {
        // Show table structure if it exists
        $stmt = $conn->query("DESCRIBE users");
        echo "Table 'users' structure:<br>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo print_r($row, true) . "<br>";
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 