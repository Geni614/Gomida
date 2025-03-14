<?php
header('Content-Type: application/json');
session_start();

require_once 'db.php';

$response = ['success' => false, 'error' => ''];

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $response['error'] = 'Not logged in';
    echo json_encode($response);
    exit;
}

// Get the user ID from session
$userId = $_SESSION['user_id'];

try {
    $conn = getConnection();
    
    // First, check if the channel_reward column exists in the users table
    $stmt = $conn->prepare("SHOW COLUMNS FROM users LIKE 'channel_reward'");
    $stmt->execute();
    
    // If the column doesn't exist, add it
    if ($stmt->rowCount() == 0) {
        $conn->exec("ALTER TABLE users ADD COLUMN channel_reward TINYINT(1) DEFAULT 0");
    }
    
    // Check if user has already claimed the channel reward
    $stmt = $conn->prepare("SELECT channel_reward, coins FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && $user['channel_reward'] == 0) {
        // Award coins and mark reward as claimed
        $stmt = $conn->prepare("UPDATE users SET channel_reward = 1, coins = coins + 5000 WHERE id = ?");
        if ($stmt->execute([$userId])) {
            // Get updated balance
            $stmt = $conn->prepare("SELECT coins FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $updatedUser = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Update session
            $_SESSION['coins'] = $updatedUser['coins'];
            
            $response['success'] = true;
            $response['new_balance'] = $updatedUser['coins'];
            $response['message'] = 'You received 5000 coins for joining the Telegram channel!';
        } else {
            $response['error'] = 'Failed to update user record';
        }
    } else {
        $response['error'] = 'Reward already claimed';
    }
} catch (PDOException $e) {
    error_log("Error in Telegram join: " . $e->getMessage());
    $response['error'] = 'Database error occurred';
}

echo json_encode($response);