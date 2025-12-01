<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<div class="container-fluid" aria-label="Dashboard Warehouse & Assets" role="region">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800" id="pageTitle">
            <i class="fas fa-warehouse me-2" aria-hidden="true"></i>Dashboard Warehouse & Assets
        </h1>
        <div class="d-sm-flex align-items-center">
            <div class="me-3">
                <small class="text-muted">Terakhir diperbarui: </small>
                <span class="fw-bold" aria-live="polite"><?= date('d M Y, H:i') ?></span>
            </div>
            <div class="btn-group" role="group" aria-label="Aksi Warehouse">
                <button class="btn btn-outline-primary btn-sm" onclick="exportWarehouseDashboard()" aria-label="Export Dashboard"><i class="fas fa-download me-1" aria-hidden="true"></i>Export</button>
                <button class="btn btn-outline-success btn-sm" onclick="generateInventoryReport()" aria-label="Generate Report"><i class="fas fa-file-alt me-1" aria-hidden="true"></i>Report</button>
                <button class="btn btn-outline-warning btn-sm" onclick="stockAuditModal()" aria-label="Audit Stok"><i class="fas fa-clipboard-check me-1" aria-hidden="true"></i>Audit</button>
                <button class="btn btn-primary btn-sm" onclick="location.reload()" aria-label="Refresh Dashboard"><i class="fas fa-sync-alt me-1" aria-hidden="true"></i>Refresh</button>
            </div>
        </div>
    </div>

    <!-- Inventory Overview KPIs - Professional Standard -->
    <div class="row g-4 mb-4" aria-label="Statistik Warehouse" role="list">
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats bg-primary text-white h-100" role="listitem">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h2 class="fw-bold mb-1" data-count="<?= $warehouse_stats['total_assets'] ?? 0 ?>">0</h2>
                        <h6 class="card-title text-uppercase small mb-0">TOTAL ASSETS</h6>
                        <div class="small mt-1 opacity-75"><i class="fas fa-arrow-up" aria-hidden="true"></i> +3 unit baru</div>
                    </div>
                    <div class="ms-3" aria-hidden="true">
                        <i class="fas fa-boxes fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-stats bg-success text-white h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h2 class="fw-bold mb-1"><?= $warehouse_stats['available_units'] ?? 0 ?></h2>
                        <h6 class="card-title text-uppercase small mb-0">UNIT TERSEDIA</h6>
                        <div class="small mt-1 opacity-75">
                            <i class="fas fa-check-circle"></i> <?= round(($warehouse_stats['available_units'] ?? 0) / max(($warehouse_stats['total_assets'] ?? 1), 1) * 100, 1) ?>% availability
                        </div>
                    </div>
                    <div class="ms-3">
                        <i class="fas fa-check-circle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-stats bg-warning text-white h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h2 class="fw-bold mb-1"><?= $warehouse_stats['total_spareparts'] ?? 0 ?></h2>
                        <h6 class="card-title text-uppercase small mb-0">SPAREPART ITEMS</h6>
                        <div class="small mt-1 opacity-75">
                            <i class="fas fa-exclamation-triangle"></i> <?= $warehouse_stats['low_stock_items'] ?? 0 ?> low stock
                        </div>
                    </div>
                    <div class="ms-3">
                        <i class="fas fa-cog fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-stats bg-info text-white h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h2 class="fw-bold mb-1">Rp 45.2B</h2>
                        <h6 class="card-title text-uppercase small mb-0">ASSET VALUE</h6>
                        <div class="small mt-1 opacity-75">
                            <i class="fas fa-chart-line"></i> +2.3% dari bulan lalu
                        </div>
                    </div>
                    <div class="ms-3">
                        <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Analytics -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-area me-2"></i>Asset Utilization Trend
                    </h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow">
                            <a class="dropdown-item" href="#" onclick="exportChart('utilizationChart')">
                                <i class="fas fa-download me-2"></i>Export Chart
                            </a>
                            <a class="dropdown-item" href="#" onclick="viewUtilizationDetails()">
                                <i class="fas fa-eye me-2"></i>View Details
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="utilizationChart" style="height: 300px;"></canvas>
                    </div>
                    <div class="mt-3 text-center">
                        <small class="text-muted">Rata-rata utilization: <strong>82.3%</strong> | Target: <strong>85%</strong></small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>Asset Categories
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="assetCategoryChart" style="height: 200px;"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <div class="row">
                            <div class="col-6">
                                <span class="mr-2">
                                    <i class="fas fa-circle text-primary"></i> Forklift
                                </span>
                            </div>
                            <div class="col-6">
                                <span class="mr-2">
                                    <i class="fas fa-circle text-success"></i> Crane
                                </span>
                            </div>
                            <div class="col-6">
                                <span class="mr-2">
                                    <i class="fas fa-circle text-warning"></i> Excavator
                                </span>
                            </div>
                            <div class="col-6">
                                <span class="mr-2">
                                    <i class="fas fa-circle text-info"></i> Others
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Management -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar me-2"></i>Sparepart Inventory Levels
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="sparepartChart" style="height: 250px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-line me-2"></i>Asset Depreciation
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="depreciationChart" style="height: 250px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Information -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list me-2"></i>Recent Inventory Movements
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="inventoryMovementsTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Item</th>
                                    <th>Movement Type</th>
                                    <th>Quantity</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>2024-01-15</td>
                                    <td>FL-001 - Toyota Forklift</td>
                                    <td>Outbound</td>
                                    <td>1</td>
                                    <td>Site Jakarta</td>
                                    <td><span class="badge badge-success">Deployed</span></td>
                                </tr>
                                <tr>
                                    <td>2024-01-14</td>
                                    <td>Engine Oil - 5L</td>
                                    <td>Inbound</td>
                                    <td>50</td>
                                    <td>Warehouse A</td>
                                    <td><span class="badge badge-info">Received</span></td>
                                </tr>
                                <tr>
                                    <td>2024-01-13</td>
                                    <td>FL-015 - Mitsubishi Forklift</td>
                                    <td>Inbound</td>
                                    <td>1</td>
                                    <td>Workshop</td>
                                    <td><span class="badge badge-warning">Maintenance</span></td>
                                </tr>
                                <tr>
                                    <td>2024-01-12</td>
                                    <td>Hydraulic Filter</td>
                                    <td>Outbound</td>
                                    <td>10</td>
                                    <td>Workshop</td>
                                    <td><span class="badge badge-primary">Used</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Stock Alerts -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>Stock Alerts
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">Engine Oil 5L</div>
                                <small class="text-danger">Stock: 5 units (Min: 20)</small>
                            </div>
                            <span class="badge bg-danger rounded-pill">Critical</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">Hydraulic Filter</div>
                                <small class="text-warning">Stock: 15 units (Min: 25)</small>
                            </div>
                            <span class="badge bg-warning rounded-pill">Low</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">Brake Pads</div>
                                <small class="text-info">Stock: 30 units (Min: 15)</small>
                            </div>
                            <span class="badge bg-info rounded-pill">Normal</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Asset Performance -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-trophy me-2"></i>Top Performing Assets
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold">FL-001</div>
                                <small class="text-muted">Toyota Forklift</small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-success">98.5%</div>
                                <small class="text-muted">uptime</small>
                            </div>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold">FL-007</div>
                                <small class="text-muted">Mitsubishi Forklift</small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-success">96.2%</div>
                                <small class="text-muted">uptime</small>
                            </div>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold">FL-012</div>
                                <small class="text-muted">Komatsu Forklift</small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-primary">94.8%</div>
                                <small class="text-muted">uptime</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" onclick="addAssetModal()">
                            <i class="fas fa-plus me-2"></i>Add Asset
                        </button>
                        <button class="btn btn-success" onclick="stockReceiptModal()">
                            <i class="fas fa-arrow-down me-2"></i>Stock Receipt
                        </button>
                        <button class="btn btn-info" onclick="transferAssetModal()">
                            <i class="fas fa-exchange-alt me-2"></i>Transfer Asset
                        </button>
                        <button class="btn btn-warning" onclick="stockAuditModal()">
                            <i class="fas fa-clipboard-check me-2"></i>Stock Audit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#inventoryMovementsTable').DataTable({
        responsive: true,
        pageLength: 5,
        order: [[0, 'desc']],
        dom: 'rtip'
    });

    // Initialize Charts
    initializeWarehouseCharts();
});

