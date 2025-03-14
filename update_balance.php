<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$coins = $data['coins'] ?? 0;

if (isset($_SESSION['user_id'])) {
    try {
        $stmt = $conn->prepare("UPDATE users SET coins = ? WHERE id = ?");
        $stmt->execute([$coins, $_SESSION['user_id']]);
        echo json_encode(['success' => true, 'coins' => $coins]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
}
?> 