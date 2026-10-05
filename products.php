<?php
require_once 'config/database.php';

$stmt = $pdo->query("SELECT * FROM products_services ORDER BY name ASC");
$products = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="row mb-4 align-items-center">
    <div class="col">
        <h2 class="fw-bold mb-0">Products & Services</h2>
        <p class="text-muted">Manage your standard price list and service catalog.</p>
    </div>
    <div class="col-auto">
        <button type="button" class="btn btn-success fw-medium" data-bs-toggle="modal" data-bs-target="#addProductModal">
            <i class="bi bi-plus-lg"></i> Add Item
        </button>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">Item Name</th>
                        <th>Description</th>
                        <th>Unit Price</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($products)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No products or services found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($products as $p): ?>
                        <tr>
                            <td class="ps-4 fw-medium text-white"><?= htmlspecialchars($p['name']) ?></td>
                            <td class="text-muted"><?= htmlspecialchars($p['description']) ?></td>
                            <td class="fw-semibold">$<?= number_format($p['unit_price'], 2) ?></td>
                            <td class="text-end pe-4">
                                <a href="modules/delete-product.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this product?');"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="modules/save-product.php" method="POST">
          <div class="modal-header">
            <h5 class="modal-title fw-bold" id="addProductModalLabel">New Product or Service</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label text-muted small">Item Name *</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Web Development" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small">Description</label>
                    <textarea name="description" class="form-control" rows="2"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small">Unit Price *</label>
                    <div class="input-group">
                        <span class="input-group-text bg-dark border-secondary text-white">$</span>
                        <input type="number" step="0.01" min="0" name="unit_price" class="form-control" required>
                    </div>
                </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success fw-medium">Save Item</button>
          </div>
      </form>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
