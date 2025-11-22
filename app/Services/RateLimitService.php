<?php

namespace App\Services;

use App\Models\LoginAttemptModel;
use Config\AuthSecurity;

class RateLimitService
{
    protected $loginAttemptModel;
    protected $config;

    public function __construct()
    {
        $this->loginAttemptModel = new LoginAttemptModel();
        $this->config = config('AuthSecurity');
    }

    /**
     * Check and record login attempt
     * Returns array with 'allowed', 'message', 'remaining_attempts', 'locked_until'
     */
    public function checkAndRecord(string $identifier, string $ipAddress, ?string $userAgent = null, bool $isSuccessful = false): array
    {
        // Check rate limit before recording
        $rateLimit = $this->loginAttemptModel->checkRateLimit(
            $identifier,
            $ipAddress,
            $this->config->maxLoginAttempts
        );

        // If locked, don't record new attempt
        if (!$rateLimit['allowed']) {
            if ($rateLimit['locked_until']) {
                $lockedUntil = strtotime($rateLimit['locked_until']);
                $remainingSeconds = $lockedUntil - time();
                $remainingMinutes = ceil($remainingSeconds / 60);

                return [
                    'allowed' => false,
                    'message' => "Akun Anda terkunci karena terlalu banyak percobaan login yang gagal. Silakan coba lagi dalam {$remainingMinutes} menit.",
                    'remaining_attempts' => 0,
                    'locked_until' => $rateLimit['locked_until'],
                    'locked_until_timestamp' => $lockedUntil,
                ];
            }

            return [
                'allowed' => false,
                'message' => 'Terlalu banyak percobaan login yang gagal. Akun Anda telah terkunci.',
                'remaining_attempts' => 0,
                'locked_until' => null,
            ];
        }

        // Record attempt
        $this->loginAttemptModel->recordAttempt($identifier, $ipAddress, $userAgent, $isSuccessful);

        // Check again after recording (in case we just hit the limit)
        $rateLimit = $this->loginAttemptModel->checkRateLimit(
            $identifier,
            $ipAddress,
            $this->config->maxLoginAttempts
        );

        // If after recording we hit the limit, lock account
        if (!$rateLimit['allowed'] && !$isSuccessful && $rateLimit['attempts'] >= $this->config->maxLoginAttempts) {
            $this->loginAttemptModel->lockAccount(
                $identifier,
                $ipAddress,
                $this->config->lockDurationMinutes
            );

            return [
                'allowed' => false,
                'message' => "Akun Anda terkunci selama {$this->config->lockDurationMinutes} menit karena terlalu banyak percobaan login yang gagal.",
                'remaining_attempts' => 0,
                'locked_until' => date('Y-m-d H:i:s', strtotime("+{$this->config->lockDurationMinutes} minutes")),
                'locked_until_timestamp' => time() + ($this->config->lockDurationMinutes * 60),
            ];
        }

        // If successful, reset attempts
        if ($isSuccessful && $this->config->attemptsResetAfterSuccess) {
            $this->loginAttemptModel->resetAttempts($identifier, $ipAddress);
            $rateLimit['remaining'] = $this->config->maxLoginAttempts;
        }

        return [
            'allowed' => true,
            'message' => null,
            'remaining_attempts' => $rateLimit['remaining'],
            'locked_until' => null,
        ];
    }

    /**
     * Check if login is allowed
     */
    public function isAllowed(string $identifier, string $ipAddress): bool
    {
        $rateLimit = $this->loginAttemptModel->checkRateLimit(
            $identifier,
            $ipAddress,
            $this->config->maxLoginAttempts
        );

        return $rateLimit['allowed'];
    }

    /**
     * Lock account
     */
    public function lockAccount(string $identifier, string $ipAddress, ?int $minutes = null): bool
    {
        $lockMinutes = $minutes ?? $this->config->lockDurationMinutes;
        return $this->loginAttemptModel->lockAccount($identifier, $ipAddress, $lockMinutes);
    }

    /**
     * Get remaining attempts
     */
    public function getRemainingAttempts(string $identifier, string $ipAddress): int
    {
        return $this->loginAttemptModel->getRemainingAttempts(
            $identifier,
            $ipAddress,
            $this->config->maxLoginAttempts
        );
    }

    /**
     * Get lock time remaining in seconds
     */
    public function getLockTimeRemaining(string $identifier, string $ipAddress): ?int
    {
        return $this->loginAttemptModel->getLockTimeRemaining($identifier, $ipAddress);
    }

    /**
     * Get lock time remaining in minutes
     */
    public function getLockTimeRemainingMinutes(string $identifier, string $ipAddress): ?int
    {
        $seconds = $this->getLockTimeRemaining($identifier, $ipAddress);
        return $seconds !== null ? ceil($seconds / 60) : null;
    }

    /**
     * Reset attempts (after successful login)
     */
    public function resetAttempts(string $identifier, string $ipAddress): bool
    {
        return $this->loginAttemptModel->resetAttempts($identifier, $ipAddress);
    }

    /**
     * Get rate limit status info
     */
    public function getRateLimitStatus(string $identifier, string $ipAddress): array
    {
        $rateLimit = $this->loginAttemptModel->checkRateLimit(
            $identifier,
            $ipAddress,
            $this->config->maxLoginAttempts
        );

        $lockedUntil = $rateLimit['locked_until'] ?? null;
        $lockedUntilTimestamp = $lockedUntil ? strtotime($lockedUntil) : null;

        return [
            'allowed' => $rateLimit['allowed'],
            'attempts' => $rateLimit['attempts'],
            'remaining' => $rateLimit['remaining'],
            'max_attempts' => $this->config->maxLoginAttempts,
            'locked' => !$rateLimit['allowed'] && $lockedUntil !== null,
            'locked_until' => $lockedUntil,
            'locked_until_timestamp' => $lockedUntilTimestamp,
            'lock_duration_minutes' => $this->config->lockDurationMinutes,
        ];
    }
}

