<?php

namespace App\Services;

use App\Models\PasswordResetModel;
use App\Models\UserModel;
use Config\AuthSecurity;
use Config\Services;

class PasswordResetService
{
    protected $passwordResetModel;
    protected $userModel;
    protected $config;

    public function __construct()
    {
        $this->passwordResetModel = new PasswordResetModel();
        $this->userModel = new UserModel();
        $this->config = config('AuthSecurity');
    }

    /**
     * Generate reset token dan kirim email
     */
    public function generateResetToken(int $userId, string $email, string $ipAddress, ?string $userAgent = null, string $subject = 'Reset Password - OPTIMA'): array
    {
        // Check rate limit
        $rateLimit = $this->passwordResetModel->checkRateLimit(
            $email,
            $ipAddress,
            $this->config->maxForgotPasswordRequestsPerEmail,
            $this->config->maxForgotPasswordRequestsPerIP,
            $this->config->forgotPasswordRateLimitHours
        );

        if (!$rateLimit['allowed']) {
            return [
                'success' => false,
                'error' => 'rate_limit',
                'message' => 'Terlalu banyak permintaan reset password. Silakan coba lagi nanti.',
                'rate_limit' => $rateLimit,
            ];
        }

        // Generate reset token
        $reset = $this->passwordResetModel->createResetToken(
            $userId,
            $email,
            $ipAddress,
            $userAgent,
            $this->config->resetTokenExpireHours
        );

        if (!$reset) {
            return [
                'success' => false,
                'error' => 'token_generation_failed',
                'message' => 'Gagal membuat token reset. Silakan coba lagi.',
            ];
        }

        // Send email with reset link
        $emailSent = $this->sendResetEmail($userId, $email, $reset['token'], $subject);

        if (!$emailSent) {
            return [
                'success' => false,
                'error' => 'email_failed',
                'message' => 'Gagal mengirim email reset password. Silakan coba lagi.',
            ];
        }

        return [
            'success' => true,
            'message' => 'Link reset password telah dikirim ke email Anda.',
            'token' => $reset['token'],
            'expires_at' => $reset['expires_at'],
        ];
    }

    /**
     * Send password reset email
     */
    public function sendResetEmail(int $userId, string $email, string $token, string $subject = 'Reset Password - OPTIMA'): bool
    {
        $user = $this->userModel->find($userId);
        
        if (!$user) {
            return false;
        }

        $emailService = Services::email();
        $emailConfig = config('Email');

        // Generate reset link
        $resetLink = base_url('auth/reset-password/' . $token);

        // Set email configuration
        $emailService->setFrom($emailConfig->fromEmail ?? 'noreply@optima.local', $emailConfig->fromName ?? 'OPTIMA System');
        $emailService->setTo($email);

        $message = view('emails/password_reset', [
            'user' => $user,
            'reset_link' => $resetLink,
            'token' => $token,
            'expire_hours' => $this->config->resetTokenExpireHours,
            'app_name' => 'OPTIMA',
            'support_email' => $emailConfig->fromEmail ?? 'support@optima.local',
        ]);

        $emailService->setSubject($subject);
        $emailService->setMessage($message);

        try {
            return $emailService->send();
        } catch (\Exception $e) {
            log_message('error', 'Failed to send password reset email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Validate reset token
     */
    public function validateToken(string $token): ?array
    {
        return $this->passwordResetModel->validateToken($token);
    }

    /**
     * Mark token as used
     */
    public function markTokenAsUsed(string $token): bool
    {
        return $this->passwordResetModel->markAsUsed($token);
    }

    /**
     * Check rate limit untuk forgot password
     */
    public function checkRateLimit(string $email, string $ipAddress): array
    {
        return $this->passwordResetModel->checkRateLimit(
            $email,
            $ipAddress,
            $this->config->maxForgotPasswordRequestsPerEmail,
            $this->config->maxForgotPasswordRequestsPerIP,
            $this->config->forgotPasswordRateLimitHours
        );
    }

    /**
     * Invalidate all tokens untuk user (setelah password berhasil direset)
     */
    public function invalidateUserTokens(int $userId): int
    {
        return $this->passwordResetModel->invalidateUserTokens($userId);
    }

    /**
     * Clean expired tokens
     */
    public function cleanExpiredTokens(): int
    {
        return $this->passwordResetModel->cleanExpiredTokens();
    }
}

