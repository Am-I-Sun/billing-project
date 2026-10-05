<?php
session_start();
require_once 'config/database.php';
require_once 'config/functions.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']) ? true : false;

    if ($username === '' || $password === '') {
        $error = "Please enter both username and password.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            if ($remember) {
                // Set cookie for 30 days
                setcookie('remember_user', $user['username'], time() + (86400 * 30), "/");
            }
            
            set_flash_message("Welcome back, " . $user['username'] . "!", "success");
            header("Location: index.php");
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Billing System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/custom.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center py-4 bg-body-tertiary" style="height: 100vh;">

<main class="form-signin w-100 m-auto" style="max-width: 400px;">
    <div class="card border-0 shadow-lg p-4">
        <form method="POST" action="login.php">
            <div class="text-center mb-4">
                <i class="bi bi-lightning-charge-fill text-success" style="font-size: 3rem;"></i>
                <h1 class="h3 mb-3 fw-bold mt-2">Sign In</h1>
                <p class="text-muted">Welcome to Billing System</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger p-2 small"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php display_flash(); ?>

            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="floatingInput" name="username" placeholder="admin" required>
                <label for="floatingInput" class="text-muted">Username</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" class="form-control" id="floatingPassword" name="password" placeholder="Password" required>
                <label for="floatingPassword" class="text-muted">Password</label>
            </div>

            <div class="form-check text-start my-3">
                <input class="form-check-input" type="checkbox" name="remember" value="remember-me" id="flexCheckDefault">
                <label class="form-check-label text-muted" for="flexCheckDefault">
                    Remember me
                </label>
            </div>
            <button class="btn btn-success w-100 py-2 fw-medium" type="submit">Sign in</button>
            <div class="mt-4 text-center text-muted small">
                Default login: admin / password
            </div>
        </form>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
