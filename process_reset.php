<?php
require_once 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    
    if (!isset($_POST['security_answer'])) {
        // First step: Get security question
        $stmt = $conn->prepare("SELECT security_question FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            echo json_encode([
                'success' => true,
                'step' => 'question',
                'question' => $user['security_question']
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found']);
        }
    } else {
        // Second step: Verify answer and reset password
        $security_answer = $_POST['security_answer'];
        $new_password = $_POST['new_password'];
        
        $stmt = $conn->prepare("SELECT id, security_answer FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($security_answer, $user['security_answer'])) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $update_stmt->bind_param("si", $hashed_password, $user['id']);
                
                if ($update_stmt->execute()) {
                    echo json_encode(['success' => true, 'step' => 'reset']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error updating password']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Incorrect security answer']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found']);
        }
    }
}
?>
