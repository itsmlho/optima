<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Login to OPTIMA - Forklift Rental Management System by PT Sarana Mitra Luas Tbk">
    <meta name="robots" content="noindex, nofollow">
    
    <title>Login to OPTIMA | PT Sarana Mitra Luas Tbk</title>
    
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
        /* Modern Professional Auth Page */
        * {
            box-sizing: border-box;
        }
        
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
        
        .auth-container {
            width: 100%;
            max-width: 440px;
        }
        
        .auth-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            padding: 2.5rem 2rem;
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
            height: 3px;
            background: linear-gradient(90deg, #0061f2, #0056b3);
        }
        
        .auth-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .auth-logo img {
            height: 36px;
            width: auto;
        }
        
        .logo-divider {
            width: 2px;
            height: 30px;
            background: linear-gradient(180deg, #0061f2, #0056b3);
            border-radius: 2px;
        }
        
        h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2c3e50;
            margin: 0 0 0.5rem 0;
            text-align: center;
        }
        
        .auth-subtitle {
            color: #6c757d;
            text-align: center;
            margin-bottom: 1.75rem;
            font-size: 0.9rem;
        }
        
        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #a0aec0;
            cursor: pointer;
            padding: 0.5rem;
            z-index: 10;
            min-width: 40px;
            min-height: 40px;
            border-radius: 8px;
            transition: all 0.2s;
        }
        
        .password-toggle:hover {
            background: rgba(0, 97, 242, 0.1);
            color: #0061f2;
        }
        
        /* Form Elements */
        .mb-3 {
            margin-bottom: 1.25rem;
        }
        
        .mb-3:last-of-type {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
            margin-top: 0.25rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .form-label i {
            color: #0061f2;
            font-size: 1rem;
        }
        
        .form-control {
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            background: #ffffff;
            color: #495057;
        }
        
        .form-control:focus {
            border-color: #0061f2;
            box-shadow: 0 0 0 0.15rem rgba(0, 97, 242, 0.1);
            background: white;
            outline: none;
        }
        
        .form-control::placeholder {
            color: #a0aec0;
        }
        
        .position-relative {
            position: relative;
        }
        
        .btn-primary {
            background: #0061f2;
            border: none;
            border-radius: 6px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            font-size: 0.95rem;
            width: 100%;
            transition: all 0.2s ease;
            color: white;
            cursor: pointer;
        }
        
        .btn-primary:hover {
            background: #0056b3;
            box-shadow: 0 2px 8px rgba(0, 97, 242, 0.25);
        }
        
        .btn-primary:active {
            transform: translateY(0);
        }
        
        .form-check {
            display: flex;
            align-items: center;
            margin: 1rem 0;
        }
        
        .form-check-input {
            width: 18px;
            height: 18px;
            margin-right: 0.5rem;
            cursor: pointer;
        }
        
        .form-check-label {
            color: #4a5568;
            font-size: 0.9rem;
            cursor: pointer;
            user-select: none;
        }
        
        .auth-links {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.5rem;
            gap: 1rem;
        }
        
        .auth-links a {
            color: #0061f2;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
        }
        
        .auth-links a:hover {
            color: #0056b3;
            text-decoration: underline;
        }
        
        .auth-links a i {
            font-size: 0.875rem;
        }
        
        .auth-divider {
            text-align: center;
            margin: 1.5rem 0;
            position: relative;
        }
        
        /* Login Loading Modal */
        .login-loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .login-loading-overlay.show {
            display: flex;
            opacity: 1;
        }
        
        .login-loading-modal {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.98));
            border-radius: 1rem;
            padding: 2.5rem 3rem;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0, 97, 242, 0.25);
            border: 1px solid rgba(0, 97, 242, 0.1);
            min-width: 320px;
            max-width: 400px;
            transform: scale(0.9);
            transition: transform 0.3s ease;
        }
        
        .login-loading-overlay.show .login-loading-modal {
            transform: scale(1);
        }
        
        .login-loading-logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.25rem;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-loading-logo::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 2px solid #e8e8e8;
            z-index: 1;
        }
        
        .login-loading-logo::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 2px solid transparent;
            border-top-color: #0061f2;
            border-right-color: #4d8cff;
            animation: spin-login 0.8s linear infinite;
            z-index: 1;
        }
        
        .login-loading-logo img {
            width: 48px;
            height: 48px;
            object-fit: contain;
            border-radius: 50%;
            background: white;
            padding: 6px;
            box-shadow: 0 4px 15px rgba(0, 97, 242, 0.3);
            animation: pulse-login 1.5s ease-in-out infinite;
            z-index: 2;
            position: relative;
        }
        
        .login-loading-text {
            font-size: 1.125rem;
            font-weight: 600;
            color: #0061f2;
            margin-bottom: 0.75rem;
            letter-spacing: 0.02em;
        }
        
        .login-loading-subtitle {
            font-size: 0.875rem;
            color: #69707a;
            margin-bottom: 1.25rem;
        }
        
        .login-loading-bars {
            display: flex;
            gap: 6px;
            margin: 0 auto;
            justify-content: center;
            height: 24px;
            align-items: flex-end;
        }
        
        .login-loading-bar {
            width: 4px;
            background: linear-gradient(180deg, #0061f2, #4d8cff);
            border-radius: 2px;
            animation: bar-bounce-login 1.2s ease-in-out infinite;
        }
        
        .login-loading-bar:nth-child(1) { animation-delay: 0s; }
        .login-loading-bar:nth-child(2) { animation-delay: 0.15s; }
        .login-loading-bar:nth-child(3) { animation-delay: 0.3s; }
        .login-loading-bar:nth-child(4) { animation-delay: 0.45s; }
        .login-loading-bar:nth-child(5) { animation-delay: 0.6s; }
        
        @keyframes spin-login {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @keyframes pulse-login {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.08); }
        }
        
        @keyframes bar-bounce-login {
            0%, 100% { 
                height: 8px;
                opacity: 0.4; 
            }
            50% { 
                height: 20px;
                opacity: 1; 
            }
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
        
        /* Mobile Optimization - Clean & Professional */
        @media (max-width: 767px) {
            body {
                padding: 1rem 0.75rem;
                background: #ffffff;
            }
            
            .auth-container {
                max-width: 100%;
            }
            
            .auth-card {
                padding: 1.75rem 1.25rem;
                border-radius: 8px;
                box-shadow: 0 1px 6px rgba(0, 0, 0, 0.06);
            }
            
            .auth-logo img {
                height: 32px;
            }
            
            h1 {
                font-size: 1.5rem;
            }
            
            .auth-subtitle {
                font-size: 0.85rem;
                margin-bottom: 1.5rem;
            }
            
            .form-label {
                font-size: 0.8125rem;
            }
            
            .form-control {
                padding: 0.625rem 0.875rem;
                font-size: 0.9rem;
            }
            
            .btn-primary {
                padding: 0.625rem 1.25rem;
                font-size: 0.9rem;
            }
            
            .auth-links {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
        }
        
        @media (max-width: 480px) {
            .auth-card {
                padding: 1.75rem 1.25rem;
            }
            
            .auth-logo img {
                height: 32px;
            }
            
            .logo-divider {
                height: 24px;
            }
            
            h1 {
                font-size: 1.5rem;
            }
            
            .logo-divider {
                height: 20px;
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
                <p class="auth-subtitle">Sign in to your OPTIMA account</p>
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
                
                <div class="mb-3">
                    <label for="username" class="form-label">
                        <i class="fas fa-user me-1"></i> Username or Email
                    </label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="username" 
                        name="username" 
                        placeholder="Enter username or email"
                        required 
                        autofocus
                        value="<?= old('username') ?>"
                    >
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock me-1"></i> Password
                    </label>
                    <div class="input-group">
                        <input 
                            type="password" 
                            class="form-control" 
                            id="password" 
                            name="password" 
                            placeholder="Enter password"
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
                        Remember me
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt me-2"></i> Sign In
                </button>
            </form>
            
            <div class="auth-links">
                <a href="<?= base_url('auth/forgot-password') ?>">
                    <i class="fas fa-key me-1"></i> Forgot Password?
                </a>
                <a href="<?= base_url('auth/register') ?>">
                    <i class="fas fa-user-plus me-1"></i> Create New Account
                </a>
            </div>
        </div>
    </div>
    
    <!-- Login Loading Modal -->
    <div class="login-loading-overlay" id="loginLoadingModal">
        <div class="login-loading-modal">
            <div class="login-loading-logo">
                <img src="<?= base_url('assets/images/logo-optima.png') ?>" alt="OPTIMA">
            </div>
            <div class="login-loading-text">Signing in…</div>
            <div class="login-loading-subtitle">Verifying your account.</div>
            <div class="login-loading-bars">
                <span class="login-loading-bar"></span>
                <span class="login-loading-bar"></span>
                <span class="login-loading-bar"></span>
                <span class="login-loading-bar"></span>
                <span class="login-loading-bar"></span>
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
        
        // Prevent double submission and show loading modal
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            const loadingModal = document.getElementById('loginLoadingModal');
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processing…';
            
            // Show loading modal after short delay
            setTimeout(() => {
                loadingModal.classList.add('show');
            }, 100);
        });
    </script>
</body>
</html>
