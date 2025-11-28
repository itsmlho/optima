<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Traits\ActivityLoggingTrait;

class System extends BaseController
{
    use ActivityLoggingTrait;
    
    protected $userModel;

    public function __construct()
    {
        helper('form');
        $this->userModel = new UserModel();
    }
    
    public function profile()
    {
        $userProfile = $this->getUserProfile();
        
        // Get OTP status
        $otpEnabled = !empty($userProfile['otp_enabled']) && $userProfile['otp_enabled'] == 1;
        
        // Get sessions for session management (if available)
        $sessions = [];
        $activeSessionCount = 0;
        $trackDevices = true;
        
        try {
            $db = \Config\Database::connect();
            $tableExists = $db->tableExists('user_sessions');
            
            if ($tableExists) {
                $authSecurityConfig = config('AuthSecurity');
                if ($authSecurityConfig && ($authSecurityConfig->trackDevices ?? false)) {
                    $sessionService = new \App\Services\SessionService();
                    $userId = session()->get('user_id');
                    if ($userId) {
                        $sessions = $sessionService->getUserSessions($userId, true);
                        $activeSessionCount = $sessionService->getActiveSessionCount($userId);
                    }
                }
            }
        } catch (\Exception $e) {
            log_message('debug', 'Session management skipped: ' . $e->getMessage());
        }
        
        $data = [
            'title' => 'Profil Saya',
            'page_title' => 'Profil Pengguna',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/profile' => 'Profil Saya'
            ],
            'user_data' => $userProfile,
            'user_email' => $userProfile['email'] ?? 'admin@optima.com',
            'divisions' => $this->getDivisions(),
            'locations' => $this->getLocations(),
            'supervisors' => $this->getSupervisors(),
            'profile_logs' => $this->getProfileLogs($userProfile['id'] ?? 0),
            'otp_enabled' => $otpEnabled,
            'sessions' => $sessions,
            'active_session_count' => $activeSessionCount,
            'current_session_id' => session_id(),
            'track_devices' => $trackDevices,
        ];
        
        // Ensure session avatar is set
        if (!session()->get('avatar') && !empty($userProfile['avatar'])) {
            session()->set('avatar', $userProfile['avatar']);
            log_message('debug', 'Session avatar set from database: ' . $userProfile['avatar']);
        }
        
        // Ensure session position is set
        if (!session()->get('position') && !empty($userProfile['position'])) {
            session()->set('position', $userProfile['position']);
            log_message('debug', 'Session position set from database: ' . $userProfile['position']);
        }
        
        // Ensure session division is set
        if (!session()->get('division_id') && !empty($userProfile['division_id'])) {
            session()->set('division_id', $userProfile['division_id']);
            log_message('debug', 'Session division_id set from database: ' . $userProfile['division_id']);
        }

