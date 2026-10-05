<?php
require_once 'config/database.php';

// Fetch metrics
$metrics = [
    'earnings' => 0.00,
    'pending' => 0.00,
    'overdue' => 0.00,
    'clients' => 0
];

// Earnings
$stmt = $pdo->query("SELECT SUM(total_amount) AS total FROM invoices WHERE status = 'Paid'");
$metrics['earnings'] = $stmt->fetch()['total'] ?? 0.00;

// Pending
$stmt = $pdo->query("SELECT SUM(total_amount) AS total FROM invoices WHERE status = 'Sent'");
$metrics['pending'] = $stmt->fetch()['total'] ?? 0.00;

// Overdue
$stmt = $pdo->query("SELECT SUM(total_amount) AS total FROM invoices WHERE status = 'Overdue'");
$metrics['overdue'] = $stmt->fetch()['total'] ?? 0.00;

// Clients
$stmt = $pdo->query("SELECT COUNT(id) AS total FROM clients");
$metrics['clients'] = $stmt->fetch()['total'] ?? 0;

// Recent Invoices
$stmt = $pdo->query("SELECT i.*, c.company_name FROM invoices i JOIN clients c ON i.client_id = c.id ORDER BY i.id DESC LIMIT 5");
$recent_invoices = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold mb-0">Dashboard</h2>
        <p class="text-muted">Welcome back. Here is your financial overview.</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted text-uppercase fw-semibold mb-2">Total Earnings</h6>
                <h3 class="fw-bold text-success mb-0">$<?= number_format($metrics['earnings'], 2) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted text-uppercase fw-semibold mb-2">Pending Receivables</h6>
                <h3 class="fw-bold text-warning mb-0">$<?= number_format($metrics['pending'], 2) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted text-uppercase fw-semibold mb-2">Overdue Penalties</h6>
                <h3 class="fw-bold text-danger mb-0">$<?= number_format($metrics['overdue'], 2) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted text-uppercase fw-semibold mb-2">Client Base</h6>
                <h3 class="fw-bold text-primary mb-0"><?= number_format($metrics['clients']) ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 fw-semibold">Recent Invoices</h5>
                <a href="create-invoice.php" class="btn btn-sm btn-outline-success">Create New</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-dark" style="background-color: var(--bg-primary);">
                            <tr>
                                <th class="ps-4">Invoice #</th>
                                <th>Client</th>
                                <th>Issue Date</th>
                                <th>Due Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recent_invoices)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No invoices found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recent_invoices as $inv):
                                    $badgeClass = 'bg-secondary';
                                    if ($inv['status'] == 'Paid') $badgeClass = 'bg-success';
                                    if ($inv['status'] == 'Sent') $badgeClass = 'bg-warning text-dark';
                                    if ($inv['status'] == 'Overdue') $badgeClass = 'bg-danger';
                                ?>
                                    <tr>
                                        <td class="ps-4 fw-medium"><?= htmlspecialchars($inv['invoice_number']) ?></td>
                                        <td><?= htmlspecialchars($inv['company_name']) ?></td>
                                        <td><?= date('M d, Y', strtotime($inv['issue_date'])) ?></td>
                                        <td><?= date('M d, Y', strtotime($inv['due_date'])) ?></td>
                                        <td class="fw-semibold">$<?= number_format($inv['total_amount'], 2) ?></td>
                                        <td><span class="badge <?= $badgeClass ?>"><?= $inv['status'] ?></span></td>
                                        <td class="text-end pe-4">
                                            <a href="view-invoice.php?id=<?= $inv['id'] ?>" class="btn btn-sm btn-outline-secondary">View</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>