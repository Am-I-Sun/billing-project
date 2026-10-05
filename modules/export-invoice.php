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
if (!$id) {
    set_flash_message("Invalid invoice ID for export.", "danger");
    header("Location: ../index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT i.*, c.company_name, c.email FROM invoices i JOIN clients c ON i.client_id = c.id WHERE i.id = ?");
$stmt->execute([$id]);
$invoice = $stmt->fetch();

if (!$invoice) {
    set_flash_message("Invoice not found.", "danger");
    header("Location: ../index.php");
    exit;
}

$itemsStmt = $pdo->prepare("SELECT * FROM invoice_items WHERE invoice_id = ?");
$itemsStmt->execute([$id]);
$items = $itemsStmt->fetchAll();

// Construct file content
$content = "====================================\n";
$content .= "           BILLING SYSTEM           \n";
$content .= "====================================\n\n";
$content .= "INVOICE #: " . $invoice['invoice_number'] . "\n";
$content .= "STATUS: " . $invoice['status'] . "\n";
$content .= "ISSUE DATE: " . $invoice['issue_date'] . "\n";
$content .= "DUE DATE: " . $invoice['due_date'] . "\n\n";
$content .= "BILLED TO:\n";
$content .= $invoice['company_name'] . "\n";
$content .= $invoice['email'] . "\n\n";
$content .= "------------------------------------\n";
$content .= "ITEMS:\n";

foreach ($items as $item) {
    $content .= "- " . $item['item_name'] . " (x" . $item['quantity'] . ") @ $" . number_format($item['unit_price'], 2) . " = $" . number_format($item['line_total'], 2) . "\n";
}
$content .= "------------------------------------\n";
$content .= "SUBTOTAL: $" . number_format($invoice['subtotal'], 2) . "\n";
if ($invoice['tax_rate'] > 0) {
    $content .= "TAX (" . floatval($invoice['tax_rate']) . "%): $" . number_format($invoice['subtotal'] * ($invoice['tax_rate']/100), 2) . "\n";
}
if ($invoice['discount_amount'] > 0) {
    $content .= "DISCOUNT: -$" . number_format($invoice['discount_amount'], 2) . "\n";
}
$content .= "TOTAL: $" . number_format($invoice['total_amount'], 2) . "\n\n";
$content .= "====================================\n";
$content .= "Thank you for your business!\n";

$filename = 'invoice_' . $invoice['invoice_number'] . '_' . time() . '.txt';
$filepath = '../exports/' . $filename;

// FILE HANDLING: Write to file
$handle = fopen($filepath, 'w');
if ($handle) {
    fwrite($handle, $content);
    fclose($handle);
    
    // Trigger download
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($filepath));
    readfile($filepath);
    exit;
} else {
    set_flash_message("Failed to create export file.", "danger");
    header("Location: ../view-invoice.php?id=" . $id);
    exit;
}
?>
