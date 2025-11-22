-- ========================================
-- Setup Resource Permissions
-- Run this SQL file in your database
-- ========================================

-- 1. Insert Resource Permissions
INSERT INTO permissions (`key`, name, description, module, category, is_system_permission, is_active, created_at, updated_at)
SELECT * FROM (
    SELECT 'warehouse.inventory.view' as `key`, 'View Inventory (Cross-Division)' as name, 'View inventory across divisions - untuk divisi lain yang perlu cek ketersediaan unit' as description, 'warehouse' as module, 'resource' as category, 1 as is_system_permission, 1 as is_active, NOW() as created_at, NOW() as updated_at
    UNION ALL
    SELECT 'warehouse.inventory.manage', 'Manage Inventory (Cross-Division)', 'Manage inventory across divisions - untuk Service yang perlu update status unit setelah maintenance', 'warehouse', 'resource', 1, 1, NOW(), NOW()
    UNION ALL
    SELECT 'marketing.kontrak.view', 'View Kontrak (Cross-Division)', 'View kontrak across divisions - untuk Service, Operational, Warehouse, Accounting', 'marketing', 'resource', 1, 1, NOW(), NOW()
    UNION ALL
    SELECT 'service.workorder.view', 'View Work Order (Cross-Division)', 'View work order across divisions - untuk Marketing, Warehouse, Accounting', 'service', 'resource', 1, 1, NOW(), NOW()
    UNION ALL
    SELECT 'purchasing.po.view', 'View PO (Cross-Division)', 'View purchase order across divisions - untuk Marketing, Warehouse, Accounting', 'purchasing', 'resource', 1, 1, NOW(), NOW()
    UNION ALL
    SELECT 'operational.delivery.view', 'View Delivery (Cross-Division)', 'View delivery across divisions - untuk Marketing, Warehouse', 'operational', 'resource', 1, 1, NOW(), NOW()
    UNION ALL
    SELECT 'accounting.financial.view', 'View Financial (Cross-Division)', 'View financial data across divisions - untuk Marketing, Service, Purchasing', 'accounting', 'resource', 1, 1, NOW(), NOW()
) AS tmp
WHERE NOT EXISTS (
    SELECT 1 FROM permissions WHERE permissions.`key` = tmp.`key`
);

-- 2. Assign Resource Permissions to Roles
-- Note: Adjust role names based on your actual role names in database

-- Super Administrator - All permissions
INSERT INTO role_permissions (role_id, permission_id, granted)
SELECT r.id, p.id, 1
FROM roles r
CROSS JOIN permissions p
WHERE LOWER(r.name) = 'super administrator'
AND p.`key` IN (
    'warehouse.inventory.view',
    'warehouse.inventory.manage',
    'marketing.kontrak.view',
    'service.workorder.view',
    'purchasing.po.view',
    'operational.delivery.view',
    'accounting.financial.view'
)
AND NOT EXISTS (
    SELECT 1 FROM role_permissions rp 
    WHERE rp.role_id = r.id AND rp.permission_id = p.id
);

-- Marketing Head
INSERT INTO role_permissions (role_id, permission_id, granted)
SELECT r.id, p.id, 1
FROM roles r
CROSS JOIN permissions p
WHERE LOWER(r.name) LIKE '%marketing%head%'
AND p.`key` IN (
    'warehouse.inventory.view',
    'service.workorder.view',
    'purchasing.po.view',
    'operational.delivery.view',
    'accounting.financial.view'
)
AND NOT EXISTS (
    SELECT 1 FROM role_permissions rp 
    WHERE rp.role_id = r.id AND rp.permission_id = p.id
);

-- Marketing Staff
INSERT INTO role_permissions (role_id, permission_id, granted)
SELECT r.id, p.id, 1
FROM roles r
CROSS JOIN permissions p
WHERE LOWER(r.name) LIKE '%marketing%staff%'
AND p.`key` IN (
    'warehouse.inventory.view',
    'service.workorder.view',
    'purchasing.po.view',
    'operational.delivery.view',
    'accounting.financial.view'
)
AND NOT EXISTS (
    SELECT 1 FROM role_permissions rp 
    WHERE rp.role_id = r.id AND rp.permission_id = p.id
);

-- Service Head
INSERT INTO role_permissions (role_id, permission_id, granted)
SELECT r.id, p.id, 1
FROM roles r
CROSS JOIN permissions p
WHERE LOWER(r.name) LIKE '%service%head%'
AND p.`key` IN (
    'warehouse.inventory.view',
    'warehouse.inventory.manage',
    'marketing.kontrak.view',
    'purchasing.po.view',
    'accounting.financial.view'
)
AND NOT EXISTS (
    SELECT 1 FROM role_permissions rp 
    WHERE rp.role_id = r.id AND rp.permission_id = p.id
);

-- Service Staff
INSERT INTO role_permissions (role_id, permission_id, granted)
SELECT r.id, p.id, 1
FROM roles r
CROSS JOIN permissions p
WHERE LOWER(r.name) LIKE '%service%staff%'
AND p.`key` IN (
    'warehouse.inventory.view',
    'warehouse.inventory.manage',
    'marketing.kontrak.view',
    'purchasing.po.view'
)
AND NOT EXISTS (
    SELECT 1 FROM role_permissions rp 
    WHERE rp.role_id = r.id AND rp.permission_id = p.id
);

