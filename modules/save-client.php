<?php
session_start();
require_once '../config/database.php';
require_once '../config/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_name = sanitize($_POST['company_name'] ?? '');
    $contact_name = sanitize($_POST['contact_name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $billing_address = sanitize($_POST['billing_address'] ?? '');

    if ($company_name !== '' && $email !== '' && $billing_address !== '') {
        $stmt = $pdo->prepare("INSERT INTO clients (company_name, contact_name, email, phone, billing_address) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$company_name, $contact_name, $email, $phone, $billing_address]);
        set_flash_message("Client added successfully.", "success");
    } else {
        set_flash_message("Failed to add client. Missing fields.", "danger");
    }
    
    header('Location: ../clients.php');
    exit;
}
