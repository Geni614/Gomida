<?php
require_once 'db.php';
session_start();

header('Content-Type: application/json');

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    $stmt = $conn->prepare("SELECT coins FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode([
            'success' => true,
            'coins' => $row['coins']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'User not found'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'User not logged in'
    ]);
}
?>
