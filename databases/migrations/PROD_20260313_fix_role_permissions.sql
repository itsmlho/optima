-- ============================================
-- FIX ROLE PERMISSION ASSIGNMENTS
-- Date: 2026-03-13
-- Purpose: Properly assign permissions to all roles
-- ============================================

-- ============================================
-- STEP 1: CLEAN UP - Remove all existing role_permissions (except Super Admin)
-- ============================================
DELETE FROM role_permissions WHERE role_id != 1;

-- ============================================
-- STEP 2: ADMINISTRATOR (role_id 30)
-- Grant ALL permissions
-- ============================================
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 30, id, 1, NOW()
FROM permissions
WHERE is_active = 1;

-- ============================================
-- STEP 3: HEAD MARKETING (role_id 2)
-- ============================================
-- Grant ALL marketing permissions
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 2, id, 1, NOW()
FROM permissions
WHERE module = 'marketing' AND is_active = 1;

-- Grant dashboard view
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 2, id, 1, NOW()
FROM permissions
WHERE module = 'dashboard' AND action IN ('navigation', 'view') AND is_active = 1;

-- Grant reports view
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 2, id, 1, NOW()
FROM permissions
WHERE module = 'reports' AND action IN ('navigation', 'view', 'export') AND is_active = 1;

-- CROSS-DIVISION: Inventory Unit, Attachment, SILO, Tracking
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 2, id, 1, NOW()
FROM permissions
WHERE ((module = 'warehouse' AND page IN ('unit_inventory', 'attachment_inventory', 'unit_tracking'))
    OR (module = 'perizinan' AND page = 'silo'))
   AND is_active = 1;

-- CROSS-DIVISION: Temporary Units Report (for tracking unit deployments)
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 2, id, 1, NOW()
FROM permissions
WHERE module = 'operational' AND page = 'temporary_units_report' AND is_active = 1;

-- ============================================
-- STEP 4: STAFF MARKETING (role_id 3)
-- ============================================
-- Grant marketing permissions (NO DELETE)
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 3, id, 1, NOW()
FROM permissions
WHERE module = 'marketing' 
  AND action NOT IN ('delete', 'approve')
  AND is_active = 1;

-- Grant dashboard view only
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 3, id, 1, NOW()
FROM permissions
WHERE module = 'dashboard' AND action IN ('navigation', 'view') AND is_active = 1;

-- CROSS-DIVISION: Inventory Unit, Attachment, SILO, Tracking (VIEW ONLY)
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 3, id, 1, NOW()
FROM permissions
WHERE ((module = 'warehouse' AND page IN ('unit_inventory', 'attachment_inventory', 'unit_tracking'))
    OR (module = 'perizinan' AND page = 'silo'))
   AND action NOT IN ('delete', 'approve')
   AND is_active = 1;

-- CROSS-DIVISION: Temporary Units Report (VIEW ONLY)
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 3, id, 1, NOW()
FROM permissions
WHERE module = 'operational' 
  AND page = 'temporary_units_report' 
  AND action IN ('navigation', 'view')
  AND is_active = 1;

-- ============================================
-- STEP 5: HEAD OPERATIONAL (role_id 4)
-- ============================================
-- Grant ALL operational permissions
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 4, id, 1, NOW()
FROM permissions
WHERE module = 'operational' AND is_active = 1;

-- Grant dashboard
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 4, id, 1, NOW()
FROM permissions
WHERE module = 'dashboard' AND action IN ('navigation', 'view') AND is_active = 1;

-- Grant reports
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 4, id, 1, NOW()
FROM permissions
WHERE module = 'reports' AND action IN ('navigation', 'view', 'export') AND is_active = 1;

-- CROSS-DIVISION: Inventory Unit (untuk koordinasi delivery)
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 4, id, 1, NOW()
FROM permissions
WHERE module = 'warehouse' AND page IN ('unit_inventory') AND is_active = 1;

