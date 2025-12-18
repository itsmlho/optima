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
        <h1 class="coming-soon-title"><?= lang('App.preventive_maintenance_pmps') ?></h1>
        
        <div class="coming-soon-divider"></div>
        
        <!-- Subtitle -->
        <p class="coming-soon-subtitle"><?= lang('Service.preventive_maintenance_system') ?></p>
        
        <!-- Description -->
        <p class="coming-soon-description">
            <?= lang('Service.pmps_coming_soon_description') ?>
        </p>
        
        <!-- Features Coming -->
        <div class="coming-soon-features">
            <div class="feature-item">
                <i class="fas fa-calendar-alt"></i>
                <span><?= lang('Service.maintenance_schedule') ?></span>
            </div>
            <div class="feature-item">
                <i class="fas fa-wrench"></i>
                <span><?= lang('Service.service_tracking') ?></span>
            </div>
            <div class="feature-item">
                <i class="fas fa-chart-line"></i>
                <span><?= lang('Service.performance_analysis') ?></span>
            </div>
        </div>
        
        <!-- Back Button -->
        <a href="<?= base_url('/') ?>" class="back-btn">
            <i class="fas fa-arrow-left me-2"></i><?= lang('App.back_to_dashboard') ?>
        </a>
    </div>
</div>
<?= $this->endSection() ?>
