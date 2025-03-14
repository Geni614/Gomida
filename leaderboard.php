<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

try {
    // Changed username to name in the SELECT statement
    $stmt = $pdo->prepare("SELECT id, name, coins FROM users ORDER BY coins DESC LIMIT 50");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($users);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?> 