-- RECOMMENDATION: Quotation & SPK view (untuk koordinasi)
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 4, id, 1, NOW()
FROM permissions
WHERE module = 'marketing' 
  AND page IN ('quotation', 'spk')
  AND action IN ('navigation', 'view')
  AND is_active = 1;

-- ============================================
-- STEP 6: STAFF OPERATIONAL (role_id 5)
-- ============================================
-- Grant operational permissions (NO DELETE)
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 5, id, 1, NOW()
FROM permissions
WHERE module = 'operational' 
  AND action NOT IN ('delete', 'approve')
  AND is_active = 1;

-- Grant dashboard view only
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 5, id, 1, NOW()
FROM permissions
WHERE module = 'dashboard' AND action IN ('navigation', 'view') AND is_active = 1;

-- CROSS-DIVISION: Inventory Unit (VIEW ONLY)
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 5, id, 1, NOW()
FROM permissions
WHERE module = 'warehouse' 
  AND page IN ('unit_inventory')
  AND action IN ('navigation', 'view')
  AND is_active = 1;

-- RECOMMENDATION: Quotation & SPK view (untuk koordinasi)
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 5, id, 1, NOW()
FROM permissions
WHERE module = 'marketing' 
  AND page IN ('quotation', 'spk')
  AND action IN ('navigation', 'view')
  AND is_active = 1;

-- ============================================
-- STEP 7: HEAD PURCHASING (role_id 10)
-- ============================================
-- Grant ALL purchasing permissions
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 10, id, 1, NOW()
FROM permissions
WHERE module = 'purchasing' AND is_active = 1;

-- Grant dashboard
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 10, id, 1, NOW()
FROM permissions
WHERE module = 'dashboard' AND action IN ('navigation', 'view') AND is_active = 1;

-- Grant reports
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 10, id, 1, NOW()
FROM permissions
WHERE module = 'reports' AND action IN ('navigation', 'view', 'export') AND is_active = 1;

-- ============================================
-- STEP 8: STAFF PURCHASING (role_id 11)
-- ============================================
-- Grant purchasing permissions (NO DELETE)
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 11, id, 1, NOW()
FROM permissions
WHERE module = 'purchasing' 
  AND action NOT IN ('delete', 'approve')
  AND is_active = 1;

-- Grant dashboard view only
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 11, id, 1, NOW()
FROM permissions
WHERE module = 'dashboard' AND action IN ('navigation', 'view') AND is_active = 1;

-- ============================================
-- STEP 9: HEAD ACCOUNTING (role_id 12)
-- ============================================
-- Grant ALL accounting AND finance permissions
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 12, id, 1, NOW()
FROM permissions
WHERE module IN ('accounting', 'finance') AND is_active = 1;

-- CROSS-DIVISION: SEMUA halaman MARKETING (untuk invoice/kontrak)
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 12, id, 1, NOW()
FROM permissions
WHERE module = 'marketing' AND is_active = 1;

-- Grant dashboard
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 12, id, 1, NOW()
FROM permissions
WHERE module = 'dashboard' AND action IN ('navigation', 'view') AND is_active = 1;

-- Grant reports
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 12, id, 1, NOW()
FROM permissions
WHERE module = 'reports' AND action IN ('navigation', 'view', 'export') AND is_active = 1;

-- ============================================
-- STEP 10: STAFF ACCOUNTING (role_id 13)
-- ============================================
-- Grant accounting/finance permissions (NO DELETE)
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 13, id, 1, NOW()
FROM permissions
WHERE module IN ('accounting', 'finance')
  AND action NOT IN ('delete', 'approve')
  AND is_active = 1;

-- CROSS-DIVISION: SEMUA halaman MARKETING (NO DELETE)
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 13, id, 1, NOW()
FROM permissions
WHERE module = 'marketing' 
  AND action NOT IN ('delete', 'approve')
  AND is_active = 1;

-- Grant dashboard view only
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 13, id, 1, NOW()
FROM permissions
WHERE module = 'dashboard' AND action IN ('navigation', 'view') AND is_active = 1;

