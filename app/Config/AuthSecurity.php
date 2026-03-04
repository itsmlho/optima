<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class AuthSecurity extends BaseConfig
{
    // OTP Settings
    public int $otpLength = 6;
    public int $otpExpireMinutes = 5;
    public int $otpMaxAttempts = 5;
    public int $otpResendCooldownSeconds = 60;
    
    // Rate Limiting Settings
    public int $maxLoginAttempts = 10;
    public int $lockDurationMinutes = 3;
    public bool $attemptsResetAfterSuccess = true;
    
    // Forgot Password Rate Limiting
    public int $maxForgotPasswordRequestsPerEmail = 1; // per hour
    public int $maxForgotPasswordRequestsPerIP = 5; // per hour
    public int $forgotPasswordRateLimitHours = 1;
    
    // Password Reset Settings
    public int $resetTokenExpireHours = 1; // 1 hour
    public bool $resetTokenSingleUse = true; // Token hanya bisa digunakan sekali
    
    // Session Settings
    public int $sessionIdleTimeoutHours = 2;
    public bool $allowMultipleSessions = true;
    public int $maxActiveSessions = 10;
    public bool $autoLogoutIdleSessions = true;
    
    // Device Tracking
    public bool $trackDevices = true;
    public bool $showDeviceInfo = true;
}

