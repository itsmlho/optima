<!DOCTYPE html>
<html lang="id" data-bs-theme="light">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Login ke OPTIMA - Sistem Manajemen Penyewaan Forklift PT Sarana Mitra Luas Tbk">
    <meta name="robots" content="noindex, nofollow">

    <!-- Title -->
    <title>Login - OPTIMA | PT Sarana Mitra Luas Tbk</title>

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
        
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            box-shadow: 0 2rem 4rem rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
        }
        
        .login-brand {
            background: linear-gradient(135deg, #0061f2 0%, #4d8cff 100%);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .login-brand::before {
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
        
        .login-brand-content {
            position: relative;
            z-index: 2;
        }
        
        .login-logo {
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            margin-bottom: 1.5rem;
            backdrop-filter: blur(10px);
        }
        
        .login-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            text-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.1);
        }
        
        .login-subtitle {
            font-size: 1.125rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }
        
        .login-features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .login-feature {
            display: flex;
            align-items: center;
            font-size: 0.875rem;
            opacity: 0.8;
        }
        
        .login-feature i {
            margin-right: 0.5rem;
            width: 1rem;
            text-align: center;
        }
        
        .login-form {
            padding: 3rem 2rem;
        }
        
        .login-form-title {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .login-form-title h2 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        
        .login-form-title p {
            color: #69707a;
            margin-bottom: 0;
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
            border-color: #0061f2;
            box-shadow: 0 0 0 0.2rem rgba(0, 97, 242, 0.25);
        }
        
        .form-floating label {
            padding: 1rem;
            font-weight: 500;
            color: #69707a;
        }
        
        .form-check {
            margin-bottom: 1.5rem;
        }
        
        .form-check-input {
            width: 1.25rem;
            height: 1.25rem;
            border: 2px solid #e9ecef;
            border-radius: 0.25rem;
        }
        
        .form-check-input:checked {
            background-color: #0061f2;
            border-color: #0061f2;
        }
        
        .form-check-label {
            font-weight: 500;
            color: #495057;
            margin-left: 0.5rem;
        }
        
        .btn-login {
            width: 100%;
            padding: 1rem;
            font-size: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 0.5rem;
            background: linear-gradient(135deg, #0061f2 0%, #4d8cff 100%);
            border: none;
            color: white;
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
        }
        
        .btn-login:hover {
            background: linear-gradient(135deg, #0048b8 0%, #0061f2 100%);
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 97, 242, 0.3);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }
        
        .login-links {
            text-align: center;
            margin-top: 2rem;
        }
        
        .login-links a {
            color: #0061f2;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .login-links a:hover {
            color: #0048b8;
        }
        
        .login-divider {
            display: flex;
            align-items: center;
            margin: 2rem 0;
        }
        
        .login-divider::before,
        .login-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e9ecef;
        }
        
        .login-divider span {
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
            color: #0061f2;
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
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .login-container {
                margin: 1rem;
            }
            
            .login-brand {
                padding: 2rem 1rem;
            }
            
            .login-title {
                font-size: 2rem;
            }
            
            .login-form {
                padding: 2rem 1rem;
            }
        }
        
        /* Dark Mode Support */
        [data-bs-theme="dark"] .login-container {
            background: rgba(33, 37, 41, 0.95);
        }
        
        [data-bs-theme="dark"] .login-form-title h2 {
            color: #ffffff;
        }
        
        [data-bs-theme="dark"] .form-floating .form-control {
            background-color: #2c3034;
            border-color: #495057;
            color: #ffffff;
        }
        
        [data-bs-theme="dark"] .form-floating label {
            color: #adb5bd;
        }
        
        [data-bs-theme="dark"] .form-check-label {
            color: #adb5bd;
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

    <div class="login-container row g-0">
        <!-- Brand Section -->
        <div class="col-lg-6 d-none d-lg-block">
            <div class="login-brand h-100">
                <div class="login-brand-content">
                    <div class="login-logo">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h1 class="login-title">OPTIMA</h1>
                    <p class="login-subtitle">Sistem Manajemen Penyewaan Forklift</p>
                    <p class="mb-0">PT Sarana Mitra Luas Tbk</p>
                    
                    <div class="login-features">
                        <div class="login-feature">
                            <i class="fas fa-shield-alt"></i>
                            <span>Keamanan Terjamin</span>
                        </div>
                        <div class="login-feature">
                            <i class="fas fa-clock"></i>
                            <span>Akses 24/7</span>
                        </div>
                        <div class="login-feature">
                            <i class="fas fa-mobile-alt"></i>
                            <span>Responsif Mobile</span>
                        </div>
                        <div class="login-feature">
                            <i class="fas fa-chart-line"></i>
                            <span>Laporan Real-time</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Form Section -->
        <div class="col-lg-6">
            <div class="login-form">
                <div class="login-form-title">
                    <h2>Selamat Datang</h2>
                    <p>Silakan masuk ke akun Anda untuk melanjutkan</p>
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
                        <i class="fas fa-check-circle me-2"></i>
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Login Form -->
                <form action="<?= base_url('auth/attempt-login') ?>" method="post" class="needs-validation" novalidate>
                    <?= csrf_field() ?>
                    
                    <div class="form-floating">
                        <input type="text" class="form-control" id="username" name="username" placeholder="Username atau Email" 
                               value="<?= old('username') ?>" required>
                        <label for="username">
                            <i class="fas fa-user me-2"></i>Username atau Email
                        </label>
                        <div class="invalid-feedback">
                            Silakan masukkan username atau email.
                        </div>
                    </div>
                    
                    <div class="form-floating">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        <label for="password">
                            <i class="fas fa-lock me-2"></i>Password
                        </label>
                        <div class="invalid-feedback">
                            Silakan masukkan password.
                        </div>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            Ingat saya
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-login" id="loginBtn">
                        <div class="loading-spinner" id="loginSpinner"></div>
                        <span id="loginText">Masuk</span>
                    </button>
                </form>
                
                <div class="login-links">
                    <a href="<?= base_url('auth/forgot-password') ?>">Lupa Password?</a>
                    
                    <div class="login-divider">
                        <span>atau</span>
                    </div>
                    
                    <p class="mb-0">Belum punya akun? <a href="<?= base_url('auth/register') ?>">Daftar disini</a></p>
                </div>
            </div>
            
            <div class="footer-links">
                <a href="#">Bantuan</a>
                <a href="#">Kebijakan Privasi</a>
                <a href="#">Syarat & Ketentuan</a>
            </div>
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
                        const loginBtn = document.getElementById('loginBtn');
                        const loginSpinner = document.getElementById('loginSpinner');
                        const loginText = document.getElementById('loginText');
                        
                        loginBtn.disabled = true;
                        loginSpinner.style.display = 'inline-block';
                        loginText.textContent = 'Memproses...';
                    }
                    
                    form.classList.add('was-validated');
                }, false);
            });
        })();
        
        // Auto-hide alerts
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
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
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(event) {
            // Enter key on remember checkbox
            if (event.key === 'Enter' && document.activeElement.type === 'checkbox') {
                document.activeElement.click();
            }
        });
    </script>

</body>

</html> 