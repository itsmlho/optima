<?= $this->extend('layouts/base') ?>

<?= $this->section('title') ?>Executive Dashboard<?= $this->endSection() ?>

<?= $this->section('css') ?>
<style>
    /* Modern Dashboard Redesign - Professional & Fresh */
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        --warning-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --info-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --dark-gradient: linear-gradient(135deg, #434343 0%, #000000 100%);
    }
    
    body {
        background: #f8f9fa;
        font-family: 'Inter', 'Segoe UI', sans-serif;
    }
    
    /* KPI Cards - Modern Design */
    .kpi-card {
        border: none;
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        background: white;
        position: relative;
    }
    
    .kpi-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.15);
    }
    
    .kpi-card-header {
        background: linear-gradient(135deg, rgba(0,0,0,0.05) 0%, rgba(0,0,0,0.02) 100%);
        padding: 1.25rem;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    
    .kpi-card-body {
        padding: 1.5rem;
    }
    
    .kpi-icon {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin-bottom: 1rem;
    }
    
    .kpi-value {
        font-size: 2.5rem;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 0.5rem;
    }
    
    .kpi-label {
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
    }
    
    .kpi-trend {
        font-size: 0.875rem;
        font-weight: 600;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        display: inline-block;
        margin-top: 0.5rem;
    }
    
    /* Chart Cards */
    .chart-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        background: white;
        height: 100%;
    }
    
    .chart-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        background: linear-gradient(135deg, rgba(0,0,0,0.02) 0%, transparent 100%);
    }
    
    .chart-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }
    
    .chart-body {
        padding: 1.5rem;
        height: 300px;
    }
    
    /* Widget Cards */
    .widget-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        background: white;
        transition: all 0.3s ease;
    }
    
    .widget-card:hover {
        box-shadow: 0 8px 30px rgba(0,0,0,0.12);
    }
    
    .widget-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .widget-title {
        font-size: 1rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .widget-body {
        padding: 1.5rem;
    }
    
    /* Table Styles */
    .modern-table {
        margin: 0;
    }
    
    .modern-table thead th {
        background: #f8fafc;
        border: none;
        color: #64748b;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 0.75rem 1rem;
    }
    
    .modern-table tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.2s;
    }
    
    .modern-table tbody tr:hover {
        background: #f8fafc;
    }
    
    .modern-table tbody td {
        padding: 1rem;
        border: none;
        vertical-align: middle;
    }
    
    /* Badges */
    .badge-gradient-primary {
        background: var(--primary-gradient);
        border: none;
        padding: 0.35rem 0.75rem;
        font-weight: 600;
    }
    
    .badge-gradient-success {
        background: var(--success-gradient);
        border: none;
        padding: 0.35rem 0.75rem;
        font-weight: 600;
    }
    
    .badge-gradient-warning {
        background: var(--warning-gradient);
        border: none;
        padding: 0.35rem 0.75rem;
        font-weight: 600;
    }
    
    .badge-gradient-info {
        background: var(--info-gradient);
        border: none;
        padding: 0.35rem 0.75rem;
        font-weight: 600;
    }
    
    /* Alert Cards */
    .alert-card {
        border-left: 4px solid;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 0.75rem;
        background: white;
        transition: all 0.2s;
    }
    
    .alert-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateX(4px);
    }
    
    .alert-card-warning {
        border-left-color: #f59e0b;
    }
    
    .alert-card-danger {
        border-left-color: #ef4444;
    }
    
    .alert-card-info {
        border-left-color: #3b82f6;
    }
    
    /* Timeline */
    .timeline-item {
        padding: 0.75rem 0;
        border-left: 2px solid #e5e7eb;
        padding-left: 1.5rem;
        position: relative;
    }
    
    .timeline-item:before {
        content: '';
        position: absolute;
        left: -5px;
        top: 1rem;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #3b82f6;
    }
    
    .timeline-item:last-child {
        border-left-color: transparent;
    }
    
    /* Loading States */
    .skeleton {
        animation: skeleton-loading 1s linear infinite alternate;
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
    }
    
    @keyframes skeleton-loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .kpi-value { font-size: 1.75rem; }
        .chart-body { height: 250px; }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1 fw-bold">Operational Command Center</h3>
                    <p class="text-muted mb-0">Real-time overview of PT Sarana Mitra Luas Tbk operations</p>
                </div>
                <button class="btn btn-primary" id="refreshDashboard">
                    <i class="fas fa-sync-alt me-2"></i>Refresh Data
                </button>
            </div>
        </div>
    </div>

    <!-- KPI Cards Row -->
    <div class="row g-3 mb-4">
        <!-- Fleet Utilization -->
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card">
                <div class="kpi-card-body">
                    <div class="kpi-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <i class="fas fa-chart-pie text-white"></i>
                    </div>
                    <div class="kpi-value text-primary" id="kpiFleetUtil"><?= number_format($kpi['utilization_rate'], 1) ?>%</div>
                    <div class="kpi-label">Fleet Utilization</div>
                    <small class="text-muted"><?= $kpi['total_rented'] ?> / <?= $kpi['total_units'] ?> Units</small>
                </div>
            </div>
        </div>

        <!-- Breakdown/Service -->
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card">
                <div class="kpi-card-body">
                    <div class="kpi-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <i class="fas fa-wrench text-white"></i>
                    </div>
                    <div class="kpi-value text-danger" id="kpiBreakdown"><?= $kpi['units_breakdown'] ?></div>
                    <div class="kpi-label">Units in Service</div>
                    <small class="text-muted">Requires Attention</small>
                </div>
            </div>
        </div>

        <!-- Active Contracts -->
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card">
                <div class="kpi-card-body">
                    <div class="kpi-icon" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                        <i class="fas fa-file-contract text-white"></i>
                    </div>
                    <div class="kpi-value text-success" id="kpiContracts"><?= $kpi['active_contracts'] ?></div>
                    <div class="kpi-label">Active Contracts</div>
                    <small class="text-muted">Total Deals</small>
                </div>
            </div>
        </div>

        <!-- Pending Delivery -->
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card">
                <div class="kpi-card-body">
                    <div class="kpi-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <i class="fas fa-truck text-white"></i>
                    </div>
                    <div class="kpi-value text-info" id="kpiDelivery"><?= $kpi['pending_delivery'] ?></div>
                    <div class="kpi-label">Pending Delivery</div>
                    <small class="text-muted">Scheduled for today/tmrw</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-4">
        <!-- Fleet Composition Donut Chart -->
        <div class="col-lg-6">
            <div class="chart-card">
                <div class="chart-header">
                    <h5 class="chart-title">
                        <i class="fas fa-chart-pie me-2 text-primary"></i>Fleet Status Distribution
                    </h5>
                </div>
                <div class="chart-body">
                    <canvas id="fleetStatusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Delivery Performance Bar Chart -->
        <div class="col-lg-6">
            <div class="chart-card">
                <div class="chart-header">
                    <h5 class="chart-title">
                        <i class="fas fa-chart-bar me-2 text-success"></i>Delivery Performance (This Month)
                    </h5>
                </div>
                <div class="chart-body">
                    <canvas id="deliveryChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row g-3 mb-4">
        <!-- Team Performance -->
        <div class="col-lg-6">
            <div class="widget-card">
                <div class="widget-header">
                    <h5 class="widget-title">
                        <i class="fas fa-users"></i>
                        Team Performance (Week/Month)
                    </h5>
                </div>
                <div class="widget-body">
                    <!-- Central -->
                    <div class="mb-4">
                        <h6 class="text-primary fw-bold mb-3">
                            <i class="fas fa-building me-2"></i>CENTRAL Workshop
                        </h6>
                        <div class="table-responsive">
                            <table class="table modern-table table-sm">
                                <thead>
                                    <tr>
                                        <th>Mechanic</th>
                                        <th class="text-center">WO</th>
                                        <th class="text-center">SPK</th>
                                    </tr>
                                </thead>
                                <tbody id="teamCentralBody">
                                    <tr><td colspan="3" class="text-center text-muted py-3">Loading data...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Mill -->
                    <div>
                        <h6 class="text-success fw-bold mb-3">
                            <i class="fas fa-map-marker-alt me-2"></i>MILL Areas
                        </h6>
                        <div class="table-responsive">
                            <table class="table modern-table table-sm">
                                <thead>
                                    <tr>
                                        <th>Mechanic</th>
                                        <th>Area</th>
                                        <th class="text-center">WO</th>
                                        <th class="text-center">SPK</th>
                                    </tr>
                                </thead>
                                <tbody id="teamBranchBody">
                                    <tr><td colspan="4" class="text-center text-muted py-3">Loading data...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Delivery -->
        <div class="col-lg-6">
            <div class="widget-card">
                <div class="widget-header">
                    <h5 class="widget-title">
                        <i class="fas fa-truck-loading"></i>
                        Delivery Report (This Month)
                    </h5>
                </div>
                <div class="widget-body">
                    <!-- Total Delivered -->
                    <div class="d-flex align-items-center justify-content-between mb-4 p-3 rounded" style="background: #f8fafc;">
                        <div>
                            <div class="text-muted small mb-1">Total Units Delivered</div>
                            <h3 class="mb-0 fw-bold" id="totalDelivered">0</h3>
                        </div>
                        <div class="kpi-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="fas fa-box text-white"></i>
                        </div>
                    </div>

                    <!-- By Command Type -->
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">By Command Type</h6>
                        <div id="commandTypeList" class="row g-2">
                            <div class="col-12 text-center text-muted py-2">Loading data...</div>
                        </div>
                    </div>

                    <!-- By Destination -->
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">By Destination</h6>
                        <div id="commandDestList" class="row g-2">
                            <div class="col-12 text-center text-muted py-2">Loading data...</div>
                        </div>
                    </div>

                    <!-- Status Progress -->
                    <div>
                        <h6 class="fw-bold mb-3">Status Progress</h6>
                        <div id="statusProgressList">
                            <div class="text-center text-muted py-2">Loading data...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Content Row -->
    <div class="row g-3 mb-4">
        <!-- Quotations Performance -->
        <div class="col-lg-6">
            <div class="widget-card">
                <div class="widget-header">
                    <h5 class="widget-title">
                        <i class="fas fa-file-invoice-dollar"></i>
                        Quotations Performance
                    </h5>
                </div>
                <div class="widget-body">
                    <div class="row g-3">
                        <div class="col-4">
                            <div class="text-center p-3 rounded" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <div class="text-white opacity-75 small mb-2">Total Quotations</div>
                                <h3 class="text-white fw-bold mb-0" id="totalQuotations">0</h3>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center p-3 rounded" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                                <div class="text-white opacity-75 small mb-2">Deals Converted</div>
                                <h3 class="text-white fw-bold mb-0" id="dealsConverted">0</h3>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center p-3 rounded" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <div class="text-white opacity-75 small mb-2">Conversion Rate</div>
                                <h3 class="text-white fw-bold mb-0" id="conversionRate">0%</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Spare Parts -->
        <div class="col-lg-6">
            <div class="widget-card">
                <div class="widget-header">
                    <h5 class="widget-title">
                        <i class="fas fa-cog"></i>
                        Top Spare Parts Used (WO Complaints)
                    </h5>
                </div>
                <div class="widget-body">
                    <div class="table-responsive">
                        <table class="table modern-table table-sm">
                            <thead>
                                <tr>
                                    <th>Part Name</th>
                                    <th class="text-center">Week</th>
                                    <th class="text-center">Month</th>
                                </tr>
                            </thead>
                            <tbody id="topSparePartsBody">
                                <tr><td colspan="3" class="text-center text-muted py-3">Loading data...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts Row -->
    <div class="row g-3 mb-4">
        <!-- Expiring Contracts -->
        <div class="col-lg-4">
            <div class="widget-card">
                <div class="widget-header">
                    <h5 class="widget-title">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                        Expiring Contracts
                    </h5>
                </div>
                <div class="widget-body" style="max-height: 300px; overflow-y: auto;">
                    <div id="expiringContractsList">
                        <div class="text-center text-muted py-3">No expiring contracts</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="col-lg-8">
            <div class="widget-card">
                <div class="widget-header">
                    <h5 class="widget-title">
                        <i class="fas fa-history"></i>
                        Recent Activities
                    </h5>
                    <a href="<?= base_url('admin/activity-log') ?>" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="widget-body" style="max-height: 300px; overflow-y: auto;">
                    <div id="recentActivitiesTimeline">
                        <div class="text-center text-muted py-3">Loading activities...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// Modern Dashboard JavaScript
