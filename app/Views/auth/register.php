<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Daftar akun baru di OPTIMA - Sistem Manajemen Penyewaan Forklift PT Sarana Mitra Luas Tbk">
    <meta name="robots" content="noindex, nofollow">
    
    <!-- Title -->
    <title>Daftar Akun - OPTIMA | PT Sarana Mitra Luas Tbk</title>
    
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
        
        .register-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            box-shadow: 0 2rem 4rem rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 1000px;
            width: 100%;
        }
        
        .register-brand {
            background: linear-gradient(135deg, #00ac69 0%, #4dd289 100%);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .register-brand::before {
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
        
        .register-brand-content {
            position: relative;
            z-index: 2;
        }
        
        .register-logo {
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
        
        .register-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            text-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.1);
        }
        
        .register-subtitle {
            font-size: 1.125rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }
        
        .register-benefits {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .register-benefit {
            display: flex;
            align-items: center;
            font-size: 0.875rem;
            opacity: 0.9;
        }
        
        .register-benefit i {
            margin-right: 0.75rem;
            width: 1.5rem;
            text-align: center;
            font-size: 1rem;
        }
        
        .register-form {
            padding: 3rem 2rem;
        }
        
        .register-form-title {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .register-form-title h2 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        
        .register-form-title p {
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
            border-color: #00ac69;
            box-shadow: 0 0 0 0.2rem rgba(0, 172, 105, 0.25);
        }
        
        .form-floating .form-control.is-valid {
            border-color: #00ac69;
            padding-right: 2.5rem;
        }
        
        .form-floating .form-control.is-invalid {
            border-color: #e81500;
            padding-right: 2.5rem;
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
            background-color: #00ac69;
            border-color: #00ac69;
        }
        
        .form-check-label {
            font-weight: 500;
            color: #495057;
            margin-left: 0.5rem;
            font-size: 0.875rem;
        }
        
        .form-check-label a {
            color: #00ac69;
            text-decoration: none;
        }
        
        .form-check-label a:hover {
            color: #008f57;
            text-decoration: underline;
        }
        
        .password-strength {
            margin-top: 0.5rem;
            font-size: 0.75rem;
        }
        
        .password-strength-bar {
            height: 4px;
            background: #e9ecef;
            border-radius: 2px;
            overflow: hidden;
            margin: 0.5rem 0;
        }
        
        .password-strength-fill {
            height: 100%;
            transition: width 0.3s ease, background-color 0.3s ease;
            width: 0;
            background-color: #e81500;
        }
        
        .password-strength-fill.weak {
            width: 25%;
            background-color: #e81500;
        }
        
        .password-strength-fill.fair {
            width: 50%;
            background-color: #ffb607;
        }
        
        .password-strength-fill.good {
            width: 75%;
            background-color: #39afd1;
        }
        
        .password-strength-fill.strong {
            width: 100%;
            background-color: #00ac69;
        }
        
        .password-requirements {
            font-size: 0.75rem;
            color: #69707a;
            margin-top: 0.5rem;
        }
        
        .password-requirements ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .password-requirements li {
            padding: 0.125rem 0;
            display: flex;
            align-items: center;
        }
        
        .password-requirements li i {
            margin-right: 0.5rem;
            width: 1rem;
            text-align: center;
            font-size: 0.75rem;
        }
        
        .password-requirements li.valid {
            color: #00ac69;
        }
        
        .password-requirements li.invalid {
            color: #e81500;
        }
        
        .btn-register {
            width: 100%;
            padding: 1rem;
            font-size: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 0.5rem;
            background: linear-gradient(135deg, #00ac69 0%, #4dd289 100%);
            border: none;
            color: white;
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
        }
        
        .btn-register:hover {
            background: linear-gradient(135deg, #008f57 0%, #00ac69 100%);
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 172, 105, 0.3);
        }
        
        .btn-register:active {
            transform: translateY(0);
        }
        
        .btn-register:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }
        
        .register-links {
            text-align: center;
            margin-top: 2rem;
        }
        
        .register-links a {
            color: #00ac69;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .register-links a:hover {
            color: #008f57;
        }
        
        .register-divider {
            display: flex;
            align-items: center;
            margin: 2rem 0;
        }
        
        .register-divider::before,
        .register-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e9ecef;
        }
        
        .register-divider span {
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
            color: #00ac69;
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
            .register-container {
                margin: 1rem;
            }
            
            .register-brand {
                padding: 2rem 1rem;
            }
            
            .register-title {
                font-size: 2rem;
            }
            
            .register-form {
                padding: 2rem 1rem;
            }
        }
        
        /* Dark Mode Support */
        [data-bs-theme="dark"] .register-container {
            background: rgba(33, 37, 41, 0.95);
        }
        
        [data-bs-theme="dark"] .register-form-title h2 {
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
        
        [data-bs-theme="dark"] .password-requirements {
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
    <div class="register-container row g-0">
        <!-- Brand Section -->
        <div class="col-lg-5 d-none d-lg-block">
            <div class="register-brand h-100">
                <div class="register-brand-content">
                    <div class="register-logo">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h1 class="register-title">Bergabung</h1>
                    <p class="register-subtitle">Dengan OPTIMA</p>
                    <p class="mb-0">PT Sarana Mitra Luas Tbk</p>
                    
                    <div class="register-benefits">
                        <div class="register-benefit">
                            <i class="fas fa-check-circle"></i>
                            <span>Akses penuh ke sistem manajemen forklift</span>
                        </div>
                        <div class="register-benefit">
                            <i class="fas fa-chart-bar"></i>
                            <span>Dashboard analitik real-time</span>
                        </div>
                        <div class="register-benefit">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Penjadwalan pemeliharaan otomatis</span>
                        </div>
                        <div class="register-benefit">
                            <i class="fas fa-file-invoice-dollar"></i>
                            <span>Laporan keuangan terintegrasi</span>
                        </div>
                        <div class="register-benefit">
                            <i class="fas fa-headset"></i>
                            <span>Dukungan teknis 24/7</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Form Section -->
        <div class="col-lg-7">
            <div class="register-form">
                <div class="register-form-title">
                    <h2>Buat Akun Baru</h2>
                    <p>Lengkapi informasi di bawah ini untuk membuat akun</p>
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
                
                <!-- Registration Form -->
                <form action="<?= base_url('auth/attempt-register') ?>" method="post" class="needs-validation" novalidate>
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="first_name" name="first_name" 
                                       placeholder="Nama Depan" value="<?= old('first_name') ?>" required>
                                <label for="first_name">
                                    <i class="fas fa-user me-2"></i>Nama Depan
                                </label>
                                <div class="invalid-feedback">
                                    Silakan masukkan nama depan.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="last_name" name="last_name" 
                                       placeholder="Nama Belakang" value="<?= old('last_name') ?>" required>
                                <label for="last_name">
                                    <i class="fas fa-user me-2"></i>Nama Belakang
                                </label>
                                <div class="invalid-feedback">
                                    Silakan masukkan nama belakang.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-floating">
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder="name@example.com" value="<?= old('email') ?>" required>
                        <label for="email">
                            <i class="fas fa-envelope me-2"></i>Email
                        </label>
                        <div class="invalid-feedback">
                            Silakan masukkan email yang valid.
                        </div>
                    </div>
                    
                    <div class="form-floating">
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Password" required>
                        <label for="password">
                            <i class="fas fa-lock me-2"></i>Password
                        </label>
                        <div class="invalid-feedback">
                            Silakan masukkan password.
                        </div>
                        <div class="password-strength">
                            <div class="password-strength-bar">
                                <div class="password-strength-fill" id="passwordStrengthFill"></div>
                            </div>
                            <div class="password-strength-text" id="passwordStrengthText">Kekuatan password: -</div>
                        </div>
                        <div class="password-requirements">
                            <ul>
                                <li id="lengthReq" class="invalid">
                                    <i class="fas fa-times"></i>
                                    <span>Minimal 8 karakter</span>
                                </li>
                                <li id="upperReq" class="invalid">
                                    <i class="fas fa-times"></i>
                                    <span>Minimal 1 huruf besar</span>
                                </li>
                                <li id="lowerReq" class="invalid">
                                    <i class="fas fa-times"></i>
                                    <span>Minimal 1 huruf kecil</span>
                                </li>
                                <li id="numberReq" class="invalid">
                                    <i class="fas fa-times"></i>
                                    <span>Minimal 1 angka</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="form-floating">
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                               placeholder="Konfirmasi Password" required>
                        <label for="confirm_password">
                            <i class="fas fa-lock me-2"></i>Konfirmasi Password
                        </label>
                        <div class="invalid-feedback">
                            Password tidak cocok.
                        </div>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="terms" name="terms" required>
                        <label class="form-check-label" for="terms">
                            Saya menyetujui <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Syarat & Ketentuan</a> 
                            serta <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal">Kebijakan Privasi</a>
                        </label>
                        <div class="invalid-feedback">
                            Anda harus menyetujui syarat dan ketentuan.
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-register" id="registerBtn">
                        <div class="loading-spinner" id="registerSpinner"></div>
                        <span id="registerText">Buat Akun</span>
                    </button>
                </form>
                
                <div class="register-links">
                    <div class="register-divider">
                        <span>atau</span>
                    </div>
                    
                    <p class="mb-0">Sudah punya akun? <a href="<?= base_url('auth/login') ?>">Masuk disini</a></p>
                </div>
            </div>
            
            <div class="footer-links">
                <a href="#">Bantuan</a>
                <a href="#">Kebijakan Privasi</a>
                <a href="#">Syarat & Ketentuan</a>
            </div>
        </div>
    </div>
    
    <!-- Terms Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="termsModalLabel">Syarat & Ketentuan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-file-contract fa-3x text-primary mb-3"></i>
                        <h4>Syarat & Ketentuan Penggunaan</h4>
                        <p class="text-muted">PT Sarana Mitra Luas Tbk - OPTIMA</p>
                    </div>
                    
                    <div class="terms-content">
                        <h6>1. Penerimaan Syarat</h6>
                        <p>Dengan mendaftar dan menggunakan sistem OPTIMA, Anda menyetujui untuk terikat oleh syarat dan ketentuan ini.</p>
                        
                        <h6>2. Penggunaan Layanan</h6>
                        <p>Sistem OPTIMA disediakan untuk keperluan manajemen penyewaan forklift dan peralatan terkait.</p>
                        
                        <h6>3. Akun Pengguna</h6>
                        <p>Anda bertanggung jawab untuk menjaga keamanan akun dan password Anda.</p>
                        
                        <h6>4. Privasi Data</h6>
                        <p>Data pribadi Anda akan dijaga kerahasiaannya sesuai dengan kebijakan privasi kami.</p>
                        
                        <h6>5. Pembatasan Penggunaan</h6>
                        <p>Dilarang menggunakan sistem untuk kegiatan yang melanggar hukum atau merugikan pihak lain.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Privacy Modal -->
    <div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="privacyModalLabel">Kebijakan Privasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-shield-alt fa-3x text-success mb-3"></i>
                        <h4>Kebijakan Privasi</h4>
                        <p class="text-muted">PT Sarana Mitra Luas Tbk - OPTIMA</p>
                    </div>
                    
                    <div class="privacy-content">
                        <h6>1. Informasi yang Kami Kumpulkan</h6>
                        <p>Kami mengumpulkan informasi yang Anda berikan saat mendaftar dan menggunakan layanan kami.</p>
                        
                        <h6>2. Penggunaan Informasi</h6>
                        <p>Informasi digunakan untuk menyediakan layanan, komunikasi, dan peningkatan sistem.</p>
                        
                        <h6>3. Pembagian Informasi</h6>
                        <p>Kami tidak membagikan informasi pribadi Anda kepada pihak ketiga tanpa persetujuan.</p>
                        
                        <h6>4. Keamanan Data</h6>
                        <p>Kami menggunakan langkah-langkah keamanan untuk melindungi data Anda.</p>
                        
                        <h6>5. Hak Pengguna</h6>
                        <p>Anda memiliki hak untuk mengakses, memperbarui, atau menghapus data pribadi Anda.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
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
                        const registerBtn = document.getElementById('registerBtn');
                        const registerSpinner = document.getElementById('registerSpinner');
                        const registerText = document.getElementById('registerText');
                        
                        registerBtn.disabled = true;
                        registerSpinner.style.display = 'inline-block';
                        registerText.textContent = 'Memproses...';
                    }
                    
                    form.classList.add('was-validated');
                }, false);
            });
        })();
        
        // Password strength checker
        function checkPasswordStrength(password) {
            const strengthFill = document.getElementById('passwordStrengthFill');
            const strengthText = document.getElementById('passwordStrengthText');
            
            let score = 0;
            let strength = '';
            
            // Length check
            if (password.length >= 8) score++;
            
            // Uppercase check
            if (/[A-Z]/.test(password)) score++;
            
            // Lowercase check
            if (/[a-z]/.test(password)) score++;
            
            // Number check
            if (/\d/.test(password)) score++;
            
            // Special character check
            if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) score++;
            
            // Update strength indicator
            strengthFill.className = 'password-strength-fill';
            
            switch (score) {
                case 0:
                case 1:
                    strength = 'Sangat Lemah';
                    strengthFill.classList.add('weak');
                    break;
                case 2:
                    strength = 'Lemah';
                    strengthFill.classList.add('weak');
                    break;
                case 3:
                    strength = 'Sedang';
                    strengthFill.classList.add('fair');
                    break;
                case 4:
                    strength = 'Kuat';
                    strengthFill.classList.add('good');
                    break;
                case 5:
                    strength = 'Sangat Kuat';
                    strengthFill.classList.add('strong');
                    break;
            }
            
            strengthText.textContent = `Kekuatan password: ${strength}`;
            
            return score;
        }
        
        // Password requirements checker
        function checkPasswordRequirements(password) {
            const lengthReq = document.getElementById('lengthReq');
            const upperReq = document.getElementById('upperReq');
            const lowerReq = document.getElementById('lowerReq');
            const numberReq = document.getElementById('numberReq');
            
            // Length requirement
            if (password.length >= 8) {
                lengthReq.className = 'valid';
                lengthReq.querySelector('i').className = 'fas fa-check';
            } else {
                lengthReq.className = 'invalid';
                lengthReq.querySelector('i').className = 'fas fa-times';
            }
            
            // Uppercase requirement
            if (/[A-Z]/.test(password)) {
                upperReq.className = 'valid';
                upperReq.querySelector('i').className = 'fas fa-check';
            } else {
                upperReq.className = 'invalid';
                upperReq.querySelector('i').className = 'fas fa-times';
            }
            
            // Lowercase requirement
            if (/[a-z]/.test(password)) {
                lowerReq.className = 'valid';
                lowerReq.querySelector('i').className = 'fas fa-check';
            } else {
                lowerReq.className = 'invalid';
                lowerReq.querySelector('i').className = 'fas fa-times';
            }
            
            // Number requirement
            if (/\d/.test(password)) {
                numberReq.className = 'valid';
                numberReq.querySelector('i').className = 'fas fa-check';
            } else {
                numberReq.className = 'invalid';
                numberReq.querySelector('i').className = 'fas fa-times';
            }
        }
        
        // Password input event listener
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            checkPasswordStrength(password);
            checkPasswordRequirements(password);
        });
        
        // Confirm password validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('Password tidak cocok');
            } else {
                this.setCustomValidity('');
            }
        });
        
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
        
        // Email validation
        document.getElementById('email').addEventListener('blur', function() {
            const email = this.value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (email && !emailRegex.test(email)) {
                this.setCustomValidity('Format email tidak valid');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html> 