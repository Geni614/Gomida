<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['timer_end']) || !isset($data['is_in_cooldown'])) {
    echo json_encode(['error' => 'Missing timer data']);
    exit();
}

include 'db.php';

try {
    // Get current database timer state
    $stmt = $pdo->prepare("SELECT timer_end, is_in_cooldown FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $currentState = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Validate timer values
    $now = time() * 1000;
    $timerEnd = intval($data['timer_end']);
    $isInCooldown = (bool)$data['is_in_cooldown'];
    
    // If current timer hasn't expired, keep it
    if ($currentState && $currentState['timer_end'] > $now) {
        $timerEnd = $currentState['timer_end'];
        $isInCooldown = (bool)$currentState['is_in_cooldown'];
    } else {
        // Ensure timer values are within acceptable range
        $maxDuration = $isInCooldown ? 120000 : 60000; // 2 minutes for cooldown, 1 minute for active
        if ($timerEnd > ($now + $maxDuration)) {
            $timerEnd = $now + $maxDuration;
        }
    }

    // Update database
    $stmt = $pdo->prepare("UPDATE users SET timer_end = ?, is_in_cooldown = ? WHERE id = ?");
    $stmt->execute([
        $timerEnd,
        $isInCooldown ? 1 : 0,
        $_SESSION['user_id']
    ]);

    // Update session
    $_SESSION['timer_end'] = $timerEnd;
    $_SESSION['is_in_cooldown'] = $isInCooldown;

    // Return current state to client
    echo json_encode([
        'success' => true,
        'timer_end' => $timerEnd,
        'is_in_cooldown' => $isInCooldown
    ]);
    
} catch(PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['error' => 'Database error']);