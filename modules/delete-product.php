<?php
require_once '../config/database.php';
require_once '../config/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized.");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    $stmt = $pdo->prepare("DELETE FROM products_services WHERE id = ?");
    if ($stmt->execute([$id])) {
        set_flash_message("Product/Service deleted successfully.", "success");
    } else {
        set_flash_message("Failed to delete product.", "danger");
    }
}
header('Location: ../products.php');
exit;
?>
