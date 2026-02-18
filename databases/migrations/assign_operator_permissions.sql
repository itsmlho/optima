-- ====================================
-- Assign Operator Permissions to Roles
-- Date: 2026-02-16
-- ====================================

-- Get permission IDs for operator management
SET @perm_navigation = (SELECT id FROM permissions WHERE key_name = 'marketing.operator.navigation');
SET @perm_view = (SELECT id FROM permissions WHERE key_name = 'marketing.operator.view');
SET @perm_create = (SELECT id FROM permissions WHERE key_name = 'marketing.operator.create');
SET @perm_edit = (SELECT id FROM permissions WHERE key_name = 'marketing.operator.edit');
SET @perm_delete = (SELECT id FROM permissions WHERE key_name = 'marketing.operator.delete');
SET @perm_assign = (SELECT id FROM permissions WHERE key_name = 'marketing.operator.assign');
SET @perm_export = (SELECT id FROM permissions WHERE key_name = 'marketing.operator.export');

-- Assign ALL operator permissions to Super Administrator (id=1)
INSERT IGNORE INTO role_permissions (role_id, permission_id)
VALUES 
    (1, @perm_navigation),
    (1, @perm_view),
    (1, @perm_create),
    (1, @perm_edit),
    (1, @perm_delete),
    (1, @perm_assign),
    (1, @perm_export);

-- Assign ALL operator permissions to Head Marketing (id=2)
INSERT IGNORE INTO role_permissions (role_id, permission_id)
VALUES 
    (2, @perm_navigation),
    (2, @perm_view),
    (2, @perm_create),
    (2, @perm_edit),
    (2, @perm_delete),
    (2, @perm_assign),
    (2, @perm_export);

-- Assign limited operator permissions to Staff Marketing (id=3) - view, assign only
INSERT IGNORE INTO role_permissions (role_id, permission_id)
VALUES 
    (3, @perm_navigation),
    (3, @perm_view),
    (3, @perm_assign);

-- Verify assignments
SELECT 
    r.name AS role_name,
    p.display_name AS permission,
    p.key_name
FROM role_permissions rp
JOIN roles r ON r.id = rp.role_id
JOIN permissions p ON p.id = rp.permission_id
WHERE p.key_name LIKE 'marketing.operator.%'
ORDER BY r.id, p.key_name;

-- Summary
SELECT 
    'Operator permissions assigned successfully!' AS status,
    COUNT(DISTINCT rp.role_id) AS roles_assigned,
    COUNT(*) AS total_assignments
FROM role_permissions rp
JOIN permissions p ON p.id = rp.permission_id
WHERE p.key_name LIKE 'marketing.operator.%';
