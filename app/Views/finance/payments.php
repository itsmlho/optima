<?= $this->extend('layouts/base') ?>

<?php
/**
 * Finance Payments Module (Coming Soon)
 *
 * BADGE SYSTEM: Menggunakan Optima Badge Standards (optima-pro.css)
 * Styles: see optima-pro.css section "FINANCE PAYMENTS - COMING SOON PAGE"
 */
?>

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
            <i class="fas fa-credit-card"></i>
        </div>
        
        <!-- Title -->
        <h1 class="coming-soon-title"><?= lang('Finance.payment_validation') ?></h1>
        
        <div class="coming-soon-divider"></div>
        
        <!-- Subtitle -->
        <p class="coming-soon-subtitle"><?= lang('Finance.payment_validation_subtitle') ?></p>
        
        <!-- Description -->
        <p class="coming-soon-description">
            <?= lang('Finance.payment_validation_description') ?>
        </p>
        
        <!-- Features Coming -->
        <div class="coming-soon-features">
            <div class="feature-item">
                <i class="fas fa-check-double"></i>
                <span><?= lang('Finance.feature_validate_payment') ?></span>
            </div>
            <div class="feature-item">
                <i class="fas fa-clock"></i>
                <span><?= lang('Finance.feature_track_payment_status') ?></span>
            </div>
            <div class="feature-item">
                <i class="fas fa-bell"></i>
                <span><?= lang('Finance.feature_payment_alerts') ?></span>
            </div>
        </div>
        
        <!-- Back Button -->
        <a href="<?= base_url('/') ?>" class="back-btn">
            <i class="fas fa-arrow-left me-2"></i><?= lang('Finance.back_to_dashboard') ?>
        </a>
    </div>
</div>
<?= $this->endSection() ?>
