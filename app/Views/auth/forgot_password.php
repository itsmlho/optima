<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Reset password akun OPTIMA - Sistem Manajemen Penyewaan Forklift PT Sarana Mitra Luas Tbk">
    <meta name="robots" content="noindex, nofollow">
    
    <!-- Title -->
    <title>Lupa Password - OPTIMA | PT Sarana Mitra Luas Tbk</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?= base_url('assets/images/optima-favicon.svg') ?>">
    <link rel="icon" type="image/x-icon" href="<?= base_url('favicon.ico') ?>">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Metropolis:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="<?= base_url('assets/css/optima-pro.css') ?>" rel="stylesheet">
    
    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Metropolis', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        
        .forgot-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            box-shadow: 0 2rem 4rem rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 600px;
            width: 100%;
        }
        
        .forgot-header {
            background: linear-gradient(135deg, #39afd1 0%, #70c6e0 100%);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .forgot-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            animation: float 20s linear infinite;
        }
        
        @keyframes float {
            0% { transform: translateX(0) translateY(0); }
            100% { transform: translateX(-10px) translateY(-10px); }
        }
        
        .forgot-header-content {
            position: relative;
            z-index: 2;
        }
        
        .forgot-logo {
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            backdrop-filter: blur(10px);
        }
        
        .forgot-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.1);
        }
        
        .forgot-subtitle {
            font-size: 1rem;
            opacity: 0.9;
            margin-bottom: 0;
        }
        
        .forgot-body {
            padding: 3rem 2rem;
        }
        
        .forgot-description {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .forgot-description h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 1rem;
        }
        
        .forgot-description p {
            color: #69707a;
            line-height: 1.6;
            margin-bottom: 0;
        }
        
        .forgot-steps {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid #e9ecef;
        }
        
        .forgot-steps h6 {
            color: #495057;
            font-weight: 600;
            margin-bottom: 1rem;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .forgot-steps ol {
            margin: 0;
            padding-left: 1.25rem;
            color: #69707a;
        }
        
        .forgot-steps li {
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }
        
        .form-floating {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .form-floating .form-control {
            border: 2px solid #e9ecef;
            border-radius: 0.5rem;
            padding: 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-floating .form-control:focus {
            border-color: #39afd1;
            box-shadow: 0 0 0 0.2rem rgba(57, 175, 209, 0.25);
        }
        
        .form-floating label {
            padding: 1rem;
            font-weight: 500;
            color: #69707a;
        }
        
        .btn-reset {
            width: 100%;
            padding: 1rem;
            font-size: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 0.5rem;
            background: linear-gradient(135deg, #39afd1 0%, #70c6e0 100%);
            border: none;
            color: white;
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
        }
        
        .btn-reset:hover {
            background: linear-gradient(135deg, #2e8ba8 0%, #39afd1 100%);
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(57, 175, 209, 0.3);
        }
        
        .btn-reset:active {
            transform: translateY(0);
        }
        
        .btn-reset:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }
        
        .forgot-links {
            text-align: center;
            margin-top: 2rem;
        }
        
        .forgot-links a {
            color: #39afd1;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
            margin: 0 1rem;
        }
        
        .forgot-links a:hover {
            color: #2e8ba8;
        }
        
        .forgot-divider {
            display: flex;
            align-items: center;
            margin: 2rem 0;
        }
        
        .forgot-divider::before,
        .forgot-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e9ecef;
        }
        
        .forgot-divider span {
            padding: 0 1rem;
            color: #69707a;
            font-size: 0.875rem;
        }
        
        .alert {
            border-radius: 0.5rem;
            font-weight: 500;
            margin-bottom: 1.5rem;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, rgba(232, 21, 0, 0.1) 0%, rgba(232, 21, 0, 0.05) 100%);
            color: #bb1100;
            border: 1px solid rgba(232, 21, 0, 0.2);
        }
        
        .alert-success {
            background: linear-gradient(135deg, rgba(0, 172, 105, 0.1) 0%, rgba(0, 172, 105, 0.05) 100%);
            color: #006644;
            border: 1px solid rgba(0, 172, 105, 0.2);
        }
        
        .alert-info {
            background: linear-gradient(135deg, rgba(57, 175, 209, 0.1) 0%, rgba(57, 175, 209, 0.05) 100%);
            color: #2e8ba8;
            border: 1px solid rgba(57, 175, 209, 0.2);
        }
        
        .footer-links {
            text-align: center;
            padding: 2rem;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }
        
        .footer-links a {
            color: #69707a;
            text-decoration: none;
            margin: 0 1rem;
            font-size: 0.875rem;
            transition: color 0.3s ease;
        }
        
        .footer-links a:hover {
            color: #39afd1;
        }
        
        .loading-spinner {
            display: none;
            width: 1.5rem;
            height: 1.5rem;
            border: 2px solid transparent;
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 0.5rem;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .success-message {
            text-align: center;
            padding: 2rem;
        }
        
        .success-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #00ac69 0%, #4dd289 100%);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            margin-bottom: 1.5rem;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(0, 172, 105, 0.7);
            }
            70% {
                transform: scale(1.05);
                box-shadow: 0 0 0 10px rgba(0, 172, 105, 0);
            }
            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(0, 172, 105, 0);
            }
        }
        
        .success-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 1rem;
        }
        
        .success-text {
            color: #69707a;
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .forgot-container {
                margin: 1rem;
            }
            
            .forgot-header {
                padding: 2rem 1rem;
            }
            
            .forgot-title {
                font-size: 1.75rem;
            }
            
            .forgot-body {
                padding: 2rem 1rem;
            }
        }
        
        /* Dark Mode Support */
        [data-bs-theme="dark"] .forgot-container {
            background: rgba(33, 37, 41, 0.95);
        }
        
        [data-bs-theme="dark"] .forgot-description h3 {
            color: #ffffff;
        }
        
        [data-bs-theme="dark"] .forgot-steps {
            background: linear-gradient(135deg, #2c3034 0%, #343a40 100%);
            border-color: #495057;
        }
        
        [data-bs-theme="dark"] .forgot-steps h6 {
            color: #adb5bd;
        }
        
        [data-bs-theme="dark"] .forgot-steps ol {
            color: #adb5bd;
        }
        
        [data-bs-theme="dark"] .form-floating .form-control {
            background-color: #2c3034;
            border-color: #495057;
            color: #ffffff;
        }
        
        [data-bs-theme="dark"] .form-floating label {
            color: #adb5bd;
        }
        
        [data-bs-theme="dark"] .success-title {
            color: #ffffff;
        }
        
        [data-bs-theme="dark"] .footer-links {
            background: #2c3034;
            border-top-color: #495057;
        }
        
        [data-bs-theme="dark"] .footer-links a {
            color: #adb5bd;
        }
    </style>
</head>
<body>
    <div class="forgot-container">
        <!-- Header Section -->
        <div class="forgot-header">
            <div class="forgot-header-content">
                <div class="forgot-logo">
                    <i class="fas fa-key"></i>
                </div>
                <h1 class="forgot-title">Lupa Password?</h1>
                <p class="forgot-subtitle">Jangan khawatir, kami akan membantu Anda</p>
            </div>
        </div>
        
        <!-- Body Section -->
        <div class="forgot-body">
            <!-- Description -->
            <div class="forgot-description">
                <h3>Reset Password Anda</h3>
                <p>Masukkan alamat email yang terdaftar di akun OPTIMA Anda. Kami akan mengirimkan link untuk mereset password ke email tersebut.</p>
            </div>
            
            <!-- Steps -->
            <div class="forgot-steps">
                <h6>Langkah-langkah reset password:</h6>
                <ol>
                    <li>Masukkan email yang terdaftar di akun Anda</li>
                    <li>Klik tombol "Kirim Link Reset"</li>
                    <li>Cek email Anda dan klik link yang dikirimkan</li>
                    <li>Buat password baru yang aman</li>
                    <li>Login dengan password baru Anda</li>
                </ol>
            </div>
            
            <!-- Flash Messages -->
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <div class="success-message">
                        <div class="success-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="success-title">Email Terkirim!</div>
                        <div class="success-text">
                            Kami telah mengirimkan link reset password ke email Anda. 
                            Silakan cek inbox dan folder spam Anda.
                        </div>
                        <a href="<?= base_url('auth/login') ?>" class="btn btn-reset">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Login
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Reset Form -->
                <form action="<?= base_url('auth/sendResetLink') ?>" method="post" class="needs-validation" novalidate>
                    <?= csrf_field() ?>
                    
                    <div class="form-floating">
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder="name@example.com" value="<?= old('email') ?>" required>
                        <label for="email">
                            <i class="fas fa-envelope me-2"></i>Email Terdaftar
                        </label>
                        <div class="invalid-feedback">
                            Silakan masukkan email yang valid.
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-reset" id="resetBtn">
                        <div class="loading-spinner" id="resetSpinner"></div>
                        <i class="fas fa-paper-plane me-2" id="resetIcon"></i>
                        <span id="resetText">Kirim Link Reset</span>
                    </button>
                </form>
            <?php endif; ?>
            
            <!-- Links -->
            <div class="forgot-links">
                <a href="<?= base_url('auth/login') ?>">
                    <i class="fas fa-arrow-left me-1"></i>Kembali ke Login
                </a>
                
                <div class="forgot-divider">
                    <span>atau</span>
                </div>
                
                <a href="<?= base_url('auth/register') ?>">
                    <i class="fas fa-user-plus me-1"></i>Buat Akun Baru
                </a>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer-links">
            <a href="#">Bantuan</a>
            <a href="#">Hubungi Support</a>
            <a href="#">FAQ</a>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Form validation
        (function() {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');
            
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    } else {
                        // Show loading state
                        const resetBtn = document.getElementById('resetBtn');
                        const resetSpinner = document.getElementById('resetSpinner');
                        const resetIcon = document.getElementById('resetIcon');
                        const resetText = document.getElementById('resetText');
                        
                        if (resetBtn) {
                            resetBtn.disabled = true;
                            resetSpinner.style.display = 'inline-block';
                            resetIcon.style.display = 'none';
                            resetText.textContent = 'Mengirim...';
                        }
                    }
                    
                    form.classList.add('was-validated');
                }, false);
            });
        })();
        
        // Auto-hide alerts
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert:not(.alert-success)');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });
        
        // Focus first input
        window.addEventListener('load', function() {
            const firstInput = document.querySelector('input:not([type="hidden"])');
            if (firstInput) {
                firstInput.focus();
            }
        });
        
        // Enhanced form interactions
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });
        });
        
        // Email validation
        document.getElementById('email')?.addEventListener('blur', function() {
            const email = this.value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (email && !emailRegex.test(email)) {
                this.setCustomValidity('Format email tidak valid');
            } else {
                this.setCustomValidity('');
            }
        });
        
        // Add animation to success message if present
        if (document.querySelector('.success-message')) {
            setTimeout(() => {
                document.querySelector('.success-message').classList.add('animate-fadeIn');
            }, 100);
        }
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(event) {
            // Escape key to go back
            if (event.key === 'Escape') {
                window.location.href = '<?= base_url('auth/login') ?>';
            }
        });
    </script>
</body>
</html> 