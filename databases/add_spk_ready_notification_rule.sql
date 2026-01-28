-- Add Notification Rule for SPK Ready Event
-- Notification ini akan dikirim ke Operational division ketika SPK status berubah menjadi READY

-- Step 1: Add trigger event first
INSERT INTO trigger_events (
    event_code,
    event_name,
    description,
    module,
    is_active,
    created_at
) VALUES (
    'spk_ready',
    'SPK Siap Operasional',
    'Triggered when all SPK units complete PDI and SPK status becomes READY',
    'SPK',
    1,
    NOW()
);

-- Step 2: Insert notification rule for SPK Ready
INSERT INTO notification_rules (
    name,
    description,
    trigger_event,
    title_template,
    message_template,
    target_divisions,
    category,
    type,
    priority,
    is_active,
    created_at,
    updated_at
) VALUES (
    'SPK Ready for Operational',
    'Notifikasi ketika SPK sudah siap untuk dibuatkan Delivery Instruction oleh divisi Operational',
    'spk_ready',
    'SPK Siap untuk Operasional',
    'SPK {nomor_spk} untuk customer {pelanggan} sudah siap ({jumlah_unit} unit telah selesai PDI). Silakan buat Delivery Instruction.',
    'Operational',
    'SPK',
    'success',
    2,
    1,
    NOW(),
    NOW()
);

-- Step 3: Verify insertions
SELECT 
    event_code,
    event_name,
    module,
    is_active
FROM trigger_events
WHERE event_code = 'spk_ready';

SELECT 
    id,
    name,
    trigger_event,
    target_divisions,
    category,
    type,
    is_active
FROM notification_rules
WHERE trigger_event = 'spk_ready';
