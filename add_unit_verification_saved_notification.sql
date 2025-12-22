-- ============================================================================
-- Add notification rule for Unit Verification Saved (Always Trigger)
-- ============================================================================
-- This is DIFFERENT from work_order_unit_verified:
-- - unit_verification_saved: Trigger SETIAP kali verifikasi disimpan
-- - work_order_unit_verified: Trigger HANYA jika ada perubahan data
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
    'Notifikasi Unit Verification Disimpan',
    'Notifikasi dikirim setiap kali unit verification disimpan untuk work order',
    'unit_verification_saved',
    'Unit Verification Disimpan - WO: {{wo_number}}',
    'Unit {{unit_code}} telah diverifikasi.\n\nStatus: {{verification_status}}\nOleh: {{verified_by}}\nTanggal: {{verification_date}}',
    'success',
    'Service',
    1,
    NOW(),
    NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM notification_rules 
    WHERE trigger_event = 'unit_verification_saved' 
    AND target_divisions = 'Service'
);

-- Add notification for Manager role
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
    'Notifikasi Unit Verification Disimpan - Manager',
    'Notifikasi untuk manager setiap kali unit verification disimpan',
    'unit_verification_saved',
    'Unit Verification Disimpan - WO: {{wo_number}}',
    'Unit {{unit_code}} telah diverifikasi.\n\nStatus: {{verification_status}}\nOleh: {{verified_by}}',
    'info',
    'Manager',
    1,
    NOW(),
    NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM notification_rules 
    WHERE trigger_event = 'unit_verification_saved' 
    AND target_roles = 'Manager'
);

-- Show results
SELECT 'Notification rules for unit_verification_saved created successfully!' as Status;

SELECT 
    nr.id,
    nr.name,
    nr.trigger_event,
    nr.target_divisions,
    nr.target_roles,
    nr.is_active
FROM notification_rules nr
WHERE nr.trigger_event = 'unit_verification_saved';
