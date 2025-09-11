<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    // Allowed fields untuk sistem advanced user management
    protected $allowedFields = [
        'first_name', 'last_name', 'username', 'email', 'password_hash', 
        'phone', 'department', 'position', 'is_active', 'last_login', 
        'remember_token', 'reset_token', 'reset_expires', 'is_super_admin',
        'division_id', 'email_verified_at', 'created_at', 'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation untuk sistem advanced user management
    protected $validationRules = [
        'first_name' => 'required|min_length[2]|max_length[50]',
        'last_name'  => 'permit_empty|max_length[50]',
        'username'   => 'required|max_length[100]|is_unique[users.username,id,{id}]',
        'email'      => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password'   => 'permit_empty|min_length[6]',
        'is_active'  => 'permit_empty|in_list[0,1]',
        'department' => 'permit_empty|max_length[100]',
        'position'   => 'permit_empty|max_length[100]',
        'phone'      => 'permit_empty|max_length[20]'
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'Email sudah terdaftar dalam sistem.'
        ],
        'username' => [
            'is_unique' => 'Username sudah terdaftar dalam sistem.'
        ]
    ];

    /**
     * Mendapatkan user dengan password untuk autentikasi
     */
    public function getUserWithPassword(string $loginIdentifier): ?array
    {
        return $this->where('email', $loginIdentifier)
                    ->orWhere('username', $loginIdentifier)
                    ->where('is_active', 1)
                    ->first();
    }

    /**
     * Mendapatkan user dengan role dan permissions untuk sistem advanced
     */
    public function getUserWithRoles(int $userId): ?array
    {
        $userRoleModel = new \App\Models\UserRoleModel();
        // $userDivisionModel = new \App\Models\UserDivisionModel(); // Disabled - using user_roles
        
        $user = $this->find($userId);
        if (!$user) return null;

        // Get user roles
        $user['roles'] = $userRoleModel->getUserRoles($userId);
        
        // Get user divisions from user_roles
        try {
            $divisions = $this->db->table('user_roles ur')
                ->join('divisions d', 'd.id = ur.division_id')
                ->where('ur.user_id', $userId)
                ->where('ur.division_id IS NOT NULL')
                ->select('d.id, d.name, d.code')
                ->get()->getResultArray();
            $user['divisions'] = $divisions;
        } catch (\Exception $e) {
            $user['divisions'] = [];
        }

        return $user;
    }

    /**
     * Set remember token untuk user
     */
    public function setRememberToken(int $userId, string $token): bool
    {
        return $this->update($userId, ['remember_token' => $token]);
    }

    /**
     * Clear remember token
     */
    public function clearRememberToken(int $userId): bool
    {
        return $this->update($userId, ['remember_token' => null]);
    }

    /**
     * Find user by remember token
     */
    public function findByRememberToken(string $token): ?array
    {
        return $this->where('remember_token', $token)
                    ->where('is_active', 1)
                    ->first();
    }


    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['hashPassword'];
    protected $beforeUpdate   = ['hashPasswordIfProvided']; // Mengubah nama callback untuk update

    /**
     * Override insert method to ensure password is hashed
     */
    public function insert($data = null, bool $returnID = true)
    {
        // Ensure password is hashed before insert
        if (is_array($data) && isset($data['password']) && !empty($data['password'])) {
            log_message('debug', 'UserModel: Manual password hashing in insert()');
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            unset($data['password']); // Remove plain password
            log_message('debug', 'UserModel: Password hashed, length: ' . strlen($data['password_hash']));
        }
        
        return parent::insert($data, $returnID);
    }

    /**
     * Override update method to ensure password is hashed if provided
     */
    public function update($id = null, $data = null): bool
    {
        // Ensure password is hashed before update if provided
        if (is_array($data) && isset($data['password']) && !empty($data['password'])) {
            log_message('debug', 'UserModel: Manual password hashing in update()');
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            unset($data['password']); // Remove plain password
            log_message('debug', 'UserModel: Password hashed, length: ' . strlen($data['password_hash']));
        } elseif (is_array($data) && isset($data['password'])) {
            // Remove empty password field
            unset($data['password']);
        }
        
        return parent::update($id, $data);
    }

    /**
     * Callback untuk menghash password sebelum insert
     */
    protected function hashPassword(array $data)
    {
        log_message('debug', 'UserModel: hashPassword callback called');
        log_message('debug', 'UserModel: Data received: ' . json_encode($data));
        
        if (isset($data['data']['password']) && !empty($data['data']['password'])) {
            log_message('debug', 'UserModel: Password found, hashing...');
            $data['data']['password_hash'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
            log_message('debug', 'UserModel: Password hashed, length: ' . strlen($data['data']['password_hash']));
            unset($data['data']['password']); // Remove plain password
        } else {
            log_message('debug', 'UserModel: No password to hash');
        }
        
        log_message('debug', 'UserModel: Final data: ' . json_encode($data));
        return $data;
    }
    
    /**
     * Callback untuk menghash password hanya jika password baru disediakan saat update
     */
    protected function hashPasswordIfProvided(array $data)
    {
        if (isset($data['data']['password']) && !empty($data['data']['password'])) {
            $data['data']['password_hash'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
            unset($data['data']['password']); // Remove plain password
        } else {
            unset($data['data']['password']);
        }
        return $data;
    }

    /**
     * Mengambil semua users dengan informasi tambahan untuk management
     */
    public function getAllUsersForManagement(): array
    {
        return $this->select('users.*, 
                            COALESCE(GROUP_CONCAT(DISTINCT d.name SEPARATOR ", "), "-") as divisions,
                            COALESCE(GROUP_CONCAT(DISTINCT r.name SEPARATOR ", "), "-") as roles')
                    ->join('user_roles ur', 'ur.user_id = users.id', 'left')
                    ->join('divisions d', 'd.id = ur.division_id', 'left')
                    ->join('roles r', 'r.id = ur.role_id', 'left')
                    ->groupBy('users.id')
                    ->orderBy('users.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin(int $userId): bool
    {
        return $this->update($userId, ['last_login' => date('Y-m-d H:i:s')]);
    }

    /**
     * Mengupdate status pengguna (active/inactive)
     */
    public function updateStatus(int $userId, bool $isActive): bool
    {
        return $this->update($userId, ['is_active' => $isActive ? 1 : 0]);
    }

    /**
     * Get users with advanced filtering
     */
    public function getUsersWithFilter(array $filters): array
    {
        $builder = $this->builder();
        $builder->select('users.*, 
                         COALESCE(GROUP_CONCAT(DISTINCT d.name SEPARATOR ", "), "-") as divisions,
                         COALESCE(GROUP_CONCAT(DISTINCT r.name SEPARATOR ", "), "-") as roles');
        
        $builder->join('user_roles ur', 'ur.user_id = users.id', 'left')
                ->join('divisions d', 'd.id = ur.division_id', 'left')
                ->join('roles r', 'r.id = ur.role_id', 'left');

        if (!empty($filters['status'])) {
            $builder->where('users.is_active', $filters['status'] == 'active' ? 1 : 0);
        }
        
        if (!empty($filters['division_id'])) {
            $builder->where('ur.division_id', $filters['division_id']);
        }
        
        if (!empty($filters['role_id'])) {
            $builder->where('ur.role_id', $filters['role_id']);
        }
        
        if (!empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $builder->groupStart()
                    ->like('users.first_name', $search)
                    ->orLike('users.last_name', $search)
                    ->orLike('users.email', $search)
                    ->orLike('users.username', $search)
                    ->groupEnd();
        }

        $builder->groupBy('users.id');
        return $builder->get()->getResultArray();
    }

    public function getUsersByDivision($divisionId)
    {
        // Query ini akan mengambil semua pengguna yang terhubung ke divisi tertentu
        return $this->select('users.*')
                    ->join('user_roles', 'user_roles.user_id = users.id')
                    ->where('user_roles.division_id', $divisionId)
                    ->findAll();
    }

    /**
     * Get users by roles (for notification targeting)
     */
    public function getUsersByRoles($roles)
    {
        if (!is_array($roles)) {
            $roles = [$roles];
        }

        return $this->select('users.id, users.first_name, users.last_name, users.email')
                    ->join('user_roles ur', 'ur.user_id = users.id')
                    ->join('roles r', 'r.id = ur.role_id')
                    ->whereIn('r.name', $roles)
                    ->where('users.is_active', 1)
                    ->groupBy('users.id')
                    ->findAll();
    }

    /**
     * Get users by divisions (for notification targeting)
     */
    public function getUsersByDivisions($divisions)
    {
        if (!is_array($divisions)) {
            $divisions = [$divisions];
        }

        return $this->select('users.id, users.first_name, users.last_name, users.email, divisions.name as division_name')
                    ->join('divisions', 'divisions.id = users.division_id', 'left')
                    ->whereIn('divisions.name', $divisions)
                    ->where('users.is_active', 1)
                    ->findAll();
    }

    /**
     * Get users by departments (for notification targeting)
     */
    public function getUsersByDepartments($departments)
    {
        if (!is_array($departments)) {
            $departments = [$departments];
        }

        return $this->select('users.id, users.first_name, users.last_name, users.email')
                    ->whereIn('users.department', $departments)
                    ->where('users.is_active', 1)
                    ->findAll();
    }

    /**
     * Get users by division AND department combination (for specific targeting)
     */
    public function getUsersByDivisionAndDepartment($division, $department)
    {
        return $this->select('users.id, users.first_name, users.last_name, users.email')
                    ->join('user_roles ur', 'ur.user_id = users.id')
                    ->join('divisions d', 'd.id = ur.division_id')
                    ->where('d.name', $division)
                    ->where('users.department', $department)
                    ->where('users.is_active', 1)
                    ->groupBy('users.id')
                    ->findAll();
    }

    /**
     * Get all managers (for escalation notifications)
     */
    public function getManagers()
    {
        return $this->select('users.id, users.first_name, users.last_name, users.email')
                    ->join('user_roles ur', 'ur.user_id = users.id')
                    ->join('roles r', 'r.id = ur.role_id')
                    ->where('r.name', 'manager')
                    ->where('users.is_active', 1)
                    ->groupBy('users.id')
                    ->findAll();
    }

    /**
     * Get supervisors for specific division
     */
    public function getSupervisorsByDivision($division)
    {
        return $this->select('users.id, users.first_name, users.last_name, users.email')
                    ->join('user_roles ur', 'ur.user_id = users.id')
                    ->join('roles r', 'r.id = ur.role_id')
                    ->join('divisions d', 'd.id = ur.division_id')
                    ->where('r.name', 'supervisor')
                    ->where('d.name', $division)
                    ->where('users.is_active', 1)
                    ->groupBy('users.id')
                    ->findAll();
    }
}
