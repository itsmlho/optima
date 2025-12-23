<!DOCTYPE html>
<html lang="id" data-bs-theme="light">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="<?= lang('Auth.meta_login_description') ?>">
    <meta name="robots" content="noindex, nofollow">

    <!-- Title -->
    <title><?= lang('Auth.login') ?> - OPTIMA | PT Sarana Mitra Luas Tbk</title>

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
        
        /* Abstract Geometric Background - Cocok dengan Logo OPTIMA */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                /* Radial gradients biru halus */
                radial-gradient(circle at 15% 25%, rgba(0, 97, 242, 0.06) 0%, transparent 50%),
                radial-gradient(circle at 85% 75%, rgba(77, 140, 255, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(0, 97, 242, 0.04) 0%, transparent 60%),
                /* Geometric shapes */
                linear-gradient(135deg, rgba(0, 97, 242, 0.02) 0%, transparent 50%),
                linear-gradient(45deg, rgba(77, 140, 255, 0.02) 0%, transparent 50%);
            background-size: 100% 100%;
            animation: gentleMove 25s ease-in-out infinite;
            pointer-events: none;
            z-index: 0;
        }
        
        @keyframes gentleMove {
            0%, 100% { 
                background-position: 0% 0%, 100% 100%, 50% 50%, 0% 0%, 100% 0%;
                opacity: 1;
            }
            50% { 
                background-position: 20% 30%, 80% 70%, 60% 60%, 10% 10%, 90% 10%;
                opacity: 0.9;
            }
        }
        
        /* Subtle Grid Pattern */
        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                /* Subtle grid lines */
                repeating-linear-gradient(0deg, transparent, transparent 40px, rgba(0, 97, 242, 0.015) 40px, rgba(0, 97, 242, 0.015) 41px),
                repeating-linear-gradient(90deg, transparent, transparent 40px, rgba(0, 97, 242, 0.015) 40px, rgba(0, 97, 242, 0.015) 41px),
                /* Wave pattern di bagian bawah */
                url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(0,97,242,0.03)" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,112C672,96,768,96,864,112C960,128,1056,160,1152,160C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-size: 100% 100%, 100% 100%, 100% 30%;
            background-position: 0 0, 0 0, bottom;
            background-repeat: no-repeat;
            animation: gridFloat 20s ease-in-out infinite;
            pointer-events: none;
            z-index: 0;
        }
        
        @keyframes gridFloat {
            0%, 100% { 
                transform: translateY(0);
                opacity: 1;
            }
            50% { 
                transform: translateY(-10px);
                opacity: 0.95;
            }
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(30px) saturate(180%);
            -webkit-backdrop-filter: blur(30px) saturate(180%);
            border-radius: 2rem;
            box-shadow: 
                0 20px 60px rgba(0, 97, 242, 0.08),
                0 8px 24px rgba(0, 0, 0, 0.06),
                0 2px 8px rgba(0, 0, 0, 0.04),
                inset 0 1px 0 rgba(255, 255, 255, 0.9),
                inset 0 -1px 0 rgba(0, 97, 242, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.8);
            overflow: hidden;
            max-width: 1000px;
            width: 100%;
            position: relative;
            z-index: 1;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .login-container:hover {
            transform: translateY(-2px);
            box-shadow: 
                0 24px 72px rgba(0, 97, 242, 0.12),
                0 12px 32px rgba(0, 0, 0, 0.08),
                0 4px 12px rgba(0, 0, 0, 0.06),
                inset 0 1px 0 rgba(255, 255, 255, 0.9);
        }
        
        .login-container::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: 
                radial-gradient(circle at 30% 30%, rgba(0, 97, 242, 0.06) 0%, transparent 50%),
                radial-gradient(circle at 70% 70%, rgba(77, 140, 255, 0.05) 0%, transparent 50%);
            animation: pulse 20s ease-in-out infinite;
            pointer-events: none;
            z-index: 0;
        }
        
        @keyframes pulse {
            0%, 100% { 
                transform: scale(1) translate(0, 0) rotate(0deg); 
                opacity: 0.6; 
            }
            50% { 
                transform: scale(1.15) translate(-8%, -8%) rotate(5deg); 
                opacity: 0.9; 
            }
        }
        
        .login-brand {
            background: linear-gradient(135deg, #0061f2 0%, #4d8cff 50%, #0061f2 100%);
            background-size: 200% 200%;
            animation: gradientShift 15s ease infinite;
            color: white;
            padding: 3.5rem 2.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 
                inset 0 0 60px rgba(0, 0, 0, 0.1),
                0 0 40px rgba(0, 97, 242, 0.2);
        }
        
        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        
        .login-brand::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: 
                radial-gradient(circle at 20% 30%, rgba(255, 255, 255, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="12" height="12" patternUnits="userSpaceOnUse"><path d="M 12 0 L 0 0 0 12" fill="none" stroke="rgba(255,255,255,0.08)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            animation: float 25s linear infinite;
            opacity: 0.8;
        }
        
        .login-brand::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                linear-gradient(135deg, transparent 0%, rgba(255, 255, 255, 0.05) 50%, transparent 100%);
            pointer-events: none;
        }
        
        @keyframes float {
            0% { transform: translateX(0) translateY(0) rotate(0deg); }
            100% { transform: translateX(-15px) translateY(-15px) rotate(2deg); }
        }
        
        .login-brand-content {
            position: relative;
            z-index: 2;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .login-logo {
            width: 140px;
            height: 140px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 2rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border: 2px solid rgba(255, 255, 255, 0.3);
            padding: 1.25rem;
            box-shadow: 
                0 12px 40px rgba(0, 0, 0, 0.15),
                0 4px 16px rgba(0, 0, 0, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.4);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .login-logo::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.3) 0%, transparent 70%);
            animation: logoShine 3s ease-in-out infinite;
        }
        
        @keyframes logoShine {
            0%, 100% { transform: translate(-50%, -50%) scale(0.8); opacity: 0; }
            50% { transform: translate(-50%, -50%) scale(1.2); opacity: 1; }
        }
        
        .login-logo:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 
                0 16px 48px rgba(0, 0, 0, 0.2),
                0 6px 20px rgba(0, 0, 0, 0.15),
                inset 0 1px 0 rgba(255, 255, 255, 0.5);
        }
        
        .login-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            position: relative;
            z-index: 1;
            filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.1));
        }
        
        .login-title {
            font-size: 2.75rem;
            font-weight: 800;
            margin-bottom: 0.75rem;
            text-shadow: 
                0 2px 8px rgba(0, 0, 0, 0.15),
                0 1px 4px rgba(0, 0, 0, 0.1);
            letter-spacing: -0.02em;
            line-height: 1.2;
        }
        
        .login-subtitle {
            font-size: 1.125rem;
            opacity: 0.95;
            margin-bottom: 2.5rem;
            font-weight: 400;
            letter-spacing: 0.01em;
        }
        
        .login-features {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
            margin-top: 2.5rem;
        }
        
        .login-feature {
            display: flex;
            align-items: center;
            font-size: 0.9rem;
            opacity: 0.9;
            padding: 0.75rem;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 0.75rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        
        .login-feature:hover {
            background: rgba(255, 255, 255, 0.12);
            transform: translateX(4px);
            border-color: rgba(255, 255, 255, 0.2);
        }
        
        .login-feature i {
            margin-right: 0.75rem;
            width: 1.25rem;
            text-align: center;
            font-size: 1.1rem;
            opacity: 0.95;
        }
        
        .login-form {
            padding: 3.5rem 2.5rem;
            position: relative;
            z-index: 2;
            background: linear-gradient(to bottom, rgba(255, 255, 255, 0.98) 0%, rgba(255, 255, 255, 1) 100%);
        }
        
        .login-brand {
            position: relative;
            z-index: 2;
        }
        
        /* Footer Powered By */
        .login-footer {
            position: relative;
            z-index: 2;
            padding: 2rem 2rem 1.5rem;
            text-align: center;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            margin-top: auto;
            background: rgba(0, 0, 0, 0.05);
            backdrop-filter: blur(10px);
        }
        
        .login-footer-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }
        
        .login-footer-text {
            font-size: 0.875rem;
            opacity: 0.95;
            color: white;
            font-weight: 500;
            letter-spacing: 0.02em;
        }
        
        .login-footer-logo {
            height: 45px;
            width: auto;
            max-width: 220px;
            opacity: 1;
            filter: none;
            -webkit-filter: none;
            transition: transform 0.3s ease, opacity 0.3s ease, filter 0.3s ease;
            display: inline-block;
            vertical-align: middle;
            object-fit: contain;
            background: rgba(255, 255, 255, 0.15);
            padding: 6px 12px;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .login-footer-logo:hover {
            transform: scale(1.1);
            opacity: 1;
            background: rgba(255, 255, 255, 0.25);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .login-form-title {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .login-form-title h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 0.75rem;
            letter-spacing: -0.01em;
            line-height: 1.3;
        }
        
        .login-form-title p {
            color: #64748b;
            margin-bottom: 0;
            font-size: 0.95rem;
            font-weight: 400;
        }
        
        .form-floating {
            position: relative;
            margin-bottom: 1.75rem;
        }
        
        .form-floating .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 1.125rem 1rem;
            font-size: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: #ffffff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        
        .form-floating .form-control:hover {
            border-color: #cbd5e1;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }
        
        .form-floating .form-control:focus {
            border-color: #0061f2;
            box-shadow: 
                0 0 0 4px rgba(0, 97, 242, 0.1),
                0 4px 12px rgba(0, 97, 242, 0.15);
            outline: none;
            transform: translateY(-1px);
        }
        
        .form-floating label {
            padding: 1.125rem 1rem;
            font-weight: 500;
            color: #64748b;
            font-size: 0.95rem;
        }
        
        .form-floating .form-control:focus ~ label,
        .form-floating .form-control:not(:placeholder-shown) ~ label {
            color: #0061f2;
            font-weight: 600;
        }
        
        .form-check {
            margin-bottom: 1.75rem;
            padding-left: 2rem;
        }
        
        .form-check-input {
            width: 1.25rem;
            height: 1.25rem;
            border: 2px solid #cbd5e1;
            border-radius: 0.375rem;
            transition: all 0.2s ease;
            margin-top: 0.25rem;
        }
        
        .form-check-input:hover {
            border-color: #0061f2;
        }
        
        .form-check-input:checked {
            background-color: #0061f2;
            border-color: #0061f2;
            box-shadow: 0 0 0 3px rgba(0, 97, 242, 0.1);
        }
        
        .form-check-label {
            color: #475569;
            font-weight: 500;
            cursor: pointer;
            user-select: none;
        }
        
        .form-check-label {
            font-weight: 500;
            color: #495057;
            margin-left: 0.5rem;
        }
        
        .btn-login {
            width: 100%;
            padding: 1.125rem 2rem;
            font-size: 1.05rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.75px;
            border-radius: 0.75rem;
            background: linear-gradient(135deg, #0061f2 0%, #4d8cff 100%);
            background-size: 200% 200%;
            animation: buttonGradient 3s ease infinite;
            border: none;
            color: white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 1.75rem;
            box-shadow: 
                0 4px 12px rgba(0, 97, 242, 0.25),
                0 2px 6px rgba(0, 97, 242, 0.15),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
        }
        
        @keyframes buttonGradient {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        
        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }
        
        .btn-login:hover::before {
            left: 100%;
        }
        
        .btn-login:hover {
            background: linear-gradient(135deg, #0050d0 0%, #0061f2 100%);
            transform: translateY(-3px);
            box-shadow: 
                0 8px 20px rgba(0, 97, 242, 0.35),
                0 4px 10px rgba(0, 97, 242, 0.25),
                inset 0 1px 0 rgba(255, 255, 255, 0.3);
        }
        
        .btn-login:active {
            transform: translateY(-1px);
            box-shadow: 
                0 4px 12px rgba(0, 97, 242, 0.3),
                0 2px 6px rgba(0, 97, 242, 0.2);
        }
        
        .btn-login:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
            animation: none;
        }
        
        .login-links {
            text-align: center;
            margin-top: 2.5rem;
        }
        
        .login-links a {
            color: #0061f2;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            position: relative;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
        }
        
        .login-links a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: #0061f2;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        
        .login-links a:hover {
            color: #0048b8;
            background: rgba(0, 97, 242, 0.05);
        }
        
        .login-links a:hover::after {
            width: 100%;
        }
        
        .login-divider {
            display: flex;
            align-items: center;
            margin: 2rem 0;
        }
        
        .login-divider::before,
        .login-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e9ecef;
        }
        
        .login-divider span {
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
            color: #0061f2;
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
            .login-container {
                margin: 1rem;
            }
            
            .login-brand {
                padding: 2rem 1rem;
            }
            
            .login-title {
                font-size: 2rem;
            }
            
            .login-form {
                padding: 2rem 1rem;
            }
            
            .login-footer {
                padding: 1rem;
            }
            
            .login-footer-content {
                flex-direction: column;
                gap: 0.5rem;
            }
        }
        
        /* Dark Mode Support */
        [data-bs-theme="dark"] .login-container {
            background: rgba(33, 37, 41, 0.95);
        }
        
        [data-bs-theme="dark"] .login-form-title h2 {
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

    <div class="login-container row g-0">
        <!-- Brand Section -->
        <div class="col-lg-6 d-none d-lg-block">
            <div class="login-brand h-100">
                <div class="login-brand-content">
                    <div class="login-logo">
                        <img src="<?= base_url('assets/images/logo-optima.png') ?>" alt="OPTIMA Logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div style="display: none; width: 100%; height: 100%; align-items: center; justify-content: center; font-size: 3rem; color: white;">
                            <i class="fas fa-cube"></i>
                        </div>
                    </div>
                    <h1 class="login-title">OPTIMA</h1>
                </div>
                
                <!-- Footer Powered By -->
                <div class="login-footer">
                    <div class="login-footer-content">
                        <span class="login-footer-text">Powered by</span>
                        <img src="<?= base_url('assets/images/company-logo.png') ?>" 
                             alt="PT Sarana Mitra Luas Tbk" 
                             class="login-footer-logo">
                        <span class="login-footer-text">PT SARANA MITRA LUAS Tbk</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Form Section -->
        <div class="col-lg-6">
            <div class="login-form">
                <div class="login-form-title">
                    <h2><?= lang('Auth.welcome') ?></h2>
                    <p><?= lang('Auth.login_message') ?></p>
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
                
                <!-- Rate Limiting Messages -->
                <?php 
                $rateLimit = session()->getFlashdata('rate_limit');
                if ($rateLimit): 
                    $lockedUntil = $rateLimit['locked_until'] ?? null;
                    $lockedUntilTimestamp = $rateLimit['locked_until_timestamp'] ?? null;
                    $remainingAttempts = $rateLimit['remaining_attempts'] ?? null;
                ?>
                    <?php if ($lockedUntil && $lockedUntilTimestamp): ?>
                        <!-- Account Locked Message -->
                        <div class="alert alert-warning alert-dismissible fade show" role="alert" id="lockAlert">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-lock me-2 mt-1"></i>
                                <div class="flex-grow-1">
                                    <strong><?= lang('Auth.account_locked') ?></strong>
                                    <p class="mb-1"><?= lang('Auth.too_many_failed_attempts') ?></p>
                                    <p class="mb-0">
                                        <small>
                                            <?= lang('Auth.try_again_in') ?>: 
                                            <strong id="countdownTimer" class="text-danger"></strong>
                                        </small>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <script>
                            // Set locked until timestamp for countdown
                            window.lockedUntilTimestamp = <?= $lockedUntilTimestamp ?>;
                        </script>
                    <?php elseif ($remainingAttempts !== null && $remainingAttempts < 5): ?>
                        <!-- Remaining Attempts Warning -->
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong><?= lang('Auth.warning') ?>:</strong> 
                            <?= lang('Auth.remaining_attempts_message', ['attempts' => $remainingAttempts]) ?>
                            <?php if ($remainingAttempts <= 2): ?>
                                <?= lang('Auth.account_will_be_locked') ?>
                            <?php endif; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                
                <!-- Login Form -->
                <form action="<?= base_url('auth/attempt-login') ?>" method="post" class="needs-validation" novalidate>
                    <?= csrf_field() ?>
                    
                    <div class="form-floating">
                        <input type="text" class="form-control" id="username" name="username" placeholder="<?= lang('Auth.username_or_email') ?>" 
                               value="<?= old('username') ?>" required>
                        <label for="username">
                            <i class="fas fa-user me-2"></i><?= lang('Auth.username_or_email') ?>
                        </label>
                        <div class="invalid-feedback" style="display: none;">
                            <?= lang('Auth.please_enter_username_email') ?>
                        </div>
                    </div>
                    
                    <div class="form-floating">
                        <input type="password" class="form-control" id="password" name="password" placeholder="<?= lang('Auth.password') ?>" required>
                        <label for="password">
                            <i class="fas fa-lock me-2"></i><?= lang('Auth.password') ?>
                        </label>
                        <div class="invalid-feedback" style="display: none;">
                            <?= lang('Auth.please_enter_password') ?>
                        </div>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            <?= lang('Auth.remember_me') ?>
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-login" id="loginBtn">
                        <div class="loading-spinner" id="loginSpinner"></div>
                        <span id="loginText"><?= lang('Auth.login') ?></span>
                    </button>
                </form>
                
                <div class="login-links">
                    <a href="<?= base_url('auth/forgot-password') ?>"><?= lang('Auth.forgot_password') ?>?</a>
                    
                    <div class="login-divider">
                        <span><?= lang('App.or') ?></span>
                    </div>
                    
                    <p class="mb-0"><?= lang('Auth.no_account') ?>? <a href="<?= base_url('auth/register') ?>"><?= lang('Auth.register_here') ?></a></p>
                </div>
            </div>
            
            <div class="footer-links">
                <a href="#"><?= lang('App.help') ?></a>
                <a href="#"><?= lang('App.privacy_policy') ?></a>
                <a href="#"><?= lang('App.terms_conditions') ?></a>
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
                // Hide invalid-feedback on input
                form.querySelectorAll('input').forEach(input => {
                    input.addEventListener('input', function() {
                        const feedback = this.parentElement.querySelector('.invalid-feedback');
                        if (feedback && !this.classList.contains('is-invalid')) {
                            feedback.style.display = 'none';
                        }
                    });
                });
                
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    } else {
                        // Show loading state
                        const loginBtn = document.getElementById('loginBtn');
                        const loginSpinner = document.getElementById('loginSpinner');
                        const loginText = document.getElementById('loginText');
                        
                        loginBtn.disabled = true;
                        loginSpinner.style.display = 'inline-block';
                        loginText.textContent = '<?= lang('App.processing') ?>';
                    }
                    
                    form.classList.add('was-validated');
                }, false);
            });
        })();
        
        // Countdown timer for locked account
        if (typeof window.lockedUntilTimestamp !== 'undefined' && window.lockedUntilTimestamp) {
            function updateCountdown() {
                const countdownElement = document.getElementById('countdownTimer');
                const lockAlert = document.getElementById('lockAlert');
                
                if (!countdownElement) return;
                
                const now = Math.floor(Date.now() / 1000);
                const lockedUntil = window.lockedUntilTimestamp;
                const remaining = lockedUntil - now;
                
                if (remaining <= 0) {
                    // Lock expired
                    countdownElement.textContent = 'Kunci sudah berakhir. Silakan coba login lagi.';
                    if (lockAlert) {
                        lockAlert.classList.remove('alert-warning');
                        lockAlert.classList.add('alert-info');
                        const icon = lockAlert.querySelector('.fa-lock');
                        if (icon) icon.className = 'fas fa-check-circle me-2 mt-1';
                    }
                    // Reload page after 2 seconds
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    // Calculate minutes and seconds
                    const minutes = Math.floor(remaining / 60);
                    const seconds = remaining % 60;
                    
                    countdownElement.textContent = minutes + ' menit ' + seconds + ' detik';
                    
                    // Update every second
                    setTimeout(updateCountdown, 1000);
                }
            }
            
            // Start countdown
            updateCountdown();
        }

        // Auto-hide alerts (except locked account alert)
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert:not(#lockAlert)');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
            
            // Hide invalid-feedback messages on page load
            document.querySelectorAll('.invalid-feedback').forEach(feedback => {
                feedback.style.display = 'none';
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
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(event) {
            // Enter key on remember checkbox
            if (event.key === 'Enter' && document.activeElement.type === 'checkbox') {
                document.activeElement.click();
            }
        });
    </script>

</body>

</html> 