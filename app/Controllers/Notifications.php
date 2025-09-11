<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Traits\ActivityLoggingTrait;
use App\Models\NotificationModel;
use App\Models\NotificationRuleModel;
use App\Models\NotificationRecipientModel;
use App\Models\NotificationLogModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class Notifications extends BaseController
{
    use ActivityLoggingTrait;
    protected $db;
    protected $notificationModel;
    protected $ruleModel;
    protected $recipientModel;
    protected $logModel;
    protected $userModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->notificationModel = new NotificationModel();
        $this->ruleModel = new NotificationRuleModel();
        $this->recipientModel = new NotificationRecipientModel();
        $this->logModel = new NotificationLogModel();
        $this->userModel = new UserModel();
        
        // Ensure table and expected columns exist early to avoid runtime SQL errors
        try {
            $this->createNotificationsTable();
            $this->ensureNotificationColumns();
        } catch (\Throwable $e) {
            // best-effort; don't block page load
        }
    }

    public function index()
    {
        // Prevent caching for fresh notifications
        $this->response->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate');
        $this->response->setHeader('Pragma', 'no-cache');
        $this->response->setHeader('Expires', '0');
        
        $this->ensureNotificationColumns();
        
        $stats = $this->getNotificationStats();
        $notifications = $this->getNotificationsForUser();
        
        $data = [
            'stats' => $stats,
            'notifications' => $notifications,
            'title' => 'Notification Center',
            'current_route' => 'notifications'
        ];
        
        return view('notifications/index', $data);
    }

    /**
     * Admin Panel for Managing Notification Rules
     */
    public function admin()
    {
        // Check if user is superadmin
        if (session()->get('role') !== 'super_admin') {
            return redirect()->to('/')->with('error', 'Access denied');
        }

        $data = [
            'title' => 'Notification Rules | OPTIMA',
            'page_title' => 'Notification Rules Management',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/notifications' => 'Notifications',
                '/notifications/admin' => 'Rules Management'
            ],
            'rules' => $this->ruleModel->getAllRules(),
            'activity_types' => $this->getActivityTypes(),
            'available_roles' => $this->getAvailableRoles(),
            'available_divisions' => $this->getAvailableDivisions(),
            'available_departments' => $this->getAvailableDepartments()
        ];

        return view('notifications/admin', $data);
    }

    /**
     * Create notification rule via API
     */
    public function createRule()
    {
        if (session()->get('role') !== 'super_admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied'
            ]);
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => 'required|max_length[255]',
            'trigger_event' => 'required|max_length[100]',
            'title_template' => 'required|max_length[255]',
            'message_template' => 'required',
            'target_type' => 'permit_empty|in_list[role,division,department,user,all]',
            'target_values' => 'permit_empty',
            'is_active' => 'permit_empty|in_list[0,1]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validation->getErrors()
            ]);
        }

        try {
            // Get data from JSON body or form data
            $requestData = $this->request->getJSON(true);
            if (empty($requestData)) {
                $requestData = $this->request->getPost();
            }
            
            if (empty($requestData)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No data received'
                ]);
            }

            $data = [
                'name' => $requestData['name'] ?? '',
                'trigger_event' => $requestData['trigger_event'] ?? '',
                'title_template' => $requestData['title_template'] ?? '',
                'message_template' => $requestData['message_template'] ?? '',
                'conditions' => '{}',
                'priority' => (int)($requestData['priority'] ?? 1),
                'is_active' => isset($requestData['is_active']) && $requestData['is_active'] ? 1 : 0,
                'created_by' => session()->get('user_id') ?? session()->get('id') ?? 1
            ];

            // Handle target values
            $targetType = $requestData['target_type'] ?? 'all';
            $targetValues = $requestData['target_values'] ?? [];
            
            // Initialize all target fields
            $data['target_roles'] = '';
            $data['target_divisions'] = '';
            $data['target_departments'] = '';
            $data['target_users'] = '';
            
            // Set the appropriate target field
            if (!empty($targetValues) && $targetType !== 'all') {
                $targetField = 'target_' . $targetType . 's';
                if ($targetType === 'role') $targetField = 'target_roles';
                $data[$targetField] = is_array($targetValues) ? implode(',', $targetValues) : $targetValues;
            }

            $ruleId = $this->ruleModel->insert($data);

            if ($ruleId) {
                $this->logActivity('notification_rule_created', 'notification_rules', $ruleId, 'Created notification rule: ' . $data['name']);
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Notification rule created successfully',
                    'rule_id' => $ruleId
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to create notification rule'
                ]);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error creating rule: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get rule data for editing
     */
    public function getRule($ruleId)
    {
        if (session()->get('role') !== 'super_admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied'
            ]);
        }

        try {
            $rule = $this->ruleModel->find($ruleId);
            if (!$rule) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Rule not found'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'rule' => $rule
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error fetching rule: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update notification rule
     */
    public function updateRule($ruleId)
    {
        if (session()->get('role') !== 'super_admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied'
            ]);
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => 'required|max_length[255]',
            'trigger_event' => 'required|max_length[100]',
            'title_template' => 'required|max_length[255]',
            'message_template' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validation->getErrors()
            ]);
        }

        try {
            $rule = $this->ruleModel->find($ruleId);
            if (!$rule) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Rule not found'
                ]);
            }

            // Get data from JSON body or form data
            $data = $this->request->getJSON(true);
            if (empty($data)) {
                $data = $this->request->getPost();
            }

            // Ensure required fields and proper data types
            $data['priority'] = (int)($data['priority'] ?? 1);
            $data['is_active'] = isset($data['is_active']) && $data['is_active'] ? 1 : 0;
            
            // Handle target values
            if (isset($data['target_values']) && is_array($data['target_values'])) {
                $targetType = $data['target_type'] ?? 'all';
                
                // Clear all target fields first
                $data['target_roles'] = '';
                $data['target_divisions'] = '';
                $data['target_departments'] = '';
                $data['target_users'] = '';
                
                // Set the appropriate target field
                if (!empty($data['target_values']) && $targetType !== 'all') {
                    $targetField = 'target_' . $targetType . 's';
                    if ($targetType === 'role') $targetField = 'target_roles';
                    $data[$targetField] = implode(',', $data['target_values']);
                }
            }
            
            // Remove target_type and target_values as they're not database fields
            unset($data['target_type'], $data['target_values']);

            $updated = $this->ruleModel->update($ruleId, $data);

            if ($updated) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Rule updated successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to update rule'
                ]);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error updating rule: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete notification rule
     */
    public function deleteRule($ruleId)
    {
        if (session()->get('role') !== 'super_admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied'
            ]);
        }

        try {
            $rule = $this->ruleModel->find($ruleId);
            if (!$rule) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Rule not found'
                ]);
            }

            $deleted = $this->ruleModel->delete($ruleId);

            if ($deleted) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Rule deleted successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to delete rule'
                ]);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error deleting rule: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Test notification rule
     */
    public function testRule($ruleId)
    {
        if (session()->get('role') !== 'super_admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied'
            ]);
        }

        try {
            $rule = $this->ruleModel->find($ruleId);
            if (!$rule) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Rule not found'
                ]);
            }

            // Get test context data
            $testContext = $this->request->getJSON(true) ?: [];
            
            // Test the rule
            $testResult = $this->ruleModel->testRule($rule, $testContext);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Rule test completed',
                'result' => $testResult
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error testing rule: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Send notification using rule-based system
     */
    public function sendByRule()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'activity_type' => 'required|max_length[100]',
            'context' => 'permit_empty'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validation->getErrors()
            ]);
        }

        try {
            $activityType = $this->request->getPost('activity_type');
            $context = $this->request->getPost('context') ?: [];
            
            if (is_string($context)) {
                $context = json_decode($context, true) ?: [];
            }

            $result = $this->notificationModel->sendByRule($activityType, $context);

            if ($result['success']) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Notifications sent successfully',
                    'sent_count' => $result['sent_count'],
                    'recipients' => $result['recipients']
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $result['message']
                ]);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error sending notifications: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Enhanced system notification methods for workflow integration
     */
    
    /**
     * Notify SPK workflow events
     */
    public function notifySPKEvent($spkId, $eventType, $spkData = [])
    {
        $context = array_merge($spkData, [
            'spk_id' => $spkId,
            'event_type' => $eventType,
            'created_by' => session()->get('user_id'),
            'timestamp' => date('Y-m-d H:i:s')
        ]);

        return $this->notificationModel->sendByRule('spk_' . $eventType, $context);
    }

    /**
     * Notify DI workflow events
     */
    public function notifyDIEvent($diId, $eventType, $diData = [])
    {
        $context = array_merge($diData, [
            'di_id' => $diId,
            'event_type' => $eventType,
            'processed_by' => session()->get('user_id'),
            'timestamp' => date('Y-m-d H:i:s')
        ]);

        return $this->notificationModel->sendByRule('di_' . $eventType, $context);
    }

    /**
     * Notify inventory events
     */
    public function notifyInventoryEvent($itemId, $eventType, $inventoryData = [])
    {
        $context = array_merge($inventoryData, [
            'item_id' => $itemId,
            'event_type' => $eventType,
            'timestamp' => date('Y-m-d H:i:s')
        ]);

        return $this->notificationModel->sendByRule('inventory_' . $eventType, $context);
    }

    /**
     * Get user's notification preferences
     */
    public function getPreferences()
    {
        $userId = session()->get('user_id');
        
        try {
            $preferences = $this->db->table('user_notification_preferences')
                                   ->where('user_id', $userId)
                                   ->get()
                                   ->getRowArray();

            if (!$preferences) {
                // Create default preferences
                $defaults = [
                    'user_id' => $userId,
                    'email_enabled' => 1,
                    'push_enabled' => 1,
                    'sound_enabled' => 1,
                    'email_frequency' => 'instant',
                    'quiet_hours_start' => '22:00',
                    'quiet_hours_end' => '08:00',
                    'created_at' => date('Y-m-d H:i:s')
                ];
                
                $this->db->table('user_notification_preferences')->insert($defaults);
                $preferences = $defaults;
            }

            return $this->response->setJSON([
                'success' => true,
                'preferences' => $preferences
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to get preferences: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update user's notification preferences
     */
    public function updatePreferences()
    {
        $userId = session()->get('user_id');
        
        $validation = \Config\Services::validation();
        $validation->setRules([
            'email_enabled' => 'permit_empty|in_list[0,1]',
            'push_enabled' => 'permit_empty|in_list[0,1]',
            'sound_enabled' => 'permit_empty|in_list[0,1]',
            'email_frequency' => 'permit_empty|in_list[instant,daily,weekly]',
            'quiet_hours_start' => 'permit_empty|valid_time',
            'quiet_hours_end' => 'permit_empty|valid_time'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validation->getErrors()
            ]);
        }

        try {
            $data = [
                'email_enabled' => $this->request->getPost('email_enabled') ?? 1,
                'push_enabled' => $this->request->getPost('push_enabled') ?? 1,
                'sound_enabled' => $this->request->getPost('sound_enabled') ?? 1,
                'email_frequency' => $this->request->getPost('email_frequency') ?? 'instant',
                'quiet_hours_start' => $this->request->getPost('quiet_hours_start') ?? '22:00',
                'quiet_hours_end' => $this->request->getPost('quiet_hours_end') ?? '08:00',
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Check if preferences exist
            $exists = $this->db->table('user_notification_preferences')
                              ->where('user_id', $userId)
                              ->countAllResults() > 0;

            if ($exists) {
                $this->db->table('user_notification_preferences')
                         ->where('user_id', $userId)
                         ->update($data);
            } else {
                $data['user_id'] = $userId;
                $data['created_at'] = date('Y-m-d H:i:s');
                $this->db->table('user_notification_preferences')->insert($data);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Preferences updated successfully'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update preferences: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get notification analytics for admin
     */
    public function analytics()
    {
        if (session()->get('role') !== 'super_admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied'
            ]);
        }

        try {
            $analytics = [
                'total_sent' => $this->logModel->getTotalSent(),
                'delivery_rate' => $this->logModel->getDeliveryRate(),
                'popular_types' => $this->logModel->getPopularTypes(),
                'engagement_stats' => $this->recipientModel->getEngagementStats(),
                'rule_performance' => $this->ruleModel->getPerformanceStats(),
                'recent_activity' => $this->logModel->getRecentActivity()
            ];

            return $this->response->setJSON([
                'success' => true,
                'analytics' => $analytics
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to get analytics: ' . $e->getMessage()
            ]);
        }
    }

    public function stream()
    {
    try {
    // Force raw headers for Server-Sent Events (use header() to avoid framework overrides)
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header('Connection: keep-alive');
        // Allow same-origin credentials; restrict CORS to same origin where possible
        $origin = $this->request->getHeaderLine('Origin') ?: '';
        if ($origin) {
            $this->response->setHeader('Access-Control-Allow-Origin', $origin);
            $this->response->setHeader('Access-Control-Allow-Credentials', 'true');
        }
        $this->response->setHeader('Access-Control-Allow-Headers', 'Cache-Control, Last-Event-ID');

        // Ensure script can run indefinitely for streaming
        if (function_exists('set_time_limit')) {
            @set_time_limit(0);
        }
        @ignore_user_abort(true);

        // Disable output buffering
        if (ob_get_level()) {
            ob_end_clean();
        }

    $userId = session()->get('user_id');
    $lastEventId = $this->request->getHeaderLine('Last-Event-ID') ?: 0;

    // Disable proxy buffering for nginx/varnish where possible
    header('X-Accel-Buffering: no');

    // Create notifications table if it doesn't exist and ensure columns
    $this->createNotificationsTable();
    $this->ensureNotificationColumns();

        // Send initial retry suggestion and initial payload so clients can sync
        echo "retry: 5000\n"; // client should retry after 5s on error
        $initialLastId = (int)$lastEventId;
        $unread = 0;
        try {
            $unread = $this->db->table('notifications')->where('is_read', 0)->where('user_id', $userId)->countAllResults();
        } catch (\Exception $e) { /* ignore */ }

        echo "event: init\n";
        echo "data: " . json_encode(['lastEventId' => $initialLastId, 'unread' => $unread]) . "\n\n";

        while (true) {
            // Check for new notifications
            $notifications = $this->getNewNotifications($userId, $lastEventId);
            
            if (!empty($notifications)) {
                foreach ($notifications as $notification) {
                    $payload = [
                        'type' => 'notification',
                        'notification' => [
                            'id' => $notification['id'],
                            'title' => $notification['title'],
                            'message' => $notification['message'],
                            'type' => $notification['type'],
                            'icon' => $this->getNotificationIcon($notification['type']),
                            'created_at' => $notification['created_at'],
                            'url' => $notification['url']
                        ]
                    ];

                    echo "id: {$notification['id']}\n";
                    echo "event: notification\n";
                    echo "data: " . json_encode($payload) . "\n\n";

                    $lastEventId = $notification['id'];
                }

                // Flush output
                if (ob_get_level()) {
                    ob_flush();
                }
                flush();
            }

            // Send heartbeat every 30 seconds
            echo "event: heartbeat\n";
            echo "data: " . json_encode(['timestamp' => time()]) . "\n\n";
            
            if (ob_get_level()) {
                ob_flush();
            }
            flush();

            // Sleep for 5 seconds before checking again
            sleep(5);

            // Check if connection is still alive
            if (connection_aborted()) {
                break;
            }
        }
        } catch (\Throwable $e) {
            // Log server error for debugging
            error_log('[Notifications::stream] SSE failed: ' . $e->getMessage() . " in " . $e->getFile() . ':' . $e->getLine());
            // Also write a detailed trace to writable logs for user inspection
            try {
                $logPath = WRITEPATH . 'logs' . DIRECTORY_SEPARATOR . 'sse_stream_error.log';
                $content = "[" . date('Y-m-d H:i:s') . "] SSE stream exception: " . $e->getMessage() . " in " . $e->getFile() . ':' . $e->getLine() . "\n";
                $content .= $e->getTraceAsString() . "\n\n";
                file_put_contents($logPath, $content, FILE_APPEND | LOCK_EX);
            } catch (\Exception $inner) {
                // If logging fails, still continue
                error_log('[Notifications::stream] Failed writing SSE debug log: ' . $inner->getMessage());
            }
            // If headers not sent, provide JSON 500 for easier debugging in Network tab
            if (!headers_sent()) {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'SSE stream error', 'error' => $e->getMessage()]);
            }
        }
    }

    /**
     * Lightweight SSE test endpoint (sends one event and exits)
     * Useful for debugging SSE connectivity without full stream logic.
     */
    public function testStream()
    {
    // Force raw headers for SSE test
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header('Connection: keep-alive');
        header('X-Accel-Buffering: no');

        if (ob_get_level()) ob_end_clean();

        $payload = ['type' => 'notification', 'notification' => ['id' => 0, 'title' => 'SSE Test', 'message' => 'Test event from server', 'created_at' => date('Y-m-d H:i:s')]];
        echo "event: test\n";
        echo "data: " . json_encode($payload) . "\n\n";
        if (ob_get_level()) ob_flush();
        flush();
    }

    public function create()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'title' => 'required|max_length[255]',
            'message' => 'required|max_length[1000]',
            'type' => 'required|in_list[info,success,warning,error]',
            'user_id' => 'permit_empty|integer',
            'url' => 'permit_empty|valid_url_strict'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validation->getErrors()
            ]);
        }

        try {
            $this->createNotificationsTable();
            $this->ensureNotificationColumns();

            $data = [
                'title' => $this->request->getPost('title'),
                'message' => $this->request->getPost('message'),
                'type' => $this->request->getPost('type'),
                'user_id' => $this->request->getPost('user_id') ?: null, // null for broadcast
                'url' => $this->request->getPost('url'),
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->table('notifications')->insert($data);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Notification created successfully',
                'notification_id' => $this->db->insertID()
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create notification: ' . $e->getMessage()
            ]);
        }
    }

    public function markAsRead($notificationId)
    {
        // Ensure these are defined for catch scope and static analysis
        $userId = session()->get('user_id');
        $userRole = session()->get('role') ?? session()->get('department');

        try {
            $this->ensureNotificationColumns();

            $builder = $this->db->table('notifications')->where('id', $notificationId);

            // Use user_id when available, otherwise fall back to role-based ownership or best-effort by id
            if ($this->db->fieldExists('user_id', 'notifications')) {
                $builder->groupStart()
                        ->where('user_id', $userId)
                        ->orWhere('user_id', null)
                        ->orWhere('target_role', $userRole)
                    ->groupEnd();
            } elseif ($this->db->fieldExists('target_role', 'notifications')) {
                $builder->where('target_role', $userRole);
            } // else: no ownership columns, operate by id only (legacy/broadcast)

            $updated = $builder->update(['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')]);

            if ($updated) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Notification marked as read'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Notification not found or already read'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to mark notification as read: ' . $e->getMessage()
            ]);
        }
    }

    public function markAllAsRead()
    {
        // Define early so catch handlers and analyzers see them
        $userId = session()->get('user_id');
        $userRole = session()->get('role') ?? session()->get('department');

        try {
            $this->ensureNotificationColumns();

            $builder = $this->db->table('notifications');

            if ($this->db->fieldExists('user_id', 'notifications')) {
                $builder->where('user_id', $userId);
            } elseif ($this->db->fieldExists('target_role', 'notifications')) {
                $builder->where('target_role', $userRole);
            } // else: no ownership column available, mark all unread notifications (best-effort)

            $builder->where('is_read', 0)->update(['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'All notifications marked as read'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to mark notifications as read: ' . $e->getMessage()
            ]);
        }
    }

    public function delete($notificationId)
    {
        // Pre-declare for catch scope
        $userId = session()->get('user_id');
        $userRole = session()->get('role') ?? session()->get('department');

        try {
            $this->ensureNotificationColumns();

            $builder = $this->db->table('notifications')->where('id', $notificationId);

            if ($this->db->fieldExists('user_id', 'notifications')) {
                $builder->groupStart()
                        ->where('user_id', $userId)
                        ->orWhere('user_id', null)
                        ->orWhere('target_role', $userRole)
                    ->groupEnd();
            } elseif ($this->db->fieldExists('target_role', 'notifications')) {
                $builder->where('target_role', $userRole);
            }

            $deleted = $builder->delete();

            if ($deleted) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Notification deleted successfully'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Notification not found'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete notification: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Generic update for notification (status changes or delete action)
     */
    public function update($notificationId)
    {
        try {
            $userId = session()->get('user_id');
            $userRole = session()->get('role') ?? null;

            // Accept JSON body or form data
            $data = $this->request->getJSON(true) ?: $this->request->getPost();
            if (empty($data)) {
                return $this->response->setJSON(['success' => false, 'message' => 'No data provided']);
            }

            // Ensure table exists
            $this->createNotificationsTable();
            $this->ensureNotificationColumns();

            // Build update payload
            $update = [];

            // Normalize status -> is_read/status
            if (isset($data['status'])) {
                $status = $data['status'];
                if ($status === 'read' || $status === 'unread') {
                    $update['is_read'] = $status === 'read' ? 1 : 0;
                    if ($update['is_read']) $update['read_at'] = date('Y-m-d H:i:s');
                } else {
                    // Try to set status column; add column if missing
                    try {
                        if (!$this->db->fieldExists('status', 'notifications')) {
                            $forge = \Config\Database::forge();
                            $forge->addColumn('notifications', [
                                'status' => [
                                    'type' => 'VARCHAR',
                                    'constraint' => 50,
                                    'null' => true,
                                    'after' => 'is_read'
                                ]
                            ]);
                        }
                    } catch (\Throwable $e) {
                        // ignore
                    }

                    if ($this->db->fieldExists('status', 'notifications')) {
                        $update['status'] = $status;
                    } else {
                        // Fallback: mark as read
                        $update['is_read'] = 1;
                        $update['read_at'] = date('Y-m-d H:i:s');
                    }
                }
            }

            // Support action: delete
            if (isset($data['action']) && $data['action'] === 'delete') {
                // Check ownership before deleting
                $this->ensureNotificationColumns();
                $builder = $this->db->table('notifications')->where('id', $notificationId);
                if ($this->db->fieldExists('user_id', 'notifications')) {
                    $builder->groupStart()
                            ->where('user_id', $userId)
                            ->orWhere('user_id', null)
                            ->orWhere('target_role', $userRole)
                        ->groupEnd();
                } elseif ($this->db->fieldExists('target_role', 'notifications')) {
                    $builder->where('target_role', $userRole);
                }

                $deleted = $builder->delete();
                if ($deleted) {
                    return $this->response->setJSON(['success' => true, 'message' => 'Notification deleted']);
                }
                return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete']);
            }

            if (empty($update)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Nothing to update']);
            }

            // Apply update with ownership check
            $this->ensureNotificationColumns();
            $builder = $this->db->table('notifications')->where('id', $notificationId);
            if ($this->db->fieldExists('user_id', 'notifications')) {
                $builder->groupStart()
                        ->where('user_id', $userId)
                        ->orWhere('user_id', null)
                        ->orWhere('target_role', $userRole)
                    ->groupEnd();
            } elseif ($this->db->fieldExists('target_role', 'notifications')) {
                $builder->where('target_role', $userRole);
            }

            $updated = $builder->update($update);

            if ($updated) {
                return $this->response->setJSON(['success' => true, 'message' => 'Notification updated']);
            }

            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update notification']);

        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function getCount()
    {
        try {
            // Prevent caching
            $this->response->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate');
            $this->response->setHeader('Pragma', 'no-cache');
            $this->response->setHeader('Expires', '0');
            
            $this->ensureNotificationColumns();
            
            $userId = session()->get('user_id') ?? session()->get('id');
            
            if (!$userId || !$this->db->tableExists('notifications')) {
                return $this->response->setJSON(['success' => true, 'count' => 0]);
            }

            // Check if status column exists, fallback to is_read
            $builder = $this->db->table('notifications')->where('user_id', $userId);
            
            if ($this->db->fieldExists('status', 'notifications')) {
                $count = $builder->where('status !=', 'read')->countAllResults();
            } else {
                $count = $builder->where('is_read', 0)->countAllResults();
            }

            return $this->response->setJSON(['success' => true, 'count' => (int)$count]);

        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => true, 'count' => 0]);
        }
    }

    /**
     * Return recent notifications as JSON for header dropdown
     */
    public function recent()
    {
        try {
            // Prevent caching
            $this->response->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate');
            $this->response->setHeader('Pragma', 'no-cache');
            $this->response->setHeader('Expires', '0');

            $this->ensureNotificationColumns();

            // Use internal helper to fetch latest notifications for user
            $notifications = $this->getNotifications(10);

            // Normalize fields for front-end
            $out = array_map(function($n) {
                return [
                    'id' => $n['id'] ?? null,
                    'title' => $n['title'] ?? '',
                    'message' => $n['message'] ?? '',
                    'type' => $n['type'] ?? 'info',
                    'created_at' => $n['created_at'] ?? date('Y-m-d H:i:s'),
                    'url' => $n['url'] ?? ''
                ];
            }, $notifications ?: []);

            return $this->response->setJSON(['success' => true, 'notifications' => $out]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'notifications' => []]);
        }
    }

    // Helper Methods for System Notifications
    public function notifyMaintenanceAlert($unitCode, $message)
    {
        return $this->createSystemNotification(
            'Maintenance Alert',
            "Unit {$unitCode}: {$message}",
            'warning',
            '/maintenance'
        );
    }

    public function notifyRentalUpdate($rentalId, $message)
    {
        return $this->createSystemNotification(
            'Rental Update',
            "Rental {$rentalId}: {$message}",
            'info',
            '/rentals/view/' . $rentalId
        );
    }

    public function notifyReportReady($reportId, $reportName)
    {
        return $this->createSystemNotification(
            'Report Ready',
            "Your report '{$reportName}' is ready for download",
            'success',
            '/reports/view/' . $reportId
        );
    }

    public function notifySystemAlert($title, $message)
    {
        return $this->createSystemNotification($title, $message, 'error');
    }

    // Private Methods
    private function createSystemNotification($title, $message, $type, $url = null)
    {
        try {
            $this->createNotificationsTable();

            $data = [
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'user_id' => session()->get('user_id'),
                'url' => $url,
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ];

            return $this->db->table('notifications')->insert($data);

        } catch (\Exception $e) {
            log_message('error', 'Failed to create system notification: ' . $e->getMessage());
            return false;
        }
    }

    private function getNotifications($limit = 50)
    {
    if (!$this->db->tableExists('notifications')) {
            return [];
        }

        $userId = session()->get('user_id');
        $userRole = session()->get('role') ?? session()->get('department');
    // Ensure schema compatibility before querying
    $this->ensureNotificationColumns();
        return $this->db->table('notifications')
            ->groupStart()
                ->where('user_id', $userId)
                ->orWhere('target_role', $userRole)
            ->groupEnd()
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    private function getNewNotifications($userId, $lastEventId)
    {
    if (!$this->db->tableExists('notifications')) {
            return [];
        }

        $userRole = session()->get('role') ?? session()->get('department');
    $this->ensureNotificationColumns();
        // Build query defensively depending on available columns
        $builder = $this->db->table('notifications');

        // Prefer user_id if available
        if ($this->db->fieldExists('user_id', 'notifications')) {
            $builder->groupStart()
                    ->where('user_id', $userId)
                    ->orWhere('target_role', $userRole)
                ->groupEnd();
        } else {
            // Fallback to role/target_role/division if user_id doesn't exist
            if ($this->db->fieldExists('target_role', 'notifications')) {
                $builder->where('target_role', $userRole);
            } elseif ($this->db->fieldExists('role', 'notifications')) {
                $builder->where('role', $userRole);
            } else {
                // As last resort, return any notifications newer than lastEventId
                // (broadcast behavior)
            }
        }

        return $builder
            ->where('id >', $lastEventId)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();
    }

    private function getNotificationStats()
    {
        if (!$this->db->tableExists('notifications')) {
            return [
                'total' => 0,
                'unread' => 0,
                'read_today' => 0,
                'this_week' => 0
            ];
        }

        $userId = session()->get('user_id');
        $this->ensureNotificationColumns();
        $userRole = session()->get('role') ?? session()->get('department');
        
        return [
            'total' => $this->db->table('notifications')
                ->groupStart()
                    ->where('user_id', $userId)
                    ->orWhere('target_role', $userRole)
                ->groupEnd()
                ->countAllResults(),
            'unread' => $this->db->table('notifications')
                ->groupStart()
                    ->where('user_id', $userId)
                    ->orWhere('target_role', $userRole)
                ->groupEnd()
                ->where('is_read', 0)
                ->countAllResults(),
            'read_today' => $this->db->table('notifications')
                ->groupStart()
                    ->where('user_id', $userId)
                    ->orWhere('target_role', $userRole)
                ->groupEnd()
                ->where('is_read', 1)
                ->where('DATE(created_at)', date('Y-m-d'))
                ->countAllResults(),
            'this_week' => $this->db->table('notifications')
                ->groupStart()
                    ->where('user_id', $userId)
                    ->orWhere('target_role', $userRole)
                ->groupEnd()
                ->where('created_at >=', date('Y-m-d', strtotime('-7 days')))
                ->countAllResults()
        ];
    }

    private function getNotificationIcon($type)
    {
        switch ($type) {
            case 'success':
                return 'fas fa-check-circle';
            case 'warning':
                return 'fas fa-exclamation-triangle';
            case 'error':
                return 'fas fa-times-circle';
            case 'info':
            default:
                return 'fas fa-info-circle';
        }
    }

    private function createNotificationsTable()
    {
        if (!$this->db->tableExists('notifications')) {
            $forge = \Config\Database::forge();
            
            $forge->addField([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true
                ],
                'title' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255
                ],
                'message' => [
                    'type' => 'TEXT'
                ],
                'type' => [
                    'type' => 'VARCHAR',
                    'constraint' => 20,
                    'default' => 'info'
                ],
                'user_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true
                ],
                // target_role for broadcasting to a role (e.g., 'service', 'marketing')
                'target_role' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => true
                ],
                'url' => [
                    'type' => 'VARCHAR',
                    'constraint' => 500,
                    'null' => true
                ],
                'is_read' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true
                ],
                'read_at' => [
                    'type' => 'DATETIME',
                    'null' => true
                ]
            ]);
            
            $forge->addKey('id', true);
            $forge->addKey('user_id');
            $forge->addKey('is_read');
            $forge->addKey('created_at');
            $forge->createTable('notifications');
        }
        // Always ensure expected columns exist (handle legacy schema created elsewhere)
        $this->ensureNotificationColumns();
    }

    /** Ensure optional/legacy columns exist to satisfy various query paths */
    private function ensureNotificationColumns(): void
    {
        try {
            $fields = $this->db->getFieldData('notifications');
            $names = array_map(fn($f) => $f->name, $fields);
            $missing = function(string $col) use ($names) { return !in_array($col, $names, true); };
            $forge = \Config\Database::forge();
            // Add target_role if missing
            if ($missing('target_role')) {
                $forge->addColumn('notifications', [
                    'target_role' => [
                        'type' => 'VARCHAR',
                        'constraint' => 100,
                        'null' => true,
                        'after' => 'user_id'
                    ]
                ]);
            }
            // Add url/read_at if old API created a simpler table
            if ($missing('url')) {
                $forge->addColumn('notifications', [
                    'url' => [
                        'type' => 'VARCHAR',
                        'constraint' => 500,
                        'null' => true,
                        'after' => 'target_role'
                    ]
                ]);
            }
            if ($missing('read_at')) {
                $forge->addColumn('notifications', [
                    'read_at' => [
                        'type' => 'DATETIME',
                        'null' => true,
                        'after' => 'created_at'
                    ]
                ]);
            }
            // Add compatibility columns used by Api\Notifications + NotificationModel
            if ($missing('role')) {
                $forge->addColumn('notifications', [
                    'role' => [
                        'type' => 'VARCHAR',
                        'constraint' => 100,
                        'null' => true,
                        'after' => 'target_role'
                    ]
                ]);
            }
            if ($missing('division')) {
                $forge->addColumn('notifications', [
                    'division' => [
                        'type' => 'VARCHAR',
                        'constraint' => 100,
                        'null' => true,
                        'after' => 'role'
                    ]
                ]);
            }
            if ($missing('link')) {
                $forge->addColumn('notifications', [
                    'link' => [
                        'type' => 'VARCHAR',
                        'constraint' => 500,
                        'null' => true,
                        'after' => 'url'
                    ]
                ]);
            }
        } catch (\Throwable $e) {
            // swallow; schema will be re-attempted on next request
        }
    }

    /**
     * Get available activity types for notification rules
     */
    private function getActivityTypes()
    {
        return [
            'spk_created' => 'SPK Created',
            'spk_approved' => 'SPK Approved',
            'spk_rejected' => 'SPK Rejected',
            'di_created' => 'DI Created',
            'di_processed' => 'DI Processed',
            'di_completed' => 'DI Completed',
            'inventory_low_stock' => 'Low Stock Alert',
            'inventory_reorder' => 'Reorder Alert',
            'maintenance_due' => 'Maintenance Due',
            'maintenance_overdue' => 'Maintenance Overdue',
            'rental_expiring' => 'Rental Expiring',
            'payment_due' => 'Payment Due',
            'system_error' => 'System Error',
            'backup_completed' => 'Backup Completed'
        ];
    }

    /**
     * Get available roles for targeting
     */
    private function getAvailableRoles()
    {
        try {
            return $this->userModel->getDistinctRoles();
        } catch (\Exception $e) {
            return ['superadmin', 'admin', 'manager', 'staff'];
        }
    }

    /**
     * Get available divisions for targeting
     */
    private function getAvailableDivisions()
    {
        try {
            return $this->userModel->getDistinctDivisions();
        } catch (\Exception $e) {
            return ['service', 'marketing', 'finance', 'warehouse', 'administration'];
        }
    }

    /**
     * Get available departments for targeting
     */
    private function getAvailableDepartments()
    {
        try {
            return $this->userModel->getDistinctDepartments();
        } catch (\Exception $e) {
            return ['diesel', 'forklift', 'generator', 'compressor'];
        }
    }

    /**
     * Get notifications for current user
     */
    private function getNotificationsForUser()
    {
        if (!$this->db->tableExists('notifications')) {
            return [];
        }

        $userId = session()->get('user_id');
        $userRole = session()->get('role') ?? session()->get('department');
        
        $builder = $this->db->table('notifications n')
            ->select('n.*, u.first_name, u.last_name')
            ->join('users u', 'u.id = n.user_id', 'left')
            ->groupStart()
                ->where('n.user_id', $userId)
                ->orWhere('n.target_role', $userRole)
            ->groupEnd()
            ->orderBy('n.created_at', 'DESC')
            ->limit(50);

        $notifications = $builder->get()->getResultArray();

        // Ensure all notifications have required fields
        foreach ($notifications as &$notification) {
            $notification['is_read'] = $notification['is_read'] ?? 0;
            $notification['priority'] = $notification['priority'] ?? 1;
            $notification['type'] = $notification['type'] ?? 'info';
            $notification['sender_name'] = trim(($notification['first_name'] ?? '') . ' ' . ($notification['last_name'] ?? '')) ?: 'System';
        }

        return $notifications;
    }
} 