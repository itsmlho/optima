<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<style>
    .card-stats {
        transition: all 0.3s ease;
        cursor: pointer;
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
    }
    
    .card-stats:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    
    .card-stats::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.1) 100%);
    }
    
    .filter-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: none;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    
    .table-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    
    .table-responsive {
        border-radius: 0;
    }
    
    .btn-action {
        padding: 0.4rem 0.8rem;
        font-size: 0.875rem;
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    
    .btn-action:hover {
        transform: scale(1.05);
    }
    
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem 0;
        margin: -1.5rem -1.5rem 2rem -1.5rem;
        border-radius: 0 0 20px 20px;
    }
    
    .status-badge {
        font-size: 0.75rem;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .po-badge {
        font-size: 0.75rem;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-weight: 600;
    }
    
    .table thead th {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: white;
        border: none;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        padding: 1rem 0.75rem;
    }
    
    .table tbody tr {
        transition: all 0.2s ease;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
        transform: scale(1.001);
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: #667eea;
        color: white !important;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 8px;
        padding: 0.5rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }
    
    .alert {
        border: none;
        border-radius: 10px;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .modal-content {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    }
    
    .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px 15px 0 0;
        padding: 1.5rem;
    }
    
    .form-control, .form-select {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 0.6rem 1rem;
        transition: all 0.2s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
</style>
<?= $this->endSection() ?>

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
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Items</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $inventory_stats['total_items'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cog fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Low Stock</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $inventory_stats['low_stock_items'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Out of Stock</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $inventory_stats['out_of_stock'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Value</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($inventory_stats['total_value'], 0, ',', '.') ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
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
                <table class="table table-bordered" id="sparepartsTable" width="100%" cellspacing="0">
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSparepartModalLabel">Add New Sparepart</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addSparepartForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="partNumber" class="form-label">Part Number</label>
                                <input type="text" class="form-control" id="partNumber" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="partName" class="form-label">Part Name</label>
                                <input type="text" class="form-control" id="partName" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-select" id="category" required>
                                    <option value="">Select Category</option>
                                    <option value="Engine Parts">Engine Parts</option>
                                    <option value="Brake Parts">Brake Parts</option>
                                    <option value="Hydraulic Parts">Hydraulic Parts</option>
                                    <option value="Tire & Wheel">Tire & Wheel</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="brand" class="form-label">Brand</label>
                                <input type="text" class="form-control" id="brand" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="stock" class="form-label">Initial Stock</label>
                                <input type="number" class="form-control" id="stock" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="minStock" class="form-label">Min Stock</label>
                                <input type="number" class="form-control" id="minStock" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="unitPrice" class="form-label">Unit Price</label>
                                <input type="number" class="form-control" id="unitPrice" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="location" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="supplier" class="form-label">Supplier</label>
                                <input type="text" class="form-control" id="supplier" required>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveSparepart()">Save Sparepart</button>
            </div>
        </div>
    </div>
</div>

<!-- Stock Adjustment Modal -->
<div class="modal fade" id="stockAdjustmentModal" tabindex="-1" aria-labelledby="stockAdjustmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="stockAdjustmentModalLabel">Adjust Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="stockAdjustmentForm">
                    <div class="mb-3">
                        <label for="adjustmentType" class="form-label">Adjustment Type</label>
                        <select class="form-select" id="adjustmentType" required>
                            <option value="">Select Type</option>
                            <option value="in">Stock In</option>
                            <option value="out">Stock Out</option>
                            <option value="adjustment">Adjustment</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="quantity" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason</label>
                        <textarea class="form-control" id="reason" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="confirmStockAdjustment()">Confirm Adjustment</button>
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