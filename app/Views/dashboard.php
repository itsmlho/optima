<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<!-- CSS umum sudah ada di optima-pro.css (stats-card, quick-action-card, activity-item, dll) -->
<style>
    /* Custom dashboard widgets */
    .stats-icon {
        font-size: 2.5rem;
        opacity: 0.8;
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
    
    /* OPTIMA Theme - Centralized CSS */
    
    /* Color Palette */
    :root {
        --optima-primary: #0061f2;
        --optima-primary-light: #4d7cff;
        --optima-primary-dark: #0041a3;
        --optima-success: #00ac69;
        --optima-success-light: #4dd289;
        --optima-success-dark: #007a4d;
        --optima-info: #17a2b8;
        --optima-info-light: #5bc0de;
        --optima-info-dark: #117a8b;
        --optima-warning: #ffb607;
        --optima-warning-light: #ffc947;
        --optima-warning-dark: #cc9205;
        --optima-danger: #e81500;
        --optima-danger-light: #ff4d4d;
        --optima-danger-dark: #b30e00;
        --optima-light: #f8f9fa;
        --optima-dark: #343a40;
        --optima-gray: #6c757d;
        --optima-gray-light: #adb5bd;
        --optima-gray-dark: #495057;
    }
    
    /* Base Card Styles */
    .card {
        border-radius: 12px;
        border: 1px solid rgba(0, 0, 0, 0.05);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: #ffffff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        border-color: rgba(0, 97, 242, 0.1);
    }
    
    /* Stats Card Styles */
    .stats-card {
        border-radius: 16px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        border: none;
    }
    
    .stats-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
        pointer-events: none;
    }
    
    .stats-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
    }
    
    .stats-icon {
        font-size: 2.5rem;
        opacity: 0.9;
        transition: all 0.3s ease;
    }
    
    .stats-card:hover .stats-icon {
        transform: scale(1.1);
        opacity: 1;
    }
    
    /* Gradient Backgrounds */
    .bg-gradient-primary {
        background: linear-gradient(135deg, var(--optima-primary) 0%, var(--optima-primary-dark) 100%);
    }
    
    .bg-gradient-success {
        background: linear-gradient(135deg, var(--optima-success) 0%, var(--optima-success-dark) 100%);
    }
    
    .bg-gradient-info {
        background: linear-gradient(135deg, var(--optima-info) 0%, var(--optima-info-dark) 100%);
    }
    
    .bg-gradient-warning {
        background: linear-gradient(135deg, var(--optima-warning) 0%, var(--optima-warning-dark) 100%);
    }
    
    .bg-gradient-danger {
        background: linear-gradient(135deg, var(--optima-danger) 0%, var(--optima-danger-dark) 100%);
    }
    
    /* Division Cards */
    .division-card {
        border-radius: 12px;
        border: 1px solid rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        background: #ffffff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }
    
    .division-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        border-color: rgba(0, 97, 242, 0.1);
    }
    
    .division-card .card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 12px 12px 0 0;
        padding: 1.25rem 1.5rem;
    }
    
    .division-card .card-body {
        padding: 1.5rem;
    }
    
    /* Metric Cards */
    .metric-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 8px;
        padding: 1rem;
        transition: all 0.3s ease;
        text-align: center;
    }
    
    .metric-card:hover {
        background: rgba(0, 97, 242, 0.02);
        border-color: rgba(0, 97, 242, 0.1);
        transform: translateY(-1px);
    }
    
    .metric-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--optima-primary);
        margin-bottom: 0.25rem;
    }
    
    .metric-label {
        font-size: 0.875rem;
        color: var(--optima-gray);
        font-weight: 500;
    }
    
    /* Progress Bars */
    .progress {
        border-radius: 8px;
        overflow: hidden;
        background: rgba(0, 0, 0, 0.05);
        height: 8px;
    }
    
    .progress-bar {
        border-radius: 8px;
        transition: width 0.6s ease;
    }
    
    /* Badge Styles */
    .badge {
        border-radius: 6px;
        font-weight: 600;
        padding: 0.375rem 0.75rem;
        font-size: 0.75rem;
    }
    
    /* Button Styles */
    .btn {
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        padding: 0.5rem 1rem;
    }
    
    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    /* Chart Containers */
    .chart-container {
        position: relative;
        height: 200px;
        background: #ffffff;
        border-radius: 8px;
        padding: 1rem;
    }
    
    /* Activity Items */
    .activity-item {
        transition: all 0.3s ease;
        border-radius: 8px;
        padding: 0.75rem;
        border: 1px solid rgba(0, 0, 0, 0.05);
        background: #ffffff;
    }
    
    .activity-item:hover {
        background: rgba(0, 97, 242, 0.02);
        border-color: rgba(0, 97, 242, 0.1);
        transform: translateX(4px);
    }
    
    /* Quick Action Cards */
    .quick-action-card {
        transition: all 0.3s ease;
        border-radius: 12px;
        cursor: pointer;
        padding: 1.5rem;
        border: 1px solid rgba(0, 0, 0, 0.05);
        background: #ffffff;
    }
    
    .quick-action-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        border-color: rgba(0, 97, 242, 0.1);
    }
    
    /* Notification Items */
    .notification-item {
        border-radius: 8px;
        transition: all 0.3s ease;
        padding: 0.75rem;
        border: 1px solid rgba(0, 0, 0, 0.05);
        background: #ffffff;
    }
    
    .notification-item:hover {
        background: rgba(0, 97, 242, 0.02);
        border-color: rgba(0, 97, 242, 0.1);
        transform: translateX(4px);
    }
    
    /* Maintenance Alert */
    .maintenance-alert {
        border-radius: 12px;
        border-left: 4px solid var(--optima-warning);
        background: linear-gradient(135deg, rgba(255, 182, 7, 0.1) 0%, rgba(255, 182, 7, 0.05) 100%);
        padding: 1.5rem;
        margin: 1rem 0;
    }
    
    /* Section Spacing */
    .row.g-4 > * {
        margin-bottom: 1.5rem;
    }
    
    /* Typography */
    .fw-bold {
        font-weight: 700 !important;
    }
    
    .text-muted {
        color: var(--optima-gray) !important;
    }
    
    /* Icon Enhancements */
    .fas, .far, .fab {
        transition: all 0.3s ease;
    }
    
    .card:hover .fas,
    .card:hover .far,
    .card:hover .fab {
        transform: scale(1.05);
    }
    
    /* Division Specific Colors */
    .division-operational {
        border-left: 4px solid var(--optima-primary);
    }
    
    .division-warehouse {
        border-left: 4px solid var(--optima-success);
    }
    
    .division-maintenance {
        border-left: 4px solid var(--optima-warning);
    }
    
    .division-delivery {
        border-left: 4px solid var(--optima-info);
    }
    
    .division-purchase {
        border-left: 4px solid var(--optima-danger);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Executive Summary - Director Dashboard -->
<div class="row g-4 mb-4" aria-label="Executive Summary" role="region">
    <!-- Total Units -->
    <div class="col-xl-3 col-md-6">
        <div class="card stats-card bg-gradient-primary text-white h-100 shadow-lg border-0" onclick="location.href='<?= base_url('/units') ?>'" tabindex="0" role="button" aria-pressed="false" aria-label="Total Unit">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stats-value h2 mb-2 fw-bold" data-count="<?= $director_metrics['total_units'] ?>">0</div>
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
    
    <!-- Active Contracts -->
    <div class="col-xl-3 col-md-6">
        <div class="card stats-card bg-gradient-success text-white h-100 shadow-lg border-0" onclick="location.href='<?= base_url('/kontrak') ?>'">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stats-value h2 mb-2 fw-bold" data-count="<?= $director_metrics['active_contracts'] ?>">0</div>
                        <div class="stats-label text-uppercase">Kontrak Aktif</div>
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
    
    <!-- Total Customers -->
    <div class="col-xl-3 col-md-6">
        <div class="card stats-card bg-gradient-info text-white h-100 shadow-lg border-0" onclick="location.href='<?= base_url('/customers') ?>'">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stats-value h2 mb-2 fw-bold" data-count="<?= $director_metrics['total_customers'] ?>">0</div>
                        <div class="stats-label text-uppercase">Total Customer</div>
                        <div class="small mt-1 opacity-75">
                            <i class="fas fa-arrow-up me-1"></i>15% dari bulan lalu
                        </div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Utilization Rate -->
    <div class="col-xl-3 col-md-6">
        <div class="card stats-card bg-gradient-warning text-white h-100 shadow-lg border-0" onclick="location.href='<?= base_url('/analytics') ?>'">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stats-value h2 mb-2 fw-bold"><?= $director_metrics['utilization_rate'] ?>%</div>
                        <div class="stats-label text-uppercase">Utilisasi</div>
                        <div class="small mt-1 opacity-75">
                            <i class="fas fa-chart-line me-1"></i>Target: 80%
                        </div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Director Dashboard - Main Content -->
<div class="row g-4 mb-4">
    <!-- Operational Overview -->
    <div class="col-12">
        <div class="card division-card division-operational h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                    <i class="fas fa-tachometer-alt me-2" style="color: var(--optima-primary);"></i>
                    Overview Operasional
                </h5>
                <div class="btn-group" role="group">
                    <input type="radio" class="btn-check" name="operationalChart" id="operationalUnits" autocomplete="off" checked>
                    <label class="btn btn-outline-primary btn-sm" for="operationalUnits">Unit Status</label>
                    
                    <input type="radio" class="btn-check" name="operationalChart" id="operationalComplaints" autocomplete="off">
                    <label class="btn btn-outline-primary btn-sm" for="operationalComplaints">Service Complaints</label>
                </div>
            </div>
            <div class="card-body">
                <!-- Unit Status Visualization -->
                <div id="unitStatusSection">
                    <!-- Detailed Unit Metrics -->
                    <div class="row g-3 mb-4">
                        <div class="col-lg-3 col-md-6">
                            <div class="metric-card bg-gradient-success">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-truck-loading text-white" style="font-size: 2rem;"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="metric-value text-white"><?= $operational_overview['unit_status'][0]['count'] ?? 87 ?></div>
                                        <div class="metric-label text-white-50">Unit Disewakan</div>
                                        <div class="small text-white-50">Aktif dalam kontrak</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="metric-card bg-gradient-warning">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-parking text-white" style="font-size: 2rem;"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="metric-value text-white"><?= $operational_overview['unit_status'][1]['count'] ?? 28 ?></div>
                                        <div class="metric-label text-white-50">Unit Tersedia</div>
                                        <div class="small text-white-50">Siap untuk disewa</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="metric-card bg-gradient-danger">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-tools text-white" style="font-size: 2rem;"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="metric-value text-white"><?= $operational_overview['unit_status'][2]['count'] ?? 10 ?></div>
                                        <div class="metric-label text-white-50">Dalam Maintenance</div>
                                        <div class="small text-white-50">Sedang diperbaiki</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="metric-card bg-gradient-info">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-chart-line text-white" style="font-size: 2rem;"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="metric-value text-white"><?= $director_metrics['utilization_rate'] ?>%</div>
                                        <div class="metric-label text-white-50">Tingkat Utilisasi</div>
                                        <div class="small text-white-50">Efisiensi operasional</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Operational Chart -->
                    <div class="chart-container mb-4">
                        <canvas id="operationalChart" style="height: 200px;"></canvas>
                    </div>
                    
                    <!-- Performance Indicators -->
                    <div class="row g-3">
                        <div class="col-lg-3 col-md-6">
                            <div class="metric-card">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="h4 mb-1 text-success"><?= $director_metrics['customer_satisfaction'] ?>/5</div>
                                        <div class="small text-muted">Customer Satisfaction</div>
                                        <div class="progress mt-2" style="height: 6px;">
                                            <div class="progress-bar bg-success" style="width: <?= ($director_metrics['customer_satisfaction']/5)*100 ?>%"></div>
                                        </div>
                                    </div>
                                    <i class="fas fa-star text-success" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="metric-card">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="h4 mb-1 text-warning"><?= $director_metrics['on_time_delivery'] ?>%</div>
                                        <div class="small text-muted">On-Time Delivery</div>
                                        <div class="progress mt-2" style="height: 6px;">
                                            <div class="progress-bar bg-warning" style="width: <?= $director_metrics['on_time_delivery'] ?>%"></div>
                                        </div>
                                    </div>
                                    <i class="fas fa-clock text-warning" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="metric-card">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="h4 mb-1 text-danger"><?= $director_metrics['downtime_rate'] ?>%</div>
                                        <div class="small text-muted">Downtime Rate</div>
                                        <div class="progress mt-2" style="height: 6px;">
                                            <div class="progress-bar bg-danger" style="width: <?= $director_metrics['downtime_rate'] ?>%"></div>
                                        </div>
                                    </div>
                                    <i class="fas fa-exclamation-triangle text-danger" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="metric-card">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="h4 mb-1 text-info"><?= $director_metrics['total_customers'] ?></div>
                                        <div class="small text-muted">Total Customers</div>
                                        <div class="small text-success">+5 this month</div>
                                    </div>
                                    <i class="fas fa-users text-info" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Service Complaints Section (Hidden by default) -->
                <div id="workOrdersSection" style="display: none;">
                    <!-- Complaint Overview Cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-lg-3 col-md-6">
                            <div class="metric-card bg-gradient-danger">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-exclamation-triangle text-white" style="font-size: 2rem;"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="metric-value text-white">12</div>
                                        <div class="metric-label text-white-50">Critical Issues</div>
                                        <div class="small text-white-50">Unit breakdown</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="metric-card bg-gradient-warning">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-tools text-white" style="font-size: 2rem;"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="metric-value text-white">28</div>
                                        <div class="metric-label text-white-50">Maintenance</div>
                                        <div class="small text-white-50">Scheduled repairs</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="metric-card bg-gradient-info">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-user-times text-white" style="font-size: 2rem;"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="metric-value text-white">8</div>
                                        <div class="metric-label text-white-50">Customer Complaints</div>
                                        <div class="small text-white-50">Service issues</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="metric-card bg-gradient-success">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-check-circle text-white" style="font-size: 2rem;"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="metric-value text-white">85%</div>
                                        <div class="metric-label text-white-50">Resolution Rate</div>
                                        <div class="small text-white-50">Issues resolved</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Complaint Categories -->
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <h6 class="mb-3">
                                <i class="fas fa-list-alt me-2 text-primary"></i>
                                Complaint Categories
                            </h6>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <div class="metric-card">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <div class="h5 mb-1 text-danger">12</div>
                                                <div class="small text-muted">Mechanical Issues</div>
                                                <div class="small text-danger">Engine, Hydraulic</div>
                                            </div>
                                            <i class="fas fa-cog text-danger"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="metric-card">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <div class="h5 mb-1 text-warning">8</div>
                                                <div class="small text-muted">Electrical Issues</div>
                                                <div class="small text-warning">Battery, Wiring</div>
                                            </div>
                                            <i class="fas fa-bolt text-warning"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="metric-card">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <div class="h5 mb-1 text-info">6</div>
                                                <div class="small text-muted">Safety Issues</div>
                                                <div class="small text-info">Brakes, Lights</div>
                                            </div>
                                            <i class="fas fa-shield-alt text-info"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Service Performance Metrics -->
                    <div class="row g-3 mb-4">
                        <div class="col-lg-4">
                            <div class="metric-card">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="h4 mb-1 text-success">2.3 days</div>
                                        <div class="small text-muted">Avg. Resolution Time</div>
                                        <div class="progress mt-2" style="height: 6px;">
                                            <div class="progress-bar bg-success" style="width: 85%"></div>
                                        </div>
                                    </div>
                                    <i class="fas fa-clock text-success" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="metric-card">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="h4 mb-1 text-warning">15%</div>
                                        <div class="small text-muted">Repeat Issues</div>
                                        <div class="progress mt-2" style="height: 6px;">
                                            <div class="progress-bar bg-warning" style="width: 15%"></div>
                                        </div>
                                    </div>
                                    <i class="fas fa-redo text-warning" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="metric-card">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="h4 mb-1 text-info">92%</div>
                                        <div class="small text-muted">First Call Resolution</div>
                                        <div class="progress mt-2" style="height: 6px;">
                                            <div class="progress-bar bg-info" style="width: 92%"></div>
                                        </div>
                                    </div>
                                    <i class="fas fa-phone text-info" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Service Complaints Chart -->
                    <div class="chart-container">
                        <canvas id="workOrderChart" style="height: 200px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Warehouse Insights -->
    <div class="col-12">
        <div class="card division-card division-warehouse h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-warehouse me-2" style="color: var(--optima-success);"></i>
                    Warehouse Insights
                </h5>
            </div>
            <div class="card-body">
                <!-- Unit Status Chart -->
                <div class="chart-container mb-4">
                    <canvas id="warehouseChart" style="height: 200px;"></canvas>
                </div>
                
                <!-- Warehouse Metrics -->
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="metric-card">
                            <div class="metric-value text-success"><?= $warehouse_insights['available_units'] ?></div>
                            <div class="metric-label">Available</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="metric-card">
                            <div class="metric-value text-warning"><?= $warehouse_insights['preparation_units'] ?></div>
                            <div class="metric-label">Preparation</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="metric-card">
                            <div class="metric-value text-danger"><?= $warehouse_insights['damaged_units'] ?></div>
                            <div class="metric-label">Damaged</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="metric-card">
                            <div class="metric-value text-info"><?= $warehouse_insights['warehouse_efficiency'] ?>%</div>
                            <div class="metric-label">Efficiency</div>
                        </div>
                    </div>
                </div>
                
                <!-- Location Distribution -->
                <div class="mt-3">
                    <h6 class="mb-2">Unit Distribution by Location</h6>
                    <?php foreach($warehouse_insights['units_by_location'] as $location): ?>
                    <div class="d-flex align-items-center justify-content-between mb-1 p-2 border rounded">
                        <span class="small"><?= $location['lokasi_unit'] ?></span>
                        <span class="badge bg-success"><?= $location['count'] ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Director Dashboard - Critical Information -->
<div class="row g-4 mb-4">
    <!-- Work Order Trends -->
    <div class="col-xl-6">
        <div class="card division-card division-operational">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2" style="color: var(--optima-info);"></i>
                    Work Order Trends
                </h5>
                <span class="badge bg-info">
                    <?= $work_order_trends['completion_rate'] ?>% Complete
                </span>
            </div>
            <div class="card-body">
                <!-- Complaint Categories Chart -->
                <div class="chart-container mb-4">
                    <canvas id="complaintTrendsChart" style="height: 200px;"></canvas>
                </div>
                
                <!-- Work Order Metrics -->
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="metric-card">
                            <div class="metric-value text-primary"><?= $work_order_trends['avg_resolution_time'] ?>h</div>
                            <div class="metric-label">Avg Resolution Time</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="metric-card">
                            <div class="metric-value text-success"><?= $work_order_trends['completion_rate'] ?>%</div>
                            <div class="metric-label">Completion Rate</div>
                        </div>
                    </div>
                </div>
                
                <!-- Priority Distribution -->
                <div class="mb-3">
                    <h6 class="mb-2">Priority Distribution</h6>
                    <?php foreach($work_order_trends['priority_distribution'] as $priority => $count): ?>
                    <div class="d-flex align-items-center justify-content-between mb-1 p-2 border rounded">
                        <span class="small"><?= $priority ?></span>
                        <span class="badge bg-<?= $priority == 'Critical' ? 'danger' : ($priority == 'High' ? 'warning' : 'info') ?>"><?= $count ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="text-center mt-3">
                    <a href="<?= base_url('/work-orders') ?>" class="btn btn-outline-info">
                        <i class="fas fa-tools me-1"></i>Lihat Detail Work Orders
                    </a>
                </div>
            </div>
        </div>
    </div>
    


<!-- Contract Summary Section -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                    <i class="fas fa-file-contract me-2 text-success"></i>
                    Contract Summary
                </h5>
                <span class="badge bg-success"><?= $contract_summary['active'] ?> Active</span>
            </div>
            <div class="card-body">
                <!-- Contract Metrics -->
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <div class="text-center p-3 border rounded">
                            <div class="h4 mb-1 text-success"><?= $contract_summary['active'] ?></div>
                            <div class="small text-muted">Active Contracts</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 border rounded">
                            <div class="h4 mb-1 text-warning"><?= $contract_summary['expiring_soon'] ?></div>
                            <div class="small text-muted">Expiring Soon</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 border rounded">
                            <div class="h4 mb-1 text-primary"><?= $contract_summary['new_this_month'] ?></div>
                            <div class="small text-muted">New This Month</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 border rounded">
                            <div class="h4 mb-1 text-info"><?= $contract_summary['high_value_contracts'] + $contract_summary['medium_value_contracts'] + $contract_summary['standard_contracts'] ?></div>
                            <div class="small text-muted">Total Value Contracts</div>
                        </div>
                    </div>
                </div>
                
                <!-- Contract Value Distribution -->
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center justify-content-between p-3 border rounded">
                            <div>
                                <div class="fw-semibold">High Value (>Rp 100M)</div>
                                <div class="small text-muted">Premium contracts</div>
                            </div>
                            <span class="badge bg-danger fs-6"><?= $contract_summary['high_value_contracts'] ?></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center justify-content-between p-3 border rounded">
                            <div>
                                <div class="fw-semibold">Medium Value (Rp 50-100M)</div>
                                <div class="small text-muted">Standard contracts</div>
                            </div>
                            <span class="badge bg-warning fs-6"><?= $contract_summary['medium_value_contracts'] ?></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center justify-content-between p-3 border rounded">
                            <div>
                                <div class="fw-semibold">Standard (<Rp 50M)</div>
                                <div class="small text-muted">Basic contracts</div>
                            </div>
                            <span class="badge bg-info fs-6"><?= $contract_summary['standard_contracts'] ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <a href="<?= base_url('/kontrak') ?>" class="btn btn-outline-success">
                        <i class="fas fa-file-contract me-1"></i>Lihat Semua Kontrak
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delivery & Purchase Order Insights -->
<div class="row g-4 mb-4">
    <!-- Delivery Insights -->
    <div class="col-xl-6">
        <div class="card division-card division-delivery">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                    <i class="fas fa-shipping-fast me-2" style="color: var(--optima-info);"></i>
                    Delivery Insights
                </h5>
                <span class="badge bg-info">
                    <?= $delivery_insights['on_time_rate'] ?>% On-Time
                </span>
            </div>
            <div class="card-body">
                <!-- Delivery Status Overview -->
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div class="metric-card">
                            <div class="metric-value text-warning"><?= $delivery_insights['pending_deliveries'] ?></div>
                            <div class="metric-label">Pending</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="metric-card">
                            <div class="metric-value text-info"><?= $delivery_insights['in_transit_deliveries'] ?></div>
                            <div class="metric-label">In Transit</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="metric-card">
                            <div class="metric-value text-success"><?= $delivery_insights['completed_this_month'] ?></div>
                            <div class="metric-label">Completed This Month</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="metric-card">
                            <div class="metric-value text-primary"><?= $delivery_insights['avg_delivery_time'] ?>h</div>
                            <div class="metric-label">Avg Delivery Time</div>
                        </div>
                    </div>
                </div>
                
                <!-- Delivery Chart -->
                <div class="chart-container mb-3">
                    <canvas id="deliveryChart" style="height: 150px;"></canvas>
                </div>
                
                <!-- Delivery Performance -->
                <div class="mb-3">
                    <h6 class="mb-2">Delivery Performance</h6>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small">On-Time Rate</span>
                        <div class="progress" style="width: 60%; height: 8px;">
                            <div class="progress-bar bg-success" style="width: <?= $delivery_insights['on_time_rate'] ?>%"></div>
                        </div>
                        <span class="small fw-semibold"><?= $delivery_insights['on_time_rate'] ?>%</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="small">Efficiency</span>
                        <div class="progress" style="width: 60%; height: 8px;">
                            <div class="progress-bar bg-info" style="width: <?= $delivery_insights['delivery_efficiency'] ?>%"></div>
                        </div>
                        <span class="small fw-semibold"><?= $delivery_insights['delivery_efficiency'] ?>%</span>
                    </div>
                </div>
                
                <div class="text-center mt-3">
                    <a href="<?= base_url('/delivery') ?>" class="btn btn-sm btn-outline-success">
                        <i class="fas fa-shipping-fast me-1"></i>Lihat Detail Delivery
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Purchase Order Insights -->
    <div class="col-xl-6">
        <div class="card division-card division-purchase">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                    <i class="fas fa-shopping-cart me-2" style="color: var(--optima-danger);"></i>
                    Purchase Order Insights
                </h5>
                <span class="badge bg-danger">
                    <?= $purchase_order_insights['approval_rate'] ?>% Approval Rate
                </span>
            </div>
            <div class="card-body">
                <!-- PO Status Overview -->
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div class="metric-card">
                            <div class="metric-value text-warning"><?= $purchase_order_insights['pending_pos'] ?></div>
                            <div class="metric-label">Pending POs</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="metric-card">
                            <div class="metric-value text-success"><?= $purchase_order_insights['approved_pos'] ?></div>
                            <div class="metric-label">Approved POs</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="metric-card">
                            <div class="metric-value text-primary"><?= $purchase_order_insights['po_this_month'] ?></div>
                            <div class="metric-label">POs This Month</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="metric-card">
                            <div class="metric-value text-info"><?= $purchase_order_insights['avg_processing_time'] ?>h</div>
                            <div class="metric-label">Avg Processing Time</div>
                        </div>
                    </div>
                </div>
                
                <!-- Purchase Order Chart -->
                <div class="chart-container mb-3">
                    <canvas id="purchaseOrderChart" style="height: 150px;"></canvas>
                </div>
                
                <!-- PO Performance -->
                <div class="mb-3">
                    <h6 class="mb-2">PO Performance</h6>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small">Approval Rate</span>
                        <div class="progress" style="width: 60%; height: 8px;">
                            <div class="progress-bar bg-warning" style="width: <?= $purchase_order_insights['approval_rate'] ?>%"></div>
                        </div>
                        <span class="small fw-semibold"><?= $purchase_order_insights['approval_rate'] ?>%</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="small">Efficiency</span>
                        <div class="progress" style="width: 60%; height: 8px;">
                            <div class="progress-bar bg-success" style="width: <?= $purchase_order_insights['po_efficiency'] ?>%"></div>
                        </div>
                        <span class="small fw-semibold"><?= $purchase_order_insights['po_efficiency'] ?>%</span>
                    </div>
                </div>
                
                <div class="text-center mt-3">
                    <a href="<?= base_url('/purchase-orders') ?>" class="btn btn-sm btn-outline-warning">
                        <i class="fas fa-shopping-cart me-1"></i>Lihat Detail Purchase Orders
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions & Activity -->
<div class="row g-4 mb-4">

    
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
    
    
    // Initialize director dashboard
    initializeDirectorDashboard();
    
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

// Director Dashboard specific functions
function initializeDirectorDashboard() {
    // Initialize operational chart toggles
    const operationalToggles = document.querySelectorAll('input[name="operationalChart"]');
    operationalToggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            if (this.id === 'operationalUnits') {
                document.getElementById('unitStatusSection').style.display = 'block';
                document.getElementById('workOrdersSection').style.display = 'none';
            } else if (this.id === 'operationalComplaints') {
                document.getElementById('unitStatusSection').style.display = 'none';
                document.getElementById('workOrdersSection').style.display = 'block';
            }
        });
    });
    
    // Initialize warehouse chart
    initializeWarehouseChart();
    
    // Initialize work order trends chart
    initializeWorkOrderTrendsChart();
    
    // Initialize operational chart
    initializeOperationalChart();
    
    // Initialize work order chart
    initializeWorkOrderChart();
    
    // Initialize maintenance chart
    initializeMaintenanceChart();
    
    // Initialize delivery chart
    initializeDeliveryChart();
    
    // Initialize purchase order chart
    initializePurchaseOrderChart();
}