-- Warehouse Head
INSERT INTO role_permissions (role_id, permission_id, granted)
SELECT r.id, p.id, 1
FROM roles r
CROSS JOIN permissions p
WHERE LOWER(r.name) LIKE '%warehouse%head%'
AND p.`key` IN (
    'marketing.kontrak.view',
    'service.workorder.view',
    'purchasing.po.view',
    'operational.delivery.view',
    'accounting.financial.view'
)
AND NOT EXISTS (
    SELECT 1 FROM role_permissions rp 
    WHERE rp.role_id = r.id AND rp.permission_id = p.id
);

-- Warehouse Staff
INSERT INTO role_permissions (role_id, permission_id, granted)
SELECT r.id, p.id, 1
FROM roles r
CROSS JOIN permissions p
WHERE LOWER(r.name) LIKE '%warehouse%staff%'
AND p.`key` IN (
    'marketing.kontrak.view',
    'service.workorder.view',
    'purchasing.po.view',
    'operational.delivery.view'
)
AND NOT EXISTS (
    SELECT 1 FROM role_permissions rp 
    WHERE rp.role_id = r.id AND rp.permission_id = p.id
);

-- Purchasing Head
INSERT INTO role_permissions (role_id, permission_id, granted)
SELECT r.id, p.id, 1
FROM roles r
CROSS JOIN permissions p
WHERE LOWER(r.name) LIKE '%purchasing%head%'
AND p.`key` IN (
    'warehouse.inventory.view',
    'marketing.kontrak.view',
    'service.workorder.view',
    'accounting.financial.view'
)
AND NOT EXISTS (
    SELECT 1 FROM role_permissions rp 
    WHERE rp.role_id = r.id AND rp.permission_id = p.id
);

-- Purchasing Staff
INSERT INTO role_permissions (role_id, permission_id, granted)
SELECT r.id, p.id, 1
FROM roles r
CROSS JOIN permissions p
WHERE LOWER(r.name) LIKE '%purchasing%staff%'
AND p.`key` IN (
    'warehouse.inventory.view',
    'marketing.kontrak.view',
    'service.workorder.view'
)
AND NOT EXISTS (
    SELECT 1 FROM role_permissions rp 
    WHERE rp.role_id = r.id AND rp.permission_id = p.id
);

-- Operational Head
INSERT INTO role_permissions (role_id, permission_id, granted)
SELECT r.id, p.id, 1
FROM roles r
CROSS JOIN permissions p
WHERE LOWER(r.name) LIKE '%operational%head%'
AND p.`key` IN (
    'marketing.kontrak.view',
    'warehouse.inventory.view',
    'service.workorder.view',
    'purchasing.po.view'
)
AND NOT EXISTS (
    SELECT 1 FROM role_permissions rp 
    WHERE rp.role_id = r.id AND rp.permission_id = p.id
);

-- Operational Staff
INSERT INTO role_permissions (role_id, permission_id, granted)
SELECT r.id, p.id, 1
FROM roles r
CROSS JOIN permissions p
WHERE LOWER(r.name) LIKE '%operational%staff%'
AND p.`key` IN (
    'marketing.kontrak.view',
    'warehouse.inventory.view',
    'service.workorder.view'
)
AND NOT EXISTS (
    SELECT 1 FROM role_permissions rp 
    WHERE rp.role_id = r.id AND rp.permission_id = p.id
);

-- Accounting Head
INSERT INTO role_permissions (role_id, permission_id, granted)
SELECT r.id, p.id, 1
FROM roles r
CROSS JOIN permissions p
WHERE LOWER(r.name) LIKE '%accounting%head%'
AND p.`key` IN (
    'marketing.kontrak.view',
    'purchasing.po.view',
    'service.workorder.view',
    'warehouse.inventory.view'
)
AND NOT EXISTS (
    SELECT 1 FROM role_permissions rp 
    WHERE rp.role_id = r.id AND rp.permission_id = p.id
);

-- Accounting Staff
INSERT INTO role_permissions (role_id, permission_id, granted)
SELECT r.id, p.id, 1
FROM roles r
CROSS JOIN permissions p
WHERE LOWER(r.name) LIKE '%accounting%staff%'
AND p.`key` IN (
    'marketing.kontrak.view',
    'purchasing.po.view',
    'service.workorder.view'
)
AND NOT EXISTS (
    SELECT 1 FROM role_permissions rp 
    WHERE rp.role_id = r.id AND rp.permission_id = p.id
);

-- Perizinan Head
INSERT INTO role_permissions (role_id, permission_id, granted)
SELECT r.id, p.id, 1
FROM roles r
CROSS JOIN permissions p
WHERE LOWER(r.name) LIKE '%perizinan%head%'
AND p.`key` IN (
    'warehouse.inventory.view',
    'marketing.kontrak.view'
)
AND NOT EXISTS (
    SELECT 1 FROM role_permissions rp 
    WHERE rp.role_id = r.id AND rp.permission_id = p.id
);

-- Perizinan Staff
INSERT INTO role_permissions (role_id, permission_id, granted)
SELECT r.id, p.id, 1
FROM roles r
CROSS JOIN permissions p
WHERE LOWER(r.name) LIKE '%perizinan%staff%'
AND p.`key` IN (
    'warehouse.inventory.view',
    'marketing.kontrak.view'
)
AND NOT EXISTS (
    SELECT 1 FROM role_permissions rp 
    WHERE rp.role_id = r.id AND rp.permission_id = p.id
);

-- Verification
SELECT 'Setup completed!' as status;
SELECT COUNT(*) as total_permissions FROM permissions;
SELECT COUNT(*) as resource_permissions FROM permissions WHERE category = 'resource';
SELECT COUNT(*) as total_role_permissions FROM role_permissions;

