-- ═══════════════════════════════════════════════════════════════════════════════
-- ASSIGN MARKETING MANAGER PERMISSIONS (Full Marketing Access)
-- Date: 2026-03-11
-- ═══════════════════════════════════════════════════════════════════════════════

-- Marketing Manager gets ALL marketing permissions (same as staff but with manager capabilities)
INSERT INTO role_permissions (role_id, permission_id, granted)
SELECT 3, p.id, 1
FROM permissions p
WHERE p.module IN ('marketing', 'dashboard')
AND NOT EXISTS (
    SELECT 1 FROM role_permissions rp
    WHERE rp.role_id = 3 AND rp.permission_id = p.id
);

-- Also assign operational permissions for unit tracking
INSERT INTO role_permissions (role_id, permission_id, granted)
SELECT 3, p.id, 1
FROM permissions p
WHERE p.key_name LIKE 'operational.tracking%'
AND NOT EXISTS (
    SELECT 1 FROM role_permissions rp
    WHERE rp.role_id = 3 AND rp.permission_id = p.id
);

-- Verify Marketing Manager permissions
SELECT 
    'Marketing Manager' as role_name,
    COUNT(*) as total_permissions,
    COUNT(CASE WHEN p.module = 'marketing' THEN 1 END) as marketing_perms,
    COUNT(CASE WHEN p.module = 'dashboard' THEN 1 END) as dashboard_perms,
    COUNT(CASE WHEN p.module = 'operational' THEN 1 END) as operational_perms
FROM role_permissions rp
INNER JOIN permissions p ON p.id = rp.permission_id
WHERE rp.role_id = 3 AND rp.granted = 1;

SELECT 'Marketing Manager permissions assigned!' AS Status;
