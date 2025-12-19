-- ============================================================================
-- ADD MISSING NOTIFICATION EVENTS FOR AUDIT IMPROVEMENTS
-- Created: 2025-12-18
-- Purpose: Add notification rules for attachment_uploaded event
-- ============================================================================

-- Check if attachment_uploaded event exists
SELECT COUNT(*) as count FROM notification_rules WHERE trigger_event = 'attachment_uploaded';

-- Add notification rule for attachment uploads on SPK stages
INSERT INTO `notification_rules` 
(`name`, `description`, `trigger_event`, `is_active`, `conditions`, `target_roles`, `target_divisions`, `target_departments`, `target_users`, `exclude_creator`, `title_template`, `message_template`, `category`, `type`, `priority`, `url_template`, `delay_minutes`, `expire_days`, `created_by`, `created_at`, `updated_at`, `auto_include_superadmin`, `target_mixed`, `rule_description`)
VALUES
(
    'Attachment Upload - Service Team',
    'Notifikasi ketika attachment diupload pada stage SPK',
    'attachment_uploaded',
    1,
    NULL,
    'supervisor,manager',
    'service',
    NULL,
    NULL,
    0,
    'Attachment Diupload: {{stage_name}}',
    'Attachment baru telah diupload untuk SPK {{spk_number}} pada stage {{stage_name}} oleh {{uploaded_by}}',
    'spk',
    'info',
    2,
    '/service/spk_service',
    0,
    30,
    NULL,
    NOW(),
    NOW(),
    1,
    NULL,
    'Notifikasi attachment upload pada stages SPK'
)
ON DUPLICATE KEY UPDATE
    `description` = VALUES(`description`),
    `is_active` = VALUES(`is_active`),
    `updated_at` = NOW();

-- ============================================================================
-- VERIFICATION QUERIES
-- ============================================================================

-- Show all active notification rules
SELECT 
    id,
    name,
    trigger_event,
    target_divisions,
    target_roles,
    is_active
FROM notification_rules
WHERE trigger_event IN ('customer_created', 'customer_updated', 'customer_deleted', 
                        'customer_location_added', 'customer_contract_created',
                        'di_created', 'attachment_uploaded')
ORDER BY trigger_event;

-- ============================================================================
-- SUMMARY OF NOTIFICATION EVENTS NOW COVERED
-- ============================================================================

/*
✅ customer_created          - Customer dibuat (Marketing)
✅ customer_updated          - Customer diupdate (Marketing)
✅ customer_deleted          - Customer dihapus (Marketing)
✅ customer_location_added   - Lokasi customer ditambah (Marketing)
✅ customer_contract_created - Kontrak dibuat (Marketing)
✅ di_created                - DI dibuat dari SPK (Marketing → Operational)
✅ spk_created               - SPK dibuat (Marketing → Service) [ALREADY EXISTS]
✅ attachment_uploaded       - Attachment diupload pada SPK stages (Service)

IMPLEMENTATION LOCATIONS:
- CustomerManagementController.php
  * storeCustomer() - notify_customer_created + notify_customer_location_added
  * updateCustomer() - notify_customer_updated
  * deleteCustomer() - notify_customer_deleted
  * storeCustomerLocation() - notify_customer_location_added
  * updateCustomerLocation() - notify_customer_location_added

- Marketing.php
  * createContract() - notify_customer_contract_created
  * createCustomer() - notify_customer_created (2 locations)
  * createCustomerFromDeal() - notify_customer_created
  * diCreate() - notify_di_created

- Service.php
  * saveStageApproval() - notify_attachment_uploaded
*/
