<?= $this->extend('layouts/base') ?>

<?= $this->section('title') ?>Executive Dashboard<?= $this->endSection() ?>

<?= $this->section('css') ?>
<style>
    /* Page-specific overrides only */
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
    
    /* Badges - Clean Enterprise Style */
    .badge-gradient-primary {
        background: #3b82f6;
        color: white;
        border: none;
        padding: 0.35rem 0.75rem;
        font-weight: 600;
    }
    
    .badge-gradient-success {
        background: #22c55e;
        color: white;
        border: none;
        padding: 0.35rem 0.75rem;
        font-weight: 600;
    }
    
    .badge-gradient-warning {
        background: #fb923c;
        color: white;
        border: none;
        padding: 0.35rem 0.75rem;
        font-weight: 600;
    }
    
    .badge-gradient-info {
        background: #38bdf8;
        color: white;
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
    <div class="kpi-grid">
        <!-- Fleet Utilization -->
        <div class="kpi-card kpi-primary">
            <div class="kpi-icon">
                <i class="fas fa-chart-pie"></i>
            </div>
            <div class="kpi-content">
                <div class="kpi-value" id="kpiFleetUtil">--</div>
                <div class="kpi-label">Fleet Utilization</div>
                <div class="kpi-change" id="kpiFleetDetails">Loading...</div>
            </div>
        </div>

        <!-- Breakdown/Service -->
        <div class="kpi-card kpi-danger">
            <div class="kpi-icon">
                <i class="fas fa-wrench"></i>
            </div>
            <div class="kpi-content">
                <div class="kpi-value" id="kpiBreakdown">--</div>
                <div class="kpi-label">Units in Service</div>
                <div class="kpi-change">Requires Attention</div>
            </div>
        </div>

        <!-- Active Contracts -->
        <div class="kpi-card kpi-success">
            <div class="kpi-icon">
                <i class="fas fa-file-contract"></i>
            </div>
            <div class="kpi-content">
                <div class="kpi-value" id="kpiContracts">--</div>
                <div class="kpi-label">Active Contracts</div>
                <div class="kpi-change">Total Deals</div>
            </div>
        </div>

        <!-- Pending Delivery -->
        <div class="kpi-card kpi-info">
            <div class="kpi-icon">
                <i class="fas fa-truck"></i>
            </div>
            <div class="kpi-content">
                <div class="kpi-value" id="kpiDelivery">--</div>
                <div class="kpi-label">Pending Delivery</div>
                <div class="kpi-change">Scheduled for today/tmrw</div>
            </div>
        </div>
    </div>

    <!-- Unlinked Deliveries Alert Widget -->
    <div class="row mb-4" id="unlinkedDeliveriesWidget" style="display:none;">
        <div class="col-12">
            <div class="alert alert-warning border-warning border-start border-4 shadow-sm mb-0" role="alert">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                        </div>
                        <div>
                            <h5 class="alert-heading mb-1">
                                <strong id="urgentDeliveriesCount">0</strong> Urgent Deliveries Pending Contract Link
                            </h5>
                            <p class="mb-0">
                                <span id="totalUnlinkedCount">0</span> total unlinked deliveries | 
                                Oldest pending: <strong id="oldestPendingDays">0</strong> days
                            </p>
                        </div>
                    </div>
                    <div>
                        <a href="<?= base_url('marketing/di') ?>" class="btn btn-warning">
                            <i class="fas fa-link me-2"></i>View Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-4">
        <!-- Fleet Composition Donut Chart -->
        <div class="col-lg-4">
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

        <!-- Rental Type Breakdown Chart -->
        <div class="col-lg-4">
            <div class="chart-card">
                <div class="chart-header">
                    <h5 class="chart-title">
                        <i class="fas fa-file-contract me-2 text-info"></i>Rental Type Breakdown
                    </h5>
                </div>
                <div class="chart-body">
                    <canvas id="rentalTypeChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Delivery Performance Bar Chart -->
        <div class="col-lg-4">
            <div class="chart-card">
                <div class="chart-header">
                    <h5 class="chart-title">
                        <i class="fas fa-chart-bar me-2 text-success"></i>Delivery Performance (Last 6 Months)
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
                        <div class="table-scroll-wrapper">
                            <table class="table table-mobile-card modern-table table-sm">
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
                        <div class="table-scroll-wrapper">
                            <table class="table table-mobile-card modern-table table-sm">
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
                        Delivery Report (Last 6 Months)
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
                            <div class="text-center p-3 rounded" style="background: #3b82f6;">
                                <div class="text-white opacity-75 small mb-2">Total Quotations</div>
                                <h3 class="text-white fw-bold mb-0" id="totalQuotations">0</h3>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center p-3 rounded" style="background: #22c55e;">
                                <div class="text-white opacity-75 small mb-2">Deals Converted</div>
                                <h3 class="text-white fw-bold mb-0" id="dealsConverted">0</h3>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center p-3 rounded" style="background: #ec4899;">
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
                    <div class="table-scroll-wrapper">
                        <table class="table table-mobile-card modern-table table-sm">
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
let fleetChart, deliveryChart, rentalTypeChart;

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
    // Fleet Status Donut Chart - will be populated by API
    const fleetCtx = document.getElementById('fleetStatusChart').getContext('2d');
    fleetChart = new Chart(fleetCtx, {
        type: 'doughnut',
        data: {
            labels: ['Rented', 'Ready', 'Maintenance', 'Breakdown'],
            datasets: [{
                data: [0, 0, 0, 0],
                backgroundColor: [
                    '#3b82f6',
                    '#22c55e',
                    '#fb923c',
                    '#f87171'
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

    // Rental Type Breakdown Donut Chart
    const rentalTypeCtx = document.getElementById('rentalTypeChart').getContext('2d');
    rentalTypeChart = new Chart(rentalTypeCtx, {
        type: 'doughnut',
        data: {
            labels: ['Formal Contract', 'PO Only', 'Daily/Spot'],
            datasets: [{
                data: [0, 0, 0],
                backgroundColor: [
                    '#3b82f6',  // Blue for CONTRACT
                    '#38bdf8',  // Light blue for PO_ONLY
                    '#fb923c'   // Orange for DAILY_SPOT
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
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return label + ': ' + value + ' (' + percentage + '%)';
                        }
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
                backgroundColor: '#22c55e',
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
    loadKPIData();
    loadReportDelivery();
    loadTeamPerformance();
    loadQuotationsPerformance();
    loadTopSpareParts();
    loadExpiringContracts();
    loadRecentActivities();
    loadRentalTypeAnalytics();
    loadUnlinkedDeliveriesWidget();
}

// Load KPI Data and Fleet Chart
async function loadKPIData() {
    try {
        const response = await fetch('<?= base_url('dashboard/kpi-data') ?>');
        if (!response.ok) throw new Error('Failed to load KPI data');
        
        const data = await response.json();
        // console.log('KPI Data received:', data);
        
        // Check if data has expected structure
        if (data && data.kpi) {
            // Update KPI Cards with safe fallbacks
            document.getElementById('kpiFleetUtil').textContent = (data.kpi.utilization_rate || 0).toFixed(1) + '%';
            document.getElementById('kpiFleetDetails').textContent = `${data.kpi.total_rented || 0} / ${data.kpi.total_units || 0} Units`;
            document.getElementById('kpiBreakdown').textContent = data.kpi.units_breakdown || 0;
            document.getElementById('kpiContracts').textContent = data.kpi.active_contracts || 0;
            document.getElementById('kpiDelivery').textContent = data.kpi.pending_delivery || 0;
            
            // Update Fleet Chart
            if (fleetChart && data.chartUnit) {
                fleetChart.data.datasets[0].data = [
                    data.chartUnit.rented || 0,
                    data.chartUnit.ready || 0,
                    data.chartUnit.maintenance || 0,
                    data.chartUnit.breakdown || 0
                ];
                fleetChart.update();
            }
        } else {
            console.warn('KPI data structure unexpected:', data);
            document.getElementById('kpiFleetUtil').textContent = '0%';
            document.getElementById('kpiFleetDetails').textContent = '0 / 0 Units';
            document.getElementById('kpiBreakdown').textContent = '0';
            document.getElementById('kpiContracts').textContent = '0';
            document.getElementById('kpiDelivery').textContent = '0';
        }
    } catch (error) {
        console.error('Error loading KPI data:', error);
        document.getElementById('kpiFleetUtil').textContent = 'Error';
        document.getElementById('kpiFleetDetails').textContent = 'Failed to load';
        document.getElementById('kpiBreakdown').textContent = '--';
        document.getElementById('kpiContracts').textContent = '--';
        document.getElementById('kpiDelivery').textContent = '--';
    }
}

// Load Report Delivery
function loadReportDelivery() {
    fetch('<?= base_url('dashboard/report-delivery') ?>')
        .then(response => response.json())
        .then(result => {
            // console.log('Delivery Report Data:', result);
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
                if (deliveryChart && data.chart_labels && data.chart_data) {
                    deliveryChart.data.labels = data.chart_labels;
                    deliveryChart.data.datasets[0].data = data.chart_data;
                    deliveryChart.update();
                }
            } else {
                console.warn('Delivery report: No data or invalid response', result);
            }
        })
        .catch(error => {
            console.error('Error loading delivery report:', error);
            document.getElementById('totalDelivered').textContent = 'Error';
        });
}

// Load Team Performance
function loadTeamPerformance() {
    fetch('<?= base_url('dashboard/team-performance') ?>')
        .then(response => response.json())
        .then(result => {
            // console.log('Team Performance Data:', result);
            if (result.success && result.data) {
                const data = result.data;
                
                // Debug info
                if (result.debug) {
                    // console.log('Team Debug:', result.debug);
                }
                
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
            } else {
                console.warn('Team performance: No data or invalid response', result);
                document.getElementById('teamCentralBody').innerHTML = '<tr><td colspan="3" class="text-center text-muted py-3">No data available</td></tr>';
                document.getElementById('teamBranchBody').innerHTML = '<tr><td colspan="4" class="text-center text-muted py-3">No data available</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error loading team performance:', error);
            document.getElementById('teamCentralBody').innerHTML = '<tr><td colspan="3" class="text-center text-danger py-3">Error loading data</td></tr>';
            document.getElementById('teamBranchBody').innerHTML = '<tr><td colspan="4" class="text-center text-danger py-3">Error loading data</td></tr>';
        });
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

// Load Expiring Contracts
function loadExpiringContracts() {
    fetch('<?= base_url('dashboard/expiring-contracts') ?>')
        .then(response => response.json())
        .then(result => {
            if (result.success && result.data) {
                const contracts = result.data;
                let html = '';
                
                if (contracts.length === 0) {
                    html = '<div class="text-center text-muted py-3">No expiring contracts</div>';
                } else {
                    contracts.forEach(contract => {
                        const daysClass = contract.days_left <= 7 ? 'danger' : 
                                        contract.days_left <= 14 ? 'warning' : 'info';
                        html += `
                            <div class="alert alert-${daysClass} alert-sm mb-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>${contract.no_kontrak}</strong><br>
                                        <small>${contract.customer_location || 'N/A'}</small>
                                    </div>
                                    <div class="text-end">
                                        <div class="badge bg-${daysClass}">${contract.days_left} days</div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                }
                document.getElementById('expiringContractsList').innerHTML = html;
            }
        })
        .catch(error => console.error('Error loading expiring contracts:', error));
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

// Load Rental Type Analytics
function loadRentalTypeAnalytics() {
    fetch('<?= base_url('dashboard/rental-type-analytics') ?>')
        .then(response => response.json())
        .then(result => {
            // console.log('Rental Type Analytics:', result);
            if (result.success && result.data) {
                const data = result.data.breakdown;
                
                // Update chart
                if (rentalTypeChart && data.length > 0) {
                    // Map rental types to chart data
                    const rentalTypeMap = {
                        'CONTRACT': 0,
                        'PO_ONLY': 0,
                        'DAILY_SPOT': 0
                    };
                    
                    data.forEach(item => {
                        if (rentalTypeMap.hasOwnProperty(item.rental_type)) {
                            rentalTypeMap[item.rental_type] = parseInt(item.active_contracts) || 0;
                        }
                    });
                    
                    rentalTypeChart.data.datasets[0].data = [
                        rentalTypeMap['CONTRACT'],
                        rentalTypeMap['PO_ONLY'],
                        rentalTypeMap['DAILY_SPOT']
                    ];
                    rentalTypeChart.update();
                }
            }
        })
        .catch(error => console.error('Error loading rental type analytics:', error));
}

// Load Unlinked Deliveries Widget
async function loadUnlinkedDeliveriesWidget() {
    try {
        const response = await fetch('<?= base_url('customer-management/getUnlinkedDeliveriesWidget') ?>');
        if (!response.ok) throw new Error('Failed to load unlinked deliveries data');
        
        const result = await response.json();
        // console.log('Unlinked Deliveries Widget:', result);
        
        if (result.success && result.data) {
            const data = result.data;
            
            // Update widget display
            document.getElementById('urgentDeliveriesCount').textContent = data.urgent_count || 0;
            document.getElementById('totalUnlinkedCount').textContent = data.total_unlinked || 0;
            document.getElementById('oldestPendingDays').textContent = data.oldest_pending || 0;
            
            // Show widget only if there are urgent deliveries
            if (data.urgent_count > 0) {
                document.getElementById('unlinkedDeliveriesWidget').style.display = 'block';
            } else {
                document.getElementById('unlinkedDeliveriesWidget').style.display = 'none';
            }
        }
    } catch (error) {
        console.error('Error loading unlinked deliveries widget:', error);
        // Hide widget on error
        document.getElementById('unlinkedDeliveriesWidget').style.display = 'none';
    }
}
</script>
<?= $this->endSection() ?>
