-- ============================================================================
-- Standardize Notification Variables - Database Template Migration
-- ============================================================================
-- File: standardize_notification_variables.sql
-- Purpose: Update notification_rules templates to use standardized variables
-- Date: 2025-12-22
-- 
-- IMPORTANT: Backup database before running!
--   mysqldump --no-defaults -u root optima_ci notification_rules > backup.sql
-- ============================================================================

-- Disable safe updates for this session
SET SQL_SAFE_UPDATES = 0;

-- ============================================================================
-- SECTION 1: CONSERVATIVE UPDATES (Recommended)
-- Updates only critical templates with missing or incorrect variables
-- ============================================================================

-- 1. Add {{customer}} to delivery_created templates
UPDATE notification_rules
SET message_template = CASE
    WHEN message_template NOT LIKE '%customer%' THEN
        CONCAT(message_template, ' untuk customer {{customer}}')
    ELSE
        message_template
END
WHERE trigger_event = 'delivery_created'
  AND message_template NOT LIKE '%{{customer}}%'
  AND is_active = 1;

SELECT '✓ Updated delivery_created templates with customer variable' as status;

-- 2. Add {{departemen}} and fix unit references in work_order_created
UPDATE notification_rules
SET 
    title_template = REPLACE(title_template, '{{unit_code}}', '{{no_unit}}'),
    message_template = CASE
        WHEN message_template NOT LIKE '%departemen%' THEN
            CONCAT('Departemen {{departemen}}: ', REPLACE(message_template, '{{unit_code}}', '{{no_unit}}'))
        ELSE
            REPLACE(message_template, '{{unit_code}}', '{{no_unit}}')
    END
WHERE trigger_event = 'work_order_created'
  AND is_active = 1;

SELECT '✓ Updated work_order_created templates with departemen and no_unit' as status;

-- 3. Add {{updated_at}} to delivery_status_changed
UPDATE notification_rules
SET message_template = CASE
    WHEN message_template NOT LIKE '%updated_at%' THEN
        CONCAT(message_template, ' pada {{updated_at}}')
    ELSE
        message_template
END
WHERE trigger_event = 'delivery_status_changed'
  AND message_template NOT LIKE '%{{updated_at}}%'
  AND is_active = 1;

SELECT '✓ Updated delivery_status_changed templates with updated_at' as status;

-- 4. Standardize sparepart_used variables
UPDATE notification_rules
SET 
    title_template = REPLACE(REPLACE(title_template, '{{qty}}', '{{quantity}}'), '{{nama_sparepart}}', '{{sparepart_name}}'),
    message_template = REPLACE(REPLACE(message_template, '{{qty}}', '{{quantity}}'), '{{nama_sparepart}}', '{{sparepart_name}}')
WHERE trigger_event = 'sparepart_used'
  AND (title_template LIKE '%{{qty}}%' OR title_template LIKE '%{{nama_sparepart}}%'
       OR message_template LIKE '%{{qty}}%' OR message_template LIKE '%{{nama_sparepart}}%')
  AND is_active = 1;

SELECT '✓ Updated sparepart_used templates with quantity and sparepart_name' as status;

-- ============================================================================
-- SECTION 2: OPTIONAL AGGRESSIVE STANDARDIZATION
-- Uncomment to replace ALL old variable names with standardized ones
-- ============================================================================

-- 5. OPTIONAL: Global unit field standardization (unit_code → no_unit)
-- UNCOMMENT BELOW TO EXECUTE:
/*
UPDATE notification_rules
SET 
    title_template = REPLACE(REPLACE(title_template, '{{unit_code}}', '{{no_unit}}'), '{{unit_no}}', '{{no_unit}}'),
    message_template = REPLACE(REPLACE(message_template, '{{unit_code}}', '{{no_unit}}'), '{{unit_no}}', '{{no_unit}}')
WHERE (title_template LIKE '%{{unit_code}}%' OR title_template LIKE '%{{unit_no}}%'
       OR message_template LIKE '%{{unit_code}}%' OR message_template LIKE '%{{unit_no}}%')
  AND is_active = 1;

SELECT '✓ Globally standardized unit references to no_unit' as status;
*/

-- 6. OPTIONAL: Global customer field standardization (customer_name → customer)
-- UNCOMMENT BELOW TO EXECUTE:
/*
UPDATE notification_rules
SET 
    title_template = REPLACE(title_template, '{{customer_name}}', '{{customer}}'),
    message_template = REPLACE(message_template, '{{customer_name}}', '{{customer}}')
WHERE (title_template LIKE '%{{customer_name}}%' OR message_template LIKE '%{{customer_name}}%')
  AND is_active = 1;

SELECT '✓ Globally standardized customer references' as status;
*/

