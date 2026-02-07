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
        /* Custom styles for waiting approval page */
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            background-attachment: fixed;
            padding: 1.5rem;
        }
        
        .waiting-container {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(30px) saturate(180%);
            position: relative;
        }
        
        .contact-box {
            background: linear-gradient(135deg, #0061f2 0%, #4d8cff 100%);
            color: white;
        }
        
        .contact-box h5 {
            color: white;
        }
        
        .contact-box p {
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            line-height: 1.5;
        }
        
        .contact-box a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
        }
        
        .contact-box a:hover {
            text-decoration: underline;
        }
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

