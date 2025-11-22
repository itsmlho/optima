<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<style>
.welcome-page {
    min-height: 100vh;
    background: #ffffff;
    padding: 4rem 2rem;
}

.welcome-container {
    max-width: 800px;
    margin: 0 auto;
}

.welcome-header {
    text-align: center;
    margin-bottom: 3rem;
    padding-bottom: 2rem;
    border-bottom: 2px solid #e9ecef;
}

.welcome-logo {
    width: 100px;
    height: 100px;
    margin-bottom: 1.5rem;
}

.welcome-title {
    font-size: 2.25rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
    color: #212529;
}

.welcome-subtitle {
    font-size: 1rem;
    color: #6c757d;
    margin: 0;
}

.welcome-content {
    margin-top: 3rem;
}

.welcome-message {
    background: #f8f9fa;
    border-left: 4px solid #007bff;
    padding: 1.5rem;
    margin-bottom: 2rem;
    border-radius: 4px;
}

.welcome-message p {
    color: #495057;
    font-size: 1rem;
    line-height: 1.6;
    margin: 0;
}

.user-info-section {
    background: #ffffff;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 2rem;
}

.user-info-section h5 {
    color: #212529;
    margin-bottom: 1.5rem;
    font-weight: 600;
    font-size: 1.125rem;
    border-bottom: 1px solid #e9ecef;
    padding-bottom: 0.75rem;
}

.user-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
}

.user-info-item {
    padding: 0.5rem 0;
}

.user-info-label {
    color: #6c757d;
    font-size: 0.875rem;
    font-weight: 500;
    margin-bottom: 0.5rem;
    display: block;
}

.user-info-value {
    color: #212529;
    font-size: 1rem;
    font-weight: 600;
}

.welcome-footer {
    text-align: center;
    margin-top: 3rem;
    padding-top: 2rem;
    border-top: 1px solid #e9ecef;
}

.welcome-footer p {
    color: #6c757d;
    font-size: 0.875rem;
    margin: 0;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="welcome-page">
    <div class="welcome-container">
        <div class="welcome-header">
            <img src="<?= base_url('assets/images/logo-optima.ico') ?>" alt="OPTIMA" class="welcome-logo">
            <h1 class="welcome-title">Selamat Datang di OPTIMA</h1>
            <p class="welcome-subtitle">PT SARANA MITRA LUAS Tbk</p>
        </div>

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

            <div class="welcome-footer">
                <p><i class="fas fa-info-circle me-1"></i>Gunakan menu navigasi untuk mulai bekerja</p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
