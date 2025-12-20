-- ============================================================================
-- DATABASE VERIFICATION SCRIPT
-- Cross-Division Notification Rules Audit
-- Generated: <?= date('Y-m-d H:i:s') ?>
-- ============================================================================

-- ============================================================================
-- SECTION 1: CHECK EXISTING RULES TARGET_DIVISIONS
-- ============================================================================

-- 1.1 SPK Created - Should target SERVICE
SELECT 
    id, 
    name, 
    trigger_event, 
    target_divisions,
    CASE 
        WHEN target_divisions = 'service' THEN '✅ CORRECT'
        ELSE '❌ NEEDS UPDATE'
    END as status
FROM notification_rules 
WHERE trigger_event = 'spk_created' AND is_active = 1;

-- Expected: service
-- User requirement: Marketing → Service

-- 1.2 DI Created - Should target OPERATIONAL
SELECT 
    id, 
    name, 
    trigger_event, 
    target_divisions,
    CASE 
        WHEN target_divisions = 'operational' THEN '✅ CORRECT'
        ELSE '❌ NEEDS UPDATE'
    END as status
FROM notification_rules 
WHERE trigger_event = 'di_created' AND is_active = 1;

-- Expected: operational
-- User requirement: Marketing → Operational

-- 1.3 Delivery Created - Should target OPERATIONAL (for DI) OR WAREHOUSE,PURCHASING (for PO)
SELECT 
    id, 
    name, 
    trigger_event, 
    target_divisions,
    CASE 
        WHEN target_divisions LIKE '%warehouse%' AND target_divisions LIKE '%purchasing%' THEN '✅ CORRECT'
        WHEN target_divisions = 'operational' THEN '⚠️ PARTIAL - Need separate rule for PO delivery'
        ELSE '❌ NEEDS UPDATE'
    END as status
FROM notification_rules 
WHERE trigger_event = 'delivery_created' AND is_active = 1;

-- Expected: operational (for DI), OR need new rule for PO delivery → warehouse,purchasing

-- 1.4 Delivery Completed - Should target MARKETING
SELECT 
    id, 
    name, 
    trigger_event, 
    target_divisions,
    CASE 
        WHEN target_divisions = 'marketing' THEN '✅ CORRECT'
        WHEN target_divisions LIKE '%marketing%' THEN '⚠️ INCLUDES OTHERS'
        ELSE '❌ NEEDS UPDATE'
    END as status
FROM notification_rules 
WHERE trigger_event = 'delivery_completed' AND is_active = 1;

-- Expected: marketing
-- User requirement: Operational → Marketing (DI completed)

-- 1.5 PO Verified - Should target PURCHASING,WAREHOUSE
SELECT 
    id, 
    name, 
    trigger_event, 
    target_divisions,
    CASE 
        WHEN target_divisions LIKE '%warehouse%' AND target_divisions LIKE '%purchasing%' THEN '✅ CORRECT'
        WHEN target_divisions LIKE '%purchasing%' THEN '⚠️ MISSING WAREHOUSE'
        ELSE '❌ NEEDS UPDATE'
    END as status
FROM notification_rules 
WHERE trigger_event = 'po_verified' AND is_active = 1;

-- Current: purchasing,accounting
-- Expected: purchasing,warehouse (Warehouse does verification)

-- 1.6 Inventory Unit Status Changed - Should target WAREHOUSE,SERVICE
SELECT 
    id, 
    name, 
    trigger_event, 
    target_divisions,
    CASE 
        WHEN target_divisions LIKE '%warehouse%' AND target_divisions LIKE '%service%' THEN '✅ CORRECT'
        WHEN target_divisions = 'warehouse' THEN '⚠️ MISSING SERVICE'
        ELSE '❌ NEEDS UPDATE'
    END as status
FROM notification_rules 
WHERE trigger_event = 'inventory_unit_status_changed' AND is_active = 1;

-- Current: warehouse
-- Expected: warehouse,service (Service needs to know unit status changes)

-- 1.7 Attachment Operations - Should target SERVICE
SELECT 
    id, 
    name, 
    trigger_event, 
    target_divisions,
    CASE 
        WHEN target_divisions = 'service' THEN '✅ CORRECT'
        ELSE '❌ NEEDS UPDATE'
    END as status
FROM notification_rules 
WHERE trigger_event IN ('attachment_attached', 'attachment_detached', 'attachment_swapped') 
AND is_active = 1
ORDER BY trigger_event;

-- Expected: service (Warehouse performs, Service gets notified)

