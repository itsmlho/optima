<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\NotificationModel;
use App\Models\NotificationRuleModel;
use App\Models\DivisionModel;
use App\Models\RoleModel;
use App\Traits\ActivityLoggingTrait;

class NotificationController extends BaseController
{
    use ActivityLoggingTrait;

    protected $notificationModel;
    protected $ruleModel;
    protected $db;
    protected $divisionModel;
    protected $roleModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        
        try {
            $this->notificationModel = new NotificationModel();
            $this->ruleModel = new NotificationRuleModel();
            $this->divisionModel = new DivisionModel();
            $this->roleModel = new RoleModel();
        } catch (\Exception $e) {
            log_message('error', 'NotificationController model init error: ' . $e->getMessage());
        }
        
        try {
            $this->initializeActivityLogging();
        } catch (\Exception $e) {
            log_message('debug', 'Activity logging init skipped: ' . $e->getMessage());
        }
    }

    // ========================================================================
    // USER NOTIFICATION CENTER
    // ========================================================================

    /**
     * Notification center page
     */
    public function index()
    {
        if (!$this->canAccess('admin')) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak');
        }

        $userId = session()->get('user_id');
        
        // Get notifications for the user
        $notifications = $this->notificationModel
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll(50);
        
        // Debug: Log notification count
        log_message('debug', 'Notification count for user ' . $userId . ': ' . count($notifications));
        
        // Get notification stats
        $stats = [
            'total' => $this->notificationModel->where('user_id', $userId)->countAllResults(),
            'unread' => $this->notificationModel->where('user_id', $userId)->where('is_read', 0)->countAllResults(),
            'read' => $this->notificationModel->where('user_id', $userId)->where('is_read', 1)->countAllResults(),
            'today' => $this->notificationModel->where('user_id', $userId)->where('DATE(created_at)', date('Y-m-d'))->countAllResults()
        ];

        return view('notifications/user_center', [
            'stats' => $stats,
            'notifications' => $notifications
        ]);
    }

    /**
     * API: Get notifications for current user
     */
    public function getNotifications()
    {
        // For debugging: temporarily disable authentication check
        // if (!$this->canAccess('admin')) {
        //     return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak']);
        // }

        $userId = session()->get('user_id') ?? 1; // Default to user ID 1 for testing
        $limit = (int)($this->request->getGet('limit') ?? 20);
        $offset = (int)($this->request->getGet('offset') ?? 0);

        $notifications = $this->notificationModel
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit($limit, $offset)
            ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'notifications' => $notifications
        ]);
    }

    /**
     * API: Mark notification as read
     */
    public function markAsRead($notificationId)
    {
        $userId = session()->get('user_id');
        
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'User belum terautentikasi. Silakan login kembali.']);
        }

        try {
            // Use database builder for single update
            $db = \Config\Database::connect();
            $builder = $db->table('notifications');
            
            $result = $builder
                ->where('id', $notificationId)
                ->where('user_id', $userId)
                ->update([
                    'is_read' => 1,
                    'read_at' => date('Y-m-d H:i:s')
                ]);

            log_message('info', "Mark as read - Notification ID: {$notificationId}, User ID: {$userId}, Result: {$result}");

            if ($result) {
                return $this->response->setJSON(['success' => true, 'message' => 'Notification marked as read']);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi administrator.']);
            }
        } catch (\Exception $e) {
            log_message('error', 'markAsRead error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem.']);
        }
    }

    /**
     * API: Get available trigger event options from database
     */
    public function eventTypeOptions()
    {
        try {
            $rows = $this->db->table('notification_rules')
                ->select('trigger_event, COUNT(*) as rule_count')
                ->where('trigger_event IS NOT NULL')
                ->where('trigger_event !=', '')
                ->groupBy('trigger_event')
                ->orderBy('trigger_event', 'ASC')
                ->get()
                ->getResultArray();

            $options = array_map(function ($row) {
                $value = trim($row['trigger_event']);
                return [
                    'value' => $value,
                    'label' => $this->humanizeLabel($value),
                    'rule_count' => (int) $row['rule_count'],
                ];
            }, $rows);

            return $this->response->setJSON([
                'success' => true,
                'data' => $options,
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memproses permintaan. Silakan coba lagi.',
            ]);
        }
    }

    /**
     * API: Get available division targets
     */
    public function divisionOptions()
    {
        try {
            $divisions = $this->divisionModel
                ->where('is_active', 1)
                ->orderBy('name', 'ASC')
                ->findAll();

            $options = array_map(function ($division) {
                $name = trim($division['name'] ?? '');
                if ($name === '') {
                    return null;
                }

                return [
                    'value' => $name,
                    'label' => $name,
                    'meta' => [
                        'code' => $division['code'] ?? null,
                        'id' => $division['id'] ?? null,
                    ],
                ];
            }, $divisions);

            $options = array_values(array_filter($options));

            return $this->response->setJSON([
                'success' => true,
                'data' => $options,
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memproses permintaan. Silakan coba lagi.',
            ]);
        }
    }

    /**
     * API: Get available role targets
     */
    public function roleOptions()
    {
        try {
            $roles = $this->roleModel
                ->where('is_active', 1)
                ->orderBy('name', 'ASC')
                ->findAll();

            $options = array_map(function ($role) {
                $name = trim($role['name'] ?? '');
                if ($name === '') {
                    return null;
                }

                return [
                    'value' => $name,
                    'label' => $name,
                    'meta' => [
                        'id' => $role['id'] ?? null,
                        'level' => $role['level'] ?? null,
                    ],
                ];
            }, $roles);

            $options = array_values(array_filter($options));

            return $this->response->setJSON([
                'success' => true,
                'data' => $options,
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memproses permintaan. Silakan coba lagi.',
            ]);
        }
    }

    /**
     * API: Get metadata (event types, divisions, roles, legacy tokens)
     */
    public function optionsMetadata()
    {
        try {
            $divisionRecords = $this->divisionModel
                ->where('is_active', 1)
                ->orderBy('name', 'ASC')
                ->findAll();

            $divisionOptions = [];
            $divisionKeys = [];
            foreach ($divisionRecords as $division) {
                $name = trim($division['name'] ?? '');
                if ($name === '') {
                    continue;
                }

                $code = $division['code'] ?? null;
                $slug = $this->slugify($name);

                $divisionOptions[] = [
                    'value' => $name,
                    'label' => $name,
                    'code' => $code,
                    'meta' => [
                        'id' => $division['id'] ?? null,
                        'code' => $code,
                        'slug' => $slug,
                    ],
                ];

                $divisionKeys[] = strtolower($name);
                $divisionKeys[] = $slug;
                if (!empty($code)) {
                    $divisionKeys[] = strtolower($code);
                }
            }

            $roleRecords = $this->roleModel
                ->where('is_active', 1)
                ->orderBy('name', 'ASC')
                ->findAll();

            $roleOptions = [];
            $roleKeys = [];
            foreach ($roleRecords as $role) {
                $name = trim($role['name'] ?? '');
                if ($name === '') {
                    continue;
                }

                $slug = $this->slugify($name);

                $roleOptions[] = [
                    'value' => $name,
                    'label' => $name,
                    'meta' => [
                        'id' => $role['id'] ?? null,
                        'level' => $role['level'] ?? null,
                        'slug' => $slug,
                    ],
                ];

                $roleKeys[] = strtolower($name);
                $roleKeys[] = $slug;
            }

            $legacyDivisionTokens = array_values($this->collectDistinctRuleValues('target_divisions', $divisionKeys));
            $legacyRoleTokens = array_values($this->collectDistinctRuleValues('target_roles', $roleKeys));

            $eventTypeRows = $this->db->table('notification_rules')
                ->select('trigger_event, COUNT(*) as rule_count')
                ->where('trigger_event IS NOT NULL')
                ->where('trigger_event !=', '')
                ->groupBy('trigger_event')
                ->orderBy('trigger_event', 'ASC')
                ->get()
                ->getResultArray();

            $eventTypes = array_map(function ($row) {
                $value = trim($row['trigger_event']);
                return [
                    'value' => $value,
                    'label' => $this->humanizeLabel($value),
                    'rule_count' => (int) $row['rule_count'],
                ];
            }, $eventTypeRows);

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'event_types' => $eventTypes,
                    'divisions' => [
                        'official' => $divisionOptions,
                        'legacy' => $legacyDivisionTokens,
                    ],
                    'roles' => [
                        'official' => $roleOptions,
                        'legacy' => $legacyRoleTokens,
                    ],
                ],
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memproses permintaan. Silakan coba lagi.',
            ]);
        }
    }

    /**
     * Collect unique comma-separated values from notification_rules column
     */
    protected function collectDistinctRuleValues(string $column, array $ignoreKeys = []): array
    {
        $results = $this->db->table('notification_rules')
            ->select($column)
            ->where("$column IS NOT NULL")
            ->where("$column !=", '')
            ->get()
            ->getResultArray();

        $ignoreLookup = [];
        foreach ($ignoreKeys as $key) {
            if ($key === null || $key === '') {
                continue;
            }
            $lower = strtolower(trim($key));
            if ($lower === '') {
                continue;
            }
            $ignoreLookup[$lower] = true;
        }

        $unique = [];

        foreach ($results as $row) {
            $rawValue = $row[$column] ?? '';
            if (!$rawValue) {
                continue;
            }

            $items = array_filter(array_map('trim', explode(',', $rawValue)));
            foreach ($items as $item) {
                if ($item === '') {
                    continue;
                }
                $key = strtolower($item);
                $slug = $this->slugify($item);

                if (isset($ignoreLookup[$key]) || isset($ignoreLookup[$slug])) {
                    continue;
                }

                if (!isset($unique[$key])) {
                    $unique[$key] = [
                        'value' => $item,
                        'label' => $this->humanizeLabel($item),
                        'count' => 1,
                    ];
                } else {
                    $unique[$key]['count']++;
                }
            }
        }

        uasort($unique, static function ($a, $b) {
            return strcmp($a['label'], $b['label']);
        });

        return $unique;
    }

    /**
     * Convert database-friendly value (snake_case/kebab) into human readable label
     */
    protected function humanizeLabel(string $value): string
    {
        $normalized = str_replace(['_', '-'], ' ', strtolower($value));
        return ucwords($normalized);
    }

    /**
     * Slugify a string
     */
    protected function slugify(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '_', $value);
        return trim($value, '_');
    }

    /**
     * API: Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $userId = session()->get('user_id');
        
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'User belum terautentikasi. Silakan login kembali.']);
        }

        try {
            // Use database builder for bulk update
            $db = \Config\Database::connect();
            $builder = $db->table('notifications');
            
            $result = $builder
                ->where('user_id', $userId)
                ->where('is_read', 0)
                ->update([
                    'is_read' => 1,
                    'read_at' => date('Y-m-d H:i:s')
                ]);

            log_message('info', "Mark all as read - User ID: {$userId}, Updated: {$result} notifications");

            return $this->response->setJSON([
                'success' => true, 
                'message' => 'All notifications marked as read',
                'updated_count' => $result
            ]);
        } catch (\Exception $e) {
            log_message('error', 'markAllAsRead error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi administrator.']);
        }
    }

    /**
     * API: Get notification count
     */
    public function getCount()
    {
        $userId = session()->get('user_id');
        session_write_close(); // Release session lock immediately — DB work below doesn't need session
        
        if (!$userId) {
            return $this->response->setJSON(['success' => true, 'count' => 0]);
        }

        try {
            // Check if notifications table exists
            if (!$this->db->tableExists('notifications')) {
                return $this->response->setJSON(['success' => true, 'count' => 0]);
            }
            
            $count = $this->db->table('notifications')
                ->where('user_id', $userId)
                ->where('is_read', 0)
                ->countAllResults();

            return $this->response->setJSON([
                'success' => true,
                'count' => $count
            ]);
        } catch (\Exception $e) {
            log_message('debug', 'Notification getCount error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => true, 'count' => 0]);
        }
    }

    /**
     * API: Poll for new notifications
     */
    public function poll()
    {
        $userId = session()->get('user_id');
        $lastId = $this->request->getGet('lastId') ?? 0;
        session_write_close(); // Release session lock immediately — DB work below doesn't need session
        
        if (!$userId) {
            return $this->response->setJSON(['success' => true, 'notifications' => [], 'lastId' => 0]);
        }

        try {
            // Check if notifications table exists
            if (!$this->db->tableExists('notifications')) {
                return $this->response->setJSON(['success' => true, 'notifications' => [], 'lastId' => 0]);
            }
            
            $notifications = $this->db->table('notifications')
                ->where('user_id', $userId)
                ->where('id >', $lastId)
                ->where('is_read', 0)
                ->orderBy('created_at', 'DESC')
                ->limit(10)
                ->get()
                ->getResultArray();

            $maxId = $lastId;
            if (!empty($notifications)) {
                $maxId = max(array_column($notifications, 'id'));
            }

            return $this->response->setJSON([
                'success' => true,
                'notifications' => $notifications,
                'lastId' => $maxId
            ]);
        } catch (\Exception $e) {
            log_message('debug', 'Notification poll error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => true, 'notifications' => [], 'lastId' => 0]);
        }
    }




    /**
     * Delete notification
     */
    public function delete($notificationId)
    {
        if (!$this->canAccess('admin')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak']);
        }

        $userId = session()->get('user_id');
        
        $result = $this->notificationModel
            ->where('id', $notificationId)
            ->where('user_id', $userId)
            ->delete();

        if ($result) {
            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete notification']);
    }

    // ========================================================================
    // LIGHTWEIGHT NOTIFICATION SYSTEM (NO SSE)
    // ========================================================================


    // ========================================================================
    // ADMIN PANEL - NOTIFICATION RULES
    // ========================================================================

    /**
     * Admin panel for managing notification rules
     */
    public function admin()
    {
        if (!$this->canManage('admin')) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak');
        }

        $rules = $this->ruleModel->findAll();
        
        // Get all divisions for tabs
        $divisions = $this->divisionModel
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->findAll();
        
        // Get stats for admin panel
        $stats = [
            'total_rules' => count($rules),
            'active_rules' => count(array_filter($rules, function($rule) { return $rule['is_active'] == 1; })),
            'inactive_rules' => count(array_filter($rules, function($rule) { return $rule['is_active'] == 0; })),
            'total_notifications' => $this->notificationModel->countAllResults(),
            'today_notifications' => $this->notificationModel->where('DATE(created_at)', date('Y-m-d'))->countAllResults()
        ];
        
        return view('notifications/admin_panel', [
            'rules' => $rules,
            'stats' => $stats,
            'divisions' => $divisions
        ]);
    }

    /**
     * Get all notification rules
     */
    public function getRules()
    {
        if (!$this->canManage('admin')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak']);
        }

        $rules = $this->ruleModel->findAll();
        
        return $this->response->setJSON([
            'success' => true,
            'rules' => $rules
        ]);
    }

    /**
     * Get rule detail
     */
    public function getRuleDetail($ruleId)
    {
        // Temporarily disable permission check for testing
        // if (!$this->canManage('admin')) {
        //     return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak']);
        // }

        $rule = $this->ruleModel->find($ruleId);
        
        if (!$rule) {
            return $this->response->setJSON(['success' => false, 'message' => 'Rule notifikasi tidak ditemukan']);
        }

        return $this->response->setJSON([
            'success' => true,
            'rule' => $rule
        ]);
    }

    /**
     * Get single notification rule (alias for getRuleDetail)
     */
    public function getRule($ruleId)
    {
        return $this->getRuleDetail($ruleId);
    }

    /**
     * Create new notification rule
     */
    public function createRule()
    {
        // Temporarily disable permission check for testing
        // if (!$this->canManage('admin')) {
        //     return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak']);
        // }

        $data = [
            'name' => $this->request->getPost('name'),
            'trigger_event' => $this->request->getPost('trigger_event'),
            'target_divisions' => implode(',', $this->request->getPost('target_divisions') ?? []),
            'target_roles' => implode(',', $this->request->getPost('target_roles') ?? []),
            'target_users' => implode(',', $this->request->getPost('target_users') ?? []),
            'title_template' => $this->request->getPost('title_template'),
            'message_template' => $this->request->getPost('message_template'),
            'type' => $this->request->getPost('type') ?? 'info',
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];

        $result = $this->ruleModel->insert($data);
        
        if ($result) {
            // Log the create activity
            $this->logActivity('CREATE', 'notification_rules', $result, '', $data);
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Notification rule created successfully',
                'rule_id' => $result
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to create notification rule'
        ]);
    }

    /**
     * Update notification rule
     */
    public function updateRule($ruleId)
    {
        // Temporarily disable permission check for testing
        // if (!$this->canManage('admin')) {
        //     return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak']);
        // }

        $oldRule = $this->ruleModel->find($ruleId);
        
        if (!$oldRule) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Rule notifikasi tidak ditemukan'
            ]);
        }
        
        // Get form data - handle both array and string formats
        $name = $this->request->getPost('name');
        $eventType = $this->request->getPost('event_type');
        $titleTemplate = $this->request->getPost('title_template');
        $messageTemplate = $this->request->getPost('message_template');
        $type = $this->request->getPost('type') ?? 'info';
        $isActive = $this->request->getPost('is_active');
        
        // Handle checkbox arrays properly - they come as target_divisions[], target_roles[], etc.
        $targetDivisions = $this->request->getPost('target_divisions');
        $targetRoles = $this->request->getPost('target_roles');
        $targetUsers = $this->request->getPost('target_users');
        
        // Convert arrays to comma-separated strings
        $divisionsStr = is_array($targetDivisions) ? implode(',', $targetDivisions) : '';
        $rolesStr = is_array($targetRoles) ? implode(',', $targetRoles) : '';
        $usersStr = is_array($targetUsers) ? implode(',', $targetUsers) : '';
        
        $data = [
            'name' => $name,
            'trigger_event' => $eventType,
            'target_divisions' => $divisionsStr,
            'target_roles' => $rolesStr,
            'target_users' => $usersStr,
            'title_template' => $titleTemplate,
            'message_template' => $messageTemplate,
            'type' => $type,
            'is_active' => $isActive ? 1 : 0
        ];
        
        log_message('info', 'Updating notification rule ' . $ruleId . ' with data: ' . json_encode($data));
        
        try {
            $result = $this->ruleModel->update($ruleId, $data);
            
            if ($result) {
                // Get updated rule
                $updatedRule = $this->ruleModel->find($ruleId);
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Notification rule updated successfully',
                    'rule' => $updatedRule
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No changes were made to the rule'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Toggle notification rule status
     */
    public function toggleStatus($ruleId)
    {
        // Temporarily disable permission check for testing
        // if (!$this->canManage('admin')) {
        //     return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak']);
        // }

        $rule = $this->ruleModel->find($ruleId);
        
        if (!$rule) {
            return $this->response->setJSON(['success' => false, 'message' => 'Rule notifikasi tidak ditemukan']);
        }

        $newStatus = $rule['is_active'] == '1' ? '0' : '1';
        $result = $this->ruleModel->update($ruleId, ['is_active' => $newStatus]);
        
        if ($result) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Rule status updated successfully',
                'is_active' => $newStatus
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to update rule status']);
    }

    /**
     * Delete notification rule
     */
    public function deleteRule($ruleId)
    {
        if (!$this->canManage('admin')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak']);
        }

        $oldRule = $this->ruleModel->find($ruleId);
        $result = $this->ruleModel->delete($ruleId);
        
        if ($result) {
            $this->logDelete('notification_rules', $ruleId, $oldRule);
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Notification rule deleted successfully'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to delete notification rule'
        ]);
    }

    /**
     * Toggle rule status (active/inactive)
     */
    public function toggleRuleStatus($ruleId)
    {
        if (!$this->canManage('admin')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak']);
        }
        
        $rule = $this->ruleModel->find($ruleId);
        $newStatus = $rule['is_active'] ? 0 : 1;
        
        $result = $this->ruleModel->update($ruleId, ['is_active' => $newStatus]);
        
        if ($result) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Rule status updated',
                'is_active' => $newStatus
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update rule status'
        ]);
    }

    // ========================================================================
    // SEND NOTIFICATION (PROGRAMMATIC)
    // ========================================================================

    /**
     * Send notification based on event and data
     * This is called by other controllers (Marketing, Service, etc)
     */
    public function sendByEvent($eventType, $eventData = [])
    {
        // Get active rules for this event
        $rules = $this->ruleModel
            ->where('event_type', $eventType)
            ->where('is_active', 1)
            ->findAll();
        
        if (empty($rules)) {
            return false;
        }

        $notificationsSent = 0;
        
        foreach ($rules as $rule) {
            // Get target users based on rule criteria
            $targetUsers = $this->getTargetUsers($rule);
            
            if (empty($targetUsers)) {
                continue;
            }

            // Prepare notification data
            $title = $this->replaceTemplateVars($rule['template_title'], $eventData);
            $message = $this->replaceTemplateVars($rule['template_message'], $eventData);

            // Send to each target user
            foreach ($targetUsers as $userId) {
                $notificationData = [
                    'user_id' => $userId,
                    'title' => $title,
                    'message' => $message,
                    'type' => $rule['type'],
                    'icon' => $rule['icon'],
                    'related_module' => $eventData['module'] ?? null,
                    'related_id' => $eventData['id'] ?? null,
                    'url' => $eventData['url'] ?? null
                ];

                $this->notificationModel->insert($notificationData);
                $notificationsSent++;
            }
        }

        return $notificationsSent;
    }

    /**
     * Get target users based on rule criteria
     */
    private function getTargetUsers($rule)
    {
        $query = $this->db->table('users u')
            ->select('u.id')
            ->join('roles r', 'r.id = u.role_id', 'left')
            ->join('divisions d', 'd.id = u.division_id', 'left')
            ->where('u.is_active', 1);

        // Filter by division
        if (!empty($rule['target_divisions'])) {
            $query->where('d.name', $rule['target_divisions']);
        }

        // Filter by department
        if (!empty($rule['target_department'])) {
            $query->where('u.department', $rule['target_department']);
        }

        // Filter by role
        if (!empty($rule['target_role'])) {
            $query->where('r.name', $rule['target_role']);
        }

        $result = $query->get()->getResultArray();
        return array_column($result, 'id');
    }

    /**
     * Replace template variables with actual data
     */
    private function replaceTemplateVars($template, $data)
    {
        $replacements = [
            '{nomor_spk}' => $data['nomor_spk'] ?? '',
            '{pelanggan}' => $data['pelanggan'] ?? '',
            '{departemen}' => $data['departemen'] ?? '',
            '{lokasi}' => $data['lokasi'] ?? '',
            '{id}' => $data['id'] ?? '',
            '{module}' => $data['module'] ?? '',
            '{user}' => $data['user'] ?? '',
            '{date}' => date('d/m/Y H:i'),
            '{time}' => date('H:i'),
            '{today}' => date('d/m/Y')
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }
}