-- =====================================================
-- INVENTORY ATTACHMENT STATUS CLEANUP MIGRATION
-- =====================================================
-- Date: 2026-02-02
-- Purpose: Standardisasi status fields di inventory_attachment
-- Phase 1: Data Cleanup & Standardization
-- =====================================================

-- 1. Buat backup data sebelum migration
CREATE TABLE IF NOT EXISTS inventory_attachment_backup_20260202 AS 
SELECT * FROM inventory_attachment;

-- 2. Standardisasi nilai RUSAK menjadi BROKEN
UPDATE inventory_attachment 
SET attachment_status = 'BROKEN' 
WHERE attachment_status = 'RUSAK';

-- 3. Mapping status_unit ke attachment_status untuk data yang belum konsisten
-- Berdasarkan analisis kode, status_unit values:
-- 1 = AVAILABLE (stock)
-- 7 = AVAILABLE (default) 
-- 11 = AVAILABLE (stock non asset)
-- Other values = perlu mapping sesuai business logic

UPDATE inventory_attachment 
SET attachment_status = CASE 
    WHEN status_unit IN (1, 7, 11) AND attachment_status != 'IN_USE' THEN 'AVAILABLE'
    WHEN status_unit IN (2, 3, 4, 5, 6) THEN 'IN_USE'
    WHEN status_unit IN (8, 9) THEN 'MAINTENANCE' 
    WHEN status_unit = 10 THEN 'BROKEN'
    ELSE attachment_status
END
WHERE attachment_status IS NULL 
   OR attachment_status = '';

-- 4. Pastikan semua record punya attachment_status yang valid
UPDATE inventory_attachment 
SET attachment_status = 'AVAILABLE' 
WHERE attachment_status IS NULL 
   OR attachment_status = '';

-- 5. Verification query - check data consistency
SELECT 'DATA CONSISTENCY CHECK' as check_type;

SELECT 
    attachment_status, 
    COUNT(*) as count,
    GROUP_CONCAT(DISTINCT status_unit) as status_units_found
FROM inventory_attachment 
GROUP BY attachment_status;

-- 6. Show records with potential issues
SELECT 'POTENTIAL ISSUES' as check_type;

SELECT id_inventory_attachment, tipe_item, attachment_status, status_unit, status_attachment_id
FROM inventory_attachment 
WHERE attachment_status IS NULL 
   OR attachment_status = ''
   OR attachment_status NOT IN ('AVAILABLE', 'IN_USE', 'USED', 'MAINTENANCE', 'BROKEN', 'RESERVED');