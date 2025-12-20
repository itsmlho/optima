-- ============================================================================
-- Fill Category and Description for Notification Rules
-- ============================================================================
-- Created: 2025-12-19
-- Purpose: Add category and description to rules that are NULL
-- ============================================================================

-- Payment Related
UPDATE notification_rules SET 
    category = 'payment',
    description = 'Notifikasi ketika status pembayaran diperbarui'
WHERE id = 105;

-- Purchase Order Related
UPDATE notification_rules SET 
    category = 'purchase_order',
    description = 'Notifikasi ketika Purchase Order baru dibuat'
WHERE id = 106;

-- Delivery Related
UPDATE notification_rules SET 
    category = 'delivery',
    description = 'Notifikasi ketika status delivery berubah'
WHERE id = 108;

-- Work Order Related
UPDATE notification_rules SET 
    category = 'work_order',
    description = 'Notifikasi ketika status work order berubah'
WHERE id = 110;

UPDATE notification_rules SET 
    category = 'work_order',
    description = 'Notifikasi ketika work order ditugaskan ke mechanic'
WHERE id = 116;

UPDATE notification_rules SET 
    category = 'work_order',
    description = 'Notifikasi ketika work order selesai dikerjakan'
WHERE id = 117;

UPDATE notification_rules SET 
    category = 'work_order',
    description = 'Notifikasi critical ketika work order terlambat'
WHERE id = 118;

UPDATE notification_rules SET 
    category = 'work_order',
    description = 'Notifikasi ketika sparepart ditambahkan ke work order'
WHERE id = 119;

-- PO Verification
UPDATE notification_rules SET 
    category = 'verification',
    description = 'Notifikasi ketika verifikasi PO diperbarui'
WHERE id = 111;

-- Quotation Related
UPDATE notification_rules SET 
    category = 'quotation',
    description = 'Notifikasi ketika quotation diperbarui'
WHERE id = 113;

UPDATE notification_rules SET 
    category = 'quotation',
    description = 'Notifikasi ketika quotation disetujui'
WHERE id = 114;

UPDATE notification_rules SET 
    category = 'quotation',
    description = 'Notifikasi ketika quotation ditolak'
WHERE id = 115;

UPDATE notification_rules SET 
    category = 'quotation',
    description = 'Notifikasi ketika quotation dikirim ke customer'
WHERE id = 141;

UPDATE notification_rules SET 
    category = 'quotation',
    description = 'Notifikasi reminder untuk follow-up quotation'
WHERE id = 142;

-- Service Assignment
UPDATE notification_rules SET 
    category = 'service_assignment',
    description = 'Notifikasi ketika penugasan service area dibuat'
WHERE id = 120;

UPDATE notification_rules SET 
    category = 'service_assignment',
    description = 'Notifikasi ketika penugasan service area diperbarui'
WHERE id = 121;

UPDATE notification_rules SET 
    category = 'service_assignment',
    description = 'Notifikasi ketika penugasan service area selesai'
WHERE id = 122;

-- Unit Related
UPDATE notification_rules SET 
    category = 'unit',
    description = 'Notifikasi ketika lokasi unit diperbarui (GPS tracking)'
WHERE id = 123;

UPDATE notification_rules SET 
    category = 'unit',
    description = 'Notifikasi ketika data unit warehouse diperbarui'
WHERE id = 124;

-- Contract & Customer
UPDATE notification_rules SET 
    category = 'contract',
    description = 'Notifikasi ketika kontrak baru dibuat dari quotation'
WHERE id = 125;

UPDATE notification_rules SET 
    category = 'customer',
    description = 'Notifikasi ketika customer baru ditambahkan'
WHERE id = 126;

UPDATE notification_rules SET 
    category = 'customer',
    description = 'Notifikasi ketika status customer berubah'
WHERE id = 128;

-- Warehouse Related
UPDATE notification_rules SET 
    category = 'warehouse',
    description = 'Notifikasi alert ketika stok barang rendah'
WHERE id = 129;

UPDATE notification_rules SET 
    category = 'warehouse',
    description = 'Notifikasi ketika transfer barang antar warehouse selesai'
WHERE id = 130;

UPDATE notification_rules SET 
    category = 'warehouse',
    description = 'Notifikasi ketika stocktake warehouse selesai'
WHERE id = 131;

-- Maintenance & Inspection
UPDATE notification_rules SET 
    category = 'inspection',
    description = 'Notifikasi ketika inspeksi unit dijadwalkan'
WHERE id = 132;

UPDATE notification_rules SET 
    category = 'inspection',
    description = 'Notifikasi ketika inspeksi unit selesai'
WHERE id = 133;

UPDATE notification_rules SET 
    category = 'maintenance',
    description = 'Notifikasi ketika maintenance unit dijadwalkan'
WHERE id = 134;

UPDATE notification_rules SET 
    category = 'maintenance',
    description = 'Notifikasi ketika maintenance unit selesai'
WHERE id = 135;

-- Payment Critical
UPDATE notification_rules SET 
    category = 'payment',
    description = 'Notifikasi ketika pembayaran diterima'
WHERE id = 136;

UPDATE notification_rules SET 
    category = 'payment',
    description = 'Notifikasi critical ketika pembayaran terlambat (overdue)'
WHERE id = 137;

-- Budget
UPDATE notification_rules SET 
    category = 'budget',
    description = 'Notifikasi alert ketika budget melebihi threshold'
WHERE id = 138;

-- SPK
UPDATE notification_rules SET 
    category = 'spk',
    description = 'Notifikasi ketika SPK selesai dikerjakan'
WHERE id = 140;

-- ============================================================================
-- Verification Query
-- ============================================================================
SELECT 'Rules with category' as status, COUNT(*) as count 
FROM notification_rules 
WHERE category IS NOT NULL AND category != '';

SELECT 'Rules with description' as status, COUNT(*) as count 
FROM notification_rules 
WHERE description IS NOT NULL AND description != '';

-- Show sample
SELECT id, name, category, LEFT(description, 50) as description_preview
FROM notification_rules 
WHERE id IN (105, 106, 116, 125, 126, 137, 140)
ORDER BY id;

-- ============================================================================
-- DONE
-- ============================================================================
