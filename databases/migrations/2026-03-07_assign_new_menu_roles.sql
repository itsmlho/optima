-- ========================================================================
-- Assign New Menu Permissions to Roles
-- Date: March 7, 2026
-- Purpose: Grant access to Unit Audit, Audit Approval, and Surat Jalan menus
-- ========================================================================

-- ========================================================================
-- 1. MARKETING ROLE: Audit Approval Access
-- ========================================================================

-- Grant all audit approval permissions to marketing roles
INSERT INTO role_permissions (role_id, permission_id, granted)
SELECT r.id, p.id, 1
FROM roles r
CROSS JOIN permissions p
WHERE r.name IN ('Head Marketing', 'Staff Marketing')
  AND p.key_name LIKE 'marketing.audit_approval.%'
  AND NOT EXISTS (
      SELECT 1 FROM role_permissions rp 
      WHERE rp.role_id = r.id AND rp.permission_id = p.id
  );

-- ========================================================================
-- 2. SERVICE ROLE: Unit Audit Access
-- ========================================================================

-- Grant all unit audit permissions to service roles
INSERT INTO role_permissions (role_id, permission_id, granted)
SELECT r.id, p.id, 1
FROM roles r
CROSS JOIN permissions p
WHERE r.name IN ('Head Service', 'Staff Service', 'Admin Service Pusat', 'Admin Service Area', 'Supervisor Service', 'Manager Service Area')
  AND p.key_name LIKE 'service.unit_audit.%'
  AND NOT EXISTS (
      SELECT 1 FROM role_permissions rp 
      WHERE rp.role_id = r.id AND rp.permission_id = p.id
  );

-- ========================================================================
-- 3. WAREHOUSE ROLE: Surat Jalan Access
-- ========================================================================

-- Grant all movements/surat jalan permissions to warehouse roles
INSERT INTO role_permissions (role_id, permission_id, granted)
SELECT r.id, p.id, 1
FROM roles r
CROSS JOIN permissions p
WHERE r.name IN ('Head Warehouse', 'Staff Warehouse')
  AND p.key_name LIKE 'warehouse.movements.%'
  AND NOT EXISTS (
      SELECT 1 FROM role_permissions rp 
      WHERE rp.role_id = r.id AND rp.permission_id = p.id
  );

-- ========================================================================
-- 4. ADMIN/SUPER ADMIN: Full Access to All New Features
-- ========================================================================

-- Grant admin full access to all new permissions
INSERT INTO role_permissions (role_id, permission_id, granted)
SELECT r.id, p.id, 1
FROM roles r
CROSS JOIN permissions p
WHERE r.name IN ('Administrator', 'Super Administrator')
  AND (p.key_name LIKE 'marketing.audit_approval.%'
       OR p.key_name LIKE 'service.unit_audit.%'
       OR p.key_name LIKE 'warehouse.movements.%')
  AND NOT EXISTS (
      SELECT 1 FROM role_permissions rp 
      WHERE rp.role_id = r.id AND rp.permission_id = p.id
  );

-- ========================================================================
-- Verification Queries
-- ========================================================================

-- Check Marketing role assignments
SELECT 
    '✓ Marketing Role - Audit Approval Permissions' AS section,
    r.name AS role_name,
    COUNT(*) AS total_permissions,
    GROUP_CONCAT(p.action ORDER BY p.action SEPARATOR ', ') AS actions
FROM role_permissions rp
JOIN roles r ON rp.role_id = r.id
JOIN permissions p ON rp.permission_id = p.id
WHERE r.name = 'marketing_role' 
  AND p.key_name LIKE 'marketing.audit_approval.%'
  AND rp.granted = 1
GROUP BY r.name;

-- Check Service role assignments
SELECT 
    '✓ Service Role - Unit Audit Permissions' AS section,
    r.name AS role_name,
    COUNT(*) AS total_permissions,
    GROUP_CONCAT(p.action ORDER BY p.action SEPARATOR ', ') AS actions
FROM role_permissions rp
JOIN roles r ON rp.role_id = r.id
JOIN permissions p ON rp.permission_id = p.id
WHERE r.name = 'service_role' 
  AND p.key_name LIKE 'service.unit_audit.%'
  AND rp.granted = 1
GROUP BY r.name;

-- Check Warehouse role assignments
SELECT 
    '✓ Warehouse Role - Surat Jalan Permissions' AS section,
    r.name AS role_name,
    COUNT(*) AS total_permissions,
    GROUP_CONCAT(p.action ORDER BY p.action SEPARATOR ', ') AS actions
FROM role_permissions rp
JOIN roles r ON rp.role_id = r.id
JOIN permissions p ON rp.permission_id = p.id
WHERE r.name = 'warehouse_role' 
  AND p.key_name LIKE 'warehouse.movements.%'
  AND rp.granted = 1
GROUP BY r.name;

-- Show all role-permission mappings for new features
SELECT 
    r.name AS role_name,
    p.module,
    p.page,
    p.action,
    p.key_name,
    rp.granted
FROM role_permissions rp
JOIN roles r ON rp.role_id = r.id
JOIN permissions p ON rp.permission_id = p.id
WHERE (p.key_name LIKE 'marketing.audit_approval.%'
       OR p.key_name LIKE 'service.unit_audit.%'
       OR p.key_name LIKE 'warehouse.movements.%')
  AND rp.granted = 1
ORDER BY r.name, p.module, p.page, p.action;

-- Summary by role
SELECT 
    r.name AS role_name,
    COUNT(CASE WHEN p.key_name LIKE 'marketing.audit_approval.%' THEN 1 END) AS audit_approval_perms,
    COUNT(CASE WHEN p.key_name LIKE 'service.unit_audit.%' THEN 1 END) AS unit_audit_perms,
    COUNT(CASE WHEN p.key_name LIKE 'warehouse.movements.%' THEN 1 END) AS movements_perms,
    COUNT(*) AS total_new_perms
FROM role_permissions rp
JOIN roles r ON rp.role_id = r.id
JOIN permissions p ON rp.permission_id = p.id
WHERE (p.key_name LIKE 'marketing.audit_approval.%'
       OR p.key_name LIKE 'service.unit_audit.%'
       OR p.key_name LIKE 'warehouse.movements.%')
  AND rp.granted = 1
GROUP BY r.name
ORDER BY r.name;
