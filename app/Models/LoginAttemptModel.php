<?php

namespace App\Models;

use CodeIgniter\Model;

class LoginAttemptModel extends Model
{
    protected $table = 'login_attempts';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    protected $allowedFields = [
        'identifier',
        'ip_address',
        'user_agent',
        'attempts',
        'last_attempt_at',
        'locked_until',
        'is_successful',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'identifier' => 'required|max_length[255]',
        'ip_address' => 'required|max_length[45]',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Record login attempt
     */
    public function recordAttempt(string $identifier, string $ipAddress, ?string $userAgent = null, bool $isSuccessful = false): int
    {
        // Check jika sudah ada record untuk identifier + IP ini
        $existing = $this->where('identifier', $identifier)
                        ->where('ip_address', $ipAddress)
                        ->first();

        if ($existing) {
            // Update existing record
            $data = [
                'attempts' => $existing['attempts'] + 1,
                'last_attempt_at' => date('Y-m-d H:i:s'),
                'is_successful' => $isSuccessful ? 1 : 0,
                'user_agent' => $userAgent ?? $existing['user_agent'],
            ];

            // Reset lock jika login sukses
            if ($isSuccessful) {
                $data['locked_until'] = null;
                $data['attempts'] = 0;
            }

            $this->update($existing['id'], $data);
            return $existing['id'];
        } else {
            // Create new record
            $data = [
                'identifier' => $identifier,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'attempts' => 1,
                'last_attempt_at' => date('Y-m-d H:i:s'),
                'is_successful' => $isSuccessful ? 1 : 0,
            ];

            return $this->insert($data);
        }
    }

    /**
     * Check rate limit untuk identifier dan IP
     */
    public function checkRateLimit(string $identifier, string $ipAddress, int $maxAttempts = 5): array
    {
        $record = $this->where('identifier', $identifier)
                      ->where('ip_address', $ipAddress)
                      ->first();

        if (!$record) {
            return [
                'allowed' => true,
                'attempts' => 0,
                'remaining' => $maxAttempts,
                'locked_until' => null,
            ];
        }

        // Check if locked
        if ($record['locked_until'] && strtotime($record['locked_until']) > time()) {
            return [
                'allowed' => false,
                'attempts' => $record['attempts'],
                'remaining' => 0,
                'locked_until' => $record['locked_until'],
            ];
        }

        // Check if max attempts reached
        if ($record['attempts'] >= $maxAttempts) {
            return [
                'allowed' => false,
                'attempts' => $record['attempts'],
                'remaining' => 0,
                'locked_until' => null,
            ];
        }

        return [
            'allowed' => true,
            'attempts' => $record['attempts'],
            'remaining' => $maxAttempts - $record['attempts'],
            'locked_until' => $record['locked_until'],
        ];
    }

    /**
     * Lock account untuk identifier dan IP
     */
    public function lockAccount(string $identifier, string $ipAddress, int $minutes = 15): bool
    {
        $record = $this->where('identifier', $identifier)
                      ->where('ip_address', $ipAddress)
                      ->first();

        if ($record) {
            return $this->update($record['id'], [
                'locked_until' => date('Y-m-d H:i:s', strtotime("+{$minutes} minutes")),
            ]);
        }

        // Create new record with lock
        return $this->insert([
            'identifier' => $identifier,
            'ip_address' => $ipAddress,
            'attempts' => 5, // Max attempts
            'locked_until' => date('Y-m-d H:i:s', strtotime("+{$minutes} minutes")),
            'last_attempt_at' => date('Y-m-d H:i:s'),
        ]) !== false;
    }

    /**
     * Check jika account locked
     */
    public function isLocked(string $identifier, string $ipAddress): bool
    {
        $record = $this->where('identifier', $identifier)
                      ->where('ip_address', $ipAddress)
                      ->first();

        if (!$record || !$record['locked_until']) {
            return false;
        }

        return strtotime($record['locked_until']) > time();
    }

    /**
     * Reset attempts untuk identifier dan IP (after successful login)
     */
    public function resetAttempts(string $identifier, string $ipAddress): bool
    {
        $record = $this->where('identifier', $identifier)
                      ->where('ip_address', $ipAddress)
                      ->first();

        if ($record) {
            return $this->update($record['id'], [
                'attempts' => 0,
                'locked_until' => null,
                'is_successful' => 1,
            ]);
        }

        return true;
    }

    /**
     * Clean old attempts (older than X days)
     */
    public function cleanOldAttempts(int $days = 30): int
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        return $this->where('created_at <', $cutoffDate)->delete();
    }

    /**
     * Get remaining attempts
     */
    public function getRemainingAttempts(string $identifier, string $ipAddress, int $maxAttempts = 5): int
    {
        $record = $this->where('identifier', $identifier)
                      ->where('ip_address', $ipAddress)
                      ->first();

        if (!$record) {
            return $maxAttempts;
        }

        if ($this->isLocked($identifier, $ipAddress)) {
            return 0;
        }

        return max(0, $maxAttempts - $record['attempts']);
    }

    /**
     * Get lock time remaining in seconds
     */
    public function getLockTimeRemaining(string $identifier, string $ipAddress): ?int
    {
        $record = $this->where('identifier', $identifier)
                      ->where('ip_address', $ipAddress)
                      ->first();

        if (!$record || !$record['locked_until']) {
            return null;
        }

        $remaining = strtotime($record['locked_until']) - time();
        return $remaining > 0 ? $remaining : null;
    }
}

