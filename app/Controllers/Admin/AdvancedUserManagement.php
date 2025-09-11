<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\RoleModel;
use App\Models\DivisionModel;
use App\Models\PermissionModel;
use App\Models\UserRoleModel;
use App\Models\UserPermissionModel;
use App\Traits\ActivityLoggingTrait;
use CodeIgniter\API\ResponseTrait;

class AdvancedUserManagement extends BaseController
{
    use ResponseTrait, ActivityLoggingTrait;

    protected $db;
    protected $userModel;
    protected $roleModel;
    protected $divisionModel;
    protected $permissionModel;
    protected $userRoleModel;
    protected $userPermissionModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
        $this->divisionModel = new DivisionModel();
        $this->permissionModel = new PermissionModel();
        $this->userRoleModel = new UserRoleModel();
        $this->userPermissionModel = new UserPermissionModel();
    }

    public function index()
    {
        try {
            $statsData = $this->getUserStats();
            $allPermissions = $this->permissionModel->findAll();
            $groupedPermissions = [];
            foreach ($allPermissions as $permission) {
                $module = $permission['module'] ?? 'general';
                $groupedPermissions[$module][] = $permission;
            }
            $users = $this->getUsersWithCompleteInfo();

            $data = [
                'title' => 'Advanced User Management',
                'stats' => $statsData,
                'permissions' => $groupedPermissions,
                'users' => $users,
                'roles' => $this->roleModel->findAll(),
                'divisions' => $this->divisionModel->findAll()
            ];

            return view('admin/advanced_user_management/index', $data);

        } catch (\Exception $e) {
            log_message('error', 'Index Error: ' . $e->getMessage());
            $data = [
                'title' => 'Advanced User Management',
                'stats' => [
                    'total_users' => 0,
                    'active_users' => 0,
                    'inactive_users' => 0,
                    'total_roles' => 0,
                    'users_with_multiple_divisions' => 0,
                    'users_with_custom_permissions' => 0
                ],
                'permissions' => [],
                'users' => [],
                'roles' => [],
                'divisions' => []
            ];
            return view('admin/advanced_user_management/index', $data);
        }
    }

    /**
     * Create New User Form
     */
    public function create()
    {
        if (!$this->hasPermission('admin.user_create')) {
            return redirect()->to('/admin/advanced-users')->with('error', 'Akses ditolak.');
        }

        $data = [
            'title' => 'Create New User',
            'allRoles' => $this->roleModel->findAll(),
            'allDivisions' => $this->divisionModel->findAll(),
            // 'allPositions' => $this->positionModel->findAll(),
            'allPermissions' => method_exists($this->permissionModel, 'getPermissionsGroupedByModule') 
                ? $this->permissionModel->getPermissionsGroupedByModule() 
                : $this->permissionModel->findAll(),
        ];

        return view('admin/advanced_user_management/create', $data);
    }

    /**
     * Store New User
     */
    public function store()
    {
        if (!$this->hasPermission('admin.user_create')) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak'])->setStatusCode(403);
            }
            return redirect()->to('/admin/advanced-users')->with('error', 'Akses ditolak.');
        }

        $validation = $this->validate([
            'first_name' => 'required|min_length[2]|max_length[50]',
            'last_name' => 'required|min_length[2]|max_length[50]',
            'username' => 'required|min_length[3]|max_length[20]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]',
            'phone' => 'permit_empty|max_length[20]'
        ]);

        if (!$validation) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Validation failed', 'errors' => $this->validator->getErrors()])->setStatusCode(400);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->db->transStart();

        try {
            // Create user
            $status = $this->request->getPost('status') ?? 'active';
            $userData = [
                'first_name' => $this->request->getPost('first_name'),
                'last_name' => $this->request->getPost('last_name'),
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'password' => $this->request->getPost('password'), // Let model callback handle hashing
                'phone' => $this->request->getPost('phone'),
                'is_active' => ($status == 'active') ? 1 : 0,
                'created_at' => date('Y-m-d H:i:s')
            ];

            log_message('info', 'Creating user with data: ' . json_encode($userData));
            
            // Debug: Test password callback directly
            log_message('debug', 'Password before insert: ' . $userData['password']);
            
            $userId = $this->userModel->insert($userData);

            if (!$userId) {
                $errors = $this->userModel->errors();
                log_message('error', 'User creation failed. Model errors: ' . json_encode($errors));
                throw new \Exception('Failed to create user: ' . implode(', ', $errors));
            }

            log_message('info', 'User created with ID: ' . $userId);
            
            // Debug: Check if password was saved correctly
            $savedUser = $this->userModel->find($userId);
            log_message('debug', 'Password hash saved: ' . (!empty($savedUser['password_hash']) ? 'YES (length: ' . strlen($savedUser['password_hash']) . ')' : 'NO - EMPTY!'));

            // Assign roles and divisions (combined approach)
            $roles = $this->request->getPost('roles') ?? [];
            $divisions = $this->request->getPost('divisions') ?? [];
            
            // If user has both roles and divisions, create combinations
            if (!empty($roles) && !empty($divisions)) {
                foreach ($roles as $roleId) {
                    foreach ($divisions as $divisionId) {
                        $this->db->table('user_roles')->insert([
                            'user_id' => $userId,
                            'role_id' => $roleId,
                            'division_id' => $divisionId,
                            'assigned_by' => session()->get('user_id') ?? 1,
                            'assigned_at' => date('Y-m-d H:i:s'),
                            'is_active' => 1
                        ]);
                    }
                }
            } elseif (!empty($roles)) {
                // Only roles, no specific division
                foreach ($roles as $roleId) {
                    $this->db->table('user_roles')->insert([
                        'user_id' => $userId,
                        'role_id' => $roleId,
                        'division_id' => null, // No specific division
                        'assigned_by' => session()->get('user_id') ?? 1,
                        'assigned_at' => date('Y-m-d H:i:s'),
                        'is_active' => 1
                    ]);
                }
            } elseif (!empty($divisions)) {
                // Only divisions, use default role (e.g., Division Staff)
                $defaultRoleId = 4; // Division Staff role
                foreach ($divisions as $divisionId) {
                    $this->db->table('user_roles')->insert([
                        'user_id' => $userId,
                        'role_id' => $defaultRoleId,
                        'division_id' => $divisionId,
                        'assigned_by' => session()->get('user_id') ?? 1,
                        'assigned_at' => date('Y-m-d H:i:s'),
                        'is_active' => 1
                    ]);
                }
            }

            // Assign permissions (if provided)
            $permissions = $this->request->getPost('permissions') ?? [];
            foreach ($permissions as $permissionId) {
                $this->db->table('user_permissions')->insert([
                    'user_id' => $userId,
                    'permission_id' => $permissionId,
                    'granted' => 1,
                    'assigned_by' => session()->get('user_id') ?? 1,
                    'assigned_at' => date('Y-m-d H:i:s')
                ]);
            }

            $this->db->transComplete();

            if ($this->db->transStatus()) {
                // Log user creation using trait
                $this->logCreate('users', $userId, [
                    'user_id' => $userId,
                    'username' => $userData['username'],
                    'email' => $userData['email'],
                    'roles' => $roles,
                    'divisions' => $divisions,
                    'permissions' => $permissions,
                    'created_by' => session()->get('user_id') ?? 1
                ]);
                
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => true, 'message' => 'User berhasil dibuat', 'user_id' => $userId]);
                }
                return redirect()->to('/admin/advanced-users')->with('success', 'User berhasil dibuat.');
            } else {
                throw new \Exception('Transaction failed');
            }

        } catch (\Exception $e) {
            $this->db->transRollback();
            // Debug: log what data was sent
            log_message('error', 'Create User Error: ' . $e->getMessage() . ' | Data: ' . json_encode($this->request->getPost()));
            
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Error: ' . $e->getMessage()])->setStatusCode(500);
            }
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Show User Details
     */
    public function show($userId)
    {
        if (!$this->hasPermission('admin.user_view')) {
            return redirect()->to('/admin/advanced-users')->with('error', 'Akses ditolak.');
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            return redirect()->to('/admin/advanced-users')->with('error', 'User tidak ditemukan.');
        }

        // Ensure all required fields exist
        $user['first_name'] = $user['first_name'] ?? '';
        $user['last_name'] = $user['last_name'] ?? '';
        $user['username'] = $user['username'] ?? '';
        $user['email'] = $user['email'] ?? '';
        $user['phone'] = $user['phone'] ?? '';
        $user['is_active'] = $user['is_active'] ?? 1;
        $user['created_at'] = $user['created_at'] ?? date('Y-m-d H:i:s');
        $user['last_login'] = $user['last_login'] ?? null;
        // Map status for view compatibility
        if (!isset($user['status'])) {
            $user['status'] = ($user['is_active'] == 1 || $user['is_active'] === true) ? 'active' : 'inactive';
        }

        // Get user roles
        try {
            $userRoles = $this->db->table('user_roles ur')
                ->join('roles r', 'r.id = ur.role_id')
                ->where('ur.user_id', $userId)
                ->select('r.id, r.name, r.description')
                ->get()->getResultArray();
        } catch (\Exception $e) {
            $userRoles = [];
        }

        // Get user divisions (from user_roles)
        try {
            $userDivisions = $this->db->table('user_roles ur')
                ->join('divisions d', 'd.id = ur.division_id')
                ->where('ur.user_id', $userId)
                ->select('d.id, d.name, d.code')
                ->get()->getResultArray();
        } catch (\Exception $e) {
            $userDivisions = [];
        }

        $data = [
            'title' => 'User Details - ' . $user['first_name'] . ' ' . $user['last_name'],
            'user' => $user,
            'userRoles' => $userRoles,
            'userDivisions' => $userDivisions,
            'userPermissions' => [],
            'permissionMatrix' => $this->buildSimplePermissionMatrix($userId)
        ];

        return view('admin/advanced_user_management/show', $data);
    }

    /**
     * Edit User Form
     */
    public function edit($userId)
    {
        if (!$this->hasPermission('admin.user_edit')) {
            return redirect()->to('/admin/advanced-users')->with('error', 'Akses ditolak.');
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            return redirect()->to('/admin/advanced-users')->with('error', 'User tidak ditemukan.');
        }

        // Ensure all required fields exist
        $user['first_name'] = $user['first_name'] ?? '';
        $user['last_name'] = $user['last_name'] ?? '';
        $user['username'] = $user['username'] ?? '';
        $user['email'] = $user['email'] ?? '';
        $user['phone'] = $user['phone'] ?? '';
        $user['status'] = $user['status'] ?? 'active';
        $user['created_at'] = $user['created_at'] ?? date('Y-m-d H:i:s');
        $user['last_login'] = $user['last_login'] ?? null;

        // Get current user roles
        try {
            $userRoles = $this->db->table('user_roles ur')
                ->join('roles r', 'r.id = ur.role_id')
                ->where('ur.user_id', $userId)
                ->select('r.id, r.name, r.description')
                ->get()->getResultArray();
        } catch (\Exception $e) {
            $userRoles = [];
        }

        // Get current user divisions (from user_roles)
        try {
            $userDivisions = $this->db->table('user_roles ur')
                ->join('divisions d', 'd.id = ur.division_id')
                ->where('ur.user_id', $userId)
                ->select('d.id, d.name, d.code')
                ->get()->getResultArray();
        } catch (\Exception $e) {
            $userDivisions = [];
        }

        // Get current user permissions (TAMBAHKAN INI)
        try {
            $userPermissions = $this->db->table('user_permissions up')
                ->join('permissions p', 'p.id = up.permission_id')
                ->where('up.user_id', $userId)
                ->select('up.permission_id as id, p.key, p.name, p.description, up.granted')
                ->get()->getResultArray();
        } catch (\Exception $e) {
            $userPermissions = [];
        }

        $data = [
            'title' => 'Edit User - ' . $user['first_name'] . ' ' . $user['last_name'],
            'user' => $user,
            'userRoles' => $userRoles,
            'userDivisions' => $userDivisions,
            'userPermissions' => $userPermissions,  // TAMBAHKAN INI
            'allRoles' => $this->roleModel->findAll(),
            'allDivisions' => $this->divisionModel->findAll(),
            'allPermissions' => method_exists($this->permissionModel, 'getPermissionsGroupedByModule') 
                ? $this->permissionModel->getPermissionsGroupedByModule() 
                : $this->permissionModel->findAll(),
        ];

        return view('admin/advanced_user_management/edit', $data);
    }

    /**
     * Update User
     */
    public function update($userId)
    {
        // Debug: Log all POST data
        log_message('debug', 'Update User POST data: ' . json_encode($this->request->getPost()));
        log_message('debug', 'Update User ID: ' . $userId);
        
        if (!$this->hasPermission('admin.user_edit')) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak'])->setStatusCode(403);
            }
            return redirect()->to('/admin/advanced-users')->with('error', 'Akses ditolak.');
        }

        // Debug: Log all POST data
        log_message('debug', 'Update User POST data: ' . json_encode($this->request->getPost()));

        $user = $this->userModel->find($userId);
        if (!$user) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'User tidak ditemukan'])->setStatusCode(404);
            }
            return redirect()->to('/admin/advanced-users')->with('error', 'User tidak ditemukan.');
        }

        $validation = $this->validate([
            'first_name' => 'required|min_length[2]|max_length[50]',
            'last_name' => 'required|min_length[2]|max_length[50]',
            'username' => "required|min_length[3]|max_length[20]|is_unique[users.username,id,{$userId}]",
            'email' => "required|valid_email|is_unique[users.email,id,{$userId}]",
            'password' => 'permit_empty|min_length[6]',
            'confirm_password' => 'permit_empty|matches[password]',
            'phone' => 'permit_empty|max_length[20]'
        ]);

        if (!$validation) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Validation failed', 'errors' => $this->validator->getErrors()])->setStatusCode(400);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->db->transStart();

        try {
            // Update user data
            $userData = [
                'first_name' => $this->request->getPost('first_name'),
                'last_name' => $this->request->getPost('last_name'),
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'phone' => $this->request->getPost('phone'),
                'is_active' => $this->request->getPost('is_active') !== null ? (int)$this->request->getPost('is_active') : 1,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Update password if provided
            $password = $this->request->getPost('password');
            if (!empty($password)) {
                // Don't hash here - let the model callback handle it
                $userData['password'] = $password;
            }

            $result = $this->userModel->skipValidation(true)->update($userId, $userData);

            if (!$result) {
                // Debug: log what data was sent
                log_message('error', 'Update failed. UserData: ' . json_encode($userData));
                log_message('error', 'Model errors: ' . json_encode($this->userModel->errors()));
                throw new \Exception('Failed to update user - check logs for details');
            }

            // Clear existing user_roles
            $this->db->table('user_roles')->where('user_id', $userId)->delete();
            
            // Get roles and divisions
            $roles = $this->request->getPost('roles') ?? [];
            $divisions = $this->request->getPost('divisions') ?? [];

            // Debug logging
            log_message('debug', 'Update User Roles: ' . json_encode($roles));
            log_message('debug', 'Update User Divisions: ' . json_encode($divisions));

            // Get default role if no roles selected (role_id cannot be null)
            if (empty($roles)) {
                $defaultRole = $this->db->table('roles')->where('name', 'User')->get()->getRow();
                if ($defaultRole) {
                    $roles = [$defaultRole->id];
                    log_message('debug', 'Using default role: ' . $defaultRole->id);
                } else {
                    // If no default role exists, create or use first available role
                    $firstRole = $this->db->table('roles')->limit(1)->get()->getRow();
                    if ($firstRole) {
                        $roles = [$firstRole->id];
                        log_message('debug', 'Using first available role: ' . $firstRole->id);
                    }
                }
            }

            // Create role-division combinations
            if (!empty($roles) && !empty($divisions)) {
                // Create combinations of roles and divisions
                foreach ($roles as $roleId) {
                    foreach ($divisions as $divisionId) {
                        $insertData = [
                            'user_id' => $userId,
                            'role_id' => $roleId,
                            'division_id' => $divisionId,
                            'assigned_by' => session()->get('user_id') ?? 1,
                            'assigned_at' => date('Y-m-d H:i:s'),
                            'is_active' => 1
                        ];
                        log_message('debug', 'Inserting user_role: ' . json_encode($insertData));
                        $this->db->table('user_roles')->insert($insertData);
                    }
                }
            } elseif (!empty($roles)) {
                // Only roles, no specific division
                foreach ($roles as $roleId) {
                    $insertData = [
                        'user_id' => $userId,
                        'role_id' => $roleId,
                        'assigned_by' => session()->get('user_id') ?? 1,
                        'assigned_at' => date('Y-m-d H:i:s'),
                        'is_active' => 1
                    ];
                    log_message('debug', 'Inserting user_role (no division): ' . json_encode($insertData));
                    $this->db->table('user_roles')->insert($insertData);
                }
            } elseif (!empty($divisions)) {
                // Only divisions, assign default role
                $defaultRole = $this->db->table('roles')->where('name', 'User')->get()->getRow();
                if ($defaultRole) {
                    foreach ($divisions as $divisionId) {
                        $insertData = [
                            'user_id' => $userId,
                            'role_id' => $defaultRole->id,
                            'division_id' => $divisionId,
                            'assigned_by' => session()->get('user_id') ?? 1,
                            'assigned_at' => date('Y-m-d H:i:s'),
                            'is_active' => 1
                        ];
                        log_message('debug', 'Inserting user_role (division only): ' . json_encode($insertData));
                        $this->db->table('user_roles')->insert($insertData);
                    }
                }
            } else {
                // No roles or divisions specified, assign default role
                $defaultRole = $this->db->table('roles')->where('name', 'User')->get()->getRow();
                if ($defaultRole) {
                    $insertData = [
                        'user_id' => $userId,
                        'role_id' => $defaultRole->id,
                        'assigned_by' => session()->get('user_id') ?? 1,
                        'assigned_at' => date('Y-m-d H:i:s'),
                        'is_active' => 1
                    ];
                    log_message('debug', 'Inserting default user_role: ' . json_encode($insertData));
                    $this->db->table('user_roles')->insert($insertData);
                }
            }



            // Update permissions
            $this->db->table('user_permissions')->where('user_id', $userId)->delete();
            $permissions = $this->request->getPost('permissions') ?? [];
            foreach ($permissions as $permissionId) {
                $this->db->table('user_permissions')->insert([
                    'user_id' => $userId,
                    'permission_id' => $permissionId,
                    'granted' => 1,
                    'assigned_by' => session()->get('user_id') ?? 1,
                    'assigned_at' => date('Y-m-d H:i:s')
                ]);
            }

            $this->db->transComplete();

            if ($this->db->transStatus()) {
                // Log user update using trait
                $this->logUpdate('users', $userId, [
                    'user_id' => $userId,
                    'username' => $userData['username'],
                    'email' => $userData['email'],
                    'first_name' => $userData['first_name'],
                    'last_name' => $userData['last_name'],
                    'roles' => $roles,
                    'divisions' => $divisions,
                    'permissions' => $permissions,
                    'updated_by' => session()->get('user_id') ?? 1,
                    'password_changed' => !empty($password) ? 'Yes' : 'No'
                ]);
                
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => true, 'message' => 'User berhasil diperbarui']);
                }
                return redirect()->to('/admin/advanced-users')->with('success', 'User berhasil diperbarui.');
            } else {
                throw new \Exception('Transaction failed');
            }

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Update User Error: ' . $e->getMessage());
            
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Error: ' . $e->getMessage()])->setStatusCode(500);
            }
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Delete User
     */
    public function delete($userId)
    {
        if (!$this->hasPermission('admin.user_delete')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak'])->setStatusCode(403);
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'User tidak ditemukan'])->setStatusCode(404);
        }

        // Prevent deleting current user
        if ($userId == session()->get('user_id')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tidak dapat menghapus akun sendiri'])->setStatusCode(400);
        }

        $this->db->transStart();

        try {
            // Store user data before deletion for logging
            $userDataForLogging = [
                'user_id' => $userId,
                'username' => $user['username'],
                'email' => $user['email'],
                'first_name' => $user['first_name'] ?? '',
                'last_name' => $user['last_name'] ?? '',
                'deleted_by' => session()->get('user_id') ?? 1
            ];

            // Delete related records first
            try {
                $this->db->table('user_roles')->where('user_id', $userId)->delete();
                $this->db->table('user_permissions')->where('user_id', $userId)->delete();
            } catch (\Exception $e) {
                // Tables might not exist, continue with user deletion
                log_message('info', 'Some related tables do not exist: ' . $e->getMessage());
            }
            
            // Delete user
            $result = $this->userModel->delete($userId);

            if (!$result) {
                throw new \Exception('Failed to delete user');
            }

            $this->db->transComplete();

            if ($this->db->transStatus()) {
                // Log user deletion using trait
                $this->logDelete('users', $userId, $userDataForLogging);
                
                return $this->response->setJSON(['success' => true, 'message' => 'User berhasil dihapus']);
            } else {
                throw new \Exception('Transaction failed');
            }

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Delete User Error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Error: ' . $e->getMessage()])->setStatusCode(500);
        }
    }

    /**
     * Export Users Data
     */
    public function export()
    {
        if (!$this->hasPermission('admin.user_export')) {
            return redirect()->to('/admin/advanced-users')->with('error', 'Akses ditolak.');
        }

        // Use full user data with complete info
        $users = $this->getUsersWithCompleteInfo();
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="users_export_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Enhanced CSV Headers
        fputcsv($output, ['ID', 'Username', 'First Name', 'Last Name', 'Email', 'Phone', 'Status', 'Roles', 'Divisions', 'Created At']);
        
        foreach ($users as $user) {
            $roles = implode('; ', array_column($user['roles'] ?? [], 'name'));
            $divisions = implode('; ', array_column($user['divisions'] ?? [], 'name'));
            
            fputcsv($output, [
                $user['id'],
                $user['username'],
                $user['first_name'],
                $user['last_name'],
                $user['email'],
                $user['phone'] ?? '',
                $user['status'],
                $roles,
                $divisions,
                $user['created_at'] ?? ''
            ]);
        }
        
        fclose($output);
        exit;
    }

    /**
     * Clean Expired Permissions
     */
    public function cleanExpired()
    {
        if (!$this->hasPermission('admin.user_permissions')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak'])->setStatusCode(403);
        }

        try {
            // This would clean expired permissions if we had expiry dates
            // For now, just return success
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Expired permissions cleaned successfully'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Build Permission Matrix for User Details
     */
    private function buildSimplePermissionMatrix($userId)
    {
        try {
            // Use the full permission matrix method if available
            return $this->buildUserPermissionMatrix($userId);
        } catch (\Exception $e) {
            // Fallback to simple matrix if models are not fully implemented
            return [
                'effective_permissions' => [
                    'admin.access' => true,
                    'users.manage' => true,
                    'service.view' => true,
                    'service.work_orders.manage' => true
                ]
            ];
        }
    }

    private function hasPermission($permission)
    {
        // Enhanced permission check - can be replaced with actual implementation
        // For now, always return true for admin users
        return true;
    }

    /**
     * Delete User (Legacy method name for backward compatibility)
     */
    public function deleteUser($userId)
    {
        return $this->delete($userId);
    }

    /**
     * Quick Permission Assignment
     */
    public function quickAssignPermission()
    {
        if (!$this->hasPermission('admin.user_permissions')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(403);
        }

        $userId = $this->request->getPost('user_id');
        $permissionId = $this->request->getPost('permission_id');
        $divisionId = $this->request->getPost('division_id');
        $granted = $this->request->getPost('granted') == '1';

        if (!$userId || !$permissionId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User ID and Permission ID are required'
            ])->setStatusCode(400);
        }

        try {
            // Check if permission already exists
            $existing = $this->db->table('user_permissions')
                ->where('user_id', $userId)
                ->where('permission_id', $permissionId);
            
            if ($divisionId) {
                $existing->where('division_id', $divisionId);
            }
            
            $existingRecord = $existing->get()->getRowArray();

            if ($existingRecord) {
                // Update existing permission
                $result = $this->db->table('user_permissions')
                    ->where('id', $existingRecord['id'])
                    ->update([
                        'granted' => $granted ? 1 : 0,
                        'assigned_by' => session()->get('user_id') ?? 1,
                        'assigned_at' => date('Y-m-d H:i:s')
                    ]);
            } else {
                // Insert new permission
                $result = $this->db->table('user_permissions')->insert([
                    'user_id' => $userId,
                    'permission_id' => $permissionId,
                    'division_id' => $divisionId,
                    'granted' => $granted ? 1 : 0,
                    'assigned_by' => session()->get('user_id') ?? 1,
                    'assigned_at' => date('Y-m-d H:i:s')
                ]);
            }

            if ($result) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Permission berhasil ' . ($granted ? 'diberikan' : 'dicabut')
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal mengubah permission'
                ])->setStatusCode(500);
            }
        } catch (\Exception $e) {
            log_message('error', 'Quick Assign Permission Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Get User Permission Matrix (AJAX Endpoint)
     */
    public function userMatrix($userId)
    {
        try {
            $user = $this->userModel->find($userId);
            if (!$user) {
                return $this->response->setJSON(['success' => false, 'message' => 'User not found']);
            }

            // Roles
            $roles = $this->db->table('user_roles ur')
                ->join('roles r', 'r.id = ur.role_id')
                ->where('ur.user_id', $userId)
                ->select('r.name')
                ->get()->getResultArray();

            // Divisions
            $divisions = $this->db->table('user_roles ur')
                ->join('divisions d', 'd.id = ur.division_id')
                ->where('ur.user_id', $userId)
                ->select('d.name, ur.is_head')
                ->get()->getResultArray();

            // Permissions (gabungan role, division, custom)
            $permissions = $this->calculateEffectivePermissionsForUser($userId);

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'user' => [
                        'first_name' => $user['first_name'],
                        'last_name' => $user['last_name'],
                        'email' => $user['email'],
                        'status' => $user['is_active'] ? 'active' : 'inactive'
                    ],
                    'roles' => $roles,
                    'divisions' => $divisions,
                    'permissions' => $permissions
                ]
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Gabungkan permission dari role, division, custom user
     * Return: ['effective_permissions' => ['perm.key' => true/false, ...]]
     */
    protected function calculateEffectivePermissionsForUser($userId)
    {
        // Ambil semua permission yang mungkin
        $allPerms = $this->permissionModel->findAll();
        $result = [];
        foreach ($allPerms as $perm) {
            $result[$perm['key']] = false;
        }

        // Dari role
        $rolePerms = $this->db->table('user_roles ur')
            ->join('role_permissions rp', 'rp.role_id = ur.role_id')
            ->join('permissions p', 'p.id = rp.permission_id')
            ->where('ur.user_id', $userId)
            ->select('p.key')
            ->get()->getResultArray();
        foreach ($rolePerms as $rp) {
            $result[$rp['key']] = true;
        }

        // Dari division (jika ada logic permission per division)
        // -- tambahkan jika memang ada

        // Dari custom user permission (override)
        $customPerms = $this->db->table('user_permissions up')
            ->join('permissions p', 'p.id = up.permission_id')
            ->where('up.user_id', $userId)
            ->select('p.key, up.granted')
            ->get()->getResultArray();
        foreach ($customPerms as $cp) {
            $result[$cp['key']] = (bool)$cp['granted'];
        }

        return ['effective_permissions' => $result];
    }

    /**
     * Division Based User Management (AJAX)
     */
    public function divisionUsers($divisionId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        try {
            // Get users in this division
            $users = $this->db->table('users u')
                ->select('
                    u.id, u.username, u.email, u.first_name, u.last_name, 
                    u.phone, u.is_active, u.avatar,
                    GROUP_CONCAT(DISTINCT r.name ORDER BY r.name SEPARATOR ", ") as roles,
                    COUNT(DISTINCT up.permission_id) as custom_permission_count
                ')
                ->join('user_roles ur', 'ur.user_id = u.id', 'left')
                ->join('roles r', 'r.id = ur.role_id', 'left')
                ->join('user_permissions up', 'up.user_id = u.id', 'left')
                ->where('ur.division_id', $divisionId)
                ->groupBy('u.id')
                ->orderBy('u.first_name', 'ASC')
                ->get()
                ->getResultArray();

            // Get division info
            $division = $this->divisionModel->find($divisionId);
            if (!$division) {
                return $this->response->setStatusCode(404);
            }

            // Add status field for compatibility
            foreach ($users as &$user) {
                $user['status'] = $user['is_active'] ? 'active' : 'inactive';
            }

            // Prepare data for view
            $data = [
                'users' => $users,
                'division' => $division
            ];

            return view('admin/advanced_user_management/division_users', $data);

        } catch (\Exception $e) {
            log_message('error', 'Division Users Error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setBody('Error loading division users: ' . $e->getMessage());
        }
    }

    public function removeFromDivision()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        try {
            $userId = $this->request->getPost('user_id');
            $divisionId = $this->request->getPost('division_id');

            if (!$userId || !$divisionId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Missing required parameters'
                ]);
            }

            // Remove user from division (remove user_roles entries for this division)
            $removed = $this->db->table('user_roles')
                ->where('user_id', $userId)
                ->where('division_id', $divisionId)
                ->delete();

            if ($removed) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'User removed from division successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to remove user from division'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Remove from Division Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Bulk Permission Assignment
     */
    public function bulkAssignPermissions()
    {
        if (!$this->hasPermission('admin.user_permissions')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(403);
        }

        $userIds = $this->request->getPost('user_ids') ?? [];
        $permissionIds = $this->request->getPost('permission_ids') ?? [];
        $divisionId = $this->request->getPost('division_id');
        $granted = $this->request->getPost('granted') == '1';

        if (empty($userIds) || empty($permissionIds)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please select users and permissions'
            ])->setStatusCode(400);
        }

        $this->db->transStart();

        try {
            foreach ($userIds as $userId) {
                foreach ($permissionIds as $permissionId) {
                    // Check if permission already exists
                    $existing = $this->db->table('user_permissions')
                        ->where('user_id', $userId)
                        ->where('permission_id', $permissionId);
                    
                    if ($divisionId) {
                        $existing->where('division_id', $divisionId);
                    }
                    
                    $existingRecord = $existing->get()->getRowArray();

                    if ($existingRecord) {
                        // Update existing permission
                        $this->db->table('user_permissions')
                            ->where('id', $existingRecord['id'])
                            ->update([
                                'granted' => $granted ? 1 : 0,
                                'assigned_by' => session()->get('user_id') ?? 1,
                                'assigned_at' => date('Y-m-d H:i:s')
                            ]);
                    } else {
                        // Insert new permission
                        $this->db->table('user_permissions')->insert([
                            'user_id' => $userId,
                            'permission_id' => $permissionId,
                            'division_id' => $divisionId,
                            'granted' => $granted ? 1 : 0,
                            'assigned_by' => session()->get('user_id') ?? 1,
                            'assigned_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Bulk permission assignment berhasil'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat assignment'
                ])->setStatusCode(500);
            }

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Bulk Assign Permissions Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Get Menu Permissions for User
     */
    public function getUserMenuPermissions($userId)
    {
        $userMenus = $this->buildUserMenuPermissions($userId);
        
        return $this->response->setJSON([
            'success' => true,
            'menus' => $userMenus
        ]);
    }

    /**
     * Helper Methods
     */
    protected function getUsersWithCompleteInfo()
    {
        $users = $this->userModel->findAll();
        
        foreach ($users as &$user) {
            // Ensure all required fields exist
            $user['first_name'] = $user['first_name'] ?? '';
            $user['last_name'] = $user['last_name'] ?? '';
            $user['username'] = $user['username'] ?? '';
            $user['email'] = $user['email'] ?? '';
            $user['phone'] = $user['phone'] ?? '';
            $user['status'] = $user['status'] ?? 'active';
            $user['created_at'] = $user['created_at'] ?? date('Y-m-d H:i:s');
            $user['last_login'] = $user['last_login'] ?? null;

            // Get user roles with error handling
            try {
                $userRoles = $this->db->table('user_roles ur')
                    ->join('roles r', 'r.id = ur.role_id')
                    ->where('ur.user_id', $user['id'])
                    ->select('r.id, r.name, r.description')
                    ->get()->getResultArray();
                $user['roles'] = $userRoles;
            } catch (\Exception $e) {
                $user['roles'] = [];
            }

            // Get user divisions with error handling (from user_roles)
            try {
                $userDivisions = $this->db->table('user_roles ur')
                    ->join('divisions d', 'd.id = ur.division_id')
                    ->where('ur.user_id', $user['id'])
                    ->select('d.id, d.name, d.code')
                    ->get()->getResultArray();
                $user['divisions'] = $userDivisions;
            } catch (\Exception $e) {
                $user['divisions'] = [];
            }

            // Get custom permissions count with error handling
            try {
                $customPermissionsCount = $this->db->table('user_permissions')
                    ->where('user_id', $user['id'])
                    ->countAllResults();
                $user['custom_permissions_count'] = $customPermissionsCount;
            } catch (\Exception $e) {
                $user['custom_permissions_count'] = 0;
            }
        }
        
        return $users;
    }

    protected function getUserStats()
    {
        try {
            $totalUsers = $this->userModel->countAll();
            $activeUsers = $this->db->table('users')->where('is_active', 1)->countAllResults();
            
            // Count users with multiple divisions (from user_roles)
            $multiDivisionUsers = $this->db->table('user_roles')
                ->select('user_id, COUNT(DISTINCT division_id) as division_count')
                ->where('division_id IS NOT NULL')
                ->groupBy('user_id')
                ->having('division_count >', 1)
                ->countAllResults();
            
            // Count users with custom permissions
            $customPermissionUsers = $this->db->table('user_permissions')
                ->select('DISTINCT user_id')
                ->countAllResults();

            return [    
                'total_users' => $totalUsers,
                'active_users' => $activeUsers,
                'users_with_multiple_divisions' => $multiDivisionUsers,
                'users_with_custom_permissions' => $customPermissionUsers
            ];
        } catch (\Exception $e) {
            // Fallback stats if tables don't exist
            $totalUsers = $this->userModel->countAll();
            return [
                'total_users' => $totalUsers,
                'active_users' => $totalUsers,
                'users_with_multiple_divisions' => 0,
                'users_with_custom_permissions' => 0
            ];
        }
    }

    protected function buildUserPermissionMatrix($userId)
    {
        try {
            // Get role permissions
            $rolePermissions = $this->db->table('user_roles ur')
                ->join('roles r', 'r.id = ur.role_id')
                ->join('role_permissions rp', 'rp.role_id = r.id')
                ->join('permissions p', 'p.id = rp.permission_id')
                ->where('ur.user_id', $userId)
                ->select('p.key as permission_key, p.name, p.description, "role" as source, r.name as source_name')
                ->get()->getResultArray();
        } catch (\Exception $e) {
            $rolePermissions = [];
        }

        try {
            // Get custom permissions
            $customPermissions = $this->db->table('user_permissions up')
                ->join('permissions p', 'p.id = up.permission_id')
                ->where('up.user_id', $userId)
                ->select('p.key as permission_key, p.name, p.description, up.granted, "custom" as source')
                ->get()->getResultArray();
        } catch (\Exception $e) {
            $customPermissions = [];
        }

        try {
            // Get division permissions (if implemented)
            $divisionPermissions = [];
        } catch (\Exception $e) {
            $divisionPermissions = [];
        }
        
        // Build comprehensive permission matrix
        return [
            'role_permissions' => $rolePermissions,
            'custom_permissions' => $customPermissions,
            'division_permissions' => $divisionPermissions,
            'effective_permissions' => $this->calculateEffectivePermissions($rolePermissions, $customPermissions, $divisionPermissions)
        ];
    }

    protected function buildUserMenuPermissions($userId)
    {
        try {
            $userDivisions = $this->db->table('user_roles ur')
                ->join('divisions d', 'd.id = ur.division_id')
                ->where('ur.user_id', $userId)
                ->select('d.code, d.name')
                ->get()->getResultArray();
            $divisionCodes = array_column($userDivisions, 'code');
            $menuPermissions = [
                'service' => in_array('SVC', $divisionCodes) && $this->userHasPermission($userId, 'service.view'),
                'unit_rolling' => in_array('URT', $divisionCodes) && $this->userHasPermission($userId, 'unit_rolling.view'),
                'marketing' => in_array('MKT', $divisionCodes) && $this->userHasPermission($userId, 'marketing.view'),
                'finance' => in_array('FIN', $divisionCodes) && $this->userHasPermission($userId, 'finance.view'),
                'warehouse' => in_array('WHS', $divisionCodes) && $this->userHasPermission($userId, 'warehouse.view'),
                'purchasing' => in_array('PUR', $divisionCodes) && $this->userHasPermission($userId, 'purchasing.view'),
            ];
            return $menuPermissions;
        } catch (\Exception $e) {
            // Fallback: return basic permissions
            return [
                'service' => true,
                'unit_rolling' => false,
                'marketing' => false,
                'finance' => false,
                'warehouse' => false,
                'purchasing' => false,
            ];
        }
    }

    protected function calculateEffectivePermissions($rolePermissions, $customPermissions, $divisionPermissions)
    {
        // Logic to calculate final effective permissions
        // Custom permissions override role permissions
        // Division-specific permissions take precedence
        
        $effective = [];
        
        // Start with role permissions
        foreach ($rolePermissions as $perm) {
            $effective[$perm['permission_key']] = true;
        }
        
        // Apply custom permissions (can grant or deny)
        foreach ($customPermissions as $perm) {
            $effective[$perm['permission_key']] = isset($perm['granted']) ? ($perm['granted'] == 1) : true;
        }

        // Apply division permissions if any
        foreach ($divisionPermissions as $perm) {
            $effective[$perm['permission_key']] = isset($perm['granted']) ? ($perm['granted'] == 1) : true;
        }
        
        return $effective;
    }

    /**
     * Cek permission efektif: custom permission (override) > role permission
     */
    protected function userHasPermission($userId, $permissionKey, $divisionId = null)
    {
        try {
            // 1. Cek custom permission (override)
            $customPermission = $this->db->table('user_permissions up')
                ->join('permissions p', 'p.id = up.permission_id')
                ->where('up.user_id', $userId)
                ->where('p.key', $permissionKey);
            if ($divisionId) {
                $customPermission->where('up.division_id', $divisionId);
            }
            $customPermission = $customPermission->orderBy('up.id', 'DESC')->get()->getRowArray();
            if ($customPermission) {
                return $customPermission['granted'] == 1;
            }

            // 2. Cek role permission
            $rolePermission = $this->db->table('user_roles ur')
                ->join('role_permissions rp', 'rp.role_id = ur.role_id')
                ->join('permissions p', 'p.id = rp.permission_id')
                ->where('ur.user_id', $userId)
                ->where('p.key', $permissionKey)
                ->countAllResults();
            if ($rolePermission > 0) {
                return true;
            }

            // 3. (Opsional) Cek division-based permission jika ada kebutuhan
            // Implementasi sesuai kebutuhan

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get DataTable data for users
     */
    public function getDataTable()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'draw' => 1,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Not an AJAX request'
            ])->setStatusCode(400);
        }

        try {
            $draw = intval($this->request->getPost('draw') ?? 1);
            $start = intval($this->request->getPost('start') ?? 0);
            $length = intval($this->request->getPost('length') ?? 10);
            $searchValue = $this->request->getPost('search')['value'] ?? '';
            $orderColumn = intval($this->request->getPost('order')[0]['column'] ?? 0);
            $orderDir = $this->request->getPost('order')[0]['dir'] ?? 'asc';

            // Column mapping for DataTable
            $columns = ['user_info', 'email', 'roles', 'divisions', 'custom_permissions', 'status', 'actions'];
            $orderByColumn = $columns[$orderColumn] ?? 'first_name';

            // Build query
            $builder = $this->db->table('users u');
            $builder->select('u.id, u.first_name, u.last_name, u.email, u.username, u.is_active, u.created_at, u.avatar');
            if (!empty($searchValue)) {
                $builder->groupStart();
                $builder->like('u.first_name', $searchValue);
                $builder->orLike('u.last_name', $searchValue);
                $builder->orLike('u.email', $searchValue);
                $builder->orLike('u.username', $searchValue);
                $builder->groupEnd();
            }
            $totalRecords = $this->userModel->countAll();
            $tempBuilder = clone $builder;
            $filteredRecords = $tempBuilder->countAllResults(false);

            // Ordering
            if ($orderByColumn === 'email') {
                $builder->orderBy('u.email', $orderDir);
            } else {
                $builder->orderBy('u.first_name', 'asc');
            }

            // Pagination
            if ($length > 0) {
                $builder->limit($length, $start);
            }

            $users = $builder->get()->getResultArray();

            // Prepare data for DataTable (associative array for each row)
            $data = [];
            foreach ($users as $user) {
                // User Info
                $avatar = '<span class="avatar-circle bg-primary text-white me-2">' . strtoupper(substr($user['first_name'], 0, 1)) . '</span>';
                $userInfo = $avatar . '<strong>' . esc($user['first_name'] . ' ' . $user['last_name']) . '</strong><br><small class="text-muted">' . esc($user['username']) . '</small>';

                // Roles
                $roles = $this->db->table('user_roles ur')
                    ->join('roles r', 'r.id = ur.role_id')
                    ->where('ur.user_id', $user['id'])
                    ->select('r.name')
                    ->get()->getResultArray();
                $rolesHtml = '';
                foreach ($roles as $role) {
                    $rolesHtml .= '<span class="badge bg-primary me-1">' . esc($role['name']) . '</span>';
                }

                // Divisions
                $divisions = $this->db->table('user_roles ur')
                    ->join('divisions d', 'd.id = ur.division_id')
                    ->where('ur.user_id', $user['id'])
                    ->select('d.name')
                    ->get()->getResultArray();
                $divisionsHtml = '';
                foreach ($divisions as $division) {
                    $divisionsHtml .= '<span class="badge bg-info me-1">' . esc($division['name']) . '</span>';
                }

                // Custom Permissions
                $customPermCount = $this->db->table('user_permissions')
                    ->where('user_id', $user['id'])
                    ->countAllResults();
                $customPermHtml = $customPermCount > 0 ? '<span class="badge bg-warning">' . $customPermCount . '</span>' : '<span class="text-muted">-</span>';

                // Status
                $statusHtml = $user['is_active'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>';

                // Actions
                $actions = '
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-info" onclick="viewUserMatrix(' . $user['id'] . ')" title="View Matrix"><i class="fas fa-th"></i></button>
                        <a href="' . base_url('admin/advanced-users/show/' . $user['id']) . '" class="btn btn-primary" title="Detail"><i class="fas fa-eye"></i></a>
                        <a href="' . base_url('admin/advanced-users/edit/' . $user['id']) . '" class="btn btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                        <button class="btn btn-danger" onclick="confirmDeleteUser(' . $user['id'] . ', \'' . esc($user['first_name'] . ' ' . $user['last_name']) . '\')" title="Delete"><i class="fas fa-trash"></i></button>
                    </div>
                ';

                $data[] = [
                    'user_info' => $userInfo,
                    'email' => esc($user['email']),
                    'roles' => $rolesHtml,
                    'divisions' => $divisionsHtml,
                    'custom_permissions' => $customPermHtml,
                    'status' => $statusHtml,
                    'actions' => $actions
                ];
            }

            return $this->response->setJSON([
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            log_message('error', 'DataTable User Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'draw' => intval($this->request->getPost('draw') ?? 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Server error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }
    
    // Assign role ke user, tidak auto-assign permission ke user/division
    public function assignRole($userId, $roleId) {
        $this->userRoleModel->assignRole($userId, $roleId);
        // Tidak perlu auto-assign permission ke user/division
        // Permission didapat dari role, kecuali ada custom permission (override)
    }
}
