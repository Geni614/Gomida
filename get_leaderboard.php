<?php
require_once 'db.php';

$response = ['success' => false, 'leaderboard' => []];

try {
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT name, coins FROM users ORDER BY coins DESC");
    $stmt->execute();
    $response['leaderboard'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response['success'] = true;
} catch (PDOException $e) {
    error_log("Error fetching leaderboard data: " . $e->getMessage());
}

echo json_encode($response);
?>