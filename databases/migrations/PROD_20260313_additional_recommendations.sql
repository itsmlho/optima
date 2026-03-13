-- ============================================================================
-- OPTIMA PERMISSION SYSTEM - ADDITIONAL RECOMMENDATIONS
-- ============================================================================
-- File: PROD_20260313_additional_recommendations.sql
-- Purpose: Implementasi rekomendasi tambahan cross-division access
-- Date: March 13, 2026
-- Author: GitHub Copilot Assistant
-- 
-- RECOMMENDATIONS INCLUDED:
-- 1. Head Purchasing → Inventory View Access
-- 2. All HEAD Roles → Activity Logs Access (Audit Trail)
-- 3. All HEAD Roles → Notification Settings (Customize Alerts)
--
-- EXPECTED CHANGES:
-- - No new permissions (all exist)
-- - Additional role_permissions assignments: ~30-40 rows
-- 
-- SAFETY:
-- - No DELETE operations (purely additive)
-- - Uses INSERT IGNORE (won't fail if exists)
-- - All changes can be rolled back
-- ============================================================================

-- ============================================================================
-- PRE-DEPLOYMENT CHECKLIST
-- ============================================================================
-- [ ] Database backup created
-- [ ] Tested in development/staging environment
-- [ ] Verified all HEAD role IDs exist
-- [ ] Confirmed permissions exist in permissions table
-- [ ] Read ROLE_PERMISSION_FIX_GUIDE.md
-- ============================================================================

-- ============================================================================
-- BACKUP VERIFICATION
-- ============================================================================
-- Run this BEFORE executing migration:
-- CREATE TABLE role_permissions_backup_20260313_v2 AS 
-- SELECT * FROM role_permissions;

-- Verify backup:
-- SELECT COUNT(*) FROM role_permissions_backup_20260313_v2;
-- ============================================================================

-- ============================================================================
-- RECOMMENDATION 1: HEAD PURCHASING - INVENTORY VIEW ACCESS
-- ============================================================================
-- Reason: Head Purchasing perlu cek current stock sebelum create PO
--         Koordinasi dengan warehouse untuk procurement planning
--         Prevent overstock/understock situations
-- 
-- Permissions Added:
-- - warehouse.unit_inventory.navigation
-- - warehouse.unit_inventory.view
-- - warehouse.sparepart_inventory.navigation  
-- - warehouse.sparepart_inventory.view
-- ============================================================================

INSERT IGNORE INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 10, id, 1, NOW()
FROM permissions
WHERE module = 'warehouse' 
  AND page IN ('unit_inventory', 'sparepart_inventory')
  AND action IN ('navigation', 'view')
  AND is_active = 1;

-- Verification Query:
-- SELECT p.module, p.page, p.action, p.key_name
-- FROM role_permissions rp
-- JOIN permissions p ON rp.permission_id = p.id
-- WHERE rp.role_id = 10 
--   AND p.module = 'warehouse'
--   AND p.page IN ('unit_inventory', 'sparepart_inventory')
-- ORDER BY p.page, p.action;
-- Expected: 4 rows (2 permissions × 2 actions)


-- ============================================================================
-- RECOMMENDATION 2: ALL HEAD ROLES - ACTIVITY LOGS ACCESS
-- ============================================================================
-- Reason: Head roles perlu audit trail untuk monitoring team activities
--         Investigate suspicious activities, performance tracking
--         Meet compliance requirements for audit logging
-- 
-- Applies To:
-- - Head Marketing (role_id 2)
-- - Head Operational (role_id 4)
-- - Head Purchasing (role_id 10)
-- - Head Accounting (role_id 12)
-- - Head HRD (role_id 14)
-- - Head Warehouse (role_id 16)
-- - Head Service (role_id 35)
--
-- Permissions Added:
-- - activity.activity_log.navigation
-- - activity.activity_log.view
-- - activity.activity_log.export
-- 
-- NOTE: DELETE permission NOT granted (only Admin can delete logs)
-- ============================================================================

-- HEAD MARKETING (role_id 2) - Activity Logs
INSERT IGNORE INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 2, id, 1, NOW()
FROM permissions
WHERE module = 'activity' 
  AND page = 'activity_log'
  AND action IN ('navigation', 'view', 'export')
  AND is_active = 1;

-- HEAD OPERATIONAL (role_id 4) - Activity Logs
INSERT IGNORE INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 4, id, 1, NOW()
FROM permissions
WHERE module = 'activity' 
  AND page = 'activity_log'
  AND action IN ('navigation', 'view', 'export')
  AND is_active = 1;

-- HEAD PURCHASING (role_id 10) - Activity Logs
INSERT IGNORE INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 10, id, 1, NOW()
FROM permissions
WHERE module = 'activity' 
  AND page = 'activity_log'
  AND action IN ('navigation', 'view', 'export')
  AND is_active = 1;

-- HEAD ACCOUNTING (role_id 12) - Activity Logs
INSERT IGNORE INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 12, id, 1, NOW()
FROM permissions
WHERE module = 'activity' 
  AND page = 'activity_log'
  AND action IN ('navigation', 'view', 'export')
  AND is_active = 1;

-- HEAD HRD (role_id 14) - Activity Logs
INSERT IGNORE INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 14, id, 1, NOW()
FROM permissions
WHERE module = 'activity' 
  AND page = 'activity_log'
  AND action IN ('navigation', 'view', 'export')
  AND is_active = 1;

-- HEAD WAREHOUSE (role_id 16) - Activity Logs
INSERT IGNORE INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 16, id, 1, NOW()
FROM permissions
WHERE module = 'activity' 
  AND page = 'activity_log'
  AND action IN ('navigation', 'view', 'export')
  AND is_active = 1;

-- HEAD SERVICE (role_id 35) - Activity Logs
INSERT IGNORE INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 35, id, 1, NOW()
FROM permissions
WHERE module = 'activity' 
  AND page = 'activity_log'
  AND action IN ('navigation', 'view', 'export')
  AND is_active = 1;

-- Verification Query:
-- SELECT r.name AS role_name, 
--        COUNT(DISTINCT rp.permission_id) AS activity_permissions
-- FROM roles r
-- LEFT JOIN role_permissions rp ON r.id = rp.role_id
-- LEFT JOIN permissions p ON rp.permission_id = p.id
--   AND p.module = 'activity' 
--   AND p.page = 'activity_log'
-- WHERE r.id IN (2, 4, 10, 12, 14, 16, 35)
-- GROUP BY r.id, r.name
-- ORDER BY r.id;
-- Expected: Each HEAD role should have 3 activity permissions


-- ============================================================================
-- RECOMMENDATION 3: ALL HEAD ROLES - NOTIFICATION SETTINGS ACCESS
-- ============================================================================
-- Reason: Head roles perlu customize notification rules per department
--         Set alert thresholds, manage email templates for their team
--         Reduce notification noise, focus on critical alerts only
-- 
-- Applies To: Same 7 HEAD roles as Recommendation 2
--
-- Permissions Added:
-- - settings.notification.navigation
-- - settings.notification.view
-- - settings.notification.edit
-- 
-- NOTE: CREATE/DELETE not granted (only Admin can create new notification types)
-- ============================================================================

-- HEAD MARKETING (role_id 2) - Notification Settings
INSERT IGNORE INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 2, id, 1, NOW()
FROM permissions
WHERE module = 'settings' 
  AND page = 'notification'
  AND action IN ('navigation', 'view', 'edit')
  AND is_active = 1;

-- HEAD OPERATIONAL (role_id 4) - Notification Settings
INSERT IGNORE INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 4, id, 1, NOW()
FROM permissions
WHERE module = 'settings' 
  AND page = 'notification'
  AND action IN ('navigation', 'view', 'edit')
  AND is_active = 1;

-- HEAD PURCHASING (role_id 10) - Notification Settings
INSERT IGNORE INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 10, id, 1, NOW()
FROM permissions
WHERE module = 'settings' 
  AND page = 'notification'
  AND action IN ('navigation', 'view', 'edit')
  AND is_active = 1;

-- HEAD ACCOUNTING (role_id 12) - Notification Settings
INSERT IGNORE INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 12, id, 1, NOW()
FROM permissions
WHERE module = 'settings' 
  AND page = 'notification'
  AND action IN ('navigation', 'view', 'edit')
  AND is_active = 1;

-- HEAD HRD (role_id 14) - Notification Settings
INSERT IGNORE INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 14, id, 1, NOW()
FROM permissions
WHERE module = 'settings' 
  AND page = 'notification'
  AND action IN ('navigation', 'view', 'edit')
  AND is_active = 1;

-- HEAD WAREHOUSE (role_id 16) - Notification Settings
INSERT IGNORE INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 16, id, 1, NOW()
FROM permissions
WHERE module = 'settings' 
  AND page = 'notification'
  AND action IN ('navigation', 'view', 'edit')
  AND is_active = 1;

-- HEAD SERVICE (role_id 35) - Notification Settings
INSERT IGNORE INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 35, id, 1, NOW()
FROM permissions
WHERE module = 'settings' 
  AND page = 'notification'
  AND action IN ('navigation', 'view', 'edit')
  AND is_active = 1;

-- Verification Query:
-- SELECT r.name AS role_name, 
--        COUNT(DISTINCT rp.permission_id) AS notification_permissions
-- FROM roles r
-- LEFT JOIN role_permissions rp ON r.id = rp.role_id
-- LEFT JOIN permissions p ON rp.permission_id = p.id
--   AND p.module = 'settings' 
--   AND p.page = 'notification'
-- WHERE r.id IN (2, 4, 10, 12, 14, 16, 35)
-- GROUP BY r.id, r.name
-- ORDER BY r.id;
-- Expected: Each HEAD role should have 3 notification permissions


-- ============================================================================
-- FINAL VERIFICATION QUERIES
-- ============================================================================

-- 1. Verify all recommendations applied successfully
SELECT 
    'Recommendation 1: Head Purchasing Inventory' AS recommendation,
    COUNT(*) AS permissions_added,
    CASE 
        WHEN COUNT(*) = 4 THEN '✅ SUCCESS'
        ELSE '❌ FAILED'
    END AS status
FROM role_permissions rp
JOIN permissions p ON rp.permission_id = p.id
WHERE rp.role_id = 10 
  AND p.module = 'warehouse'
  AND p.page IN ('unit_inventory', 'sparepart_inventory')
  AND p.action IN ('navigation', 'view')

UNION ALL

SELECT 
    'Recommendation 2: Activity Logs for HEAD roles' AS recommendation,
    COUNT(*) AS permissions_added,
    CASE 
        WHEN COUNT(*) = 21 THEN '✅ SUCCESS' -- 7 roles × 3 permissions
        ELSE '❌ FAILED'
    END AS status
FROM role_permissions rp
JOIN permissions p ON rp.permission_id = p.id
WHERE rp.role_id IN (2, 4, 10, 12, 14, 16, 35)
  AND p.module = 'activity'
  AND p.page = 'activity_log'
  AND p.action IN ('navigation', 'view', 'export')

UNION ALL

SELECT 
    'Recommendation 3: Notification Settings for HEAD roles' AS recommendation,
    COUNT(*) AS permissions_added,
    CASE 
        WHEN COUNT(*) = 21 THEN '✅ SUCCESS' -- 7 roles × 3 permissions
        ELSE '❌ FAILED'
    END AS status
FROM role_permissions rp
JOIN permissions p ON rp.permission_id = p.id
WHERE rp.role_id IN (2, 4, 10, 12, 14, 16, 35)
  AND p.module = 'settings'
  AND p.page = 'notification'
  AND p.action IN ('navigation', 'view', 'edit');


-- 2. Total new role_permissions added
SELECT 
    'Total New Assignments' AS metric,
    COUNT(*) AS total,
    CASE 
        WHEN COUNT(*) BETWEEN 40 AND 50 THEN '✅ EXPECTED RANGE'
        ELSE '⚠️ CHECK MANUALLY'
    END AS status
FROM role_permissions
WHERE assigned_at >= CURDATE(); -- Today's assignments only


-- 3. Detailed breakdown per HEAD role
SELECT 
    r.name AS role_name,
    r.id AS role_id,
    COUNT(CASE WHEN p.module = 'warehouse' THEN 1 END) AS warehouse_permissions,
    COUNT(CASE WHEN p.module = 'activity' THEN 1 END) AS activity_permissions,
    COUNT(CASE WHEN p.module = 'settings' THEN 1 END) AS notification_permissions,
    COUNT(*) AS total_new_permissions
FROM roles r
LEFT JOIN role_permissions rp ON r.id = rp.role_id AND rp.assigned_at >= CURDATE()
LEFT JOIN permissions p ON rp.permission_id = p.id
WHERE r.id IN (2, 4, 10, 12, 14, 16, 35)
GROUP BY r.id, r.name
ORDER BY r.id;

-- Expected Results:
-- role_id 2 (Head Marketing): 0 warehouse, 3 activity, 3 notification = 6 total
-- role_id 4 (Head Operational): 0 warehouse, 3 activity, 3 notification = 6 total
-- role_id 10 (Head Purchasing): 4 warehouse, 3 activity, 3 notification = 10 total
-- role_id 12 (Head Accounting): 0 warehouse, 3 activity, 3 notification = 6 total
-- role_id 14 (Head HRD): 0 warehouse, 3 activity, 3 notification = 6 total
-- role_id 16 (Head Warehouse): 0 warehouse, 3 activity, 3 notification = 6 total
-- role_id 35 (Head Service): 0 warehouse, 3 activity, 3 notification = 6 total


-- ============================================================================
-- ROLLBACK PROCEDURE (IF NEEDED)
-- ============================================================================

-- OPTION 1: Delete only today's assignments for these specific recommendations
/*
DELETE FROM role_permissions
WHERE assigned_at >= CURDATE()
  AND (
    -- Recommendation 1: Head Purchasing Inventory
    (role_id = 10 AND permission_id IN (
        SELECT id FROM permissions 
        WHERE module = 'warehouse' 
          AND page IN ('unit_inventory', 'sparepart_inventory')
          AND action IN ('navigation', 'view')
    ))
    OR
    -- Recommendation 2: Activity Logs
    (role_id IN (2, 4, 10, 12, 14, 16, 35) AND permission_id IN (
        SELECT id FROM permissions 
        WHERE module = 'activity' 
          AND page = 'activity_log'
          AND action IN ('navigation', 'view', 'export')
    ))
    OR
    -- Recommendation 3: Notification Settings
    (role_id IN (2, 4, 10, 12, 14, 16, 35) AND permission_id IN (
        SELECT id FROM permissions 
        WHERE module = 'settings' 
          AND page = 'notification'
          AND action IN ('navigation', 'view', 'edit')
    ))
  );

-- Verify rollback:
SELECT 'Rollback Complete' AS status, 
       ROW_COUNT() AS rows_deleted;
*/

-- OPTION 2: Restore from backup (safer)
/*
-- Delete current state
DELETE FROM role_permissions;

-- Restore from backup
INSERT INTO role_permissions 
SELECT * FROM role_permissions_backup_20260313_v2;

-- Verify restoration:
SELECT COUNT(*) FROM role_permissions;
-- Should match backup count
*/


-- ============================================================================
-- DEPLOYMENT NOTES
-- ============================================================================
-- 1. Run PROD_20260313_kontrak_permissions.sql FIRST (if not already done)
-- 2. Run PROD_20260313_fix_role_permissions.sql SECOND (if not already done)
-- 3. Run this file THIRD (additional recommendations)
--
-- 4. After deployment:
--    - Clear application cache
--    - Test each HEAD role login
--    - Verify menu access for:
--      * Warehouse > Inventory (Head Purchasing only)
--      * Activity > Activity Logs (All HEAD roles)
--      * Settings > Notifications (All HEAD roles)
--    - Check activity logs are recording properly
--
-- 5. Monitor for 24 hours:
--    - Check error logs for permission denied errors
--    - Verify notification customization works
--    - Confirm audit trail is complete
-- ============================================================================

-- ============================================================================
-- END OF ADDITIONAL RECOMMENDATIONS MIGRATION
-- ============================================================================
-- Total Expected Changes:
-- - New permissions: 0 (all exist)
-- - New role_permission assignments: ~46 rows
--   * Head Purchasing Inventory: 4 rows
--   * Activity Logs (7 roles × 3): 21 rows
--   * Notification Settings (7 roles × 3): 21 rows
--
-- Deployment Time: ~5-10 seconds
-- Risk Level: LOW (purely additive, no deletions)
-- ============================================================================
