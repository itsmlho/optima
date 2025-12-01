<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<style>
.welcome-page {
    min-height: calc(100vh - 60px); /* Account for header height */
    max-height: calc(100vh - 60px); /* Prevent overflow */
    background: #ffffff;
    padding: 1.5rem 2rem; /* Reduced padding */
    overflow-y: auto; /* Handle any overflow gracefully */
}

.welcome-container {
    max-width: 800px;
    margin: 0 auto;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.welcome-header {
    text-align: center;
    margin-bottom: 2rem; /* Increased margin for better spacing */
    padding-bottom: 1.5rem; /* Increased padding */
    border-bottom: 2px solid #e9ecef;
}

.sml-logo {
    max-width: 400px;
    height: auto;
    margin: 0 auto 1.5rem;
    display: block;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    background: white;
    padding: 1rem;
}

/* Fallback for when image is not available */
.sml-logo:not([src]), .sml-logo[src=""], .sml-logo[alt]::after {
    content: "SML RENTAL";
    font-size: 2.5rem;
    font-weight: 700;
    color: #dc3545;
    display: block;
    text-align: center;
    padding: 2rem;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border: 3px solid #28a745;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
}


.company-name {
    font-size: 1.5rem;
    font-weight: 800;
    color: #dc3545;
    letter-spacing: 2px;
    margin-bottom: 0.25rem;
}

.company-tagline {
    font-size: 0.9rem;
    font-weight: 600;
    color: #28a745;
    letter-spacing: 0.5px;
}

.welcome-title {
    font-size: 1.875rem; /* Smaller font size */
    font-weight: 700;
    margin-bottom: 0.5rem; /* Reduced margin */
    color: #212529;
}

.welcome-subtitle {
    font-size: 0.9rem;
    color: #6c757d;
    margin: 0;
}

.welcome-content {
    margin-top: 1.5rem; /* Reduced margin */
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 1rem; /* Use gap instead of large margins */
}

.welcome-message {
    background: #f8f9fa;
    border-left: 4px solid #007bff;
    padding: 1rem; /* Reduced padding */
    border-radius: 4px;
}

.welcome-message p {
    color: #495057;
    font-size: 0.9rem; /* Smaller font */
    line-height: 1.5; /* Tighter line height */
    margin: 0;
}

.user-info-section {
    background: #ffffff;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 1.25rem; /* Reduced padding */
    flex: 1;
}

.user-info-section h5 {
    color: #212529;
    margin-bottom: 1rem; /* Reduced margin */
    font-weight: 600;
    font-size: 1rem; /* Smaller font */
    border-bottom: 1px solid #e9ecef;
    padding-bottom: 0.5rem; /* Reduced padding */
}

.user-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); /* Smaller min width */
    gap: 1rem; /* Reduced gap */
}

.user-info-item {
    padding: 0.25rem 0; /* Reduced padding */
}

.user-info-label {
    color: #6c757d;
    font-size: 0.8rem; /* Smaller font */
    font-weight: 500;
    margin-bottom: 0.25rem; /* Reduced margin */
    display: block;
}

.user-info-value {
    color: #212529;
    font-size: 0.9rem; /* Smaller font */
    font-weight: 600;
}

.welcome-footer {
    text-align: center;
    margin-top: 1rem; /* Reduced margin */
    padding-top: 1rem; /* Reduced padding */
    border-top: 1px solid #e9ecef;
}

.welcome-footer p {
    color: #6c757d;
    font-size: 0.8rem; /* Smaller font */
    margin: 0;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="welcome-page">
    <div class="welcome-container">
        <div class="welcome-header">
            <img src="<?= base_url('assets/images/company-logo.png') ?>" alt="SML Rental" class="sml-logo">

        <div class="welcome-content">
            <div class="welcome-message">
                <p>
                    Selamat datang di sistem OPTIMA - Sistem Manajemen Operasional Terpadu PT SARANA MITRA LUAS Tbk. 
                    Gunakan menu navigasi di sebelah kiri untuk mengakses berbagai modul dan fitur yang tersedia sesuai dengan role dan akses Anda. 
                    Jika Anda memerlukan bantuan, silakan hubungi administrator sistem.
                </p>
            </div>

            <div class="user-info-section">
                <h5><i class="fas fa-user-circle me-2"></i>Informasi Akun Anda</h5>
                <div class="user-info-grid">
                    <div class="user-info-item">
                        <span class="user-info-label">Nama Lengkap</span>
                        <span class="user-info-value"><?= esc($user['name']) ?></span>
                    </div>
                    <div class="user-info-item">
                        <span class="user-info-label">Username</span>
                        <span class="user-info-value"><?= esc($user['username']) ?></span>
                    </div>
                    <div class="user-info-item">
                        <span class="user-info-label">Email</span>
                        <span class="user-info-value"><?= esc($user['email']) ?></span>
                    </div>
                    <div class="user-info-item">
                        <span class="user-info-label">Role</span>
                        <span class="user-info-value">
                            <span class="badge bg-primary"><?= esc(ucfirst(str_replace('_', ' ', $user['role'] ?? 'User'))) ?></span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
