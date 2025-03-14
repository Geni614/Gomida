
Proposed edit:
index.html
+11
-4
 55
Apply
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gomida</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="top-bar">
        <div class="balance-container">
            <img src="./assets/images/g.png" alt="coin" width="45px">
            <span id="balance">0</span>
        </div>
        <div class="timer-container">
            <span id="timer">60</span>
        </div>
    </div>

    <div class="container">
        <div class="coin-wrapper">
            <div class="magnetic-field">
                <div class="field-circle"></div>
            </div>
            <div class="coin-container" id="main-coin">
                <img src="./assets/images/rr.png" class="coin-image" alt="coin" />
            </div>
        </div>

        <div class="menu-container">
            <div class="menu-buttons">
                <a href="frens.php" class="menu-button">
                    <img src="./assets/images/friends.png" alt="friends" width="28px">
                    <span>Invite</span>
                </a>
                <a href="earn.php" class="menu-button">
                    <img src="./assets/images/mark.png" alt="tasks" width="28px">
                    <span>Tasks</span>
                </a>
                <a href="boost.php" class="menu-button">
                    <img src="./assets/images/nice.png" alt="balance" width="28px">
                    <span>Balance</span>
                </a>
                <a href="leaderboard.php" class="menu-button">
                    <img src="./assets/images/Trophy.png" alt="leaderboard" width="28px">
                    <span>Leaders</span>
                </a>
                <a href="logout.php" class="menu-button">
                    <img src="./assets/images/logout.png" alt="logout" width="28px">
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </div>

    <script src="assets/js/game.js"></script>
</body>
</html>