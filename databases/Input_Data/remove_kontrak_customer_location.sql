-- ============================================================
-- MIGRATION: Remove customer_location_id from kontrak table
-- Date: 2026-03-05
-- Reason: customer_location_id is ambiguous at kontrak level
--         because 1 contract can have units deployed to 
--         MULTIPLE customer locations.
--         
--         Location should be tracked at kontrak_unit level only.
-- ============================================================

-- Safety check: Show contracts that would lose location info
SELECT 
    k.id,
    k.no_kontrak,
    k.customer_location_id as kontrak_location,
    COUNT(ku.id) as unit_count,
    COUNT(DISTINCT ku.customer_location_id) as unit_locations
FROM kontrak k
LEFT JOIN kontrak_unit ku ON k.id = ku.kontrak_id
WHERE k.customer_location_id IS NOT NULL
GROUP BY k.id
LIMIT 10;

-- Show total counts
SELECT 
    COUNT(*) as total_kontrak,
    COUNT(CASE WHEN customer_location_id IS NOT NULL THEN 1 END) as with_location,
    COUNT(CASE WHEN customer_location_id IS NULL THEN 1 END) as without_location
FROM kontrak;

-- ============================================================
-- EXECUTE MIGRATION (Uncomment to run)
-- ============================================================

-- Step 1: Drop the foreign key constraint (if exists)
-- ALTER TABLE kontrak DROP FOREIGN KEY kontrak_customer_location_id_foreign;

-- Step 2: Drop the column
-- ALTER TABLE kontrak DROP COLUMN customer_location_id;

-- Step 3: Verify
-- SHOW COLUMNS FROM kontrak;

-- ============================================================
-- ROLLBACK (if needed)
-- ============================================================

-- If you need to rollback:
-- ALTER TABLE kontrak ADD COLUMN customer_location_id INT UNSIGNED AFTER customer_id;
-- ALTER TABLE kontrak ADD CONSTRAINT kontrak_customer_location_id_foreign 
--     FOREIGN KEY (customer_location_id) REFERENCES customer_locations(id);

-- ============================================================
-- NOTES:
-- - This is a schema change, not data migration
-- - customer_location_id will ONLY exist in kontrak_unit table
-- - kontrak table will have customer_id only (umbrella level)
-- - Each unit in kontrak_unit will have its own customer_location_id
-- ============================================================
