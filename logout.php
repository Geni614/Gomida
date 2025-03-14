<?php
session_start();

// Clear timer_end from session
unset($_SESSION['timer_end']);

session_destroy();

header("Location: login.html");
exit();
?>