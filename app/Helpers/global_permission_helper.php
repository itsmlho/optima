<?php
// Global Permission Helper - Simple & Universal
if (!function_exists('get_global_permission')) {
    function get_global_permission($module) {
        helper('permission');

        $username = $_SESSION['username'] ?? null;
        $role = strtolower((string)($_SESSION['role'] ?? ''));

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

        $moduleKeyMap = [
            'marketing' => [
                'view' => ['marketing.quotation.navigation', 'marketing.customer.navigation', 'marketing.spk.navigation', 'marketing.delivery.navigation'],
                'create' => ['marketing.quotation.create', 'marketing.customer.create', 'marketing.spk.create', 'marketing.delivery.create'],
                'edit' => ['marketing.quotation.edit', 'marketing.customer.edit', 'marketing.spk.edit', 'marketing.delivery.edit'],
                'delete' => ['marketing.quotation.delete', 'marketing.customer.delete', 'marketing.spk.delete'],
                'export' => ['marketing.quotation.export', 'marketing.customer.export', 'marketing.contract.export', 'export.kontrak', 'export.customer']
            ],
            'service' => [
                'view' => ['service.workorder.navigation', 'service.work_order.navigation', 'service.pmps.navigation', 'service.area.navigation'],
                'create' => ['service.workorder.create', 'service.work_order.create', 'service.pmps.create', 'service.area.create'],
                'edit' => ['service.workorder.edit', 'service.work_order.edit', 'service.pmps.edit', 'service.area.edit'],
                'delete' => ['service.work_order.delete'],
                'export' => ['service.work_order.export', 'export.workorder', 'export.service_employee', 'export.service_area']
            ],
            'purchasing' => [
                'view' => ['purchasing.po.navigation', 'purchasing.po.index', 'purchasing.po_sparepart.navigation', 'purchasing.supplier.navigation'],
                'create' => ['purchasing.po.create', 'purchasing.po_sparepart.create', 'purchasing.supplier.create'],
                'edit' => ['purchasing.po.edit', 'purchasing.po_sparepart.edit', 'purchasing.supplier.edit'],
                'delete' => ['purchasing.po.delete', 'purchasing.po_sparepart.delete', 'purchasing.supplier.delete'],
                'export' => ['purchasing.po.export', 'purchasing.po_sparepart.export', 'export.purchasing_progres', 'export.purchasing_delivery', 'export.purchasing_completed']
            ],
            'warehouse' => [
                'view' => ['warehouse.unit_inventory.navigation', 'warehouse.attachment_inventory.navigation', 'warehouse.sparepart_inventory.navigation', 'warehouse.po_verification.navigation'],
                'create' => ['warehouse.unit_inventory.create', 'warehouse.attachment_inventory.create', 'warehouse.sparepart_inventory.create'],
                'edit' => ['warehouse.unit_inventory.edit', 'warehouse.sparepart_usage.create'],
                'delete' => ['warehouse.unit_inventory.delete'],
                'export' => ['warehouse.unit_inventory.export', 'export.inventory_unit', 'export.inventory_attachment', 'export.inventory_battery', 'export.inventory_charger']
            ],
            'accounting' => [
                'view' => ['accounting.invoice.navigation', 'accounting.payment.navigation'],
                'create' => ['accounting.invoice.create'],
                'edit' => ['accounting.invoice.edit', 'accounting.payment.validate'],
                'delete' => ['accounting.invoice.delete'],
                'export' => ['accounting.invoice.export']
            ],
            'operational' => [
                'view' => ['operational.delivery.navigation', 'operational.tracking.view'],
                'create' => ['operational.delivery.create'],
                'edit' => ['operational.delivery.edit', 'operational.delivery_instructions.edit'],
                'delete' => ['operational.delivery.delete'],
                'export' => ['operational.delivery.export']
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