-- 7. OPTIONAL: Global quantity field standardization (qty → quantity)
-- UNCOMMENT BELOW TO EXECUTE:
/*
UPDATE notification_rules
SET 
    title_template = REPLACE(title_template, '{{qty}}', '{{quantity}}'),
    message_template = REPLACE(message_template, '{{qty}}', '{{quantity}}')
WHERE (title_template LIKE '%{{qty}}%' OR message_template LIKE '%{{qty}}%')
  AND is_active = 1;

SELECT '✓ Globally standardized quantity references' as status;
*/

-- 8. OPTIONAL: Global delivery number standardization
-- UNCOMMENT BELOW TO EXECUTE:
/*
UPDATE notification_rules
SET 
    title_template = REPLACE(title_template, '{{delivery_number}}', '{{nomor_delivery}}'),
    message_template = REPLACE(message_template, '{{delivery_number}}', '{{nomor_delivery}}')
WHERE (title_template LIKE '%{{delivery_number}}%' OR message_template LIKE '%{{delivery_number}}%')
  AND is_active = 1;

SELECT '✓ Globally standardized delivery number references' as status;
*/

-- ============================================================================
-- SECTION 3: VERIFICATION QUERIES
-- Run these to verify updates were applied correctly
-- ============================================================================

-- Show updated delivery_created templates
SELECT 
    id,
    trigger_event,
    LEFT(title_template, 50) as title_sample,
    LEFT(message_template, 80) as message_sample,
    CASE 
        WHEN message_template LIKE '%{{customer}}%' THEN '✓ Has customer'
        ELSE '✗ Missing customer'
    END as check_customer
FROM notification_rules
WHERE trigger_event = 'delivery_created'
  AND is_active = 1;

-- Show updated work_order_created templates
SELECT 
    id,
    trigger_event,
    LEFT(title_template, 50) as title_sample,
    LEFT(message_template, 80) as message_sample,
    CASE 
        WHEN message_template LIKE '%{{departemen}}%' THEN '✓ Has departemen'
        ELSE '✗ Missing departemen'
    END as check_departemen,
    CASE 
        WHEN message_template LIKE '%{{no_unit}}%' THEN '✓ Uses no_unit'
        WHEN message_template LIKE '%{{unit_code}}%' THEN '⚠ Uses unit_code'
        ELSE '✗ No unit reference'
    END as check_unit
FROM notification_rules
WHERE trigger_event = 'work_order_created'
  AND is_active = 1;

-- Show updated sparepart_used templates
SELECT 
    id,
    trigger_event,
    LEFT(message_template, 80) as message_sample,
    CASE 
        WHEN message_template LIKE '%{{quantity}}%' THEN '✓ Uses quantity'
        WHEN message_template LIKE '%{{qty}}%' THEN '⚠ Uses qty'
        ELSE '✗ No quantity'
    END as check_quantity,
    CASE 
        WHEN message_template LIKE '%{{sparepart_name}}%' THEN '✓ Uses sparepart_name'
        WHEN message_template LIKE '%{{nama_sparepart}}%' THEN '⚠ Uses nama_sparepart'
        ELSE '✗ No sparepart'
    END as check_sparepart
FROM notification_rules
WHERE trigger_event = 'sparepart_used'
  AND is_active = 1;

-- Count standardized vs non-standardized templates
SELECT 
    'Standardized Templates' as category,
    COUNT(*) as count
FROM notification_rules
WHERE is_active = 1
  AND (message_template LIKE '%{{no_unit}}%' 
       OR message_template LIKE '%{{customer}}%'
       OR message_template LIKE '%{{quantity}}%'
       OR message_template LIKE '%{{sparepart_name}}%'
       OR message_template LIKE '%{{nomor_delivery}}%')

UNION ALL

SELECT 
    'Templates with Old Names' as category,
    COUNT(*) as count
FROM notification_rules
WHERE is_active = 1
  AND (message_template LIKE '%{{unit_code}}%'
       OR message_template LIKE '%{{customer_name}}%'
       OR message_template LIKE '%{{qty}}%'
       OR message_template LIKE '%{{nama_sparepart}}%'
       OR message_template LIKE '%{{delivery_number}}%');

-- Re-enable safe updates
SET SQL_SAFE_UPDATES = 1;

SELECT '✅ Migration completed! Check verification results above.' as final_status;

-- ============================================================================
-- ROLLBACK INSTRUCTIONS (if needed)
-- ============================================================================
-- To rollback, restore from backup:
--   mysql --no-defaults -u root optima_ci < notification_rules_backup_working.sql
-- ============================================================================
