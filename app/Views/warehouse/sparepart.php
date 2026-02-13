<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-cog text-primary me-2"></i>
            Sparepart Inventory
        </h1>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="exportInventory()">
                <i class="fas fa-download me-2"></i>Export
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSparepartModal">
                <i class="fas fa-plus me-2"></i>Add Sparepart
            </button>
        </div>
    </div>

    <!-- Inventory Stats -->
<?php if (isset($inventory_stats) && is_array($inventory_stats)): ?>
    <div class="row mt-3 mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-primary-soft">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-gear stat-icon text-primary"></i>
                    </div>
                    <div>
                        <div class="stat-value"><?= $inventory_stats['total_items'] ?></div>
                        <div class="text-muted">Total Items</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-warning-soft">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-exclamation-triangle stat-icon text-warning"></i>
                    </div>
                    <div>
                        <div class="stat-value"><?= $inventory_stats['low_stock_items'] ?></div>
                        <div class="text-muted">Low Stock</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-danger-soft">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-x-circle stat-icon text-danger"></i>
                    </div>
                    <div>
                        <div class="stat-value"><?= $inventory_stats['out_of_stock'] ?></div>
                        <div class="text-muted">Out of Stock</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-success-soft">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-currency-dollar stat-icon text-success"></i>
                    </div>
                    <div>
                        <div class="stat-value">Rp <?= number_format($inventory_stats['total_value'], 0, ',', '.') ?></div>
                        <div class="text-muted">Total Value</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Filter Section -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="filterCategory" class="form-label">Category</label>
                        <select class="form-select" id="filterCategory">
                            <option value="">All Categories</option>
                            <option value="Engine Parts">Engine Parts</option>
                            <option value="Brake Parts">Brake Parts</option>
                            <option value="Hydraulic Parts">Hydraulic Parts</option>
                            <option value="Tire & Wheel">Tire & Wheel</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="filterBrand" class="form-label">Brand</label>
                        <select class="form-select" id="filterBrand">
                            <option value="">All Brands</option>
                            <option value="Toyota">Toyota</option>
                            <option value="Universal">Universal</option>
                            <option value="Shell">Shell</option>
                            <option value="Bridgestone">Bridgestone</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="filterLocation" class="form-label">Location</label>
                        <select class="form-select" id="filterLocation">
                            <option value="">All Locations</option>
                            <?php if (isset($locations) && is_array($locations)): ?>
                                <?php foreach ($locations as $location): ?>
                                    <option value="<?= $location ?>"><?= $location ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="filterStock" class="form-label">Stock Status</label>
                        <select class="form-select" id="filterStock">
                            <option value="">All Stock</option>
                            <option value="in_stock">In Stock</option>
                            <option value="low_stock">Low Stock</option>
                            <option value="out_of_stock">Out of Stock</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <button class="btn btn-primary" onclick="applyFilters()">
                        <i class="fas fa-filter me-2"></i>Apply Filters
                    </button>
                    <button class="btn btn-outline-secondary" onclick="clearFilters()">
                        <i class="fas fa-times me-2"></i>Clear Filters
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Spareparts Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Sparepart Inventory</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="sparepartsTable">
                    <thead>
                        <tr>
                            <th>Part Number</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Brand</th>
                            <th>Stock</th>
                            <th>Min Stock</th>
                            <th>Location</th>
                            <th>Unit Price</th>
                            <th>Supplier</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($spareparts) && is_array($spareparts)): ?>
                            <?php foreach ($spareparts as $part): ?>
                                <tr>
                                    <td><strong><?= $part['part_number'] ?></strong></td>
                                    <td><?= $part['name'] ?></td>
                                    <td><?= $part['category'] ?></td>
                                    <td><?= $part['brand'] ?></td>
                                    <td>
                                        <span class="badge badge-<?= $part['stock'] <= 0 ? 'danger' : ($part['stock'] <= $part['min_stock'] ? 'warning' : 'success') ?>">
                                            <?= $part['stock'] ?>
                                        </span>
                                    </td>
                                    <td><?= $part['min_stock'] ?></td>
                                    <td><?= $part['location'] ?></td>
                                    <td>Rp <?= number_format($part['unit_price'], 0, ',', '.') ?></td>
                                    <td><?= $part['supplier'] ?></td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-h"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="#" onclick="viewSparepart(<?= $part['id'] ?>)"><i class="fas fa-eye me-2"></i>Lihat Detail</a></li>
                                                <li><a class="dropdown-item" href="#" onclick="editSparepart(<?= $part['id'] ?>)"><i class="fas fa-edit me-2"></i>Edit</a></li>
                                                <li><a class="dropdown-item" href="#" onclick="adjustStock(<?= $part['id'] ?>)"><i class="fas fa-plus-minus me-2"></i>Adjust Stock</a></li>
                                                <li><a class="dropdown-item" href="#" onclick="orderSparepart(<?= $part['id'] ?>)"><i class="fas fa-shopping-cart me-2"></i>Order</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="text-center">No spareparts found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Sparepart Modal -->
