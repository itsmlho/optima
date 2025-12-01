<?= $this->extend('layouts/base_new') ?>

<?= $this->section('title') ?>Dashboard<?= $this->endSection() ?>

<?= $this->section('css') ?>
<style>
/* Dashboard specific styles */
.dashboard-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.dashboard-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.75rem 1.5rem rgba(18, 38, 63, 0.15);
}

.stat-card {
    border-left: 4px solid var(--bs-primary);
    background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
}

.stat-card.success {
    border-left-color: var(--bs-success);
}

.stat-card.warning {
    border-left-color: var(--bs-warning);
}

.stat-card.danger {
    border-left-color: var(--bs-danger);
}

.stat-card.info {
    border-left-color: var(--bs-info);
}

.metric-icon {
    font-size: 2rem;
    opacity: 0.8;
}

.chart-container {
    position: relative;
    height: 300px;
    margin-bottom: 1rem;
}

.recent-activities {
    max-height: 400px;
    overflow-y: auto;
}

.activity-item {
    padding: 0.75rem;
    border-left: 3px solid transparent;
    transition: all 0.2s ease;
}

.activity-item:hover {
    background-color: #f8f9fa;
    border-left-color: var(--bs-primary);
}

.activity-time {
    font-size: 0.8rem;
    color: #6c757d;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Dashboard Overview<?= $this->endSection() ?>

<?= $this->section('page_actions') ?>
<div class="d-flex gap-2">
    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#settingsModal">
        <i class="fas fa-cog me-1"></i> Settings
    </button>
    <button class="btn btn-sm btn-primary" onclick="refreshDashboard()">
        <i class="fas fa-sync-alt me-1"></i> Refresh
    </button>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Alert Example -->
<div class="alert alert-info alert-dismissible fade show" role="alert">
    <i class="fas fa-info-circle me-2"></i>
    <strong>Selamat datang!</strong> Anda sedang melihat layout baru OPTIMA v2.0 dengan komponen CSS yang telah diperbarui.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <!-- Total Units Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card dashboard-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Units
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">245</div>
                        <div class="text-xs text-success mt-1">
                            <i class="fas fa-arrow-up me-1"></i>12% dari bulan lalu
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-truck metric-icon text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Contracts Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card success dashboard-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Kontrak Aktif
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">189</div>
                        <div class="text-xs text-success mt-1">
                            <i class="fas fa-arrow-up me-1"></i>5% dari bulan lalu
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-file-contract metric-icon text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card warning dashboard-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Pendapatan (Bulan Ini)
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp 1.2M</div>
                        <div class="text-xs text-warning mt-1">
                            <i class="fas fa-minus me-1"></i>2% dari target
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign metric-icon text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Maintenance Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card danger dashboard-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Perlu Maintenance
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">12</div>
                        <div class="text-xs text-danger mt-1">
                            <i class="fas fa-exclamation-triangle me-1"></i>Perlu perhatian
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tools metric-icon text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Chart Section -->
    <div class="col-xl-8 col-lg-7">
        <!-- Revenue Chart -->
        <div class="card dashboard-card mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-line me-2"></i>Grafik Pendapatan
                </h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end shadow animated--fade-in">
                        <div class="dropdown-header">Opsi Grafik:</div>
                        <a class="dropdown-item" href="#">7 Hari Terakhir</a>
                        <a class="dropdown-item" href="#">30 Hari Terakhir</a>
                        <a class="dropdown-item active" href="#">3 Bulan Terakhir</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">Export Data</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
                <hr>
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Data pendapatan berdasarkan kontrak aktif dalam 3 bulan terakhir
                </small>
            </div>
        </div>

        <!-- Units Status Chart -->
        <div class="card dashboard-card mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-pie me-2"></i>Status Unit Forklift
                </h6>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="unitsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Section -->
    <div class="col-xl-4 col-lg-5">
        <!-- Recent Activities -->
        <div class="card dashboard-card mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-list me-2"></i>Aktivitas Terbaru
                </h6>
                <a href="<?= base_url('reports/activities') ?>" class="btn btn-sm btn-outline-primary">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body p-0">
                <div class="recent-activities">
                    <div class="activity-item">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-3">
                                <div class="avatar-img rounded-circle bg-success d-flex align-items-center justify-content-center">
                                    <i class="fas fa-plus text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="small fw-bold">Kontrak Baru Ditambahkan</div>
                                <div class="small text-muted">PT ABC - 5 unit forklift</div>
                                <div class="activity-time">2 jam yang lalu</div>
                            </div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-3">
                                <div class="avatar-img rounded-circle bg-warning d-flex align-items-center justify-content-center">
                                    <i class="fas fa-wrench text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="small fw-bold">Maintenance Scheduled</div>
                                <div class="small text-muted">Unit FL-001 dijadwalkan maintenance</div>
                                <div class="activity-time">4 jam yang lalu</div>
                            </div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-3">
                                <div class="avatar-img rounded-circle bg-info d-flex align-items-center justify-content-center">
                                    <i class="fas fa-money-bill text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="small fw-bold">Pembayaran Diterima</div>
                                <div class="small text-muted">PT XYZ - Rp 50.000.000</div>
                                <div class="activity-time">6 jam yang lalu</div>
                            </div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-3">
                                <div class="avatar-img rounded-circle bg-primary d-flex align-items-center justify-content-center">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="small fw-bold">Customer Baru</div>
                                <div class="small text-muted">PT DEF mendaftar sebagai customer</div>
                                <div class="activity-time">1 hari yang lalu</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card dashboard-card mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-bolt me-2"></i>Quick Actions
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary btn-sm" onclick="location.href='<?= base_url('marketing/contracts/create') ?>'">
                        <i class="fas fa-plus me-2"></i>Kontrak Baru
                    </button>
                    <button class="btn btn-outline-success btn-sm" onclick="location.href='<?= base_url('operational/units/create') ?>'">
                        <i class="fas fa-truck me-2"></i>Tambah Unit
                    </button>
                    <button class="btn btn-outline-warning btn-sm" onclick="location.href='<?= base_url('operational/maintenance') ?>'">
                        <i class="fas fa-tools me-2"></i>Schedule Maintenance
                    </button>
                    <button class="btn btn-outline-info btn-sm" onclick="location.href='<?= base_url('reports') ?>'">
                        <i class="fas fa-chart-bar me-2"></i>Lihat Laporan
                    </button>
                </div>
            </div>
        </div>

        <!-- System Status -->
        <div class="card dashboard-card">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-server me-2"></i>System Status
                </h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="small fw-bold">Database</span>
                    <span class="badge bg-success">Online</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="small fw-bold">API Services</span>
                    <span class="badge bg-success">Running</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="small fw-bold">Backup System</span>
                    <span class="badge bg-warning">Scheduled</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="small fw-bold">Last Update</span>
                    <span class="small text-muted">29 Nov 2024</span>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
            label: 'Pendapatan (Juta Rp)',
            data: [1.2, 1.4, 1.1, 1.6, 1.3, 1.5, 1.7, 1.4, 1.6, 1.8, 1.2, 1.9],
            backgroundColor: 'rgba(13, 110, 253, 0.1)',
            borderColor: 'rgba(13, 110, 253, 1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + value + 'M';
                    }
                }
            }
        }
    }
});

