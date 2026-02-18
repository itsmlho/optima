-- ====================================
-- Add Operator Management Permissions
-- Date: 2026-02-16
-- ====================================

-- Check and insert operator permissions for Marketing module
INSERT INTO permissions (module, page, action, key_name, display_name, description, category, is_active, created_at, updated_at)
SELECT 'marketing', 'operator', 'navigation', 'marketing.operator.navigation', 'Operator Management Navigation', 'Access to operator management menu', 'Marketing', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE key_name = 'marketing.operator.navigation');

INSERT INTO permissions (module, page, action, key_name, display_name, description, category, is_active, created_at, updated_at)
SELECT 'marketing', 'operator', 'view', 'marketing.operator.view', 'View Operator List', 'View and search operator data', 'Marketing', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE key_name = 'marketing.operator.view');

INSERT INTO permissions (module, page, action, key_name, display_name, description, category, is_active, created_at, updated_at)
SELECT 'marketing', 'operator', 'create', 'marketing.operator.create', 'Create Operator', 'Add new operator/driver', 'Marketing', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE key_name = 'marketing.operator.create');

INSERT INTO permissions (module, page, action, key_name, display_name, description, category, is_active, created_at, updated_at)
SELECT 'marketing', 'operator', 'edit', 'marketing.operator.edit', 'Edit Operator', 'Update operator information', 'Marketing', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE key_name = 'marketing.operator.edit');

INSERT INTO permissions (module, page, action, key_name, display_name, description, category, is_active, created_at, updated_at)
SELECT 'marketing', 'operator', 'delete', 'marketing.operator.delete', 'Delete Operator', 'Remove operator from system', 'Marketing', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE key_name = 'marketing.operator.delete');

INSERT INTO permissions (module, page, action, key_name, display_name, description, category, is_active, created_at, updated_at)
SELECT 'marketing', 'operator', 'assign', 'marketing.operator.assign', 'Assign Operator to Contract', 'Assign operator to rental contracts', 'Marketing', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE key_name = 'marketing.operator.assign');

INSERT INTO permissions (module, page, action, key_name, display_name, description, category, is_active, created_at, updated_at)
SELECT 'marketing', 'operator', 'export', 'marketing.operator.export', 'Export Operator Data', 'Export operator list to Excel/PDF', 'Marketing', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE key_name = 'marketing.operator.export');

-- Verify insertions
SELECT 
    'Operator permissions inserted successfully!' AS status,
    COUNT(*) AS total_operator_permissions
FROM permissions 
WHERE key_name LIKE 'marketing.operator.%';

-- Show all operator permissions
SELECT id, module, page, action, key_name, display_name, is_active
FROM permissions 
WHERE key_name LIKE 'marketing.operator.%'
ORDER BY id;
