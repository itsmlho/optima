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
        <h1 class="coming-soon-title">Payment Validation</h1>
        
        <div class="coming-soon-divider"></div>
        
        <!-- Subtitle -->
        <p class="coming-soon-subtitle">Sistem Validasi Pembayaran</p>
        
        <!-- Description -->
        <p class="coming-soon-description">
            Modul Payment Validation sedang dalam pengembangan untuk memvalidasi dan melacak pembayaran. 
            Fitur ini akan membantu tim accounting dalam memverifikasi status pembayaran invoice.
        </p>
        
        <!-- Features Coming -->
        <div class="coming-soon-features">
            <div class="feature-item">
                <i class="fas fa-check-double"></i>
                <span>Validate Payment</span>
            </div>
            <div class="feature-item">
                <i class="fas fa-clock"></i>
                <span>Track Status</span>
            </div>
            <div class="feature-item">
                <i class="fas fa-bell"></i>
                <span>Payment Alerts</span>
            </div>
        </div>
        
        <!-- Back Button -->
        <a href="<?= base_url('/') ?>" class="back-btn">
            <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
        </a>
    </div>
</div>
<?= $this->endSection() ?>