function initializeWarehouseChart() {
    const canvas = document.getElementById('warehouseChart');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    
    // Destroy existing chart if it exists
    if (window.warehouseChartInstance) {
        window.warehouseChartInstance.destroy();
    }
    
    // Get warehouse data from PHP
    const warehouseData = <?= json_encode($warehouse_insights['unit_status']) ?>;
    const labels = warehouseData.map(item => item.nama_status);
    const values = warehouseData.map(item => item.count);
    const colors = ['#00ac69', '#ffb607', '#e81500', '#6c757d', '#17a2b8'];
    
    window.warehouseChartInstance = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: colors.slice(0, values.length),
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
}

function initializeWorkOrderTrendsChart() {
    const canvas = document.getElementById('complaintTrendsChart');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    
    // Destroy existing chart if it exists
    if (window.workOrderTrendsChartInstance) {
        window.workOrderTrendsChartInstance.destroy();
    }
    
    // Get work order trends data from PHP
    const trendsData = <?= json_encode($work_order_trends['complaint_trends']) ?>;
    const labels = Object.keys(trendsData);
    const values = Object.values(trendsData);
    const colors = ['#0061f2', '#00ac69', '#ffb607', '#e81500', '#6c757d'];
    
    window.workOrderTrendsChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Work Orders',
                data: values,
                backgroundColor: colors.slice(0, values.length),
                borderColor: colors.slice(0, values.length),
                borderWidth: 1,
                borderRadius: 4,
                borderSkipped: false
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
                            return context.label + ': ' + context.parsed.y + ' work orders';
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
                        color: '#69707a',
                        maxRotation: 45
                    }
                },
                y: {
                    display: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        color: '#69707a',
                        beginAtZero: true,
                        stepSize: 1
                    }
                }
            }
        }
    });
}

