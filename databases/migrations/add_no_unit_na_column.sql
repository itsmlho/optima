-- =====================================================
-- Migration: Add no_unit_na column for Non-Asset numbering
-- Purpose: Enable separate numbering system for Non-Asset units (NA-001 to NA-500)
-- Strategy: Gap-filling (reuse vacated numbers when units convert to Asset)
-- Date: 2025-12-23
-- =====================================================

-- Add no_unit_na column to inventory_unit table
ALTER TABLE inventory_unit 
ADD COLUMN no_unit_na VARCHAR(50) DEFAULT NULL 
COMMENT 'Nomor unit untuk Non-Asset (format: NA-001 to NA-500, reusable)' 
AFTER no_unit;

-- Create unique index to ensure no duplicate non-asset numbers
CREATE UNIQUE INDEX idx_no_unit_na ON inventory_unit(no_unit_na);

-- Add index for performance on searches
CREATE INDEX idx_no_unit_na_pattern ON inventory_unit(no_unit_na(10));

-- =====================================================
-- Optional: Populate existing non-asset units with numbers
-- WARNING: Only run this if you want to assign numbers to existing units
-- =====================================================

-- Uncomment below to auto-assign numbers to existing non-asset units
/*
SET @counter = 0;

UPDATE inventory_unit 
SET no_unit_na = CONCAT('NA-', LPAD(@counter := @counter + 1, 3, '0'))
WHERE status_unit_id = 8 
  AND no_unit_na IS NULL
ORDER BY id_inventory_unit ASC;
*/

-- =====================================================
-- Verification queries
-- =====================================================

-- Check if column exists
SELECT 
    COLUMN_NAME,
    COLUMN_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT,
    COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'inventory_unit'
  AND COLUMN_NAME = 'no_unit_na';

-- Check indexes
SHOW INDEX FROM inventory_unit WHERE Key_name LIKE '%no_unit_na%';

-- Count non-asset units with and without numbers
SELECT 
    COUNT(*) as total_non_asset,
    SUM(CASE WHEN no_unit_na IS NOT NULL THEN 1 ELSE 0 END) as with_number,
    SUM(CASE WHEN no_unit_na IS NULL THEN 1 ELSE 0 END) as without_number
FROM inventory_unit
WHERE status_unit_id = 8;

-- =====================================================
-- Rollback script (if needed)
-- =====================================================

-- Uncomment below to rollback this migration
/*
ALTER TABLE inventory_unit DROP INDEX idx_no_unit_na;
ALTER TABLE inventory_unit DROP INDEX idx_no_unit_na_pattern;
ALTER TABLE inventory_unit DROP COLUMN no_unit_na;
*/
