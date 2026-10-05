<?php
require_once 'config/database.php';

$stmt = $pdo->query("SELECT * FROM clients ORDER BY company_name ASC");
$clients = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="row mb-4 align-items-center">
    <div class="col">
        <h2 class="fw-bold mb-0">Clients</h2>
        <p class="text-muted">Manage your client list and contact details.</p>
    </div>
    <div class="col-auto">
        <button type="button" class="btn btn-success fw-medium" data-bs-toggle="modal" data-bs-target="#addClientModal">
            <i class="bi bi-person-plus"></i> Add Client
        </button>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">Company Name</th>
                        <th>Contact Person</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Added On</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($clients)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No clients added yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($clients as $c): ?>
                        <tr>
                            <td class="ps-4 fw-medium text-white"><?= htmlspecialchars($c['company_name']) ?></td>
                            <td><?= htmlspecialchars($c['contact_name']) ?></td>
                            <td><a href="mailto:<?= htmlspecialchars($c['email']) ?>" class="text-decoration-none"><?= htmlspecialchars($c['email']) ?></a></td>
                            <td><?= htmlspecialchars($c['phone']) ?></td>
                            <td><?= date('M d, Y', strtotime($c['created_at'])) ?></td>
                            <td class="text-end pe-4">
                                <a href="modules/delete-client.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this client?');"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Client Modal -->
<div class="modal fade" id="addClientModal" tabindex="-1" aria-labelledby="addClientModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="modules/save-client.php" method="POST">
          <div class="modal-header">
            <h5 class="modal-title fw-bold" id="addClientModalLabel">New Client</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label text-muted small">Company Name *</label>
                    <input type="text" name="company_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small">Contact Name</label>
                    <input type="text" name="contact_name" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small">Email Address *</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small">Phone Number</label>
                    <input type="text" name="phone" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small">Billing Address *</label>
                    <textarea name="billing_address" class="form-control" rows="3" required></textarea>
                </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success fw-medium">Save Client</button>
          </div>
      </form>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
