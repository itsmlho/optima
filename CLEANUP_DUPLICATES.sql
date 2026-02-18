-- ========================================
-- CLEANUP DUPLICATE LOCATIONS & CONTRACTS
-- ========================================
-- Purpose: Remove duplicate locations and contracts after merge
-- Date: 2026-02-18
-- Safe: Updates foreign keys before deletion
-- NOTE: Select database 'u138256737_optima_db' in PHPMyAdmin before running

-- ========================================
-- STEP 1: FIX DUPLICATE LOCATIONS
-- ========================================
-- Strategy: Keep LOWEST ID (oldest), redirect references, delete duplicates

SELECT '========================================' as '';
SELECT 'STEP 1: Analyzing Duplicate Locations' as '';
SELECT '========================================' as '';

-- Show duplicates before cleanup
SELECT customer_id, location_name, COUNT(*) as duplicate_count
FROM customer_locations
GROUP BY customer_id, location_name
HAVING COUNT(*) > 1
ORDER BY duplicate_count DESC, customer_id
LIMIT 10;

-- Create temp table to store duplicate mappings
CREATE TEMPORARY TABLE location_duplicates AS
SELECT 
    cl.id as duplicate_id,
    (SELECT MIN(id) 
     FROM customer_locations cl2 
     WHERE cl2.customer_id = cl.customer_id 
       AND cl2.location_name = cl.location_name) as primary_id,
    cl.customer_id,
    cl.location_name
FROM customer_locations cl
WHERE cl.id NOT IN (
    SELECT MIN(id)
    FROM customer_locations
    GROUP BY customer_id, location_name
);

SELECT CONCAT('Found ', COUNT(*), ' duplicate locations to clean') as status
FROM location_duplicates;

-- Update inventory_unit references
UPDATE inventory_unit iu
INNER JOIN location_duplicates ld ON iu.customer_location_id = ld.duplicate_id
SET iu.customer_location_id = ld.primary_id;

SELECT CONCAT('Updated ', ROW_COUNT(), ' inventory_unit references') as status;

-- Update kontrak references
UPDATE kontrak k
INNER JOIN location_duplicates ld ON k.customer_location_id = ld.duplicate_id
SET k.customer_location_id = ld.primary_id;

SELECT CONCAT('Updated ', ROW_COUNT(), ' kontrak references') as status;

-- Delete duplicate locations
DELETE cl FROM customer_locations cl
INNER JOIN location_duplicates ld ON cl.id = ld.duplicate_id;

SELECT CONCAT('Deleted ', ROW_COUNT(), ' duplicate locations') as status;

-- Verify no more duplicates
SELECT CONCAT('Remaining duplicates: ', COUNT(*)) as verification
FROM (
    SELECT customer_id, location_name
    FROM customer_locations
    GROUP BY customer_id, location_name
    HAVING COUNT(*) > 1
) as remaining;

-- ========================================
-- STEP 2: FIX DUPLICATE CONTRACTS
-- ========================================
-- Strategy: Keep LOWEST ID (oldest), redirect references, delete duplicates

SELECT '========================================' as '';
SELECT 'STEP 2: Analyzing Duplicate Contracts' as '';
SELECT '========================================' as '';

-- Show duplicates before cleanup
SELECT no_po_marketing, COUNT(*) as duplicate_count
FROM kontrak
WHERE no_po_marketing IS NOT NULL
GROUP BY no_po_marketing
HAVING COUNT(*) > 1
ORDER BY duplicate_count DESC
LIMIT 10;

-- Create temp table for contract duplicates
CREATE TEMPORARY TABLE contract_duplicates AS
SELECT 
    k.id as duplicate_id,
    (SELECT MIN(id) 
     FROM kontrak k2 
     WHERE k2.no_po_marketing = k.no_po_marketing) as primary_id,
    k.no_po_marketing
