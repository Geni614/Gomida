<?php
require_once 'db.php';
session_start();

// Track invites and reward users
function trackInvite($referrer_id) {
    global $conn;
    
    try {
        // Get current invite count
        $stmt = $conn->prepare("SELECT invite_count, coins FROM users WHERE id = ?");
        $stmt->execute([$referrer_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $new_invite_count = ($user['invite_count'] ?? 0) + 1;
        
        // Update invite count
        $stmt = $conn->prepare("UPDATE users SET invite_count = ? WHERE id = ?");
        $stmt->execute([$new_invite_count, $referrer_id]);
        
        // If reached 5 invites, reward coins
        if ($new_invite_count >= 5 && $new_invite_count % 5 === 0) {
            $new_coins = $user['coins'] + 2500;
            $stmt = $conn->prepare("UPDATE users SET coins = ? WHERE id = ?");
            $stmt->execute([$new_coins, $referrer_id]);
            
            return true; // Reward given
        }
    } catch (PDOException $e) {
        error_log("Invite tracking error: " . $e->getMessage());
    }
    return false;
}
?> 