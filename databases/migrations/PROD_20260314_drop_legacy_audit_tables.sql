-- ============================================================================
-- MIGRATION: Drop Legacy Audit Tables (After Verification)
-- Date: 2026-03-14
-- Purpose: Drop 3 old audit tables after verifying all data has been migrated
--          to component_audit_log
-- ============================================================================
-- 
-- INSTRUCTIONS:
-- 1. Run PROD_20260314_component_audit_log.sql FIRST
-- 2. Verify data migration by running verification queries below
-- 3. Only run this script AFTER confirming data is correct
-- 4. Keep backups of old tables data before dropping
--
-- ============================================================================

-- ============================================================================
-- VERIFICATION QUERIES (RUN BEFORE DROPPING)
-- ============================================================================

-- 1. Check row counts match
SELECT 
    'inventory_item_unit_log' as source_table,
    COUNT(*) as old_count,
    (SELECT COUNT(*) FROM component_audit_log WHERE triggered_by IN ('ASSIGN_TO_UNIT', 'REMOVE_FROM_UNIT', 'DETACH_FROM_UNIT')) as migrated_count
FROM inventory_item_unit_log
UNION ALL
SELECT 
    'component_timeline' as source_table,
    COUNT(*) as old_count,
    (SELECT COUNT(*) FROM component_audit_log WHERE event_category IN ('ASSIGNMENT', 'TRANSFER', 'MAINTENANCE')) as migrated_count
FROM component_timeline
UNION ALL
SELECT 
    'attachment_transfer_log' as source_table,
    COUNT(*) as old_count,
    (SELECT COUNT(*) FROM component_audit_log WHERE triggered_by LIKE '%FABRIKASI%' OR triggered_by LIKE '%DI_WORKFLOW%') as migrated_count
FROM attachment_transfer_log;

-- 2. Check total records in component_audit_log
SELECT COUNT(*) as total_component_audit_log FROM component_audit_log;

-- ============================================================================
-- STEP 1: Rename old tables (keep as backup for 30 days)
-- ============================================================================

RENAME TABLE 
    inventory_item_unit_log TO _deprecated_inventory_item_unit_log,
    component_timeline TO _deprecated_component_timeline,
    attachment_transfer_log TO _deprecated_attachment_transfer_log;

-- ============================================================================
-- STEP 2: Drop tables (ONLY after verification and 30-day backup period)
-- Run this section separately after confirming everything works
-- ============================================================================

-- UNCOMMENT AND RUN ONLY AFTER 30 DAYS OF VERIFICATION:
-- DROP TABLE IF EXISTS _deprecated_inventory_item_unit_log;
-- DROP TABLE IF EXISTS _deprecated_component_timeline;
-- DROP TABLE IF EXISTS _deprecated_attachment_transfer_log;

-- ============================================================================

SELECT 'Legacy audit tables renamed with _deprecated_ prefix. Run verification queries above.' AS status;
