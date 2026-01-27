<?= $this->extend('layouts/base') ?>

<?= $this->section('title') ?>Executive Dashboard<?= $this->endSection() ?>

<?= $this->section('css') ?>
<style>
    /* Executive Dashboard Custom CSS */
    .kpi-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border: none;
        border-radius: 12px;
        overflow: hidden;
    }
    
    .kpi-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    
    .kpi-icon-wrapper {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
    }
    
    .kpi-value {
        font-size: 2rem;
        font-weight: 700;
        color: #2c3e50;
        line-height: 1.2;
    }
    
    .kpi-label {
        color: #7f8c8d;
        font-size: 0.9rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .chart-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    
    .chart-header {
        background: transparent;
        border-bottom: 1px solid #f1f2f6;
        padding: 1.5rem;
    }
    
    .chart-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #2c3e50;
        margin: 0;
    }

    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .table-alert th {
        font-weight: 600;
        color: #95a5a6;
        font-size: 0.85rem;
        text-transform: uppercase;
        border-top: none;
    }
    
    .table-alert td {
        vertical-align: middle;
        font-size: 0.95rem;
    }
    
    /* Animations */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .animate-fade-in-up {
        animation: fadeInUp 0.5s ease-out forwards;
    }
    
    .delay-100 { animation-delay: 0.1s; }
    .delay-200 { animation-delay: 0.2s; }
    .delay-300 { animation-delay: 0.3s; }
    .delay-400 { animation-delay: 0.4s; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-wrapper container-fluid p-4">
    
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4 animate-fade-in-up">
        <div>
            <h2 class="fw-bold text-dark mb-1">Operational Command Center</h2>
            <p class="text-muted mb-0">Real-time overview of PT Sarana Mitra Luas Tbk operations.</p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-light text-dark border d-flex align-items-center px-3">
                <i class="fas fa-clock me-2 text-primary"></i>
                <span id="currentDateTime">Loading...</span>
            </span>
            <button class="btn btn-primary" onclick="window.location.reload()">
                <i class="fas fa-sync-alt me-2"></i>Refresh Data
            </button>
        </div>
    </div>

    <!-- KPI Cards Row -->
    <div class="row g-4 mb-4">
        <!-- 1. Fleet Utilization -->
        <div class="col-xl-3 col-md-6 animate-fade-in-up delay-100">
            <div class="card kpi-card shadow-sm h-100 bg-white">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="kpi-icon-wrapper bg-primary bg-opacity-10 text-primary me-4">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div>
                        <div class="kpi-label">Fleet Utilization</div>
                        <div class="kpi-value text-primary"><?= number_format($kpi['utilization_rate'], 1) ?>%</div>
                        <div class="small text-muted mt-1">
                            <i class="fas fa-truck me-1"></i> <?= $kpi['total_rented'] ?> / <?= $kpi['total_units'] ?> Units
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Units on Service/Breakdown -->
        <div class="col-xl-3 col-md-6 animate-fade-in-up delay-200">
            <div class="card kpi-card shadow-sm h-100 bg-white">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="kpi-icon-wrapper bg-warning bg-opacity-10 text-warning me-4">
                        <i class="fas fa-tools"></i>
                    </div>
                    <div>
                        <div class="kpi-label">Breakdown/Service</div>
                        <div class="kpi-value text-warning"><?= $kpi['units_breakdown'] ?></div>
                        <div class="small text-muted mt-1">
                            <i class="fas fa-exclamation-circle me-1"></i> Requires Attention
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Active Contracts -->
        <div class="col-xl-3 col-md-6 animate-fade-in-up delay-300">
            <div class="card kpi-card shadow-sm h-100 bg-white">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="kpi-icon-wrapper bg-success bg-opacity-10 text-success me-4">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <div>
                        <div class="kpi-label">Active Contracts</div>
                        <div class="kpi-value text-success"><?= $kpi['active_contracts'] ?></div>
                        <div class="small text-muted mt-1">
                            <i class="fas fa-handshake me-1"></i> Total Deals
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Pending Deliveries -->
        <div class="col-xl-3 col-md-6 animate-fade-in-up delay-400">
            <div class="card kpi-card shadow-sm h-100 bg-white">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="kpi-icon-wrapper bg-info bg-opacity-10 text-info me-4">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <div>
                        <div class="kpi-label">Pending Delivery</div>
                        <div class="kpi-value text-info"><?= $kpi['pending_delivery'] ?></div>
                        <div class="small text-muted mt-1">
                            <i class="fas fa-clock me-1"></i> Scheduled for today/tmrw
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Unit Status Composition -->
        <div class="col-lg-5 animate-fade-in-up delay-200">
            <div class="card chart-card h-100">
                <div class="card-header chart-header d-flex justify-content-between align-items-center bg-white">
                    <h5 class="chart-title">Fleet Composition</h5>
                    <button class="btn btn-sm btn-light rounded-circle" type="button"><i class="fas fa-ellipsis-h text-muted"></i></button>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 300px;">
                    <div style="width: 100%; max-width: 350px;">
                        <canvas id="unitStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sales Trend -->
        <div class="col-lg-7 animate-fade-in-up delay-300">
            <div class="card chart-card h-100">
                <div class="card-header chart-header d-flex justify-content-between align-items-center bg-white">
                    <h5 class="chart-title">Sales Performance (Recent)</h5>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary active">6 Months</button>
                        <button type="button" class="btn btn-outline-secondary">Year</button>
                    </div>
                </div>
                <div class="card-body" style="min-height: 300px;">
                    <canvas id="salesTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Tables Row -->
    <div class="row g-4 animate-fade-in-up delay-400">
        <!-- Low Stock Alert -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-danger"><i class="fas fa-cubes me-2"></i>Low Stock Alert (Warehouse)</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-alert mb-0 align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Item Name</th>
                                    <th>Stock</th>
                                    <th>Min</th>
                                    <th>Link</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($alerts['low_stock'])): ?>
                                    <tr><td colspan="4" class="text-center py-4 text-muted">All stock levels are healthy via Optima.</td></tr>
                                <?php else: ?>
                                    <?php foreach (array_slice($alerts['low_stock'], 0, 5) as $item): ?>
                                    <tr>
                                        <td class="ps-4 fw-medium"><?= $item['name'] ?></td>
                                        <td><span class="text-danger fw-bold"><?= $item['qty'] ?></span></td>
                                        <td><?= $item['min_stock'] ?></td>
                                        <td><a href="#" class="btn btn-sm btn-light text-primary"><i class="fas fa-arrow-right"></i></a></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preventive Maintenance -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-warning"><i class="fas fa-wrench me-2"></i>Upcoming Maintenance</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-alert mb-0 align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Unit Code</th>
                                    <th>Type</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($alerts['maintenance'])): ?>
                                    <tr><td colspan="4" class="text-center py-4 text-muted">No immediate maintenance required.</td></tr>
                                <?php else: ?>
                                    <?php foreach (array_slice($alerts['maintenance'], 0, 5) as $unit): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-dark"><?= $unit['code'] ?></td>
                                        <td><small class="text-muted"><?= $unit['type'] ?></small></td>
                                        <td><?= date('d M', strtotime($unit['next_service_date'])) ?></td>
                                        <td><span class="badge bg-warning text-dark">Upcoming</span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expiring Contracts -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-info"><i class="fas fa-file-signature me-2"></i>Expiring Contracts</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-alert mb-0 align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Customer</th>
                                    <th>Unit</th>
                                    <th>End Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($alerts['expiring_contracts'])): ?>
                                    <tr><td colspan="4" class="text-center py-4 text-muted">No contracts expiring next 30 days.</td></tr>
                                <?php else: ?>
                                    <?php foreach (array_slice($alerts['expiring_contracts'], 0, 5) as $contract): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="text-truncate" style="max-width: 120px;"><?= $contract['customer'] ?></div>
                                        </td>
                                        <td><small><?= $contract['unit_code'] ?></small></td>
                                        <td class="text-danger"><?= date('d M', strtotime($contract['end_date'])) ?></td>
                                        <td><a href="#" class="btn btn-sm btn-outline-info rounded-pill px-2">Renew</a></td>
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
</div>

