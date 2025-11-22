<?php

namespace App\Models;

use CodeIgniter\Model;

class UserSessionModel extends Model
{
    protected $table = 'user_sessions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    protected $allowedFields = [
        'user_id',
        'session_id',
        'device_id',
        'device_name',
        'device_type',
        'browser',
        'os',
        'ip_address',
        'user_agent',
        'is_active',
        'last_activity',
        'login_at',
        'logout_at',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'user_id' => 'required|integer',
        'session_id' => 'required|max_length[128]',
        'device_id' => 'required|max_length[255]',
        'ip_address' => 'required|max_length[45]',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Create new session tracking
     */
    public function createSession(int $userId, string $sessionId, array $deviceInfo, string $ipAddress): ?int
    {
        // Check if session already exists
        $existing = $this->where('session_id', $sessionId)->first();
        if ($existing) {
            // Update existing session
            return $this->update($existing['id'], [
                'is_active' => 1,
                'last_activity' => date('Y-m-d H:i:s'),
                'logout_at' => null,
            ]);
        }

        $data = [
            'user_id' => $userId,
            'session_id' => $sessionId,
            'device_id' => $deviceInfo['device_id'] ?? '',
            'device_name' => $deviceInfo['device_name'] ?? null,
            'device_type' => $deviceInfo['device_type'] ?? null,
            'browser' => $deviceInfo['browser'] ?? null,
            'os' => $deviceInfo['os'] ?? null,
            'ip_address' => $ipAddress,
            'user_agent' => $deviceInfo['user_agent'] ?? null,
            'is_active' => 1,
            'last_activity' => date('Y-m-d H:i:s'),
            'login_at' => date('Y-m-d H:i:s'),
        ];

        return $this->insert($data);
    }

    /**
     * Get user sessions
     */
    public function getUserSessions(int $userId, bool $activeOnly = true): array
    {
        $builder = $this->where('user_id', $userId);

        if ($activeOnly) {
            $builder->where('is_active', 1);
        }

        return $builder->orderBy('last_activity', 'DESC')->findAll();
    }

    /**
     * Update last activity untuk session
     */
    public function updateActivity(string $sessionId): bool
    {
        return $this->where('session_id', $sessionId)
                   ->set('last_activity', date('Y-m-d H:i:s'))
                   ->update();
    }

    /**
     * Logout specific session
     */
    public function logoutSession(string $sessionId, int $userId): bool
    {
        $session = $this->where('session_id', $sessionId)
                       ->where('user_id', $userId)
                       ->first();

        if ($session) {
            return $this->update($session['id'], [
                'is_active' => 0,
                'logout_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return false;
    }

    /**
     * Logout all sessions untuk user
     */
    public function logoutAllSessions(int $userId, bool $exceptCurrent = true): array
    {
        $currentSessionId = session_id();

        $builder = $this->where('user_id', $userId)
                       ->where('is_active', 1);

        if ($exceptCurrent && $currentSessionId) {
            $builder->where('session_id !=', $currentSessionId);
        }

        $sessions = $builder->findAll();

        $sessionIds = [];
        foreach ($sessions as $session) {
            $this->update($session['id'], [
                'is_active' => 0,
                'logout_at' => date('Y-m-d H:i:s'),
            ]);
            $sessionIds[] = $session['session_id'];
        }

        return $sessionIds;
    }

    /**
     * Clean inactive sessions (idle > X hours)
     */
    public function cleanInactiveSessions(int $hours = 2): int
    {
        $cutoffTime = date('Y-m-d H:i:s', strtotime("-{$hours} hours"));

        $sessions = $this->where('is_active', 1)
                        ->where('last_activity <', $cutoffTime)
                        ->findAll();

        $count = 0;
        foreach ($sessions as $session) {
            $this->update($session['id'], [
                'is_active' => 0,
                'logout_at' => date('Y-m-d H:i:s'),
            ]);
            $count++;
        }

        return $count;
    }

    /**
     * Get session by session ID
     */
    public function getSessionBySessionId(string $sessionId): ?array
    {
        return $this->where('session_id', $sessionId)->first();
    }

    /**
     * Get session by ID
     */
    public function getSessionById(int $sessionId): ?array
    {
        return $this->find($sessionId);
    }

    /**
     * Check if session is active
     */
    public function isSessionActive(string $sessionId): bool
    {
        $session = $this->where('session_id', $sessionId)
                       ->where('is_active', 1)
                       ->first();

        return $session !== null;
    }

    /**
     * Get active session count untuk user
     */
    public function getActiveSessionCount(int $userId): int
    {
        return $this->where('user_id', $userId)
                   ->where('is_active', 1)
                   ->countAllResults();
    }
}

