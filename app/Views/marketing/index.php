<?= $this->extend('layouts/base') ?>

<?php
/**
 * Marketing Division Dashboard Module
 *
 * BADGE SYSTEM: Menggunakan Optima Badge Standards (optima-pro.css)
 * Quick Reference: Approved → badge-soft-green, PENDING → badge-soft-yellow, other → badge-soft-gray
 * See optima-pro.css line ~2030 for complete badge standards
 */
?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header inside card -->
    <div class="card table-card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="card-title mb-0">
                    <i class="fas fa-bullhorn me-2 text-primary"></i>Marketing Division Dashboard
                </h5>
                <p class="text-muted small mb-0">
                    Overview quotations, contracts, and marketing performance
                    <span class="ms-2 text-info"><i class="bi bi-info-circle me-1"></i><small>Tip: Use Quick Access or links below to open modules</small></span>
                </p>
            </div>
            <div class="d-flex gap-2">
                <?= ui_button('refresh', 'Refresh', [
                    'color' => 'outline-info',
                    'size' => 'sm',
                    'onclick' => 'refreshMarketingData()'
                ]) ?>
                <?= ui_button('view', 'Quotations', [
                    'href' => base_url('marketing/quotations'),
                    'color' => 'outline-primary',
                    'size' => 'sm',
                    'icon' => 'fas fa-file-contract'
                ]) ?>
                <?= ui_button('view', 'Contracts', [
                    'href' => base_url('marketing/kontrak'),
                    'size' => 'sm',
                    'icon' => 'fas fa-handshake'
                ]) ?>
            </div>
        </div>
    </div>

    <!-- Marketing Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-primary border-4 shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row g-0 align-items-center">
                        <div class="col me-2">
                            <div class="small fw-bold text-primary text-uppercase mb-1">
                                Total Quotations</div>
                            <div class="h5 mb-0 fw-bold text-dark">
                                <?= $marketing_stats['total_quotations'] ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-contract fa-2x text-muted opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-warning border-4 shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row g-0 align-items-center">
                        <div class="col me-2">
                            <div class="small fw-bold text-warning text-uppercase mb-1">
                                Pending Quotations</div>
                            <div class="h5 mb-0 fw-bold text-dark">
                                <?= $marketing_stats['pending_quotations'] ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-muted opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-success border-4 shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row g-0 align-items-center">
                        <div class="col me-2">
                            <div class="small fw-bold text-success text-uppercase mb-1">
                                Active Contracts</div>
                            <div class="h5 mb-0 fw-bold text-dark">
                                <?= $marketing_stats['active_contracts'] ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-handshake fa-2x text-muted opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-info border-4 shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row g-0 align-items-center">
                        <div class="col me-2">
                            <div class="small fw-bold text-info text-uppercase mb-1">
                                Monthly Revenue</div>
                            <div class="h5 mb-0 fw-bold text-dark">
                                Rp <?= number_format($marketing_stats['monthly_revenue'], 0, ',', '.') ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-muted opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Performance -->
    <div class="row">
        <!-- Revenue Trend Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">Monthly Revenue Trend</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Marketing Performance -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">Marketing Performance</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="performanceChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Conversion Rate
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> Customer Satisfaction
                        </span>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h6 class="fw-bold mb-3">Key Metrics</h6>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Conversion Rate</span>
                            <span class="fw-bold text-success"><?= $marketing_stats['conversion_rate'] ?>%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" style="width: <?= $marketing_stats['conversion_rate'] ?>%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Customer Satisfaction</span>
                            <span class="fw-bold text-info"><?= $marketing_stats['customer_satisfaction'] ?>%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-info" style="width: <?= $marketing_stats['customer_satisfaction'] ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Access and Recent Activities -->
    <div class="row">
        <!-- Quick Access -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">Quick Access</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3 d-grid">
                            <?= ui_button('add', 'Create Quotation', [
                                'href' => base_url('marketing/quotations'),
                                'color' => 'outline-primary',
                                'icon' => 'fas fa-file-contract',
                                'class' => 'w-100'
                            ]) ?>
                        </div>
                        <div class="col-md-6 mb-3 d-grid">
                            <?= ui_button('view', 'Manage Contracts', [
                                'href' => base_url('marketing/kontrak'),
                                'color' => 'outline-success',
                                'icon' => 'fas fa-handshake',
                                'class' => 'w-100'
                            ]) ?>
                        </div>
                        <div class="col-md-6 mb-3 d-grid">
                            <?= ui_button('view', 'Unit Catalog', [
                                'href' => base_url('marketing/list-unit'),
                                'color' => 'outline-info',
                                'icon' => 'fas fa-list',
                                'class' => 'w-100'
                            ]) ?>
                        </div>
                        <div class="col-md-6 mb-3 d-grid">
                            <?= ui_button('view', 'Available Units', [
                                'href' => base_url('marketing/unit-tersedia'),
                                'color' => 'outline-warning',
                                'icon' => 'fas fa-check-circle',
                                'class' => 'w-100'
                            ]) ?>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-12">
                            <h6 class="fw-bold mb-3">Marketing Tools</h6>
                            <div class="list-group list-group-flush">
                                <a href="#" class="list-group-item list-group-item-action" onclick="showPriceCalculator()">
                                    <i class="fas fa-calculator me-2"></i>
                                    Price Calculator
                                </a>
                                <a href="#" class="list-group-item list-group-item-action" onclick="showCustomerDatabase()">
                                    <i class="fas fa-users me-2"></i>
                                    Customer Database
                                </a>
                                <a href="#" class="list-group-item list-group-item-action" onclick="showMarketAnalysis()">
                                    <i class="fas fa-chart-line me-2"></i>
                                    Market Analysis
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Quotations -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">Recent Quotations</h6>
                    <?= ui_button('view', 'View All', [
                        'href' => base_url('marketing/quotations'),
                        'size' => 'sm'
                    ]) ?>
                </div>
                <div class="card-body">
                    <?php foreach ($recent_quotations as $quotation): ?>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h6 class="mb-1"><?= esc($quotation['id']) ?> - <?= esc($quotation['client']) ?></h6>
                                <p class="mb-1 text-muted"><?= esc($quotation['project']) ?></p>
                                <small class="text-muted">Value: Rp <?= number_format($quotation['value'], 0, ',', '.') ?></small>
                            </div>
                            <div class="text-end">
                                <?php
                                $statusClass = $quotation['status'] == 'Approved' ? 'badge-soft-green' : 
                                              ($quotation['status'] == 'PENDING' ? 'badge-soft-yellow' : 'badge-soft-gray');
                                ?>
                                <span class="badge <?= $statusClass ?>">
                                    <?= $quotation['status'] ?>
                                </span>
                                <br>
                                <small class="text-muted"><?= date('d M Y', strtotime($quotation['created_at'])) ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Contracts -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 fw-bold text-primary">Active Contracts</h6>
            <?= ui_button('view', 'View All Contracts', [
                'href' => base_url('marketing/kontrak'),
                'size' => 'sm'
            ]) ?>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Contract Number</th>
                            <th>Client</th>
                            <th>Project</th>
                            <th>Value</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($active_contracts as $contract): ?>
                            <tr>
                                <td class="fw-bold"><?= esc($contract['contract_number']) ?></td>
                                <td><?= esc($contract['client']) ?></td>
                                <td><?= esc($contract['project']) ?></td>
                                <td>Rp <?= number_format($contract['value'], 0, ',', '.') ?></td>
                                <td><?= date('d M Y', strtotime($contract['start_date'])) ?></td>
                                <td><?= date('d M Y', strtotime($contract['end_date'])) ?></td>
                                <td>
                                    <span class="badge badge-soft-green"><?= $contract['status'] ?></span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <?= ui_button('view', '', [
                                            'onclick' => "viewContract('" . $contract['contract_number'] . "')",
                                            'size' => 'sm',
                                            'color' => 'info',
                                            'title' => 'View',
                                            'icon-only' => true
                                        ]) ?>
                                        <?= ui_button('edit', '', [
                                            'onclick' => "editContract('" . $contract['contract_number'] . "')",
                                            'size' => 'sm',
                                            'title' => 'Edit',
                                            'icon-only' => true
                                        ]) ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    initializeCharts();
});