-- ============================================
-- STEP 11: HEAD HRD (role_id 14)
-- ============================================
-- Strategy: Grant specific modules with FULL access first, then VIEW ONLY for others

-- 1. Grant employee/division/position management (FULL CRUD)
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 14, id, 1, NOW()
FROM permissions
WHERE ((module = 'admin' AND page IN ('employee', 'division', 'position'))
    OR (module = 'settings' AND page IN ('division')))
   AND is_active = 1;

-- 2. Grant dashboard (FULL access)
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 14, id, 1, NOW()
FROM permissions
WHERE module = 'dashboard' AND is_active = 1;

-- 3. Grant reports (FULL access)
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 14, id, 1, NOW()
FROM permissions
WHERE module = 'reports' AND is_active = 1;

-- 4. Grant VIEW ONLY for all OTHER modules (exclude already granted modules)
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 14, id, 1, NOW()
FROM permissions
WHERE module NOT IN ('admin', 'settings', 'dashboard', 'reports')
  AND action IN ('navigation', 'view', 'export', 'print')
  AND is_active = 1;

-- ============================================
-- STEP 12: STAFF HRD (role_id 15)
-- ============================================
-- Grant HRD permissions (NO DELETE)
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 15, id, 1, NOW()
FROM permissions
WHERE (module = 'admin' AND page IN ('employee'))
  AND action NOT IN ('delete', 'approve')
  AND is_active = 1;

-- Grant dashboard view only
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 15, id, 1, NOW()
FROM permissions
WHERE module = 'dashboard' AND action IN ('navigation', 'view') AND is_active = 1;

-- ============================================
-- STEP 13: HEAD WAREHOUSE (role_id 16)
-- ============================================
-- Grant ALL warehouse permissions
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 16, id, 1, NOW()
FROM permissions
WHERE module = 'warehouse' AND is_active = 1;

-- CROSS-DIVISION: PO Unit & Attachment (untuk koordinasi)
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 16, id, 1, NOW()
FROM permissions
WHERE module = 'purchasing' 
  AND page IN ('po', 'po_attachment')
  AND is_active = 1;

-- Grant dashboard
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 16, id, 1, NOW()
FROM permissions
WHERE module = 'dashboard' AND action IN ('navigation', 'view') AND is_active = 1;

-- Grant reports
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 16, id, 1, NOW()
FROM permissions
WHERE module = 'reports' AND action IN ('navigation', 'view', 'export') AND is_active = 1;

-- CROSS-DIVISION: Temporary Units Report (untuk tracking unit movements)
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 16, id, 1, NOW()
FROM permissions
WHERE module = 'operational' AND page = 'temporary_units_report' AND is_active = 1;

-- ============================================
-- STEP 14: STAFF WAREHOUSE (role_id 32)
-- ============================================
-- Grant warehouse permissions (NO DELETE)
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 32, id, 1, NOW()
FROM permissions
WHERE module = 'warehouse' 
  AND action NOT IN ('delete', 'approve')
  AND is_active = 1;

-- CROSS-DIVISION: PO Unit & Attachment (VIEW + EDIT)
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 32, id, 1, NOW()
FROM permissions
WHERE module = 'purchasing' 
  AND page IN ('po', 'po_attachment')
  AND action NOT IN ('delete')
  AND is_active = 1;

-- Grant dashboard view only
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 32, id, 1, NOW()
FROM permissions
WHERE module = 'dashboard' AND action IN ('navigation', 'view') AND is_active = 1;

-- CROSS-DIVISION: Temporary Units Report (VIEW ONLY)
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 32, id, 1, NOW()
FROM permissions
WHERE module = 'operational' 
  AND page = 'temporary_units_report' 
  AND action IN ('navigation', 'view')
  AND is_active = 1;

-- ============================================
-- STEP 15: HEAD IT (role_id 33)
-- ============================================
-- Grant ALL admin, settings, activity permissions
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 33, id, 1, NOW()
FROM permissions
WHERE module IN ('admin', 'settings', 'activity') AND is_active = 1;

