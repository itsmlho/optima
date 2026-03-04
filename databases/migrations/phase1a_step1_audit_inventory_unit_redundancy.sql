-- ===================================================
-- PHASE 1A - STEP 1: AUDIT INVENTORY_UNIT REDUNDANCY
-- ===================================================
-- Purpose: Check for data mismatches between inventory_unit redundant fields
--          and the proper kontrak_unit junction table
-- Date: March 4, 2026
-- Author: System Refactoring
-- ===================================================

-- INSTRUCTIONS:
-- 1. Run this script in staging environment first
-- 2. Review results carefully
-- 3. Document any mismatches found
-- 4. Create reconciliation plan before proceeding with migration

-- ===================================================
-- 1. CHECK FOR MISMATCHES
-- ===================================================

SELECT 
    iu.id_inventory_unit,
    iu.no_unit,
    iu.kontrak_id as unit_table_kontrak_id,
    ku.kontrak_id as junction_table_kontrak_id,
    iu.customer_id as unit_table_customer_id,
    cl.customer_id as derived_customer_id,
    iu.customer_location_id as unit_table_location_id,
    k.customer_location_id as derived_location_id,
    CASE 
        WHEN iu.kontrak_id IS NULL AND ku.kontrak_id IS NULL THEN 'OK: Both NULL (Unit not assigned)'
        WHEN iu.kontrak_id IS NOT NULL AND ku.kontrak_id IS NULL THEN 'MISMATCH: Unit table has kontrak_id but junction is empty'
        WHEN iu.kontrak_id IS NULL AND ku.kontrak_id IS NOT NULL THEN 'MISMATCH: Junction has kontrak_id but unit table is NULL'
        WHEN iu.kontrak_id != ku.kontrak_id THEN 'MISMATCH: Different kontrak_id values'
        WHEN iu.kontrak_id = ku.kontrak_id THEN 'OK: Match'
        ELSE 'UNKNOWN'
    END as kontrak_status,
    CASE
        WHEN iu.customer_id IS NULL AND cl.customer_id IS NULL THEN 'OK: Both NULL'
        WHEN iu.customer_id = cl.customer_id THEN 'OK: Match'
        ELSE 'MISMATCH: Different customer_id'
    END as customer_status,
    ku.status as junction_status,
    ku.is_temporary
FROM inventory_unit iu
LEFT JOIN kontrak_unit ku ON iu.id_inventory_unit = ku.unit_id 
    AND ku.status IN ('AKTIF', 'DIPERPANJANG', 'MENUNGGU_PERSETUJUAN')
    AND ku.is_temporary = 0  -- Exclude temporary replacements
LEFT JOIN kontrak k ON ku.kontrak_id = k.id
LEFT JOIN customer_locations cl ON k.customer_location_id = cl.id
WHERE iu.kontrak_id IS NOT NULL OR ku.kontrak_id IS NOT NULL
ORDER BY 
    CASE 
        WHEN iu.kontrak_id != ku.kontrak_id THEN 1
        WHEN iu.customer_id != cl.customer_id THEN 2
        ELSE 3
    END,
    iu.id_inventory_unit;

-- ===================================================
-- 2. SUMMARY STATISTICS
-- ===================================================

SELECT 
    'Total Units' as metric,
    COUNT(*) as count
FROM inventory_unit
UNION ALL
SELECT 
    'Units with kontrak_id (direct field)',
    COUNT(*)
FROM inventory_unit
WHERE kontrak_id IS NOT NULL
UNION ALL
SELECT 
    'Units in junction table (active)',
    COUNT(*)
FROM kontrak_unit
WHERE status IN ('AKTIF', 'DIPERPANJANG', 'MENUNGGU_PERSETUJUAN')
    AND is_temporary = 0
UNION ALL
SELECT 
    'Units with MATCHING kontrak_id',
    COUNT(*)
FROM inventory_unit iu
INNER JOIN kontrak_unit ku ON iu.id_inventory_unit = ku.unit_id
    AND iu.kontrak_id = ku.kontrak_id
    AND ku.status IN ('AKTIF', 'DIPERPANJANG', 'MENUNGGU_PERSETUJUAN')
    AND ku.is_temporary = 0
