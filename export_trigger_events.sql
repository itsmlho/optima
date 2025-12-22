-- Export all trigger events for variable extraction
-- Run this and save output as notification_templates_db.txt

SELECT 
    id,
    trigger_event,
    event_category,
    is_active
FROM notification_rules
WHERE is_active = 1
ORDER BY event_category, trigger_event;
