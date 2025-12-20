-- ============================================================================
-- CROSS-DIVISION NOTIFICATION RULES - Database Updates
-- Implementation Date: 2024-12-19
-- Purpose: Add missing notification rules and update existing ones
-- ============================================================================

-- BACKUP EXISTING RULES FIRST (Optional - for safety)
-- CREATE TABLE notification_rules_backup_20241219 AS SELECT * FROM notification_rules;

-- ============================================================================
-- SECTION 1: UPDATE EXISTING RULES
-- ============================================================================

-- 1.1 Update PO Verified to include Warehouse division
-- Warehouse performs verification, so they need to be notified
UPDATE notification_rules 
SET 
    target_divisions = 'purchasing,warehouse',
    updated_at = NOW()
WHERE 
    id = 76 
    AND trigger_event = 'po_verified' 
    AND is_active = 1;

-- Verify update
SELECT id, name, trigger_event, target_divisions 
FROM notification_rules 
WHERE trigger_event = 'po_verified';

-- 1.2 Update Inventory Unit Status Changed to include Service division
-- Service needs to know when unit status changes in warehouse
UPDATE notification_rules 
SET 
    target_divisions = 'warehouse,service',
    updated_at = NOW()
WHERE 
    id = 55 
    AND trigger_event = 'inventory_unit_status_changed' 
    AND is_active = 1;

-- Verify update
SELECT id, name, trigger_event, target_divisions 
FROM notification_rules 
WHERE trigger_event = 'inventory_unit_status_changed';

-- ============================================================================
-- SECTION 2: INSERT NEW SPK STAGE RULES
-- ============================================================================

-- 2.1 SPK Unit Preparation Completed
-- Notifies: Marketing (success), Warehouse (items report)
INSERT INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    title_template,
    message_template,
    is_active,
    priority,
    created_at,
    updated_at
) VALUES (
    'SPK Unit Preparation Completed',
    'spk_unit_prep_completed',
    'marketing,warehouse',
    'manager,supervisor',
    'Unit Preparation Completed - {{spk_number}}',
    'Unit preparation has been completed for SPK {{spk_number}}. Customer: {{pelanggan}}. Approved by: {{approved_by}}.',
    1,
    'medium',
    NOW(),
    NOW()
);

-- 2.2 SPK Fabrication Completed
-- Notifies: Marketing (success), Warehouse (attachment report)
INSERT INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    title_template,
    message_template,
    is_active,
    priority,
    created_at,
    updated_at
) VALUES (
    'SPK Fabrication Completed',
    'spk_fabrication_completed',
    'marketing,warehouse',
    'manager,supervisor',
    'Fabrication Completed - {{spk_number}}',
    'Fabrication has been completed for SPK {{spk_number}}. Customer: {{pelanggan}}. Approved by: {{approved_by}}.',
    1,
    'medium',
    NOW(),
    NOW()
);

-- 2.3 SPK PDI Completed - Unit Ready for Delivery
-- Notifies: Marketing (SPK ready), Operational (ready for DI)
INSERT INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    title_template,
    message_template,
    is_active,
    priority,
    created_at,
    updated_at
) VALUES (
    'SPK PDI Completed - Ready for Delivery',
    'spk_pdi_completed',
    'marketing,operational',
    'manager,supervisor,staff',
    'PDI Completed - SPK Ready - {{spk_number}}',
    'PDI has been completed for SPK {{spk_number}}. Unit is now READY for delivery. Customer: {{pelanggan}}. Approved by: {{approved_by}}.',
    1,
    'high',
    NOW(),
    NOW()
);

-- ============================================================================
-- SECTION 3: INSERT SPAREPART RETURN RULE
-- ============================================================================

-- 3.1 Sparepart Return Confirmed
-- Notifies: Service (sparepart availability updated)
INSERT INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    title_template,
    message_template,
    is_active,
    priority,
    created_at,
    updated_at
) VALUES (
    'Sparepart Return Confirmed',
    'sparepart_returned',
    'service',
    'manager,supervisor',
    'Sparepart Return Confirmed - {{sparepart_name}}',
    'Sparepart return has been confirmed. Item: {{sparepart_name}}, Quantity: {{quantity}}. Returned by: {{returned_by}}, Confirmed by: {{confirmed_by}}.',
    1,
    'low',
    NOW(),
    NOW()
);

-- ============================================================================
-- SECTION 4: VERIFICATION QUERIES
-- ============================================================================

