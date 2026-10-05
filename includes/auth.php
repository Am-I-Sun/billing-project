<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/database.php';

// Check if not logged in via session
if (!isset($_SESSION['user_id'])) {
    
    // Check if remember_me cookie exists
    if (isset($_COOKIE['remember_user'])) {
        $username = $_COOKIE['remember_user'];
        
        // Very basic validation against DB for the cookie
        $stmt = $pdo->prepare("SELECT id, username FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
        } else {
            // Invalid cookie
            setcookie('remember_user', '', time() - 3600, '/');
            header('Location: login.php');
            exit;
        }
    } else {
        header('Location: login.php');
        exit;
    }
}
?>
