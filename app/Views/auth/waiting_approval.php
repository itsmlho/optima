<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        body {
            font-family: 'Metropolis', sans-serif;
            background: #ffffff;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
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
            background: 
                radial-gradient(circle at 15% 25%, rgba(0, 97, 242, 0.06) 0%, transparent 50%),
                radial-gradient(circle at 85% 75%, rgba(77, 140, 255, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(0, 97, 242, 0.04) 0%, transparent 60%);
            pointer-events: none;
            z-index: 0;
        }
        
        .waiting-container {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(30px) saturate(180%);
            border-radius: 1.5rem;
            box-shadow: 
                0 20px 60px rgba(0, 97, 242, 0.08),
                0 8px 24px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.8);
            max-width: 1000px;
            width: 100%;
            position: relative;
            z-index: 1;
            padding: 2rem;
        }
        
        .header-section {
            text-align: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .waiting-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #0061f2 0%, #4d8cff 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            box-shadow: 0 10px 30px rgba(0, 97, 242, 0.3);
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .waiting-icon i {
            font-size: 2.5rem;
            color: white;
        }
        
        .waiting-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 0.5rem;
        }
        
        .waiting-message {
            font-size: 0.95rem;
            color: #6c757d;
            line-height: 1.5;
        }
        
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-top: 1.5rem;
        }
        
        @media (max-width: 768px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
            .waiting-container {
                padding: 1.5rem;
            }
        }
        
        .info-box, .contact-box {
            background: #f8f9fa;
            padding: 1.25rem;
            border-radius: 1rem;
            border: 1px solid #e9ecef;
        }
        
        .info-box h5, .contact-box h5 {
            margin-bottom: 0.75rem;
            font-weight: 600;
            font-size: 1rem;
            color: #1a202c;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .info-box ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .info-box li {
            padding: 0.5rem 0;
            padding-left: 1.5rem;
            position: relative;
            font-size: 0.875rem;
            color: #495057;
            line-height: 1.5;
        }
        
        .info-box li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #0061f2;
            font-weight: bold;
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
        
        .btn-back {
            margin-top: 1.5rem;
            padding: 0.75rem 2rem;
            background: #0061f2;
            color: white;
            border: none;
            border-radius: 0.75rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            width: 100%;
            text-align: center;
        }
        
        .btn-back:hover {
            background: #0050d0;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 97, 242, 0.3);
            color: white;
        }
        
        .alert {
            border-radius: 0.75rem;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            padding: 1rem;
            border: none;
        }
        
        .alert-warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
        }
        
        .alert-success {
            background: #d4edda;
            border-left: 4px solid #28a745;
        }
        
        .alert-danger {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
        }
        
        .alert-icon {
            font-size: 1.75rem;
            flex-shrink: 0;
        }
        
        .alert-content {
            flex: 1;
        }
        
        .alert-title {
            font-weight: 700;
            font-size: 0.95rem;
            margin-bottom: 0.25rem;
            display: block;
        }
        
        .alert-text {
            font-size: 0.85rem;
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="waiting-container">
        <!-- Header Section -->
        <div class="header-section">
            <div class="waiting-icon">
                <i class="fas fa-clock"></i>
            </div>
            
            <h1 class="waiting-title">Waiting for Admin Approval</h1>
            
            <p class="waiting-message">
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
        <div class="content-grid">
            <!-- Left Column: Important Information -->
            <div class="info-box">
                <h5><i class="fas fa-info-circle"></i> Important Information</h5>
                <ul>
                    <li>Your account will be activated after receiving approval from the administrator</li>
                    <li>The activation process usually takes 1-2 business days</li>
                    <li>After your account is activated, you will be able to access the OPTIMA system</li>
                </ul>
            </div>
            
            <!-- Right Column: Contact Support -->
            <div class="contact-box">
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
        
        <a href="<?= base_url('auth/login') ?>" class="btn-back">
            <i class="fas fa-arrow-left me-2"></i> Back to Login Page
        </a>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

