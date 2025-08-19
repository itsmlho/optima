<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\PermissionModel;
use App\Models\RoleModel;
use App\Models\DivisionModel;
use App\Models\PositionModel;
use App\Models\ActivityLogModel;

class SetupPermissionSystem extends BaseCommand
{
    protected $group       = 'Permission';
    protected $name        = 'permission:setup';
    protected $description = 'Setup awal sistem permission dengan data default';

    public function run(array $params)
    {
        CLI::write('🚀 Memulai setup sistem permission...', 'green');

        try {
            // 1. Create default permissions
            CLI::write('📝 Membuat default permissions...', 'yellow');
            $this->createDefaultPermissions();
            CLI::write('✅ Default permissions berhasil dibuat', 'green');

            // 2. Create default roles
            CLI::write('👥 Membuat default roles...', 'yellow');
            $this->createDefaultRoles();
            CLI::write('✅ Default roles berhasil dibuat', 'green');

            // 3. Create default divisions
            CLI::write('🏢 Membuat default divisions...', 'yellow');
            $this->createDefaultDivisions();
            CLI::write('✅ Default divisions berhasil dibuat', 'green');

            // 4. Create default positions
            CLI::write('💼 Membuat default positions...', 'yellow');
            $this->createDefaultPositions();
            CLI::write('✅ Default positions berhasil dibuat', 'green');

            // 5. Create super admin user
            CLI::write('👑 Membuat super admin user...', 'yellow');
            $this->createSuperAdmin();
            CLI::write('✅ Super admin user berhasil dibuat', 'green');

            CLI::write('🎉 Setup sistem permission selesai!', 'green');
            CLI::write('Email: admin@optima.com', 'cyan');
            CLI::write('Password: admin123', 'cyan');

        } catch (\Exception $e) {
            CLI::error('❌ Error: ' . $e->getMessage());
            return;
        }
    }

    private function createDefaultPermissions()
    {
        $permissionModel = new PermissionModel();
        $permissionModel->createDefaultPermissions();
    }

    private function createDefaultRoles()
    {
        $roleModel = new RoleModel();
        $permissionModel = new PermissionModel();

        $presetRoles = [
            [
                'name' => 'Super Administrator',
                'description' => 'Akses penuh ke seluruh sistem',
                'is_preset' => 1,
                'permissions' => [
                    'dashboard.view', 'dashboard.export',
                    'users.view', 'users.create', 'users.edit', 'users.delete',
                    'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
                    'permissions.view', 'permissions.manage',
                    'divisions.view', 'divisions.create', 'divisions.edit', 'divisions.delete',
                    'projects.view', 'projects.create', 'projects.edit', 'projects.delete',
                    'rentals.view', 'rentals.create', 'rentals.edit', 'rentals.delete', 'rentals.approve',
                    'forklifts.view', 'forklifts.create', 'forklifts.edit', 'forklifts.delete',
                    'reports.view', 'reports.export',
                    'settings.view', 'settings.edit',
                    'logs.view', 'logs.export'
                ]
            ],
            [
                'name' => 'Management',
                'description' => 'Akses penuh ke data lintas divisi kecuali panel kontrol permission',
                'is_preset' => 1,
                'permissions' => [
                    'dashboard.view', 'dashboard.export',
                    'users.view', 'users.create', 'users.edit',
                    'divisions.view',
                    'projects.view', 'projects.create', 'projects.edit',
                    'rentals.view', 'rentals.create', 'rentals.edit', 'rentals.approve',
                    'forklifts.view', 'forklifts.create', 'forklifts.edit',
                    'reports.view', 'reports.export',
                    'settings.view',
                    'logs.view'
                ]
            ],
            [
                'name' => 'Head Divisi',
                'description' => 'Akses terbatas pada divisinya dengan kemampuan filter data',
                'is_preset' => 1,
                'permissions' => [
                    'dashboard.view',
                    'users.view',
                    'divisions.view',
                    'projects.view', 'projects.create', 'projects.edit',
                    'rentals.view', 'rentals.create', 'rentals.edit', 'rentals.approve',
                    'forklifts.view', 'forklifts.create', 'forklifts.edit',
                    'reports.view',
                    'logs.view'
                ]
            ],
            [
                'name' => 'Admin',
                'description' => 'Akses terbatas hanya pada data yang ditandai dengan identifikasi admin',
                'is_preset' => 1,
                'permissions' => [
                    'dashboard.view',
                    'rentals.view', 'rentals.create', 'rentals.edit',
                    'forklifts.view', 'forklifts.create', 'forklifts.edit',
                    'reports.view'
                ]
            ],
            [
                'name' => 'Staff',
                'description' => 'Akses sangat terbatas, hanya pada data dan fitur yang diberikan',
                'is_preset' => 1,
                'permissions' => [
                    'dashboard.view',
                    'rentals.view', 'rentals.create',
                    'forklifts.view'
                ]
            ]
        ];

        foreach ($presetRoles as $presetRole) {
            $permissions = $presetRole['permissions'];
            unset($presetRole['permissions']);

            // Get permission IDs
            $permissionIds = [];
            foreach ($permissions as $permissionKey) {
                $permission = $permissionModel->where('key', $permissionKey)->first();
                if ($permission) {
                    $permissionIds[] = $permission['permission_id'];
                }
            }

            $roleModel->createRoleWithPermissions($presetRole, $permissionIds);
        }
    }

