-- ========================================
-- PRODUCTION MIGRATION - COMPLETE SQL
-- ========================================
-- Generated: 2026-02-18 (PRODUCTION COMPATIBLE)
-- Compatible with: u138256737_optima_db
-- Total units to import: 2178
-- Schema: Compatible with older kontrak table (no rental_type)

SET FOREIGN_KEY_CHECKS=0;
START TRANSACTION;

-- ========================================
-- NOTE: This is a STREAMLINED production script
-- If production has NO existing assigned units, 
-- we can SKIP the RESET step and go straight to import
-- ========================================

-- ========================================
-- OPTION 1: If production HAS existing assignments
-- Run this query first to check:
-- SELECT COUNT(*) FROM inventory_unit WHERE customer_id IS NOT NULL;
--
-- If result > 0, uncomment the RESET section below
-- If result = 0, skip to STEP 2
-- ========================================

/*
-- ========================================
-- STEP 1: RESET OVERLAPPING UNITS (OPTIONAL)
-- Only needed if production already has assignments
-- ========================================
UPDATE inventory_unit
SET customer_id = NULL,
    kontrak_id = NULL,
    customer_location_id = NULL,
    area_id = NULL,
    harga_sewa_bulanan = NULL,
    harga_sewa_harian = NULL,
    on_hire_date = NULL,
    off_hire_date = NULL,
    rate_changed_at = NULL
WHERE customer_id IS NOT NULL;
*/

-- ========================================
-- STEP 2: Import will be done via Python script
-- Due to 2MB size limit in PHPMyAdmin
-- ========================================

COMMIT;
SET FOREIGN_KEY_CHECKS=1;

SELECT 'Preparation complete. Now run: python upload_to_production.py' as next_step;