<div class="modal fade" id="addSparepartModal" tabindex="-1" aria-labelledby="addSparepartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSparepartModalLabel"><?= lang('Warehouse.add_sparepart') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addSparepartForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="partNumber" class="form-label"><?= lang('Warehouse.part_number') ?></label>
                                <input type="text" class="form-control" id="partNumber" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="partName" class="form-label"><?= lang('Warehouse.part_name') ?></label>
                                <input type="text" class="form-control" id="partName" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category" class="form-label"><?= lang('Warehouse.category') ?></label>
                                <select class="form-select" id="category" required>
                                    <option value=""><?= lang('App.select') ?> <?= lang('Warehouse.category') ?></option>
                                    <option value="Engine Parts">Engine Parts</option>
                                    <option value="Brake Parts">Brake Parts</option>
                                    <option value="Hydraulic Parts">Hydraulic Parts</option>
                                    <option value="Tire & Wheel">Tire & Wheel</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="brand" class="form-label"><?= lang('Warehouse.brand') ?></label>
                                <input type="text" class="form-control" id="brand" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="stock" class="form-label"><?= lang('Warehouse.initial_stock') ?></label>
                                <input type="number" class="form-control" id="stock" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="minStock" class="form-label"><?= lang('Warehouse.min_stock') ?></label>
                                <input type="number" class="form-control" id="minStock" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="unitPrice" class="form-label"><?= lang('Warehouse.unit_price') ?></label>
                                <input type="number" class="form-control" id="unitPrice" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="location" class="form-label"><?= lang('Warehouse.location') ?></label>
                                <input type="text" class="form-control" id="location" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="supplier" class="form-label"><?= lang('Warehouse.supplier') ?></label>
                                <input type="text" class="form-control" id="supplier" required>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= lang('App.cancel') ?></button>
                <button type="button" class="btn btn-primary" onclick="saveSparepart()"><?= lang('App.save') ?> <?= lang('Warehouse.sparepart') ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Stock Adjustment Modal -->
<div class="modal fade" id="stockAdjustmentModal" tabindex="-1" aria-labelledby="stockAdjustmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="stockAdjustmentModalLabel"><?= lang('Warehouse.adjust_stock') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="stockAdjustmentForm">
                    <div class="mb-3">
                        <label for="adjustmentType" class="form-label"><?= lang('Warehouse.adjustment_type') ?></label>
                        <select class="form-select" id="adjustmentType" required>
                            <option value=""><?= lang('App.select') ?> <?= lang('App.type') ?></option>
                            <option value="in"><?= lang('Warehouse.stock_in') ?></option>
                            <option value="out"><?= lang('Warehouse.stock_out') ?></option>
                            <option value="adjustment"><?= lang('Warehouse.adjustment') ?></option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label"><?= lang('Warehouse.quantity') ?></label>
                        <input type="number" class="form-control" id="quantity" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="reason" class="form-label"><?= lang('Warehouse.reason') ?></label>
                        <textarea class="form-control" id="reason" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= lang('App.cancel') ?></button>
                <button type="button" class="btn btn-primary" onclick="confirmStockAdjustment()"><?= lang('App.confirm') ?> <?= lang('Warehouse.adjustment') ?></button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#sparepartsTable').DataTable({
        responsive: true,
        pageLength: 10,
        order: [[0, 'asc']]
    });
});

function applyFilters() {
    location.reload();
}

function clearFilters() {
    $('#filterCategory, #filterBrand, #filterLocation, #filterStock').val('');
    applyFilters();
}

function viewSparepart(id) {
    showNotification('View Sparepart ID: ' + id, 'info');
}

function editSparepart(id) {
    showNotification('Edit Sparepart ID: ' + id, 'info');
}

function adjustStock(id) {
    $('#stockAdjustmentModal').modal('show');
    $('#stockAdjustmentForm').data('sparepart-id', id);
}

function orderSparepart(id) {
    showNotification('Order Sparepart ID: ' + id, 'info');
}

function exportInventory() {
    showNotification('Export inventory functionality', 'info');
}

function saveSparepart() {
    showNotification('Sparepart saved successfully!', 'success');
    $('#addSparepartModal').modal('hide');
}

function confirmStockAdjustment() {
    const sparepartId = $('#stockAdjustmentForm').data('sparepart-id');
    showNotification('Stock adjusted for Sparepart ID: ' + sparepartId, 'success');
    $('#stockAdjustmentModal').modal('hide');
}
</script>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
$(document).ready(function() {
    // Initialize DataTable for sorting and search functionality
    $('#sparepartsTable').DataTable({
        processing: true,
        pageLength: 25,
        order: [[1, 'asc']], // Sort by sparepart name
        columnDefs: [
            { orderable: false, targets: [-1] } // Disable sorting on last column (actions)
        ]
    });
});
</script>
<?= $this->endSection() ?> 