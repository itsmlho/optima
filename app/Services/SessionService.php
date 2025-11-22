<?php

namespace App\Services;

use App\Models\UserSessionModel;
use Config\AuthSecurity;
use Config\Services;

class SessionService
{
    protected $userSessionModel;
    protected $config;

    public function __construct()
    {
        $this->userSessionModel = new UserSessionModel();
        $this->config = config('AuthSecurity');
    }

    /**
     * Track new session saat login
     */
    public function trackSession(int $userId, string $sessionId): bool
    {
        if (!$this->config->trackDevices) {
            return false;
        }

        $request = Services::request();
        $userAgent = $request->getUserAgent()->getAgentString();
        $ipAddress = $request->getIPAddress();

        // Get device info
        helper('device');
        $browser = getBrowserInfo($userAgent);
        $os = getOSInfo($userAgent);
        $deviceType = getDeviceType($userAgent);
        $deviceId = generateDeviceId($userAgent, $ipAddress);
        $deviceName = getDeviceName($userAgent);

        $deviceInfo = [
            'device_id' => $deviceId,
            'device_name' => $deviceName,
            'device_type' => $deviceType,
            'browser' => $browser['full'],
            'os' => $os['full'],
            'user_agent' => $userAgent,
        ];

        // Create session tracking
        $sessionIdCreated = $this->userSessionModel->createSession(
            $userId,
            $sessionId,
            $deviceInfo,
            $ipAddress
        );

        return $sessionIdCreated !== false;
    }

    /**
     * Update last activity untuk session
     */
    public function updateActivity(string $sessionId): bool
    {
        if (!$this->config->trackDevices) {
            return false;
        }

        try {
            // Check if table exists before trying to update
            $db = \Config\Database::connect();
            if (!$db->tableExists('user_sessions')) {
                return false; // Table doesn't exist - migration not run yet
            }
            
            return $this->userSessionModel->updateActivity($sessionId);
        } catch (\Exception $e) {
            // Silently fail if table doesn't exist
            log_message('debug', 'Session activity update failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all active sessions untuk user
     */
    public function getUserSessions(int $userId, bool $activeOnly = true): array
    {
        return $this->userSessionModel->getUserSessions($userId, $activeOnly);
    }

    /**
     * Logout specific session
     */
    public function logoutSession(string $sessionId, int $userId): array
    {
        $result = [
            'success' => false,
            'message' => '',
            'session_ids' => [],
        ];

        // Update session in database
        $session = $this->userSessionModel->logoutSession($sessionId, $userId);

        if ($session) {
            // Destroy actual session
            $sessionData = \Config\Services::session();
            
            // Get all session IDs for this user to destroy
            $sessions = $this->userSessionModel->getUserSessions($userId, true);
            $sessionIds = [];
            
            foreach ($sessions as $s) {
                if ($s['session_id'] === $sessionId) {
                    $sessionIds[] = $sessionId;
                    break;
                }
            }

            $result = [
                'success' => true,
                'message' => 'Session berhasil di-logout.',
                'session_ids' => $sessionIds,
            ];
        } else {
            $result['message'] = 'Session tidak ditemukan atau sudah tidak aktif.';
        }

        return $result;
    }

    /**
     * Logout all sessions untuk user (except current)
     */
    public function logoutAllSessions(int $userId, bool $exceptCurrent = true): array
    {
        $result = [
            'success' => false,
            'message' => '',
            'session_ids' => [],
        ];

        // Get session IDs to logout
        $sessionIds = $this->userSessionModel->logoutAllSessions($userId, $exceptCurrent);

        if (!empty($sessionIds)) {
            // Destroy sessions (CodeIgniter will handle session destruction automatically)
            $result = [
                'success' => true,
                'message' => count($sessionIds) . ' session berhasil di-logout.',
                'session_ids' => $sessionIds,
            ];
        } else {
            $result['message'] = 'Tidak ada session lain yang aktif.';
        }

        return $result;
    }

    /**
     * Clean inactive sessions (idle > X hours)
     */
    public function cleanInactiveSessions(): int
    {
        if (!$this->config->autoLogoutIdleSessions) {
            return 0;
        }

        return $this->userSessionModel->cleanInactiveSessions($this->config->sessionIdleTimeoutHours);
    }

    /**
     * Get active session count untuk user
     */
    public function getActiveSessionCount(int $userId): int
    {
        return $this->userSessionModel->getActiveSessionCount($userId);
    }

    /**
     * Check if session is active
     */
    public function isSessionActive(string $sessionId): bool
    {
        return $this->userSessionModel->isSessionActive($sessionId);
    }

    /**
     * Get device info untuk display
     */
    public function getDeviceInfo(?string $userAgent = null): array
    {
        helper('device');
        
        if (empty($userAgent)) {
            $request = Services::request();
            $userAgent = $request->getUserAgent()->getAgentString();
        }

        return [
            'browser' => getBrowserInfo($userAgent),
            'os' => getOSInfo($userAgent),
            'device_type' => getDeviceType($userAgent),
            'device_name' => getDeviceName($userAgent),
            'device_id' => generateDeviceId($userAgent, Services::request()->getIPAddress()),
        ];
    }
}

