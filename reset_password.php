<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data using the correct field names
    $username = $_POST['username'] ?? '';
    $security_answer = $_POST['security_answer'] ?? '';
    $new_password = $_POST['new_password'] ?? '';

    // Check if all fields are filled
    if (empty($username) || empty($security_answer) || empty($new_password)) {
        die("All fields are required");
    }

    try {
        // Check if user exists and security answer is correct
        $stmt = $pdo->prepare("SELECT id FROM users WHERE name = ? AND security_answer = ?");
        $stmt->execute([$username, $security_answer]);

        if ($stmt->rowCount() > 0) {
            // Update password
            $update_stmt = $pdo->prepare("UPDATE users SET password = ? WHERE name = ?");
            $update_stmt->execute([$new_password, $username]);

            // Redirect to login page with success message
            header("Location: login.html?reset=success");
            exit();
        } else {
            echo "Invalid username or security answer. <a href='reset_password.html'>Try again</a>";
        }
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>