// Operational Chart
function initializeOperationalChart() {
    const canvas = document.getElementById('operationalChart');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    
    // Destroy existing chart if it exists
    if (window.operationalChartInstance) {
        window.operationalChartInstance.destroy();
    }
    
    window.operationalChartInstance = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Disewakan', 'Tersedia', 'Maintenance'],
            datasets: [{
                data: [87, 28, 10],
                backgroundColor: [
                    'rgba(0, 172, 105, 0.8)',
                    'rgba(255, 182, 7, 0.8)',
                    'rgba(232, 21, 0, 0.8)'
                ],
                borderColor: [
                    'rgba(0, 172, 105, 1)',
                    'rgba(255, 182, 7, 1)',
                    'rgba(232, 21, 0, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#0061f2',
                    borderWidth: 1,
                    cornerRadius: 8
                }
            }
        }
    });
}

// Service Complaints Chart
function initializeWorkOrderChart() {
    const canvas = document.getElementById('workOrderChart');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    
    // Destroy existing chart if it exists
    if (window.workOrderChartInstance) {
        window.workOrderChartInstance.destroy();
    }
    
    window.workOrderChartInstance = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Mechanical Issues', 'Electrical Issues', 'Safety Issues', 'Customer Complaints'],
            datasets: [{
                data: [12, 8, 6, 8],
                backgroundColor: [
                    'rgba(232, 21, 0, 0.8)',
                    'rgba(255, 182, 7, 0.8)',
                    'rgba(23, 162, 184, 0.8)',
                    'rgba(108, 117, 125, 0.8)'
                ],
                borderColor: [
                    'rgba(232, 21, 0, 1)',
                    'rgba(255, 182, 7, 1)',
                    'rgba(23, 162, 184, 1)',
                    'rgba(108, 117, 125, 1)'
                ],
                borderWidth: 2
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
                        usePointStyle: true
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#0061f2',
                    borderWidth: 1,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((context.parsed / total) * 100);
                            return context.label + ': ' + context.parsed + ' issues (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
}

// Maintenance Chart
function initializeMaintenanceChart() {
    const canvas = document.getElementById('maintenanceChart');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    
    // Destroy existing chart if it exists
    if (window.maintenanceChartInstance) {
        window.maintenanceChartInstance.destroy();
    }
    
    window.maintenanceChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Maintenance Requests',
                data: [12, 8, 15, 10, 18, 14],
                borderColor: 'rgba(255, 182, 7, 1)',
                backgroundColor: 'rgba(255, 182, 7, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'rgba(255, 182, 7, 1)',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
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
                    borderColor: '#ffb607',
                    borderWidth: 1,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return 'Maintenance: ' + context.parsed.y + ' requests';
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
                        color: '#6c757d'
                    }
                },
                y: {
                    display: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        color: '#6c757d',
                        beginAtZero: true,
                        stepSize: 5
                    }
                }
            }
        }
    });
}

