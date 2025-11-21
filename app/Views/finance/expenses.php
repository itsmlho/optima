<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<style>
/* ==============================
   COMING SOON STYLES
   Professional coming soon page
   ============================== */
.coming-soon-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #ffffff;
    padding: 2rem 1rem;
}

.coming-soon-card {
    background: white;
    border-radius: 20px;
    padding: 3rem 2rem;
    text-align: center;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    max-width: 600px;
    width: 100%;
    position: relative;
    overflow: hidden;
    border: 1px solid #e9ecef;
}

.coming-soon-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #007bff, #00ac69);
}

.coming-soon-logos {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    margin-bottom: 2rem;
}

.coming-soon-logo {
    height: 40px;
    width: auto;
}

.logo-divider {
    width: 2px;
    height: 30px;
    background: linear-gradient(180deg, #007bff, #00ac69);
    border-radius: 1px;
}

.coming-soon-icon {
    font-size: 4rem;
    color: #007bff;
    margin-bottom: 1.5rem;
    animation: bounce 2s ease-in-out infinite;
}

@keyframes bounce {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

.coming-soon-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 1rem;
    line-height: 1.2;
}

.coming-soon-subtitle {
    font-size: 1.2rem;
    color: #6c757d;
    margin-bottom: 1.5rem;
    font-weight: 500;
}

.coming-soon-divider {
    width: 80px;
    height: 4px;
    background: linear-gradient(90deg, #007bff, #00ac69);
    margin: 1.5rem auto;
    border-radius: 2px;
}

.coming-soon-description {
    font-size: 1rem;
    color: #495057;
    margin-bottom: 2.5rem;
    line-height: 1.6;
}

.coming-soon-features {
    display: flex;
    justify-content: center;
    gap: 2rem;
    margin: 2rem 0;
    flex-wrap: wrap;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #6c757d;
    font-size: 0.9rem;
    padding: 0.5rem 1rem;
    background: #f8f9fa;
    border-radius: 25px;
    transition: all 0.3s ease;
}

.feature-item:hover {
    background: #e9ecef;
    transform: translateY(-2px);
}

.feature-item i {
    color: #00ac69;
    font-size: 1.2rem;
}

.back-btn {
    margin-top: 2rem;
    padding: 0.875rem 2rem;
    font-size: 1rem;
    font-weight: 600;
    border-radius: 50px;
    background: linear-gradient(135deg, #007bff, #0056b3);
    border: none;
    color: #fff;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
}

.back-btn:hover {
    background: linear-gradient(135deg, #0056b3, #004085);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
    color: #fff;
}

/* Dark Mode Support */
[data-bs-theme="dark"] .coming-soon-card {
    background: #2c3034;
    color: #e2e8f0;
}

[data-bs-theme="dark"] .coming-soon-title {
    color: #e2e8f0;
}

[data-bs-theme="dark"] .coming-soon-subtitle {
    color: #adb5bd;
}

[data-bs-theme="dark"] .coming-soon-description {
    color: #adb5bd;
}

[data-bs-theme="dark"] .feature-item {
    background: #343a40;
    color: #adb5bd;
}

[data-bs-theme="dark"] .feature-item:hover {
    background: #495057;
}

/* Responsive Design */
@media (max-width: 768px) {
    .coming-soon-container {
        padding: 1rem;
    }
    
    .coming-soon-card {
        padding: 2rem 1.5rem;
    }
    
    .coming-soon-title {
        font-size: 2rem;
    }
    
    .coming-soon-features {
        flex-direction: column;
        gap: 1rem;
    }
    
    .feature-item {
        justify-content: center;
    }
}
</style>
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
            <i class="fas fa-receipt"></i>
        </div>
        
        <!-- Title -->
        <h1 class="coming-soon-title">Expense Management</h1>
        
        <div class="coming-soon-divider"></div>
        
        <!-- Subtitle -->
        <p class="coming-soon-subtitle">Sistem Manajemen Pengeluaran</p>
        
        <!-- Description -->
        <p class="coming-soon-description">
            Modul Expense Management sedang dalam pengembangan untuk mengelola pengeluaran perusahaan. 
            Fitur ini akan membantu tim accounting dalam mencatat, mengkategorikan, dan melacak expenses.
        </p>
        
        <!-- Features Coming -->
        <div class="coming-soon-features">
            <div class="feature-item">
                <i class="fas fa-plus"></i>
                <span>Record Expenses</span>
            </div>
            <div class="feature-item">
                <i class="fas fa-tags"></i>
                <span>Category Management</span>
            </div>
            <div class="feature-item">
                <i class="fas fa-chart-bar"></i>
                <span>Expense Reports</span>
            </div>
        </div>
        
        <!-- Back Button -->
        <a href="<?= base_url('/') ?>" class="back-btn">
            <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
        </a>
    </div>
</div>
<?= $this->endSection() ?>
