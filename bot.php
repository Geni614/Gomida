<?php
$botToken = "6892830978:AAGxZxEGPxPxVXXXXXXXXXXXXXXXXXXXX";
$website = "https://api.telegram.org/bot".$botToken;

$update = file_get_contents('php://input');
$update = json_decode($update, TRUE);

$chatId = $update["message"]["chat"]["id"];
$message = $update["message"]["text"];

if($message == "/start"){
    $response = "Welcome to Gomida Bot! Click the button below to start playing.";
    $keyboard = [
        'inline_keyboard' => [
            [['text' => 'Play Now', 'web_app' => ['url' => 'https://yourdomain.com/index.html']]]
        ]
    ];
    file_get_contents($website."/sendMessage?chat_id=".$chatId."&text=".$response."&reply_markup=".json_encode($keyboard));
}
?>
