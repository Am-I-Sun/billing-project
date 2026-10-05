<?php
session_start();
require_once 'config/functions.php';

// Destroy session
$_SESSION = array();
session_destroy();

// Destroy cookie
if (isset($_COOKIE['remember_user'])) {
    setcookie('remember_user', '', time() - 3600, '/');
}

session_start(); // Start a fresh session to hold the flash message
set_flash_message("You have been successfully logged out.", "info");

header("Location: login.php");
exit;
?>
