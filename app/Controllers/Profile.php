<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Profile extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $userId = session()->get('user_id') ?: 1;
        
        try {
            $user = $this->userModel->find($userId);
            
            if (!$user) {
                // Create default user if none exists
                $user = [
                    'id' => 1,
                    'first_name' => 'Admin',
                    'last_name' => 'System',
                    'email' => 'admin@optima.com',
                    'phone' => '',
                    'avatar' => null,
                    'role' => 'admin',
                    'status' => 'active',
                    'division' => 'admin',
                    'position' => 'System Administrator',
                    'location' => 'jakarta',
                    'supervisor_id' => null,
                    'bio' => 'System Administrator',
                    'created_at' => date('Y-m-d H:i:s')
                ];
                
                // Set session data
                session()->set([
                    'user_id' => 1,
                    'first_name' => 'Admin',
                    'last_name' => 'System',
                    'email' => 'admin@optima.com',
                    'role' => 'admin',
                    'division' => 'admin'
                ]);
            }
        } catch (\Exception $e) {
            // If table doesn't exist, create default user data
            $user = [
                'id' => 1,
                'first_name' => 'Admin',
                'last_name' => 'System',
                'email' => 'admin@optima.com',
                'phone' => '',
                'avatar' => null,
                'role' => 'admin',
                'status' => 'active',
                'division' => 'admin',
                'position' => 'System Administrator',
                'location' => 'jakarta',
                'supervisor_id' => null,
                'bio' => 'System Administrator',
                'created_at' => date('Y-m-d H:i:s')
            ];
        }

        $data = [
            'title' => 'Profile | OPTIMA',
            'page_title' => 'Profile Management',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/profile' => 'Profile'
            ],
            'user_data' => $user,
            'divisions' => $this->getDivisions(),
            'locations' => $this->getLocations(),
            'supervisors' => $this->getSupervisors(),
            'profile_logs' => $this->getProfileLogs($userId)
        ];

        return view('profile/index', $data);
    }

    public function update()
    {
        $userId = session()->get('user_id');
        
        $validation = \Config\Services::validation();
        $validation->setRules([
            'first_name' => 'required|min_length[2]|max_length[50]',
            'last_name' => 'required|min_length[2]|max_length[50]',
            'email' => 'required|valid_email',
            'phone' => 'permit_empty|min_length[10]|max_length[15]',
            'division' => 'required',
            'position' => 'required|max_length[100]',
            'location' => 'required',
            'supervisor_id' => 'permit_empty|integer',
            'bio' => 'permit_empty|max_length[500]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $currentUser = $this->userModel->find($userId);
        $updateData = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'division' => $this->request->getPost('division'),
            'position' => $this->request->getPost('position'),
            'location' => $this->request->getPost('location'),
            'supervisor_id' => $this->request->getPost('supervisor_id') ?: null,
            'bio' => $this->request->getPost('bio'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Log changes
        $changes = $this->getChanges($currentUser, $updateData);
        
        if ($this->userModel->update($userId, $updateData)) {
            // Log the profile changes
            $this->logProfileChange($userId, 'profile_update', $changes);
            
            // Update session data
            session()->set([
                'first_name' => $updateData['first_name'],
                'last_name' => $updateData['last_name'],
                'email' => $updateData['email'],
                'division' => $updateData['division']
            ]);

            return redirect()->to('/profile')->with('success', 'Profile updated successfully');
        } else {
            return redirect()->back()->with('error', 'Failed to update profile');
        }
    }

    public function changePassword()
    {
        $userId = session()->get('user_id');
        
        $validation = \Config\Services::validation();
        $validation->setRules([
            'current_password' => 'required',
            'new_password' => 'required|min_length[8]',
            'confirm_password' => 'required|matches[new_password]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->with('errors', $validation->getErrors());
        }

        $user = $this->userModel->find($userId);
        
        if (!password_verify($this->request->getPost('current_password'), $user['password'])) {
            return redirect()->back()->with('error', 'Current password is incorrect');
        }

        $newPassword = password_hash($this->request->getPost('new_password'), PASSWORD_DEFAULT);
        
        if ($this->userModel->update($userId, ['password' => $newPassword, 'updated_at' => date('Y-m-d H:i:s')])) {
            // Log password change
            $this->logProfileChange($userId, 'password_change', ['action' => 'Password changed']);
            
            return redirect()->to('/profile')->with('success', 'Password changed successfully');
        } else {
            return redirect()->back()->with('error', 'Failed to change password');
        }
    }

    public function uploadAvatar()
    {
        $userId = session()->get('user_id');
        
        $validation = \Config\Services::validation();
        $validation->setRules([
            'avatar' => 'uploaded[avatar]|is_image[avatar]|max_size[avatar,2048]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid file. Please upload a valid image (max 2MB)'
            ]);
        }

        $file = $this->request->getFile('avatar');
        
        if ($file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $uploadPath = WRITEPATH . 'uploads/avatars/';
            
            // Create directory if it doesn't exist
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            if ($file->move($uploadPath, $newName)) {
                $avatarUrl = base_url('writable/uploads/avatars/' . $newName);
                
                // Update user avatar
                if ($this->userModel->update($userId, ['avatar' => $avatarUrl, 'updated_at' => date('Y-m-d H:i:s')])) {
                    // Log avatar change
                    $this->logProfileChange($userId, 'avatar_change', ['avatar' => $avatarUrl]);
                    
                    // Update session
                    session()->set('avatar', $avatarUrl);
                    
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Avatar updated successfully',
                        'avatar_url' => $avatarUrl
                    ]);
                }
            }
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to upload avatar'
        ]);
    }

    public function getLogs()
    {
        $userId = session()->get('user_id');
        $logs = $this->getProfileLogs($userId);
        
        return $this->response->setJSON([
            'success' => true,
            'logs' => $logs
        ]);
    }

    private function getDivisions()
    {
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
            return $this->userModel->where('role', 'manager')
                                  ->orWhere('role', 'supervisor')
                                  ->findAll();
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getChanges($currentData, $newData)
    {
        $changes = [];
        
        foreach ($newData as $key => $value) {
            if (isset($currentData[$key]) && $currentData[$key] != $value) {
                $changes[$key] = [
                    'from' => $currentData[$key],
                    'to' => $value
                ];
            }
        }
        
        return $changes;
    }

    private function logProfileChange($userId, $action, $changes)
    {
        $db = \Config\Database::connect();
        
        // Create profile_logs table if it doesn't exist
        $this->createProfileLogsTable($db);
        
        $logData = [
            'user_id' => $userId,
            'action' => $action,
            'changes' => json_encode($changes),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $db->table('profile_logs')->insert($logData);
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

    private function createProfileLogsTable($db)
    {
        if (!$db->tableExists('profile_logs')) {
            $forge = \Config\Database::forge();
            
            $forge->addField([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true
                ],
                'user_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true
                ],
                'action' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50
                ],
                'changes' => [
                    'type' => 'TEXT',
                    'null' => true
                ],
                'ip_address' => [
                    'type' => 'VARCHAR',
                    'constraint' => 45,
                    'null' => true
                ],
                'user_agent' => [
                    'type' => 'TEXT',
                    'null' => true
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true
                ]
            ]);
            
            $forge->addKey('id', true);
            $forge->addKey('user_id');
            $forge->createTable('profile_logs');
        }
    }
} 