<?php
session_start();

require_once 'db.php';

function generateReferralCode($length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $referralCode = '';
    for ($i = 0; $i < $length; $i++) {
        $referralCode .= $characters[rand(0, $charactersLength - 1)];
    }
    return $referralCode;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    // Validate inputs
    if (empty($name) || empty($phone) || !preg_match('/^(09|07)\d{8}$/', $phone)) {
        header("Location: index.html?error=Invalid input");
        exit();
    }

    try {
        // Get database connection
        $conn = getConnection();
        
        // Convert phone to integer for database storage
        $phoneInt = intval($phone);
        
        // Check if phone number already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE phone = ?");
        $stmt->execute([$phoneInt]);
        
        if ($stmt->fetch()) {
            header("Location: index.html?error=Phone number already registered");
            exit();
        } else {
            // Generate a unique referral code
            $referralCode = generateReferralCode();
            while (true) {
                $stmt = $conn->prepare("SELECT id FROM users WHERE referral_code = ?");
                $stmt->execute([$referralCode]);
                if (!$stmt->fetch()) {
                    break;
                }
                $referralCode = generateReferralCode();
            }

            // Insert new user with all essential fields initialized
            $stmt = $conn->prepare("
                INSERT INTO users (
                    name, 
                    phone, 
                    coins,
                    timer_end,
                    is_in_cooldown,
                    referral_code
                ) VALUES (
                    ?, -- name
                    ?, -- phone
                    0, -- initial coins
                    ?, -- timer_end (set to current time + 60 seconds)
                    0, -- is_in_cooldown
                    ?  -- referral_code
                )
            ");
            
            $timer_end = time() + 60; // Set initial timer to 60 seconds
            
            if ($stmt->execute([$name, $phoneInt, $timer_end, $referralCode])) {
                $userId = $conn->lastInsertId();
                
                // Set session variables
                $_SESSION['user_id'] = $userId;
                $_SESSION['name'] = $name;
                $_SESSION['coins'] = 0;
                
                // Redirect to game.html
                header("Location: game.html");
                exit();
            } else {
                $errorInfo = $stmt->errorInfo();
                error_log("Signup failed - Database error: " . print_r($errorInfo, true));
                header("Location: index.html?error=Failed to create account");
                exit();
            }
        }
    } catch (PDOException $e) {
        error_log("Signup failed - PDO Exception: " . $e->getMessage());
        header("Location: index.html?error=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    header("Location: index.html?error=Invalid request method");
    exit();
}
?>