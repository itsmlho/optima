<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class System extends BaseController
{
    public function profile()
    {
        $userProfile = $this->getUserProfile();
        
        $data = [
            'title' => 'Profil Saya | OPTIMA',
            'page_title' => 'Profil Pengguna',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/profile' => 'Profil Saya'
            ],
            'user_data' => $userProfile,
            'user_email' => $userProfile['email'] ?? 'admin@optima.com',
        ];

        return view('system/profile', $data);
    }

    public function settings()
    {
        $data = [
            'title' => 'Pengaturan | OPTIMA',
            'page_title' => 'Pengaturan Sistem',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/settings' => 'Pengaturan'
            ],
            'system_settings' => $this->getSystemSettings(),
        ];

        return view('system/settings', $data);
    }

    public function notifications()
    {
        // Load notification data properly
        $db = \Config\Database::connect();
        $this->ensureNotificationTables($db);
        
        $stats = $this->getNotificationStats($db);
        $notifications = $this->getNotificationsForUser($db);
        
        $data = [
            'title' => 'Notifikasi | OPTIMA',
            'page_title' => 'Pusat Notifikasi',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/notifications' => 'Notifikasi'
            ],
            'stats' => $stats,
            'notifications' => $notifications,
        ];

        return view('notifications/index', $data);
    }

    public function help()
    {
        $data = [
            'title' => 'Bantuan | OPTIMA',
            'page_title' => 'Pusat Bantuan',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/help' => 'Bantuan'
            ],
            'help_topics' => $this->getHelpTopics(),
        ];

        return view('system/help', $data);
    }

    public function logout()
    {
        // Clear session
        session()->destroy();
        return redirect()->to('/auth/login')->with('success', 'Anda telah berhasil logout');
    }

    private function getUserProfile()
    {
        // Mock user profile data
        return [
            'id' => 1,
            'username' => 'admin',
            'first_name' => 'Administrator',
            'last_name' => 'System',
            'email' => 'admin@optima.com',
            'role' => 'admin',
            'department' => 'IT',
            'phone' => '+62 812-3456-7890',
            'address' => 'Jakarta, Indonesia',
            'joined_date' => '2023-01-15',
            'last_login' => '2024-01-15 08:30:00',
            'avatar' => base_url('assets/images/default-avatar.svg')
        ];
    }

    private function getSystemSettings()
    {
        // Mock system settings
        return [
            'company_name' => 'PT Sarana Mitra Luas Tbk',
            'company_address' => 'Jakarta, Indonesia',
            'company_phone' => '+62 21-1234-5678',
            'company_email' => 'info@optima.com',
            'timezone' => 'Asia/Jakarta',
            'date_format' => 'd/m/Y',
            'currency' => 'IDR',
            'theme' => 'light',
            'notifications_enabled' => true,
            'email_notifications' => true,
            'sms_notifications' => false,
            'backup_frequency' => 'daily',
            'session_timeout' => 30
        ];
    }

    private function getNotifications()
    {
        // Mock notifications data
        return [
            [
                'id' => 1,
                'type' => 'maintenance',
                'title' => 'Unit FL-045 Maintenance Urgent',
                'message' => 'Engine overheat detected. Perlu segera diperiksa.',
                'icon' => 'fas fa-exclamation-triangle',
                'color' => 'danger',
                'time' => '2 jam yang lalu',
                'read' => false
            ],
            [
                'id' => 2,
                'type' => 'schedule',
                'title' => 'Maintenance Terjadwal Besok',
                'message' => '5 unit memerlukan service rutin.',
                'icon' => 'fas fa-calendar-check',
                'color' => 'warning',
                'time' => '5 jam yang lalu',
                'read' => false
            ],
            [
                'id' => 3,
                'type' => 'invoice',
                'title' => 'Invoice Overdue',
                'message' => 'PT Mandiri Logistik - INV-001234',
                'icon' => 'fas fa-file-invoice-dollar',
                'color' => 'info',
                'time' => '1 hari yang lalu',
                'read' => true
            ],
            [
                'id' => 4,
                'type' => 'contract',
                'title' => 'Kontrak Baru Ditandatangani',
                'message' => 'CV Sejahtera Bersama - 12 bulan kontrak',
                'icon' => 'fas fa-file-contract',
                'color' => 'success',
                'time' => '2 hari yang lalu',
                'read' => true
            ],
        ];
    }

    private function getHelpTopics()
    {
        // Mock help topics
        return [
            [
                'category' => 'Memulai',
                'icon' => 'fas fa-play-circle',
                'topics' => [
                    ['title' => 'Cara Login ke Sistem', 'url' => '#'],
                    ['title' => 'Navigasi Dashboard', 'url' => '#'],
                    ['title' => 'Pengaturan Akun', 'url' => '#'],
                ]
            ],
            [
                'category' => 'Manajemen Unit',
                'icon' => 'fas fa-truck',
                'topics' => [
                    ['title' => 'Menambah Unit Baru', 'url' => '#'],
                    ['title' => 'Update Status Unit', 'url' => '#'],
                    ['title' => 'Laporan Unit', 'url' => '#'],
                ]
            ],
            [
                'category' => 'Rental & Marketing',
                'icon' => 'fas fa-handshake',
                'topics' => [
                    ['title' => 'Membuat Penawaran', 'url' => '#'],
                    ['title' => 'Manajemen Kontrak', 'url' => '#'],
                    ['title' => 'Laporan Rental', 'url' => '#'],
                ]
            ],
            [
                'category' => 'Maintenance & Service',
                'icon' => 'fas fa-tools',
                'topics' => [
                    ['title' => 'Schedule PMPS', 'url' => '#'],
                    ['title' => 'Work Order Management', 'url' => '#'],
                    ['title' => 'Sparepart Management', 'url' => '#'],
                ]
            ],
        ];
    }

    private function ensureNotificationTables($db)
    {
        if (!$db->tableExists('notifications')) {
            // Create notifications table if not exists
            $forge = \Config\Database::forge();
            
            $fields = [
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true
                ],
                'user_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true
                ],
                'target_role' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'null' => true
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
                    'constraint' => 50,
                    'default' => 'info'
                ],
                'priority' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 1
                ],
                'is_read' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0
                ],
                'read_at' => [
                    'type' => 'DATETIME',
                    'null' => true
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true
                ]
            ];
            
            $forge->addField($fields);
            $forge->addKey('id', true);
            $forge->addKey('user_id');
            $forge->addKey('target_role');
            $forge->createTable('notifications');
        }
    }

    private function getNotificationStats($db)
    {
        if (!$db->tableExists('notifications') || !$db->tableExists('notification_recipients')) {
            return [
                'total' => 0,
                'unread' => 0,
                'read_today' => 0,
                'this_week' => 0
            ];
        }

        $userId = session()->get('user_id');
        
        return [
            'total' => $db->table('notification_recipients nr')
                ->join('notifications n', 'n.id = nr.notification_id')
                ->where('nr.user_id', $userId)
                ->countAllResults(),
            'unread' => $db->table('notification_recipients nr')
                ->join('notifications n', 'n.id = nr.notification_id')
                ->where('nr.user_id', $userId)
                ->where('nr.is_read', 0)
                ->countAllResults(),
            'read_today' => $db->table('notification_recipients nr')
                ->join('notifications n', 'n.id = nr.notification_id')
                ->where('nr.user_id', $userId)
                ->where('nr.is_read', 1)
                ->where('DATE(nr.read_at)', date('Y-m-d'))
                ->countAllResults(),
            'this_week' => $db->table('notification_recipients nr')
                ->join('notifications n', 'n.id = nr.notification_id')
                ->where('nr.user_id', $userId)
                ->where('n.created_at >=', date('Y-m-d', strtotime('-7 days')))
                ->countAllResults()
        ];
    }

    private function getNotificationsForUser($db)
    {
        if (!$db->tableExists('notifications') || !$db->tableExists('notification_recipients')) {
            return [];
        }

        $userId = session()->get('user_id');
        
        $builder = $db->table('notification_recipients nr')
            ->select('n.*, nr.is_read, nr.read_at, u.first_name, u.last_name')
            ->join('notifications n', 'n.id = nr.notification_id')
            ->join('users u', 'u.id = n.created_by', 'left')
            ->where('nr.user_id', $userId)
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
