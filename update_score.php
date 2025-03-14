<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

// Get data from request
$data = json_decode(file_get_contents('php://input'), true);
$coins = $data['coins'] ?? 0;

if (isset($_SESSION['user_id'])) {
    try {
        // Update user's coins in database
        $stmt = $conn->prepare("UPDATE users SET coins = ? WHERE id = ?");
        $stmt->execute([$coins, $_SESSION['user_id']]);
        
        echo json_encode(['success' => true, 'message' => 'Score updated successfully']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
}
?> 