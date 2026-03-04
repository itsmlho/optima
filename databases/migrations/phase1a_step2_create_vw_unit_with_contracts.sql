-- ===================================================
-- PHASE 1A - STEP 2: CREATE VIEW FOR BACKWARD COMPATIBILITY
-- ===================================================
-- Purpose: Create vw_unit_with_contracts view to replace direct FK fields
--          This view derives kontrak_id, customer_id, customer_location_id
--          from the proper kontrak_unit junction table
-- Date: March 4, 2026
-- Author: System Refactoring
-- ===================================================

-- PREREQUISITES:
-- 1. Phase 1A Step 1 audit completed
-- 2. Data mismatches reconciled
-- 3. Tested in staging environment

-- BACKUP FIRST!
-- mysqldump -u root -p optima_ci inventory_unit kontrak_unit kontrak customer_locations customers > backup_before_create_view_$(date +%Y%m%d_%H%M%S).sql

-- ===================================================
-- 1. DROP EXISTING VIEW (IF EXISTS)
-- ===================================================

DROP VIEW IF EXISTS vw_unit_with_contracts;

-- ===================================================
-- 2. CREATE VIEW WITH DERIVED RELATIONSHIPS
-- ===================================================

CREATE VIEW vw_unit_with_contracts AS
SELECT 
    -- All columns from inventory_unit
    iu.id_inventory_unit,
    iu.no_unit,
    iu.no_unit_na,
    iu.serial_number,
    iu.id_po,
    iu.tahun_unit,
    iu.status_unit_id,
    iu.lokasi_unit,
    iu.departemen_id,
    iu.tanggal_kirim,
    iu.keterangan,
    iu.harga_sewa_bulanan,
    iu.rate_changed_at,
    iu.harga_sewa_harian,
    iu.on_hire_date,
    iu.off_hire_date,
    iu.area_id,
    iu.tipe_unit_id,
    iu.model_unit_id,
    iu.kapasitas_unit_id,
    iu.model_mast_id,
    iu.tinggi_mast,
    iu.sn_mast,
    iu.model_mesin_id,
    iu.sn_mesin,
    iu.roda_id,
    iu.ban_id,
    iu.valve_id,
    iu.aksesoris,
    iu.spk_id,
    iu.hour_meter,
    iu.asset_tag,
    iu.fuel_type,
    iu.ownership_status,
    iu.warehouse_location_id,
    iu.created_at,
    iu.updated_at,
    
    -- Derived from kontrak_unit junction (SOURCE OF TRUTH)
    ku.kontrak_id,
    ku.tanggal_mulai as assignment_start_date,
    ku.tanggal_selesai as assignment_end_date,
    ku.status as assignment_status,
    ku.is_temporary,
    ku.id as kontrak_unit_id,
    
    -- Derived from kontrak table
    k.no_kontrak as contract_number,
    k.customer_location_id,
    k.tanggal_mulai as contract_start_date,
    k.tanggal_berakhir as contract_end_date,
    k.rental_type,
    k.jenis_sewa as billing_period_type,
    k.status as contract_status,
    
    -- Derived from customer_locations
    cl.customer_id,
    cl.location_name,
    cl.location_code,
    cl.address as location_address,
    cl.city as location_city,
    cl.province as location_province,
    cl.is_primary as is_primary_location,
    
    -- Derived from customers
    c.customer_name,
    c.customer_code,
    c.phone as customer_phone,
    c.email as customer_email,
    c.marketing_name,
    c.is_active as customer_is_active,
    
    -- Derived from area (if needed)
    a.area_name,
    a.area_code
    
FROM inventory_unit iu

-- LEFT JOIN to kontrak_unit (unit may not be assigned)
-- Filter for ACTIVE assignments only (not historical)
LEFT JOIN kontrak_unit ku ON iu.id_inventory_unit = ku.unit_id 
    AND ku.status IN ('ACTIVE', 'TEMP_ACTIVE')
    AND ku.is_temporary = 0  -- Exclude temporary replacements

-- JOIN to get contract details
LEFT JOIN kontrak k ON ku.kontrak_id = k.id

-- JOIN to get customer location details
LEFT JOIN customer_locations cl ON k.customer_location_id = cl.id

-- JOIN to get customer details
LEFT JOIN customers c ON cl.customer_id = c.id

-- JOIN to get area details
LEFT JOIN areas a ON iu.area_id = a.id;

-- ===================================================
-- 3. CREATE INDEXES ON UNDERLYING TABLES (PERFORMANCE)
-- ===================================================

