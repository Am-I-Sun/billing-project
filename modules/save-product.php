<?php
session_start();
require_once '../config/database.php';
require_once '../config/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $unit_price = floatval($_POST['unit_price'] ?? 0);

    if ($name !== '' && $unit_price >= 0) {
        $stmt = $pdo->prepare("INSERT INTO products_services (name, description, unit_price) VALUES (?, ?, ?)");
        $stmt->execute([$name, $description, $unit_price]);
        set_flash_message("Product/Service added successfully.", "success");
    } else {
        set_flash_message("Failed to add product. Invalid input.", "danger");
    }
    
    header('Location: ../products.php');
    exit;
}