-- 4.1 Verify all new SPK stage rules were created
SELECT 
    id, 
    name, 
    trigger_event, 
    target_divisions, 
    target_roles,
    is_active
FROM notification_rules 
WHERE trigger_event IN (
    'spk_unit_prep_completed',
    'spk_fabrication_completed',
    'spk_pdi_completed'
)
ORDER BY trigger_event;

-- Expected: 3 rows

-- 4.2 Verify sparepart return rule was created
SELECT 
    id, 
    name, 
    trigger_event, 
    target_divisions, 
    target_roles,
    is_active
FROM notification_rules 
WHERE trigger_event = 'sparepart_returned';

-- Expected: 1 row

-- 4.3 Verify updated rules
SELECT 
    id, 
    name, 
    trigger_event, 
    target_divisions, 
    target_roles,
    updated_at
FROM notification_rules 
WHERE trigger_event IN ('po_verified', 'inventory_unit_status_changed')
ORDER BY trigger_event;

-- Expected: 
-- po_verified → target_divisions = 'purchasing,warehouse'
-- inventory_unit_status_changed → target_divisions = 'warehouse,service'

-- 4.4 Count total active notification rules
SELECT COUNT(*) as total_active_rules
FROM notification_rules
WHERE is_active = 1;

-- Expected: Previous count + 4 (if all inserts successful)

-- 4.5 Verify cross-division notification coverage
SELECT 
    'marketing' as division,
    COUNT(*) as notification_count
FROM notification_rules
WHERE is_active = 1 AND target_divisions LIKE '%marketing%'
UNION ALL
SELECT 
    'service' as division,
    COUNT(*) as notification_count
FROM notification_rules
WHERE is_active = 1 AND target_divisions LIKE '%service%'
UNION ALL
SELECT 
    'warehouse' as division,
    COUNT(*) as notification_count
FROM notification_rules
WHERE is_active = 1 AND target_divisions LIKE '%warehouse%'
UNION ALL
SELECT 
    'operational' as division,
    COUNT(*) as notification_count
FROM notification_rules
WHERE is_active = 1 AND target_divisions LIKE '%operational%'
UNION ALL
SELECT 
    'purchasing' as division,
    COUNT(*) as notification_count
FROM notification_rules
WHERE is_active = 1 AND target_divisions LIKE '%purchasing%';

-- ============================================================================
-- SECTION 5: ROLLBACK SCRIPT (If Needed)
-- ============================================================================

-- ONLY RUN IF YOU NEED TO UNDO CHANGES

-- 5.1 Rollback PO Verified update
-- UPDATE notification_rules 
-- SET 
--     target_divisions = 'purchasing,accounting',
--     updated_at = NOW()
-- WHERE id = 76 AND trigger_event = 'po_verified';

-- 5.2 Rollback Inventory Unit Status Changed update
-- UPDATE notification_rules 
-- SET 
--     target_divisions = 'warehouse',
--     updated_at = NOW()
-- WHERE id = 55 AND trigger_event = 'inventory_unit_status_changed';

-- 5.3 Delete SPK stage rules
-- DELETE FROM notification_rules 
-- WHERE trigger_event IN (
--     'spk_unit_prep_completed',
--     'spk_fabrication_completed',
--     'spk_pdi_completed'
-- );

-- 5.4 Delete Sparepart return rule
-- DELETE FROM notification_rules 
-- WHERE trigger_event = 'sparepart_returned';

-- ============================================================================
-- END OF SCRIPT
-- ============================================================================

-- EXECUTION NOTES:
-- 1. Backup database before running
-- 2. Run in development/staging first
-- 3. Test each notification type after implementation
-- 4. Monitor application logs for notification delivery
-- 5. Verify users receive notifications in correct divisions

-- CHANGES SUMMARY:
-- - Updated: 2 existing rules (po_verified, inventory_unit_status_changed)
-- - Created: 4 new rules (3 SPK stages + 1 sparepart return)
-- - Total changes: 6 database rules
-- - Affected divisions: Marketing, Service, Warehouse, Operational, Purchasing

-- POST-IMPLEMENTATION TESTING:
-- 1. Test SPK persiapan_unit approval → Check Marketing + Warehouse notifications
-- 2. Test SPK fabrikasi approval → Check Marketing + Warehouse notifications
-- 3. Test SPK pdi approval → Check Marketing + Operational notifications
-- 4. Test sparepart return confirmation → Check Service notifications
-- 5. Test PO verification → Check Purchasing + Warehouse notifications
-- 6. Test inventory unit status change → Check Warehouse + Service notifications
