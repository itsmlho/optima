-- ============================================================================
-- NOTIFICATION SYSTEM - QUICK TEST VERIFICATION
-- Run this to verify all rules are deployed and ready for testing
-- ============================================================================

USE optima_ci;

-- Test 1: Check notification rules exist and are active
SELECT '========================================' as separator;
SELECT 'TEST 1: Notification Rules Verification' as test_name;
SELECT '========================================' as separator;

SELECT 
    trigger_event,
    name,
    CASE 
        WHEN priority = 1 THEN 'CRITICAL'
        WHEN priority = 2 THEN 'HIGH'
        WHEN priority = 3 THEN 'MEDIUM'
        ELSE 'LOW'
    END as priority_label,
    CASE WHEN is_active = 1 THEN '✅ Active' ELSE '❌ Inactive' END as status
FROM notification_rules
WHERE trigger_event IN (
    'customer_created', 
    'customer_updated', 
    'customer_status_changed',
    'warehouse_stock_alert',
    'quotation_sent_to_customer'
)
ORDER BY priority, trigger_event;

-- Test 2: Count total rules by phase
SELECT '========================================' as separator;
SELECT 'TEST 2: Rules Count by Phase' as test_name;
SELECT '========================================' as separator;

SELECT 
    CASE 
        WHEN priority = 1 THEN 'Phase 1 (CRITICAL)'
        WHEN priority = 2 THEN 'Phase 2 (HIGH)'
        WHEN priority = 3 THEN 'Phase 3 (MEDIUM)'
        ELSE 'Other'
    END as phase,
    COUNT(*) as rule_count
FROM notification_rules
WHERE trigger_event IN (
    'invoice_created', 'payment_status_updated', 'po_created', 'delivery_created', 
    'delivery_status_changed', 'workorder_created', 'workorder_status_changed', 
    'po_verification_updated', 'quotation_created', 'quotation_updated', 
    'quotation_approved', 'quotation_rejected', 'workorder_assigned', 
    'workorder_completed', 'workorder_delayed', 'workorder_sparepart_added',
    'service_assignment_created', 'service_assignment_updated', 
    'service_assignment_completed', 'unit_location_updated', 
    'warehouse_unit_updated', 'contract_created', 'customer_created', 
    'customer_updated', 'customer_status_changed', 'warehouse_stock_alert',
    'warehouse_transfer_completed', 'warehouse_stocktake_completed',
    'inspection_scheduled', 'inspection_completed', 'maintenance_scheduled',
    'maintenance_completed', 'payment_received', 'payment_overdue',
    'budget_threshold_exceeded', 'spk_created', 'spk_completed',
    'quotation_sent_to_customer', 'quotation_follow_up_required'
)
GROUP BY phase
ORDER BY priority;

-- Test 3: Check for today's notifications
SELECT '========================================' as separator;
SELECT 'TEST 3: Today''s Notifications' as test_name;
SELECT '========================================' as separator;

SELECT 
    notification_type,
    COUNT(*) as count,
    MAX(created_at) as last_notification
FROM notifications
WHERE DATE(created_at) = CURDATE()
GROUP BY notification_type
ORDER BY count DESC
LIMIT 10;

-- Test 4: Check notification system readiness
SELECT '========================================' as separator;
SELECT 'TEST 4: System Readiness Summary' as test_name;
SELECT '========================================' as separator;

SELECT 
    'Total Notification Rules' as metric,
    COUNT(*) as value
FROM notification_rules
UNION ALL
SELECT 
    'Active Rules' as metric,
    COUNT(*) as value
FROM notification_rules
WHERE is_active = 1
UNION ALL
SELECT 
    'Phase 1-3 Rules' as metric,
    COUNT(*) as value
FROM notification_rules
WHERE trigger_event IN (
    'invoice_created', 'payment_status_updated', 'po_created', 'delivery_created', 
    'delivery_status_changed', 'workorder_created', 'workorder_status_changed', 
    'po_verification_updated', 'quotation_created', 'quotation_updated', 
    'quotation_approved', 'quotation_rejected', 'workorder_assigned', 
    'workorder_completed', 'workorder_delayed', 'workorder_sparepart_added',
    'service_assignment_created', 'service_assignment_updated', 
    'service_assignment_completed', 'unit_location_updated', 
    'warehouse_unit_updated', 'contract_created', 'customer_created', 
    'customer_updated', 'customer_status_changed', 'warehouse_stock_alert',
    'warehouse_transfer_completed', 'warehouse_stocktake_completed',
    'inspection_scheduled', 'inspection_completed', 'maintenance_scheduled',
    'maintenance_completed', 'payment_received', 'payment_overdue',
    'budget_threshold_exceeded', 'spk_created', 'spk_completed',
    'quotation_sent_to_customer', 'quotation_follow_up_required'
)
UNION ALL
SELECT 
    'Today''s Notifications' as metric,
    COUNT(*) as value
FROM notifications
WHERE DATE(created_at) = CURDATE();

-- Test 5: List testable notification rules
SELECT '========================================' as separator;
SELECT 'TEST 5: Ready-to-Test Notifications' as test_name;
SELECT '========================================' as separator;

SELECT 
    CONCAT('Test ', 
        CASE trigger_event
            WHEN 'customer_created' THEN '1'
            WHEN 'customer_updated' THEN '2'
            WHEN 'customer_status_changed' THEN '3'
            WHEN 'warehouse_stock_alert' THEN '4'
            WHEN 'quotation_sent_to_customer' THEN '5'
        END
    ) as test_number,
    trigger_event,
    name,
    CASE WHEN is_active = 1 THEN '✅ Ready' ELSE '❌ Not Ready' END as test_status,
    target_divisions,
    target_roles
FROM notification_rules
WHERE trigger_event IN (
    'customer_created', 
    'customer_updated', 
    'customer_status_changed',
    'warehouse_stock_alert',
    'quotation_sent_to_customer'
)
ORDER BY trigger_event;

SELECT '========================================' as separator;
SELECT '✅ VERIFICATION COMPLETE!' as status;
SELECT 'System is ready for UI testing' as message;
SELECT '========================================' as separator;
