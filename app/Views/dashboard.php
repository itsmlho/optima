<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<!-- Additional CSS for dashboard specific styling -->
<style>
    .stats-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.15) !important;
    }
    
    .stats-icon {
        font-size: 2.5rem;
        opacity: 0.8;
    }
    
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
    
    .quick-action-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border: 1px solid #e9ecef;
        border-radius: 0.75rem;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
        height: 100%;
    }
    
    .quick-action-card:hover {
        background: linear-gradient(135deg, #0061f2 0%, #4d8cff 100%);
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1rem rgba(0, 97, 242, 0.25);
    }
    
    .quick-action-icon {
        width: 60px;
        height: 60px;
        background: rgba(0, 97, 242, 0.1);
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }
    
    .quick-action-card:hover .quick-action-icon {
        background: rgba(255, 255, 255, 0.2);
        color: white;
    }
    
    .activity-item {
        display: flex;
        align-items: center;
        padding: 1rem;
        border-bottom: 1px solid #e9ecef;
        transition: background-color 0.3s ease;
    }
    
    .activity-item:hover {
        background-color: rgba(0, 97, 242, 0.05);
    }
    
    .activity-item:last-child {
        border-bottom: none;
    }
    
    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        font-size: 0.875rem;
    }
    
    .activity-icon.success {
        background: rgba(0, 172, 105, 0.1);
        color: #00ac69;
    }
    
    .activity-icon.warning {
        background: rgba(255, 182, 7, 0.1);
        color: #ffb607;
    }
    
    .activity-icon.danger {
        background: rgba(232, 21, 0, 0.1);
        color: #e81500;
    }
    
    .activity-icon.info {
        background: rgba(57, 175, 209, 0.1);
        color: #39afd1;
    }
    
    .notification-item {
        display: flex;
        align-items: flex-start;
        padding: 1rem;
        border-bottom: 1px solid #e9ecef;
        transition: background-color 0.3s ease;
    }
    
    .notification-item:hover {
        background-color: rgba(0, 97, 242, 0.05);
    }
    
    .notification-item:last-child {
        border-bottom: none;
    }
    
    .notification-icon {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.75rem;
        font-size: 0.75rem;
        flex-shrink: 0;
    }
    
    .progress-ring {
        transform: rotate(-90deg);
    }
    
    .progress-ring-circle {
        stroke-dasharray: 188.4;
        stroke-dashoffset: 188.4;
        transition: stroke-dashoffset 0.5s ease-in-out;
    }
    
    .maintenance-alert {
        background: linear-gradient(135deg, rgba(255, 182, 7, 0.1) 0%, rgba(255, 182, 7, 0.05) 100%);
        border-left: 4px solid #ffb607;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    
    .revenue-card {
        background: linear-gradient(135deg, #00ac69 0%, #4dd289 100%);
        color: white;
        border-radius: 1rem;
        padding: 2rem;
        position: relative;
        overflow: hidden;
    }
    
    .revenue-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 100%;
        height: 200%;
        background: rgba(255, 255, 255, 0.1);
        transform: rotate(15deg);
    }
    
    .calendar-widget {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        overflow: hidden;
    }
    
    .calendar-header {
        background: linear-gradient(135deg, #0061f2 0%, #4d8cff 100%);
        color: white;
        padding: 1rem;
        text-align: center;
        font-weight: 600;
    }
    
    .calendar-body {
        padding: 1rem;
    }
    
    .calendar-day {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.5rem;
        border-bottom: 1px solid #f1f3f4;
        font-size: 0.875rem;
    }
    
    .calendar-day:last-child {
        border-bottom: none;
    }
    
    .calendar-day.today {
        background: rgba(0, 97, 242, 0.1);
        font-weight: 600;
        color: #0061f2;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Dashboard Overview -->
<div class="row g-4 mb-4" aria-label="Statistik utama" role="region">
    <!-- Total Units -->
    <div class="col-xl-3 col-md-6">
        <div class="card stats-card bg-gradient-primary text-white h-100" onclick="location.href='<?= base_url('/units') ?>'" tabindex="0" role="button" aria-pressed="false" aria-label="Total Unit">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stats-value h2 mb-2 fw-bold" data-count="125">0</div>
                        <div class="stats-label text-uppercase">Total Unit</div>
                        <div class="small mt-1 opacity-75">
                            <i class="fas fa-arrow-up me-1" aria-hidden="true"></i>12% dari bulan lalu
                        </div>
                    </div>
                    <div class="stats-icon" aria-hidden="true">
                        <i class="fas fa-truck"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Active Rentals -->
    <div class="col-xl-3 col-md-6">
        <div class="card stats-card bg-gradient-success text-white h-100" onclick="location.href='<?= base_url('/rentals') ?>'">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stats-value h2 mb-2 fw-bold" data-count="87">0</div>
                        <div class="stats-label text-uppercase">Rental Aktif</div>
                        <div class="small mt-1 opacity-75">
                            <i class="fas fa-arrow-up me-1"></i>8% dari bulan lalu
                        </div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Monthly Revenue -->
    <div class="col-xl-3 col-md-6">
        <div class="card stats-card bg-gradient-warning text-white h-100" onclick="location.href='<?= base_url('/finance') ?>'">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stats-value h2 mb-2 fw-bold" data-currency="2750000000">Rp 0</div>
                        <div class="stats-label text-uppercase">Pendapatan Bulan Ini</div>
                        <div class="small mt-1 opacity-75">
                            <i class="fas fa-arrow-up me-1"></i>15% dari bulan lalu
                        </div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Maintenance Due -->
    <div class="col-xl-3 col-md-6">
        <div class="card stats-card bg-gradient-danger text-white h-100" onclick="location.href='<?= base_url('/maintenance') ?>'">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stats-value h2 mb-2 fw-bold" data-count="12">0</div>
                        <div class="stats-label text-uppercase">Perlu Maintenance</div>
                        <div class="small mt-1 opacity-75">
                            <i class="fas fa-clock me-1"></i>3 urgent, 9 terjadwal
                        </div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-wrench"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <!-- Revenue Chart -->
    <div class="col-xl-8">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-area me-2 text-primary"></i>
                    Tren Pendapatan
                </h5>
                <div class="btn-group" role="group">
                    <input type="radio" class="btn-check" name="revenueChart" id="revenue7d" autocomplete="off">
                    <label class="btn btn-outline-primary btn-sm" for="revenue7d">7H</label>
                    
                    <input type="radio" class="btn-check" name="revenueChart" id="revenue30d" autocomplete="off" checked>
                    <label class="btn btn-outline-primary btn-sm" for="revenue30d">30H</label>
                    
                    <input type="radio" class="btn-check" name="revenueChart" id="revenue12m" autocomplete="off">
                    <label class="btn btn-outline-primary btn-sm" for="revenue12m">12B</label>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="revenueChart" class="area-chart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Unit Status Chart -->
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-pie me-2 text-success"></i>
                    Status Unit
                </h5>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="unitStatusChart" class="doughnut-chart"></canvas>
                </div>
                <div class="mt-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center">
                            <div class="bg-success rounded-circle me-2" style="width: 12px; height: 12px;"></div>
                            <span class="small">Disewakan</span>
                        </div>
                        <span class="small fw-semibold">87 unit</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning rounded-circle me-2" style="width: 12px; height: 12px;"></div>
                            <span class="small">Tersedia</span>
                        </div>
                        <span class="small fw-semibold">28 unit</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center">
                            <div class="bg-danger rounded-circle me-2" style="width: 12px; height: 12px;"></div>
                            <span class="small">Maintenance</span>
                        </div>
                        <span class="small fw-semibold">10 unit</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions & Activity -->
<div class="row g-4 mb-4">
    <!-- Quick Actions -->
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt me-2 text-warning"></i>
                    Aksi Cepat
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="quick-action-card" onclick="location.href='<?= base_url('/rentals/new') ?>'">
                            <div class="quick-action-icon">
                                <i class="fas fa-plus text-primary"></i>
                            </div>
                            <div class="fw-semibold">Rental Baru</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="quick-action-card" onclick="location.href='<?= base_url('/units/add') ?>'">
                            <div class="quick-action-icon">
                                <i class="fas fa-truck text-success"></i>
                            </div>
                            <div class="fw-semibold">Tambah Unit</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="quick-action-card" onclick="location.href='<?= base_url('/customers/add') ?>'">
                            <div class="quick-action-icon">
                                <i class="fas fa-user-plus text-info"></i>
                            </div>
                            <div class="fw-semibold">Customer Baru</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="quick-action-card" onclick="location.href='<?= base_url('/maintenance/schedule') ?>'">
                            <div class="quick-action-icon">
                                <i class="fas fa-calendar text-warning"></i>
                            </div>
                            <div class="fw-semibold">Jadwal Service</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history me-2 text-info"></i>
                    Aktivitas Terbaru
                </h5>
                <a href="<?= base_url('/activity') ?>" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="activity-item" role="listitem">
                    <div class="activity-icon success" aria-hidden="true">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">Rental FL-125 dimulai</div>
                        <div class="small text-muted">PT Sinar Jaya - 2 jam yang lalu</div>
                    </div>
                    <div class="badge bg-success" role="status" aria-label="Aktif">
                        <i class="fas fa-check-circle me-1" aria-hidden="true"></i>Aktif
                    </div>
                </div>
                
                <div class="activity-item" role="listitem">
                    <div class="activity-icon warning">
                        <i class="fas fa-wrench"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">Maintenance FL-087 selesai</div>
                        <div class="small text-muted">Teknisi: Ahmad Rifai - 4 jam yang lalu</div>
                    </div>
                    <div class="badge bg-warning">Selesai</div>
                </div>
                
                <div class="activity-item" role="listitem">
                    <div class="activity-icon info">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">Invoice #INV-2024-001234</div>
                        <div class="small text-muted">PT Mandiri Logistik - 6 jam yang lalu</div>
                    </div>
                    <div class="badge bg-info">Terkirim</div>
                </div>
                
                <div class="activity-item" role="listitem">
                    <div class="activity-icon danger">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">Unit FL-045 butuh maintenance</div>
                        <div class="small text-muted">Alert otomatis - 8 jam yang lalu</div>
                    </div>
                    <div class="badge bg-danger">Urgent</div>
                </div>
                
                <div class="activity-item" role="listitem">
                    <div class="activity-icon success">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">Customer baru terdaftar</div>
                        <div class="small text-muted">CV Sejahtera Bersama - 1 hari yang lalu</div>
                    </div>
                    <div class="badge bg-success">Baru</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Performance & Notifications -->
<div class="row g-4 mb-4">
    <!-- Performance Metrics -->
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-tachometer-alt me-2 text-primary"></i>
                    Metrik Performa
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <!-- Utilization Rate -->
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="mb-3">
                                <svg class="progress-ring" width="100" height="100">
                                    <circle class="progress-ring-circle" stroke="#0061f2" stroke-width="8" fill="transparent" r="30" cx="50" cy="50" style="stroke-dashoffset: 47.1;"></circle>
                                </svg>
                                <div class="position-absolute" style="top: 50%; left: 50%; transform: translate(-50%, -50%);">
                                    <div class="h4 mb-0 fw-bold text-primary">75%</div>
                                </div>
                            </div>
                            <div class="fw-semibold">Tingkat Utilisasi</div>
                            <div class="small text-muted">Target: 80%</div>
                        </div>
                    </div>
                    
                    <!-- Customer Satisfaction -->
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="mb-3">
                                <svg class="progress-ring" width="100" height="100">
                                    <circle class="progress-ring-circle" stroke="#00ac69" stroke-width="8" fill="transparent" r="30" cx="50" cy="50" style="stroke-dashoffset: 37.7;"></circle>
                                </svg>
                                <div class="position-absolute" style="top: 50%; left: 50%; transform: translate(-50%, -50%);">
                                    <div class="h4 mb-0 fw-bold text-success">80%</div>
                                </div>
                            </div>
                            <div class="fw-semibold">Kepuasan Customer</div>
                            <div class="small text-muted">Rata-rata rating: 4.2/5</div>
                        </div>
                    </div>
                    
                    <!-- On-time Delivery -->
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="mb-3">
                                <svg class="progress-ring" width="100" height="100">
                                    <circle class="progress-ring-circle" stroke="#ffb607" stroke-width="8" fill="transparent" r="30" cx="50" cy="50" style="stroke-dashoffset: 18.8;"></circle>
                                </svg>
                                <div class="position-absolute" style="top: 50%; left: 50%; transform: translate(-50%, -50%);">
                                    <div class="h4 mb-0 fw-bold text-warning">90%</div>
                                </div>
                            </div>
                            <div class="fw-semibold">Ketepatan Waktu</div>
                            <div class="small text-muted">Delivery & pickup</div>
                        </div>
                    </div>
                </div>
                
                <!-- Additional Metrics -->
                <div class="row g-3 mt-2">
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 rounded p-2 me-3">
                                <i class="fas fa-clock text-primary"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">4.2 hari</div>
                                <div class="small text-muted">Rata-rata durasi rental</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-success bg-opacity-10 rounded p-2 me-3">
                                <i class="fas fa-redo text-success"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">65%</div>
                                <div class="small text-muted">Customer berulang</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning bg-opacity-10 rounded p-2 me-3">
                                <i class="fas fa-tools text-warning"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">2.1%</div>
                                <div class="small text-muted">Downtime rate</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-info bg-opacity-10 rounded p-2 me-3">
                                <i class="fas fa-dollar-sign text-info"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">Rp 2.5M</div>
                                <div class="small text-muted">Revenue per unit</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Notifications -->
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bell me-2 text-warning"></i>
                    Notifikasi
                </h5>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Filter
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Semua</a></li>
                        <li><a class="dropdown-item" href="#">Urgent</a></li>
                        <li><a class="dropdown-item" href="#">Maintenance</a></li>
                        <li><a class="dropdown-item" href="#">Pembayaran</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;" role="list" aria-label="Daftar notifikasi">
                <div class="notification-item" role="listitem">
                    <div class="notification-icon bg-danger text-white" aria-hidden="true">
                        <i class="fas fa-exclamation"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold small">Unit FL-045 Maintenance Urgent</div>
                        <div class="text-muted small">Engine overheat detected. Perlu segera diperiksa.</div>
                        <div class="text-muted small">2 jam yang lalu</div>
                    </div>
                </div>
                
                <div class="notification-item" role="listitem">
                    <div class="notification-icon bg-warning text-white">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold small">Maintenance Terjadwal Besok</div>
                        <div class="text-muted small">5 unit memerlukan service rutin.</div>
                        <div class="text-muted small">5 jam yang lalu</div>
                    </div>
                </div>
                
                <div class="notification-item" role="listitem">
                    <div class="notification-icon bg-info text-white">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold small">Invoice Overdue</div>
                        <div class="text-muted small">PT Mandiri Logistik - INV-001234</div>
                        <div class="text-muted small">1 hari yang lalu</div>
                    </div>
                </div>
                
                <div class="notification-item" role="listitem">
                    <div class="notification-icon bg-success text-white">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold small">Kontrak Baru Ditandatangani</div>
                        <div class="text-muted small">CV Sejahtera - 12 bulan kontrak</div>
                        <div class="text-muted small">2 hari yang lalu</div>
                    </div>
                </div>
                
                <div class="notification-item" role="listitem">
                    <div class="notification-icon bg-primary text-white">
                        <i class="fas fa-truck"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold small">Unit Baru Tersedia</div>
                        <div class="text-muted small">FL-126 telah siap untuk disewakan.</div>
                        <div class="text-muted small">3 hari yang lalu</div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-center">
                <a href="<?= base_url('/notifications') ?>" class="btn btn-sm btn-outline-primary">Lihat Semua Notifikasi</a>
            </div>
        </div>
    </div>
</div>

<!-- Maintenance Alert -->
<div class="maintenance-alert" role="alert" aria-live="polite">
    <div class="d-flex align-items-center">
        <div class="me-3">
            <i class="fas fa-exclamation-triangle fa-2x text-warning" aria-hidden="true"></i>
        </div>
        <div class="flex-grow-1">
            <h6 class="mb-1">Peringatan Maintenance</h6>
            <p class="mb-2">Ada 12 unit yang memerlukan maintenance. 3 diantaranya bersifat urgent dan perlu segera ditangani.</p>
            <a href="<?= base_url('/maintenance') ?>" class="btn btn-warning btn-sm" role="button" aria-label="Lihat detail maintenance">
                <i class="fas fa-wrench me-1" aria-hidden="true"></i>Lihat Detail
            </a>
        </div>
    </div>
</div>

<!-- Footer Statistics -->
<div class="row g-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="h3 mb-2 text-primary" data-count="1247">0</div>
                <div class="text-muted">Total Rental Selesai</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="h3 mb-2 text-success" data-count="98">0</div>
                <div class="text-muted">Customer Aktif</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="h3 mb-2 text-warning">98.5%</div>
                <div class="text-muted">Uptime Rate</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="h3 mb-2 text-info" data-currency="32500000000">Rp 0</div>
                <div class="text-muted">Total Revenue YTD</div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize dashboard
    initializeDashboard();
});

function initializeDashboard() {
    // Initialize counters
    initializeCounters();
    
    // Initialize charts
    initializeRevenueChart();
    initializeUnitStatusChart();
    
    // Initialize real-time updates
    initializeRealTimeUpdates();
    
    // Initialize chart period filters
    initializeChartFilters();
}

// Counter animations
function initializeCounters() {
    const counters = document.querySelectorAll('[data-count]');
    const currencyCounters = document.querySelectorAll('[data-currency]');
    
    counters.forEach(counter => {
        animateCounter(counter, parseInt(counter.dataset.count));
    });
    
    currencyCounters.forEach(counter => {
        animateCurrencyCounter(counter, parseInt(counter.dataset.currency));
    });
}

function animateCounter(element, target) {
    let current = 0;
    const increment = target / 100;
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            element.textContent = target;
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current);
        }
    }, 20);
}

