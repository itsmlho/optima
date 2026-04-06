-- ============================================================================
-- Migration: Fix Notification Template Variable Mismatches
-- Date: 2026
-- Description:
--   3 notification_rules templates used wrong variable names that never matched
--   data passed by PHP helper functions, resulting in blank/missing values:
--
--   1. quotation_sent_to_customer (id=32):
--      - Was: {{quotation_number}} + {{customer}} 
--      - Fix: {{quote_number}} + {{customer_name}} (matches notify_quotation_sent_to_customer())
--
--   2. service_assignment_created (id=105):
--      - Was: {{assignment_number}} (never passed — data has employee_name, area_name)
--      - Fix: {{employee_name}} + {{area_name}}
--
--   3. service_assignment_deleted (id=107):
--      - Was: {{assignment_number}} (same issue)
--      - Fix: {{employee_name}} + {{area_name}}
--
--   4. service_assignment_updated (id=106): fixed for consistency
-- ============================================================================

-- 1. quotation_sent_to_customer
UPDATE notification_rules
SET
    title_template   = 'Quotation Dikirim: {{quote_number}}',
    message_template = 'Quotation {{quote_number}} telah dikirim ke {{customer_name}}'
WHERE id = 32
  AND trigger_event = 'quotation_sent_to_customer';

-- 2. service_assignment_created
UPDATE notification_rules
SET
    title_template   = 'Penugasan Baru: {{employee_name}}',
    message_template = '{{employee_name}} ditugaskan ke area {{area_name}}'
WHERE id = 105
  AND trigger_event = 'service_assignment_created';

-- 3. service_assignment_deleted
UPDATE notification_rules
SET
    title_template   = 'Penugasan Dihapus: {{employee_name}}',
    message_template = 'Penugasan {{employee_name}} dari area {{area_name}} telah dihapus'
WHERE id = 107
  AND trigger_event = 'service_assignment_deleted';

-- 4. service_assignment_updated (consistency fix)
UPDATE notification_rules
SET
    title_template   = 'Penugasan Diupdate: {{employee_name}}',
    message_template = 'Penugasan {{employee_name}} di area {{area_name}} telah diupdate'
WHERE id = 106
  AND trigger_event = 'service_assignment_updated';
