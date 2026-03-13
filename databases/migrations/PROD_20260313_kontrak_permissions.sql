-- ============================================
-- ADD CRITICAL MISSING PERMISSIONS ONLY
-- Date: 2026-03-13
-- Purpose: Add ONLY the Kontrak CRUD permissions that are missing
-- Note: permissions.csv already has 348 permissions (updated March 7, 2026)
-- ============================================

-- ============================================
-- Check current state
-- ============================================
SELECT 
    '=== BEFORE MIGRATION ===' as status,
    MAX(id) as max_permission_id,
    COUNT(*) as total_permissions
FROM permissions;

-- Check existing Kontrak permissions
SELECT 
    '=== EXISTING KONTRAK PERMISSIONS ===' as status;
SELECT id, module, page, action, key_name, display_name
FROM permissions 
WHERE page = 'kontrak' 
ORDER BY id;

-- ============================================
-- 1. KONTRAK MODULE - Add Missing CRUD
-- Current: Only has 'view_cross_division' (ID 150)
-- Adding: navigation, view, create, edit, delete, approve, print, export
-- ============================================
INSERT IGNORE INTO permissions (module, page, action, key_name, display_name, description, category, is_active, created_at, updated_at) VALUES
('marketing', 'kontrak', 'navigation', 'marketing.kontrak.navigation', 'Marketing - Kontrak - Navigation', 'Access to Kontrak management menu', 'navigation', 1, NOW(), NOW()),
('marketing', 'kontrak', 'view', 'marketing.kontrak.view', 'Marketing - Kontrak - View', 'View kontrak list', 'read', 1, NOW(), NOW()),
('marketing', 'kontrak', 'create', 'marketing.kontrak.create', 'Marketing - Kontrak - Create', 'Create new kontrak', 'write', 1, NOW(), NOW()),
('marketing', 'kontrak', 'edit', 'marketing.kontrak.edit', 'Marketing - Kontrak - Edit', 'Edit kontrak data', 'write', 1, NOW(), NOW()),
('marketing', 'kontrak', 'delete', 'marketing.kontrak.delete', 'Marketing - Kontrak - Delete', 'Delete kontrak', 'delete', 1, NOW(), NOW()),
('marketing', 'kontrak', 'approve', 'marketing.kontrak.approve', 'Marketing - Kontrak - Approve', 'Approve kontrak', 'action', 1, NOW(), NOW()),
('marketing', 'kontrak', 'print', 'marketing.kontrak.print', 'Marketing - Kontrak - Print', 'Print kontrak document', 'action', 1, NOW(), NOW()),
('marketing', 'kontrak', 'export', 'marketing.kontrak.export', 'Marketing - Kontrak - Export', 'Export kontrak data', 'export', 1, NOW(), NOW());

-- ============================================
-- 2. TEMPORARY UNITS REPORT - Add Permission
-- Route: operational/temporary-units-report
-- For: Cross-division tracking of temporary unit deployments
-- ============================================
INSERT IGNORE INTO permissions (module, page, action, key_name, display_name, description, category, is_active, created_at, updated_at) VALUES
('operational', 'temporary_units_report', 'navigation', 'operational.temporary_units_report.navigation', 'Operational - Temporary Units Report - Navigation', 'Access to Temporary Units Report', 'navigation', 1, NOW(), NOW()),
('operational', 'temporary_units_report', 'view', 'operational.temporary_units_report.view', 'Operational - Temporary Units Report - View', 'View temporary units tracking report', 'read', 1, NOW(), NOW()),
('operational', 'temporary_units_report', 'export', 'operational.temporary_units_report.export', 'Operational - Temporary Units Report - Export', 'Export temporary units data', 'export', 1, NOW(), NOW());

-- ============================================
-- Verification
-- ============================================
SELECT 
    '=== AFTER MIGRATION ===' as status,
    MAX(id) as max_permission_id,
    COUNT(*) as total_permissions
FROM permissions;

-- Check ALL Kontrak permissions after migration
SELECT 
    '=== ALL KONTRAK PERMISSIONS AFTER MIGRATION ===' as status;
SELECT id, module, page, action, key_name, display_name
FROM permissions 
WHERE page = 'kontrak' 
ORDER BY action;

-- ============================================
-- NOTES:
-- ============================================
-- Total permissions added: 11 (8 Kontrak CRUD + 3 Temporary Units Report)
-- Expected new total: 348 + 11 = 359 permissions
-- 
-- Next step: Run PROD_20260313_fix_role_permissions.sql
-- to properly assign all permissions to departmental roles.
-- 
-- Modules that already have sufficient permissions (no action needed):
-- - Finance: 21 permissions ✅
-- - Reports: 12 permissions ✅
-- - Service: 57 permissions ✅
-- - Warehouse: 50 permissions ✅
-- - Admin: 31 permissions ✅
-- - Settings: 30 permissions ✅
-- - Accounting: 12 permissions ✅
-- - Dashboard: 5 permissions ✅
-- - Marketing: 60 permissions (will be 68 after this migration) ✅
-- - Operational: 26 permissions (will be 29 after this migration) ✅