// Units Status Chart
const unitsCtx = document.getElementById('unitsChart').getContext('2d');
const unitsChart = new Chart(unitsCtx, {
    type: 'doughnut',
    data: {
        labels: ['Disewa', 'Tersedia', 'Maintenance', 'Rusak'],
        datasets: [{
            data: [189, 45, 8, 3],
            backgroundColor: [
                'rgba(25, 135, 84, 0.8)',
                'rgba(13, 110, 253, 0.8)',
                'rgba(255, 193, 7, 0.8)',
                'rgba(220, 53, 69, 0.8)'
            ],
            borderColor: [
                'rgba(25, 135, 84, 1)',
                'rgba(13, 110, 253, 1)',
                'rgba(255, 193, 7, 1)',
                'rgba(220, 53, 69, 1)'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Refresh Dashboard Function
function refreshDashboard() {
    // Add loading spinner
    const refreshBtn = event.target.closest('button');
    const originalContent = refreshBtn.innerHTML;
    refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Refreshing...';
    refreshBtn.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        refreshBtn.innerHTML = originalContent;
        refreshBtn.disabled = false;
        
        // Show success message
        showAlert('Dashboard berhasil diperbarui!', 'success');
    }, 2000);
}

// Auto-refresh every 5 minutes
setInterval(() => {
    console.log('Auto-refreshing dashboard data...');
    // Add your auto-refresh logic here
}, 300000);
</script>
<?= $this->endSection() ?>