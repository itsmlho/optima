<?php

namespace App\Models;

use CodeIgniter\Model;

class PasswordResetModel extends Model
{
    protected $table = 'password_resets';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    protected $allowedFields = [
        'user_id',
        'email',
        'token',
        'ip_address',
        'user_agent',
        'attempts',
        'is_used',
        'expires_at',
        'used_at',
        'created_at',
    ];

    protected $useTimestamps = false; // Manual timestamps
    protected $dateFormat = 'datetime';

    protected $validationRules = [
        'user_id' => 'required|integer',
        'email' => 'required|valid_email',
        'token' => 'required|max_length[255]',
        'ip_address' => 'required|max_length[45]',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Create reset token untuk user
     */
    public function createResetToken(int $userId, string $email, string $ipAddress, ?string $userAgent = null, int $expireHours = 1): ?array
    {
        // Generate token
        $token = bin2hex(random_bytes(32));

        $data = [
            'user_id' => $userId,
            'email' => $email,
            'token' => $token,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'attempts' => 0,
            'is_used' => 0,
            'expires_at' => date('Y-m-d H:i:s', strtotime("+{$expireHours} hours")),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $resetId = $this->insert($data);

        if ($resetId) {
            $data['id'] = $resetId;
            return $data;
        }

        return null;
    }

    /**
     * Validate reset token
     */
    public function validateToken(string $token): ?array
    {
        return $this->where('token', $token)
                   ->where('is_used', 0)
                   ->where('expires_at >', date('Y-m-d H:i:s'))
                   ->first();
    }

    /**
     * Mark token as used
     */
    public function markAsUsed(string $token): bool
    {
        $reset = $this->where('token', $token)->first();

        if ($reset) {
            return $this->update($reset['id'], [
                'is_used' => 1,
                'used_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return false;
    }

    /**
     * Get reset by token
     */
    public function getResetByToken(string $token): ?array
    {
        return $this->where('token', $token)->first();
    }

    /**
     * Check rate limit untuk email dan IP
     */
    public function checkRateLimit(string $email, string $ipAddress, int $maxPerEmail = 3, int $maxPerIP = 5, int $hours = 1): array
    {
        $cutoffTime = date('Y-m-d H:i:s', strtotime("-{$hours} hours"));

        // Count by email
        $emailCount = $this->where('email', $email)
                          ->where('created_at >', $cutoffTime)
                          ->countAllResults(false);

        // Count by IP
        $ipCount = $this->where('ip_address', $ipAddress)
                       ->where('created_at >', $cutoffTime)
                       ->countAllResults(false);

        $allowed = ($emailCount < $maxPerEmail) && ($ipCount < $maxPerIP);

        return [
            'allowed' => $allowed,
            'email_count' => $emailCount,
            'ip_count' => $ipCount,
            'max_per_email' => $maxPerEmail,
            'max_per_ip' => $maxPerIP,
            'remaining_email' => max(0, $maxPerEmail - $emailCount),
            'remaining_ip' => max(0, $maxPerIP - $ipCount),
        ];
    }

    /**
     * Increment attempts untuk reset token
     */
    public function incrementAttempts(int $resetId): bool
    {
        return $this->set('attempts', 'attempts + 1', false)
                   ->where('id', $resetId)
                   ->update();
    }

    /**
     * Clean expired tokens
     */
    public function cleanExpiredTokens(): int
    {
        return $this->where('expires_at <', date('Y-m-d H:i:s'))
                   ->orWhere('is_used', 1)
                   ->delete();
    }

    /**
     * Check if token is valid
     */
    public function isTokenValid(string $token): bool
    {
        $reset = $this->validateToken($token);
        return $reset !== null;
    }

    /**
     * Get all resets untuk user (untuk cleanup)
     */
    public function getUserResets(int $userId, bool $activeOnly = true): array
    {
        $builder = $this->where('user_id', $userId);

        if ($activeOnly) {
            $builder->where('is_used', 0)
                   ->where('expires_at >', date('Y-m-d H:i:s'));
        }

        return $builder->orderBy('created_at', 'DESC')->findAll();
    }

    /**
     * Invalidate all tokens untuk user (setelah password berhasil direset)
     */
    public function invalidateUserTokens(int $userId): int
    {
        $tokens = $this->where('user_id', $userId)
                      ->where('is_used', 0)
                      ->findAll();

        $count = 0;
        foreach ($tokens as $token) {
            $this->update($token['id'], [
                'is_used' => 1,
                'used_at' => date('Y-m-d H:i:s'),
            ]);
            $count++;
        }

        return $count;
    }
}

