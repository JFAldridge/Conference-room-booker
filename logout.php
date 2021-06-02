<?php
session_start();

// Destroy session
unset($_SESSION['user_id']);
session_destroy();

// Redirect to login

header('Location: login.php');
exit;
?>