<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Database connection
$host = 'localhost';
$dbname = 'gomida';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $password = $_POST['password'];

    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE name = ? AND password = ?");
        $stmt->execute([$name, $password]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['name'];

            // Fetch timer_end from database
            $stmt = $conn->prepare("SELECT timer_end, is_in_cooldown FROM users WHERE name = ?");
            $stmt->execute([$name]);
            $timerData = $stmt->fetch();

            // Get current timestamp
            $now = time() * 1000; // Convert to milliseconds to match JavaScript

            if ($timerData) {
                $timerEnd = intval($timerData['timer_end']);
                
                // If timer has expired, give the user a fresh active timer
                if ($timerEnd <= $now) {
                    $timerEnd = $now + (60 * 1000); // 60 seconds in milliseconds
                    $isInCooldown = false;
                    
                    // Update the database
                    $updateStmt = $conn->prepare("UPDATE users SET timer_end = ?, is_in_cooldown = 0 WHERE name = ?");
                    $updateStmt->execute([$timerEnd, $name]);
                } else {
                    $isInCooldown = (bool)$timerData['is_in_cooldown'];
                }
                
                $_SESSION['timer_end'] = $timerEnd;
                $_SESSION['is_in_cooldown'] = $isInCooldown;
            } else {
                // Default values for new users
                $_SESSION['timer_end'] = $now + (60 * 1000); // 60 seconds active timer
                $_SESSION['is_in_cooldown'] = false;
            }

            header("Location: index.html");
            exit();
        } else {
            header("Location: login.html?error=invalid");
            exit();
        }
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    header("Location: login.html?error=Invalid request method");
    exit();
}
?>