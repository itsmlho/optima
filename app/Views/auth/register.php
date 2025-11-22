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
                radial-gradient(circle at 15% 25%, rgba(0, 97, 242, 0.06) 0%, transparent 50%),
                radial-gradient(circle at 85% 75%, rgba(77, 140, 255, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(0, 97, 242, 0.04) 0%, transparent 60%),
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
                repeating-linear-gradient(0deg, transparent, transparent 40px, rgba(0, 97, 242, 0.015) 40px, rgba(0, 97, 242, 0.015) 41px),
                repeating-linear-gradient(90deg, transparent, transparent 40px, rgba(0, 97, 242, 0.015) 40px, rgba(0, 97, 242, 0.015) 41px),
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
        
        .register-container {
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
            max-width: 1100px;
            width: 100%;
            position: relative;
            z-index: 1;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .register-container:hover {
            transform: translateY(-2px);
            box-shadow: 
                0 24px 72px rgba(0, 97, 242, 0.12),
                0 12px 32px rgba(0, 0, 0, 0.08),
                0 4px 12px rgba(0, 0, 0, 0.06),
                inset 0 1px 0 rgba(255, 255, 255, 0.9);
        }
        
        .register-container::before {
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
        
        .register-brand {
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
        
        .register-brand::before {
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
        
        .register-brand::after {
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
        
        .register-brand-content {
            position: relative;
            z-index: 2;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .register-logo {
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
        
        .register-logo::before {
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
        
        .register-logo:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 
                0 16px 48px rgba(0, 0, 0, 0.2),
                0 6px 20px rgba(0, 0, 0, 0.15),
                inset 0 1px 0 rgba(255, 255, 255, 0.5);
        }
        
        .register-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            position: relative;
            z-index: 1;
            filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.1));
        }
        
        .register-title {
            font-size: 2.75rem;
            font-weight: 800;
            margin-bottom: 0.75rem;
            text-shadow: 
                0 2px 8px rgba(0, 0, 0, 0.15),
                0 1px 4px rgba(0, 0, 0, 0.1);
            letter-spacing: -0.02em;
            line-height: 1.2;
        }
        
        .register-subtitle {
            font-size: 1.125rem;
            opacity: 0.95;
            margin-bottom: 2.5rem;
            font-weight: 400;
            letter-spacing: 0.01em;
        }
        
        .register-benefits {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.25rem;
            margin-top: 2.5rem;
        }
        
        .register-benefit {
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
        
        .register-benefit:hover {
            background: rgba(255, 255, 255, 0.12);
            transform: translateX(4px);
            border-color: rgba(255, 255, 255, 0.2);
        }
        
        .register-benefit i {
            margin-right: 0.75rem;
            width: 1.25rem;
            text-align: center;
            font-size: 1.1rem;
            opacity: 0.95;
        }
        
        .register-form {
            padding: 3.5rem 2.5rem;
            position: relative;
            z-index: 2;
            background: linear-gradient(to bottom, rgba(255, 255, 255, 0.98) 0%, rgba(255, 255, 255, 1) 100%);
        }
        
        .register-brand {
            position: relative;
            z-index: 2;
        }
        
        .register-form-title {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .register-form-title h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 0.75rem;
            letter-spacing: -0.01em;
            line-height: 1.3;
        }
        
        .register-form-title p {
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
        
        .form-select {
            border: 2px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 1.125rem 3rem 1.125rem 1rem;
            font-size: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: #ffffff url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e") no-repeat right 1rem center/16px 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            cursor: pointer;
        }
        
        .form-select:hover:not(:disabled) {
            border-color: #cbd5e1;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }
        
        .form-select:focus {
            border-color: #0061f2;
            box-shadow: 
                0 0 0 4px rgba(0, 97, 242, 0.1),
                0 4px 12px rgba(0, 97, 242, 0.15);
            outline: none;
            transform: translateY(-1px);
        }
        
        .form-select:disabled {
            background-color: #f1f5f9;
            border-color: #e2e8f0;
            cursor: not-allowed;
            opacity: 0.7;
        }
        
        .form-select.is-invalid {
            border-color: #e81500;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23e81500' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
        }
        
        .form-select.is-valid {
            border-color: #0061f2;
        }
        
        .form-text {
            font-size: 0.75rem;
            color: #64748b;
            margin-top: 0.25rem;
            display: block;
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
            font-weight: 500;
            color: #475569;
            cursor: pointer;
            user-select: none;
        }
        
        .form-check-label a {
            color: #0061f2;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .form-check-label a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: #0061f2;
            transition: all 0.3s ease;
        }
        
        .form-check-label a:hover {
            color: #0048b8;
        }
        
        .form-check-label a:hover::after {
            width: 100%;
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
            color: #0061f2;
        }
        
        .password-requirements li.invalid {
            color: #e81500;
        }
        
        .password-strength-fill.weak {
            background-color: #e81500;
        }
        
        .password-strength-fill.fair {
            background-color: #ffb607;
        }
        
        .password-strength-fill.good {
            background-color: #39afd1;
        }
        
        .password-strength-fill.strong {
            background-color: #0061f2;
        }
        
        .btn-register {
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
        
        .btn-register::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }
        
        .btn-register:hover::before {
            left: 100%;
        }
        
        .btn-register:hover {
            background: linear-gradient(135deg, #0050d0 0%, #0061f2 100%);
            transform: translateY(-3px);
            box-shadow: 
                0 8px 20px rgba(0, 97, 242, 0.35),
                0 4px 10px rgba(0, 97, 242, 0.25),
                inset 0 1px 0 rgba(255, 255, 255, 0.3);
        }
        
        .btn-register:active {
            transform: translateY(-1px);
            box-shadow: 
                0 4px 12px rgba(0, 97, 242, 0.3),
                0 2px 6px rgba(0, 97, 242, 0.2);
        }
        
        .btn-register:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
            animation: none;
        }
        
        .register-links {
            text-align: center;
            margin-top: 2.5rem;
        }
        
        .register-links a {
            color: #0061f2;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            position: relative;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
        }
        
        .register-links a::after {
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
        
        .register-links a:hover {
            color: #0048b8;
            background: rgba(0, 97, 242, 0.05);
        }
        
        .register-links a:hover::after {
            width: 100%;
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
            background: linear-gradient(to top, rgba(248, 250, 252, 0.98) 0%, rgba(255, 255, 255, 1) 100%);
            border-top: 1px solid #e2e8f0;
            position: relative;
            z-index: 2;
        }
        
        .footer-links a {
            color: #64748b;
            text-decoration: none;
            margin: 0 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            position: relative;
        }
        
        .footer-links a::after {
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
        
        .footer-links a:hover {
            color: #0061f2;
            background: rgba(0, 97, 242, 0.05);
        }
        
        .footer-links a:hover::after {
            width: 100%;
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
                        <img src="<?= base_url('assets/images/logo-optima.png') ?>" alt="OPTIMA Logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div style="display: none; width: 100%; height: 100%; align-items: center; justify-content: center; font-size: 3rem; color: white;">
                            <i class="fas fa-user-plus"></i>
                        </div>
                    </div>
                    <h1 class="register-title">Bergabung</h1>
                    <p class="register-subtitle">Dengan OPTIMA</p>
                    
                
                </div>
                
                <!-- Footer Powered By -->
                <div class="login-footer" style="position: relative; z-index: 2; padding: 2rem 2rem 1.5rem; text-align: center; border-top: 1px solid rgba(255, 255, 255, 0.15); margin-top: auto; background: rgba(0, 0, 0, 0.05); backdrop-filter: blur(10px);">
                    <div class="login-footer-content" style="display: flex; align-items: center; justify-content: center; gap: 0.75rem; flex-wrap: wrap;">
                        <span class="login-footer-text" style="font-size: 0.875rem; opacity: 0.95; color: white; font-weight: 500; letter-spacing: 0.02em;">Powered by</span>
                        <img src="<?= base_url('assets/images/company-logo.png') ?>" 
                             alt="PT Sarana Mitra Luas Tbk" 
                             class="login-footer-logo" 
                             style="height: 45px; width: auto; max-width: 220px; opacity: 1; filter: none; -webkit-filter: none; transition: transform 0.3s ease, opacity 0.3s ease; display: inline-block; vertical-align: middle; object-fit: contain; background: rgba(255, 255, 255, 0.15); padding: 6px 12px; border-radius: 6px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);">
                        <span class="login-footer-text" style="font-size: 0.875rem; opacity: 0.95; color: white; font-weight: 500; letter-spacing: 0.02em;">PT SARANA MITRA LUAS Tbk</span>
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
                <form action="<?= base_url('auth/attempt-register') ?>" method="post" id="registerForm" novalidate>
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="first_name" name="first_name" 
                                       placeholder="Nama Depan" value="<?= old('first_name') ?>">
                                <label for="first_name">
                                    <i class="fas fa-user me-2"></i>Nama Depan
                                </label>
                                <div class="invalid-feedback" style="display: none;">
                                    Silakan masukkan nama depan.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="last_name" name="last_name" 
                                       placeholder="Nama Belakang" value="<?= old('last_name') ?>">
                                <label for="last_name">
                                    <i class="fas fa-user me-2"></i>Nama Belakang
                                </label>
                                <div class="invalid-feedback" style="display: none;">
                                    Silakan masukkan nama belakang.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="username" name="username" 
                                       placeholder="username" value="<?= old('username') ?>">
                                <label for="username">
                                    <i class="fas fa-user-circle me-2"></i>Username
                                </label>
                                <div class="invalid-feedback" style="display: none;">
                                    Silakan masukkan username (minimal 3 karakter).
                                </div>
                                <div class="form-text" style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">
                                    Minimal 3 karakter, hanya huruf, angka, dan underscore
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="email" class="form-control" id="email" name="email" 
                                       placeholder="name@example.com" value="<?= old('email') ?>">
                                <label for="email">
                                    <i class="fas fa-envelope me-2"></i>Email
                                </label>
                                <div class="invalid-feedback" style="display: none;">
                                    Silakan masukkan email yang valid.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-floating">
                        <input type="tel" class="form-control" id="phone" name="phone" 
                               placeholder="+62 812-3456-7890" value="<?= old('phone') ?>">
                        <label for="phone">
                            <i class="fas fa-phone me-2"></i>Nomor Telepon
                        </label>
                        <div class="invalid-feedback" style="display: none;">
                            Silakan masukkan nomor telepon yang valid.
                        </div>
                        <div class="form-text" style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">
                            Contoh: +62 812-3456-7890 (opsional)
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select" id="division_id" name="division_id">
                                    <option value="">Pilih Divisi</option>
                                    <?php if (!empty($divisions)): ?>
                                        <?php 
                                        $oldDivisionId = old('division_id');
                                        foreach ($divisions as $divId => $divName): 
                                        ?>
                                            <option value="<?= $divId ?>" <?= (!empty($oldDivisionId) && $oldDivisionId == $divId) ? 'selected' : '' ?>>
                                                <?= esc($divName) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <label for="division_id">
                                    <i class="fas fa-building me-2"></i>Divisi
                                </label>
                                <div class="invalid-feedback" style="display: none;">
                                    Silakan pilih divisi.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select" id="position" name="position">
                                    <option value="">Pilih Posisi</option>
                                    <option value="Head of Divisi" <?= old('position') == 'Head of Divisi' ? 'selected' : '' ?>>Head of Divisi</option>
                                    <option value="Staff Admin" <?= old('position') == 'Staff Admin' ? 'selected' : '' ?>>Staff Admin</option>
                                    <option value="Mechanic" <?= old('position') == 'Mechanic' ? 'selected' : '' ?>>Mechanic</option>
                                </select>
                                <label for="position">
                                    <i class="fas fa-briefcase me-2"></i>Posisi
                                </label>
                                <div class="invalid-feedback" style="display: none;">
                                    Silakan pilih posisi.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-floating">
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Password">
                        <label for="password">
                            <i class="fas fa-lock me-2"></i>Password
                        </label>
                        <div class="invalid-feedback" style="display: none;">
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
                               placeholder="Konfirmasi Password">
                        <label for="confirm_password">
                            <i class="fas fa-lock me-2"></i>Konfirmasi Password
                        </label>
                        <div class="invalid-feedback" style="display: none;">
                            Password tidak cocok.
                        </div>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="terms" name="terms">
                        <label class="form-check-label" for="terms">
                            Saya menyetujui <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Syarat & Ketentuan</a> 
                            serta <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal">Kebijakan Privasi</a>
                        </label>
                        <div class="invalid-feedback" style="display: none;">
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
        // Custom Form Validation - Only show errors after submit
        const registerForm = document.getElementById('registerForm');
        let formSubmitted = false;
        
        registerForm.addEventListener('submit', function(event) {
            event.preventDefault();
            event.stopPropagation();
            formSubmitted = true;
            
            // Validate all fields
            let isValid = true;
            
            // First Name
            const firstName = document.getElementById('first_name');
            const firstNameFeedback = firstName.parentElement.querySelector('.invalid-feedback');
            if (!firstName.value.trim()) {
                isValid = false;
                firstName.classList.add('is-invalid');
                firstNameFeedback.style.display = 'block';
            } else {
                firstName.classList.remove('is-invalid');
                firstName.classList.add('is-valid');
                firstNameFeedback.style.display = 'none';
            }
            
            // Last Name
            const lastName = document.getElementById('last_name');
            const lastNameFeedback = lastName.parentElement.querySelector('.invalid-feedback');
            if (!lastName.value.trim()) {
                isValid = false;
                lastName.classList.add('is-invalid');
                lastNameFeedback.style.display = 'block';
            } else {
                lastName.classList.remove('is-invalid');
                lastName.classList.add('is-valid');
                lastNameFeedback.style.display = 'none';
            }
            
            // Username
            const username = document.getElementById('username');
            const usernameFeedback = username.parentElement.querySelector('.invalid-feedback');
            const usernameRegex = /^[a-zA-Z0-9_]{3,20}$/;
            if (!username.value.trim() || !usernameRegex.test(username.value)) {
                isValid = false;
                username.classList.add('is-invalid');
                usernameFeedback.style.display = 'block';
            } else {
                username.classList.remove('is-invalid');
                username.classList.add('is-valid');
                usernameFeedback.style.display = 'none';
            }
            
            // Email
            const email = document.getElementById('email');
            const emailFeedback = email.parentElement.querySelector('.invalid-feedback');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email.value.trim() || !emailRegex.test(email.value)) {
                isValid = false;
                email.classList.add('is-invalid');
                emailFeedback.style.display = 'block';
            } else {
                email.classList.remove('is-invalid');
                email.classList.add('is-valid');
                emailFeedback.style.display = 'none';
            }
            
            // Division
            const divisionId = document.getElementById('division_id');
            const divisionFeedback = divisionId.parentElement.querySelector('.invalid-feedback');
            if (!divisionId.value) {
                isValid = false;
                divisionId.classList.add('is-invalid');
                divisionFeedback.style.display = 'block';
            } else {
                divisionId.classList.remove('is-invalid');
                divisionId.classList.add('is-valid');
                divisionFeedback.style.display = 'none';
            }
            
            // Position
            const position = document.getElementById('position');
            const positionFeedback = position.parentElement.querySelector('.invalid-feedback');
            if (!position.value) {
                isValid = false;
                position.classList.add('is-invalid');
                positionFeedback.style.display = 'block';
            } else {
                position.classList.remove('is-invalid');
                position.classList.add('is-valid');
                positionFeedback.style.display = 'none';
            }
            
            // Password
            const password = document.getElementById('password');
            const passwordFeedback = password.parentElement.querySelector('.invalid-feedback');
            if (!password.value || password.value.length < 8) {
                isValid = false;
                password.classList.add('is-invalid');
                passwordFeedback.style.display = 'block';
            } else {
                password.classList.remove('is-invalid');
                password.classList.add('is-valid');
                passwordFeedback.style.display = 'none';
            }
            
            // Confirm Password
            const confirmPassword = document.getElementById('confirm_password');
            const confirmPasswordFeedback = confirmPassword.parentElement.querySelector('.invalid-feedback');
            if (!confirmPassword.value || confirmPassword.value !== password.value) {
                isValid = false;
                confirmPassword.classList.add('is-invalid');
                confirmPasswordFeedback.style.display = 'block';
            } else {
                confirmPassword.classList.remove('is-invalid');
                confirmPassword.classList.add('is-valid');
                confirmPasswordFeedback.style.display = 'none';
            }
            
            // Terms
            const terms = document.getElementById('terms');
            const termsFeedback = terms.parentElement.querySelector('.invalid-feedback');
            if (!terms.checked) {
                isValid = false;
                terms.classList.add('is-invalid');
                termsFeedback.style.display = 'block';
            } else {
                terms.classList.remove('is-invalid');
                termsFeedback.style.display = 'none';
            }
            
            if (isValid) {
                // Show loading state
                const registerBtn = document.getElementById('registerBtn');
                const registerSpinner = document.getElementById('registerSpinner');
                const registerText = document.getElementById('registerText');
                
                registerBtn.disabled = true;
                registerSpinner.style.display = 'inline-block';
                registerText.textContent = 'Memproses...';
                
                // Submit form
                registerForm.submit();
            } else {
                // Scroll to first error
                const firstError = registerForm.querySelector('.is-invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }
            }
        });
        
        // Remove validation classes on input (only after form was submitted)
        registerForm.querySelectorAll('input, select').forEach(field => {
            field.addEventListener('input', function() {
                if (formSubmitted) {
                    this.classList.remove('is-invalid', 'is-valid');
                    const feedback = this.parentElement.querySelector('.invalid-feedback');
                    if (feedback) {
                        feedback.style.display = 'none';
                    }
                }
            });
        });
        
        
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
            // Ensure division dropdown is not pre-selected
            const divisionSelect = document.getElementById('division_id');
            if (divisionSelect && !divisionSelect.value) {
                divisionSelect.selectedIndex = 0; // Select "Pilih Divisi" option
            }
            
            // Ensure position dropdown is not pre-selected
            const positionSelect = document.getElementById('position');
            if (positionSelect && !positionSelect.value) {
                positionSelect.selectedIndex = 0; // Select "Pilih Posisi" option
            }
            
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