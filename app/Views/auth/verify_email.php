<!DOCTYPE html>
<html lang="<?= service('request')->getLocale() ?>" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Metropolis', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 1.5rem 1rem;
        }
        
        .verify-container {
            width: 100%;
            max-width: 700px;
        }
        
        .verify-card {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            border: 1px solid #e9ecef;
            position: relative;
            overflow: hidden;
        }
        
        .verify-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #0061f2, #00ac69);
        }
        
        .header-section {
            text-align: center;
            padding-bottom: 1.25rem;
            margin-bottom: 1.25rem;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .email-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1rem;
            background: linear-gradient(135deg, #0061f2, #00ac69);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(0, 97, 242, 0.3);
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .email-icon i {
            font-size: 2.5rem;
            color: white;
        }
        
        .verify-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        
        .verify-subtitle {
            font-size: 0.95rem;
            color: #6c757d;
            line-height: 1.5;
        }
        
        .email-address {
            background: #f8f9fa;
            padding: 0.875rem;
            border-radius: 0.75rem;
            border: 2px dashed #0061f2;
            margin: 1rem 0;
            font-size: 1rem;
            font-weight: 600;
            color: #0061f2;
            text-align: center;
        }
        
        .alert {
            background: #e7f3ff;
            color: #0061f2;
            padding: 0.875rem 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1rem;
            font-size: 0.875rem;
            border-left: 4px solid #0061f2;
        }
        
        .steps {
            background: #f8f9fa;
            padding: 1.25rem;
            border-radius: 1rem;
            margin: 1rem 0;
            border: 1px solid #e9ecef;
        }
        
        .steps h5 {
            font-size: 1rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .step-item {
            display: flex;
            gap: 0.875rem;
            margin-bottom: 0.75rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #e9ecef;
        }
        
        .step-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        
        .step-number {
            width: 28px;
            height: 28px;
            background: linear-gradient(135deg, #0061f2, #00ac69);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.875rem;
            flex-shrink: 0;
        }
        
        .step-content strong {
            color: #2c3e50;
            display: block;
            margin-bottom: 0.125rem;
            font-size: 0.875rem;
        }
        
        .step-content span {
            color: #6c757d;
            font-size: 0.8rem;
            line-height: 1.4;
        }
        
        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border-left: 4px solid #ffc107;
            font-size: 0.875rem;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
            border: none;
            text-decoration: none;
            display: inline-block;
            width: 100%;
            text-align: center;
        }
        
        .btn-outline-primary {
            color: #0061f2;
            border: 2px solid #0061f2;
            background: transparent;
        }
        
        .btn-outline-primary:hover {
            background: #0061f2;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 97, 242, 0.3);
        }
        
        .help-text {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 0.85rem;
            text-align: center;
        }
        
        .help-text a {
            color: #0061f2;
            text-decoration: none;
            font-weight: 600;
        }
        
        .help-text a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="verify-container">
        <div class="verify-card">
            <div class="header-section">
                <div class="email-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                
                <h1 class="verify-title">Cek Email Anda</h1>
                <p class="verify-subtitle">
                    Kami telah mengirimkan link verifikasi ke alamat email Anda
                </p>
            </div>
            
            <?php if (isset($email) && $email): ?>
                <div class="email-address">
                    <i class="fas fa-envelope me-2"></i><?= esc($email) ?>
                </div>
            <?php endif; ?>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Email verifikasi telah dikirim!</strong> Silakan cek inbox atau folder spam Anda.
            </div>
            
            <div class="steps">
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
