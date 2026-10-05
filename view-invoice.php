<?php
require_once __DIR__ . '/config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;


$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) {
    die("Invalid invoice ID.");
}

$stmt = $pdo->prepare("SELECT i.*, c.company_name, c.contact_name, c.email, c.phone, c.billing_address FROM invoices i JOIN clients c ON i.client_id = c.id WHERE i.id = ?");
$stmt->execute([$id]);
$invoice = $stmt->fetch();

if (!$invoice) {
    die("Invoice not found.");
}

$itemsStmt = $pdo->prepare("SELECT * FROM invoice_items WHERE invoice_id = ?");
$itemsStmt->execute([$id]);
$items = $itemsStmt->fetchAll();

$badgeClass = 'bg-secondary';
if ($invoice['status'] == 'Paid') $badgeClass = 'bg-success';
if ($invoice['status'] == 'Sent') $badgeClass = 'bg-warning text-dark';
if ($invoice['status'] == 'Overdue') $badgeClass = 'bg-danger';

include 'includes/header.php';
?>

<div class="row mb-4 no-print align-items-center">
    <div class="col">
        <a href="index.php" class="text-decoration-none text-muted"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
    </div>
    <div class="col-auto">
        <a href="modules/export-invoice.php?id=<?= $invoice['id'] ?>" class="btn btn-outline-primary me-2">
            <i class="bi bi-file-earmark-arrow-down"></i> Export TXT
        </a>
        <button class="btn btn-outline-secondary me-2" onclick="window.print()">
            <i class="bi bi-printer"></i> Print / PDF
        </button>
        <?php if ($invoice['status'] !== 'Paid'): ?>
            <form action="modules/mark-invoice-paid.php" method="POST" class="d-inline">
                <input type="hidden" name="id" value="<?= (int)$invoice['id'] ?>">
                <input type="hidden" name="csrf_token"
                    value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                <button type="submit" class="btn btn-success fw-medium">
                    <i class="bi bi-check2-circle"></i> Mark as Paid
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card invoice-container p-5 mb-5 border-0 shadow-sm">

            <!-- Invoice Header -->
            <div class="row mb-5">
                <div class="col-sm-6">
                    <h2 class="fw-bold mb-0">
                        <i class="bi bi-lightning-charge-fill text-success fs-3"></i>
                        <span class="text-white">Billing System</span>
                    </h2>
                    <p class="text-muted mt-2 mb-0">123 Business Road, Tech City<br>hello@billing-system.com<br>+1 (555) 123-4567</p>
                </div>
                <div class="col-sm-6 text-sm-end mt-4 mt-sm-0">
                    <h1 class="fw-bold text-uppercase text-muted opacity-50 mb-3">INVOICE</h1>
                    <h5 class="fw-bold">#<?= htmlspecialchars($invoice['invoice_number']) ?></h5>
                    <span class="badge <?= $badgeClass ?> no-print fs-6 mb-2"><?= $invoice['status'] ?></span>
                    <p class="mb-0"><span class="text-muted">Issue Date:</span> <?= date('F d, Y', strtotime($invoice['issue_date'])) ?></p>
                    <p class="mb-0"><span class="text-muted">Due Date:</span> <?= date('F d, Y', strtotime($invoice['due_date'])) ?></p>
                </div>
            </div>

            <hr class="border-secondary opacity-25 mb-5">

            <!-- Client Info -->
            <div class="row mb-5">
                <div class="col-sm-6">
                    <h6 class="text-muted text-uppercase fw-bold mb-3">Billed To</h6>
                    <h5 class="fw-bold mb-1"><?= htmlspecialchars($invoice['company_name']) ?></h5>
                    <?php if ($invoice['contact_name']): ?>
                        <p class="mb-1 text-muted"><?= htmlspecialchars($invoice['contact_name']) ?></p>
                    <?php endif; ?>
                    <p class="mb-1 text-muted"><?= nl2br(htmlspecialchars($invoice['billing_address'])) ?></p>
                    <p class="mb-1 text-muted"><?= htmlspecialchars($invoice['email']) ?></p>
                    <p class="mb-0 text-muted"><?= htmlspecialchars($invoice['phone']) ?></p>
                </div>
                <div class="col-sm-6 text-sm-end mt-4 mt-sm-0">
                    <!-- Additional metadata if needed -->
                </div>
            </div>

            <!-- Invoice Items -->
            <div class="table-responsive mb-4">
                <table class="table table-borderless">
                    <thead class="border-bottom border-secondary opacity-75">
                        <tr>
                            <th class="ps-0 text-muted text-uppercase">Description</th>
                            <th class="text-end text-muted text-uppercase" style="width: 100px;">Qty</th>
                            <th class="text-end text-muted text-uppercase" style="width: 150px;">Unit Price</th>
                            <th class="text-end pe-0 text-muted text-uppercase" style="width: 150px;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr class="border-bottom border-secondary border-opacity-10">
                                <td class="ps-0 py-3">
                                    <h6 class="mb-1 fw-semibold text-white"><?= htmlspecialchars($item['item_name']) ?></h6>
                                    <span class="text-muted small"><?= nl2br(htmlspecialchars($item['item_description'])) ?></span>
                                </td>
                                <td class="text-end py-3"><?= number_format($item['quantity'], 2) ?></td>
                                <td class="text-end py-3">$<?= number_format($item['unit_price'], 2) ?></td>
                                <td class="text-end pe-0 py-3 fw-medium">$<?= number_format($item['line_total'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Totals -->
            <div class="row justify-content-end mb-5">
                <div class="col-sm-6 col-md-5 col-lg-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal</span>
                        <span class="fw-medium">$<?= number_format($invoice['subtotal'], 2) ?></span>
                    </div>
                    <?php if ($invoice['tax_rate'] > 0): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Tax (<?= floatval($invoice['tax_rate']) ?>%)</span>
                            <span class="fw-medium">$<?= number_format($invoice['subtotal'] * ($invoice['tax_rate'] / 100), 2) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ($invoice['discount_amount'] > 0): ?>
                        <div class="d-flex justify-content-between mb-3 text-success">
                            <span>Discount</span>
                            <span>-$<?= number_format($invoice['discount_amount'], 2) ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="d-flex justify-content-between pt-3 border-top border-secondary border-opacity-50">
                        <h4 class="fw-bold mb-0">Total</h4>
                        <h4 class="fw-bold text-success mb-0">$<?= number_format($invoice['total_amount'], 2) ?></h4>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <?php if (!empty($invoice['notes'])): ?>
                <div class="mt-4 pt-4 border-top border-secondary border-opacity-10">
                    <h6 class="text-muted text-uppercase fw-bold mb-2">Notes & Terms</h6>
                    <p class="text-muted small mb-0"><?= nl2br(htmlspecialchars($invoice['notes'])) ?></p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>