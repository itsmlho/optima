<!DOCTYPE html>
<html lang="<?= service('request')->getLocale() ?>" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
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
        * { box-sizing: border-box; }
        body {
            background: #f5f7fa;
            font-family: 'Metropolis', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            margin: 0;
        }
        .auth-container { width: 100%; max-width: 460px; }
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
        .auth-logo {
            display: flex; align-items: center;
            justify-content: center; gap: 0.75rem;
            margin-bottom: 1.5rem;
        }
        .auth-logo img { height: 35px; width: auto; }
        .logo-divider {
            width: 2px; height: 25px;
            background: linear-gradient(180deg, #0061f2, #0056b3);
        }
        .auth-icon {
            width: 56px; height: 56px;
            background: #e7f3ff; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; color: #0061f2;
            margin: 0 auto 1rem;
        }
        .auth-title {
            font-size: 1.5rem; font-weight: 700;
            color: #2c3e50; text-align: center; margin: 0 0 0.25rem;
        }
        .form-label { font-weight: 600; color: #495057; font-size: 0.875rem; margin-bottom: 0.4rem; }
        .form-label i { color: #0061f2; }
        .form-group { margin-bottom: 1rem; }
        .form-control {
            border: 1px solid #dee2e6; border-radius: 6px;
            padding: 0.65rem 1rem; font-size: 0.925rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-control:focus {
            border-color: #0061f2;
            box-shadow: 0 0 0 0.15rem rgba(0,97,242,0.12);
            outline: none;
        }
        .btn-primary {
            background: #0061f2; border: none; border-radius: 6px;
            padding: 0.7rem 1.5rem; font-weight: 600; font-size: 0.95rem;
            color: white; width: 100%; transition: background 0.2s; cursor: pointer;
        }
        .btn-primary:hover { background: #0056b3; }
        .btn-secondary {
            width: 100%; padding: 0.7rem;
            font-weight: 600; border-radius: 6px;
            background: white; border: 1px solid #dee2e6;
            color: #495057; font-size: 0.95rem;
            transition: all 0.2s; margin-top: 0.75rem;
            text-align: center; display: block; text-decoration: none;
        }
        .btn-secondary:hover { border-color: #0061f2; color: #0061f2; }
        .info-box {
            background: #f8f9fa; border-left: 3px solid #0061f2;
            padding: 0.875rem 1rem; border-radius: 6px;
            margin-bottom: 1.25rem; font-size: 0.875rem; color: #6c757d;
        }
        .info-box i { color: #0061f2; }
        .auth-links { text-align: center; margin-top: 1.25rem; font-size: 0.875rem; }
        .auth-links a { color: #0061f2; text-decoration: none; font-weight: 500; }
        .alert { border-radius: 8px; padding: 0.875rem 1rem; margin-bottom: 1rem; border: none; font-size: 0.9rem; }
        .alert-danger { background: #ffe5e5; color: #c92a2a; }
        .alert-success { background: #d4edda; color: #155724; }
        @media (max-width: 576px) {
            .auth-logo img { height: 28px; }
            .logo-divider { height: 20px; }
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
