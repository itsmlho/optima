<!DOCTYPE html>
<html lang="<?= service('request')->getLocale() ?>" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Login to OPTIMA - PT Sarana Mitra Luas Tbk">
    <meta name="robots" content="noindex, nofollow">
    
    <title>Login - OPTIMA | PT Sarana Mitra Luas Tbk</title>
    
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
            padding: 1rem;
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
            margin-bottom: 1.5rem;
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
        
        .auth-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        
        .auth-subtitle {
            color: #6c757d;
            font-size: 0.95rem;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        
        .form-control {
            padding: 0.675rem 0.875rem;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #0061f2;
            box-shadow: 0 0 0 0.2rem rgba(0, 97, 242, 0.1);
        }
        
        .input-group {
            position: relative;
        }
        
        .input-group .form-control {
            padding-right: 3rem;
        }
        
        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            padding: 0;
            z-index: 10;
        }
        
        .password-toggle:hover {
            color: #0061f2;
        }
        
        .form-check {
            margin: 1rem 0;
        }
        
        .form-check-input:checked {
            background-color: #0061f2;
            border-color: #0061f2;
        }
        
        .form-check-label {
            color: #6c757d;
            font-size: 0.9rem;
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
        
        .auth-links {
            display: flex;
            justify-content: space-between;
            align-items: center;
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
        
        .auth-divider {
            text-align: center;
            margin: 1.5rem 0;
            position: relative;
        }
        
        .auth-divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e9ecef;
        }
        
        .auth-divider span {
            position: relative;
            background: white;
            padding: 0 1rem;
            color: #6c757d;
            font-size: 0.85rem;
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
            background-color: #e5ffe5;
            color: #2b8a3e;
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
        [data-bs-theme="dark"] .form-label,
        [data-bs-theme="dark"] .form-check-label {
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
        
        [data-bs-theme="dark"] .auth-divider span {
            background: #2c3034;
        }
        
        /* Responsive */
        @media (max-width: 576px) {
            .auth-card {
                padding: 2rem 1.5rem;
            }
            
            .auth-title {
                font-size: 1.5rem;
            }
            
            .auth-links {
                flex-direction: column;
                gap: 0.75rem;
                text-align: center;
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
                <h1 class="auth-title">Login</h1>
                <p class="auth-subtitle">Masuk ke akun OPTIMA Anda</p>
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
            
            <form action="<?= base_url('auth/attempt-login') ?>" method="POST" id="loginForm">
                <?= csrf_field() ?>
                
                <div class="form-group">
                    <label for="username" class="form-label">
                        <i class="fas fa-user me-1"></i> Username atau Email
                    </label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="username" 
                        name="username" 
                        placeholder="Masukkan username atau email"
                        required 
                        autofocus
                        value="<?= old('username') ?>"
                    >
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock me-1"></i> Password
                    </label>
                    <div class="input-group">
                        <input 
                            type="password" 
                            class="form-control" 
                            id="password" 
                            name="password" 
                            placeholder="Masukkan password"
                            required
                        >
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>
                
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                    <label class="form-check-label" for="remember">
                        Ingat saya
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt me-2"></i> Masuk
                </button>
            </form>
            
            <div class="auth-links">
                <a href="<?= base_url('auth/forgot-password') ?>">
                    <i class="fas fa-key me-1"></i> Lupa Password?
                </a>
                <a href="<?= base_url('auth/register') ?>">
                    <i class="fas fa-user-plus me-1"></i> Daftar Akun
                </a>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
        
        // Prevent double submission
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Memproses...';
        });
    </script>
</body>
</html>
