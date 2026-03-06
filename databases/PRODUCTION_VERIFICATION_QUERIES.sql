-- =====================================================
-- PRODUCTION DATABASE VERIFICATION QUERIES
-- Compare optima_ci (development) vs u138256737_optima_db (production)
-- Date: March 6, 2026
-- =====================================================

-- ===== STEP 1: Check Table Counts =====

-- Development (run locally)
SELECT 'DEV' as env, COUNT(*) as table_count 
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'optima_ci';

-- Production (run via phpMyAdmin: https://auth-db1866.hstgr.io)
SELECT 'PROD' as env, COUNT(*) as table_count 
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'u138256737_optima_db';

-- Expected: Both should have ~138 tables


-- ===== STEP 2: Check Recent Schema Changes =====

-- 2.1 Check if kontrak still has customer_location_id (should be REMOVED)
-- Production:
SELECT COLUMN_NAME 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'u138256737_optima_db' 
  AND TABLE_NAME = 'kontrak' 
  AND COLUMN_NAME = 'customer_location_id';
-- Expected: EMPTY (column should NOT exist)

-- 2.2 Check if kontrak_unit has customer_location_id (should be ADDED)
-- Production:
SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'u138256737_optima_db' 
  AND TABLE_NAME = 'kontrak_unit' 
  AND COLUMN_NAME = 'customer_location_id';
-- Expected: customer_location_id | int | YES

-- 2.3 Check if kontrak_unit has is_spare column
-- Production:
SELECT COLUMN_NAME, COLUMN_TYPE, COLUMN_DEFAULT
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'u138256737_optima_db' 
  AND TABLE_NAME = 'kontrak_unit' 
  AND COLUMN_NAME = 'is_spare';
-- Expected: is_spare | tinyint(1) | 0

-- 2.4 Check if unit_audit_requests table exists (NEW)
-- Production:
SELECT TABLE_NAME 
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'u138256737_optima_db' 
  AND TABLE_NAME = 'unit_audit_requests';
-- Expected: unit_audit_requests (or EMPTY if not yet migrated)

-- 2.5 Check if unit_movements table exists (NEW)
-- Production:
SELECT TABLE_NAME 
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'u138256737_optima_db' 
  AND TABLE_NAME = 'unit_movements';
-- Expected: unit_movements (or EMPTY if not yet migrated)


-- ===== STEP 3: Check Critical Data Counts =====

-- Production (run all these):
SELECT 'customers' as tbl, COUNT(*) as count FROM customers
UNION ALL
SELECT 'customer_locations', COUNT(*) FROM customer_locations
UNION ALL
SELECT 'kontrak', COUNT(*) FROM kontrak
UNION ALL
SELECT 'kontrak_unit', COUNT(*) FROM kontrak_unit
UNION ALL
SELECT 'inventory_unit', COUNT(*) FROM inventory_unit
UNION ALL
SELECT 'users', COUNT(*) FROM users;

-- Expected (from development):
-- customers: 246
-- customer_locations: 399
-- kontrak: 676
-- kontrak_unit: 1992
-- inventory_unit: 4989
-- users: 4+ (production might have more)


-- ===== STEP 4: Check Data Integrity =====

-- 4.1 Check for orphaned kontrak_unit records
-- Production:
SELECT COUNT(*) as orphaned_units
FROM kontrak_unit ku
LEFT JOIN inventory_unit iu ON ku.unit_id = iu.id_inventory_unit
WHERE iu.id_inventory_unit IS NULL;
-- Expected: 0

-- 4.2 Check for orphaned customer_location references
-- Production:
SELECT COUNT(*) as orphaned_locations
FROM kontrak_unit ku
LEFT JOIN customer_locations cl ON ku.customer_location_id = cl.id
WHERE ku.customer_location_id IS NOT NULL AND cl.id IS NULL;
-- Expected: 0

-- 4.3 Check kontrak_unit WITHOUT customer_location_id
-- Production:
SELECT COUNT(*) as units_without_location
FROM kontrak_unit
WHERE customer_location_id IS NULL;
-- Expected: 0 (all should have location) or HIGH number if not migrated yet

-- 4.4 Check contract status distribution
-- Production:
SELECT status, COUNT(*) as count
FROM kontrak
GROUP BY status;
-- Expected: ACTIVE ~583, PENDING ~93


-- ===== STEP 5: Compare Schema Structures =====

-- 5.1 Get kontrak table structure (Production)
DESCRIBE kontrak;
-- Compare with development

-- 5.2 Get kontrak_unit table structure (Production)
DESCRIBE kontrak_unit;
-- Compare with development - should have these NEW columns:
-- - customer_location_id (int, nullable)
-- - is_spare (tinyint(1), default 0)
-- - harga_sewa (decimal(15,2), nullable)

-- 5.3 Get unit_audit_requests structure (if exists)
DESCRIBE unit_audit_requests;

-- 5.4 Get unit_movements structure (if exists)
DESCRIBE unit_movements;


-- ===== STEP 6: Check Foreign Keys =====

-- Production:
SELECT 
    TABLE_NAME,
    COLUMN_NAME,
    CONSTRAINT_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'u138256737_optima_db'
  AND TABLE_NAME IN ('kontrak', 'kontrak_unit', 'unit_audit_requests', 'unit_movements')
  AND REFERENCED_TABLE_NAME IS NOT NULL
ORDER BY TABLE_NAME, COLUMN_NAME;


-- ===== STEP 7: Check Indexes =====

-- Production:
SELECT 
    TABLE_NAME,
    INDEX_NAME,
    COLUMN_NAME,
    NON_UNIQUE
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = 'u138256737_optima_db'
  AND TABLE_NAME = 'kontrak_unit'
ORDER BY TABLE_NAME, INDEX_NAME, SEQ_IN_INDEX;


-- ===== STEP 8: Sample Data Comparison =====

-- 8.1 Check first 5 contracts (Production)
SELECT id, no_kontrak, customer_id, status, tanggal_mulai, tanggal_berakhir
FROM kontrak
ORDER BY id ASC
LIMIT 5;

-- 8.2 Check kontrak_unit with customer_location_id (Production)
SELECT 
    ku.id,
    ku.kontrak_id,
    ku.unit_id,
    ku.customer_location_id,
    ku.is_spare,
    ku.status,
    cl.location_name
FROM kontrak_unit ku
LEFT JOIN customer_locations cl ON cl.id = ku.customer_location_id
WHERE ku.customer_location_id IS NOT NULL
LIMIT 10;


-- =====================================================
-- VERIFICATION SUMMARY CHECKLIST
-- =====================================================

/*
Run these checks and verify:

SCHEMA CHANGES (March 5, 2026):
[ ] kontrak.customer_location_id removed
[ ] kontrak_unit.customer_location_id added
[ ] kontrak_unit.is_spare added
[ ] kontrak_unit.harga_sewa exists
[ ] unit_audit_requests table exists (optional - new feature)
[ ] unit_movements table exists (optional - new feature)

DATA INTEGRITY:
[ ] No orphaned kontrak_unit records (0)
[ ] No orphaned customer_location references (0)
[ ] All kontrak_unit have customer_location_id populated (1992/1992)
[ ] Contract counts match (~676)
[ ] Unit counts match (~4989)

CODE CHANGES (March 5-6, 2026):
[ ] Kontrak.php updated (removed customer_location_id references)
[ ] Marketing.php updated (getActiveContracts fixed)
[ ] kontrak_edit.php updated (customer disabled, location removed, financials readonly)
[ ] kontrak_detail.php updated (Overview tab default, currency formatting)
[ ] add_unit_modal.php updated (English translation, all units shown)

If any of these checks FAIL:
- Production needs migration files executed
- Data needs to be populated
- Code needs to be uploaded
*/
