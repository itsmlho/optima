-- ═══════════════════════════════════════════════════════════════════════════════
-- ASSIGN MARKETING PERMISSIONS TO MARKETING STAFF ROLE
-- Date: 2026-03-11
-- Purpose: Give Marketing Staff full access to marketing module
-- ═══════════════════════════════════════════════════════════════════════════════

-- Assign ALL marketing permissions to Marketing Staff role (ID: 4)
INSERT INTO role_permissions (role_id, permission_id, granted)
SELECT 4, p.id, 1
FROM permissions p
WHERE p.module = 'marketing'
AND NOT EXISTS (
    SELECT 1 FROM role_permissions rp
    WHERE rp.role_id = 4 AND rp.permission_id = p.id
);

-- Assign dashboard permissions
INSERT INTO role_permissions (role_id, permission_id, granted)
SELECT 4, p.id, 1
FROM permissions p
WHERE p.key_name IN (
    'dashboard.home.navigation',
    'dashboard.home.view'
)
AND NOT EXISTS (
    SELECT 1 FROM role_permissions rp
    WHERE rp.role_id = 4 AND rp.permission_id = p.id
);

-- Show assigned permissions
SELECT 
    'Marketing Staff' as role_name,
    COUNT(*) as total_permissions,
    COUNT(CASE WHEN p.module = 'marketing' THEN 1 END) as marketing_perms,
    COUNT(CASE WHEN p.module = 'dashboard' THEN 1 END) as dashboard_perms
FROM role_permissions rp
INNER JOIN permissions p ON p.id = rp.permission_id
WHERE rp.role_id = 4 AND rp.granted = 1;

-- Assign user Firsty to Marketing Staff role
-- First, find Firsty's user ID
SET @firsty_user_id = (SELECT id FROM users WHERE username = 'Firsty1a21' LIMIT 1);

-- Insert user role assignment if not exists
INSERT INTO user_roles (user_id, role_id, is_active)
SELECT @firsty_user_id, 4, 1
WHERE @firsty_user_id IS NOT NULL
AND NOT EXISTS (
    SELECT 1 FROM user_roles WHERE user_id = @firsty_user_id AND role_id = 4
);

-- Show user role assignment
SELECT 
    u.id as user_id,
    u.username,
    u.first_name,
    u.last_name,
    r.name as role_name,
    r.display_name,
    ur.is_active
FROM user_roles ur
INNER JOIN users u ON u.id = ur.user_id
INNER JOIN roles r ON r.id = ur.role_id
WHERE u.username = 'Firsty1a21';

SELECT 'Marketing Staff permissions assigned successfully!' AS Status;
