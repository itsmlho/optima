-- FIX ATTACHMENT_SWAPPED NOTIFICATION
-- Update notification_rules untuk attachment_swapped dengan variable names yang benar

UPDATE notification_rules 
SET 
    title_template = REPLACE(REPLACE(REPLACE(REPLACE(
        title_template,
        '{{old_unit}}', '{{from_unit_number}}'),
        '{{new_unit}}', '{{to_unit_number}}'),
        '{{swapped_by}}', '{{performed_by}}'),
        '{{id}}', '{{attachment_id}}'
    ),
    message_template = REPLACE(REPLACE(REPLACE(REPLACE(
        message_template,
        '{{old_unit}}', '{{from_unit_number}}'),
        '{{new_unit}}', '{{to_unit_number}}'),
        '{{swapped_by}}', '{{performed_by}}'),
        '{{id}}', '{{attachment_id}}'
    ),
    rule_description = 'Available variables: {{module}}, {{attachment_id}}, {{tipe_item}}, {{serial_number}}, {{merk}}, {{model}}, {{attachment_info}}, {{from_unit_id}}, {{from_unit_number}}, {{to_unit_id}}, {{to_unit_number}}, {{reason}}, {{performed_by}}, {{performed_at}}, {{url}}'
WHERE trigger_event = 'attachment_swapped';

-- CHECK CURRENT TEMPLATES
SELECT 
    id,
    name,
    trigger_event,
    title_template,
    message_template
FROM notification_rules
WHERE trigger_event IN ('attachment_swapped', 'attachment_attached', 'attachment_detached')
ORDER BY trigger_event;
