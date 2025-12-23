<!DOCTYPE html>
<html lang="<?= service('request')->getLocale() ?>" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Reset Password - OPTIMA | PT Sarana Mitra Luas Tbk">
    <meta name="robots" content="noindex, nofollow">
    
    <title>Lupa Password - OPTIMA | PT Sarana Mitra Luas Tbk</title>
    
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
            padding: 2rem 1rem;
        }
        
        .auth-container {
            width: 100%;
            max-width: 420px;
        }
        
        .auth-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            padding: 2rem 1.75rem;
            border: 1px solid #e9ecef;
            position: relative;
            overflow: hidden;
        }
        
        .auth-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #0061f2, #00ac69);
        }
        
        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .auth-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }
        
        .auth-logo img {
            height: 35px;
            width: auto;
        }
        
        .logo-divider {
            width: 2px;
            height: 25px;
            background: linear-gradient(180deg, #0061f2, #00ac69);
        }
        
        .auth-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #0061f2, #00ac69);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        
        .auth-icon i {
            font-size: 2rem;
            color: white;
        }
        
        .auth-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        
        .auth-subtitle {
            color: #6c757d;
            font-size: 0.95rem;
            line-height: 1.5;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        
        .form-control {
            padding: 0.75rem 0.875rem;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #0061f2;
            box-shadow: 0 0 0 0.2rem rgba(0, 97, 242, 0.1);
        }
        
        .btn-primary {
            width: 100%;
            padding: 0.875rem;
            font-weight: 600;
            border-radius: 10px;
            background: linear-gradient(135deg, #0061f2, #0056b3);
            border: none;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 97, 242, 0.3);
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #0056b3, #004085);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 97, 242, 0.4);
        }
        
        .btn-secondary {
            width: 100%;
            padding: 0.875rem;
            font-weight: 600;
            border-radius: 10px;
            background: white;
            border: 2px solid #e9ecef;
            color: #495057;
            font-size: 1rem;
            transition: all 0.3s ease;
            margin-top: 0.75rem;
        }
        
        .btn-secondary:hover {
            background: #f8f9fa;
            border-color: #0061f2;
            color: #0061f2;
        }
        
        .auth-links {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.9rem;
        }
        
        .auth-links a {
            color: #0061f2;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .auth-links a:hover {
            color: #004085;
        }
        
        .alert {
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border: none;
        }
        
        .alert-danger {
            background-color: #ffe5e5;
            color: #c92a2a;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #0061f2;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        
        .info-box i {
            color: #0061f2;
            margin-right: 0.5rem;
        }
        
        .info-box p {
            margin: 0;
            font-size: 0.85rem;
            color: #6c757d;
        }
        
        /* Dark mode support */
        [data-bs-theme="dark"] body {
            background: linear-gradient(135deg, #1a1d23 0%, #2c3034 100%);
        }
        
        [data-bs-theme="dark"] .auth-card {
            background: #2c3034;
            border-color: #343a40;
        }
        
        [data-bs-theme="dark"] .auth-title {
            color: #e2e8f0;
        }
        
        [data-bs-theme="dark"] .auth-subtitle,
        [data-bs-theme="dark"] .form-label {
            color: #adb5bd;
        }
        
        [data-bs-theme="dark"] .form-control {
            background: #343a40;
            border-color: #495057;
            color: #e2e8f0;
        }
        
        [data-bs-theme="dark"] .form-control:focus {
            background: #343a40;
            border-color: #0061f2;
        }
        
        [data-bs-theme="dark"] .btn-secondary {
            background: #343a40;
            border-color: #495057;
            color: #adb5bd;
        }
        
        [data-bs-theme="dark"] .btn-secondary:hover {
            background: #495057;
            border-color: #0061f2;
            color: #0061f2;
        }
        
        [data-bs-theme="dark"] .info-box {
            background: #343a40;
            border-color: #0061f2;
        }
        
        [data-bs-theme="dark"] .info-box p {
            color: #adb5bd;
        }
        
        /* Responsive */
        @media (max-width: 576px) {
            body {
                padding: 1rem;
            }
            
            .auth-card {
                padding: 2rem 1.5rem;
            }
            
            .auth-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">
                    <img src="<?= base_url('assets/images/company-logo.svg') ?>" alt="SML Logo">
                    <div class="logo-divider"></div>
                    <img src="<?= base_url('logo-optima.ico') ?>" alt="OPTIMA Logo">
                </div>
                
                <div class="auth-icon">
                    <i class="fas fa-key"></i>
                </div>
                
                <h1 class="auth-title">Lupa Password?</h1>
            </div>
            
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>
            
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>
            
            <div class="info-box">
                <i class="fas fa-info-circle"></i>
                <p>
                    Link reset password akan dikirim ke email Anda. Pastikan email yang Anda masukkan sudah terdaftar di sistem.
                </p>
            </div>
            
            <form action="<?= base_url('auth/send-reset-link') ?>" method="POST" id="forgotPasswordForm">
                <?= csrf_field() ?>
                
                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope me-1"></i> Email
                    </label>
                    <input 
                        type="email" 
                        class="form-control" 
                        id="email" 
                        name="email" 
                        placeholder="nama@email.com"
                        required
                        value="<?= old('email') ?>"
                    >
                </div>
                
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-paper-plane me-2"></i> Kirim Link Reset
                </button>
                
                <a href="<?= base_url('auth/login') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Kembali ke Login
                </a>
            </form>
            
            <div class="auth-links">
                Belum punya akun? 
                <a href="<?= base_url('auth/register') ?>">
                    <i class="fas fa-user-plus me-1"></i> Daftar di sini
                </a>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Prevent double submission
        document.getElementById('forgotPasswordForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Mengirim...';
        });
    </script>
</body>
</html>