UNION ALL
SELECT 
    'Units with MISMATCHED kontrak_id',
    COUNT(*)
FROM inventory_unit iu
LEFT JOIN kontrak_unit ku ON iu.id_inventory_unit = ku.unit_id
    AND ku.status IN ('AKTIF', 'DIPERPANJANG', 'MENUNGGU_PERSETUJUAN')
    AND ku.is_temporary = 0
WHERE (iu.kontrak_id IS NOT NULL AND ku.kontrak_id IS NULL)
   OR (iu.kontrak_id IS NULL AND ku.kontrak_id IS NOT NULL)
   OR (iu.kontrak_id != ku.kontrak_id);

-- ===================================================
-- 3. IDENTIFY ORPHANED RECORDS
-- ===================================================

-- Units with kontrak_id that doesn't exist in kontrak table
SELECT 
    'Orphaned kontrak_id in inventory_unit' as issue,
    iu.id_inventory_unit,
    iu.no_unit,
    iu.kontrak_id as invalid_kontrak_id
FROM inventory_unit iu
LEFT JOIN kontrak k ON iu.kontrak_id = k.id
WHERE iu.kontrak_id IS NOT NULL 
  AND k.id IS NULL;

-- Units with customer_id that doesn't exist in customers table
SELECT 
    'Orphaned customer_id in inventory_unit' as issue,
    iu.id_inventory_unit,
    iu.no_unit,
    iu.customer_id as invalid_customer_id
FROM inventory_unit iu
LEFT JOIN customers c ON iu.customer_id = c.id
WHERE iu.customer_id IS NOT NULL 
  AND c.id IS NULL;

-- Units with customer_location_id that doesn't exist
SELECT 
    'Orphaned customer_location_id in inventory_unit' as issue,
    iu.id_inventory_unit,
    iu.no_unit,
    iu.customer_location_id as invalid_location_id
FROM inventory_unit iu
LEFT JOIN customer_locations cl ON iu.customer_location_id = cl.id
WHERE iu.customer_location_id IS NOT NULL 
  AND cl.id IS NULL;

-- ===================================================
-- 4. JUNCTION TABLE INTEGRITY CHECK
-- ===================================================

-- Check for units in junction that don't exist in inventory_unit
SELECT 
    'Unit in junction but not in inventory_unit' as issue,
    ku.unit_id,
    ku.kontrak_id,
    ku.status
FROM kontrak_unit ku
LEFT JOIN inventory_unit iu ON ku.unit_id = iu.id_inventory_unit
WHERE iu.id_inventory_unit IS NULL;

-- Check for contracts in junction that don't exist in kontrak table
SELECT 
    'Contract in junction but not in kontrak table' as issue,
    ku.kontrak_id,
    COUNT(*) as affected_units
FROM kontrak_unit ku
LEFT JOIN kontrak k ON ku.kontrak_id = k.id
WHERE k.id IS NULL
GROUP BY ku.kontrak_id;

-- ===================================================
-- 5. EXPORT RESULTS FOR DOCUMENTATION
-- ===================================================

-- Instructions:
-- 1. Save results of each query to CSV
-- 2. Document in JIRA/issue tracker
-- 3. Create cleanup plan for mismatches
-- 4. Get approval before proceeding to next migration step

-- ===================================================
-- EXPECTED OUTCOMES
-- ===================================================

-- IDEAL STATE:
-- - Zero mismatches between inventory_unit and kontrak_unit
-- - All units either have kontrak_id in BOTH tables or NEITHER
-- - No orphaned records
-- - Junction table is source of truth

-- IF MISMATCHES FOUND:
-- - Create reconciliation SQL script
-- - Update inventory_unit.kontrak_id from kontrak_unit (junction is truth)
-- - Or set kontrak_id = NULL if unit not in active contract
-- - Document business decision and get approval

-- ===================================================
-- END OF AUDIT SCRIPT
-- ===================================================
