-- Migration: Fix #N/A values in inventory_unit table
-- Date: 2026-03-03
-- Purpose: Replace #N/A (Excel error values) with NULL for proper FK relationships

-- Check current state of problematic columns
-- SELECT kontrak_id, customer_id, customer_location_id, COUNT(*) as total
-- FROM inventory_unit
-- WHERE kontrak_id = '#N/A' OR customer_id = '#N/A' OR customer_location_id = '#N/A'
-- GROUP BY kontrak_id, customer_id, customer_location_id;

-- Fix kontrak_id - replace #N/A and invalid values with NULL
UPDATE inventory_unit
SET kontrak_id = NULL
WHERE kontrak_id = '#N/A' OR kontrak_id = 0 OR kontrak_id IS NULL;

-- Fix customer_id - replace #N/A and invalid values with NULL
UPDATE inventory_unit
SET customer_id = NULL
WHERE customer_id = '#N/A' OR customer_id = 0;

-- Fix customer_location_id - replace #N/A and invalid values with NULL
UPDATE inventory_unit
SET customer_location_id = NULL
WHERE customer_location_id = '#N/A' OR customer_location_id = 0;

-- Fix area_id - replace invalid values with NULL
UPDATE inventory_unit
SET area_id = NULL
WHERE area_id = '#N/A' OR area_id = 0;

-- Verification queries
-- SELECT 'kontrak_id issues' as check_type, COUNT(*) as count FROM inventory_unit WHERE kontrak_id = '#N/A' OR kontrak_id = 0;
-- SELECT 'customer_id issues' as check_type, COUNT(*) as count FROM inventory_unit WHERE customer_id = '#N/A' OR customer_id = 0;
-- SELECT 'customer_location_id issues' as check_type, COUNT(*) as count FROM inventory_unit WHERE customer_location_id = '#N/A' OR customer_location_id = 0;

-- Verify current state after fix
-- SELECT COUNT(*) as total_units, SUM(CASE WHEN kontrak_id IS NOT NULL THEN 1 ELSE 0 END) as linked_to_kontrak FROM inventory_unit;
-- SELECT COUNT(*) as total_units, SUM(CASE WHEN customer_id IS NOT NULL THEN 1 ELSE 0 END) as linked_to_customer FROM inventory_unit;

SELECT 'Fix #N/A values completed' AS status;