-- Grant dashboard
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 33, id, 1, NOW()
FROM permissions
WHERE module = 'dashboard' AND is_active = 1;

-- Grant reports
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 33, id, 1, NOW()
FROM permissions
WHERE module = 'reports' AND is_active = 1;

-- ============================================
-- STEP 16: STAFF IT (role_id 34)
-- ============================================
-- Grant admin/settings permissions (NO DELETE, LIMITED)
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 34, id, 1, NOW()
FROM permissions
WHERE module IN ('admin', 'settings', 'activity')
  AND action NOT IN ('delete')
  AND is_active = 1;

-- Grant dashboard
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 34, id, 1, NOW()
FROM permissions
WHERE module = 'dashboard' AND is_active = 1;

-- ============================================
-- STEP 17: HEAD SERVICE (role_id 35)
-- ============================================
-- Grant ALL service permissions
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 35, id, 1, NOW()
FROM permissions
WHERE module = 'service' AND is_active = 1;

-- CROSS-DIVISION: Inventory Unit (FULL), Attachment (FULL), SILO, Tracking, Sparepart Usage, PO Verification, Surat Jalan
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 35, id, 1, NOW()
FROM permissions
WHERE ((module = 'warehouse' AND page IN ('unit_inventory', 'attachment_inventory', 'unit_tracking', 'sparepart_usage', 'surat_jalan'))
    OR (module = 'perizinan' AND page IN ('silo'))
    OR (module = 'purchasing' AND page IN ('po_verification')))
   AND is_active = 1;

-- Grant dashboard
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 35, id, 1, NOW()
FROM permissions
WHERE module = 'dashboard' AND action IN ('navigation', 'view') AND is_active = 1;

-- Grant reports
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 35, id, 1, NOW()
FROM permissions
WHERE module = 'reports' AND action IN ('navigation', 'view', 'export') AND is_active = 1;

-- CROSS-DIVISION: Temporary Units Report
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 35, id, 1, NOW()
FROM permissions
WHERE module = 'operational' AND page = 'temporary_units_report' AND is_active = 1;

-- ============================================
-- STEP 18: ADMIN SERVICE PUSAT (role_id 36)
-- ============================================
-- Grant service permissions (LIMITED - No user management)
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 36, id, 1, NOW()
FROM permissions
WHERE module = 'service' 
  AND page NOT IN ('user_management')
  AND is_active = 1;

-- CROSS-DIVISION: Inventory Unit (FULL), Attachment (FULL), SILO, Tracking, Sparepart Usage, PO Verification, Surat Jalan
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 36, id, 1, NOW()
FROM permissions
WHERE ((module = 'warehouse' AND page IN ('unit_inventory', 'attachment_inventory', 'unit_tracking', 'sparepart_usage', 'surat_jalan'))
    OR (module = 'perizinan' AND page IN ('silo'))
    OR (module = 'purchasing' AND page IN ('po_verification')))
   AND is_active = 1;

-- Grant dashboard view
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 36, id, 1, NOW()
FROM permissions
WHERE module = 'dashboard' AND action IN ('navigation', 'view') AND is_active = 1;

-- CROSS-DIVISION: Temporary Units Report
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 36, id, 1, NOW()
FROM permissions
WHERE module = 'operational' AND page = 'temporary_units_report' AND is_active = 1;

-- ============================================
-- STEP 19: ADMIN SERVICE AREA (role_id 37)
-- ============================================
-- Grant service permissions (LIMITED - No delete, No user management)
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 37, id, 1, NOW()
FROM permissions
WHERE module = 'service' 
  AND page NOT IN ('user_management')
  AND action NOT IN ('delete')
  AND is_active = 1;

