<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="<?= lang('Auth.otp_title') ?> - OPTIMA">
    <meta name="robots" content="noindex, nofollow">
    
    <!-- Title -->
    <title><?= lang('Auth.otp_title') ?> - OPTIMA | PT Sarana Mitra Luas Tbk</title>
    
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
            background: linear-gradient(135deg, #f5f7fa 0%, #ffffff 50%, #f8f9fa 100%);
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            position: relative;
            overflow-x: hidden;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(0, 97, 242, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(77, 140, 255, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 40% 20%, rgba(0, 97, 242, 0.03) 0%, transparent 50%);
            background-size: 100% 100%;
            pointer-events: none;
            z-index: 0;
        }
        
        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(0, 97, 242, 0.02) 2px, rgba(0, 97, 242, 0.02) 4px);
            pointer-events: none;
            z-index: 0;
        }
        
        .otp-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 1.5rem;
            box-shadow: 
                0 8px 32px rgba(0, 97, 242, 0.1),
                0 2px 8px rgba(0, 0, 0, 0.05),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.5);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
            position: relative;
            z-index: 1;
        }
        
        .otp-container::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(0, 97, 242, 0.08) 0%, transparent 70%);
            animation: pulse 15s ease-in-out infinite;
            pointer-events: none;
            z-index: 0;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1) translate(0, 0); opacity: 0.5; }
            50% { transform: scale(1.1) translate(-5%, -5%); opacity: 0.8; }
        }
        
        .otp-header {
            background: linear-gradient(135deg, #0061f2 0%, #4d8cff 100%);
            color: white;
            padding: 2.5rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .otp-header-content {
            position: relative;
            z-index: 2;
        }
        
        .otp-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            backdrop-filter: blur(10px);
        }
        
        .otp-title {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .otp-subtitle {
            font-size: 0.95rem;
            opacity: 0.9;
        }
        
        .otp-body {
            padding: 2.5rem 2rem;
            position: relative;
            z-index: 2;
        }
        
        .otp-header {
            position: relative;
            z-index: 2;
        }
        
        .otp-info {
            background: #f8f9fa;
            border-left: 4px solid #0061f2;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }
        
        .otp-info strong {
            color: #0061f2;
        }
        
        .otp-input-group {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            margin-bottom: 1.5rem;
        }
        
        .otp-input {
            width: 100%;
            max-width: 300px;
            height: 70px;
            text-align: center;
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: 0.5rem;
            border: 2px solid #e9ecef;
            border-radius: 0.75rem;
            padding: 1rem;
            transition: all 0.3s ease;
        }
        
        .otp-input:focus {
            border-color: #0061f2;
            box-shadow: 0 0 0 0.2rem rgba(0, 97, 242, 0.25);
            outline: none;
        }
        
        .otp-input:disabled {
            background-color: #f8f9fa;
            cursor: not-allowed;
        }
        
        .btn-verify {
            width: 100%;
            padding: 0.875rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 0.5rem;
            background: linear-gradient(135deg, #0061f2 0%, #4d8cff 100%);
            border: none;
            color: white;
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }
        
        .btn-verify:hover:not(:disabled) {
            background: linear-gradient(135deg, #0050d0 0%, #0061f2 100%);
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 97, 242, 0.3);
        }
        
        .btn-verify:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .resend-section {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .btn-resend {
            background: none;
            border: none;
            color: #0061f2;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            transition: all 0.3s ease;
        }
        
        .btn-resend:hover:not(:disabled) {
            background-color: #f8f9fa;
            color: #0050d0;
        }
        
        .btn-resend:disabled {
            color: #6c757d;
            cursor: not-allowed;
        }
        
        .back-link {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .back-link a {
            color: #6c757d;
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        .back-link a:hover {
            color: #0061f2;
        }
        
        .alert {
            border-radius: 0.5rem;
            font-weight: 500;
            margin-bottom: 1.5rem;
        }
        
        .countdown {
            color: #0061f2;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="otp-container">
        <!-- Header -->
        <div class="otp-header">
            <div class="otp-header-content">
                <div class="otp-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h1 class="otp-title"><?= lang('Auth.verify_otp') ?></h1>
                <p class="otp-subtitle"><?= lang('Auth.otp_subtitle_email') ?></p>
            </div>
        </div>
        
        <!-- Body -->
        <div class="otp-body">
            <!-- Flash Messages -->
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (session()->getFlashdata('info')): ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <?= session()->getFlashdata('info') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- OTP Info -->
            <div class="otp-info">
                <i class="fas fa-envelope me-2"></i>
                <strong><?= lang('Auth.email') ?>:</strong> <?= esc($email ?? 'N/A') ?><br>
                <small class="text-muted"><?= lang('Auth.otp_sent_validity') ?></small>
            </div>
            
            <!-- OTP Form -->
            <form action="<?= base_url('auth/verify-otp') ?>" method="post" id="otpForm">
                <?= csrf_field() ?>
                
                <!-- OTP Input Fields -->
                <div class="otp-input-group">
                    <input type="text" class="form-control otp-input" id="otp1" name="otp_code" maxlength="6" pattern="[0-9]{6}" required autocomplete="off" autofocus placeholder="000000">
                </div>
                
                <input type="hidden" id="otpCode" name="otp_code">
                
                <button type="submit" class="btn btn-verify" id="verifyBtn">
                    <div class="spinner-border spinner-border-sm me-2 d-none" id="verifySpinner" role="status"></div>
                    <span id="verifyText"><?= lang('Auth.verify') ?></span>
                </button>
            </form>
            
            <!-- Resend Section -->
            <div class="resend-section">
                <p class="text-muted mb-2"><?= lang('Auth.didnt_receive_otp') ?>?</p>
                <button type="button" class="btn-resend" id="resendBtn" disabled>
                    <i class="fas fa-redo me-1"></i>
                    <span id="resendText"><?= lang('Auth.resend_otp') ?></span>
                </button>
                <div id="resendCountdown" class="mt-2"></div>
            </div>
            
            <!-- Back Link -->
            <div class="back-link">
                <a href="<?= base_url('auth/login') ?>">
                    <i class="fas fa-arrow-left me-1"></i>Kembali ke Login
                </a>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const otpInput = document.getElementById('otp1');
        const otpCodeInput = document.getElementById('otpCode');
        const otpForm = document.getElementById('otpForm');
        const verifyBtn = document.getElementById('verifyBtn');
        const verifySpinner = document.getElementById('verifySpinner');
        const verifyText = document.getElementById('verifyText');
        const resendBtn = document.getElementById('resendBtn');
        const resendText = document.getElementById('resendText');
        const resendCountdown = document.getElementById('resendCountdown');
        
        let resendCooldown = 60; // seconds
        let countdownInterval = null;
        
        // Auto-format OTP input (6 digits)
        otpInput.addEventListener('input', function(e) {
            let value = this.value.replace(/\D/g, '').slice(0, 6);
            this.value = value;
            
            // Update hidden input
            otpCodeInput.value = value;
            
            // Auto-submit if 6 digits entered
            if (value.length === 6) {
                setTimeout(() => {
                    otpForm.submit();
                }, 200);
            }
        });
        
        // Prevent non-numeric input
        otpInput.addEventListener('keypress', function(e) {
            if (!/[0-9]/.test(e.key) && !['Backspace', 'Delete', 'Tab', 'Enter'].includes(e.key)) {
                e.preventDefault();
            }
        });
        
        // Paste handling
        otpInput.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedData = (e.clipboardData || window.clipboardData).getData('text');
            const digits = pastedData.replace(/\D/g, '').slice(0, 6);
            this.value = digits;
            otpCodeInput.value = digits;
            
            if (digits.length === 6) {
                setTimeout(() => {
                    otpForm.submit();
                }, 200);
            }
        });
        
        // Form submission
        otpForm.addEventListener('submit', function(e) {
            const otpValue = otpCodeInput.value;
            
            if (otpValue.length !== 6) {
                e.preventDefault();
                alert('Silakan masukkan 6 digit kode OTP');
                return;
            }
            
            // Show loading state
            verifyBtn.disabled = true;
            verifySpinner.classList.remove('d-none');
            verifyText.textContent = 'Memverifikasi...';
        });
        
        // Resend OTP
        resendBtn.addEventListener('click', function() {
            if (this.disabled) return;
            
            // Disable button
            this.disabled = true;
            
            // Make AJAX request
            fetch('<?= base_url('auth/resend-otp') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    resendText.textContent = 'OTP Terkirim!';
                    
                    // Reset countdown
                    resendCooldown = data.remaining_seconds || 60;
                    startCountdown();
                    
                    // Show alert
                    alert('OTP baru telah dikirim ke email Anda.');
                    
                    // Reset after 2 seconds
                    setTimeout(() => {
                        resendText.textContent = 'Kirim Ulang OTP';
                    }, 2000);
                } else {
                    alert(data.message || 'Gagal mengirim OTP. Silakan coba lagi.');
                    
                    if (data.remaining_seconds) {
                        resendCooldown = data.remaining_seconds;
                        startCountdown();
                    } else {
                        this.disabled = false;
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
                this.disabled = false;
            });
        });
        
        // Start countdown
        function startCountdown() {
            if (countdownInterval) {
                clearInterval(countdownInterval);
            }
            
            resendCountdown.innerHTML = `<small class="text-muted">Kirim ulang dalam <span class="countdown">${resendCooldown}</span> detik</small>`;
            
            countdownInterval = setInterval(() => {
                resendCooldown--;
                
                if (resendCooldown > 0) {
                    resendCountdown.innerHTML = `<small class="text-muted">Kirim ulang dalam <span class="countdown">${resendCooldown}</span> detik</small>`;
                } else {
                    resendBtn.disabled = false;
                    resendCountdown.innerHTML = '';
                    clearInterval(countdownInterval);
                }
            }, 1000);
        }
        
        // Start countdown on page load
        startCountdown();
        
        // Auto-hide alerts
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                if (!alert.classList.contains('alert-danger')) {
                    setTimeout(() => {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }, 5000);
                }
            });
        });
    </script>
</body>
</html>

