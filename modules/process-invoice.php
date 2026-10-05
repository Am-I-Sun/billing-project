<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_id = intval($_POST['client_id'] ?? 0);
    $invoice_number = trim($_POST['invoice_number'] ?? '');
    $issue_date = trim($_POST['issue_date'] ?? date('Y-m-d'));
    $due_date = trim($_POST['due_date'] ?? date('Y-m-d'));
    $tax_rate = floatval($_POST['tax_rate'] ?? 0);
    $discount_amount = floatval($_POST['discount_amount'] ?? 0);
    $notes = trim($_POST['notes'] ?? '');
    $items = $_POST['items'] ?? [];

    // Business Invariant Verification
    if (strtotime($due_date) < strtotime($issue_date)) {
        die("Error: Due date cannot be before the issue date.");
    }
    if ($client_id === 0 || empty($invoice_number) || empty($items)) {
        die("Error: Missing required fields.");
    }

    try {
        $pdo->beginTransaction();

        // Subtotal calculation placeholder
        $subtotal = 0.00;

        // Insert Master Invoice initially with 0 totals to get ID
        $stmt = $pdo->prepare("INSERT INTO invoices (invoice_number, client_id, issue_date, due_date, tax_rate, discount_amount, subtotal, total_amount, notes, status) VALUES (?, ?, ?, ?, ?, ?, 0, 0, ?, 'Sent')");
        $stmt->execute([$invoice_number, $client_id, $issue_date, $due_date, $tax_rate, $discount_amount, $notes]);
        $invoice_id = $pdo->lastInsertId();

        // Process Items
        $itemStmt = $pdo->prepare("INSERT INTO invoice_items (invoice_id, product_id, item_name, item_description, quantity, unit_price) VALUES (?, ?, ?, ?, ?, ?)");
        
        foreach ($items as $item) {
            $product_id = !empty($item['product_id']) ? intval($item['product_id']) : null;
            $item_name = trim($item['item_name'] ?? 'Custom Item');
            $item_desc = trim($item['item_description'] ?? '');
            $qty = floatval($item['quantity'] ?? 0);
            $price = floatval($item['unit_price'] ?? 0);

            if ($qty > 0 && $price >= 0) {
                $itemStmt->execute([$invoice_id, $product_id, $item_name, $item_desc, $qty, $price]);
                $subtotal += ($qty * $price);
            }
        }

        // Master Calculations
        $tax_amount = $subtotal * ($tax_rate / 100);
        $total_amount = $subtotal + $tax_amount - $discount_amount;
        if ($total_amount < 0) $total_amount = 0;

        // Update Master Invoice
        $updateStmt = $pdo->prepare("UPDATE invoices SET subtotal = ?, total_amount = ? WHERE id = ?");
        $updateStmt->execute([$subtotal, $total_amount, $invoice_id]);

        $pdo->commit();
        header("Location: ../view-invoice.php?id=" . $invoice_id);
        exit;
    } catch (\PDOException $e) {
        $pdo->rollBack();
        die("Database error: " . $e->getMessage());
    }
}
