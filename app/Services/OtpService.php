<?php

namespace App\Services;

use App\Models\OtpModel;
use App\Models\UserModel;
use Config\AuthSecurity;
use Config\Services;

class OtpService
{
    protected $otpModel;
    protected $userModel;
    protected $config;

    public function __construct()
    {
        $this->otpModel = new OtpModel();
        $this->userModel = new UserModel();
        $this->config = config('AuthSecurity');
    }

    /**
     * Generate OTP untuk user
     */
    public function generateOtp(int $userId, string $email): ?array
    {
        $user = $this->userModel->find($userId);
        
        if (!$user) {
            return null;
        }

        // Clean expired OTPs untuk user ini
        $this->otpModel->cleanExpiredOtps($userId);

        // Check if user already has active OTP
        $activeOtp = $this->otpModel->getActiveOtp($userId);
        if ($activeOtp) {
            // Check cooldown
            $createdAt = strtotime($activeOtp['created_at']);
            $cooldownEnd = $createdAt + $this->config->otpResendCooldownSeconds;
            
            if (time() < $cooldownEnd) {
                $remainingSeconds = $cooldownEnd - time();
                return [
                    'error' => 'cooldown',
                    'remaining_seconds' => $remainingSeconds,
                    'message' => "Silakan tunggu {$remainingSeconds} detik sebelum request OTP baru.",
                ];
            }
        }

        $ipAddress = Services::request()->getIPAddress();
        
        // Generate OTP
        $otp = $this->otpModel->generateOtp(
            $userId,
            $email,
            $ipAddress,
            $this->config->otpExpireMinutes
        );

        if (!$otp) {
            return null;
        }

        // Send OTP via email
        $emailSent = $this->sendOtpEmail($userId, $email, $otp['otp_code']);

        if (!$emailSent) {
            return [
                'error' => 'email_failed',
                'message' => 'Gagal mengirim email OTP. Silakan coba lagi.',
            ];
        }

        return [
            'success' => true,
            'otp_id' => $otp['id'],
            'expires_at' => $otp['expires_at'],
            'email' => $email,
            'message' => 'OTP telah dikirim ke email Anda.',
        ];
    }

    /**
     * Validate OTP code
     */
    public function validateOtp(string $otpCode, int $userId): array
    {
        // Clean expired OTPs
        $this->otpModel->cleanExpiredOtps($userId);

        // Get active OTP
        $otp = $this->otpModel->getActiveOtp($userId);

        if (!$otp) {
            return [
                'valid' => false,
                'message' => 'OTP tidak ditemukan atau sudah expired. Silakan request OTP baru.',
            ];
        }

        // Check if OTP code matches
        if ($otp['otp_code'] !== $otpCode) {
            // Increment attempts
            $this->otpModel->incrementAttempts($otp['id']);
            
            $remainingAttempts = $otp['max_attempts'] - ($otp['attempts'] + 1);
            
            if ($remainingAttempts <= 0) {
                return [
                    'valid' => false,
                    'message' => 'Terlalu banyak percobaan OTP yang salah. Silakan request OTP baru.',
                    'max_attempts_reached' => true,
                ];
            }

            return [
                'valid' => false,
                'message' => 'Kode OTP salah. Sisa percobaan: ' . $remainingAttempts,
                'remaining_attempts' => $remainingAttempts,
            ];
        }

        // Check if attempts exceeded
        if ($otp['attempts'] >= $otp['max_attempts']) {
            return [
                'valid' => false,
                'message' => 'Terlalu banyak percobaan OTP yang salah. Silakan request OTP baru.',
                'max_attempts_reached' => true,
            ];
        }

        // OTP is valid - mark as verified
        $this->otpModel->markAsVerified($otp['id']);

        return [
            'valid' => true,
            'message' => 'OTP berhasil diverifikasi.',
            'otp_id' => $otp['id'],
        ];
    }

    /**
     * Send OTP via email
     */
    public function sendOtpEmail(int $userId, string $email, string $otpCode): bool
    {
        $user = $this->userModel->find($userId);
        
        if (!$user) {
            return false;
        }

        $emailService = Services::email();
        $emailConfig = config('Email');

        // Set email configuration
        $emailService->setFrom($emailConfig->fromEmail ?? 'noreply@optima.local', $emailConfig->fromName ?? 'OPTIMA System');
        $emailService->setTo($email);
        
        $subject = 'Kode OTP untuk Login - OPTIMA';
        $message = view('emails/otp_verification', [
            'user' => $user,
            'otp_code' => $otpCode,
            'expire_minutes' => $this->config->otpExpireMinutes,
            'app_name' => 'OPTIMA',
            'support_email' => $emailConfig->fromEmail ?? 'support@optima.local',
        ]);

        $emailService->setSubject($subject);
        $emailService->setMessage($message);

        try {
            return $emailService->send();
        } catch (\Exception $e) {
            log_message('error', 'Failed to send OTP email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if user can request new OTP (cooldown check)
     */
    public function canRequestNewOtp(int $userId): array
    {
        $activeOtp = $this->otpModel->getActiveOtp($userId);
        
        if (!$activeOtp) {
            return [
                'allowed' => true,
                'remaining_seconds' => 0,
            ];
        }

        $createdAt = strtotime($activeOtp['created_at']);
        $cooldownEnd = $createdAt + $this->config->otpResendCooldownSeconds;
        
        if (time() < $cooldownEnd) {
            $remainingSeconds = $cooldownEnd - time();
            return [
                'allowed' => false,
                'remaining_seconds' => $remainingSeconds,
            ];
        }

        return [
            'allowed' => true,
            'remaining_seconds' => 0,
        ];
    }

    /**
     * Clean expired OTPs
     */
    public function cleanExpiredOtps(?int $userId = null): int
    {
        return $this->otpModel->cleanExpiredOtps($userId);
    }
}