    private function createDefaultDivisions()
    {
        $divisionModel = new DivisionModel();

        $divisions = [
            [
                'name' => 'Finance',
                'description' => 'Divisi Keuangan'
            ],
            [
                'name' => 'Human Resources',
                'description' => 'Divisi Sumber Daya Manusia'
            ],
            [
                'name' => 'Information Technology',
                'description' => 'Divisi Teknologi Informasi'
            ],
            [
                'name' => 'Operations',
                'description' => 'Divisi Operasional'
            ],
            [
                'name' => 'Marketing',
                'description' => 'Divisi Pemasaran'
            ],
            [
                'name' => 'Sales',
                'description' => 'Divisi Penjualan'
            ],
            [
                'name' => 'Customer Service',
                'description' => 'Divisi Layanan Pelanggan'
            ],
            [
                'name' => 'Legal',
                'description' => 'Divisi Hukum'
            ]
        ];

        foreach ($divisions as $division) {
            $divisionModel->insert($division);
        }
    }

    private function createDefaultPositions()
    {
        $positionModel = new PositionModel();

        $positions = [
            // Management Level (1-3)
            ['name' => 'CEO', 'level' => 1, 'description' => 'Chief Executive Officer'],
            ['name' => 'Director', 'level' => 2, 'description' => 'Direktur'],
            ['name' => 'Manager', 'level' => 3, 'description' => 'Manajer'],
            
            // Head Division Level (4-5)
            ['name' => 'Head of Division', 'level' => 4, 'description' => 'Kepala Divisi'],
            ['name' => 'Assistant Manager', 'level' => 5, 'description' => 'Asisten Manajer'],
            
            // Admin Level (6-7)
            ['name' => 'Senior Admin', 'level' => 6, 'description' => 'Admin Senior'],
            ['name' => 'Admin', 'level' => 7, 'description' => 'Admin'],
            
            // Staff Level (8-10)
            ['name' => 'Senior Staff', 'level' => 8, 'description' => 'Staff Senior'],
            ['name' => 'Staff', 'level' => 9, 'description' => 'Staff'],
            ['name' => 'Junior Staff', 'level' => 10, 'description' => 'Staff Junior']
        ];

        foreach ($positions as $position) {
            $positionModel->insert($position);
        }
    }

    private function createSuperAdmin()
    {
        $userModel = new \App\Models\UserModel();
        $roleModel = new RoleModel();
        $divisionModel = new DivisionModel();
        $positionModel = new PositionModel();

        // Create super admin user
        $superAdminData = [
            'name' => 'Super Administrator',
            'email' => 'admin@optima.com',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'phone' => '081234567890',
            'address' => 'Jakarta, Indonesia',
            'is_active' => 1
        ];

        $userId = $userModel->insert($superAdminData);

        if ($userId) {
            // Assign Super Administrator role
            $superAdminRole = $roleModel->where('name', 'Super Administrator')->first();
            if ($superAdminRole) {
                $userModel->db->table('user_roles')->insert([
                    'user_id' => $userId,
                    'role_id' => $superAdminRole['role_id']
                ]);
            }

            // Assign to IT division
            $itDivision = $divisionModel->where('name', 'Information Technology')->first();
            if ($itDivision) {
                $divisionModel->addUserToDivision($userId, $itDivision['division_id'], true);
            }

            // Assign CEO position
            $ceoPosition = $positionModel->where('name', 'CEO')->first();
            if ($ceoPosition) {
                $positionModel->addUserToPosition($userId, $ceoPosition['position_id']);
            }
        }
    }
} 