// Delivery Chart
function initializeDeliveryChart() {
    const canvas = document.getElementById('deliveryChart');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    
    // Destroy existing chart if it exists
    if (window.deliveryChartInstance) {
        window.deliveryChartInstance.destroy();
    }
    
    window.deliveryChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Deliveries',
                data: [45, 52, 38, 48, 55, 42],
                borderColor: 'rgba(23, 162, 184, 1)',
                backgroundColor: 'rgba(23, 162, 184, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'rgba(23, 162, 184, 1)',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
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
                    borderColor: '#17a2b8',
                    borderWidth: 1,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return 'Deliveries: ' + context.parsed.y;
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
                        color: '#6c757d'
                    }
                },
                y: {
                    display: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        color: '#6c757d',
                        beginAtZero: true,
                        stepSize: 10
                    }
                }
            }
        }
    });
}

// Purchase Order Chart
function initializePurchaseOrderChart() {
    const canvas = document.getElementById('purchaseOrderChart');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    
    // Destroy existing chart if it exists
    if (window.purchaseOrderChartInstance) {
        window.purchaseOrderChartInstance.destroy();
    }
    
    window.purchaseOrderChartInstance = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Approved', 'Completed'],
            datasets: [{
                data: [8, 25, 45],
                backgroundColor: [
                    'rgba(255, 182, 7, 0.8)',
                    'rgba(0, 172, 105, 0.8)',
                    'rgba(23, 162, 184, 0.8)'
                ],
                borderColor: [
                    'rgba(255, 182, 7, 1)',
                    'rgba(0, 172, 105, 1)',
                    'rgba(23, 162, 184, 1)'
                ],
                borderWidth: 2
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
                        usePointStyle: true
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#e81500',
                    borderWidth: 1,
                    cornerRadius: 8
                }
            }
        }
    });
}

// Export functions for external use
window.dashboardFunctions = {
    refreshCharts: function() {
        if (window.revenueChart) window.revenueChart.update();
        if (window.unitStatusChart) window.unitStatusChart.update();
        if (window.warehouseChartInstance) window.warehouseChartInstance.update();
        if (window.workOrderTrendsChartInstance) window.workOrderTrendsChartInstance.update();
        if (window.operationalChartInstance) window.operationalChartInstance.update();
        if (window.workOrderChartInstance) window.workOrderChartInstance.update();
        if (window.maintenanceChartInstance) window.maintenanceChartInstance.update();
        if (window.deliveryChartInstance) window.deliveryChartInstance.update();
        if (window.purchaseOrderChartInstance) window.purchaseOrderChartInstance.update();
    },
    updateData: updateDashboardData,
    markNotificationAsRead: markNotificationAsRead,
    initializeDirectorDashboard: initializeDirectorDashboard
};
</script>
<?= $this->endSection() ?>