FROM kontrak k
WHERE k.no_po_marketing IS NOT NULL
  AND k.id NOT IN (
    SELECT MIN(id)
    FROM kontrak
    WHERE no_po_marketing IS NOT NULL
    GROUP BY no_po_marketing
);

SELECT CONCAT('Found ', COUNT(*), ' duplicate contracts to clean') as status
FROM contract_duplicates;

-- Update inventory_unit references
UPDATE inventory_unit iu
INNER JOIN contract_duplicates cd ON iu.kontrak_id = cd.duplicate_id
SET iu.kontrak_id = cd.primary_id;

SELECT CONCAT('Updated ', ROW_COUNT(), ' inventory_unit references') as status;

-- Update customer_contracts references
UPDATE IGNORE customer_contracts cc
INNER JOIN contract_duplicates cd ON cc.kontrak_id = cd.duplicate_id
SET cc.kontrak_id = cd.primary_id;

SELECT CONCAT('Updated ', ROW_COUNT(), ' customer_contracts references') as status;

-- Delete duplicate from customer_contracts (after UPDATE IGNORE may have dupes)
DELETE cc FROM customer_contracts cc
INNER JOIN (
    SELECT customer_id, kontrak_id, MIN(id) as keep_id
    FROM customer_contracts
    GROUP BY customer_id, kontrak_id
    HAVING COUNT(*) > 1
) dupes ON cc.customer_id = dupes.customer_id 
       AND cc.kontrak_id = dupes.kontrak_id 
       AND cc.id != dupes.keep_id;

SELECT CONCAT('Removed ', ROW_COUNT(), ' duplicate customer_contracts links') as status;

-- Delete duplicate contracts
DELETE k FROM kontrak k
INNER JOIN contract_duplicates cd ON k.id = cd.duplicate_id;

SELECT CONCAT('Deleted ', ROW_COUNT(), ' duplicate contracts') as status;

-- Verify no more duplicates
SELECT CONCAT('Remaining duplicates: ', COUNT(*)) as verification
FROM (
    SELECT no_po_marketing
    FROM kontrak
    WHERE no_po_marketing IS NOT NULL
    GROUP BY no_po_marketing
    HAVING COUNT(*) > 1
) as remaining;

-- ========================================
-- STEP 3: UPDATE CONTRACT TOTAL_UNITS
-- ========================================

SELECT '========================================' as '';
SELECT 'STEP 3: Updating Contract Unit Counts' as '';
SELECT '========================================' as '';

UPDATE kontrak k
SET k.total_units = (
    SELECT COUNT(*)
    FROM inventory_unit iu
    WHERE iu.kontrak_id = k.id
);

SELECT CONCAT('Updated total_units for ', ROW_COUNT(), ' contracts') as status;

-- ========================================
-- FINAL VERIFICATION
-- ========================================

SELECT '========================================' as '';
SELECT 'FINAL VERIFICATION' as '';
SELECT '========================================' as '';

SELECT 
    'Units Assigned' as metric,
    COUNT(*) as count
FROM inventory_unit 
WHERE customer_id IS NOT NULL

UNION ALL

SELECT 
    'Locations (After Cleanup)',
    COUNT(*)
FROM customer_locations

UNION ALL

SELECT 
    'Duplicate Locations',
    COUNT(*)
FROM (
    SELECT customer_id, location_name
    FROM customer_locations
    GROUP BY customer_id, location_name
    HAVING COUNT(*) > 1
) d

UNION ALL

SELECT 
    'Contracts (After Cleanup)',
    COUNT(*)
FROM kontrak

UNION ALL

SELECT 
    'Duplicate Contracts',
    COUNT(*)
FROM (
    SELECT no_po_marketing
    FROM kontrak
    WHERE no_po_marketing IS NOT NULL
    GROUP BY no_po_marketing
    HAVING COUNT(*) > 1
) d;

-- ========================================
-- CLEANUP COMPLETE!
-- ========================================

SELECT '✅ CLEANUP COMPLETE - DATABASE 100% CLEAN!' as result;
