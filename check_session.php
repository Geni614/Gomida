<?php
header('Content-Type: application/json');
session_start();

$response = ['logged_in' => false];

if (!isset($_SESSION['user_id'])) {
    $response['error'] = 'Not logged in';
} else {
    require_once 'db.php';
    
    try {
        $conn = getConnection();
        
        // Verify user exists in database
        $stmt = $conn->prepare("SELECT id, name, coins, referral_code, channel_reward FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $response['logged_in'] = true;
            $response['user_id'] = $user['id'];
            $response['name'] = $user['name'];
            $response['coins'] = (int)$user['coins'];
            $response['referral_code'] = $user['referral_code'];
            $response['channel_reward'] = (bool)($user['channel_reward'] ?? false);
            
            // Update session with latest data
            $_SESSION['name'] = $user['name'];
            $_SESSION['coins'] = (int)$user['coins'];
            $_SESSION['referral_code'] = $user['referral_code'];
            $_SESSION['channel_reward'] = (bool)($user['channel_reward'] ?? false);
        } else {
            // Session exists but user not found in database
            session_destroy();
            $response['error'] = 'Invalid session';
        }
    } catch (PDOException $e) {
        $response['error'] = 'Database error';
        error_log("Session check error: " . $e->getMessage());
    }
}

echo json_encode($response);