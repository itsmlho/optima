<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PermissionModel;
use CodeIgniter\API\ResponseTrait;
use App\Traits\ActivityLoggingTrait;

class PermissionController extends BaseController
{
    use ResponseTrait;
    use ActivityLoggingTrait;

    protected $db;
    protected $permissionModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->permissionModel = new PermissionModel();
    }

    /**
     * Permission Management Dashboard
     */
    public function index()
    {
        if (!$this->hasPermission('admin.permission_management')) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        try {
            $permissions = $this->getPermissionsWithStats();
            $stats = $this->getPermissionStats();

            $data = [
                'title' => 'Permission Management',
                'breadcrumbs' => [
                    '/' => 'Dashboard',
                    '/admin' => 'Administration',
                    '/admin/permissions' => 'Permission Management'
                ],
                'permissions' => $permissions,
                'stats' => $stats
            ];

            return view('admin/advanced_user_management/permissions', $data);
        } catch (\Exception $e) {
            log_message('error', 'Permission Management Error: ' . $e->getMessage());

            $data = [
                'title' => 'Permission Management - Error',
                'error_message' => 'Gagal memuat data permission. Silakan coba lagi.',
                'permissions' => [],
                'stats' => [
                    'total' => 0,
                    'modules' => 0,
                    'system' => 0,
                    'custom' => 0
                ]
            ];
            return view('admin/advanced_user_management/permissions', $data);
        }
    }

    /**
     * Create New Permission
     */
    public function store()
    {
        if (!$this->hasPermission('admin.permission_create')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak'])->setStatusCode(403);
        }

        $validation = $this->validate([
            'key_name' => "required|min_length[3]|max_length[100]|is_unique[permissions.key_name]",
            'display_name' => 'required|max_length[100]',
            'description' => 'permit_empty|max_length[255]',
            'module' => 'required|max_length[50]',
            'page' => 'required|max_length[50]',
            'action' => 'required|max_length[50]'
        ], [
            'key_name' => [
                'required' => 'Key name harus diisi.',
                'min_length' => 'Key name minimal 3 karakter.',
                'max_length' => 'Key name maksimal 100 karakter.',
                'is_unique' => 'Key name sudah digunakan. Silakan gunakan nama yang berbeda.'
            ],
            'display_name' => [
                'required' => 'Nama tampilan harus diisi.',
                'max_length' => 'Nama tampilan maksimal 100 karakter.'
            ],
            'module' => [
                'required' => 'Module harus diisi.'
            ],
            'page' => [
                'required' => 'Page harus diisi.'
            ],
            'action' => [
                'required' => 'Action harus diisi.'
            ]
        ]);

        if (!$validation) {
            $errors = $this->validator->getErrors();
            $firstError = !empty($errors) ? reset($errors) : 'Validasi gagal';
            return $this->response->setJSON([
                'success' => false,
                'message' => $firstError,
                'errors' => $errors
            ])->setStatusCode(400);
        }

        try {
            // Gunakan field baru sesuai struktur database
            $permissionData = [
                'key_name' => $this->request->getPost('key_name'),
                'display_name' => $this->request->getPost('display_name'),
                'description' => $this->request->getPost('description'),
                'module' => $this->request->getPost('module'),
                'page' => $this->request->getPost('page'),
                'action' => $this->request->getPost('action'),
                'is_active' => 1 // Default active
            ];

            $permissionId = $this->permissionModel->insert($permissionData);

            if (!$permissionId) {
                throw new \Exception('Gagal membuat permission');
            }

            // Log permission creation using trait
            $this->logCreate('permissions', $permissionId, [
                'permission_id' => $permissionId,
                'key_name' => $permissionData['key_name'],
                'display_name' => $permissionData['display_name'],
                'description' => $permissionData['description'],
                'module' => $permissionData['module'],
                'page' => $permissionData['page'],
                'action' => $permissionData['action'],
                'created_by' => session()->get('user_id') ?? 1
            ]);

            // Send notification - permission created
            if (function_exists('notify_permission_created')) {
                notify_permission_created([
                    'id' => $permissionId,
                    'permission_name' => $permissionData['display_name'],
                    'permission_code' => $permissionData['key_name'],
                    'module_name' => $permissionData['module'],
                    'created_by' => session('username') ?? session('user_id'),
                    'url' => base_url('/admin/permissions')
                ]);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Permission berhasil dibuat',
                'permission_id' => $permissionId
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Create Permission Error. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat permission. Silakan coba lagi.'
            ])->setStatusCode(500);
        }
    }

    /**
     * Update Permission
     */
    public function update($permissionId)
    {
        if (!$this->hasPermission('admin.permission_edit')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak'])->setStatusCode(403);
        }

        $permission = $this->permissionModel->find($permissionId);
        if (!$permission) {
            return $this->response->setJSON(['success' => false, 'message' => 'Permission tidak ditemukan'])->setStatusCode(404);
        }

        // Manual validation untuk update
        $validation = \Config\Services::validation();
        $validation->setRules([
            'key_name' => "required|min_length[3]|max_length[150]|is_unique[permissions.key_name,id,{$permissionId}]",
            'display_name' => 'required|max_length[100]',
            'description' => 'permit_empty|max_length[255]',
            'module' => 'required|max_length[50]',
            'page' => 'required|max_length[50]',
            'action' => 'required|max_length[50]'
        ], [
            'key_name' => [
                'required' => 'Key name harus diisi.',
                'min_length' => 'Key name minimal 3 karakter.',
                'is_unique' => 'Key name sudah digunakan. Silakan gunakan nama yang berbeda.'
            ],
            'display_name' => [
                'required' => 'Nama tampilan harus diisi.'
            ],
            'module' => [
                'required' => 'Module harus diisi.'
            ],
            'page' => [
                'required' => 'Page harus diisi.'
            ],
            'action' => [
                'required' => 'Action harus diisi.'
            ]
        ]);

        if (!$validation->run($this->request->getPost())) {
            $errors = $validation->getErrors();
            $firstError = !empty($errors) ? reset($errors) : 'Validasi gagal';
            return $this->response->setJSON([
                'success' => false,
                'message' => $firstError,
                'errors' => $errors
            ]);
        }

        try {
            // Gunakan field baru sesuai struktur database
            $permissionData = [
                'key_name' => $this->request->getPost('key_name'),
                'display_name' => $this->request->getPost('display_name'),
                'description' => $this->request->getPost('description'),
                'module' => $this->request->getPost('module'),
                'page' => $this->request->getPost('page'),
                'action' => $this->request->getPost('action')
            ];

            $result = $this->permissionModel->update($permissionId, $permissionData);

            if ($result === false) {
                throw new \Exception('Gagal memperbarui permission');
            }

            // Log permission update using trait
            $this->logUpdate('permissions', $permissionId, [
                'key_name' => $permission['key_name'],
                'display_name' => $permission['display_name'],
                'description' => $permission['description'],
                'module' => $permission['module'],
                'page' => $permission['page'],
                'action' => $permission['action']
            ], [
                'key_name' => $permissionData['key_name'],
                'display_name' => $permissionData['display_name'],
                'description' => $permissionData['description'],
                'module' => $permissionData['module'],
                'page' => $permissionData['page'],
                'action' => $permissionData['action']
            ], [
                'updated_by' => session()->get('user_id') ?? 1
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Permission berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Update Permission Error. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui permission. Silakan coba lagi.'
            ])->setStatusCode(500);
        }
    }

    /**
     * Delete Permission
     */
    public function delete($permissionId)
    {
        if (!$this->hasPermission('admin.permission_delete')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak'])->setStatusCode(403);
        }

        $permission = $this->permissionModel->find($permissionId);
        if (!$permission) {
            return $this->response->setJSON(['success' => false, 'message' => 'Permission tidak ditemukan'])->setStatusCode(404);
        }

        // Check if permission is assigned to roles
        $roleCount = $this->db->table('role_permissions')->where('permission_id', $permissionId)->countAllResults();
        
        // Check if permission is assigned to users directly
        $userCount = $this->db->table('user_permissions')->where('permission_id', $permissionId)->countAllResults();

        if ($roleCount > 0 || $userCount > 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => "Tidak dapat menghapus permission. Permission ini digunakan oleh {$roleCount} role dan {$userCount} user."
            ])->setStatusCode(400);
        }

        try {
            $result = $this->permissionModel->delete($permissionId);

            if (!$result) {
                log_message('error', 'PermissionModel delete failed for ID: ' . $permissionId);
                throw new \Exception('Gagal menghapus permission');
            }

            // Log permission deletion using trait
            $this->logDelete('permissions', $permissionId, [
                'permission_id' => $permissionId,
                'key_name' => $permission['key_name'],
                'display_name' => $permission['display_name'],
                'description' => $permission['description'],
                'module' => $permission['module'],
                'page' => $permission['page'],
                'action' => $permission['action'],
                'deleted_by' => session()->get('user_id') ?? 1
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Permission berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Delete Permission Error. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus permission. Silakan coba lagi.'
            ])->setStatusCode(500);
        }
    }

    /**
     * Get Permission Details (AJAX)
     */
    public function getDetail($permissionId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        try {
            $permission = $this->permissionModel->find($permissionId);
            if (!$permission) {
                return $this->response->setJSON(['success' => false, 'message' => 'Permission tidak ditemukan']);
            }

            return $this->response->setJSON([
                'success' => true,
                'permission' => $permission
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat detail permission.'
            ]);
        }
    }

    /**
     * Get DataTable data for permissions
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
            $filter = $this->request->getPost('filter') ?? 'all';

            // Column mapping untuk DataTable
            $columns = ['key_name', 'display_name', 'description', 'module', 'actions'];
            $orderByColumn = $columns[$orderColumn] ?? 'key_name';

            // Build query - gunakan field baru
            $builder = $this->db->table('permissions');
            $builder->select('id, key_name, display_name, description, module, page, action, is_active');

            // Filter berdasarkan tipe
            if ($filter === 'active') {
                $builder->where('is_active', 1);
            } elseif ($filter === 'inactive') {
                $builder->where('is_active', 0);
            } elseif ($filter === 'module') {
                // Ambil daftar module unik
                $modules = $this->db->table('permissions')
                    ->select('module')
                    ->groupBy('module')
                    ->orderBy('module', 'asc')
                    ->get()->getResultArray();

                $data = [];
                foreach ($modules as $mod) {
                    $data[] = [
                        'module' => strtoupper($mod['module']),
                        'actions' => '<button class="btn btn-sm btn-primary" onclick="toggleModulePermissions(\'' . $mod['module'] . '\', this)"><i class="fas fa-plus"></i></button>'
                    ];
                }

                return $this->response->setJSON([
                    'draw' => $draw,
                    'recordsTotal' => count($data),
                    'recordsFiltered' => count($data),
                    'data' => $data
                ]);
            }
            // jika 'all' tidak ada kondisi tambahan

            // Search
            if (!empty($searchValue)) {
                $builder->groupStart();
                $builder->like('key_name', $searchValue);
                $builder->orLike('display_name', $searchValue);
                $builder->orLike('description', $searchValue);
                $builder->orLike('module', $searchValue);
                $builder->orLike('page', $searchValue);
                $builder->orLike('action', $searchValue);
                $builder->groupEnd();
            }

            // Get total count
            $totalRecords = $this->db->table('permissions')->countAllResults();
            
            // Clone untuk filtered count
            $tempBuilder = clone $builder;
            $filteredRecords = $tempBuilder->countAllResults(false);

            // Apply ordering - hindari order by actions
            if ($orderByColumn !== 'actions' && in_array($orderByColumn, ['key_name', 'display_name', 'description', 'module'])) {
                $builder->orderBy($orderByColumn, $orderDir);
            } else {
                // Default ordering untuk filter module
                if ($filter === 'module') {
                    $builder->orderBy('module', 'asc');
                    $builder->orderBy('key_name', 'asc');
                } else {
                    $builder->orderBy('key_name', 'asc');
                }
            }
            
            // Apply pagination
            if ($length > 0) {
                $builder->limit($length, $start);
            }
            
            $permissions = $builder->get()->getResultArray();

            // Format data untuk DataTable - return sebagai indexed array
            $data = [];
            $currentModule = '';
            
            foreach ($permissions as $permission) {
                $statusBadge = ($permission['is_active'] ?? 1) ?
                    '<span class="badge bg-success">Active</span>' :
                    '<span class="badge bg-secondary">Inactive</span>';

                $actions = '
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-info btn-sm" onclick="editPermission(' . $permission['id'] . ')" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="deletePermission(' . $permission['id'] . ')" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>';

                // Untuk filter module, tambahkan header module
                $keyDisplay = esc($permission['key_name']);
                $moduleDisplay = $permission['module'] ?? 'General';
                
                if ($filter === 'module') {
                    // Jika module berubah, tampilkan sebagai header
                    if ($currentModule !== $moduleDisplay) {
                        $currentModule = $moduleDisplay;
                        $keyDisplay = '<strong class="text-primary">' . ucfirst($moduleDisplay) . '</strong><br><span class="ms-3">' . esc($permission['key_name']) . '</span>';
                    } else {
                        $keyDisplay = '<span class="ms-4">' . esc($permission['key_name']) . '</span>';
                    }
                }

                // Return sebagai indexed array, bukan associative - sesuai dengan kolom tabel baru
                $data[] = [
                    esc($permission['display_name'] ?? $permission['key_name']), // column 0: Display Name
                    '<code>' . esc($permission['key_name']) . '</code>', // column 1: Key Name
                    '<span class="badge bg-primary">' . esc($moduleDisplay) . '</span>', // column 2: Module
                    esc($permission['page'] ?? '-'), // column 3: Page
                    '<span class="badge bg-info">' . esc($permission['action'] ?? '-') . '</span>', // column 4: Action
                    esc($permission['description'] ?? '-'), // column 5: Description
                    $actions // column 6: Actions
                ];
            }

            return $this->response->setJSON([
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            log_message('error', 'DataTable Permission Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'draw' => intval($this->request->getPost('draw') ?? 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Gagal memuat data permission. Silakan coba lagi.'
            ])->setStatusCode(500);
        }
    }

    /**
     * Get permission counts for stats/cards/tabs
     */
    public function getCounts()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        try {
            $stats = $this->getPermissionStats();
            return $this->response->setJSON($stats);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'total' => 0,
                'modules' => 0,
                'system' => 0,
                'custom' => 0
            ]);
        }
    }

    public function byModule($module)
    {
        $permissions = $this->db->table('permissions')
            ->where('module', $module)
            ->orderBy('key', 'asc')
            ->get()->getResultArray();

        return $this->response->setJSON(['permissions' => $permissions]);
    }

    /**
     * Get permission usage details
     */
    public function usage($permissionId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        try {
            $permission = $this->permissionModel->find($permissionId);
            if (!$permission) {
                return $this->response->setJSON(['success' => false, 'message' => 'Permission tidak ditemukan']);
            }

            // Get roles using this permission
            $roles = $this->db->table('role_permissions rp')
                ->join('roles r', 'r.id = rp.role_id')
                ->where('rp.permission_id', $permissionId)
                ->select('r.id, r.name, r.description')
                ->get()->getResultArray();

            // Get users with direct permission assignments
            $users = $this->db->table('user_permissions up')
                ->join('users u', 'u.id = up.user_id')
                ->where('up.permission_id', $permissionId)
                ->select('u.id, u.first_name, u.last_name, u.email, up.granted')
                ->get()->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'permission' => $permission,
                'roles' => $roles,
                'users' => $users
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat data penggunaan permission.'
            ]);
        }
    }

    /**
     * Helper Methods
     */
    protected function getPermissionsWithStats()
    {
        $permissions = $this->permissionModel->findAll();

        foreach ($permissions as &$permission) {
            // Get role count
            try {
                $roleCount = $this->db->table('role_permissions')
                    ->where('permission_id', $permission['id'])
                    ->countAllResults();
                $permission['role_count'] = $roleCount;
            } catch (\Exception $e) {
                $permission['role_count'] = 0;
            }

            // Get user override count
            try {
                $userCount = $this->db->table('user_permissions')
                    ->where('permission_id', $permission['id'])
                    ->countAllResults();
                $permission['user_override_count'] = $userCount;
            } catch (\Exception $e) {
                $permission['user_override_count'] = 0;
            }

            // Ensure required fields - update untuk field baru
            $permission['key_name'] = $permission['key_name'] ?? '';
            $permission['display_name'] = $permission['display_name'] ?? $permission['key_name'];
            $permission['description'] = $permission['description'] ?? '';
            $permission['module'] = $permission['module'] ?? '';
            $permission['page'] = $permission['page'] ?? '';
            $permission['action'] = $permission['action'] ?? '';
            $permission['is_active'] = $permission['is_active'] ?? 1;
        }

        return $permissions;
    }

    private function getPermissionStats()
    {
        try {
            $total = $this->db->table('permissions')->countAllResults();
            
            $active = $this->db->table('permissions')
                ->where('is_active', 1)
                ->countAllResults();
            
            $inactive = $this->db->table('permissions')
                ->where('is_active', 0)
                ->countAllResults();
            
            $modules = $this->db->table('permissions')
                ->select('module')
                ->where('module IS NOT NULL')
                ->where('module !=', '')
                ->groupBy('module')
                ->countAllResults();
            
            $pages = $this->db->table('permissions')
                ->select('page')
                ->where('page IS NOT NULL')
                ->where('page !=', '')
                ->groupBy('page')
                ->countAllResults();

            return [
                'total' => $total,
                'active' => $active,
                'inactive' => $inactive,
                'modules' => $modules,
                'pages' => $pages
            ];
        } catch (\Exception $e) {
            log_message('error', 'Permission Stats Error. Silakan coba lagi.');
            return [
                'total' => 0,
                'system' => 0,
                'custom' => 0,
                'modules' => 0
            ];
        }
    }

    // hasPermission method removed - using BaseController's protected method instead
}