-- ============================================================================
-- CRITICAL PRIORITY NOTIFICATION RULES - Phase 1 Implementation
-- ============================================================================
-- Created: 19 December 2024
-- Purpose: Add notification rules for CRITICAL priority functions:
--          - Finance (invoice_created, payment_status_updated)
--          - Purchasing (po_created, delivery_created, delivery_status_changed)
--          - WorkOrder (workorder_created, workorder_status_changed)
--          - WarehousePO (po_verification_updated)
-- ============================================================================

-- Check current notification rules
SELECT 'Current notification rules count:' as info, COUNT(*) as count FROM notification_rules;
SELECT '================================================================================';

-- ============================================================================
-- FINANCE MODULE NOTIFICATIONS
-- ============================================================================

-- 1. Invoice Created
INSERT INTO notification_rules (
    trigger_event,
    target_divisions,
    target_roles,
    type,
    title_template,
    message_template,
    is_active,
    created_at
) VALUES (
    'invoice_created',
    'Finance,Accounting,Marketing',
    'Director,Manager,Supervisor,Staff',
    'info',
    'Invoice Baru Dibuat',
    'Invoice {{invoice_number}} telah dibuat untuk {{customer_name}} dengan nilai {{amount}}. Jatuh tempo: {{due_date}}. Dibuat oleh: {{created_by}}',
    1,
    NOW()
);

-- 2. Payment Status Updated
INSERT INTO notification_rules (
    trigger_event,
    target_divisions,
    target_roles,
    type,
    title_template,
    message_template,
    is_active,
    created_at
) VALUES (
    'payment_status_updated',
    'Finance,Accounting,Marketing',
    'Director,Manager,Supervisor',
    'success',
    'Status Pembayaran Diperbarui',
    'Status pembayaran invoice {{invoice_number}} ({{customer_name}}) telah diubah dari {{old_status}} menjadi {{new_status}}. Nilai: {{amount}}. Tanggal: {{payment_date}}. Diperbarui oleh: {{updated_by}}',
    1,
    NOW()
);

-- ============================================================================
-- PURCHASING MODULE NOTIFICATIONS
-- ============================================================================

-- 3. Purchase Order Created
INSERT INTO notification_rules (
    trigger_event,
    target_divisions,
    target_roles,
    type,
    title_template,
    message_template,
    is_active,
    created_at
) VALUES (
    'po_created',
    'Purchasing,Finance,Accounting',
    'Manager,Supervisor,Staff',
    'info',
    'Purchase Order Baru Dibuat',
    'PO {{po_number}} telah dibuat untuk supplier {{supplier_name}}. Tipe: {{po_type}}. Nilai Total: {{total_amount}}. Tanggal Kirim: {{delivery_date}}. Dibuat oleh: {{created_by}}',
    1,
    NOW()
);

-- 4. Delivery Created
INSERT INTO notification_rules (
    trigger_event,
    target_divisions,
    target_roles,
    type,
    title_template,
    message_template,
    is_active,
    created_at
) VALUES (
    'delivery_created',
    'Warehouse,Purchasing,QualityControl',
    'Manager,Supervisor,Staff',
    'info',
    'Pengiriman Baru Dijadwalkan',
    'Pengiriman {{delivery_number}} telah dibuat untuk PO {{po_number}} dari {{supplier_name}}. Tanggal kirim: {{delivery_date}}. Jumlah item: {{items_count}}. Dibuat oleh: {{created_by}}',
    1,
    NOW()
);

-- 5. Delivery Status Changed
INSERT INTO notification_rules (
    trigger_event,
    target_divisions,
    target_roles,
    type,
    title_template,
    message_template,
    is_active,
    created_at
) VALUES (
    'delivery_status_changed',
    'Warehouse,Purchasing',
    'Manager,Supervisor,Staff',
    'warning',
    'Status Pengiriman Berubah',
    'Status pengiriman {{delivery_number}} (PO {{po_number}}) dari {{supplier_name}} telah berubah dari {{old_status}} menjadi {{new_status}}. Diperbarui oleh: {{updated_by}}',
    1,
    NOW()
);

-- ============================================================================
-- WORK ORDER MODULE NOTIFICATIONS
-- ============================================================================

-- 6. Work Order Created
INSERT INTO notification_rules (
    trigger_event,
    target_divisions,
    target_roles,
    type,
    title_template,
    message_template,
    is_active,
    created_at
) VALUES (
    'workorder_created',
    'Service,Workshop',
    'Manager,Supervisor,Foreman,Staff',
    'info',
    'Work Order Baru Dibuat',
    'Work Order {{wo_number}} telah dibuat untuk unit {{unit_code}}. Tipe: {{order_type}}. Prioritas: {{priority}}. Kategori: {{category}}. Keluhan: {{complaint}}. Dibuat oleh: {{created_by}}',
    1,
    NOW()
);