function animateCurrencyCounter(element, target) {
    let current = 0;
    const increment = target / 100;
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            element.textContent = formatCurrency(target);
            clearInterval(timer);
        } else {
            element.textContent = formatCurrency(Math.floor(current));
        }
    }, 20);
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount);
}

// Revenue Chart
function initializeRevenueChart() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    
    // Destroy existing chart if it exists
    if (window.revenueChartInstance) {
        window.revenueChartInstance.destroy();
    }
    
    window.revenueChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Pendapatan (Miliar Rp)',
                data: [2.1, 2.3, 2.8, 2.4, 2.7, 3.1, 2.9, 3.2, 2.8, 3.0, 2.9, 2.7],
                borderColor: '#0061f2',
                backgroundColor: 'rgba(0, 97, 242, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#0061f2',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 3,
                pointRadius: 6,
                pointHoverRadius: 8
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
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#0061f2',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return 'Pendapatan: Rp ' + context.parsed.y + ' Miliar';
                        }
                    }
                }
            },
            scales: {
                x: {
                    display: true,
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#69707a'
                    }
                },
                y: {
                    display: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        color: '#69707a',
                        callback: function(value) {
                            return 'Rp ' + value + 'M';
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
    
    // Store chart instance for updates
    window.revenueChart = revenueChart;
}

// Unit Status Chart
function initializeUnitStatusChart() {
    const ctx = document.getElementById('unitStatusChart').getContext('2d');
    
    // Destroy existing chart if it exists
    if (window.unitStatusChartInstance) {
        window.unitStatusChartInstance.destroy();
    }
    
    window.unitStatusChartInstance = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Disewakan', 'Tersedia', 'Maintenance'],
            datasets: [{
                data: [87, 28, 10],
                backgroundColor: [
                    '#00ac69',
                    '#ffb607',
                    '#e81500'
                ],
                borderWidth: 3,
                borderColor: '#ffffff',
                hoverBorderWidth: 5
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
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#0061f2',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((context.parsed / total) * 100);
                            return context.label + ': ' + context.parsed + ' unit (' + percentage + '%)';
                        }
                    }
                }
            },
            cutout: '70%'
        }
    });
    
    // Store chart instance for updates
    window.unitStatusChart = unitStatusChart;
}

