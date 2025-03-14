<?php
session_start();

require_once 'db.php';

$response = ['success' => false];

if (isset($_SESSION['user_id'])) {
    try {
        $conn = getConnection();
        $stmt = $conn->prepare("SELECT coins, timer_end, is_in_cooldown FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $response['success'] = true;
            $response['coins'] = $user['coins'];
            $response['timer'] = max(0, $user['timer_end'] - time());
            $response['is_in_cooldown'] = $user['is_in_cooldown'];
        }
    } catch (PDOException $e) {
        error_log("Error fetching user data: " . $e->getMessage());
    }
}

echo json_encode($response);
?>