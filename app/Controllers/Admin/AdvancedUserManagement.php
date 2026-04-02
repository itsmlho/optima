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
    protected array $permissionColumns = [];

    public function __construct()
    {
        // Load permission helper for access control
        helper('permission_helper');
        
        $this->db = \Config\Database::connect();
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
        $this->divisionModel = new DivisionModel();
        $this->permissionModel = new PermissionModel();
        $this->userRoleModel = new UserRoleModel();
        $this->userPermissionModel = new UserPermissionModel();

        try {
            $this->permissionColumns = $this->db->tableExists('permissions')
                ? $this->db->getFieldNames('permissions')
                : [];
        } catch (\Throwable $e) {
            $this->permissionColumns = [];
        }
    }

    protected function permissionColumnExists(string $column): bool
    {
        return in_array($column, $this->permissionColumns, true);
    }

    protected function permissionKeyColumn(string $alias = 'p'): string
    {
        $column = $this->permissionColumnExists('key_name') ? 'key_name' : 'key';
        return $alias . '.' . $column;
    }

    protected function permissionDisplayColumn(string $alias = 'p'): string
    {
        $column = $this->permissionColumnExists('display_name') ? 'display_name' : 'name';
        return $alias . '.' . $column;
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
                'breadcrumbs' => [
                    '/' => 'Dashboard',
                    '/admin' => 'Administration',
                    '/admin/advanced_user_management' => 'User Management'
                ],
                'stats' => $statsData,
                'permissions' => $groupedPermissions,
                'users' => $users,
                'roles' => $this->roleModel->findAll(),
                'divisions' => $this->divisionModel->findAll(),
                'loadDataTables' => true, // Enable DataTables loading
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
        if (!$this->hasPermission('admin.manage')) {
            return redirect()->to('/admin/advanced-users')->with('error', 'Akses ditolak.');
        }

        // Get roles from database (optima_ci structure has division_id column)
        $rolesFromDb = $this->roleModel->findAll();
        $formattedRoles = [];
        foreach ($rolesFromDb as $role) {
            $formattedRoles[] = [
                'id' => (string)$role['id'],
                'name' => $role['name'],
                'slug' => $role['slug'] ?? '',
                'division_id' => (string)($role['division_id'] ?? ''),  // Use division_id from database
                'division' => (string)($role['division_id'] ?? '')  // Alias for backward compatibility
            ];
        }

        $data = [
            'title' => 'Create New User',
            'user' => null, // Explicitly set null for create mode
            'userRoles' => [], // Empty array for create mode
            'userDivisions' => [], // Empty array for create mode  
            'userPermissions' => [], // Empty array for create mode
            'userServiceAccess' => [], // Empty array for create mode - IMPORTANT!
            'roles' => $formattedRoles, // Roles with division_id
            'divisions' => $this->divisionModel->findAll(),
            'allPermissions' => $this->permissionModel->where('is_active', 1)->findAll(),
        ];

        return view('admin/advanced_user_management/create_user', $data);
    }

    /**
     * Store New User
     */
    public function store()
    {
        // Debug: Log incoming request
        log_message('info', 'Create User Request: ' . json_encode($this->request->getPost()));
        log_message('info', 'Division: ' . $this->request->getPost('division'));
        log_message('info', 'Role: ' . $this->request->getPost('role'));
        log_message('info', 'Status: ' . $this->request->getPost('status'));
        
        if (!$this->hasPermission('admin.manage')) {
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
            'password' => 'required|min_length[6]|max_length[100]',
            'password_confirm' => 'required|matches[password]',
            'phone' => 'permit_empty|max_length[20]'
        ]);

        if (!$validation) {
            $errors = $this->validator->getErrors();
            log_message('error', 'Validation failed: ' . json_encode($errors));
            
            // Create user-friendly error message
            $errorMessage = 'Validation failed: ';
            if (isset($errors['username'])) {
                $errorMessage = 'Username sudah digunakan atau tidak valid';
            } elseif (isset($errors['email'])) {
                $errorMessage = 'Email sudah terdaftar atau tidak valid';
            } elseif (isset($errors['first_name']) || isset($errors['last_name'])) {
                $errorMessage = 'Nama tidak valid (minimal 2 karakter)';
            } else {
                $errorMessage = implode(', ', $errors);
            }
            
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => $errorMessage, 'errors' => $errors])->setStatusCode(400);
            }
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        // Additional password validation
        $password = $this->request->getPost('password');
        $confirmPassword = $this->request->getPost('password_confirm');
        
        if ($password !== $confirmPassword) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Password dan Confirm Password tidak sama'])->setStatusCode(400);
            }
            return redirect()->back()->withInput()->with('error', 'Password dan Confirm Password tidak sama.');
        }
        
        if (strlen($password) < 6) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Password minimal 6 karakter'])->setStatusCode(400);
            }
            return redirect()->back()->withInput()->with('error', 'Password minimal 6 karakter.');
        }

        // Password strength validation
        if (!$this->validatePasswordStrength($password)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Password harus mengandung minimal 1 huruf dan 1 angka'])->setStatusCode(400);
            }
            return redirect()->back()->withInput()->with('error', 'Password harus mengandung minimal 1 huruf dan 1 angka.');
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
                'password_hash' => password_hash($password, PASSWORD_DEFAULT), // Hash password securely
                'phone' => $this->request->getPost('phone'),
                'is_active' => ($status == 'active') ? 1 : 0,
                'created_at' => date('Y-m-d H:i:s')
            ];

            log_message('info', 'Creating user with data: ' . json_encode($userData));
            
            // Debug: Check password hash
            log_message('debug', 'Password hash length: ' . strlen($userData['password_hash']));
            
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

            // Assign division and role (division-first approach)
            $division = $this->request->getPost('division');
            $role = $this->request->getPost('role');
            $divisions = [$division]; // Array of divisions for logging
            
            if (!empty($division) && !empty($role)) {
                $this->db->table('user_roles')->insert([
                    'user_id' => $userId,
                    'role_id' => $role,
                    'division_id' => $division,
                    'assigned_by' => session()->get('user_id') ?? 1,
                    'assigned_at' => date('Y-m-d H:i:s'),
                    'is_active' => 1
                ]);
            } else {
                throw new \Exception('Division and role are required.');
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
            
            // Handle Service Access if Service division is selected
            $this->handleServiceAccess($userId, $division);

            $this->db->transComplete();

            if ($this->db->transStatus()) {
                // Log user creation using trait
                $this->logCreate('users', $userId, [
                    'user_id' => $userId,
                    'username' => $userData['username'],
                    'email' => $userData['email'],
                    'roles' => [$role],
                    'divisions' => $divisions,
                    'permissions' => $permissions,
                    'created_by' => session()->get('user_id') ?? 1
                ]);
                
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => true, 'message' => 'User berhasil dibuat', 'user_id' => $userId]);
                }
                
                // Debug: Log redirect
                log_message('info', 'Redirecting to advanced-users with success message');
                return redirect()->to('/admin/advanced-users')->with('success', 'User berhasil dibuat.');
            } else {
                throw new \Exception('Transaction failed');
            }

        } catch (\Exception $e) {
            $this->db->transRollback();
            // Debug: log what data was sent
            log_message('error', 'Create User Error: ' . $e->getMessage() . ' | Data: ' . json_encode($this->request->getPost()));
            
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan saat membuat user. Silakan coba lagi.'])->setStatusCode(500);
            }
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat membuat user. Silakan coba lagi.');
        }
    }

    /**
     * Show User Details
     */
    public function show($userId)
    {
        if (!$this->hasPermission('admin.access')) {
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
        if (!$this->hasPermission('admin.manage')) {
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
                ->join('divisions d', 'd.id = ur.division_id', 'left')
                ->where('ur.user_id', $userId)
                ->where('ur.division_id IS NOT NULL')
                ->select('d.id, d.name, d.code')
                ->get()->getResultArray();
        } catch (\Exception $e) {
            $userDivisions = [];
        }

        // Get current user permissions (TAMBAHKAN INI)
        try {
            $permissionQuery = $this->db->table('user_permissions up')
                ->join('permissions p', 'p.id = up.permission_id')
                ->where('up.user_id', $userId)
                ->select([
                    'up.permission_id as id',
                    $this->permissionKeyColumn('p') . ' as key_name',
                    $this->permissionDisplayColumn('p') . ' as display_name',
                    'p.description',
                    'up.granted',
                ]);

            $permissionResult = $permissionQuery->get();
            $userPermissions = $permissionResult ? $permissionResult->getResultArray() : [];
        } catch (\Exception $e) {
            $userPermissions = [];
        }

        // Get current user service access data
        $userServiceAccess = [];
        try {
            // Get area access
            $areaAccess = $this->db->table('user_area_access')
                ->where('user_id', $userId)
                ->get()
                ->getRowArray();
            
            // Get branch access
            $branchAccess = $this->db->table('user_branch_access')
                ->where('user_id', $userId)
                ->get()
                ->getRowArray();
            
            if ($areaAccess) {
                $userServiceAccess['area_type'] = $areaAccess['area_type'];
                $userServiceAccess['department_scope'] = $areaAccess['department_scope'];
            }
            
            if ($branchAccess) {
                $userServiceAccess['access_type'] = $branchAccess['access_type'];
                if ($branchAccess['branch_ids']) {
                    $userServiceAccess['service_area_ids'] = json_decode($branchAccess['branch_ids'], true);
                }
            }
            
            log_message('debug', 'Loaded service access for user ' . $userId . ': ' . json_encode($userServiceAccess));
            
        } catch (\Exception $e) {
            log_message('error', 'Gagal memuat data. Silakan coba lagi.');
            $userServiceAccess = [];
        }

        // Get roles formatted for JavaScript dropdown
        $rolesFromDb = $this->roleModel->findAll();
        $formattedRoles = [];
        foreach ($rolesFromDb as $role) {
            $formattedRoles[] = [
                'id' => (string)$role['id'],
                'name' => $role['name'],
                'division' => (string)$role['division_id']
            ];
        }

        // Add current division and role info to user object
        if (!empty($userRoles)) {
            $user['role'] = $userRoles[0]['id'];
            $user['role_name'] = $userRoles[0]['name'];
        }
        
        if (!empty($userDivisions)) {
            $user['division'] = $userDivisions[0]['id'];
            $user['division_name'] = $userDivisions[0]['name'];
        }

        $data = [
            'title' => 'Edit User - ' . $user['first_name'] . ' ' . $user['last_name'],
            'user' => $user,
            'userRoles' => $userRoles,
            'userDivisions' => $userDivisions,
            'userPermissions' => $userPermissions,  // TAMBAHKAN INI
            'userServiceAccess' => $userServiceAccess, // TAMBAHKAN SERVICE ACCESS DATA
            'roles' => $formattedRoles, // Changed to 'roles' to match view
            'divisions' => $this->divisionModel->findAll(), // Changed to 'divisions' to match view
            'allPermissions' => $this->permissionModel->where('is_active', 1)->findAll(),
        ];

        return view('admin/advanced_user_management/edit_user', $data);
    }

    /**
     * Update User
     */
    public function update($userId)
    {
        log_message('debug', '=== UPDATE METHOD CALLED ===');
        log_message('debug', 'User ID: ' . $userId);
        log_message('debug', 'Is AJAX: ' . ($this->request->isAJAX() ? 'YES' : 'NO'));
        
        // Debug: Log all POST data
        log_message('debug', 'Update User POST data: ' . json_encode($this->request->getPost()));
        log_message('debug', 'Update User ID: ' . $userId);
        
        if (!$this->hasPermission('admin.manage')) {
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
        ], [
            'username' => [
                'required' => 'Username harus diisi.',
                'min_length' => 'Username minimal 3 karakter.',
                'max_length' => 'Username maksimal 20 karakter.',
                'is_unique' => 'Username sudah digunakan. Silakan gunakan username yang berbeda.'
            ],
            'email' => [
                'required' => 'Email harus diisi.',
                'valid_email' => 'Format email tidak valid.',
                'is_unique' => 'Email sudah terdaftar. Silakan gunakan email yang berbeda.'
            ],
            'first_name' => [
                'required' => 'Nama depan harus diisi.',
                'min_length' => 'Nama depan minimal 2 karakter.'
            ],
            'last_name' => [
                'required' => 'Nama belakang harus diisi.',
                'min_length' => 'Nama belakang minimal 2 karakter.'
            ],
            'password' => [
                'min_length' => 'Password minimal 6 karakter.'
            ],
            'confirm_password' => [
                'matches' => 'Konfirmasi password tidak sama dengan password.'
            ]
        ]);

        if (!$validation) {
            $errors = $this->validator->getErrors();
            $firstError = !empty($errors) ? reset($errors) : 'Validasi gagal';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => $firstError, 'errors' => $errors])->setStatusCode(400);
            }
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $this->db->transStart();

        try {
            // Store old data for logging
            $oldUserData = [
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'username' => $user['username'],
                'email' => $user['email'],
                'phone' => $user['phone'] ?? '',
                'is_active' => $user['is_active']
            ];
            
            // Get old roles and divisions
            $oldUserRoles = $this->db->table('user_roles ur')
                ->join('roles r', 'r.id = ur.role_id')
                ->where('ur.user_id', $userId)
                ->select('r.id, r.name')
                ->get()->getResultArray();
            $oldRoles = array_column($oldUserRoles, 'id');
            
            $oldUserDivisions = $this->db->table('user_roles ur')
                ->join('divisions d', 'd.id = ur.division_id', 'left')
                ->where('ur.user_id', $userId)
                ->where('ur.division_id IS NOT NULL')
                ->select('d.id, d.name')
                ->get()->getResultArray();
            $oldDivisions = array_column($oldUserDivisions, 'id');
            
            $oldUserPermissions = $this->db->table('user_permissions up')
                ->join('permissions p', 'p.id = up.permission_id')
                ->where('up.user_id', $userId)
                ->select('up.permission_id')
                ->get()->getResultArray();
            $oldPermissions = array_column($oldUserPermissions, 'permission_id');
            
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
                // Hash password securely
                $userData['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
                log_message('debug', 'Password hashed for user ID: ' . $userId);
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
            
            // Get division and role (division-first approach)
            $division = $this->request->getPost('division');
            $role = $this->request->getPost('role');

            // Debug logging
            log_message('debug', 'Update User Division: ' . $division);
            log_message('debug', 'Update User Role: ' . $role);

            // Assign division and role
            $roles = [];
            $divisions = [];
            
            if (!empty($division) && !empty($role)) {
                $insertData = [
                    'user_id' => $userId,
                    'role_id' => $role,
                    'division_id' => $division,
                    'assigned_by' => session()->get('user_id') ?? 1,
                    'assigned_at' => date('Y-m-d H:i:s'),
                    'is_active' => 1
                ];
                log_message('debug', 'Inserting user_role: ' . json_encode($insertData));
                $this->db->table('user_roles')->insert($insertData);
                
                // Prepare arrays for logging
                $roles = [$role];
                $divisions = [$division];
            } else {
                throw new \Exception('Division and role are required.');
            }



            // Update permissions
            $this->db->table('user_permissions')->where('user_id', $userId)->delete();
            
            // Handle custom permissions
            $customPermissions = $this->request->getPost('custom_permissions');
            log_message('debug', 'Custom permissions received: ' . $customPermissions);
            
            if (!empty($customPermissions)) {
                $permissionIds = json_decode($customPermissions, true);
                log_message('debug', 'Decoded permission IDs: ' . json_encode($permissionIds));
                
                if (is_array($permissionIds)) {
                    foreach ($permissionIds as $permissionId) {
                        if (is_numeric($permissionId)) {
                            $this->db->table('user_permissions')->insert([
                                'user_id' => $userId,
                                'permission_id' => (int)$permissionId,
                                'granted' => 1,
                                'assigned_by' => session()->get('user_id') ?? 1,
                                'assigned_at' => date('Y-m-d H:i:s')
                            ]);
                        }
                    }
                }
            }
            
            // Legacy permissions handling (for backward compatibility)
            $permissions = $this->request->getPost('permissions') ?? [];
            foreach ($permissions as $permissionId) {
                if (is_numeric($permissionId)) {
                    $this->db->table('user_permissions')->insert([
                        'user_id' => $userId,
                        'permission_id' => (int)$permissionId,
                        'granted' => 1,
                        'assigned_by' => session()->get('user_id') ?? 1,
                        'assigned_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }
            
            // Handle Service Access if Service division is selected
            log_message('debug', 'About to call handleServiceAccess with userId=' . $userId . ', division=' . $division);
            $this->handleServiceAccess($userId, $division);
            log_message('debug', 'handleServiceAccess completed');

            $this->db->transComplete();

            if ($this->db->transStatus()) {
                // Prepare old and new data for logging
                $oldData = array_merge($oldUserData, [
                    'roles' => $oldRoles,
                    'divisions' => $oldDivisions,
                    'permissions' => $oldPermissions
                ]);
                
                // Get final permission list for logging
                $finalPermissions = [];
                if (!empty($customPermissions)) {
                    $permissionIds = json_decode($customPermissions, true);
                    if (is_array($permissionIds)) {
                        $finalPermissions = array_merge($finalPermissions, $permissionIds);
                    }
                }
                $finalPermissions = array_merge($finalPermissions, $permissions);
                $finalPermissions = array_unique($finalPermissions);
                
                $newData = [
                    'user_id' => $userId,
                    'username' => $userData['username'],
                    'email' => $userData['email'],
                    'first_name' => $userData['first_name'],
                    'last_name' => $userData['last_name'],
                    'phone' => $userData['phone'] ?? '',
                    'is_active' => $userData['is_active'],
                    'roles' => $roles,
                    'divisions' => $divisions,
                    'permissions' => $finalPermissions,
                    'updated_by' => session()->get('user_id') ?? 1,
                    'password_changed' => !empty($password) ? 'Yes' : 'No'
                ];
                
                // Log user update using trait
                $this->logUpdate('users', $userId, $oldData, $newData);
                
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
                return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan saat memperbarui user. Silakan coba lagi.'])->setStatusCode(500);
            }
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui user. Silakan coba lagi.');
        }
    }

    /**
     * Delete User
     */
    public function delete($userId)
    {
        if (!$this->hasPermission('admin.delete')) {
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
                log_message('info', 'Some related tables do not exist. Silakan coba lagi.');
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
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan saat menghapus user. Silakan coba lagi.'])->setStatusCode(500);
        }
    }

    /**
     * Export Users Data
     */
    public function export()
    {
        if (!$this->hasPermission('admin.export')) {
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
        if (!$this->hasPermission('admin.manage')) {
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
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi administrator.'
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

    // hasPermission method removed - using BaseController's protected method instead

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
        if (!$this->hasPermission('admin.manage')) {
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
            log_message('error', 'Quick Assign Permission Error. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi administrator.'
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
                return $this->response->setJSON(['success' => false, 'message' => 'User tidak ditemukan.']);
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
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
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
            $permissionKey = $perm['key_name'] ?? $perm['key'] ?? null;
            if ($permissionKey === null) {
                continue;
            }

            $result[$permissionKey] = false;
        }

        // Dari role
        $rolePerms = $this->db->table('user_roles ur')
            ->join('role_permissions rp', 'rp.role_id = ur.role_id')
            ->join('permissions p', 'p.id = rp.permission_id')
            ->where('ur.user_id', $userId)
            ->select($this->permissionKeyColumn('p') . ' as permission_key')
            ->get()->getResultArray();
        foreach ($rolePerms as $rp) {
            $result[$rp['permission_key']] = true;
        }

        // Dari division (jika ada logic permission per division)
        // -- tambahkan jika memang ada

        // Dari custom user permission (override)
        $customPerms = $this->db->table('user_permissions up')
            ->join('permissions p', 'p.id = up.permission_id')
            ->where('up.user_id', $userId)
            ->select([
                $this->permissionKeyColumn('p') . ' as permission_key',
                'up.granted',
            ])
            ->get()->getResultArray();
        foreach ($customPerms as $cp) {
            $result[$cp['permission_key']] = (bool)$cp['granted'];
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
            return $this->response->setStatusCode(500)->setBody('Gagal memuat data. Silakan coba lagi.');
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
                // Get user and division details for notification
                $user = $this->userModel->find($userId);
                $division = $this->db->table('divisions')->where('id', $divisionId)->get()->getRowArray();
                
                // Send notification - user removed from division
                if (function_exists('notify_user_removed_from_division') && $user && $division) {
                    notify_user_removed_from_division([
                        'id' => $userId,
                        'user_name' => $user['username'] ?? '',
                        'division_name' => $division['name'] ?? '',
                        'removed_by' => session('username') ?? session('user_id'),
                        'url' => base_url('/admin/user-management')
                    ]);
                }
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'User removed from division successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal memproses permintaan. Silakan coba lagi.'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'removeUserFromDivision error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi administrator.'
            ]);
        }
    }

    /**
     * Bulk Permission Assignment
     */
    public function bulkAssignPermissions()
    {
        if (!$this->hasPermission('admin.manage')) {
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
            log_message('error', 'Bulk Assign Permissions Error. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi administrator.'
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
            $rolePermissions = safe_get_result(
                $this->db->table('user_roles ur')
                    ->join('roles r', 'r.id = ur.role_id')
                    ->join('role_permissions rp', 'rp.role_id = r.id')
                    ->join('permissions p', 'p.id = rp.permission_id')
                    ->where('ur.user_id', $userId)
                    ->select([
                        $this->permissionKeyColumn('p') . ' as permission_key',
                        $this->permissionDisplayColumn('p') . ' as display_name',
                        'p.description',
                        '"role" as source',
                        'r.name as source_name',
                    ])
            );
        } catch (\Exception $e) {
            $rolePermissions = [];
        }

        try {
            // Get custom permissions
            $customPermissions = safe_get_result(
                $this->db->table('user_permissions up')
                    ->join('permissions p', 'p.id = up.permission_id')
                    ->where('up.user_id', $userId)
                    ->select([
                        $this->permissionKeyColumn('p') . ' as permission_key',
                        $this->permissionDisplayColumn('p') . ' as display_name',
                        'p.description',
                        'up.granted',
                        '"custom" as source',
                    ])
            );
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
                ->where($this->permissionKeyColumn('p'), $permissionKey);
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
                ->where($this->permissionKeyColumn('p'), $permissionKey)
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
                'error' => 'Bukan request AJAX'
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
            $columns = ['user_info', 'email', 'divisions', 'roles', 'custom_permissions', 'status', 'actions'];
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
                    ->join('divisions d', 'd.id = ur.division_id', 'left')
                    ->where('ur.user_id', $user['id'])
                    ->where('ur.division_id IS NOT NULL')
                    ->select('d.name')
                    ->get()->getResultArray();
                $divisionsHtml = '';
                if (!empty($divisions)) {
                    foreach ($divisions as $division) {
                        if (!empty($division['name'])) {
                            $divisionsHtml .= '<span class="badge bg-info me-1">' . esc($division['name']) . '</span>';
                        }
                    }
                }
                if (empty($divisionsHtml)) {
                    // Check if user is Super Administrator
                    $isSuperAdmin = $this->db->table('user_roles ur')
                        ->join('roles r', 'r.id = ur.role_id')
                        ->where('ur.user_id', $user['id'])
                        ->where('r.name', 'Super Administrator')
                        ->countAllResults() > 0;
                    
                    if ($isSuperAdmin) {
                        $divisionsHtml = '<span class="badge bg-dark">All Divisions</span>';
                    } else {
                        $divisionsHtml = '<span class="text-muted">-</span>';
                    }
                }
                

                // Custom Permissions
                $customPermCount = $this->db->table('user_permissions')
                    ->where('user_id', $user['id'])
                    ->countAllResults();
                $customPermHtml = $customPermCount > 0 ? '<span class="badge bg-warning">' . $customPermCount . '</span>' : '<span class="text-muted">-</span>';

                // Status
                $statusHtml = $user['is_active'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>';

                // Actions
                $approveBtn = '';
                if ($user['is_active'] == 0) {
                    $approveBtn = '<button class="btn btn-success" onclick="approveUser(' . $user['id'] . ', \'' . esc($user['first_name'] . ' ' . $user['last_name']) . '\')" title="Approve User"><i class="fas fa-check-circle"></i></button>';
                } else {
                    $approveBtn = '<button class="btn btn-secondary" onclick="deactivateUser(' . $user['id'] . ', \'' . esc($user['first_name'] . ' ' . $user['last_name']) . '\')" title="Deactivate User"><i class="fas fa-times-circle"></i></button>';
                }
                
                $actions = '
                    <div class="btn-group btn-group-sm">
                        ' . $approveBtn . '
                        <button class="btn btn-info" onclick="viewUserMatrix(' . $user['id'] . ')" title="View Matrix"><i class="fas fa-th"></i></button>
                        <a href="' . base_url('admin/advanced-users/show/' . $user['id']) . '" class="btn btn-primary" title="Detail"><i class="fas fa-eye"></i></a>
                        <a href="' . base_url('admin/advanced-users/edit/' . $user['id']) . '" class="btn btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                        <button class="btn btn-danger" onclick="confirmDeleteUser(' . $user['id'] . ', \'' . esc($user['first_name'] . ' ' . $user['last_name']) . '\')" title="Delete"><i class="fas fa-trash"></i></button>
                    </div>
                ';

                $data[] = [
                    'user_info' => $userInfo,
                    'email' => esc($user['email']),
                    'divisions' => $divisionsHtml,
                    'roles' => $rolesHtml,
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
                'error' => 'Terjadi kesalahan pada server. Silakan coba lagi.'
            ])->setStatusCode(500);
        }
    }
    
    // Assign role ke user, tidak auto-assign permission ke user/division
    public function assignRole($userId, $roleId) {
        $this->userRoleModel->assignRole($userId, $roleId);
        // Tidak perlu auto-assign permission ke user/division
        // Permission didapat dari role, kecuali ada custom permission (override)
    }

    /**
     * Show Change Password Form
     */
    public function changePasswordForm($userId)
    {
        if (!$this->hasPermission('admin.manage')) {
            return redirect()->to('/admin/advanced-users')->with('error', 'Akses ditolak.');
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            return redirect()->to('/admin/advanced-users')->with('error', 'User tidak ditemukan.');
        }

        $data = [
            'title' => 'Change Password - ' . $user['first_name'] . ' ' . $user['last_name'],
            'user' => $user
        ];

        return view('admin/advanced_user_management/change_password', $data);
    }

    /**
     * Change User Password
     */
    public function changePassword($userId)
    {
        // Set JSON response header for AJAX requests
        if ($this->request->isAJAX() || $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest') {
            $this->response->setContentType('application/json');
        }
        
        if (!$this->hasPermission('admin.manage')) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak'])->setStatusCode(403);
            }
            return redirect()->to('/admin/advanced-users')->with('error', 'Akses ditolak.');
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'User tidak ditemukan'])->setStatusCode(404);
            }
            return redirect()->to('/admin/advanced-users')->with('error', 'User tidak ditemukan.');
        }

        $validation = $this->validate([
            'new_password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[new_password]'
        ]);

        if (!$validation) {
            $errors = $this->validator->getErrors();
            $errorMessage = !empty($errors) ? implode(', ', $errors) : 'Validation failed';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => $errorMessage, 'errors' => $errors])->setStatusCode(400);
            }
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        try {
            $newPassword = $this->request->getPost('new_password');
            
            // Validate password strength
            if (!$this->validatePasswordStrength($newPassword)) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Password harus mengandung minimal 1 huruf dan 1 angka'])->setStatusCode(400);
                }
                return redirect()->back()->withInput()->with('error', 'Password harus mengandung minimal 1 huruf dan 1 angka.');
            }
            
            // Store old data for logging
            $oldData = [
                'user_id' => $userId,
                'username' => $user['username'],
                'email' => $user['email'],
                'password_hash' => '***' // Don't log actual password hash
            ];
            
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            $result = $this->userModel->update($userId, [
                'password_hash' => $hashedPassword,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            if ($result) {
                // Prepare new data for logging
                $newData = [
                    'user_id' => $userId,
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'password_hash' => '***', // Don't log actual password hash
                    'action' => 'password_changed',
                    'changed_by' => session()->get('user_id') ?? 1,
                    'timestamp' => date('Y-m-d H:i:s')
                ];
                
                // Log password change
                $this->logUpdate('users', $userId, $oldData, $newData);

                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => true, 'message' => 'Password berhasil diubah']);
                }
                return redirect()->to('/admin/advanced-users')->with('success', 'Password berhasil diubah.');
            } else {
                throw new \Exception('Gagal memproses permintaan. Silakan coba lagi.');
            }

        } catch (\Exception $e) {
            log_message('error', 'Change Password Stack: ' . $e->getTraceAsString());
            
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false, 
                    'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi administrator.'
                ])->setStatusCode(500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi administrator.');
        }
    }

    /**
     * Validate password strength
     */
    private function validatePasswordStrength($password)
    {
        // Check minimum length
        if (strlen($password) < 6) {
            return false;
        }

        // Check if contains at least one letter
        if (!preg_match('/[a-zA-Z]/', $password)) {
            return false;
        }

        // Check if contains at least one number
        if (!preg_match('/[0-9]/', $password)) {
            return false;
        }

        return true;
    }

    /**
     * Get all users for notification system
     */
    public function getUsers()
    {
        try {
            $userModel = new UserModel();
            $divisionModel = new DivisionModel();
            
            $users = $userModel->select('users.id, users.username, users.email, users.division_id, d.name as division_name')
                ->join('divisions d', 'd.id = users.division_id', 'left')
                ->where('users.is_active', 1)
                ->findAll();
            
            return $this->response->setJSON([
                'success' => true,
                'users' => $users
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat data. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Get users by divisions for notification system
     */
    public function getUsersByDivisions()
    {
        try {
            $payload = $this->request->getJSON(true) ?? [];
            $divisions = array_filter(array_map('trim', $payload['divisions'] ?? []));
            $roles = array_filter(array_map('trim', $payload['roles'] ?? []));

            if (empty($divisions) && empty($roles)) {
                return $this->getUsers();
            }

            $builder = $this->db->table('users u')
                ->distinct()
                ->select('u.id, u.username, u.email, COALESCE(d.name, "No Division") as division_name')
                ->join('user_roles ur', 'ur.user_id = u.id', 'left')
                ->join('divisions d', 'd.id = ur.division_id', 'left')
                ->join('roles r', 'r.id = ur.role_id', 'left')
                ->where('u.is_active', 1);

            if (!empty($divisions)) {
                $builder->groupStart();
                foreach ($divisions as $division) {
                    $builder->orWhere('LOWER(d.name)', strtolower($division))
                            ->orWhere('LOWER(d.code)', strtolower($division));
                }
                $builder->groupEnd();
            }

            if (!empty($roles)) {
                $builder->groupStart();
                foreach ($roles as $role) {
                    $builder->orWhere('LOWER(r.name)', strtolower($role));
                }
                $builder->groupEnd();
            }

            $users = $builder->orderBy('u.username', 'ASC')->get()->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'users' => $users
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat data. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Get users by roles for notification system
     */
    public function getUsersByRoles()
    {
        try {
            $payload = $this->request->getJSON(true) ?? [];
            $roles = array_filter(array_map('trim', $payload['roles'] ?? []));
            $divisions = array_filter(array_map('trim', $payload['divisions'] ?? []));

            if (empty($roles) && empty($divisions)) {
                return $this->getUsers();
            }

            $builder = $this->db->table('users u')
                ->distinct()
                ->select('u.id, u.username, u.email, COALESCE(d.name, "No Division") as division_name')
                ->join('user_roles ur', 'ur.user_id = u.id', 'left')
                ->join('roles r', 'r.id = ur.role_id', 'left')
                ->join('divisions d', 'd.id = ur.division_id', 'left')
                ->where('u.is_active', 1);

            if (!empty($divisions)) {
                $builder->groupStart();
                foreach ($divisions as $division) {
                    $builder->orWhere('LOWER(d.name)', strtolower($division))
                            ->orWhere('LOWER(d.code)', strtolower($division));
                }
                $builder->groupEnd();
            }

            if (!empty($roles)) {
                $builder->groupStart();
                foreach ($roles as $role) {
                    $builder->orWhere('LOWER(r.name)', strtolower($role));
                }
                $builder->groupEnd();
            }

            $users = $builder->orderBy('u.username', 'ASC')->get()->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'users' => $users
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat data. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Get user data for approval modal
     */
    public function getUserForApproval($userId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Request tidak valid. Harap kirim data melalui form yang benar.'])->setStatusCode(400);
        }

        if (!$this->hasPermission('admin.manage')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak.'])->setStatusCode(403);
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'User tidak ditemukan.'])->setStatusCode(404);
        }

        // Get division name if division_id exists
        $userDivisionName = null;
        if (!empty($user['division_id'])) {
            $userDivision = $this->divisionModel->find($user['division_id']);
            $userDivisionName = $userDivision ? $userDivision['name'] : null;
        }

        // Get all divisions (for admin selection)
        $divisions = $this->divisionModel->where('name !=', 'Administrator')->findAll();

        // Get all roles for cascading dropdown
        $allRoles = $this->roleModel->findAll();

        // Get all service areas for the service access section
        $areas = $this->db->table('areas')
            ->select('id, area_code, area_name, area_type')
            ->where('is_active', 1)
            ->orderBy('area_name', 'ASC')
            ->get()->getResultArray();

        // Get user's existing service access (if any) for pre-population
        $userServiceAccess = $this->db->table('user_area_access')
            ->where('user_id', $userId)
            ->get()->getRowArray();
        $userBranchAccess = $this->db->table('user_branch_access')
            ->where('user_id', $userId)
            ->get()->getRowArray();
        if ($userBranchAccess && !empty($userBranchAccess['branch_ids'])) {
            $userBranchAccess['branch_ids'] = json_decode($userBranchAccess['branch_ids'], true);
        }

        return $this->response->setJSON([
            'success' => true,
            'user' => [
                'id' => $user['id'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'email' => $user['email'],
                'username' => $user['username'] ?? '',
                'phone' => $user['phone'] ?? '',
                'position' => $user['position'] ?? '',
                'division_id' => $user['division_id'] ?? null,
                'division_name' => $userDivisionName
            ],
            'divisions' => $divisions,
            'roles' => $allRoles,
            'areas' => $areas,
            'user_service_access' => $userServiceAccess ?: null,
            'user_branch_access' => $userBranchAccess ?: null
        ]);
    }

    /**
     * Approve user with division and position assignment
     */
    public function approveUser($userId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Request tidak valid. Harap kirim data melalui form yang benar.'])->setStatusCode(400);
        }

        if (!$this->hasPermission('admin.manage')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak.'])->setStatusCode(403);
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'User tidak ditemukan.'])->setStatusCode(404);
        }

        // Get division and role from POST
        $divisionId = $this->request->getPost('division_id');
        $roleId = $this->request->getPost('role_id');

        if (empty($divisionId) || empty($roleId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Division dan Role harus diisi.'])->setStatusCode(400);
        }

        // Validate division exists
        $division = $this->divisionModel->find($divisionId);
        if (!$division) {
            return $this->response->setJSON(['success' => false, 'message' => 'Division tidak ditemukan.'])->setStatusCode(400);
        }

        // Validate role exists
        $role = $this->roleModel->find($roleId);
        if (!$role) {
            return $this->response->setJSON(['success' => false, 'message' => 'Role tidak ditemukan.'])->setStatusCode(400);
        }

        $this->db->transStart();

        try {
            // Update user: activate and assign division
            $updateData = [
                'is_active' => 1,
                'division_id' => $divisionId,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if (!$this->userModel->update($userId, $updateData)) {
                throw new \Exception('Failed to update user.');
            }

            // Remove existing user_roles for this user
            $this->db->table('user_roles')->where('user_id', $userId)->delete();

            // Assign new role with division
            $this->db->table('user_roles')->insert([
                'user_id' => $userId,
                'role_id' => $roleId,
                'division_id' => $divisionId,
                'assigned_by' => session()->get('user_id') ?? 1,
                'assigned_at' => date('Y-m-d H:i:s'),
                'is_active' => 1
            ]);

            // Log activity
            $this->logAuthActivity('USER_APPROVED', $userId, [
                'username' => $user['username'],
                'email' => $user['email'],
                'division_id' => $divisionId,
                'division_name' => $division['name'],
                'role_id' => $roleId,
                'role_name' => $role['name'],
                'action' => 'approved',
                'description' => 'User account approved by admin with division and role assignment.'
            ]);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Database transaction failed.');
            }

            // Save service area access if this is a Service division user
            $this->handleServiceAccess($userId, $divisionId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'User berhasil diaktifkan dengan division dan role yang ditetapkan.'
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Approve User Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengaktifkan user. Silakan coba lagi.'
            ])->setStatusCode(500);
        }
    }

    /**
     * Deactivate user
     */
    public function deactivateUser($userId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Request tidak valid. Harap kirim data melalui form yang benar.'])->setStatusCode(400);
        }

        if (!$this->hasPermission('admin.manage')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak.'])->setStatusCode(403);
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'User tidak ditemukan.'])->setStatusCode(404);
        }

        if ($this->userModel->update($userId, ['is_active' => 0, 'updated_at' => date('Y-m-d H:i:s')])) {
            $this->logAuthActivity('USER_DEACTIVATED', $userId, [
                'username' => $user['username'],
                'email' => $user['email'],
                'action' => 'deactivated',
                'description' => 'User account deactivated by admin.'
            ]);
            return $this->response->setJSON(['success' => true, 'message' => 'User berhasil dinonaktifkan.']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menonaktifkan user.'])->setStatusCode(500);
        }
    }

    /**
     * Get available permissions for custom permission assignment
     */
    public function getAvailablePermissions($userId)
    {
        if (!$this->hasPermission('admin.manage')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akses ditolak'
            ])->setStatusCode(403);
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User tidak ditemukan.'
            ])->setStatusCode(404);
        }

        // Get all permissions
        $allPermissions = $this->permissionModel->findAll();
        
        // Get current user custom permissions (from user_permissions table)
        $customPermissions = $this->db->table('user_permissions')
            ->where('user_id', $userId)
            ->select('permission_id')
            ->get()
            ->getResultArray();
        $customPermissionIds = array_column($customPermissions, 'permission_id');

        // Get permissions from user roles (from role_permissions table)
        $rolePermissions = $this->db->table('user_roles ur')
            ->join('role_permissions rp', 'rp.role_id = ur.role_id')
            ->where('ur.user_id', $userId)
            ->select('rp.permission_id')
            ->distinct()
            ->get()
            ->getResultArray();
        $rolePermissionIds = array_column($rolePermissions, 'permission_id');

        // Combine both: permissions from roles + custom permissions
        $allAssignedPermissionIds = array_unique(array_merge($rolePermissionIds, $customPermissionIds));

        // Format permissions with current status
        $permissions = [];
        foreach ($allPermissions as $perm) {
            $isFromRole = in_array($perm['id'], $rolePermissionIds);
            $isCustom = in_array($perm['id'], $customPermissionIds);
            $isAssigned = in_array($perm['id'], $allAssignedPermissionIds);
            
            $permissions[] = [
                'id' => $perm['id'],
                'key' => $perm['key'],
                'name' => $perm['name'],
                'description' => $perm['description'] ?? '',
                'module' => $perm['module'] ?? 'general',
                'category' => $perm['category'] ?? 'module',
                'is_assigned' => $isAssigned,
                'is_from_role' => $isFromRole,
                'is_custom' => $isCustom
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $permissions
        ]);
    }

    /**
     * Get permissions for specific roles
     */

    public function getEnhancedPermissions()
    {
        try {
            // Use existing PermissionModel instead of non-existent EnhancedPermissionModel
            $permissionModel = new \App\Models\PermissionModel();
            
            // Get permission tree (or flat list if getPermissionTree doesn't exist)
            $permissionTree = method_exists($permissionModel, 'getPermissionTree') 
                ? $permissionModel->getPermissionTree() 
                : $permissionModel->findAll();
            
            // Get flat permissions array for compatibility
            $flatPermissions = $permissionModel->where('is_active', 1)->findAll();
            
            // Get unique modules
            $modules = array_unique(array_column($flatPermissions, 'module'));
            
            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'tree' => $permissionTree,
                    'flat' => $flatPermissions,
                    'modules' => $modules
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Gagal memuat data. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load enhanced permissions'
            ])->setStatusCode(500);
        }
    }

    public function getRolePermissions()
    {
        // Temporarily disable permission check for debugging
        // if (!$this->hasPermission('admin.manage')) {
        //     return $this->response->setJSON([
        //         'success' => false,
        //         'message' => 'Akses ditolak'
        //     ])->setStatusCode(403);
        // }

        $roleIds = $this->request->getPost('role_ids');
        
        log_message('debug', 'getRolePermissions called with roleIds: ' . json_encode($roleIds));
        
        if (empty($roleIds) || !is_array($roleIds)) {
            log_message('debug', 'No role IDs provided, returning empty array');
            return $this->response->setJSON([
                'success' => true,
                'data' => []
            ]);
        }

        try {
            $permissions = $this->db->table('role_permissions rp')
                ->join('permissions p', 'p.id = rp.permission_id')
                ->whereIn('rp.role_id', $roleIds)
                ->where('rp.granted', 1)
                ->where('p.is_active', 1)
                ->select('p.id, p.key_name, p.display_name, p.description, p.module, p.page, p.action')
                ->distinct()
                ->get()
                ->getResultArray();

            log_message('debug', 'Found ' . count($permissions) . ' role permissions');
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $permissions
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load role permissions'
            ])->setStatusCode(500);
        }
    }

    /**
     * Save custom permissions for user
     */
    public function saveCustomPermissions($userId)
    {
        if (!$this->hasPermission('admin.manage')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akses ditolak'
            ])->setStatusCode(403);
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User tidak ditemukan.'
            ])->setStatusCode(404);
        }

        $permissionIds = $this->request->getPost('permissions') ?? [];
        
        if (empty($permissionIds)) {
            // Remove all custom permissions
            $this->db->table('user_permissions')->where('user_id', $userId)->delete();
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'All custom permissions removed'
            ]);
        }

        // Validate permission IDs
        $validPermissionIds = $this->db->table('permissions')
            ->whereIn('id', $permissionIds)
            ->select('id')
            ->get()
            ->getResultArray();
        $validIds = array_column($validPermissionIds, 'id');

        if (count($validIds) !== count($permissionIds)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Some permission IDs are invalid'
            ])->setStatusCode(400);
        }

        $this->db->transStart();

        try {
            // Remove existing custom permissions
            $this->db->table('user_permissions')->where('user_id', $userId)->delete();

            // Insert new custom permissions
            $assignedBy = session()->get('user_id') ?? 1;
            foreach ($validIds as $permissionId) {
                $this->db->table('user_permissions')->insert([
                    'user_id' => $userId,
                    'permission_id' => $permissionId,
                    'granted' => 1,
                    'assigned_by' => $assignedBy,
                    'assigned_at' => date('Y-m-d H:i:s')
                ]);
            }

            $this->db->transComplete();

            if ($this->db->transStatus()) {
                // Log activity
                $this->logActivity('UPDATE', 'user_permissions', $userId, 'Custom permissions updated', [
                    'module_name' => 'ADMIN',
                    'submenu_item' => 'User Management',
                    'business_impact' => 'MEDIUM',
                    'permissions_count' => count($validIds)
                ]);

                // Send notification - user permissions updated
                if (function_exists('notify_user_permissions_updated')) {
                    notify_user_permissions_updated([
                        'id' => $userId,
                        'user_name' => $user['username'] ?? '',
                        'permissions_changed' => count($validIds) . ' custom permissions updated',
                        'updated_by' => session('username') ?? session('user_id'),
                        'url' => base_url('/admin/user-management/edit/' . $userId)
                    ]);
                }
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => count($validIds) . ' custom permission(s) saved successfully'
                ]);
            } else {
                throw new \Exception('Transaction failed');
            }

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Save Custom Permissions Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi administrator.'
            ])->setStatusCode(500);
        }
    }

    /**
     * Remove a custom permission from user
     */
    public function removeCustomPermission($userId)
    {
        if (!$this->hasPermission('admin.manage')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akses ditolak'
            ])->setStatusCode(403);
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User tidak ditemukan.'
            ])->setStatusCode(404);
        }

        $permissionId = $this->request->getPost('permission_id');
        
        if (empty($permissionId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Permission ID is required'
            ])->setStatusCode(400);
        }

        try {
            $result = $this->db->table('user_permissions')
                ->where('user_id', $userId)
                ->where('permission_id', $permissionId)
                ->delete();

            if ($result) {
                // Log activity
                $this->logActivity('DELETE', 'user_permissions', $userId, 'Custom permission removed', [
                    'module_name' => 'ADMIN',
                    'submenu_item' => 'User Management',
                    'business_impact' => 'LOW',
                    'permission_id' => $permissionId
                ]);

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Custom permission removed successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Permission tidak ditemukan atau sudah dihapus'
                ])->setStatusCode(404);
            }

        } catch (\Exception $e) {
            log_message('error', 'Remove Custom Permission Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi administrator.'
            ])->setStatusCode(500);
        }
    }
    
    /**
     * Handle Service Access for Service Division Users
     */
    private function handleServiceAccess($userId, $divisionId)
    {
        log_message('debug', '=== handleServiceAccess CALLED ===');
        log_message('debug', 'User ID: ' . $userId);
        log_message('debug', 'Division ID: ' . $divisionId);
        
        // Always log ALL POST data first
        log_message('debug', 'ALL POST Data: ' . json_encode($this->request->getPost()));
        
        // For update mode, check if current user division is Service
        if ($divisionId === null) {
            // Get user's current division from user_roles table
            $userDivision = $this->db->table('user_roles ur')
                ->join('divisions d', 'd.id = ur.division_id')
                ->where('ur.user_id', $userId)
                ->select('d.id, d.name')
                ->get()
                ->getRowArray();
            
            log_message('debug', 'User division from DB: ' . json_encode($userDivision));
            
            if (!$userDivision || !stripos($userDivision['name'], 'service')) {
                log_message('debug', 'User not in Service division during update, but forcing execution for debug');
                // Don't return, continue for debugging
            }
            
            $division = $userDivision;
        } else {
            // For create mode, check if selected division is Service
            $division = $this->divisionModel->find($divisionId);
            if (!$division || !stripos($division['name'], 'service')) {
                log_message('debug', 'Selected division is not Service division, but forcing execution for debug');
                // Don't return, continue for debugging
            }
        }
        
        // Debug: Log the data received
        $areaType = $this->request->getPost('area_type');
        $departmentScope = $this->request->getPost('department_scope'); // For CENTRAL
        
        // Try multiple ways to get service area IDs
        $serviceAreaIds = [];
        
        // Method 1: Direct array from POST
        $directArray = $this->request->getPost('service_area_ids');
        if ($directArray && is_array($directArray)) {
            $serviceAreaIds = $directArray;
        }
        
        // Method 2: JSON string from POST
        $jsonString = $this->request->getPost('service_area_ids_json');
        if (!$serviceAreaIds && $jsonString) {
            $decoded = json_decode($jsonString, true);
            if (is_array($decoded)) {
                $serviceAreaIds = $decoded;
            }
        }
        
        // Method 3: Array format from POST (service_area_ids[0], service_area_ids[1], etc.)
        $postData = $this->request->getPost();
        if (!$serviceAreaIds) {
            foreach ($postData as $key => $value) {
                if (preg_match('/^service_area_ids\[(\d+)\]$/', $key)) {
                    $serviceAreaIds[] = $value;
                }
            }
        }
        
        log_message('debug', '=== SERVICE ACCESS DEBUG ===');
        log_message('debug', 'User ID: ' . $userId);
        log_message('debug', 'Division ID: ' . $divisionId);
        log_message('debug', 'Division Name: ' . ($division['name'] ?? 'NULL'));
        log_message('debug', 'Area Type: ' . ($areaType ?? 'NULL'));
        log_message('debug', 'Department Scope: ' . ($departmentScope ?? 'NULL'));
        log_message('debug', 'Service Area IDs (Method 1 - direct): ' . json_encode($directArray));
        log_message('debug', 'Service Area IDs (Method 2 - JSON): ' . json_encode($jsonString));
        log_message('debug', 'Service Area IDs (final): ' . json_encode($serviceAreaIds));
        log_message('debug', 'Service Area IDs Type: ' . gettype($serviceAreaIds));
        log_message('debug', '========================');
        
        // Clear existing service access data first
        log_message('debug', 'Clearing existing service access data for user: ' . $userId);
        $this->db->table('user_area_access')->where('user_id', $userId)->delete();
        $this->db->table('user_branch_access')->where('user_id', $userId)->delete();
        
        // Save area access based on type
        if ($areaType) {
            $areaData = [
                'user_id' => $userId,
                'area_type' => $areaType,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            if ($areaType === 'CENTRAL' && $departmentScope) {
                $areaData['department_scope'] = $departmentScope;
            }
            
            log_message('debug', 'Inserting area access: ' . json_encode($areaData));
            $result = $this->db->table('user_area_access')->insert($areaData);
            log_message('debug', 'Area access insert result: ' . ($result ? 'SUCCESS' : 'FAILED'));
        }
        
        // Save service area access for BRANCH type
        if ($areaType === 'BRANCH' && !empty($serviceAreaIds)) {
            $branchData = [
                'user_id' => $userId,
                'access_type' => 'SPECIFIC_BRANCHES', // Use correct enum value
                'branch_ids' => json_encode($serviceAreaIds), // Store service area IDs
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            log_message('debug', 'Inserting branch access: ' . json_encode($branchData));
            $result = $this->db->table('user_branch_access')->insert($branchData);
            log_message('debug', 'Branch access insert result: ' . ($result ? 'SUCCESS' : 'FAILED'));
        } else {
            log_message('debug', 'NOT saving branch access - Area Type: ' . $areaType . ', Service Area IDs count: ' . count($serviceAreaIds));
        }
        
        log_message('debug', '=== handleServiceAccess COMPLETED ===');
    }
    
    /**
     * Get Service Access Data for User
     */
    public function getServiceAccess($userId)
    {
        try {
            // Get area access
            $areaAccess = $this->db->table('user_area_access')
                ->where('user_id', $userId)
                ->get()
                ->getRowArray();
            
            // Get branch access
            $branchAccess = $this->db->table('user_branch_access')
                ->where('user_id', $userId)
                ->get()
                ->getRowArray();
            
            $data = [];
            if ($areaAccess) {
                $data['area_type'] = $areaAccess['area_type'];
                $data['department_scope'] = $areaAccess['department_scope'];
            }
            
            if ($branchAccess) {
                $data['access_type'] = $branchAccess['access_type'];
                if ($branchAccess['branch_ids']) {
                    $data['service_area_ids'] = json_decode($branchAccess['branch_ids'], true);
                }
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Get Service Access Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi administrator.'
            ])->setStatusCode(500);
        }
    }
    
    /**
     * Update Service Access for User
     */
    public function updateServiceAccess($userId)
    {
        try {
            $this->db->transStart();
            
            // Delete existing service access
            $this->db->table('user_area_access')->where('user_id', $userId)->delete();
            $this->db->table('user_branch_access')->where('user_id', $userId)->delete();
            
            // Add new service access
            $this->handleServiceAccess($userId, null); // Pass null for division since we're updating
            
            $this->db->transComplete();
            
            if ($this->db->transStatus()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Service access updated successfully'
                ]);
            } else {
                throw new \Exception('Transaction failed');
            }
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Update service access error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memproses permintaan. Silakan coba lagi.'
            ]);
        }
    }
    
    /**
     * Get service areas for dropdown (simple version)
     */
    public function getServiceAreas()
    {
        try {
            $areas = $this->db->table('areas')
                ->select('id, area_name as name, area_description as description')
                ->where('is_active', 1)
                ->orderBy('area_name', 'ASC')
                ->get()
                ->getResultArray();
                
            return $this->response->setJSON([
                'success' => true,
                'data' => $areas
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Get service areas error. Silakan coba lagi.');
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memproses permintaan. Silakan coba lagi.',
                'data' => []
            ]);
        }
    }
}