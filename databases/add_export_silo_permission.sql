-- Add Export Permission for SILO Module
-- Script ini akan menambahkan permission export untuk SILO dan assign ke roles yang sesuai

-- Step 1: Add export permission for SILO
INSERT INTO permissions (module, page, action, subaction, component, key_name, display_name, description, category, is_active, created_at, updated_at)
VALUES ('perizinan', 'silo', 'export', NULL, NULL, 'perizinan.silo.export', 'Export Data SILO', 'Izin untuk export data SILO ke Excel', 'EXPORT', 1, NOW(), NOW());

-- Step 2: Get the newly inserted permission ID
SET @permission_id = LAST_INSERT_ID();

-- Step 3: Assign to relevant roles
-- Super Administrator, Administrator, Head Perizinan should have export access
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, @permission_id
FROM roles r
WHERE r.name IN ('Super Administrator', 'Administrator', 'Head Perizinan')
AND NOT EXISTS (
    SELECT 1 FROM role_permissions rp2 
    WHERE rp2.role_id = r.id AND rp2.permission_id = @permission_id
);

-- Step 4: Verify the insertion
SELECT 
    'VERIFICATION' as step,
    p.key_name as permission,
    p.display_name,
    GROUP_CONCAT(r.name ORDER BY r.name SEPARATOR ', ') as assigned_roles
FROM permissions p
LEFT JOIN role_permissions rp ON rp.permission_id = p.id
LEFT JOIN roles r ON r.id = rp.role_id
WHERE p.key_name = 'perizinan.silo.export'
GROUP BY p.id, p.key_name, p.display_name;

-- Step 5: Show all SILO permissions after insertion
SELECT 
    'ALL SILO PERMISSIONS' as info,
    id, key_name, display_name, category, is_active
FROM permissions
WHERE module = 'perizinan' AND page = 'silo'
ORDER BY category, action;