<?= $this->section('javascript') ?>
<script>
    // --- 1. DateTime Clock ---
    function updateDateTime() {
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
        document.getElementById('currentDateTime').textContent = now.toLocaleDateString('en-US', options);
    }
    setInterval(updateDateTime, 1000);
    updateDateTime();

    // --- 2. Unit Status Pie Chart ---
    const ctxUnit = document.getElementById('unitStatusChart').getContext('2d');
    new Chart(ctxUnit, {
        type: 'doughnut',
        data: {
            labels: ['Rented', 'Ready/Available', 'Maintenance', 'Breakdown'],
            datasets: [{
                data: [
                    <?= $charts['unit_status']['rented'] ?>,
                    <?= $charts['unit_status']['ready'] ?>,
                    <?= $charts['unit_status']['maintenance'] ?>,
                    <?= $charts['unit_status']['breakdown'] ?>
                ],
                backgroundColor: [
                    '#0d6efd', // Primary Blue (Rented)
                    '#198754', // Success Green (Ready)
                    '#ffc107', // Warning Yellow (Maintenance)
                    '#dc3545'  // Danger Red (Breakdown)
                ],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                }
            },
            cutout: '70%'
        }
    });

    // --- 3. Sales Trend Bar Chart (Quotation vs Contract) ---
    const ctxSales = document.getElementById('salesTrendChart').getContext('2d');
    new Chart(ctxSales, {
        type: 'bar',
        data: {
            labels: <?= json_encode($charts['sales_trend']['labels']) ?>,
            datasets: [
                {
                    label: 'Quotations',
                    data: <?= json_encode($charts['sales_trend']['quotations']) ?>,
                    backgroundColor: '#e9ecef',
                    borderRadius: 4,
                    barPercentage: 0.6
                },
                {
                    label: 'Contracts (Deal)',
                    data: <?= json_encode($charts['sales_trend']['contracts']) ?>,
                    backgroundColor: '#0d6efd',
                    borderRadius: 4,
                    barPercentage: 0.6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    align: 'end'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false,
                        color: '#f8f9fa'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>
