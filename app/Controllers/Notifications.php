<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Notifications extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
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
        $data = [
            'title' => 'Notifications | OPTIMA',
            'page_title' => 'Notification Center',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/notifications' => 'Notifications'
            ],
            'notifications' => $this->getNotifications(),
            'notification_stats' => $this->getNotificationStats()
        ];

    return view('notifications/index', $data);
    }

    public function stream()
    {
        // Set headers for Server-Sent Events
        $this->response->setHeader('Content-Type', 'text/event-stream');
        $this->response->setHeader('Cache-Control', 'no-cache');
        $this->response->setHeader('Connection', 'keep-alive');
        $this->response->setHeader('Access-Control-Allow-Origin', '*');
        $this->response->setHeader('Access-Control-Allow-Headers', 'Cache-Control');

        // Disable output buffering
        if (ob_get_level()) {
            ob_end_clean();
        }

        $userId = session()->get('user_id');
        $lastEventId = $this->request->getHeaderLine('Last-Event-ID') ?: 0;

    // Create notifications table if it doesn't exist and ensure columns
    $this->createNotificationsTable();
    $this->ensureNotificationColumns();

        while (true) {
            // Check for new notifications
            $notifications = $this->getNewNotifications($userId, $lastEventId);
            
            if (!empty($notifications)) {
                foreach ($notifications as $notification) {
                    $data = [
                        'id' => $notification['id'],
                        'title' => $notification['title'],
                        'message' => $notification['message'],
                        'type' => $notification['type'],
                        'icon' => $this->getNotificationIcon($notification['type']),
                        'timestamp' => date('H:i:s', strtotime($notification['created_at'])),
                        'url' => $notification['url']
                    ];

                    echo "id: {$notification['id']}\n";
                    echo "event: notification\n";
                    echo "data: " . json_encode($data) . "\n\n";
                    
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
        try {
            $userId = session()->get('user_id');
            
            $updated = $this->db->table('notifications')
                               ->where('id', $notificationId)
                               ->where('user_id', $userId)
                               ->update(['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')]);

            if ($updated) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Notification marked as read'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Notification not found or already read'
                ]);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to mark notification as read: ' . $e->getMessage()
            ]);
        }
    }

    public function markAllAsRead()
    {
        try {
            $userId = session()->get('user_id');
            
            $this->db->table('notifications')
                     ->where('user_id', $userId)
                     ->where('is_read', 0)
                     ->update(['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')]);

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
        try {
            $userId = session()->get('user_id');
            
            $deleted = $this->db->table('notifications')
                               ->where('id', $notificationId)
                               ->where('user_id', $userId)
                               ->delete();

            if ($deleted) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Notification deleted successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Notification not found'
                ]);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete notification: ' . $e->getMessage()
            ]);
        }
    }

    public function getCount()
    {
        try {
            $userId = session()->get('user_id');
            
            if (!$this->db->tableExists('notifications')) {
                return $this->response->setJSON(['count' => 0]);
            }

            $count = $this->db->table('notifications')
                             ->where('user_id', $userId)
                             ->where('is_read', 0)
                             ->countAllResults();

            return $this->response->setJSON(['count' => $count]);

        } catch (\Exception $e) {
            return $this->response->setJSON(['count' => 0]);
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
        return $this->db->table('notifications')
            ->groupStart()
                ->where('user_id', $userId)
                ->orWhere('target_role', $userRole)
            ->groupEnd()
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
                'today' => 0,
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
            'today' => $this->db->table('notifications')
                ->groupStart()
                    ->where('user_id', $userId)
                    ->orWhere('target_role', $userRole)
                ->groupEnd()
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
} 