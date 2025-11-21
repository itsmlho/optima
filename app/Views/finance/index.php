<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<!-- CSS coming soon sudah ada di optima-pro.css -->
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="coming-soon-container">
    <div class="coming-soon-card">
        <!-- Company Logos -->
        <div class="coming-soon-logos">
            <img src="<?= base_url('assets/images/company-logo.svg') ?>" alt="PT Sarana Mitra Luas Logo" class="coming-soon-logo">
            <div class="logo-divider"></div>
            <img src="<?= base_url('logo-optima.ico') ?>" alt="OPTIMA Logo" class="coming-soon-logo">
        </div>
        
        <!-- Coming Soon Icon -->
        <div class="coming-soon-icon">
            <i class="fas fa-calculator"></i>
        </div>
        
        <!-- Title -->
        <h1 class="coming-soon-title">Accounting & Finance</h1>
        
        <div class="coming-soon-divider"></div>
        
        <!-- Subtitle -->
        <p class="coming-soon-subtitle">Sistem Keuangan Terintegrasi</p>
        
        <!-- Description -->
        <p class="coming-soon-description">
            Modul Accounting & Finance sedang dalam pengembangan untuk mengelola keuangan perusahaan. 
            Fitur ini akan mencakup invoice management, payment tracking, dan financial reporting.
        </p>
        
        <!-- Features Coming -->
        <div class="coming-soon-features">
            <div class="feature-item">
                <i class="fas fa-file-invoice"></i>
                <span>Invoice Management</span>
            </div>
            <div class="feature-item">
                <i class="fas fa-credit-card"></i>
                <span>Payment Tracking</span>
            </div>
            <div class="feature-item">
                <i class="fas fa-chart-pie"></i>
                <span>Financial Reports</span>
            </div>
        </div>
        
        <!-- Back Button -->
        <a href="<?= base_url('/') ?>" class="back-btn">
            <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
        </a>
    </div>
</div>
<?= $this->endSection() ?>
