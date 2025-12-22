-- =====================================================
-- Script: Populate no_unit_na for existing Non-Asset units
-- Purpose: Assign NA-xxx numbers to units with status_unit_id IN (2, 8)
-- Date: 2025-12-23
-- =====================================================

-- Start transaction
START TRANSACTION;

-- Initialize counter
SET @counter = 0;

-- Update units with status Non-Asset (2 or 8) that don't have no_unit_na yet
UPDATE inventory_unit 
SET no_unit_na = CONCAT('NA-', LPAD(@counter := @counter + 1, 3, '0'))
WHERE status_unit_id IN (2, 8) 
  AND no_unit_na IS NULL
  AND no_unit IS NULL  -- Only units without asset number
ORDER BY id_inventory_unit ASC;

-- Show results
SELECT 
    id_inventory_unit,
    no_unit,
    no_unit_na,
    status_unit_id,
    serial_number
FROM inventory_unit
WHERE no_unit_na IS NOT NULL
ORDER BY no_unit_na;

-- Commit if everything looks good
COMMIT;

-- =====================================================
-- Verification
-- =====================================================
SELECT 
    COUNT(*) as total_non_asset,
    SUM(CASE WHEN no_unit_na IS NOT NULL THEN 1 ELSE 0 END) as with_number,
    SUM(CASE WHEN no_unit_na IS NULL THEN 1 ELSE 0 END) as without_number
FROM inventory_unit
WHERE status_unit_id IN (2, 8);
