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
    
    // First check if user can collect coins (not in cooldown and has time left)
    $stmt = $conn->prepare("SELECT timer_end, is_in_cooldown FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        $response['error'] = 'User not found';
        echo json_encode($response);
        exit;
    }
    
    $now = time();
    if ($user['is_in_cooldown']) {
        $response['error'] = 'Cannot collect coins during cooldown';
        echo json_encode($response);
        exit;
    }
    
    if ($user['timer_end'] && $user['timer_end'] <= $now) {
        $response['error'] = 'Timer has expired';
        echo json_encode($response);
        exit;
    }
    
    // Update coin balance
    $stmt = $conn->prepare("UPDATE users SET coins = coins + 1 WHERE id = ?");
    if ($stmt->execute([$_SESSION['user_id']])) {
        // Get new balance and updated timer info
        $stmt = $conn->prepare("SELECT coins, timer_end, is_in_cooldown FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $response['success'] = true;
        $response['new_balance'] = $userData['coins'];
        $response['timer_end'] = $userData['timer_end'];
        $response['is_in_cooldown'] = $userData['is_in_cooldown'];
    }
} catch (PDOException $e) {
    error_log("Error collecting coin: " . $e->getMessage());
    $response['error'] = 'Database error';
}

echo json_encode($response);
?>