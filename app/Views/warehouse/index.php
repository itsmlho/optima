<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-warehouse me-2"></i>Warehouse & Assets Dashboard
        </h1>
        <div class="d-sm-flex align-items-center">
            <div class="btn-group" role="group">
                <a href="<?= base_url('warehouse/spareparts') ?>" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-cogs me-1"></i>Spareparts
                </a>
                <a href="<?= base_url('warehouse/non-assets') ?>" class="btn btn-outline-info btn-sm">
                    <i class="fas fa-boxes me-1"></i>Non-Assets
                </a>
                <button class="btn btn-primary btn-sm" onclick="addNewItem()">
                    <i class="fas fa-plus me-1"></i>Add Item
                </button>
            </div>
        </div>
    </div>

    <!-- Warehouse Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Assets</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $warehouse_stats['total_assets'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-warehouse fa-2x text-gray-300"></i>
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
                                Available Units</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $warehouse_stats['available_units'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                Total Spareparts</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $warehouse_stats['total_spareparts'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cogs fa-2x text-gray-300"></i>
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
                                Low Stock Items</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $warehouse_stats['low_stock_items'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Overview -->
    <div class="row">
        <!-- Inventory Status Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Inventory Overview</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow">
                            <a class="dropdown-item" href="#" onclick="exportInventoryReport()">
                                <i class="fas fa-download me-2"></i>Export Report
                            </a>
                            <a class="dropdown-item" href="#" onclick="refreshInventory()">
                                <i class="fas fa-sync-alt me-2"></i>Refresh Data
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="inventoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Asset Status -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Asset Status</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="assetStatusChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Available
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-warning"></i> In Use
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-danger"></i> Maintenance
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Recent Transactions -->
    <div class="row">
        <!-- Quick Actions -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <button class="btn btn-outline-primary btn-block" onclick="location.href='<?= base_url('warehouse/spareparts') ?>'">
                                <i class="fas fa-cogs me-2"></i>Manage Spareparts
                            </button>
                        </div>
                        <div class="col-md-6 mb-3">
                            <button class="btn btn-outline-success btn-block" onclick="location.href='<?= base_url('warehouse/non-assets') ?>'">
                                <i class="fas fa-boxes me-2"></i>Non-Asset Items
                            </button>
                        </div>
                        <div class="col-md-6 mb-3">
                            <button class="btn btn-outline-warning btn-block" onclick="stockTakeModal()">
                                <i class="fas fa-clipboard-check me-2"></i>Stock Take
                            </button>
                        </div>
                        <div class="col-md-6 mb-3">
                            <button class="btn btn-outline-info btn-block" onclick="generateInventoryReport()">
                                <i class="fas fa-chart-bar me-2"></i>Inventory Report
                            </button>
                        </div>
                        <div class="col-md-6 mb-3">
                            <button class="btn btn-outline-secondary btn-block" onclick="requestPurchase()">
                                <i class="fas fa-shopping-cart me-2"></i>Purchase Request
                            </button>
                        </div>
                        <div class="col-md-6 mb-3">
                            <button class="btn btn-outline-dark btn-block" onclick="assetTransfer()">
                                <i class="fas fa-exchange-alt me-2"></i>Asset Transfer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history me-2"></i>Recent Transactions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?= date('d/m/Y') ?></td>
                                    <td><span class="badge badge-success">IN</span></td>
                                    <td>Engine Oil 10W-40</td>
                                    <td>+50</td>
                                    <td><i class="fas fa-check text-success"></i></td>
                                </tr>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime('-1 day')) ?></td>
                                    <td><span class="badge badge-warning">OUT</span></td>
                                    <td>Brake Pads</td>
                                    <td>-4</td>
                                    <td><i class="fas fa-check text-success"></i></td>
                                </tr>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime('-2 days')) ?></td>
                                    <td><span class="badge badge-info">TRANSFER</span></td>
                                    <td>Hydraulic Hose</td>
                                    <td>10</td>
                                    <td><i class="fas fa-clock text-warning"></i></td>
                                </tr>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime('-3 days')) ?></td>
                                    <td><span class="badge badge-success">IN</span></td>
                                    <td>Air Filter</td>
                                    <td>+25</td>
                                    <td><i class="fas fa-check text-success"></i></td>
                                </tr>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime('-4 days')) ?></td>
                                    <td><span class="badge badge-warning">OUT</span></td>
                                    <td>Tire 28x9-15</td>
                                    <td>-2</td>
                                    <td><i class="fas fa-check text-success"></i></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center">
                        <a href="<?= base_url('warehouse/transactions') ?>" class="btn btn-sm btn-outline-primary">
                            View All Transactions
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Alerts -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>Low Stock Alerts
            </h6>
            <a href="#" class="btn btn-sm btn-outline-danger" onclick="viewAllAlerts()">
                View All Alerts
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Item Code</th>
                            <th>Item Name</th>
                            <th>Category</th>
                            <th>Current Stock</th>
                            <th>Min Level</th>
                            <th>Location</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>SP-001</td>
                            <td>Engine Oil 10W-40</td>
                            <td>Lubricants</td>
                            <td><span class="text-danger font-weight-bold">5</span></td>
                            <td>20</td>
                            <td>Warehouse A-1</td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="reorderItem('SP-001')">
                                    <i class="fas fa-shopping-cart"></i> Reorder
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>SP-015</td>
                            <td>Brake Pads</td>
                            <td>Brake System</td>
                            <td><span class="text-danger font-weight-bold">2</span></td>
                            <td>10</td>
                            <td>Warehouse B-2</td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="reorderItem('SP-015')">
                                    <i class="fas fa-shopping-cart"></i> Reorder
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>SP-028</td>
                            <td>Air Filter</td>
                            <td>Engine Parts</td>
                            <td><span class="text-warning font-weight-bold">8</span></td>
                            <td>15</td>
                            <td>Warehouse A-3</td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="reorderItem('SP-028')">
                                    <i class="fas fa-shopping-cart"></i> Reorder
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add New Item Modal -->
<div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addItemModalLabel">
                    <i class="fas fa-plus me-2"></i>Add New Item
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addItemForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="itemType" class="form-label">Item Type *</label>
                                <select class="form-select" id="itemType" required>
                                    <option value="">Select Type</option>
                                    <option value="sparepart">Sparepart</option>
                                    <option value="non-asset">Non-Asset</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="itemCode" class="form-label">Item Code *</label>
                                <input type="text" class="form-control" id="itemCode" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="itemName" class="form-label">Item Name *</label>
                                <input type="text" class="form-control" id="itemName" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="category" class="form-label">Category *</label>
                                <select class="form-select" id="category" required>
                                    <option value="">Select Category</option>
                                    <option value="engine">Engine Parts</option>
                                    <option value="hydraulic">Hydraulic System</option>
                                    <option value="brake">Brake System</option>
                                    <option value="electrical">Electrical</option>
                                    <option value="lubricants">Lubricants</option>
                                    <option value="consumables">Consumables</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="quantity" class="form-label">Initial Quantity *</label>
                                <input type="number" class="form-control" id="quantity" required min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="minLevel" class="form-label">Minimum Level *</label>
                                <input type="number" class="form-control" id="minLevel" required min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="unitPrice" class="form-label">Unit Price</label>
                                <input type="number" class="form-control" id="unitPrice" step="0.01" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="location" class="form-label">Storage Location *</label>
                        <input type="text" class="form-control" id="location" required placeholder="e.g., Warehouse A-1">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveNewItem()">
                    <i class="fas fa-save me-2"></i>Save Item
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
$(document).ready(function() {
    // Initialize charts
    initializeInventoryChart();
    initializeAssetStatusChart();
});

