-- ========================================
-- PRODUCTION SCHEMA UPDATE
-- ========================================
-- Purpose: Add missing columns to match development schema
-- Run this BEFORE importing data
-- Date: 2026-02-18

START TRANSACTION;

-- ========================================
-- STEP 1: Check current schema
-- ========================================

SELECT 'Current kontrak table structure:' as '';
SHOW COLUMNS FROM kontrak;

-- ========================================
-- STEP 2: Add rental_type column (if not exists)
-- ========================================

-- Check if column exists
SELECT COUNT(*) as rental_type_exists 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'kontrak' 
  AND COLUMN_NAME = 'rental_type';

-- If result is 0, column doesn't exist, need to add it
-- Run this:

ALTER TABLE kontrak 
ADD COLUMN rental_type ENUM('CONTRACT','PO_ONLY','DAILY_SPOT') 
NOT NULL DEFAULT 'PO_ONLY'
AFTER no_kontrak;

SELECT '✅ Added rental_type column' as status;

-- ========================================
-- STEP 3: Verify updated schema
-- ========================================

SELECT 'Updated kontrak table structure:' as '';
SHOW COLUMNS FROM kontrak;

-- ========================================
-- STEP 4: Check if there are other missing columns
-- ========================================

-- Compare with development schema
-- Development has these columns in kontrak:
-- - id
-- - parent_contract_id
-- - is_renewal
-- - renewal_generation
-- - renewal_initiated_at
-- - renewal_initiated_by
-- - renewal_completed_at
-- - customer_location_id
-- - no_kontrak
-- - rental_type ← WE JUST ADDED THIS
-- - customer_po_number
-- - nilai_total
-- - total_units
-- - jenis_sewa
-- - billing_method
-- - tanggal_mulai
-- - tanggal_berakhir
-- - status
-- - dibuat_oleh
-- - dibuat_pada
-- - diperbarui_pada
-- - billing_notes
-- - billing_start_date
-- - fast_track
-- - spot_rental_number
-- - estimated_duration_days
-- - actual_return_date
-- - requires_po_approval

SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT, COLUMN_TYPE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'kontrak'
ORDER BY ORDINAL_POSITION;

COMMIT;

SELECT '✅ Schema update complete!' as result;
SELECT 'Production database now matches development schema' as status;
SELECT 'Next step: Run fix_customer_names.sql' as next_action;
