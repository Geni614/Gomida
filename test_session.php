<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Session Data:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>Cookie Data:</h2>";
echo "<pre>";
print_r($_COOKIE);
echo "</pre>";

if (isset($_SESSION['user_id'])) {
    require_once 'db.php';
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<h2>User Data from Database:</h2>";
    echo "<pre>";
    print_r($user);
    echo "</pre>";
}
?> 