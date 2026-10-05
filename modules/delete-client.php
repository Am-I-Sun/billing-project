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
    // Check if client has invoices first
    $check = $pdo->prepare("SELECT COUNT(*) FROM invoices WHERE client_id = ?");
    $check->execute([$id]);
    if ($check->fetchColumn() > 0) {
        set_flash_message("Cannot delete client. They have existing invoices.", "danger");
    } else {
        $stmt = $pdo->prepare("DELETE FROM clients WHERE id = ?");
        if ($stmt->execute([$id])) {
            set_flash_message("Client deleted successfully.", "success");
        } else {
            set_flash_message("Failed to delete client.", "danger");
        }
    }
}
header('Location: ../clients.php');
exit;
?>
