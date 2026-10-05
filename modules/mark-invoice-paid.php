<?php
require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit('Unauthorized.');
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$token = $_POST['csrf_token'] ?? '';

if (!$id || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
    http_response_code(400);
    exit('Invalid request.');
}

$stmt = $pdo->prepare("UPDATE invoices SET status = 'Paid' WHERE id = ?");
$stmt->execute([$id]);

header('Location: ../view-invoice.php?id=' . $id);
exit;

?>