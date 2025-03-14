<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db.php';

// Test database connection
try {
    $stmt = $conn->query("SELECT 1");
    echo "Database connection successful!<br>";
    
    // Show the last 5 users in the database
    $stmt = $conn->query("SELECT id, name, email, password FROM users ORDER BY id DESC LIMIT 5");
    echo "<h3>Last 5 registered users:</h3>";
    echo "<pre>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
        echo "\n";
    }
    echo "</pre>";
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>