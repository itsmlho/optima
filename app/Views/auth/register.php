<!DOCTYPE html>
<html lang="<?= service('request')->getLocale() ?>" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Register to OPTIMA - PT Sarana Mitra Luas Tbk">
    <meta name="robots" content="noindex, nofollow">
    
    <title>Daftar Akun - OPTIMA | PT Sarana Mitra Luas Tbk</title>
    
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
            max-width: 520px;
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
            margin-bottom: 1.25rem;
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
            font-size: 0.875rem;
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
        
        .required {
            color: #dc3545;
        }
        
        .form-control, .form-select {
            padding: 0.675rem 0.875rem;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
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
        
        .password-strength {
            height: 4px;
            border-radius: 2px;
            margin-top: 0.5rem;
            background: #e9ecef;
            overflow: hidden;
        }
        
        .password-strength-bar {
            height: 100%;
            transition: all 0.3s ease;
            width: 0;
        }
        
        .password-strength-text {
            font-size: 0.8rem;
            margin-top: 0.25rem;
        }
        
        .strength-weak { background: #dc3545; }
        .strength-fair { background: #ffc107; }
        .strength-good { background: #17a2b8; }
        .strength-strong { background: #28a745; }
        
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
        
        .form-text {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }
        
        .row {
            margin-left: -0.5rem;
            margin-right: -0.5rem;
        }
        
        .row > * {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
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
        
        [data-bs-theme="dark"] .form-control,
        [data-bs-theme="dark"] .form-select {
            background: #343a40;
            border-color: #495057;
            color: #e2e8f0;
        }
        
        [data-bs-theme="dark"] .form-control:focus,
        [data-bs-theme="dark"] .form-select:focus {
            background: #343a40;
            border-color: #0061f2;
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
                <h1 class="auth-title">Daftar Akun Baru</h1>
                <p class="auth-subtitle">Buat akun OPTIMA untuk bergabung dengan tim</p>
            </div>
            
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>
            
            <?php $validation = \Config\Services::validation(); ?>
            <?php if ($validation->getErrors()): ?>
                <div class="alert alert-danger">
                    <?= validation_list_errors() ?>
                </div>
            <?php endif; ?>
            
            <form action="<?= base_url('auth/attempt-register') ?>" method="POST" id="registerForm">
                <?= csrf_field() ?>
                
                <!-- Personal Information -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="first_name" class="form-label">
                                <i class="fas fa-user me-1"></i> Nama Depan <span class="required">*</span>
                            </label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="first_name" 
                                name="first_name" 
                                placeholder="John"
                                required
                                value="<?= old('first_name') ?>"
                            >
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="last_name" class="form-label">
                                Nama Belakang <span class="required">*</span>
                            </label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="last_name" 
                                name="last_name" 
                                placeholder="Doe"
                                required
                                value="<?= old('last_name') ?>"
                            >
                        </div>
                    </div>
                </div>
                
                <!-- Contact Information -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-1"></i> Email <span class="required">*</span>
                            </label>
                            <input 
                                type="email" 
                                class="form-control" 
                                id="email" 
                                name="email" 
                                placeholder="john.doe@example.com"
                                required
                                value="<?= old('email') ?>"
                            >
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="phone" class="form-label">
                                <i class="fas fa-phone me-1"></i> No. Telepon
                            </label>
                            <input 
                                type="tel" 
                                class="form-control" 
                                id="phone" 
                                name="phone" 
                                placeholder="08123456789"
                                value="<?= old('phone') ?>"
                            >
                        </div>
                    </div>
                </div>
                
                <!-- Username -->
                <div class="form-group">
                    <label for="username" class="form-label">
                        <i class="fas fa-at me-1"></i> Username <span class="required">*</span>
                    </label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="username" 
                        name="username" 
                        placeholder="johndoe"
                        required
                        value="<?= old('username') ?>"
                    >
                    <small class="form-text">Username hanya boleh huruf, angka, dan underscore (_)</small>
                </div>
                
                <!-- Password -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-1"></i> Password <span class="required">*</span>
                            </label>
                            <div class="input-group">
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    id="password" 
                                    name="password" 
                                    placeholder="Minimal 8 karakter"
                                    required
                                    onkeyup="checkPasswordStrength()"
                                >
                                <button type="button" class="password-toggle" onclick="togglePassword('password', 'toggleIcon1')">
                                    <i class="fas fa-eye" id="toggleIcon1"></i>
                                </button>
                            </div>
                            <div class="password-strength">
                                <div class="password-strength-bar" id="strengthBar"></div>
                            </div>
                            <small class="password-strength-text" id="strengthText"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password_confirm" class="form-label">
                                Konfirmasi Password <span class="required">*</span>
                            </label>
                            <div class="input-group">
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    id="password_confirm" 
                                    name="password_confirm" 
                                    placeholder="Ulangi password"
                                    required
                                    onkeyup="checkPasswordMatch()"
                                >
                                <button type="button" class="password-toggle" onclick="togglePassword('password_confirm', 'toggleIcon2')">
                                    <i class="fas fa-eye" id="toggleIcon2"></i>
                                </button>
                            </div>
                            <small class="form-text" id="matchText"></small>
                        </div>
                    </div>
                </div>
                
                <!-- Division & Role -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="division" class="form-label">
                                <i class="fas fa-building me-1"></i> Divisi <span class="required">*</span>
                            </label>
                            <select class="form-select" id="division" name="division" required>
                                <option value="">Pilih Divisi</option>
                                <?php if (isset($divisions) && !empty($divisions)): ?>
                                    <?php foreach ($divisions as $divisionId => $divisionName): ?>
                                        <option value="<?= $divisionId ?>" <?= old('division') == $divisionId ? 'selected' : '' ?>>
                                            <?= esc($divisionName) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="role" class="form-label">
                                <i class="fas fa-user-tag me-1"></i> Role <span class="required">*</span>
                            </label>
                            <select class="form-select" id="role" name="role" required disabled>
                                <option value="">Pilih Divisi Dahulu</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-user-plus me-2"></i> Daftar Sekarang
                </button>
            </form>
            
            <div class="auth-links">
                Sudah punya akun? 
                <a href="<?= base_url('auth/login') ?>">
                    <i class="fas fa-sign-in-alt me-1"></i> Masuk di sini
                </a>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Toggle password visibility
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        
        // Check password strength
        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthBar = document.getElementById('strengthBar');
            const strengthText = document.getElementById('strengthText');
            
            let strength = 0;
            
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]+/)) strength++;
            if (password.match(/[A-Z]+/)) strength++;
            if (password.match(/[0-9]+/)) strength++;
            if (password.match(/[$@#&!]+/)) strength++;
            
            switch(strength) {
                case 0:
                case 1:
                    strengthBar.style.width = '25%';
                    strengthBar.className = 'password-strength-bar strength-weak';
                    strengthText.textContent = 'Lemah';
                    strengthText.style.color = '#dc3545';
                    break;
                case 2:
                case 3:
                    strengthBar.style.width = '50%';
                    strengthBar.className = 'password-strength-bar strength-fair';
                    strengthText.textContent = 'Cukup';
                    strengthText.style.color = '#ffc107';
                    break;
                case 4:
                    strengthBar.style.width = '75%';
                    strengthBar.className = 'password-strength-bar strength-good';
                    strengthText.textContent = 'Baik';
                    strengthText.style.color = '#17a2b8';
                    break;
                case 5:
                    strengthBar.style.width = '100%';
                    strengthBar.className = 'password-strength-bar strength-strong';
                    strengthText.textContent = 'Kuat';
                    strengthText.style.color = '#28a745';
                    break;
            }
            
            checkPasswordMatch();
        }
        
        // Check password match
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirm').value;
            const matchText = document.getElementById('matchText');
            
            if (confirmPassword.length > 0) {
                if (password === confirmPassword) {
                    matchText.textContent = '✓ Password cocok';
                    matchText.style.color = '#28a745';
                } else {
                    matchText.textContent = '✗ Password tidak cocok';
                    matchText.style.color = '#dc3545';
                }
            } else {
                matchText.textContent = '';
            }
        }
        
        // Load roles when division changes
        document.getElementById('division').addEventListener('change', function() {
            const divisionId = this.value;
            const roleSelect = document.getElementById('role');
            
            if (divisionId) {
                fetch(`<?= base_url('auth/get-positions-by-division') ?>`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: `division_id=${divisionId}`
                })
                    .then(response => response.json())
                    .then(data => {
                        roleSelect.innerHTML = '<option value="">Pilih Role</option>';
                        
                        if (data.success && data.positions) {
                            data.positions.forEach(position => {
                                roleSelect.innerHTML += `<option value="${position.id}">${position.name}</option>`;
                            });
                            roleSelect.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error loading positions:', error);
                        roleSelect.disabled = true;
                    });
            } else {
                roleSelect.innerHTML = '<option value="">Pilih Divisi Dahulu</option>';
                roleSelect.disabled = true;
            }
        });
        
        // Prevent double submission
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirm').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Password dan konfirmasi password tidak cocok!');
                return false;
            }
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Memproses...';
        });
    </script>
</body>
</html>