// Inventory Overview Chart
function initializeInventoryChart() {
    const ctx = document.getElementById('inventoryChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Stock In',
                data: [65, 59, 80, 81, 56, 55],
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.1
            }, {
                label: 'Stock Out',
                data: [28, 48, 40, 19, 86, 27],
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

// Asset Status Chart
function initializeAssetStatusChart() {
    const ctx = document.getElementById('assetStatusChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Available', 'In Use', 'Maintenance'],
            datasets: [{
                data: [<?= $warehouse_stats['available_units'] ?? 65 ?>, 25, 10],
                backgroundColor: [
                    '#28a745',
                    '#ffc107',
                    '#dc3545'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

// Action Functions
function addNewItem() {
    $('#addItemModal').modal('show');
}

function saveNewItem() {
    // Validate form
    if (!$('#addItemForm')[0].checkValidity()) {
        $('#addItemForm')[0].reportValidity();
        return;
    }

    const formData = {
        type: $('#itemType').val(),
        code: $('#itemCode').val(),
        name: $('#itemName').val(),
        category: $('#category').val(),
        quantity: $('#quantity').val(),
        min_level: $('#minLevel').val(),
        unit_price: $('#unitPrice').val(),
        location: $('#location').val(),
        description: $('#description').val()
    };

    $.ajax({
        url: '<?= base_url('warehouse/add-item') ?>',
        method: 'POST',
        data: formData,
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                $('#addItemModal').modal('hide');
                OptimaPro.showNotification('Item added successfully!', 'success');
                setTimeout(function() {
                    location.reload();
                }, 1000);
            } else {
                OptimaPro.showNotification('Failed to add item: ' + response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            OptimaPro.showNotification('Error adding item: ' + error, 'error');
        }
    });
}

function reorderItem(itemCode) {
    OptimaPro.showConfirmDialog({
        title: 'Create Purchase Request',
        message: 'Create purchase request for ' + itemCode + '?'
    }).then(result => {
        if (result.isConfirmed) {
            OptimaPro.showNotification('Purchase request created for ' + itemCode, 'success');
        }
    });
}

function stockTakeModal() {
    OptimaPro.showNotification('Stock take feature coming soon!', 'info');
}

function generateInventoryReport() {
    OptimaPro.showNotification('Generating inventory report...', 'info');
    setTimeout(function() {
        window.open('<?= base_url('reports/inventory') ?>', '_blank');
    }, 1000);
}

function requestPurchase() {
    OptimaPro.showNotification('Purchase request feature coming soon!', 'info');
}

function assetTransfer() {
    OptimaPro.showNotification('Asset transfer feature coming soon!', 'info');
}

function exportInventoryReport() {
    OptimaPro.showNotification('Exporting inventory report...', 'info');
}

function refreshInventory() {
    OptimaPro.showNotification('Refreshing inventory data...', 'info');
    setTimeout(function() {
        location.reload();
    }, 1000);
}

function viewAllAlerts() {
    OptimaPro.showNotification('Viewing all alerts...', 'info');
}

// Legacy showNotification function replaced by OptimaPro.showNotification in base.php
</script>
<?= $this->endSection() ?> 