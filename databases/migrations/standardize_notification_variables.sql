-- ============================================================================
-- NOTIFICATION VARIABLES STANDARDIZATION - DATABASE UPDATE
-- ============================================================================
-- Date: 2025-12-22
-- Purpose: Update notification_rules templates to use standardized variable names
-- Impact: 50+ notifications will use consistent variable naming
-- ============================================================================

-- BACKUP FIRST!
-- CREATE TABLE notification_rules_backup_20251222 AS SELECT * FROM notification_rules;

-- ============================================================================
-- CATEGORY 1: ATTACHMENT NOTIFICATIONS
-- Fix: attachment_info already added to helper, templates already correct
-- ============================================================================

-- attachment_attached, attachment_detached, attachment_swapped already have correct templates
-- No changes needed - they use {{attachment_info}} which we now provide

-- ============================================================================
-- CATEGORY 2: DELIVERY NOTIFICATIONS 
-- Fix: Add 'customer' as alias (already handled in helper via dual variables)
-- Templates can use either {{customer}} or {{customer_name}}
-- ============================================================================

-- delivery_created - add customer to message
UPDATE notification_rules 
SET message_template = 'Delivery {{nomor_delivery}} telah dibuat untuk {{customer}}'
WHERE trigger_event = 'delivery_created' AND message_template LIKE '%{{customer}}%' = FALSE;

-- delivery_arrived, delivery_completed, delivery_in_transit already correct or use customer_name

-- ============================================================================
-- CATEGORY 3: SPK/WO/PMPS NOTIFICATIONS
-- Fix: Ensure all use {{departemen}} and standardized unit names
-- ============================================================================

-- PMPS notifications - already have {{departemen}} in templates ✓
-- pmps_due_soon, pmps_overdue, pmps_completed all correct

-- SPK notifications - already have {{departemen}} in templates ✓
-- spk_assigned, spk_cancelled, spk_completed all correct

-- Work Order notifications - already have {{departemen}} in templates ✓
-- work_order_assigned, work_order_cancelled, work_order_completed, work_order_in_progress all correct

-- spk_created - ensure has {{departemen}} and {{unit_no}}
-- Already correct in database ✓

-- work_order_created - needs {{departemen}} added if missing
UPDATE notification_rules 
SET title_template = 'WO Baru [{{departemen}}]: {{nomor_wo}} - {{unit_no}}'
WHERE trigger_event = 'work_order_created' 
AND title_template NOT LIKE '%{{departemen}}%';

UPDATE notification_rules 
SET message_template = 'Work Order {{nomor_wo}} telah dibuat untuk unit {{no_unit}}'
WHERE trigger_event = 'work_order_created' 
AND message_template LIKE '%{{unit_code}}%';

-- ============================================================================
-- CATEGORY 4: INVOICE & PAYMENT NOTIFICATIONS
-- Fix: Ensure use {{customer}} or {{customer_name}} consistently
-- ============================================================================

-- All invoice notifications already use {{customer}} or {{customer_name}} ✓
-- invoice_created, invoice_sent, payment_received all correct

-- ============================================================================
-- CATEGORY 5: UNIT VARIABLE STANDARDIZATION
-- Fix: Change {{unit_code}} to {{no_unit}} where applicable
-- Note: Helper functions now provide BOTH, so templates can use either
-- ============================================================================

-- These templates use {{unit_code}} but should standardize to {{no_unit}}
-- However, since helper provides both, we can keep templates as-is for backward compatibility
-- OR update them to use standard {{no_unit}}

-- Option A: Keep backward compatibility (do nothing)
-- Option B: Standardize templates (update below)

-- Standardize unit references to {{no_unit}} (OPTIONAL - uncomment if want full standardization)
-- UPDATE notification_rules SET title_template = REPLACE(title_template, '{{unit_code}}', '{{no_unit}}');
-- UPDATE notification_rules SET message_template = REPLACE(message_template, '{{unit_code}}', '{{no_unit}}');

