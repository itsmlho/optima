<!DOCTYPE html>
<html lang="<?= service('request')->getLocale() ?>" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Email Verification - OPTIMA">
    <meta name="robots" content="noindex, nofollow">
    
    <title>Verifikasi Email - OPTIMA | PT Sarana Mitra Luas Tbk</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?= base_url('assets/images/optima-favicon.svg') ?>">
    <link rel="icon" type="image/x-icon" href="<?= base_url('favicon.ico') ?>">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Metropolis:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    
    <style>
        * { box-sizing: border-box; }
        body {
            background: #f5f7fa;
            font-family: 'Metropolis', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 3rem 1rem;
            margin: 0;
        }
        .auth-container { width: 100%; max-width: 560px; }
        .auth-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            padding: 2.5rem 2rem;
            border: 1px solid #e9ecef;
            position: relative;
            overflow: hidden;
        }
        .auth-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, #0061f2, #0056b3);
        }
        /* Legacy class aliases used in HTML */
        .auth-card-wide { max-width: none; }
        .auth-icon.email-icon {
            width: 64px; height: 64px;
            background: #e7f3ff;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.25rem;
            font-size: 1.75rem; color: #0061f2;
        }
        .auth-title {
            font-size: 1.5rem; font-weight: 700;
            color: #2c3e50; text-align: center; margin: 0 0 0.5rem;
        }
        .auth-subtitle { color: #6c757d; text-align: center; margin-bottom: 1.5rem; font-size: 0.9rem; }
        .email-display {
            background: #f8f9fa; border-radius: 8px;
            padding: 0.75rem 1rem; margin-bottom: 1rem;
            font-size: 0.9rem; color: #495057; border: 1px solid #dee2e6;
        }
        .steps-container { margin: 1.5rem 0; }
        .steps-container h5 { font-weight: 700; color: #2c3e50; margin-bottom: 1rem; }
        .step-item {
            display: flex; align-items: flex-start;
            gap: 0.75rem; margin-bottom: 0.875rem;
        }
        .step-number {
            min-width: 28px; height: 28px;
            background: #0061f2; color: white;
            border-radius: 50%; display: flex;
            align-items: center; justify-content: center;
            font-size: 0.8rem; font-weight: 700; flex-shrink: 0;
        }
        .step-content span { font-size: 0.875rem; color: #6c757d; }
        .help-text { margin-top: 1.25rem; font-size: 0.875rem; color: #6c757d; }
        .help-text a { color: #0061f2; text-decoration: none; }
        .btn-outline-primary {
            border: 1px solid #0061f2; color: #0061f2;
            background: transparent; border-radius: 6px;
            padding: 0.6rem 1.25rem; font-weight: 600;
            transition: all 0.2s; display: inline-block; text-decoration: none;
        }
        .btn-outline-primary:hover { background: #0061f2; color: white; }
    </style>

</head>
<body>
    <div class="auth-container">
        <div class="auth-card auth-card-wide verify-card">
            <div class="header-section">
                <div class="auth-icon email-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                
                <h1 class="auth-title verify-title">Cek Email Anda</h1>
                <p class="auth-subtitle verify-subtitle">
                    Kami telah mengirimkan link verifikasi ke alamat email Anda
                </p>
            </div>
            
            <?php if (isset($email) && $email): ?>
                <div class="email-display">
                    <strong><i class="fas fa-envelope me-2"></i><?= esc($email) ?></strong>
                </div>
            <?php endif; ?>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Email verifikasi telah dikirim!</strong> Silakan cek inbox atau folder spam Anda.
            </div>
            
            <div class="steps-container">
                <h5>
                    <i class="fas fa-list-ol"></i>
                    Langkah Selanjutnya:
                </h5>
                
                <div class="step-item">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <strong>Buka Email Anda</strong>
                        <span>Cek inbox atau folder spam/junk untuk email dari OPTIMA</span>
                    </div>
                </div>
                
                <div class="step-item">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <strong>Klik Link Verifikasi</strong>
                        <span>Klik tombol "Verify Email" atau link verifikasi di email</span>
                    </div>
                </div>
                
                <div class="step-item">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <strong>Tunggu Persetujuan Admin</strong>
                        <span>Setelah email terverifikasi, akun menunggu approval dari admin</span>
                    </div>
                </div>
                
                <div class="step-item">
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <strong>Login ke Sistem</strong>
                        <span>Setelah disetujui admin, Anda dapat login ke OPTIMA</span>
                    </div>
                </div>
            </div>
            
            <div class="alert alert-warning">
                <i class="fas fa-clock me-2"></i>
                Link verifikasi berlaku selama <strong>24 jam</strong>. Jika expired, Anda perlu request link baru.
            </div>
            
            <a href="<?= base_url('auth/resend-verification') ?>" class="btn btn-outline-primary">
                <i class="fas fa-paper-plane me-2"></i>
                Kirim Ulang Email Verifikasi
            </a>
            
            <div class="help-text">
                Tidak menerima email? Periksa folder spam atau 
                <a href="<?= base_url('auth/resend-verification') ?>">kirim ulang email verifikasi</a>.
                <br>
                Butuh bantuan? Hubungi 
                <a href="mailto:support@sml.co.id">support@sml.co.id</a>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