function initializeCharts() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueData = <?= json_encode(array_values($revenue_data)) ?>;
    const months = <?= json_encode(array_keys($revenue_data)) ?>;
    
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Revenue (Rp)',
                data: revenueData,
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + (value / 1000000) + 'M';
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Revenue: Rp ' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Performance Chart
    const performanceCtx = document.getElementById('performanceChart').getContext('2d');
    
    new Chart(performanceCtx, {
        type: 'doughnut',
        data: {
            labels: ['Conversion Rate', 'Customer Satisfaction'],
            datasets: [{
                data: [<?= $marketing_stats['conversion_rate'] ?>, <?= $marketing_stats['customer_satisfaction'] ?>],
                backgroundColor: ['#1cc88a', '#36b9cc'],
                hoverBackgroundColor: ['#17a673', '#2c9faf'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed + '%';
                        }
                    }
                }
            }
        }
    });
}

function refreshMarketingData() {
    showNotification('Refreshing marketing data...', 'info');
    setTimeout(() => {
        location.reload();
    }, 1000);
}

function showPriceCalculator() {
    showNotification('Price calculator feature coming soon!', 'info');
}

function showCustomerDatabase() {
    window.location.href = '<?= base_url('customers') ?>';
}

function showMarketAnalysis() {
    showNotification('Market analysis feature coming soon!', 'info');
}

function viewContract(contractNumber) {
    // Navigate to kontrak page with contract number
    window.location.href = '<?= base_url('marketing/kontrak') ?>?no_kontrak=' + encodeURIComponent(contractNumber);
}

// Auto-trigger contract view if autoOpenContractId is set (from notification deep linking)
<?php if (isset($autoOpenContractId) && $autoOpenContractId): ?>
console.log('🔔 Auto-opening contract from notification: <?= $autoOpenContractId ?>');
setTimeout(() => {
    // Fetch contract data to get contract number using Kontrak controller
    fetch('<?= base_url('kontrak/detail/') ?><?= $autoOpenContractId ?>')
        .then(r => r.json())
        .then(j => {
            if (j.success && j.data && j.data.no_kontrak) {
                // Redirect to contract management page with contract number
                window.location.href = '<?= base_url('marketing/kontrak') ?>?no_kontrak=' + encodeURIComponent(j.data.no_kontrak);
            } else {
                console.error('❌ Failed to load contract data for auto-open');
            }
        })
        .catch(e => console.error('❌ Error fetching contract:', e));
}, 800);
<?php endif; ?>

function editContract(contractNumber) {
    showNotification('Editing contract ' + contractNumber, 'info');
}

function showNotification(message, type) {
    const alertClass = type === 'success' ? 'alert-success' : 
                      type === 'error' ? 'alert-danger' : 
                      type === 'warning' ? 'alert-warning' : 'alert-info';
    
    const notification = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <strong>${type.charAt(0).toUpperCase() + type.slice(1)}!</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    $('.container-fluid').prepend(notification);
    
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
}
</script>
<?= $this->endSection() ?> 