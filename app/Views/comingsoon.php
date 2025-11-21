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
            <i class="fas fa-tools"></i>
        </div>
        
        <!-- Title -->
        <h1 class="coming-soon-title">Coming Soon</h1>
        
        <div class="coming-soon-divider"></div>
        
        <!-- Subtitle -->
        <p class="coming-soon-subtitle">Fitur Dalam Pengembangan</p>
        
        <!-- Description -->
        <p class="coming-soon-description">
            Tim IT sedang mengembangkan fitur ini untuk meningkatkan efisiensi operasional perusahaan. 
            Mohon bersabar, halaman ini akan segera tersedia dengan sistem yang lebih baik.
        </p>
        
        <!-- Features Coming -->
        <div class="coming-soon-features">
            <div class="feature-item">
                <i class="fas fa-check-circle"></i>
                <span>Sistem Terintegrasi</span>
            </div>
            <div class="feature-item">
                <i class="fas fa-check-circle"></i>
                <span>Proses Lebih Cepat</span>
            </div>
            <div class="feature-item">
                <i class="fas fa-check-circle"></i>
                <span>Laporan Akurat</span>
            </div>
        </div>
        
        <!-- Back Button -->
        <a href="<?= base_url('/') ?>" class="back-btn">
            <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
        </a>
    </div>
</div>
<?= $this->endSection() ?>
