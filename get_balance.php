<?php
header('Content-Type: application/json');
session_start();

$response = ['success' => false];

if (!isset($_SESSION['user_id'])) {
    $response['error'] = 'Not logged in';
    echo json_encode($response);
    exit;
}

require_once 'db.php';

try {
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT coins FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $response['success'] = true;
        $response['coins'] = (int)$row['coins'];
    } else {
        $response['error'] = 'User not found';
    }
} catch (PDOException $e) {
    error_log("Database error in get_balance: " . $e->getMessage());
    $response['error'] = 'Database error occurred';
}

echo json_encode($response);