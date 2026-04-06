<?php
// Global Permission Helper - Simple & Universal
if (!function_exists('get_global_permission')) {
    function get_global_permission($module) {
        helper('permission');

        // Use CI4 session service (more reliable than $_SESSION with DB driver)
        $username = session()->get('username') ?? ($_SESSION['username'] ?? null);
        $role = strtolower((string)(session()->get('role') ?? ($_SESSION['role'] ?? '')));

        // Super admin style roles keep full access.
        if (in_array($role, ['super_admin', 'superadmin', 'administrator', 'admin'], true) || $username === 'super_admin') {
            return [
                'view' => true,
                'create' => true,
                'edit' => true,
                'delete' => true,
                'export' => true
            ];
        }

        // Module-specific admin roles: bypass for their own module (session-based)
        $moduleAdminRoles = [
            'service'    => ['admin_service_pusat', 'admin_service_area', 'head_service', 'supervisor_service', 'manager-service-area', 'staff_service'],
            'marketing'  => ['head_marketing', 'staff_marketing'],
            'purchasing' => ['head_purchasing', 'staff_purchasing'],
            'warehouse'  => ['head_warehouse', 'staff_warehouse'],
        ];
        if (isset($moduleAdminRoles[$module]) && in_array($role, $moduleAdminRoles[$module], true)) {
            return [
                'view'   => true,
                'create' => true,
                'edit'   => true,
                'delete' => true,
                'export' => true
            ];
        }

        // DB-based fallback: check role slug directly in database (works even with stale session)
        $userId = session()->get('user_id');
        if ($userId && isset($moduleAdminRoles[$module])) {
            try {
                $db = \Config\Database::connect();
                $slugs = $moduleAdminRoles[$module];
                $inList = implode(',', array_map(fn($s) => "'" . str_replace("'", "''", $s) . "'", $slugs));
                $row = $db->query("
                    SELECT r.slug FROM user_roles ur
                    JOIN roles r ON r.id = ur.role_id
                    WHERE ur.user_id = ? AND ur.is_active = 1
                    AND r.slug IN ({$inList})
                    LIMIT 1
                ", [$userId])->getRowArray();
                if ($row) {
                    return [
                        'view'   => true,
                        'create' => true,
                        'edit'   => true,
                        'delete' => true,
                        'export' => true
                    ];
                }
            } catch (\Throwable $e) {
                log_message('error', 'get_global_permission DB fallback failed: ' . $e->getMessage());
            }
        }

        $moduleKeyMap = [
            'marketing' => [
                'view' => ['marketing.quotation.navigation', 'marketing.customer.navigation', 'marketing.spk.navigation', 'marketing.delivery_instructions.navigation'],
                'create' => ['marketing.quotation.create', 'marketing.customer.create', 'marketing.spk.create', 'marketing.delivery_instructions.create'],
                'edit' => ['marketing.quotation.edit', 'marketing.customer.edit', 'marketing.spk.edit', 'marketing.delivery_instructions.edit'],
                'delete' => ['marketing.quotation.delete', 'marketing.customer.delete', 'marketing.spk.delete'],
                'export' => ['marketing.quotation.export', 'marketing.customer.export', 'marketing.contract.export', 'export.kontrak', 'export.customer']
            ],
            'service' => [
                'view' => ['service.work_order.navigation', 'service.spk_service.navigation', 'service.pmps.navigation', 'service.area_management.navigation'],
                'create' => ['service.work_order.create', 'service.pmps.create', 'service.area_management.create'],
                'edit' => ['service.work_order.edit', 'service.pmps.edit', 'service.area_management.edit'],
                'delete' => ['service.work_order.delete'],
                'export' => ['service.work_order.export', 'export.workorder', 'export.service_employee', 'export.service_area']
            ],
            'purchasing' => [
                'view' => ['purchasing.purchasing.navigation', 'purchasing.po_sparepart.navigation'],
                'create' => ['purchasing.purchasing.create', 'purchasing.po_sparepart.create'],
                'edit' => ['purchasing.purchasing.edit', 'purchasing.po_sparepart.edit'],
                'delete' => ['purchasing.purchasing.delete', 'purchasing.po_sparepart.delete'],
                'export' => ['purchasing.purchasing.export', 'purchasing.po_sparepart.export', 'export.purchasing_progres', 'export.purchasing_delivery', 'export.purchasing_completed']
            ],
            'warehouse' => [
                'view' => ['warehouse.inventory_unit.navigation', 'warehouse.attachment_inventory.navigation', 'warehouse.sparepart_inventory.navigation', 'warehouse.po_verification.navigation', 'warehouse.movements.navigation'],
                'create' => ['warehouse.inventory_unit.create', 'warehouse.attachment_inventory.create', 'warehouse.sparepart_inventory.create'],
                'edit' => ['warehouse.inventory_unit.edit', 'warehouse.sparepart_usage.create'],
                'delete' => ['warehouse.inventory_unit.delete'],
                'export' => ['warehouse.inventory_unit.export', 'export.inventory_unit', 'export.inventory_attachment', 'export.inventory_battery', 'export.inventory_charger']
            ],
            'accounting' => [
                'view' => ['accounting.invoice.navigation', 'accounting.payment_validation.navigation'],
                'create' => ['accounting.invoice.create'],
                'edit' => ['accounting.invoice.edit', 'accounting.payment_validation.validate'],
                'delete' => ['accounting.invoice.delete'],
                'export' => ['accounting.invoice.export']
            ],
            'operational' => [
                'view' => ['operational.delivery_process.navigation', 'operational.tracking.view'],
                'create' => ['operational.delivery_process.create'],
                'edit' => ['operational.delivery_process.edit'],
                'delete' => ['operational.delivery_process.delete'],
                'export' => ['operational.delivery_process.export']
            ],
            'perizinan' => [
                'view' => ['perizinan.silo.navigation', 'perizinan.emisi.navigation'],
                'create' => ['perizinan.silo.create', 'perizinan.emisi.create'],
                'edit' => ['perizinan.silo.edit', 'perizinan.emisi.edit'],
                'delete' => ['perizinan.silo.delete', 'perizinan.emisi.delete'],
                'export' => ['perizinan.silo.export']
            ]
        ];

        $fallbackMap = [
            'view' => ["{$module}.access", "{$module}.view", "{$module}.index", "{$module}.navigation"],
            'create' => ["{$module}.create", "{$module}.manage"],
            'edit' => ["{$module}.edit", "{$module}.manage"],
            'delete' => ["{$module}.delete"],
            'export' => ["{$module}.export"]
        ];

        $result = [
            'view' => false,
            'create' => false,
            'edit' => false,
            'delete' => false,
            'export' => false
        ];

        $module = strtolower((string)$module);
        $map = $moduleKeyMap[$module] ?? $fallbackMap;

        foreach ($result as $action => $allowed) {
            $keys = $map[$action] ?? $fallbackMap[$action];
            foreach ($keys as $key) {
                if (hasPermission($key)) {
                    $result[$action] = true;
                    break;
                }
            }
        }

        // If user can navigate module but no explicit view key hit, keep view true.
        if (!$result['view'] && function_exists('hasModuleAccess') && hasModuleAccess($module)) {
            $result['view'] = true;
        }

        return $result;
    }
}

// Global permission check function
if (!function_exists('can_global')) {
    function can_global($module, $action) {
        $permissions = get_global_permission($module);
        return $permissions[$action] ?? false;
    }
}
?>
