<?php
require_once 'db.php';
session_start();

$botToken = 'YOUR_BOT_TOKEN';
$channelUsername = '@YourChannelUsername';

function checkChannelMembership($userId) {
    global $botToken, $channelUsername, $conn;
    
    $url = "https://api.telegram.org/bot{$botToken}/getChatMember";
    $data = [
        'chat_id' => $channelUsername,
        'user_id' => $userId
    ];
    
    $ch = curl_init($url . '?' . http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $result = json_decode($response, true);
    
    if ($result['ok'] && in_array($result['result']['status'], ['member', 'administrator', 'creator'])) {
        try {
            // Check if reward already given
            $stmt = $conn->prepare("SELECT channel_reward FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user['channel_reward']) {
                // Update coins and mark reward as given
                $stmt = $conn->prepare("UPDATE users SET coins = coins + 2500, channel_reward = 1 WHERE id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                return ['success' => true, 'message' => 'Reward of 2,500 coins added!'];
            }
        } catch (PDOException $e) {
            error_log("Channel reward error: " . $e->getMessage());
        }
    }
    
    return ['success' => false, 'message' => 'Please join the channel first!'];
}

if (isset($_POST['check_membership'])) {
    echo json_encode(checkChannelMembership($_SESSION['telegram_id']));
}
?> 