-- CROSS-DIVISION: Inventory Unit (FULL), Attachment (FULL), SILO, Tracking, Sparepart Usage, PO Verification, Surat Jalan
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 37, id, 1, NOW()
FROM permissions
WHERE ((module = 'warehouse' AND page IN ('unit_inventory', 'attachment_inventory', 'unit_tracking', 'sparepart_usage', 'surat_jalan'))
    OR (module = 'perizinan' AND page IN ('silo'))
    OR (module = 'purchasing' AND page IN ('po_verification')))
   AND is_active = 1;

-- Grant dashboard view
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 37, id, 1, NOW()
FROM permissions
WHERE module = 'dashboard' AND action IN ('navigation', 'view') AND is_active = 1;

-- CROSS-DIVISION: Temporary Units Report (VIEW ONLY)
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 37, id, 1, NOW()
FROM permissions
WHERE module = 'operational' 
  AND page = 'temporary_units_report' 
  AND action IN ('navigation', 'view')
  AND is_active = 1;

-- ============================================
-- STEP 20: SUPERVISOR SERVICE (role_id 38)
-- ============================================
-- Grant service permissions (LIMITED - View, Create, Edit only)
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 38, id, 1, NOW()
FROM permissions
WHERE module = 'service' 
  AND page IN ('workorder', 'pmps')
  AND action IN ('navigation', 'view', 'create', 'edit')
  AND is_active = 1;

-- CROSS-DIVISION: Inventory Unit, Attachment, SILO, Tracking, Sparepart Usage, Surat Jalan (VIEW + EDIT)
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 38, id, 1, NOW()
FROM permissions
WHERE ((module = 'warehouse' AND page IN ('unit_inventory', 'attachment_inventory', 'unit_tracking', 'sparepart_usage', 'surat_jalan'))
    OR (module = 'perizinan' AND page IN ('silo')))
   AND action IN ('navigation', 'view', 'edit', 'create')
   AND is_active = 1;

-- Grant dashboard view
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 38, id, 1, NOW()
FROM permissions
WHERE module = 'dashboard' AND action IN ('navigation', 'view') AND is_active = 1;

-- CROSS-DIVISION: Temporary Units Report (VIEW ONLY)
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 38, id, 1, NOW()
FROM permissions
WHERE module = 'operational' 
  AND page = 'temporary_units_report' 
  AND action IN ('navigation', 'view')
  AND is_active = 1;

-- ============================================
-- STEP 21: STAFF SERVICE (role_id 39)
-- ============================================
-- Grant service permissions (READ ONLY + Create work order)
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 39, id, 1, NOW()
FROM permissions
WHERE module = 'service' 
  AND page IN ('workorder')
  AND action IN ('navigation', 'view', 'create')
  AND is_active = 1;

-- CROSS-DIVISION: Inventory Unit, Attachment, SILO, Tracking, Sparepart Usage (VIEW ONLY)
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 39, id, 1, NOW()
FROM permissions
WHERE ((module = 'warehouse' AND page IN ('unit_inventory', 'attachment_inventory', 'unit_tracking', 'sparepart_usage'))
    OR (module = 'perizinan' AND page IN ('silo')))
   AND action IN ('navigation', 'view')
   AND is_active = 1;

-- Grant dashboard view
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 39, id, 1, NOW()
FROM permissions
WHERE module = 'dashboard' AND action IN ('navigation', 'view') AND is_active = 1;

-- CROSS-DIVISION: Temporary Units Report (VIEW ONLY)
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 39, id, 1, NOW()
FROM permissions
WHERE module = 'operational' 
  AND page = 'temporary_units_report' 
  AND action IN ('navigation', 'view')
  AND is_active = 1;

-- ============================================
-- STEP 22: MANAGER SERVICE AREA (role_id 40)
-- ============================================
-- Grant service permissions (Similar to Admin Service Area + more access)
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 40, id, 1, NOW()
FROM permissions
WHERE module = 'service' 
  AND page NOT IN ('user_management')
  AND is_active = 1;