-- 7. Work Order Status Changed
INSERT INTO notification_rules (
    trigger_event,
    target_divisions,
    target_roles,
    type,
    title_template,
    message_template,
    is_active,
    created_at
) VALUES (
    'workorder_status_changed',
    'Service,Workshop',
    'Manager,Supervisor,Foreman',
    'warning',
    'Status Work Order Berubah',
    'Status Work Order {{wo_number}} (Unit: {{unit_code}}) telah berubah dari {{old_status}} menjadi {{new_status}}. Diperbarui oleh: {{updated_by}}',
    1,
    NOW()
);

-- ============================================================================
-- WAREHOUSE PO VERIFICATION NOTIFICATION
-- ============================================================================

-- 8. PO Verification Updated
INSERT INTO notification_rules (
    trigger_event,
    target_divisions,
    target_roles,
    type,
    title_template,
    message_template,
    is_active,
    created_at
) VALUES (
    'po_verification_updated',
    'Purchasing,Finance,QualityControl',
    'Manager,Supervisor',
    'success',
    'Verifikasi PO Diperbarui',
    'Verifikasi untuk PO {{po_number}} telah diperbarui dengan status: {{verification_status}}. Catatan: {{notes}}. Diverifikasi oleh: {{verified_by}} pada {{verification_date}}',
    1,
    NOW()
);

-- ============================================================================
-- VERIFICATION QUERIES
-- ============================================================================

SELECT '================================================================================';
SELECT 'Inserted notification rules:' as info;
SELECT '================================================================================';

SELECT 
    id,
    trigger_event,
    target_divisions,
    target_roles,
    type,
    title_template,
    is_active
FROM notification_rules
WHERE trigger_event IN (
    'invoice_created',
    'payment_status_updated',
    'po_created',
    'delivery_created',
    'delivery_status_changed',
    'workorder_created',
    'workorder_status_changed',
    'po_verification_updated'
)
ORDER BY id DESC;

SELECT '================================================================================';
SELECT 'Total notification rules after migration:' as info, COUNT(*) as count FROM notification_rules;
SELECT '================================================================================';

-- ============================================================================
-- SUMMARY OF ADDED EVENTS
-- ============================================================================

SELECT '================================================================================';
SELECT 'CRITICAL PRIORITY EVENTS ADDED (9 events):' as summary;
SELECT '================================================================================';
SELECT '1. invoice_created - Finance team gets notified when new invoice is created';
SELECT '2. payment_status_updated - Finance & Accounting get notified when payment status changes';
SELECT '3. po_created - Purchasing, Finance & Accounting get notified on new PO';
SELECT '4. delivery_created - Warehouse team gets notified when delivery is scheduled';
SELECT '5. delivery_status_changed - Warehouse & Purchasing track delivery progress';
SELECT '6. workorder_created - Service team gets notified on new work orders';
SELECT '7. workorder_status_changed - Service team tracks work order progress';
SELECT '8. po_verification_updated - Finance & QC get notified on PO verification';
SELECT '================================================================================';

-- ============================================================================
-- TESTING RECOMMENDATIONS
-- ============================================================================

SELECT '================================================================================';
SELECT 'TESTING STEPS:' as testing;
SELECT '================================================================================';
SELECT '1. Create a new invoice → Check Finance team receives notification';
SELECT '2. Update payment status → Check notification for status change';
SELECT '3. Create a new PO → Check Purchasing & Finance get notified';
SELECT '4. Create a delivery → Check Warehouse gets notified';
SELECT '5. Update delivery status → Check status change notification';
SELECT '6. Create a work order → Check Service team gets notified';
SELECT '7. Update WO status → Check Service managers get notified';
SELECT '8. Verify PO items → Check QC & Finance get verification notification';
SELECT '================================================================================';

-- ============================================================================
-- ROLLBACK SCRIPT (if needed)
-- ============================================================================

-- To rollback this migration, run:
-- DELETE FROM notification_rules 
-- WHERE trigger_event IN (
--     'invoice_created', 'payment_status_updated', 
--     'po_created', 'delivery_created', 'delivery_status_changed',
--     'workorder_created', 'workorder_status_changed', 'po_verification_updated'
-- );

-- ============================================================================
-- END OF MIGRATION
-- ============================================================================
