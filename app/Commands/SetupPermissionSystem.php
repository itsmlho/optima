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
            // 0. Scan codebase for permission keys and sync DB
            CLI::write('🔍 Mensinkronkan permissions dari kode...', 'yellow');
            $this->syncPermissionsFromCode();
            CLI::write('✅ Sinkronisasi permissions dari kode selesai', 'green');
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

    /**
     * Scan controller files for hasPermission('key') usage
     * and ensure the permission keys exist in DB.
     */
    private function syncPermissionsFromCode(): void
    {
        $permissionModel = new PermissionModel();
        $basePath = realpath(APPPATH . 'Controllers');
        if (!$basePath) return;

        $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($basePath));
        $foundKeys = [];
        foreach ($rii as $file) {
            if ($file->isDir()) continue;
            if (pathinfo($file->getFilename(), PATHINFO_EXTENSION) !== 'php') continue;
            $content = @file_get_contents($file->getPathname());
            if ($content === false) continue;
            // Match hasPermission('key') and hasPermission("key")
            if (preg_match_all("/hasPermission\(\s*['\"]([^'\"]+)['\"]\s*\)/", $content, $m)) {
                foreach ($m[1] as $key) {
                    $foundKeys[$key] = true;
                }
            }
        }

        if (empty($foundKeys)) return;

        foreach (array_keys($foundKeys) as $key) {
            $exists = $permissionModel->where('key', $key)->first();
            if ($exists) continue;
            // Try infer module & category from key prefix
            $module = 'system';
            if (str_starts_with($key, 'marketing.')) $module = 'marketing';
            elseif (str_starts_with($key, 'service.')) $module = 'service';
            elseif (str_starts_with($key, 'purchasing.')) $module = 'purchasing';
            elseif (str_starts_with($key, 'warehouse.') || str_starts_with($key, 'inventory.') || str_starts_with($key, 'export.inventory')) $module = 'warehouse';
            elseif (str_starts_with($key, 'admin.')) $module = 'admin';
            elseif (str_starts_with($key, 'export.')) $module = 'export';

            $category = 'access';
            if (str_contains($key, '.create')) $category = 'create';
            elseif (str_contains($key, '.edit')) $category = 'edit';
            elseif (str_contains($key, '.delete')) $category = 'delete';
            elseif (str_starts_with($key, 'export.')) $category = 'export';
            elseif (str_contains($key, '.view')) $category = 'view';

            $permissionModel->insert([
                'key' => $key,
                'name' => strtoupper(str_replace('.', ' ', $key)),
                'description' => 'Auto-synced from code usage',
                'module' => $module,
                'category' => $category,
                'is_system_permission' => 1,
                'is_active' => 1
            ]);
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

        // Roles requested by company
        $roles = [
            'Super Administrator',
            'Head Marketing', 'Staff Marketing',
            'Head Operational', 'Staff Operational',
            'Head Service Diesel', 'Staff Service Diesel' ,
            'Head Service Electric', 'Staff Service Electric',
            'Head Purchasing', 'Staff Purchasing',
            'Head Accounting', 'Staff Accounting',
            'Head HRD', 'Staff HRD',
            'Head Warehouse', 'Staff Warehouse'
        ];

        // Remove roles not in the new list (keep referential integrity by avoiding truncate)
        $roleModel->whereNotIn('name', $roles)->delete();

        // Ensure each role exists (idempotent)
        foreach ($roles as $roleName) {
            $existing = $roleModel->where('name', $roleName)->first();
            if (!$existing) {
                $roleModel->insert([
                    'name' => $roleName,
                    'description' => $roleName,
                    'is_preset' => 1
                ]);
                $existing = $roleModel->where('name', $roleName)->first();
            }

            // Assign export permissions for Head roles only
            if (str_starts_with($roleName, 'Head')) {
                $exportKeys = [
                    'export.customer', 'export.kontrak',
                    'export.service_area', 'export.service_employee', 'export.workorder',
                    'export.purchasing_progres', 'export.purchasing_delivery', 'export.purchasing_completed',
                    'export.inventory_unit', 'export.inventory_attachment', 'export.inventory_battery', 'export.inventory_charger'
                ];
                $permissionIds = [];
                foreach ($exportKeys as $key) {
                    $perm = $permissionModel->where('key', $key)->first();
                    if (!$perm) {
                        $permissionModel->insert([
                            'key' => $key,
                            'name' => strtoupper(str_replace('_',' ', $key)),
                            'description' => 'Export permission',
                            'module' => 'SYSTEM',
                            'category' => 'EXPORT',
                            'is_system_permission' => 1,
                            'is_active' => 1
                        ]);
                        $perm = $permissionModel->where('key', $key)->first();
                    }
                    if ($perm) $permissionIds[] = $perm['id'] ?? ($perm['permission_id'] ?? null);
                }
                $permissionIds = array_filter($permissionIds);
                if (!empty($permissionIds)) {
                    // attach to pivot
                    foreach ($permissionIds as $pid) {
                        $roleModel->db->table('role_permissions')->ignore(true)->insert([
                            'role_id' => $existing['role_id'] ?? $existing['id'] ?? null,
                            'permission_id' => $pid
                        ]);
                    }
                }
            }
        }
    }

    private function createDefaultDivisions()
    {
        $divisionModel = new DivisionModel();

        $divisions = [
            ['name' => 'Marketing', 'description' => 'Divisi Marketing'],
            ['name' => 'Operational', 'description' => 'Divisi Operational'],
            ['name' => 'Purchasing', 'description' => 'Divisi Purchasing'],
            ['name' => 'Warehouse', 'description' => 'Divisi Warehouse'],
            ['name' => 'Service Diesel', 'description' => 'Divisi Service Diesel'],
            ['name' => 'Service Electric', 'description' => 'Divisi Service Electric'],
            ['name' => 'Accounting', 'description' => 'Divisi Accounting'],
            ['name' => 'HRD', 'description' => 'Divisi HRD'],
            ['name' => 'HO', 'description' => 'Head Office']
        ];

        $allowed = array_column($divisions, 'name');
        // Remove divisions not in the new list
        $divisionModel->whereNotIn('name', $allowed)->delete();

        // Determine PK field (division_id vs id)
        $pk = 'division_id';
        $sample = $divisionModel->orderBy('name','ASC')->first();
        if ($sample && array_key_exists('id', $sample)) { $pk = 'id'; }

        // Upsert new divisions
        foreach ($divisions as $division) {
            $existing = $divisionModel->where('name', $division['name'])->first();
            if ($existing) {
                $divisionModel->update($existing[$pk], $division);
            } else {
                $divisionModel->insert($division);
            }
        }
    }

    private function createDefaultPositions()
    {
        $positionModel = new \App\Models\PositionModel();

        $positions = [
            // Management Level
            ['name' => 'CEO', 'code' => 'CEO', 'description' => 'Chief Executive Officer'],
            ['name' => 'Director', 'code' => 'DIR', 'description' => 'Direktur'],
            ['name' => 'Manager', 'code' => 'MGR', 'description' => 'Manajer'],
            
            // Department Heads
            ['name' => 'Head Marketing', 'code' => 'HMKT', 'description' => 'Kepala Divisi Marketing'],
            ['name' => 'Head Service', 'code' => 'HSVC', 'description' => 'Kepala Divisi Service'],
            ['name' => 'Head Purchasing', 'code' => 'HPUR', 'description' => 'Kepala Divisi Purchasing'],
            ['name' => 'Head Warehouse', 'code' => 'HWH', 'description' => 'Kepala Divisi Warehouse'],
            ['name' => 'Head Accounting', 'code' => 'HACC', 'description' => 'Kepala Divisi Accounting'],
            ['name' => 'Head HRD', 'code' => 'HHRD', 'description' => 'Kepala Divisi HRD'],
            
            // Staff Level
            ['name' => 'Staff Marketing', 'code' => 'SMKT', 'description' => 'Staff Divisi Marketing'],
            ['name' => 'Staff Service', 'code' => 'SSVC', 'description' => 'Staff Divisi Service'],
            ['name' => 'Staff Purchasing', 'code' => 'SPUR', 'description' => 'Staff Divisi Purchasing'],
            ['name' => 'Staff Warehouse', 'code' => 'SWH', 'description' => 'Staff Divisi Warehouse'],
            ['name' => 'Staff Accounting', 'code' => 'SACC', 'description' => 'Staff Divisi Accounting'],
            ['name' => 'Staff HRD', 'code' => 'SHRD', 'description' => 'Staff Divisi HRD'],
            
            // Operational Level
            ['name' => 'Technician', 'code' => 'TECH', 'description' => 'Teknisi'],
            ['name' => 'Operator', 'code' => 'OPR', 'description' => 'Operator'],
            ['name' => 'Helper', 'code' => 'HELP', 'description' => 'Helper']
        ];

        foreach ($positions as $position) {
            $positionModel->createOrUpdatePosition($position);
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