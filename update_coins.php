<?php
// update_coins.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';

header('Content-Type: application/json');

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $input = json_decode(file_get_contents('php://input'), true);
    $newCoins = intval($input['coins'] ?? 0);

    try {
        $conn = getConnection();
        $stmt = $conn->prepare("UPDATE users SET coins = ? WHERE id = ?");
        if ($stmt->execute([$newCoins, $_SESSION['user_id']])) {
            $response['success'] = true;
        }
    } catch (PDOException $e) {
        error_log("Error updating coins: " . $e->getMessage());
    }
}

echo json_encode($response);
?>