-- 1.8 Attachment Added - Should target WAREHOUSE
SELECT 
    id, 
    name, 
    trigger_event, 
    target_divisions,
    CASE 
        WHEN target_divisions = 'warehouse' THEN '✅ CORRECT'
        ELSE '❌ NEEDS UPDATE'
    END as status
FROM notification_rules 
WHERE trigger_event = 'attachment_added' AND is_active = 1;

-- Expected: warehouse (Service creates, Warehouse gets notified)

-- ============================================================================
-- SECTION 2: CHECK FOR MISSING RULES
-- ============================================================================

-- 2.1 SPK Stage Rules - Should have 3 separate rules
SELECT 
    COUNT(*) as existing_count,
    CASE 
        WHEN COUNT(*) = 0 THEN '❌ ALL 3 MISSING'
        WHEN COUNT(*) < 3 THEN '⚠️ PARTIAL'
        ELSE '✅ ALL EXIST'
    END as status
FROM notification_rules 
WHERE trigger_event IN (
    'spk_unit_prep_completed',
    'spk_fabrication_completed', 
    'spk_pdi_completed'
) AND is_active = 1;

-- Expected: 3 rules
-- Need to create if missing

-- Show details of existing SPK stage rules
SELECT 
    id, 
    name, 
    trigger_event, 
    target_divisions, 
    target_roles
FROM notification_rules 
WHERE trigger_event IN (
    'spk_unit_prep_completed',
    'spk_fabrication_completed',
    'spk_pdi_completed'
) AND is_active = 1
ORDER BY trigger_event;

-- 2.2 Sparepart Returned Rule
SELECT 
    COUNT(*) as existing_count,
    CASE 
        WHEN COUNT(*) = 0 THEN '❌ MISSING'
        ELSE '✅ EXISTS'
    END as status
FROM notification_rules 
WHERE trigger_event = 'sparepart_returned' AND is_active = 1;

-- Expected: 1 rule targeting SERVICE
-- Show details if exists
SELECT 
    id, 
    name, 
    trigger_event, 
    target_divisions, 
    target_roles
FROM notification_rules 
WHERE trigger_event = 'sparepart_returned' AND is_active = 1;

-- ============================================================================
-- SECTION 3: COMPREHENSIVE CROSS-DIVISION AUDIT
-- ============================================================================

-- 3.1 All Rules with Target Divisions
SELECT 
    id,
    name,
    trigger_event,
    target_divisions,
    target_roles,
    is_active
FROM notification_rules
WHERE is_active = 1 
AND target_divisions IS NOT NULL 
AND target_divisions != ''
ORDER BY trigger_event;

-- 3.2 Rules Without Target Divisions (but with target_roles)
SELECT 
    id,
    name,
    trigger_event,
    target_roles,
    is_active
FROM notification_rules
WHERE is_active = 1 
AND (target_divisions IS NULL OR target_divisions = '')
AND target_roles IS NOT NULL
ORDER BY trigger_event;

-- ============================================================================
-- SECTION 4: RULES THAT NEED UPDATES
-- ============================================================================

-- 4.1 Rules that need target_divisions added/updated
SELECT 
    id,
    name,
    trigger_event,
    target_divisions as current_divisions,
    CASE trigger_event
        WHEN 'po_verified' THEN 'purchasing,warehouse'
        WHEN 'inventory_unit_status_changed' THEN 'warehouse,service'
        WHEN 'delivery_created' THEN 'warehouse,purchasing (for PO) OR operational (for DI)'
        ELSE 'Review needed'
    END as recommended_divisions
FROM notification_rules
WHERE is_active = 1
AND trigger_event IN (
    'po_verified',
    'inventory_unit_status_changed',
    'delivery_created'
);

-- ============================================================================
-- SECTION 5: UPDATE STATEMENTS (COMMENTED - REVIEW BEFORE RUNNING)
-- ============================================================================

-- 5.1 Update PO Verified to include Warehouse
-- UPDATE notification_rules 
-- SET target_divisions = 'purchasing,warehouse'
-- WHERE trigger_event = 'po_verified' AND is_active = 1;

-- 5.2 Update Inventory Unit Status to include Service
-- UPDATE notification_rules 
-- SET target_divisions = 'warehouse,service'
-- WHERE trigger_event = 'inventory_unit_status_changed' AND is_active = 1;

-- ============================================================================
-- SECTION 6: INSERT STATEMENTS FOR MISSING RULES (COMMENTED)
-- ============================================================================