-- Standardize unit_no to no_unit (OPTIONAL)
-- UPDATE notification_rules SET title_template = REPLACE(title_template, '{{unit_no}}', '{{no_unit}}');
-- UPDATE notification_rules SET message_template = REPLACE(message_template, '{{unit_no}}', '{{no_unit}}');

-- ============================================================================
-- CATEGORY 6: QUANTITY STANDARDIZATION
-- Fix: Change {{qty}} to {{quantity}}
-- ============================================================================

-- sparepart_added, sparepart_low_stock, sparepart_used use {{qty}}
-- Helper now provides 'quantity' as standard, 'qty' as alias

-- Standardize to {{quantity}} (OPTIONAL)
-- UPDATE notification_rules 
-- SET message_template = REPLACE(message_template, '{{qty}}', '{{quantity}}')
-- WHERE trigger_event IN ('sparepart_added', 'sparepart_low_stock', 'sparepart_used');

-- ============================================================================
-- CATEGORY 7: DELIVERY NUMBER STANDARDIZATION  
-- Fix: Ensure {{nomor_delivery}} and {{delivery_number}} both work
-- ============================================================================

-- Helper provides both nomor_delivery and delivery_number
-- Templates can use either - no changes needed ✓

-- ============================================================================
-- CATEGORY 8: FIX SPECIFIC TEMPLATE ISSUES
-- ============================================================================

-- delivery_status_changed - add {{updated_at}} if missing
UPDATE notification_rules 
SET message_template = CONCAT(message_template, ' pada {{updated_at}}')
WHERE trigger_event = 'delivery_status_changed' 
AND message_template NOT LIKE '%{{updated_at}}%'
AND message_template LIKE '%{{updated_by}}%';

-- di_created - templates use {{creator_name}} and {{customer_name}}
-- Helper needs to be updated to provide these (already done in fixes)

-- po_verification_updated - add missing variables to template if needed
-- Helper provides: po_number, delivery_number, verification_status, verified_items, total_items, notes, verified_by

-- sparepart_used - ensure uses {{quantity}} and {{sparepart_name}}
UPDATE notification_rules 
SET message_template = 'Sparepart {{sparepart_name}} digunakan (Qty: {{quantity}})'
WHERE trigger_event = 'sparepart_used' 
AND message_template LIKE '%{{nama_sparepart}}%{{qty}}%';

-- ============================================================================
-- VERIFICATION QUERIES
-- ============================================================================

-- Check which templates still use non-standard variable names
-- SELECT trigger_event, title_template, message_template 
-- FROM notification_rules 
-- WHERE title_template LIKE '%{{unit_code}}%' 
--    OR message_template LIKE '%{{unit_code}}%'
--    OR title_template LIKE '%{{qty}}%'
--    OR message_template LIKE '%{{qty}}%';

-- Check departemen usage
-- SELECT trigger_event, title_template 
-- FROM notification_rules 
-- WHERE title_template LIKE '%[{{departemen}}]%';

-- ============================================================================
-- ROLLBACK (if needed)
-- ============================================================================

-- To rollback: 
-- DROP TABLE notification_rules;
-- RENAME TABLE notification_rules_backup_20251222 TO notification_rules;

-- ============================================================================
-- SUMMARY
-- ============================================================================
-- Changes made:
-- 1. ✅ Ensured delivery_created uses {{customer}}
-- 2. ✅ Added {{departemen}} to work_order_created if missing
-- 3. ✅ Fixed work_order_created to use {{no_unit}} instead of {{unit_code}}
-- 4. ✅ Added {{updated_at}} to delivery_status_changed
-- 5. ✅ Fixed sparepart_used to use {{quantity}} and {{sparepart_name}}
-- 6. ⚠️  Optional: Uncomment global REPLACE queries for full standardization
--
-- Note: Most templates are already correct because helper functions provide
--       BOTH old and new variable names for backward compatibility
-- ============================================================================