-- CROSS-DIVISION: Inventory Unit (FULL), Attachment (FULL), SILO, Tracking, Sparepart Usage, PO Verification, Surat Jalan
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 40, id, 1, NOW()
FROM permissions
WHERE ((module = 'warehouse' AND page IN ('unit_inventory', 'attachment_inventory', 'unit_tracking', 'sparepart_usage', 'surat_jalan'))
    OR (module = 'perizinan' AND page IN ('silo'))
    OR (module = 'purchasing' AND page IN ('po_verification')))
   AND is_active = 1;

-- Grant dashboard
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 40, id, 1, NOW()
FROM permissions
WHERE module = 'dashboard' AND action IN ('navigation', 'view') AND is_active = 1;

-- Grant reports
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 40, id, 1, NOW()
FROM permissions
WHERE module = 'reports' 
  AND page IN ('service')
  AND action IN ('navigation', 'view', 'export') 
  AND is_active = 1;

-- CROSS-DIVISION: Temporary Units Report
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 40, id, 1, NOW()
FROM permissions
WHERE module = 'operational' AND page = 'temporary_units_report' AND is_active = 1;

-- ============================================
-- VERIFICATION QUERIES
-- ============================================

-- Check total permissions per role
SELECT 
    r.id,
    r.name,
    COUNT(rp.permission_id) as total_permissions
FROM roles r
LEFT JOIN role_permissions rp ON r.id = rp.role_id AND rp.granted = 1
GROUP BY r.id, r.name
ORDER BY r.id;

-- Check modules accessible by each role
SELECT 
    r.name as role_name,
    p.module,
    COUNT(DISTINCT p.id) as permission_count
FROM roles r
LEFT JOIN role_permissions rp ON r.id = rp.role_id AND rp.granted = 1
LEFT JOIN permissions p ON rp.permission_id = p.id AND p.is_active = 1
WHERE r.id != 1  -- Exclude Super Admin
GROUP BY r.name, p.module
ORDER BY r.name, p.module;

-- Verify Administrator has ALL permissions
SELECT 
    'Administrator Total' as label,
    COUNT(*) as should_have,
    (SELECT COUNT(*) FROM role_permissions WHERE role_id = 30 AND granted = 1) as actually_has
FROM permissions
WHERE is_active = 1;

-- ============================================
-- NOTES:
-- ============================================
-- Role Hierarchy:
-- 
-- ADMIN/IT:
--   - Administrator (30): ALL permissions
--   - Super Admin (1): ALL permissions (auto-bypass)
--   - Head IT (33): All admin, settings, activity, dashboard, reports
--   - Staff IT (34): Limited admin, settings, activity (no delete)
--
-- MARKETING:
--   - Head Marketing (2): All marketing + reports + dashboard
--   - Staff Marketing (3): Limited marketing (no delete/approve)
--
-- OPERATIONAL:
--   - Head Operational (4): All operational + reports + dashboard
--   - Staff Operational (5): Limited operational (no delete/approve)
--
-- PURCHASING:
--   - Head Purchasing (10): All purchasing + reports + dashboard
--   - Staff Purchasing (11): Limited purchasing (no delete/approve)
--
-- ACCOUNTING/FINANCE:
--   - Head Accounting (12): All accounting + finance + reports + dashboard
--   - Staff Accounting (13): Limited accounting + finance (no delete/approve)
--
-- HRD:
--   - Head HRD (14): Employee, division, position management + reports + dashboard
--   - Staff HRD (15): Employee management only (no delete)
--
-- WAREHOUSE:
--   - Head Warehouse (16): All warehouse + reports + dashboard
--   - Staff Warehouse (32): Limited warehouse (no delete/approve)
--
-- SERVICE:
--   - Head Service (35): All service + reports + dashboard
--   - Admin Service Pusat (36): All service except user management
--   - Admin Service Area (37): Limited service (no delete, no user management)
--   - Supervisor Service (38): Work order + PMPS only (view, create, edit)
--   - Staff Service (39): Work order only (view, create)
--   - Manager Service Area (40): All service except user management + reports
