-- ============================================================================
-- Add notification rule for Work Order Unit Verification with data changes
-- ============================================================================

-- Add notification rule for Service Division
INSERT INTO notification_rules (
    name,
    description,
    trigger_event,
    title_template,
    message_template,
    type,
    target_divisions,
    is_active,
    created_at,
    updated_at
)
SELECT
    'Notifikasi Verifikasi Unit Work Order (Perubahan Data)',
    'Notifikasi dikirim ketika verifikasi unit work order selesai dan ada perubahan data antara database dan kondisi lapangan',
    'work_order_unit_verified',
    'Verifikasi Unit WO: {{wo_number}}',
    'Perubahan data yang dilakukan pada No Unit {{unit_code}}:\n- {{changes_list}}\n\nOleh: {{created_by}}',
    'info',
    'Service',
    1,
    NOW(),
    NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM notification_rules 
    WHERE trigger_event = 'work_order_unit_verified' 
    AND target_divisions = 'Service'
);

-- Add notification for Warehouse division (untuk monitor attachment changes)
INSERT INTO notification_rules (
    name,
    description,
    trigger_event,
    title_template,
    message_template,
    type,
    target_divisions,
    is_active,
    created_at,
    updated_at
)
SELECT
    'Notifikasi Verifikasi Unit Work Order - Warehouse Monitor',
    'Monitor perubahan attachment/charger/baterai pada verifikasi unit',
    'work_order_unit_verified',
    'Verifikasi Unit WO: {{wo_number}}',
    'Perubahan data yang dilakukan pada No Unit {{unit_code}}:\n- {{changes_list}}\n\nOleh: {{created_by}}',
    'info',
    'Warehouse',
    1,
    NOW(),
    NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM notification_rules 
    WHERE trigger_event = 'work_order_unit_verified' 
    AND target_divisions = 'Warehouse'
);

-- Add notification for Manager role (supervisor level)
INSERT INTO notification_rules (
    name,
    description,
    trigger_event,
    title_template,
    message_template,
    type,
    target_roles,
    is_active,
    created_at,
    updated_at
)
SELECT
    'Notifikasi Verifikasi Unit Work Order - Manager',
    'Notifikasi untuk manager ketika ada perubahan data pada verifikasi unit',
    'work_order_unit_verified',
    'Verifikasi Unit WO: {{wo_number}}',
    'Perubahan data yang dilakukan pada No Unit {{unit_code}}:\n- {{changes_list}}\n\nOleh: {{created_by}}\n\nTotal {{changes_count}} perubahan terdeteksi.',
    'warning',
    'Manager',
    1,
    NOW(),
    NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM notification_rules 
    WHERE trigger_event = 'work_order_unit_verified' 
    AND target_roles = 'Manager'
);

-- Show results
SELECT 'Notification rules created successfully!' as Status;

SELECT 
    nr.id,
    nr.name,
    nr.trigger_event,
    nr.target_divisions,
    nr.target_roles,
    nr.is_active
FROM notification_rules nr
WHERE nr.trigger_event = 'work_order_unit_verified';