        return view('admin/advanced_user_management/profile', $data);
    }
    
    public function updateProfile()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request method']);
        }
        
        // Debug: Log all received data
        log_message('debug', 'Profile update - POST data: ' . json_encode($this->request->getPost()));
        log_message('debug', 'Profile update - Session user_id: ' . session()->get('user_id'));
        
        $validation = \Config\Services::validation();
        // Only validate fields that are editable in the simplified profile
        $validation->setRules([
            'first_name' => 'required|min_length[2]|max_length[50]',
            'last_name'  => 'required|min_length[2]|max_length[50]',
            'phone'      => 'permit_empty|max_length[20]',
            'bio'        => 'permit_empty|max_length[500]'
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            $errors = $validation->getErrors();
            log_message('error', 'Profile validation failed: ' . json_encode($errors));
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $errors
            ]);
        }
        
        try {
            $userId = session()->get('user_id');
            if (!$userId) {
                return $this->response->setJSON(['success' => false, 'message' => 'User not authenticated']);
            }
            
            $userModel = new \App\Models\UserModel();
            // Bypass model-level validation; we already validated fields above
            if (method_exists($userModel, 'skipValidation')) { $userModel->skipValidation(true); }
            $updateData = [
                'first_name' => $this->request->getPost('first_name'),
                'last_name'  => $this->request->getPost('last_name'),
                'phone'      => $this->request->getPost('phone') ?: null,
                'bio'        => $this->request->getPost('bio') ?: null,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            log_message('debug', 'Profile update data: ' . json_encode($updateData));
            
            if ($userModel->update($userId, $updateData)) {
                // Update session data
                session()->set([
                    'first_name' => $updateData['first_name'],
                    'last_name' => $updateData['last_name']
                ]);
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Profile updated successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to update profile',
                    'errors'  => method_exists($userModel, 'errors') ? ($userModel->errors() ?: null) : null
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function uploadAvatar()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request method']);
        }
        
        // Debug: Log avatar upload attempt
        log_message('debug', 'Avatar upload - Session user_id: ' . session()->get('user_id'));
        log_message('debug', 'Avatar upload - File data: ' . json_encode($_FILES));

        $validation = \Config\Services::validation();
        $validation->setRules([
            'avatar' => [
                'label' => 'Avatar Image',
                'rules' => 'uploaded[avatar]|is_image[avatar]|mime_in[avatar,image/jpg,image/jpeg,image/png,image/gif,image/webp]|max_size[avatar,2048]'
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            $errors = $validation->getErrors();
            $errorMessage = 'Invalid image file. ';
            
            // Custom error messages
            if (isset($errors['avatar'])) {
                if (strpos($errors['avatar'], 'mime_in') !== false) {
                    $errorMessage = 'Unsupported image format. Please use JPG, PNG, GIF, or WebP format.';
                } elseif (strpos($errors['avatar'], 'max_size') !== false) {
                    $errorMessage = 'Image file is too large. Maximum size is 2MB.';
                } elseif (strpos($errors['avatar'], 'is_image') !== false) {
                    $errorMessage = 'Please select a valid image file.';
                } else {
                    $errorMessage = 'Invalid image file: ' . $errors['avatar'];
                }
            }
            
            return $this->response->setJSON([
                'success' => false,
                'message' => $errorMessage,
                'errors'  => $errors
            ]);
        }

        try {
            $userId = session()->get('user_id');
            if (!$userId) {
                return $this->response->setJSON(['success' => false, 'message' => 'User not authenticated']);
            }

            $file = $this->request->getFile('avatar');
            if (!$file || !$file->isValid()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Invalid file']);
            }

            $uploadPath = FCPATH . 'uploads/avatars/';
            if (!is_dir($uploadPath)) { 
                @mkdir($uploadPath, 0777, true); 
                @chmod($uploadPath, 0777);
            }

            $newName = 'avatar_' . $userId . '_' . time() . '.' . $file->getExtension();
            if (!$file->move($uploadPath, $newName)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Failed to move uploaded file']);
            }
            
            // Set proper permissions for uploaded file
            @chmod($uploadPath . $newName, 0666);

            // Generate avatar URL - use relative path for better compatibility
            $avatarUrl = 'uploads/avatars/' . $newName;
            log_message('debug', 'Avatar upload - Generated URL: ' . $avatarUrl);
            
            $userModel = new \App\Models\UserModel();
            if (method_exists($userModel, 'skipValidation')) { $userModel->skipValidation(true); }
            
            $updateData = ['avatar' => $avatarUrl, 'updated_at' => date('Y-m-d H:i:s')];
            log_message('debug', 'Avatar upload - Update data: ' . json_encode($updateData));
            
            if ($userModel->update($userId, $updateData)) {
                log_message('info', 'Avatar uploaded and saved to database successfully for user ' . $userId);
                
                // Update session avatar
                session()->set('avatar', $avatarUrl);
                log_message('debug', 'Session avatar updated to: ' . $avatarUrl);
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Avatar uploaded successfully',
                    'avatar_url' => $avatarUrl
                ]);
            }

            log_message('error', 'Failed to update avatar in database for user ' . $userId);
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update avatar in database']);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function settings()
    {
        $data = [
            'title' => 'Pengaturan',
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
            'title' => 'Notifikasi',
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
            'title' => 'Bantuan',
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
        $userId = session()->get('user_id');
        
        if (!$userId) {
            return [
                'id' => 0,
                'username' => 'guest',
                'first_name' => 'Guest',
                'last_name' => 'User',
                'email' => 'guest@example.com',
                'role' => 'guest',
                'department' => 'N/A',
                'phone' => '',
                'address' => '',
                'joined_date' => date('Y-m-d'),
                'last_login' => date('Y-m-d H:i:s'),
                'avatar' => base_url('assets/images/default-avatar.svg')
            ];
        }
        
        // Get user data from database
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);
        
        if (!$user) {
            return [
                'id' => 0,
                'username' => 'unknown',
                'first_name' => 'Unknown',
                'last_name' => 'User',
                'email' => 'unknown@example.com',
                'role' => 'unknown',
                'department' => 'N/A',
                'phone' => '',
                'address' => '',
                'joined_date' => date('Y-m-d'),
                'last_login' => date('Y-m-d H:i:s'),
                'avatar' => base_url('assets/images/default-avatar.svg')
            ];
        }
        
        // Get division and role info
        $db = \Config\Database::connect();
        
        // Get division info
        $divisionInfo = $db->table('users u')
            ->select('d.name as division_name')
            ->join('divisions d', 'd.id = u.division_id', 'left')
            ->where('u.id', $userId)
            ->get()
            ->getRowArray();
        
        // Get role info
        $roleInfo = $db->table('user_roles ur')
            ->select('r.name as role_name')
            ->join('roles r', 'r.id = ur.role_id', 'left')
            ->where('ur.user_id', $userId)
            ->get()
            ->getRowArray();
        
        return [
            'id' => $user['id'],
            'username' => $user['username'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'email' => $user['email'],
            'phone' => $user['phone'] ?? '',
            'position' => $user['position'] ?? '',
            'division_name' => $divisionInfo['division_name'] ?? 'No Division',
            'role_name' => $roleInfo['role_name'] ?? 'No Role',
            'location' => $user['location'] ?? '',
            'bio' => $user['bio'] ?? '',
            'avatar' => $user['avatar'] ?? base_url('assets/images/default-avatar.svg'),
            'created_at' => $user['created_at'],
            'updated_at' => $user['updated_at']
        ];
    }

    private function getDivisions()
    {
        try {
            $db = \Config\Database::connect();
            $divisions = $db->table('divisions')
                ->select('id, name')
                ->where('is_active', 1)
                ->get()
                ->getResultArray();
            
            $divisionMap = [];
            foreach ($divisions as $division) {
                $divisionMap[$division['id']] = $division['name'];
            }
            
            return $divisionMap;
        } catch (\Exception $e) {
            // Fallback to hardcoded divisions
            return [
                'service' => 'Service Division',
                'rolling_unit' => 'Rolling Unit Division',
                'marketing' => 'Marketing Division',
                'warehouse' => 'Warehouse & Assets Division',
                'finance' => 'Finance Division',
                'hr' => 'Human Resources',
                'it' => 'Information Technology',
                'management' => 'Management'
            ];
        }
    }
    
    private function getLocations()
    {
        return [
            'jakarta_pusat' => 'Jakarta Pusat',
            'jakarta_utara' => 'Jakarta Utara',
            'jakarta_selatan' => 'Jakarta Selatan',
            'jakarta_timur' => 'Jakarta Timur',
            'jakarta_barat' => 'Jakarta Barat',
            'bogor' => 'Bogor',
            'depok' => 'Depok',
            'tangerang' => 'Tangerang',
            'bekasi' => 'Bekasi',
            'bandung' => 'Bandung',
            'surabaya' => 'Surabaya',
            'medan' => 'Medan',
            'semarang' => 'Semarang',
            'palembang' => 'Palembang',
            'makassar' => 'Makassar'
        ];
    }
    
    private function getSupervisors()
    {
        try {
            $userModel = new \App\Models\UserModel();
            return $userModel->where('role', 'manager')
                              ->orWhere('role', 'supervisor')
                              ->findAll();
        } catch (\Exception $e) {
            return [];
        }
    }
    
    private function getProfileLogs($userId)
    {
        try {
            $db = \Config\Database::connect();
            
            if (!$db->tableExists('profile_logs')) {
                return [];
            }
            
            return $db->table('profile_logs')
                      ->where('user_id', $userId)
                      ->orderBy('created_at', 'DESC')
                      ->limit(50)
                      ->get()
                      ->getResultArray();
        } catch (\Exception $e) {
            return [];
        }
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

    /**
     * Toggle OTP (Enable/Disable) for current user
     */
    public function toggleOtp()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request.']);
        }

        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not authenticated.']);
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not found.']);
        }

        $currentOtpStatus = !empty($user['otp_enabled']) && $user['otp_enabled'] == 1;
        $newOtpStatus = !$currentOtpStatus;

        $updateData = [
            'otp_enabled' => $newOtpStatus ? 1 : 0,
            'otp_enabled_at' => $newOtpStatus ? date('Y-m-d H:i:s') : null,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->userModel->update($userId, $updateData)) {
            $message = $newOtpStatus ? 'Two-Factor Authentication (OTP) berhasil diaktifkan.' : 'Two-Factor Authentication (OTP) berhasil dinonaktifkan.';
            
            // Log activity if trait is available
            if (method_exists($this, 'logAuthActivity')) {
                $this->logAuthActivity('OTP_TOGGLE', $userId, [
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'action' => $newOtpStatus ? 'enabled' : 'disabled',
                    'description' => $message
                ]);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => $message,
                'otp_enabled' => $newOtpStatus
            ]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal mengubah status OTP. Silakan coba lagi.']);
        }
    }
}
