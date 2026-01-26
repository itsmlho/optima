<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<style>
/* Dashboard Compact Styles */
.dashboard-header {
    background: white;
    padding: 1rem 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    margin-bottom: 1rem;
    border-left: 4px solid #0061f2;
}
.dashboard-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #212529;
    margin: 0;
}
.dashboard-subtitle {
    font-size: 0.875rem;
    color: #6c757d;
    margin: 0;
}

/* Summary Cards */
.summary-card {
    background: white;
    border-radius: 8px;
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    border-left: 4px solid #dee2e6;
    transition: transform 0.2s;
}
.summary-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.summary-icon {
    width: 50px;
    height: 50px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}
.summary-icon.bg-primary { background: linear-gradient(135deg, #0061f2, #4d8fff); }
.summary-icon.bg-success { background: linear-gradient(135deg, #28a745, #4cbb68); }
.summary-icon.bg-warning { background: linear-gradient(135deg, #ffc107, #ffd04a); }
.summary-icon.bg-info { background: linear-gradient(135deg, #17a2b8, #45b5c6); }

.summary-content {
    flex: 1;
}
.summary-label {
    font-size: 0.75rem;
    color: #6c757d;
    text-transform: uppercase;
    font-weight: 600;
    margin: 0 0 0.25rem 0;
}
.summary-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: #212529;
    margin: 0;
    line-height: 1;
}

/* Compact Cards */
.compact-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    overflow: hidden;
}
.compact-card-header {
    background: #f8f9fa;
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #dee2e6;
    display: flex;
    justify-content: between;
    align-items: center;
}
.compact-card-header h6 {
    margin: 0;
    font-size: 0.875rem;
    font-weight: 600;
    color: #212529;
}
.compact-card-body {
    padding: 1rem;
}

/* Stat Box */
.stat-box {
    background: #f8f9fa;
    border-radius: 6px;
    padding: 0.75rem;
}
.stat-box small {
    display: block;
    font-size: 0.7rem;
    color: #6c757d;
    margin-bottom: 0.25rem;
}
.stat-box h4 {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
}

.asset-stats small {
    font-size: 0.75rem;
}
.area-stat {
    font-size: 0.875rem;
}
</style>

<!-- Dashboard Container -->

    
<!-- Dashboard Header -->
<div class="dashboard-header">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="dashboard-title mb-1">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard OPTIMA
            </h1>
            <p class="dashboard-subtitle">
                Selamat datang, <strong><?= session()->get('first_name') ?></strong> | <span id="currentDateTime"></span>
            </p>
        </div>
        <div class="col-md-4 text-end">
            <button class="btn btn-sm btn-primary" onclick="location.reload()">
                <i class="fas fa-sync-alt me-1"></i>Refresh
            </button>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-3">
    <div class="col-lg-3 col-md-6">
        <div class="summary-card">
            <div class="summary-icon bg-primary">
                <i class="fas fa-boxes"></i>
            </div>
            <div class="summary-content">
                <h6 class="summary-label">Total Aset</h6>
                <h3 class="summary-value"><?= $summary['total_assets'] ?></h3>
                <small class="text-muted" style="font-size: 0.7rem;">Unit + Attachment + Charger + Baterai</small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="summary-card">
            <div class="summary-icon bg-success">
                <i class="fas fa-handshake"></i>
            </div>
            <div class="summary-content">
                <h6 class="summary-label">Kontrak Aktif</h6>
                <h3 class="summary-value"><?= $summary['active_contracts'] ?></h3>
                <small class="text-success"><i class="fas fa-arrow-up"></i> <?= $summary['contract_growth'] ?>% bulan ini</small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="summary-card">
            <div class="summary-icon bg-warning">
                <i class="fas fa-wrench"></i>
            </div>
            <div class="summary-content">
                <h6 class="summary-label">WO Bulan Ini</h6>
                <h3 class="summary-value"><?= $summary['wo_this_month'] ?></h3>
                <small class="text-muted" style="font-size: 0.75rem;"><?= $summary['wo_pending'] ?> pending | <?= $summary['wo_completed'] ?> selesai</small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="summary-card">
            <div class="summary-icon bg-info">
                <i class="fas fa-truck"></i>
            </div>
            <div class="summary-content">
                <h6 class="summary-label">SPK & DI Bulan Ini</h6>
                <h3 class="summary-value"><?= $summary['spk_di_this_month'] ?></h3>
                <small class="text-muted" style="font-size: 0.75rem;">SPK: <?= $summary['spk_count'] ?> | DI: <?= $summary['di_count'] ?></small>
            </div>
        </div>
    </div>
</div>

<!-- Main Content: 3-Column Layout -->
<div class="row g-3">
    
    <!-- LEFT COLUMN: Assets Overview -->
    <div class="col-lg-4">
        <!-- Units Status -->
        <div class="compact-card mb-3">
            <div class="compact-card-header">
                <h6><i class="fas fa-truck me-2 text-primary"></i>Inventory Unit</h6>
            </div>
            <div class="compact-card-body">
                <canvas id="unitChart" height="160"></canvas>
                <div class="row g-2 mt-2">
                    <div class="col-6">
                        <small class="text-muted d-block">Disewakan</small>
                        <strong class="text-success"><?= $assets['units']['rented'] ?></strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Tersedia</small>
                        <strong class="text-primary"><?= $assets['units']['available'] ?></strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Maintenance</small>
                        <strong class="text-warning"><?= $assets['units']['maintenance'] ?></strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Out of Service</small>
                        <strong class="text-danger"><?= $assets['units']['out_of_service'] ?></strong>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Attachment Status -->
        <div class="compact-card mb-3">
            <div class="compact-card-header">
                <h6><i class="fas fa-puzzle-piece me-2 text-warning"></i>Attachment</h6>
            </div>
            <div class="compact-card-body">
                <div class="row g-2">
                    <div class="col-6">
                        <div class="stat-box">
                            <small>Total</small>
                            <h4 class="mb-0"><?= $assets['attachments']['total'] ?></h4>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-box">
                            <small>Digunakan</small>
                            <h4 class="mb-0 text-success"><?= $assets['attachments']['active'] ?></h4>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: <?= $assets['attachments']['utilization'] ?>%"></div>
                        </div>
                        <small class="text-muted" style="font-size: 0.7rem;">Utilisasi: <?= $assets['attachments']['utilization'] ?>%</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Charger & Baterai -->
        <div class="compact-card">
            <div class="compact-card-header">
                <h6><i class="fas fa-battery-three-quarters me-2 text-success"></i>Charger & Baterai</h6>
            </div>
            <div class="compact-card-body">
                <div class="row g-2">
                    <div class="col-6">
                        <small class="text-muted d-block mb-2" style="font-weight: 600;">Charger</small>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span style="font-size: 0.875rem;">Total:</span>
                            <strong><?= $assets['chargers']['total'] ?></strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span style="font-size: 0.875rem;"><i class="fas fa-check-circle text-success"></i> Aktif:</span>
                            <strong class="text-success"><?= $assets['chargers']['active'] ?></strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block mb-2" style="font-weight: 600;">Baterai</small>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span style="font-size: 0.875rem;">Total:</span>
                            <strong><?= $assets['batteries']['total'] ?></strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span style="font-size: 0.875rem;"><i class="fas fa-check-circle text-success"></i> Aktif:</span>
                            <strong class="text-success"><?= $assets['batteries']['active'] ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- MIDDLE COLUMN: Work Orders & Service -->
    <div class="col-lg-4">
        <!-- WO Complaint by Category -->
        <div class="compact-card mb-3">
            <div class="compact-card-header">
                <h6><i class="fas fa-tools me-2 text-danger"></i>WO Complaint - Top Kategori</h6>
                <small class="text-muted" style="font-size: 0.7rem;">Bulan ini</small>
            </div>
            <div class="compact-card-body">
                <canvas id="woCategoryChart" height="140"></canvas>
                <div class="mt-2">
                    <?php $i = 1; foreach($workorders['by_category'] as $cat): ?>
                        <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                            <span style="font-size: 0.875rem;"><?= $i ?>. <?= $cat['category'] ?></span>
                            <strong class="text-danger"><?= $cat['count'] ?></strong>
                        </div>
                    <?php $i++; if($i > 5) break; endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- WO by Area -->
        <div class="compact-card mb-3">
            <div class="compact-card-header">
                <h6><i class="fas fa-map-marker-alt me-2 text-warning"></i>WO Complaint - Area</h6>
            </div>
            <div class="compact-card-body">
                <?php foreach($workorders['by_area'] as $area): ?>
                    <div class="mb-2">
                        <div class="d-flex justify-content-between mb-1">
                            <span style="font-size: 0.875rem;"><?= $area['area_name'] ?></span>
                            <strong><?= $area['count'] ?></strong>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-warning" style="width: <?= $area['percentage'] ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- PMPS Schedule -->
        <div class="compact-card">
            <div class="compact-card-header">
                <h6><i class="fas fa-calendar-check me-2 text-info"></i>PMPS Schedule</h6>
            </div>
            <div class="compact-card-body">
                <div class="row g-2 text-center">
                    <div class="col-4">
                        <div class="stat-box">
                            <small>Overdue</small>
                            <h4 class="mb-0 text-danger"><?= $pmps['overdue'] ?></h4>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="stat-box">
                            <small>7 Hari</small>
                            <h4 class="mb-0 text-warning"><?= $pmps['next_7_days'] ?></h4>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="stat-box">
                            <small>30 Hari</small>
                            <h4 class="mb-0 text-info"><?= $pmps['next_30_days'] ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- RIGHT COLUMN: SPK, DI, Customer -->
    <div class="col-lg-4">
        <!-- SPK This Month -->
        <div class="compact-card mb-3">
            <div class="compact-card-header">
                <h6><i class="fas fa-file-alt me-2 text-primary"></i>SPK Bulan Ini</h6>
            </div>
            <div class="compact-card-body">
                <canvas id="spkChart" height="140"></canvas>
                <div class="mt-2">
                    <small class="text-muted d-block mb-1" style="font-weight: 600;">Jenis Perintah Kerja:</small>
                    <?php foreach($spk['by_jenis_perintah'] as $jenis): ?>
                        <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                            <span style="font-size: 0.875rem;"><?= $jenis['jenis'] ?></span>
                            <strong><?= $jenis['count'] ?></strong>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- DI This Month -->
        <div class="compact-card mb-3">
            <div class="compact-card-header">
                <h6><i class="fas fa-shipping-fast me-2 text-success"></i>Delivery Instruction</h6>
            </div>
            <div class="compact-card-body">
                <div class="row g-2 mb-2">
                    <div class="col-4">
                        <div class="stat-box text-center">
                            <small>Total</small>
                            <h4 class="mb-0"><?= $di['total'] ?></h4>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="stat-box text-center">
                            <small>Pending</small>
                            <h4 class="mb-0 text-warning"><?= $di['pending'] ?></h4>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="stat-box text-center">
                            <small>Selesai</small>
                            <h4 class="mb-0 text-success"><?= $di['completed'] ?></h4>
                        </div>
                    </div>
                </div>
                <small class="text-muted d-block mb-1" style="font-weight: 600;">Top Lokasi:</small>
                <?php $i = 1; foreach($di['top_locations'] as $loc): ?>
                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                        <span style="font-size: 0.875rem;"><?= $i ?>. <?= $loc['location'] ?></span>
                        <strong><?= $loc['count'] ?></strong>
                    </div>
                <?php $i++; if($i > 4) break; endforeach; ?>
            </div>
        </div>
        
        <!-- Customer & Contract -->
        <div class="compact-card">
            <div class="compact-card-header">
                <h6><i class="fas fa-users me-2 text-info"></i>Customer & Contract</h6>
            </div>
            <div class="compact-card-body">
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <div class="stat-box">
                            <small>Total Customer</small>
                            <h4 class="mb-0"><?= $customers['total'] ?></h4>
                            <small class="text-success"><i class="fas fa-arrow-up"></i> <?= $customers['growth'] ?>%</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-box">
                            <small>Kontrak Aktif</small>
                            <h4 class="mb-0 text-success"><?= $customers['active_contracts'] ?></h4>
                        </div>
                    </div>
                </div>
                <div class="alert alert-warning py-2 px-2 mb-0" style="font-size: 0.75rem;">
                    <strong><i class="fas fa-exclamation-triangle me-1"></i>Alert:</strong>
                    <?= $customers['expiring_contracts'] ?> kontrak akan berakhir dalam 30 hari
                </div>
            </div>
        </div>
    </div>
    
</div>
    

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
// Real-time clock
function updateDateTime() {
    const now = new Date();
    const options = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    document.getElementById('currentDateTime').textContent = now.toLocaleDateString('id-ID', options);
}
updateDateTime();
setInterval(updateDateTime, 60000);

// Chart.js Configuration
Chart.defaults.font.family = 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI"';
Chart.defaults.font.size = 11;

// Chart 1: Unit Status
const unitCtx = document.getElementById('unitChart').getContext('2d');
new Chart(unitCtx, {
    type: 'doughnut',
    data: {
        labels: ['Disewakan', 'Tersedia', 'Maintenance', 'Out of Service'],
        datasets: [{
            data: [
                <?= $assets['units']['rented'] ?>,
                <?= $assets['units']['available'] ?>,
                <?= $assets['units']['maintenance'] ?>,
                <?= $assets['units']['out_of_service'] ?>
            ],
            backgroundColor: ['#28a745', '#0061f2', '#ffc107', '#dc3545'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 10,
                    usePointStyle: true,
                    font: { size: 10 }
                }
            }
        },
        cutout: '70%'
    }
});

// Chart 2: WO Category
const woCatCtx = document.getElementById('woCategoryChart').getContext('2d');
new Chart(woCatCtx, {
    type: 'bar',
    data: {
        labels: [<?php foreach($workorders['by_category'] as $cat): ?>'<?= $cat['category'] ?>',<?php endforeach; ?>],
        datasets: [{
            label: 'Jumlah WO',
            data: [<?php foreach($workorders['by_category'] as $cat): ?><?= $cat['count'] ?>,<?php endforeach; ?>],
            backgroundColor: '#dc3545'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});

// Chart 3: SPK Status
const spkCtx = document.getElementById('spkChart').getContext('2d');
new Chart(spkCtx, {
    type: 'pie',
    data: {
        labels: [<?php foreach($spk['by_status'] as $status): ?>'<?= $status['status'] ?>',<?php endforeach; ?>],
        datasets: [{
            data: [<?php foreach($spk['by_status'] as $status): ?><?= $status['count'] ?>,<?php endforeach; ?>],
            backgroundColor: ['#28a745', '#ffc107', '#0061f2', '#dc3545']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 8,
                    usePointStyle: true,
                    font: { size: 10 }
                }
            }
        }
    }
});

console.log('✅ OPTIMA Dashboard Compact loaded successfully');
</script>
<?= $this->endSection() ?>
