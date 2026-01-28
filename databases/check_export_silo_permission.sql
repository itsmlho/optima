-- Check Export Silo Permission in Database
-- Script ini akan memeriksa apakah export_silo sudah ada di permissions dan role_permissions

-- 1. Check if permission exists
SELECT 
    'PERMISSION CHECK' as check_type,
    COUNT(*) as found,
    GROUP_CONCAT(key_name SEPARATOR ', ') as permission_keys
FROM permissions 
WHERE module = 'perizinan' AND (key_name LIKE '%export%' OR category = 'EXPORT');

-- 2. List all perizinan permissions
SELECT 
    'ALL PERIZINAN PERMISSIONS' as check_type,
    id, module, page, action, key_name, display_name, category
FROM permissions 
WHERE module = 'perizinan'
ORDER BY key_name;

-- 3. Check role assignments for perizinan export
SELECT 
    'ROLE ASSIGNMENTS' as check_type,
    r.name as role_name, 
    p.key_name as permission_key,
    p.display_name
FROM role_permissions rp
JOIN roles r ON r.id = rp.role_id
JOIN permissions p ON p.id = rp.permission_id
WHERE p.module = 'perizinan' AND (p.key_name LIKE '%export%' OR p.category = 'EXPORT')
ORDER BY r.name;

-- 4. If permission doesn't exist, here's the INSERT statement to add it:
-- INSERT INTO permissions (module, page, action, subaction, component, key_name, display_name, description, category, is_active, created_at, updated_at)
-- VALUES ('perizinan', 'silo', 'export', NULL, NULL, 'perizinan.silo.export', 'Export Data SILO', 'Izin untuk export data SILO ke Excel', 'EXPORT', 1, NOW(), NOW());

-- 5. To assign to roles (after getting permission_id from step 4):
-- INSERT INTO role_permissions (role_id, permission_id)
-- SELECT r.id, p.id 
-- FROM roles r, permissions p
-- WHERE p.key_name = 'perizinan.silo.export'
-- AND r.name IN ('Super Administrator', 'Administrator', 'Head Perizinan', 'Staff Perizinan');
