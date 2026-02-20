<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Waiting for Admin Approval - OPTIMA | PT Sarana Mitra Luas Tbk</title>
    
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
    
    <style>
        * { box-sizing: border-box; }
        body {
            background: #f5f7fa;
            font-family: 'Metropolis', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 3rem 1rem;
            margin: 0;
        }
        .auth-container { width: 100%; max-width: 700px; }
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
        .auth-card-wide { /* alias */ }
        .header-section { text-align: center; margin-bottom: 1.5rem; }
        .auth-icon.waiting-icon {
            width: 64px; height: 64px;
            background: #fff3cd; border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 1.75rem; color: #ffc107;
            margin-bottom: 1rem;
        }
        .auth-title { font-size: 1.5rem; font-weight: 700; color: #2c3e50; }
        .auth-subtitle { color: #6c757d; font-size: 0.9rem; }
        .info-grid.content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin: 1.25rem 0;
        }
        @media (max-width: 600px) {
            .info-grid.content-grid { grid-template-columns: 1fr; }
        }
        .info-section.info-box {
            background: #f8f9fa;
            border-left: 3px solid #0061f2;
            border-radius: 8px;
            padding: 1rem;
            font-size: 0.875rem;
        }
        .info-section.info-box h5 { font-weight: 700; color: #2c3e50; margin-bottom: 0.75rem; }
        .info-section.info-box ul { padding-left: 1.25rem; margin: 0; color: #495057; }
        .contact-box {
            background: linear-gradient(135deg, #0061f2, #4d8cff);
            color: white; border-radius: 8px; padding: 1rem;
            font-size: 0.875rem;
        }
        .contact-box h5 { color: white; font-weight: 700; margin-bottom: 0.75rem; }
        .contact-box p { margin-bottom: 0.5rem; opacity: 0.95; }
        .contact-box a { color: white; font-weight: 600; }
        .btn-primary {
            background: #0061f2; border: none; border-radius: 6px;
            padding: 0.7rem 1.5rem; font-weight: 600; font-size: 0.95rem;
            color: white; width: 100%; display: block; text-align: center;
            text-decoration: none; transition: background 0.2s; margin-top: 1.25rem;
        }
        .btn-primary:hover { background: #0056b3; color: white; }
        .btn-block { width: 100%; }
        .btn-back { margin-top: 1.25rem; }
    </style>

</head>
<body>
    <div class="auth-container">
        <div class="auth-card auth-card-wide waiting-container">
            <!-- Header Section -->
            <div class="header-section">
                <div class="auth-icon waiting-icon">
                    <i class="fas fa-clock"></i>
                </div>
                
                <h1 class="auth-title waiting-title">Waiting for Admin Approval</h1>
                
                <p class="auth-subtitle waiting-message">
                Your account has been successfully created, but is still in <strong>Inactive</strong> status and awaiting administrator approval.
            </p>
        </div>
        
        <!-- Email Verification Status -->
        <?php if (isset($email) && $email && isset($user_found) && $user_found): ?>
            <div class="alert <?= isset($email_verified) && $email_verified ? 'alert-success' : 'alert-warning' ?>">
                <div class="d-flex align-items-start gap-3">
                    <?php if (isset($email_verified) && $email_verified): ?>
                        <i class="fas fa-check-circle alert-icon" style="color: #28a745;"></i>
                        <div class="alert-content">
                            <span class="alert-title">✅ Email Verified</span>
                            <div class="alert-text">
                                <strong><?= esc($email) ?></strong><br>
                                Your email has been verified successfully. Waiting for admin approval.
                            </div>
                        </div>
                    <?php else: ?>
                        <i class="fas fa-exclamation-triangle alert-icon" style="color: #ffc107;"></i>
                        <div class="alert-content">
                            <span class="alert-title">⚠️ Email Not Verified</span>
                            <div class="alert-text mb-2">
                                <strong><?= esc($email) ?></strong><br>
                                Please verify your email first by clicking the link sent to your inbox.
                            </div>
                            <a href="<?= base_url('auth/resend-verification?email=' . urlencode($email)) ?>" 
                               class="btn btn-warning btn-sm">
                                <i class="fas fa-paper-plane me-1"></i> Resend Verification
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php elseif (isset($email) && $email && (!isset($user_found) || !$user_found)): ?>
            <div class="alert alert-danger">
                <div class="d-flex align-items-start gap-3">
                    <i class="fas fa-times-circle alert-icon" style="color: #dc3545;"></i>
                    <div class="alert-content">
                        <span class="alert-title">❌ User Not Found</span>
                        <div class="alert-text">
                            The email <strong><?= esc($email) ?></strong> was not found in our system.
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?= session()->getFlashdata('success') ?>
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
        
        <!-- 2-Column Grid Layout -->
        <div class="info-grid content-grid">
            <!-- Left Column: Important Information -->
            <div class="info-section info-box">
                <h5><i class="fas fa-info-circle"></i> Important Information</h5>
                <ul>
                    <li>Your account will be activated after receiving approval from the administrator</li>
                    <li>The activation process usually takes 1-2 business days</li>
                    <li>After your account is activated, you will be able to access the OPTIMA system</li>
                </ul>
            </div>
            
            <!-- Right Column: Contact Support -->
            <div class="info-section contact-box">
                <h5><i class="fas fa-headset"></i> Need Help?</h5>
                <p style="margin-bottom: 0.75rem;">If you need assistance or want to expedite the activation process, please contact:</p>
                <p style="margin-top: 0.75rem;">
                    <i class="fas fa-envelope me-2"></i>
                    <a href="mailto:<?= esc($support_email) ?>"><?= esc($support_email) ?></a>
                </p>
                <p style="margin-top: 0.5rem; font-size: 0.85rem; opacity: 0.95;">
                    Or confirm with your supervisor to expedite the account activation process.
                </p>
            </div>
        </div>
        
        <a href="<?= base_url('auth/login') ?>" class="btn btn-primary btn-block btn-back">
            <i class="fas fa-arrow-left me-2"></i> Back to Login Page
        </a>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