-- 6.1 SPK Unit Prep Completed
-- INSERT INTO notification_rules (
--     name, 
--     trigger_event, 
--     target_divisions, 
--     target_roles,
--     title_template, 
--     message_template, 
--     is_active
-- ) VALUES (
--     'SPK Unit Preparation Completed',
--     'spk_unit_prep_completed',
--     'marketing,warehouse',
--     'manager,supervisor',
--     'Unit Preparation Completed - {{spk_number}}',
--     'Unit preparation has been completed for SPK {{spk_number}}. Customer: {{pelanggan}}. Approved by: {{approved_by}}.',
--     1
-- );

-- 6.2 SPK Fabrication Completed
-- INSERT INTO notification_rules (
--     name, 
--     trigger_event, 
--     target_divisions, 
--     target_roles,
--     title_template, 
--     message_template, 
--     is_active
-- ) VALUES (
--     'SPK Fabrication Completed',
--     'spk_fabrication_completed',
--     'marketing,warehouse',
--     'manager,supervisor',
--     'Fabrication Completed - {{spk_number}}',
--     'Fabrication has been completed for SPK {{spk_number}}. Customer: {{pelanggan}}. Approved by: {{approved_by}}.',
--     1
-- );

-- 6.3 SPK PDI Completed
-- INSERT INTO notification_rules (
--     name, 
--     trigger_event, 
--     target_divisions, 
--     target_roles,
--     title_template, 
--     message_template, 
--     is_active
-- ) VALUES (
--     'SPK PDI Completed - Ready for Delivery',
--     'spk_pdi_completed',
--     'marketing,operational',
--     'manager,supervisor,staff',
--     'PDI Completed - SPK Ready - {{spk_number}}',
--     'PDI has been completed for SPK {{spk_number}}. Unit is now READY for delivery. Customer: {{pelanggan}}. Approved by: {{approved_by}}.',
--     1
-- );

-- 6.4 Sparepart Returned
-- INSERT INTO notification_rules (
--     name, 
--     trigger_event, 
--     target_divisions, 
--     target_roles,
--     title_template, 
--     message_template, 
--     is_active
-- ) VALUES (
--     'Sparepart Return Confirmed',
--     'sparepart_returned',
--     'service',
--     'manager,supervisor',
--     'Sparepart Return Confirmed - {{sparepart_name}}',
--     'Sparepart return has been confirmed. Item: {{sparepart_name}}, Quantity: {{quantity}}. Returned by: {{returned_by}}, Confirmed by: {{confirmed_by}}.',
--     1
-- );

-- ============================================================================
-- SECTION 7: VALIDATION QUERIES
-- ============================================================================

-- 7.1 Count total active rules
SELECT 
    COUNT(*) as total_active_rules
FROM notification_rules 
WHERE is_active = 1;

-- 7.2 Count rules with cross-division targets
SELECT 
    COUNT(*) as rules_with_divisions
FROM notification_rules 
WHERE is_active = 1 
AND target_divisions IS NOT NULL 
AND target_divisions != '';

-- 7.3 Count rules targeting multiple divisions
SELECT 
    COUNT(*) as multi_division_rules
FROM notification_rules 
WHERE is_active = 1 
AND target_divisions LIKE '%,%';

-- 7.4 Division usage statistics
SELECT 
    'marketing' as division,
    COUNT(*) as rule_count
FROM notification_rules
WHERE is_active = 1 
AND target_divisions LIKE '%marketing%'
UNION ALL
SELECT 
    'service' as division,
    COUNT(*) as rule_count
FROM notification_rules
WHERE is_active = 1 
AND target_divisions LIKE '%service%'
UNION ALL
SELECT 
    'warehouse' as division,
    COUNT(*) as rule_count
FROM notification_rules
WHERE is_active = 1 
AND target_divisions LIKE '%warehouse%'
UNION ALL
SELECT 
    'operational' as division,
    COUNT(*) as rule_count
FROM notification_rules
WHERE is_active = 1 
AND target_divisions LIKE '%operational%'
UNION ALL
SELECT 
    'purchasing' as division,
    COUNT(*) as rule_count
FROM notification_rules
WHERE is_active = 1 
AND target_divisions LIKE '%purchasing%';

-- ============================================================================
-- END OF VERIFICATION SCRIPT
-- ============================================================================

-- USAGE INSTRUCTIONS:
-- 1. Run SECTION 1 queries individually to check each rule
-- 2. Run SECTION 2 to identify missing rules
-- 3. Run SECTION 3 for comprehensive audit
-- 4. Review SECTION 4 recommendations
-- 5. Uncomment and run SECTION 5 updates if needed
-- 6. Uncomment and run SECTION 6 inserts if needed
-- 7. Run SECTION 7 for final validation

-- IMPORTANT: 
-- - Review all results before making any updates
-- - Test in development environment first
-- - Backup database before running UPDATE/INSERT statements
-- - Verify application behavior after changes
