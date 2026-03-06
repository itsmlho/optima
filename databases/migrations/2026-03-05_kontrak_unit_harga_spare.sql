-- ============================================================
-- Migration: Add harga_sewa & is_spare to kontrak_unit
-- Also backfill kontrak.customer_id from customer_locations
-- Date: 2026-03-05
-- ============================================================

-- 1. Add harga_sewa column to kontrak_unit
--    NULL means "use inventory_unit.harga_sewa_bulanan as default"
ALTER TABLE kontrak_unit 
ADD COLUMN harga_sewa DECIMAL(15,2) DEFAULT NULL 
COMMENT 'Override harga sewa per unit per kontrak. NULL = pakai default dari inventory_unit.harga_sewa_bulanan'
AFTER unit_id;

-- 2. Add is_spare flag to kontrak_unit
--    Spare/backup units have harga=0, not counted in total nilai
ALTER TABLE kontrak_unit 
ADD COLUMN is_spare TINYINT(1) NOT NULL DEFAULT 0 
COMMENT 'Flag unit spare/backup. 1 = spare (harga 0, tidak masuk total nilai)'
AFTER harga_sewa;

-- 3. Backfill kontrak.customer_id from customer_locations
--    Many kontrak have customer_id NULL but have customer_location_id set
UPDATE kontrak k
JOIN customer_locations cl ON k.customer_location_id = cl.id
SET k.customer_id = cl.customer_id
WHERE k.customer_id IS NULL AND cl.customer_id IS NOT NULL;

-- 4. Verify results
SELECT 'kontrak_unit columns added' as step,
       (SELECT COUNT(*) FROM information_schema.COLUMNS 
        WHERE TABLE_NAME = 'kontrak_unit' AND COLUMN_NAME = 'harga_sewa') as harga_sewa_exists,
       (SELECT COUNT(*) FROM information_schema.COLUMNS 
        WHERE TABLE_NAME = 'kontrak_unit' AND COLUMN_NAME = 'is_spare') as is_spare_exists;

SELECT 'kontrak.customer_id backfill' as step,
       (SELECT COUNT(*) FROM kontrak WHERE customer_id IS NULL) as still_null,
       (SELECT COUNT(*) FROM kontrak WHERE customer_id IS NOT NULL) as has_customer_id;