let fleetChart, deliveryChart;

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    loadAllData();
    
    // Refresh button
    document.getElementById('refreshDashboard').addEventListener('click', function() {
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Refreshing...';
        loadAllData();
        setTimeout(() => {
            this.innerHTML = '<i class="fas fa-sync-alt me-2"></i>Refresh Data';
        }, 1000);
    });
});

// Initialize Charts
function initializeCharts() {
    // Fleet Status Donut Chart
    const fleetCtx = document.getElementById('fleetStatusChart').getContext('2d');
    fleetChart = new Chart(fleetCtx, {
        type: 'doughnut',
        data: {
            labels: ['Rented', 'Ready', 'Maintenance', 'Breakdown'],
            datasets: [{
                data: [<?= $chartUnit['rented'] ?>, <?= $chartUnit['ready'] ?>, <?= $chartUnit['maintenance'] ?>, <?= $chartUnit['breakdown'] ?>],
                backgroundColor: [
                    'rgba(102, 126, 234, 0.8)',
                    'rgba(17, 153, 142, 0.8)',
                    'rgba(240, 147, 251, 0.8)',
                    'rgba(245, 87, 108, 0.8)'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: { size: 12, weight: '600' }
                    }
                }
            }
        }
    });

    // Delivery Performance Bar Chart (will be populated by API)
    const deliveryCtx = document.getElementById('deliveryChart').getContext('2d');
    deliveryChart = new Chart(deliveryCtx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Units',
                data: [],
                backgroundColor: 'rgba(17, 153, 142, 0.8)',
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
}

// Load All Dashboard Data
function loadAllData() {
    loadReportDelivery();
    loadTeamPerformance();
    loadQuotationsPerformance();
    loadTopSpareParts();
    loadRecentActivities();
}

// Load Report Delivery
function loadReportDelivery() {
    fetch('<?= base_url('dashboard/report-delivery') ?>')
        .then(response => response.json())
        .then(result => {
            if (result.success && result.data) {
                const data = result.data;
                
                // Total Delivered
                document.getElementById('totalDelivered').textContent = data.total_delivered;
                
                // By Command Type
                let commandTypeHtml = '';
                if (data.by_jenis_perintah && data.by_jenis_perintah.length > 0) {
                    data.by_jenis_perintah.forEach(item => {
                        commandTypeHtml += `
                            <div class="col-6">
                                <div class="d-flex align-items-center p-2 rounded bg-light">
                                    <div class="flex-grow-1">
                                        <div class="small text-muted">${item.jenis_perintah || 'Unknown'}</div>
                                        <div class="fw-bold">${item.total} units</div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    commandTypeHtml = '<div class="col-12 text-center text-muted py-2">No data</div>';
                }
                document.getElementById('commandTypeList').innerHTML = commandTypeHtml;
                
                // By Destination
                let destHtml = '';
                if (data.by_tujuan_perintah && data.by_tujuan_perintah.length > 0) {
                    data.by_tujuan_perintah.forEach(item => {
                        destHtml += `
                            <div class="col-6">
                                <div class="d-flex align-items-center p-2 rounded bg-light">
                                    <div class="flex-grow-1">
                                        <div class="small text-muted">${item.tujuan_perintah || 'Unknown'}</div>
                                        <div class="fw-bold">${item.total} units</div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    destHtml = '<div class="col-12 text-center text-muted py-2">No data</div>';
                }
                document.getElementById('commandDestList').innerHTML = destHtml;
                
                // Status Progress
                let statusHtml = '';
                if (data.by_status && data.by_status.length > 0) {
                    data.by_status.forEach(item => {
                        const statusClass = item.status_di === 'SELESAI' ? 'success' : 
                                          item.status_di === 'DALAM_PERJALANAN' ? 'info' : 'warning';
                        statusHtml += `
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-${statusClass}">${item.status_di}</span>
                                <strong>${item.total}</strong>
                            </div>
                        `;
                    });
                } else {
                    statusHtml = '<div class="text-center text-muted py-2">No data</div>';
                }
                document.getElementById('statusProgressList').innerHTML = statusHtml;
                
                // Update Delivery Chart
                if (data.by_jenis_perintah && data.by_jenis_perintah.length > 0) {
                    deliveryChart.data.labels = data.by_jenis_perintah.map(i => i.jenis_perintah || 'Unknown');
                    deliveryChart.data.datasets[0].data = data.by_jenis_perintah.map(i => i.total);
                    deliveryChart.update();
                }
            }
        })
        .catch(error => console.error('Error loading delivery report:', error));
}

// Load Team Performance
function loadTeamPerformance() {
    fetch('<?= base_url('dashboard/team-performance') ?>')
        .then(response => response.json())
        .then(result => {
            if (result.success && result.data) {
                const data = result.data;
                
                // Central Team
                let centralHtml = '';
                if (data.central && data.central.length > 0) {
                    data.central.forEach(member => {
                        centralHtml += `
                            <tr>
                                <td class="fw-medium">${member.name}</td>
                                <td class="text-center">
                                    <span class="badge badge-gradient-warning">${member.wo_week}/${member.wo_month}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-gradient-success">${member.spk_week}/${member.spk_month}</span>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    centralHtml = '<tr><td colspan="3" class="text-center text-muted py-3">No data</td></tr>';
                }
                document.getElementById('teamCentralBody').innerHTML = centralHtml;
                
                // Branch Team
                let branchHtml = '';
                if (data.branch && data.branch.length > 0) {
                    data.branch.forEach(member => {
                        branchHtml += `
                            <tr>
                                <td class="fw-medium">${member.name}</td>
                                <td><small class="text-muted">${member.area}</small></td>
                                <td class="text-center">
                                    <span class="badge badge-gradient-warning">${member.wo_week}/${member.wo_month}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-gradient-success">${member.spk_week}/${member.spk_month}</span>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    branchHtml = '<tr><td colspan="4" class="text-center text-muted py-3">No data</td></tr>';
                }
                document.getElementById('teamBranchBody').innerHTML = branchHtml;
            }
        })
        .catch(error => console.error('Error loading team performance:', error));
}

// Load Quotations Performance
function loadQuotationsPerformance() {
    fetch('<?= base_url('dashboard/quotations-performance') ?>')
        .then(response => response.json())
        .then(result => {
            if (result.success && result.data) {
                const data = result.data;
                document.getElementById('totalQuotations').textContent = data.total_quotations;
                document.getElementById('dealsConverted').textContent = data.deals_converted;
                document.getElementById('conversionRate').textContent = data.conversion_rate + '%';
            }
        })
        .catch(error => console.error('Error loading quotations:', error));
}

// Load Top Spare Parts
function loadTopSpareParts() {
    fetch('<?= base_url('dashboard/top-spare-parts') ?>')
        .then(response => response.json())
        .then(result => {
            if (result.success && result.data) {
                const parts = result.data;
                let html = '';
                
                if (parts.length === 0) {
                    html = '<tr><td colspan="3" class="text-center text-muted py-3">No data available</td></tr>';
                } else {
                    parts.forEach(part => {
                        html += `
                            <tr>
                                <td class="fw-medium">${part.name}</td>
                                <td class="text-center"><span class="badge badge-gradient-info">${part.week}</span></td>
                                <td class="text-center"><span class="badge badge-gradient-primary">${part.month}</span></td>
                            </tr>
                        `;
                    });
                }
                document.getElementById('topSparePartsBody').innerHTML = html;
            }
        })
        .catch(error => console.error('Error loading spare parts:', error));
}

// Load Recent Activities
function loadRecentActivities() {
    fetch('<?= base_url('dashboard/recent-activities') ?>?limit=5')
        .then(response => response.json())
        .then(result => {
            if (result.success && result.data) {
                const activities = result.data;
                let html = '';
                
                if (activities.length === 0) {
                    html = '<div class="text-center text-muted py-3">No recent activities</div>';
                } else {
                    activities.forEach(activity => {
                        const iconClass = activity.action_type === 'CREATE' ? 'fa-plus text-success' :
                                        activity.action_type === 'UPDATE' ? 'fa-edit text-primary' :
                                        activity.action_type === 'DELETE' ? 'fa-trash text-danger' :
                                        'fa-info-circle text-info';
                        
                        html += `
                            <div class="timeline-item">
                                <div class="d-flex align-items-start">
                                    <i class="fas ${iconClass} me-3 mt-1"></i>
                                    <div class="flex-grow-1">
                                        <div class="fw-medium">${activity.action_description}</div>
                                        <small class="text-muted">
                                            ${activity.username} • ${activity.module_name} • ${activity.time_ago}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                }
                document.getElementById('recentActivitiesTimeline').innerHTML = html;
            }
        })
        .catch(error => console.error('Error loading activities:', error));
}
</script>
<?= $this->endSection() ?>