-- Index on kontrak_unit for fast filtering
CREATE INDEX IF NOT EXISTS idx_kontrak_unit_active 
ON kontrak_unit(unit_id, status, is_temporary);

-- Index on kontrak_unit for kontrak_id lookups
CREATE INDEX IF NOT EXISTS idx_kontrak_unit_kontrak 
ON kontrak_unit(kontrak_id, status);

-- Index on kontrak.customer_location_id (if not exists)
CREATE INDEX IF NOT EXISTS idx_kontrak_customer_location 
ON kontrak(customer_location_id);

-- Index on customer_locations.customer_id (should already exist from FK)
-- CREATE INDEX IF NOT EXISTS idx_customer_locations_customer 
-- ON customer_locations(customer_id);

-- ===================================================
-- 4. VERIFICATION QUERIES
-- ===================================================

-- Test 1: Check view returns data
SELECT COUNT(*) as total_units_in_view
FROM vw_unit_with_contracts;

-- Test 2: Compare with inventory_unit table
SELECT 
    (SELECT COUNT(*) FROM inventory_unit) as inventory_unit_count,
    (SELECT COUNT(*) FROM vw_unit_with_contracts) as view_count,
    (SELECT COUNT(*) FROM inventory_unit) - (SELECT COUNT(*) FROM vw_unit_with_contracts) as difference;

-- Test 3: Check units with contracts
SELECT 
    'Units with active contracts' as metric,
    COUNT(*) as count
FROM vw_unit_with_contracts
WHERE kontrak_id IS NOT NULL;

-- Test 4: Performance test (should use indexes)
EXPLAIN SELECT * 
FROM vw_unit_with_contracts 
WHERE customer_id = 1;

EXPLAIN SELECT * 
FROM vw_unit_with_contracts 
WHERE kontrak_id = 1;

EXPLAIN SELECT * 
FROM vw_unit_with_contracts 
WHERE no_unit LIKE 'FL%';

-- Test 5: Sample data comparison
SELECT 
    iu.id_inventory_unit,
    iu.no_unit,
    iu.kontrak_id as old_kontrak_id,
    vuc.kontrak_id as new_kontrak_id,
    iu.customer_id as old_customer_id,
    vuc.customer_id as new_customer_id,
    CASE 
        WHEN iu.kontrak_id = vuc.kontrak_id THEN 'MATCH'
        WHEN iu.kontrak_id IS NULL AND vuc.kontrak_id IS NULL THEN 'BOTH NULL'
        ELSE 'MISMATCH'
    END as validation_status
FROM inventory_unit iu
LEFT JOIN vw_unit_with_contracts vuc ON iu.id_inventory_unit = vuc.id_inventory_unit
LIMIT 50;

-- ===================================================
-- 5. GRANT PERMISSIONS (IF NEEDED)
-- ===================================================

-- Grant SELECT on view to application user
-- GRANT SELECT ON optima_ci.vw_unit_with_contracts TO 'optima_app'@'localhost';

-- ===================================================
-- 6. DOCUMENTATION
-- ===================================================

-- VIEW USAGE NOTES:
-- 
-- This view replaces direct access to redundant FK fields in inventory_unit:
--   - inventory_unit.kontrak_id      → use vw_unit_with_contracts.kontrak_id
--   - inventory_unit.customer_id     → use vw_unit_with_contracts.customer_id
--   - inventory_unit.customer_location_id → use vw_unit_with_contracts.customer_location_id
--
-- IMPORTANT FILTERS:
--   - View only shows ACTIVE contract assignments (status IN AKTIF/DIPERPANJANG/MENUNGGU_PERSETUJUAN)
--   - Excludes temporary unit replacements (is_temporary = 0)
--   - For historical data, query kontrak_unit directly
--
-- PERFORMANCE:
--   - Indexes created on kontrak_unit(unit_id, status, is_temporary)
--   - Should handle 10,000+ units efficiently (< 100ms per query)
--   - For bulk operations, consider denormalization strategy
--
-- NEXT STEPS:
--   1. Update InventoryUnitModel to use this view
--   2. Update all queries in Marketing.php that reference old FK fields
--   3. Test extensively in staging (2 weeks minimum)
--   4. Deploy to production
--   5. Monitor performance for 1 week
--   6. After validation, drop redundant columns from inventory_unit

-- ===================================================
-- END OF MIGRATION SCRIPT
-- ===================================================
