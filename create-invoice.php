<?php
require_once 'config/database.php';

// Fetch clients and products for dropdowns
$clients = $pdo->query("SELECT id, company_name FROM clients ORDER BY company_name")->fetchAll();
$products = $pdo->query("SELECT id, name, unit_price, description FROM products_services ORDER BY name")->fetchAll();

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col">
        <h2 class="fw-bold mb-0">Create Invoice</h2>
        <p class="text-muted">Generate a new itemized bill.</p>
    </div>
</div>

<form action="modules/process-invoice.php" method="POST" id="invoiceForm">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-bottom py-3">
            <h5 class="mb-0 fw-semibold">Invoice Details</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label text-muted small">Client *</label>
                    <select name="client_id" class="form-select" required>
                        <option value="">Select a Client...</option>
                        <?php foreach($clients as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['company_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted small">Invoice Number *</label>
                    <input type="text" name="invoice_number" class="form-control" value="INV-<?= date('Ymd-His') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted small">Issue Date *</label>
                    <input type="date" name="issue_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted small">Due Date *</label>
                    <input type="date" name="due_date" class="form-control" value="<?= date('Y-m-d', strtotime('+14 days')) ?>" required>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold">Itemized Services</h5>
            <button type="button" class="btn btn-sm btn-outline-success" id="btnAddItem">
                <i class="bi bi-plus-lg"></i> Add Item Line
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0 align-middle" id="itemsTable">
                    <thead class="table-dark" style="background-color: var(--bg-primary);">
                        <tr>
                            <th class="ps-4">Product/Service</th>
                            <th>Description</th>
                            <th style="width: 120px;">Qty</th>
                            <th style="width: 150px;">Unit Price</th>
                            <th style="width: 150px;">Total</th>
                            <th style="width: 60px;"></th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        <!-- Initial row -->
                        <tr>
                            <td class="ps-4">
                                <select name="items[0][product_id]" class="form-select product-select" required>
                                    <option value="">Select Product...</option>
                                    <?php foreach($products as $p): ?>
                                        <option value="<?= $p['id'] ?>" data-price="<?= $p['unit_price'] ?>" data-desc="<?= htmlspecialchars($p['description']) ?>" data-name="<?= htmlspecialchars($p['name']) ?>"><?= htmlspecialchars($p['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" name="items[0][item_name]" class="item-name">
                            </td>
                            <td><input type="text" name="items[0][item_description]" class="form-control item-desc"></td>
                            <td><input type="number" name="items[0][quantity]" class="form-control item-qty" step="0.01" min="0.01" value="1" required></td>
                            <td>
                                <div class="input-group">
                                    <span class="input-group-text border-secondary bg-dark text-white">$</span>
                                    <input type="number" name="items[0][unit_price]" class="form-control item-price" step="0.01" min="0" required>
                                </div>
                            </td>
                            <td class="fw-semibold item-total">$0.00</td>
                            <td><button type="button" class="btn btn-sm btn-outline-danger btn-remove"><i class="bi bi-trash"></i></button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-transparent border-top">
            <div class="row justify-content-end">
                <div class="col-md-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal:</span>
                        <span class="fw-semibold" id="calcSubtotal">$0.00</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Tax Rate (%):</span>
                        <input type="number" name="tax_rate" id="taxRate" class="form-control form-control-sm w-50 text-end" step="0.01" min="0" value="0.00">
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Discount ($):</span>
                        <input type="number" name="discount_amount" id="discountAmount" class="form-control form-control-sm w-50 text-end" step="0.01" min="0" value="0.00">
                    </div>
                    <div class="d-flex justify-content-between pt-3 border-top" style="border-color: var(--border-color) !important;">
                        <span class="fw-bold fs-5">Total:</span>
                        <span class="fw-bold fs-5 text-success" id="calcTotal">$0.00</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <label class="form-label text-muted small">Notes / Terms</label>
            <textarea name="notes" class="form-control" rows="3" placeholder="Thank you for your business."></textarea>
        </div>
    </div>

    <div class="text-end mb-5">
        <a href="index.php" class="btn btn-outline-secondary me-2">Cancel</a>
        <button type="submit" class="btn btn-success fw-bold px-4 py-2">Save & Generate Invoice</button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let rowIndex = 1;
    
    // Add new row
    document.getElementById('btnAddItem').addEventListener('click', function() {
        const tbody = document.getElementById('itemsBody');
        const firstRow = tbody.querySelector('tr');
        const newRow = firstRow.cloneNode(true);
        
        // Reset values and update indices
        newRow.querySelectorAll('select, input').forEach(function(input) {
            // Update name attribute index
            if(input.name) {
                input.name = input.name.replace(/\[\d+\]/, '[' + rowIndex + ']');
            }
            // Reset value
            if(input.tagName === 'SELECT') input.value = '';
            else if(input.type === 'number' && input.classList.contains('item-qty')) input.value = '1';
            else if(input.type !== 'hidden') input.value = '';
        });
        
        newRow.querySelector('.item-total').textContent = '$0.00';
        
        tbody.appendChild(newRow);
        rowIndex++;
        calculateTotals();
    });

    // Remove row
    document.getElementById('itemsBody').addEventListener('click', function(e) {
        if(e.target.closest('.btn-remove')) {
            const tbody = document.getElementById('itemsBody');
            if(tbody.querySelectorAll('tr').length > 1) {
                e.target.closest('tr').remove();
                calculateTotals();
            } else {
                alert('You must have at least one item on the invoice.');
            }
        }
    });

    // Handle product selection
    document.getElementById('itemsBody').addEventListener('change', function(e) {
        if(e.target.classList.contains('product-select')) {
            const row = e.target.closest('tr');
            const selectedOption = e.target.options[e.target.selectedIndex];
            
            if(e.target.value) {
                row.querySelector('.item-price').value = selectedOption.dataset.price;
                row.querySelector('.item-desc').value = selectedOption.dataset.desc;
                row.querySelector('.item-name').value = selectedOption.dataset.name;
            } else {
                row.querySelector('.item-price').value = '';
                row.querySelector('.item-desc').value = '';
                row.querySelector('.item-name').value = '';
            }
            calculateTotals();
        }
    });

    // Handle input changes for calculation
    document.getElementById('invoiceForm').addEventListener('input', function(e) {
        if(e.target.classList.contains('item-qty') || 
           e.target.classList.contains('item-price') ||
           e.target.id === 'taxRate' || 
           e.target.id === 'discountAmount') {
            calculateTotals();
        }
    });

    function calculateTotals() {
        let subtotal = 0;
        
        document.querySelectorAll('#itemsBody tr').forEach(function(row) {
            const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            const lineTotal = qty * price;
            row.querySelector('.item-total').textContent = '$' + lineTotal.toFixed(2);
            subtotal += lineTotal;
        });

        const taxRate = parseFloat(document.getElementById('taxRate').value) || 0;
        const discount = parseFloat(document.getElementById('discountAmount').value) || 0;
        
        const taxAmount = subtotal * (taxRate / 100);
        let total = subtotal + taxAmount - discount;
        if(total < 0) total = 0;

        document.getElementById('calcSubtotal').textContent = '$' + subtotal.toFixed(2);
        document.getElementById('calcTotal').textContent = '$' + total.toFixed(2);
    }
});
</script>

<?php include 'includes/footer.php'; ?>
