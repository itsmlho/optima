<?php

namespace App\Models;

use CodeIgniter\Model;

class UserSessionModel extends Model
{
    protected $table            = 'user_sessions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    
    protected $allowedFields    = [
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
        'logout_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get active sessions for a user
     */
    public function getActiveSessions(int $userId): array
    {
        return $this->where('user_id', $userId)
                    ->where('is_active', 1)
                    ->orderBy('last_activity', 'DESC')
                    ->findAll();
    }

    /**
     * Get session by session ID
     */
    public function getBySessionId(string $sessionId): ?array
    {
        return $this->where('session_id', $sessionId)->first();
    }

    /**
     * Deactivate session
     */
    public function deactivateSession(string $sessionId): bool
    {
        return $this->where('session_id', $sessionId)
                    ->set([
                        'is_active' => 0,
                        'logout_at' => date('Y-m-d H:i:s')
                    ])
                    ->update();
    }

    /**
     * Deactivate all sessions for a user
     */
    public function deactivateAllSessions(int $userId): bool
    {
        return $this->where('user_id', $userId)
                    ->where('is_active', 1)
                    ->set([
                        'is_active' => 0,
                        'logout_at' => date('Y-m-d H:i:s')
                    ])
                    ->update();
    }

    /**
     * Update last activity
     */
    public function updateActivity(string $sessionId): bool
    {
        return $this->where('session_id', $sessionId)
                    ->set('last_activity', date('Y-m-d H:i:s'))
                    ->update();
    }

    /**
     * Clean up inactive sessions older than X days
     */
    public function cleanupOldSessions(int $days = 30): int
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        return $this->where('is_active', 0)
                    ->where('logout_at <', $cutoffDate)
                    ->delete();
    }

    /**
     * Count active sessions for a user
     */
    public function countActiveSessions(int $userId): int
    {
        return $this->where('user_id', $userId)
                    ->where('is_active', 1)
                    ->countAllResults();
    }

    /**
     * Get session by device ID
     */
    public function getByDeviceId(int $userId, string $deviceId): ?array
    {
        return $this->where('user_id', $userId)
                    ->where('device_id', $deviceId)
                    ->where('is_active', 1)
                    ->first();
    }
}