// Chart period filters
function initializeChartFilters() {
    const revenueFilters = document.querySelectorAll('input[name="revenueChart"]');
    
    revenueFilters.forEach(filter => {
        filter.addEventListener('change', function() {
            updateRevenueChart(this.id);
        });
    });
}

function updateRevenueChart(period) {
    if (!window.revenueChart) return;
    
    let newData, newLabels;
    
    switch(period) {
        case 'revenue7d':
            newLabels = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
            newData = [150, 180, 165, 190, 175, 155, 140];
            break;
        case 'revenue30d':
            newLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            newData = [2.1, 2.3, 2.8, 2.4, 2.7, 3.1, 2.9, 3.2, 2.8, 3.0, 2.9, 2.7];
            break;
        case 'revenue12m':
            newLabels = ['2022', '2023', '2024'];
            newData = [28.5, 31.2, 34.8];
            break;
    }
    
    window.revenueChart.data.labels = newLabels;
    window.revenueChart.data.datasets[0].data = newData;
    window.revenueChart.update('active');
}

// Real-time updates
function initializeRealTimeUpdates() {
    // Update every 3 minutes to reduce server load
    setInterval(() => {
        fetchDashboardData();
    }, 180000);
}

function fetchDashboardData() {
    // Simulate API call to fetch real-time data
    fetch('/api/dashboard/realtime')
        .then(response => response.json())
        .then(data => {
            updateDashboardData(data);
        })
        .catch(error => {
            console.log('Real-time update temporarily unavailable');
        });
}

function updateDashboardData(data) {
    // Update counters
    if (data.totalUnits) {
        const element = document.querySelector('[data-count="125"]');
        if (element) {
            element.dataset.count = data.totalUnits;
            animateCounter(element, data.totalUnits);
        }
    }
    
    // Update charts if needed
    if (data.unitStatus && window.unitStatusChart) {
        window.unitStatusChart.data.datasets[0].data = data.unitStatus;
        window.unitStatusChart.update();
    }
}

// Notification handling
function markNotificationAsRead(notificationId) {
    fetch('/api/notifications/' + notificationId + '/read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update notification count
            updateNotificationCount();
        }
    });
}

function updateNotificationCount() {
    fetch('/api/notifications/count')
        .then(response => response.json())
        .then(data => {
            const countElement = document.querySelector('.notification-count');
            if (countElement) {
                countElement.textContent = data.count;
            }
        });
}

// Export functions for external use
window.dashboardFunctions = {
    refreshCharts: function() {
        if (window.revenueChart) window.revenueChart.update();
        if (window.unitStatusChart) window.unitStatusChart.update();
    },
    updateData: updateDashboardData,
    markNotificationAsRead: markNotificationAsRead
};
</script>
<?= $this->endSection() ?>