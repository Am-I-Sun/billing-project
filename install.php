<?php
/**
 * install.php — One-time database installer.
 * Visit http://localhost/billing-project/install.php to run.
 * DELETE THIS FILE after setup is complete.
 */
require_once 'config/database.php';

$results = [];
$hasError = false;

$queries = [
    "Create clients table" => "CREATE TABLE IF NOT EXISTS clients (
        id INT AUTO_INCREMENT PRIMARY KEY,
        company_name VARCHAR(100) NOT NULL,
        contact_name VARCHAR(100),
        email VARCHAR(100) NOT NULL,
        phone VARCHAR(20),
        billing_address TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",

    "Create products_services table" => "CREATE TABLE IF NOT EXISTS products_services (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        unit_price DECIMAL(10,2) NOT NULL
    )",

    "Create invoices table" => "CREATE TABLE IF NOT EXISTS invoices (
        id INT AUTO_INCREMENT PRIMARY KEY,
        invoice_number VARCHAR(50) UNIQUE NOT NULL,
        client_id INT NOT NULL,
        issue_date DATE NOT NULL,
        due_date DATE NOT NULL,
        tax_rate DECIMAL(5,2) DEFAULT 0.00,
        discount_amount DECIMAL(10,2) DEFAULT 0.00,
        subtotal DECIMAL(10,2) DEFAULT 0.00,
        total_amount DECIMAL(10,2) DEFAULT 0.00,
        status ENUM('Draft','Sent','Paid','Overdue') DEFAULT 'Draft',
        notes TEXT,
        FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE RESTRICT
    )",

    "Create invoice_items table" => "CREATE TABLE IF NOT EXISTS invoice_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        invoice_id INT NOT NULL,
        product_id INT NULL,
        item_name VARCHAR(100) NOT NULL,
        item_description TEXT,
        quantity DECIMAL(6,2) NOT NULL,
        unit_price DECIMAL(10,2) NOT NULL,
        line_total DECIMAL(10,2) GENERATED ALWAYS AS (quantity * unit_price) STORED,
        FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products_services(id) ON DELETE SET NULL
    )",

    "Create users table" => "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",

    "Insert default admin user (admin / password)" => "INSERT IGNORE INTO users (username, password_hash)
        VALUES ('admin', '" . password_hash('password', PASSWORD_BCRYPT) . "')",
];

foreach ($queries as $label => $sql) {
    try {
        $pdo->exec($sql);
        $results[] = ['label' => $label, 'status' => 'ok'];
    } catch (\PDOException $e) {
        $results[] = ['label' => $label, 'status' => 'error', 'msg' => $e->getMessage()];
        $hasError = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installer | Billing System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/custom.css" rel="stylesheet">
</head>
<body class="py-5">
<div class="container" style="max-width: 680px;">
    <div class="text-center mb-4">
        <i class="bi bi-lightning-charge-fill text-success" style="font-size:2.5rem;"></i>
        <h2 class="fw-bold mt-2">Billing System — Installer</h2>
    </div>
    <div class="card border-0 shadow-sm p-4">
        <h5 class="fw-semibold mb-3">Setup Results</h5>
        <ul class="list-group list-group-flush">
            <?php foreach ($results as $r): ?>
            <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center">
                <span><?= htmlspecialchars($r['label']) ?>
                    <?php if (isset($r['msg'])): ?>
                        <br><small class="text-danger"><?= htmlspecialchars($r['msg']) ?></small>
                    <?php endif; ?>
                </span>
                <?php if ($r['status'] === 'ok'): ?>
                    <span class="badge bg-success"><i class="bi bi-check-lg"></i> OK</span>
                <?php else: ?>
                    <span class="badge bg-danger"><i class="bi bi-x-lg"></i> Failed</span>
                <?php endif; ?>
            </li>
            <?php endforeach; ?>
        </ul>

        <div class="mt-4">
            <?php if (!$hasError): ?>
                <div class="alert alert-success mb-3">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <strong>All tables created successfully!</strong><br>
                    Default credentials: <code>admin</code> / <code>password</code>
                </div>
                <a href="login.php" class="btn btn-success w-100 fw-medium">
                    <i class="bi bi-arrow-right-circle"></i> Go to Login
                </a>
            <?php else: ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Some steps failed. Make sure XAMPP MySQL is running and the <code>billing_system</code> database exists.
                </div>
            <?php endif; ?>
        </div>
    </div>
    <p class="text-muted text-center mt-3 small">
        <i class="bi bi-shield-exclamation"></i>
        <strong>Delete <code>install.php</code> from your project after setup.</strong>
    </p>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
