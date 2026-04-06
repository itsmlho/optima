<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Reset your OPTIMA account password - Forklift Rental Management System by PT Sarana Mitra Luas Tbk">
    <meta name="robots" content="noindex, nofollow">
    
    <title>Reset Password | PT Sarana Mitra Luas Tbk</title>
    
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
        .auth-subtitle {
            text-align: center; color: #6c757d;
            font-size: 0.875rem; margin-bottom: 1.5rem;
        }
        .form-label { font-weight: 600; color: #495057; font-size: 0.875rem; margin-bottom: 0.4rem; }
        .form-label i { color: #0061f2; }
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
        .input-group .form-control { border-radius: 6px 0 0 6px; }
        .input-group .btn-outline-secondary {
            border-color: #dee2e6; color: #6c757d;
            border-radius: 0 6px 6px 0;
            background: #f8f9fa;
        }
        .input-group .btn-outline-secondary:hover { background: #e9ecef; }
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
        .password-strength { margin-top: 0.4rem; }
        .strength-bar {
            height: 4px; border-radius: 2px;
            background: #e9ecef; overflow: hidden; margin-bottom: 0.25rem;
        }
        .strength-fill {
            height: 100%; border-radius: 2px;
            transition: width 0.3s, background 0.3s;
            width: 0%;
        }
        .strength-text { font-size: 0.75rem; color: #6c757d; }
        .email-info {
            background: #f8f9fa; border-left: 3px solid #0061f2;
            padding: 0.75rem 1rem; border-radius: 6px;
            margin-bottom: 1.25rem; font-size: 0.875rem;
            color: #495057;
        }
        .email-info i { color: #0061f2; }
        .email-info strong { color: #2c3e50; }
        .alert { border-radius: 8px; padding: 0.875rem 1rem; margin-bottom: 1rem; border: none; font-size: 0.9rem; }
        .alert-danger { background: #ffe5e5; color: #c92a2a; }
        .alert-success { background: #d4edda; color: #155724; }
        .invalid-feedback { font-size: 0.8rem; }
        @media (max-width: 576px) {
            .auth-logo img { height: 28px; }
            .logo-divider { height: 20px; }
            .auth-card { padding: 2rem 1.25rem; }
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
                    <i class="fas fa-lock-open"></i>
                </div>

                <h1 class="auth-title">Reset Password</h1>
                <p class="auth-subtitle">Enter your new password below.</p>
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($validation) && $validation): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?= $validation->listErrors() ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($email)): ?>
            <div class="email-info">
                <i class="fas fa-envelope me-2"></i>
                Resetting password for: <strong><?= esc($email) ?></strong>
            </div>
            <?php endif; ?>

            <form action="<?= base_url('auth/update-password') ?>" method="POST" id="resetPasswordForm" novalidate>
                <?= csrf_field() ?>
                <input type="hidden" name="token" value="<?= esc($token) ?>">

                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock me-1"></i> New Password
                    </label>
                    <div class="input-group">
                        <input
                            type="password"
                            class="form-control"
                            id="password"
                            name="password"
                            placeholder="Minimum 8 characters"
                            required
                            minlength="8"
                            autocomplete="new-password"
                        >
                        <button type="button" class="btn btn-outline-secondary" id="togglePassword" tabindex="-1">
                            <i class="fas fa-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                    <div class="password-strength mt-2">
                        <div class="strength-bar">
                            <div class="strength-fill" id="strengthFill"></div>
                        </div>
                        <span class="strength-text" id="strengthText"></span>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="confirm_password" class="form-label">
                        <i class="fas fa-lock me-1"></i> Confirm New Password
                    </label>
                    <div class="input-group">
                        <input
                            type="password"
                            class="form-control"
                            id="confirm_password"
                            name="confirm_password"
                            placeholder="Repeat your new password"
                            required
                            autocomplete="new-password"
                        >
                        <button type="button" class="btn btn-outline-secondary" id="toggleConfirm" tabindex="-1">
                            <i class="fas fa-eye" id="toggleConfirmIcon"></i>
                        </button>
                    </div>
                    <div class="invalid-feedback" id="confirmFeedback"></div>
                </div>

                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-check me-2"></i> Reset Password
                </button>

                <a href="<?= base_url('auth/login') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Back to Login
                </a>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function () {
            const input = document.getElementById('password');
            const icon = document.getElementById('togglePasswordIcon');
            const isText = input.type === 'text';
            input.type = isText ? 'password' : 'text';
            icon.classList.toggle('fa-eye', isText);
            icon.classList.toggle('fa-eye-slash', !isText);
        });

        document.getElementById('toggleConfirm').addEventListener('click', function () {
            const input = document.getElementById('confirm_password');
            const icon = document.getElementById('toggleConfirmIcon');
            const isText = input.type === 'text';
            input.type = isText ? 'password' : 'text';
            icon.classList.toggle('fa-eye', isText);
            icon.classList.toggle('fa-eye-slash', !isText);
        });

        // Password strength indicator
        document.getElementById('password').addEventListener('input', function () {
            const val = this.value;
            const fill = document.getElementById('strengthFill');
            const text = document.getElementById('strengthText');

            let score = 0;
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            const levels = [
                { width: '0%',   color: '#e9ecef', label: '' },
                { width: '25%',  color: '#dc3545', label: 'Weak' },
                { width: '50%',  color: '#fd7e14', label: 'Fair' },
                { width: '75%',  color: '#ffc107', label: 'Good' },
                { width: '100%', color: '#28a745', label: 'Strong' },
            ];

            const level = val.length === 0 ? levels[0] : levels[score];
            fill.style.width = level.width;
            fill.style.background = level.color;
            text.textContent = level.label;
            text.style.color = level.color;
        });

        // Confirm password match validation
        document.getElementById('confirm_password').addEventListener('input', function () {
            const password = document.getElementById('password').value;
            const feedback = document.getElementById('confirmFeedback');
            if (this.value && this.value !== password) {
                this.classList.add('is-invalid');
                feedback.textContent = 'Passwords do not match.';
                feedback.style.display = 'block';
            } else {
                this.classList.remove('is-invalid');
                feedback.style.display = 'none';
            }
        });

        // Prevent double submission + validate before submit
        document.getElementById('resetPasswordForm').addEventListener('submit', function (e) {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('confirm_password').value;

            if (password.length < 8) {
                e.preventDefault();
                document.getElementById('password').classList.add('is-invalid');
                return;
            }

            if (password !== confirm) {
                e.preventDefault();
                document.getElementById('confirm_password').classList.add('is-invalid');
                document.getElementById('confirmFeedback').textContent = 'Passwords do not match.';
                document.getElementById('confirmFeedback').style.display = 'block';
                return;
            }

            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Resetting…';
        });
    </script>
</body>
</html>