function initializeWarehouseCharts() {
    // Asset Utilization Chart
    const utilizationCtx = document.getElementById('utilizationChart').getContext('2d');
    new Chart(utilizationCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Utilization Rate (%)',
                data: [78, 82, 79, 85, 88, 84, 87, 91, 83, 86, 89, 82],
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }, {
                label: 'Target',
                data: [85, 85, 85, 85, 85, 85, 85, 85, 85, 85, 85, 85],
                borderColor: '#e74a3b',
                backgroundColor: 'transparent',
                borderWidth: 2,
                borderDash: [5, 5],
                fill: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            }
        }
    });

    // Asset Category Chart
    const assetCtx = document.getElementById('assetCategoryChart').getContext('2d');
    new Chart(assetCtx, {
        type: 'doughnut',
        data: {
            labels: ['Forklift', 'Crane', 'Excavator', 'Others'],
            datasets: [{
                data: [60, 20, 15, 5],
                backgroundColor: ['#4e73df', '#1cc88a', '#f6c23e', '#36b9cc'],
                borderWidth: 0
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

    // Sparepart Inventory Chart
    const sparepartCtx = document.getElementById('sparepartChart').getContext('2d');
    new Chart(sparepartCtx, {
        type: 'bar',
        data: {
            labels: ['Engine Oil', 'Hydraulic Filter', 'Brake Pads', 'Tires', 'Batteries', 'Spark Plugs'],
            datasets: [{
                label: 'Current Stock',
                data: [5, 15, 30, 25, 18, 22],
                backgroundColor: '#4e73df',
                borderColor: '#4e73df',
                borderWidth: 1
            }, {
                label: 'Minimum Stock',
                data: [20, 25, 15, 20, 12, 18],
                backgroundColor: '#e74a3b',
                borderColor: '#e74a3b',
                borderWidth: 1
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

    // Asset Depreciation Chart
    const depreciationCtx = document.getElementById('depreciationChart').getContext('2d');
    new Chart(depreciationCtx, {
        type: 'line',
        data: {
            labels: ['2019', '2020', '2021', '2022', '2023', '2024'],
            datasets: [{
                label: 'Asset Value (Billions)',
                data: [50.5, 48.2, 46.8, 45.1, 44.3, 45.2],
                borderColor: '#1cc88a',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value + 'B';
                        }
                    }
                }
            }
        }
    });
}

function exportWarehouseDashboard() {
    alert('Export warehouse dashboard functionality will be implemented');
}

function generateInventoryReport() {
    alert('Generate inventory report functionality will be implemented');
}

function exportChart(chartId) {
    alert('Export chart functionality will be implemented');
}

function viewUtilizationDetails() {
    alert('View utilization details functionality will be implemented');
}

function addAssetModal() {
    alert('Add asset modal will be implemented');
}

function stockReceiptModal() {
    alert('Stock receipt modal will be implemented');
}

function transferAssetModal() {
    alert('Transfer asset modal will be implemented');
}

function stockAuditModal() {
    alert('Stock audit modal will be implemented');
}
</script>

<?= $this->endSection() ?> 