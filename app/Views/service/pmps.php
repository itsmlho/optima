<?= $this->extend('layouts/base') ?>

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
            <i class="fas fa-calendar-check"></i>
        </div>
        
        <!-- Title -->
        <h1 class="coming-soon-title">Preventive Maintenance (PMPS)</h1>
        
        <div class="coming-soon-divider"></div>
        
        <!-- Subtitle -->
        <p class="coming-soon-subtitle">Sistem Pemeliharaan Preventif</p>
        
        <!-- Description -->
        <p class="coming-soon-description">
            Modul PMPS sedang dalam pengembangan untuk mengelola jadwal pemeliharaan preventif unit forklift. 
            Fitur ini akan membantu tim service dalam merencanakan dan melacak maintenance rutin.
        </p>
        
        <!-- Features Coming -->
        <div class="coming-soon-features">
            <div class="feature-item">
                <i class="fas fa-calendar-alt"></i>
                <span>Jadwal Maintenance</span>
            </div>
            <div class="feature-item">
                <i class="fas fa-wrench"></i>
                <span>Tracking Service</span>
            </div>
            <div class="feature-item">
                <i class="fas fa-chart-line"></i>
                <span>Analisis Performa</span>
            </div>
        </div>
        
        <!-- Back Button -->
        <a href="<?= base_url('/') ?>" class="back-btn">
            <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
        </a>
    </div>
</div>
<?= $this->endSection() ?>
