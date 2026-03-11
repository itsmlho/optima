-- ═══════════════════════════════════════════════════════════════════════════════
-- CREATE MARKETING STAFF USER FOR TESTING
-- Date: 2026-03-11
-- Purpose: Create sample Marketing Staff user for permission testing
-- ═══════════════════════════════════════════════════════════════════════════════

-- Create Marketing Staff user
INSERT INTO users (
    username, 
    email, 
    password, 
    first_name, 
    last_name, 
    is_active, 
    created_at, 
    updated_at
) VALUES (
    'marketingstaff',
    'marketing.staff@optima.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password123
    'Staff',
    'Marketing',
    1,
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE username = VALUES(username);

-- Get the inserted user ID
SET @marketing_staff_id = (SELECT id FROM users WHERE username = 'marketingstaff');

-- Assign Marketing Staff role (ID: 4)
INSERT INTO user_roles (user_id, role_id, is_active, assigned_at)
SELECT @marketing_staff_id, 4, 1, NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM user_roles WHERE user_id = @marketing_staff_id AND role_id = 4
);

-- Also assign to existing marketingmanager user (ID: 3)
INSERT INTO user_roles (user_id, role_id, is_active, assigned_at)
SELECT 3, 3, 1, NOW() -- Marketing Manager role
WHERE NOT EXISTS (
    SELECT 1 FROM user_roles WHERE user_id = 3 AND role_id = 3
);

-- Verify assignments
SELECT 
    'User Role Assignments' as Title,
    u.id,
    u.username,
    u.first_name,
    u.last_name,
    r.display_name as role_name,
    ur.is_active
FROM user_roles ur
INNER JOIN users u ON u.id = ur.user_id
INNER JOIN roles r ON r.id = ur.role_id
WHERE u.username IN ('marketingstaff', 'marketingmanager')
ORDER BY u.username;

-- Count effective permissions
SELECT 
    u.username,
    r.display_name as role_name,
    COUNT(DISTINCT p.id) as total_permissions,
    COUNT(DISTINCT CASE WHEN p.module = 'marketing' THEN p.id END) as marketing_perms
FROM user_roles ur
INNER JOIN users u ON u.id = ur.user_id
INNER JOIN roles r ON r.id = ur.role_id
INNER JOIN role_permissions rp ON rp.role_id = ur.role_id
INNER JOIN permissions p ON p.id = rp.permission_id
WHERE u.username IN ('marketingstaff', 'marketingmanager')
AND ur.is_active = 1
AND rp.granted = 1
GROUP BY u.username, r.display_name;

SELECT 'Marketing users created and assigned!' AS Status;
