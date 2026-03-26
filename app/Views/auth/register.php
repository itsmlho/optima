<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Create new account at OPTIMA - Forklift Rental Management System by PT Sarana Mitra Luas Tbk">
    <meta name="robots" content="noindex, nofollow">
    
    <title>Create New Account | PT Sarana Mitra Luas Tbk</title>
    
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
        /* Register page - matches login page style */
        * { box-sizing: border-box; }

        body {
            background: #f5f7fa;
            font-family: 'Metropolis', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 2rem 1rem;
            margin: 0;
        }

        .auth-container {
            width: 100%;
            max-width: 840px;
        }

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

        .auth-title {
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

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.4rem;
            font-size: 0.875rem;
        }

        .form-label i { color: #0061f2; }

        .form-control,
        .form-select {
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 0.65rem 1rem;
            font-size: 0.925rem;
            transition: border-color 0.2s, box-shadow 0.2s;
            background: #ffffff;
            color: #495057;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #0061f2;
            box-shadow: 0 0 0 0.15rem rgba(0,97,242,0.12);
            outline: none;
            background: white;
        }

        .form-group { margin-bottom: 1rem; }

        .btn-primary {
            background: #0061f2;
            border: none;
            border-radius: 6px;
            padding: 0.7rem 1.5rem;
            font-weight: 600;
            font-size: 0.95rem;
            transition: background 0.2s, box-shadow 0.2s;
            color: white;
            cursor: pointer;
        }

        .btn-primary:hover {
            background: #0056b3;
            box-shadow: 0 2px 8px rgba(0,97,242,0.25);
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
        
        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            padding: 0.5rem 0.75rem;
            z-index: 10;
            min-width: 44px;
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
        
        .required {
            color: #dc3545;
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
        
        /* Mobile adjustments for logo */
        @media (max-width: 576px) {
            .auth-logo img {
                height: 28px;
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
                <h1 class="auth-title">Create New Account</h1>
                <p class="auth-subtitle">Create your OPTIMA account to join the team</p>
            </div>
            
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fas fa-exclamation-circle" style="font-size: 1.25rem; margin-top: 2px;"></i>
                        <div class="flex-grow-1">
                            <strong>Registration failed!</strong><br>
                            <?= session()->getFlashdata('error') ?>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php $validation = \Config\Services::validation(); ?>
            <?php if ($validation->getErrors()): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fas fa-exclamation-triangle" style="font-size: 1.25rem; margin-top: 2px;"></i>
                        <div class="flex-grow-1">
                            <strong>Attention!</strong>
                            <ul class="mb-0 mt-2" style="padding-left: 1.25rem;">
                                <?php foreach ($validation->getErrors() as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            <?php endif; ?>
            
            <form action="<?= base_url('auth/attempt-register') ?>" method="POST" id="registerForm">
                <?= csrf_field() ?>
                
                <!-- Personal Information -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="first_name" class="form-label">
                                <i class="fas fa-user me-1"></i> First Name <span class="required">*</span>
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
                                Last Name <span class="required">*</span>
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
                                <i class="fas fa-phone me-1"></i> Phone Number
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
                    <small class="form-text">Minimum 3 characters, only letters, numbers, and underscores</small>
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
                                    placeholder="At least 8 characters"
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
                                Confirm Password <span class="required">*</span>
                            </label>
                            <div class="input-group">
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    id="password_confirm" 
                                    name="password_confirm" 
                                    placeholder="Repeat password"
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
                                <i class="fas fa-building me-1"></i> Division <span class="required">*</span>
                            </label>
                            <select class="form-select" id="division" name="division_id" required>
                                <option value="">Select Division</option>
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
                            <select class="form-select" id="role" name="position" required disabled>
                                <option value="">Select division first</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Terms & Conditions -->
                <div class="form-group">
                    <div class="form-check">
                        <input 
                            type="checkbox" 
                            class="form-check-input" 
                            id="terms" 
                            name="terms" 
                            value="1"
                            required
                        >
                        <label class="form-check-label" for="terms">
                            I agree to the <a href="#" onclick="if(window.OptimaNotify) OptimaNotify.info(registerI18n.termsNotice); return false;">Terms &amp; Conditions</a> <span class="required">*</span>
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-user-plus me-2"></i> Register Now
                </button>
            </form>
            
            <div class="auth-links">
                Already have an account? 
                <a href="<?= base_url('auth/login') ?>">
                    <i class="fas fa-sign-in-alt me-1"></i> Login here
                </a>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
const registerI18n = {
    termsNotice: 'Terms & Conditions',
    loadingRoles: 'Loading roles…',
    selectRole: 'Select role',
    noRolesForDivision: 'No roles available for this division',
    rolesLoadError: 'Failed to load roles — try again',
    selectDivisionFirst: 'Select division first',
    strengthWeak: 'Weak',
    strengthFair: 'Fair',
    strengthGood: 'Good',
    strengthStrong: 'Strong',
    matchOk: '✓ Passwords match',
    matchBad: '✗ Passwords do not match',
    errMismatch: 'Password and confirmation do not match.',
    errShort: 'Password must be at least 8 characters.',
    errDivision: 'Please select a division.',
    errRole: 'Please select a role.',
    errTerms: 'You must agree to the terms and conditions',
    processingBtn: 'Submitting registration…',
    pleaseWait: 'Submitting your registration, please wait…',
};

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
                    strengthText.textContent = registerI18n.strengthWeak;
                    strengthText.style.color = '#dc3545';
                    break;
                case 2:
                case 3:
                    strengthBar.style.width = '50%';
                    strengthBar.className = 'password-strength-bar strength-fair';
                    strengthText.textContent = registerI18n.strengthFair;
                    strengthText.style.color = '#ffc107';
                    break;
                case 4:
                    strengthBar.style.width = '75%';
                    strengthBar.className = 'password-strength-bar strength-good';
                    strengthText.textContent = registerI18n.strengthGood;
                    strengthText.style.color = '#17a2b8';
                    break;
                case 5:
                    strengthBar.style.width = '100%';
                    strengthBar.className = 'password-strength-bar strength-strong';
                    strengthText.textContent = registerI18n.strengthStrong;
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
                    matchText.textContent = registerI18n.matchOk;
                    matchText.style.color = '#28a745';
                } else {
                    matchText.textContent = registerI18n.matchBad;
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

            // Get CSRF token from meta or existing form field
            const csrfToken = document.querySelector('input[name="<?= csrf_token() ?>"]')?.value || '';
            const csrfName  = '<?= csrf_token() ?>';
            
            if (divisionId) {
                roleSelect.innerHTML = '<option value="">' + registerI18n.loadingRoles + '</option>';
                roleSelect.disabled = true;

                fetch('<?= base_url('auth/get-positions-by-division') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: `division_id=${encodeURIComponent(divisionId)}&${encodeURIComponent(csrfName)}=${encodeURIComponent(csrfToken)}`
                })
                .then(function(response) {
                    if (!response.ok) throw new Error('HTTP ' + response.status);
                    return response.json();
                })
                .then(function(data) {
                    roleSelect.innerHTML = '<option value="">' + registerI18n.selectRole + '</option>';
                    if (data.success && data.positions && data.positions.length > 0) {
                        data.positions.forEach(function(position) {
                            const opt = document.createElement('option');
                            opt.value = position.name;
                            opt.textContent = position.name;
                            roleSelect.appendChild(opt);
                        });
                        roleSelect.disabled = false;
                    } else {
                        roleSelect.innerHTML = '<option value="">' + registerI18n.noRolesForDivision + '</option>';
                        roleSelect.disabled = true;
                    }
                })
                .catch(function(error) {
                    console.error('Error loading positions:', error);
                    roleSelect.innerHTML = '<option value="">' + registerI18n.rolesLoadError + '</option>';
                    roleSelect.disabled = false;
                });
            } else {
                roleSelect.innerHTML = '<option value="">' + registerI18n.selectDivisionFirst + '</option>';
                roleSelect.disabled = true;
            }
        });
        
        // Form submission handling
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirm').value;
            const division = document.getElementById('division').value;
            const role = document.getElementById('role').value;
            const terms = document.getElementById('terms').checked;
            
            // Validate password match
            if (password !== confirmPassword) {
                e.preventDefault();
                if (window.OptimaNotify) OptimaNotify.error(registerI18n.errMismatch);
                else alert(registerI18n.errMismatch);
                return false;
            }
            
            // Validate password strength
            if (password.length < 8) {
                e.preventDefault();
                if (window.OptimaNotify) OptimaNotify.error(registerI18n.errShort);
                else alert(registerI18n.errShort);
                return false;
            }
            
            // Validate division
            if (!division) {
                e.preventDefault();
                if (window.OptimaNotify) OptimaNotify.warning(registerI18n.errDivision);
                else alert(registerI18n.errDivision);
                return false;
            }
            
            // Validate role
            if (!role) {
                e.preventDefault();
                if (window.OptimaNotify) OptimaNotify.warning(registerI18n.errRole);
                else alert(registerI18n.errRole);
                return false;
            }
            
            // Validate terms
            if (!terms) {
                e.preventDefault();
                if (window.OptimaNotify) OptimaNotify.warning(registerI18n.errTerms);
                else alert(registerI18n.errTerms);
                return false;
            }
            
            // Prevent double submission
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> ' + registerI18n.processingBtn;
            
            // Show loading indicator
            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'alert alert-info mt-3';
            loadingDiv.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> ' + registerI18n.pleaseWait;
            this.insertBefore(loadingDiv, submitBtn);
        });
    </script>
</body>
</html>
