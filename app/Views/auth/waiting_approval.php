<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= lang('Auth.waiting_approval') ?> - OPTIMA | PT Sarana Mitra Luas Tbk</title>
    
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
            padding: 1rem;
            position: relative;
            overflow-x: hidden;
        }
        
        /* Abstract Geometric Background */
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
            border-radius: 2rem;
            box-shadow: 
                0 20px 60px rgba(0, 97, 242, 0.08),
                0 8px 24px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.8);
            max-width: 600px;
            width: 100%;
            position: relative;
            z-index: 1;
            padding: 3rem;
            text-align: center;
        }
        
        .waiting-icon {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #0061f2 0%, #4d8cff 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            box-shadow: 0 10px 30px rgba(0, 97, 242, 0.3);
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .waiting-icon i {
            font-size: 4rem;
            color: white;
        }
        
        .waiting-title {
            font-size: 2rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 1rem;
        }
        
        .waiting-message {
            font-size: 1.1rem;
            color: #64748b;
            margin-bottom: 2rem;
            line-height: 1.8;
        }
        
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #0061f2;
            padding: 1.5rem;
            border-radius: 0.5rem;
            margin: 2rem 0;
            text-align: left;
        }
        
        .info-box h5 {
            color: #0061f2;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .info-box ul {
            margin: 0;
            padding-left: 1.5rem;
        }
        
        .info-box li {
            margin: 0.5rem 0;
            color: #475569;
        }
        
        .contact-box {
            background: linear-gradient(135deg, #0061f2 0%, #4d8cff 100%);
            color: white;
            padding: 2rem;
            border-radius: 1rem;
            margin: 2rem 0;
        }
        
        .contact-box h5 {
            margin-bottom: 1rem;
            font-weight: 600;
        }
        
        .contact-box a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .contact-box a:hover {
            text-decoration: underline;
        }
        
        .btn-back {
            margin-top: 2rem;
            padding: 0.75rem 2rem;
            background: #0061f2;
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .btn-back:hover {
            background: #0050d0;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 97, 242, 0.3);
            color: white;
        }
        
        .alert {
            border-radius: 0.5rem;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="waiting-container">
        <div class="waiting-icon">
            <i class="fas fa-clock"></i>
        </div>
        
        <h1 class="waiting-title"><?= lang('Auth.waiting_approval') ?></h1>
        
        <p class="waiting-message">
            <?= lang('Auth.account_pending_approval_message') ?>
        </p>
        
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
        
        <div class="info-box">
            <h5><i class="fas fa-info-circle me-2"></i><?= lang('Auth.important_information') ?></h5>
            <ul>
                <li><?= lang('Auth.account_activated_after_approval') ?></li>
                <li><?= lang('Auth.activation_time_estimate') ?></li>
                <li><?= lang('Auth.after_activation_access') ?></li>
            </ul>
        </div>
        
        <div class="contact-box">
            <h5><i class="fas fa-headset me-2"></i><?= lang('Auth.need_help') ?>?</h5>
            <p><?= lang('Auth.contact_support_message') ?>:</p>
            <p style="margin-top: 1rem;">
                <i class="fas fa-envelope me-2"></i>
                <a href="mailto:<?= esc($support_email) ?>"><?= esc($support_email) ?></a>
            </p>
            <p style="margin-top: 0.5rem; font-size: 0.9rem; opacity: 0.9;">
                <?= lang('Auth.contact_supervisor_message') ?>
            </p>
        </div>
        
        <a href="<?= base_url('auth/login') ?>" class="btn-back">
            <i class="fas fa-arrow-left me-2"></i><?= lang('Auth.back_to_login_page') ?>
        </a>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

