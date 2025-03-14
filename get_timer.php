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
    $now = time();
    
    // Get current timer state
    $stmt = $conn->prepare("SELECT timer_end, is_in_cooldown FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        $response['error'] = 'User not found';
        echo json_encode($response);
        exit;
    }
    
    // Handle timer expiration and cooldown transitions
    if ($user['timer_end'] && $user['timer_end'] <= $now) {
        if ($user['is_in_cooldown']) {
            // Cooldown period ended, start new active period
            $new_timer_end = $now + 60; // 60 seconds active period
            $stmt = $conn->prepare("UPDATE users SET timer_end = ?, is_in_cooldown = 0 WHERE id = ?");
            $stmt->execute([$new_timer_end, $_SESSION['user_id']]);
            
            $user['timer_end'] = $new_timer_end;
            $user['is_in_cooldown'] = 0;
        } else {
            // Active period ended, start cooldown
            $new_timer_end = $now + 120; // 120 seconds cooldown
            $stmt = $conn->prepare("UPDATE users SET timer_end = ?, is_in_cooldown = 1 WHERE id = ?");
            $stmt->execute([$new_timer_end, $_SESSION['user_id']]);
            
            $user['timer_end'] = $new_timer_end;
            $user['is_in_cooldown'] = 1;
        }
    } else if (!$user['timer_end']) {
        // No timer set, start with active period
        $new_timer_end = $now + 60; // 60 seconds active period
        $stmt = $conn->prepare("UPDATE users SET timer_end = ?, is_in_cooldown = 0 WHERE id = ?");
        $stmt->execute([$new_timer_end, $_SESSION['user_id']]);
        
        $user['timer_end'] = $new_timer_end;
        $user['is_in_cooldown'] = 0;
    }
    
    $response['success'] = true;
    $response['timer_end'] = (int)$user['timer_end'];
    $response['is_in_cooldown'] = (int)$user['is_in_cooldown'];
    $response['current_time'] = $now;
    
} catch (PDOException $e) {
    error_log("Database error in get_timer: " . $e->getMessage());
    $response['error'] = 'Database error occurred';
}

echo json_encode($response);
?>