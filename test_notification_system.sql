-- ============================================================================
-- Comprehensive Test: Notification System & Trigger Events
-- ============================================================================

SELECT '========================================' as '';
SELECT 'TRIGGER EVENTS SYSTEM VALIDATION' as 'TEST SUITE';
SELECT '========================================' as '';

-- Test 1: Check trigger_events table exists and populated
SELECT '1. Trigger Events Table Check' as 'TEST';
SELECT 
    COUNT(*) as total_events,
    COUNT(DISTINCT category) as categories,
    COUNT(DISTINCT module) as modules,
    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_events
FROM trigger_events;

-- Test 2: Check foreign key constraint exists
SELECT '2. Foreign Key Constraint Check' as 'TEST';
SELECT 
    CONSTRAINT_NAME,
    TABLE_NAME,
    REFERENCED_TABLE_NAME,
    UPDATE_RULE,
    DELETE_RULE
FROM information_schema.REFERENTIAL_CONSTRAINTS
WHERE CONSTRAINT_SCHEMA = 'optima_ci'
  AND CONSTRAINT_NAME = 'fk_notification_rules_trigger_event';

-- Test 3: Validate all notification rules have valid trigger events
SELECT '3. Notification Rules Validation' as 'TEST';
SELECT 
    COUNT(*) as total_rules,
    SUM(CASE WHEN nr.trigger_event IN (SELECT event_code FROM trigger_events) THEN 1 ELSE 0 END) as valid_events,
    SUM(CASE WHEN nr.trigger_event NOT IN (SELECT event_code FROM trigger_events) THEN 1 ELSE 0 END) as invalid_events
FROM notification_rules nr;

-- Test 4: Check work_order_unit_verified event and rules
SELECT '4. Work Order Unit Verified Event Check' as 'TEST';
SELECT 
    te.id,
    te.event_code,
    te.event_name,
    te.category,
    te.module,
    COUNT(nr.id) as rule_count
FROM trigger_events te
LEFT JOIN notification_rules nr ON te.event_code = nr.trigger_event
WHERE te.event_code = 'work_order_unit_verified'
GROUP BY te.id;

-- Test 5: List notification rules for work_order_unit_verified
SELECT '5. Work Order Unit Verified Notification Rules' as 'TEST';
SELECT 
    id,
    name,
    target_divisions,
    target_roles,
    type,
    is_active
FROM notification_rules
WHERE trigger_event = 'work_order_unit_verified';

-- Test 6: Category distribution
SELECT '6. Events by Category (Top 10)' as 'TEST';
SELECT 
    category,
    COUNT(*) as event_count
FROM trigger_events
GROUP BY category
ORDER BY event_count DESC
LIMIT 10;

-- Test 7: Module distribution
SELECT '7. Events by Module (Top 10)' as 'TEST';
SELECT 
    module,
    COUNT(*) as event_count
FROM trigger_events
GROUP BY module
ORDER BY event_count DESC
LIMIT 10;

-- Test 8: Most used events
SELECT '8. Most Used Events (Top 10)' as 'TEST';
SELECT 
    te.event_code,
    te.event_name,
    te.category,
    COUNT(nr.id) as rule_count
FROM trigger_events te
LEFT JOIN notification_rules nr ON te.event_code = nr.trigger_event
GROUP BY te.id
ORDER BY rule_count DESC
LIMIT 10;

-- Test 9: Unused events
SELECT '9. Unused Events (No Notification Rules)' as 'TEST';
SELECT 
    COUNT(*) as unused_events_count
FROM trigger_events te
LEFT JOIN notification_rules nr ON te.event_code = nr.trigger_event
WHERE nr.id IS NULL;

-- Test 10: Check helper function existence
SELECT '10. Helper Function Check (Manual Verification Required)' as 'TEST';
SELECT 'Please verify function exists: notify_work_order_unit_verified()' as note,
       'Location: app/Helpers/notification_helper.php' as location;

SELECT '========================================' as '';
SELECT 'ALL TESTS COMPLETED' as 'STATUS';
SELECT '========================================' as '';
