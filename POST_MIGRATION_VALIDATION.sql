-- ============================================================================
-- OPTIMA ERP - Post-Migration Validation Queries
-- ============================================================================
-- Purpose: Validate data integrity after marketing_fix.csv import
-- Date: 2026-02-18
-- Run these queries after executing INSERT_MARKETING_DATA.sql
-- ============================================================================

-- ============================================================================
-- SECTION 1: DATA COUNTS & SUMMARY
-- ============================================================================

SELECT '========== DATA COUNTS ==========' as ' ';

-- Total customers
SELECT 
    'Total Customers' as metric, 
    COUNT(*) as count,
    COUNT(CASE WHEN is_active = 1 THEN 1 END) as active_count
FROM customers;

-- Total locations
SELECT 
    'Total Customer Locations' as metric,
    COUNT(*) as count,
    COUNT(CASE WHEN area_id IS NOT NULL THEN 1 END) as with_area,
    COUNT(CASE WHEN area_id IS NULL THEN 1 END) as without_area
FROM customer_locations;

-- Total contracts
SELECT 
    'Total Contracts' as metric,
    COUNT(*) as count,
    COUNT(CASE WHEN status = 'ACTIVE' THEN 1 END) as active,
    COUNT(CASE WHEN status = 'PENDING' THEN 1 END) as pending,
    COUNT(CASE WHEN status = 'EXPIRED' THEN 1 END) as expired
FROM kontrak;

-- Total units
SELECT 
    'Total Inventory Units' as metric,
    COUNT(*) as total_units,
    COUNT(CASE WHEN customer_id IS NOT NULL THEN 1 END) as assigned_to_customer,
    COUNT(CASE WHEN kontrak_id IS NOT NULL THEN 1 END) as assigned_to_contract,
    COUNT(CASE WHEN harga_sewa_bulanan IS NOT NULL THEN 1 END) as with_monthly_price
FROM inventory_unit;

-- Customer-Contract links
SELECT 
    'Customer-Contract Links' as metric,
    COUNT(*) as total_links,
    COUNT(CASE WHEN is_active = 1 THEN 1 END) as active_links
FROM customer_contracts;


-- ============================================================================
-- SECTION 2: DATA QUALITY CHECKS
-- ============================================================================

SELECT '========== DATA QUALITY CHECKS ==========' as ' ';

-- Check: Customers without locations (should be 0!)
SELECT 
    '❌ Customers without locations' as check_name,
    COUNT(DISTINCT c.id) as violation_count
FROM customers c
LEFT JOIN customer_locations cl ON c.id = cl.customer_id
WHERE cl.id IS NULL AND c.is_active = 1;

-- Show violating customers if any
SELECT 
    c.id,
    c.customer_code,
    c.customer_name,
    '⚠️ NEEDS DEFAULT LOCATION' as issue
FROM customers c
LEFT JOIN customer_locations cl ON c.id = cl.customer_id
WHERE cl.id IS NULL AND c.is_active = 1
LIMIT 10;

-- Check: Locations without area_id (warning only, can be NULL)
SELECT 
    '⚠️ Locations without area assignment' as check_name,
    COUNT(*) as count
FROM customer_locations
WHERE area_id IS NULL;

-- Check: Contracts with 0 units
SELECT 
    '⚠️ Contracts with 0 units' as check_name,
    COUNT(*) as count
FROM kontrak
WHERE total_units = 0 AND status IN ('ACTIVE', 'PENDING');

-- Show contracts with 0 units
SELECT 
    k.id,
    k.no_kontrak,
    k.customer_po_number,
    k.status,
    k.total_units,
    c.customer_name
FROM kontrak k
JOIN customer_locations cl ON k.customer_location_id = cl.id
JOIN customers c ON cl.customer_id = c.id
WHERE k.total_units = 0 AND k.status IN ('ACTIVE', 'PENDING')
ORDER BY k.id DESC
LIMIT 10;

-- Check: Units without price
SELECT 
    '⚠️ Assigned units without monthly price' as check_name,
    COUNT(*) as count
FROM inventory_unit
WHERE customer_id IS NOT NULL 
  AND kontrak_id IS NOT NULL 
  AND harga_sewa_bulanan IS NULL;

-- Check: Units assigned to customer but no contract
SELECT 
    '⚠️ Units with customer but no contract' as check_name,
    COUNT(*) as count
FROM inventory_unit
WHERE customer_id IS NOT NULL AND kontrak_id IS NULL;

-- Check: Orphaned customer_contracts (contract doesn't exist)
SELECT 
    '❌ Orphaned customer_contracts (invalid kontrak_id)' as check_name,
    COUNT(*) as violation_count
FROM customer_contracts cc
LEFT JOIN kontrak k ON cc.kontrak_id = k.id
WHERE k.id IS NULL;

-- Check: Duplicate customer_contracts
SELECT 
    '❌ Duplicate customer_contracts' as check_name,
    COUNT(*) as violation_count
FROM (
    SELECT customer_id, kontrak_id, COUNT(*) as dup_count
    FROM customer_contracts
    GROUP BY customer_id, kontrak_id
    HAVING COUNT(*) > 1
) dups;


-- ============================================================================
-- SECTION 3: REFERENTIAL INTEGRITY CHECKS
-- ============================================================================

SELECT '========== REFERENTIAL INTEGRITY ==========' as ' ';

-- Check: customer_locations → customers
SELECT 
    'customer_locations.customer_id → customers.id' as foreign_key,
    COUNT(*) as violations
FROM customer_locations cl
LEFT JOIN customers c ON cl.customer_id = c.id
WHERE c.id IS NULL;

-- Check: customer_locations → areas
SELECT 
    'customer_locations.area_id → areas.id (NULL OK)' as foreign_key,
    COUNT(*) as violations
FROM customer_locations cl
LEFT JOIN areas a ON cl.area_id = a.id
WHERE cl.area_id IS NOT NULL AND a.id IS NULL;

-- Check: kontrak → customer_locations
SELECT 
    'kontrak.customer_location_id → customer_locations.id' as foreign_key,
    COUNT(*) as violations
FROM kontrak k
LEFT JOIN customer_locations cl ON k.customer_location_id = cl.id
WHERE cl.id IS NULL;

-- Check: inventory_unit → customers
SELECT 
    'inventory_unit.customer_id → customers.id' as foreign_key,
    COUNT(*) as violations
FROM inventory_unit iu
LEFT JOIN customers c ON iu.customer_id = c.id
WHERE iu.customer_id IS NOT NULL AND c.id IS NULL;

-- Check: inventory_unit → kontrak
SELECT 
    'inventory_unit.kontrak_id → kontrak.id' as foreign_key,
    COUNT(*) as violations
FROM inventory_unit iu
LEFT JOIN kontrak k ON iu.kontrak_id = k.id
WHERE iu.kontrak_id IS NOT NULL AND k.id IS NULL;

-- Check: customer_contracts → customers
SELECT 
    'customer_contracts.customer_id → customers.id' as foreign_key,
    COUNT(*) as violations
FROM customer_contracts cc
LEFT JOIN customers c ON cc.customer_id = c.id
WHERE c.id IS NULL;

-- Check: customer_contracts → kontrak
SELECT 
    'customer_contracts.kontrak_id → kontrak.id' as foreign_key,
    COUNT(*) as violations
FROM customer_contracts cc
LEFT JOIN kontrak k ON cc.kontrak_id = k.id
WHERE k.id IS NULL;


-- ============================================================================
-- SECTION 4: BUSINESS LOGIC VALIDATION
-- ============================================================================

SELECT '========== BUSINESS LOGIC CHECKS ==========' as ' ';

-- Check: Units assigned to contract but not in customer_contracts
SELECT 
    '⚠️ Units linked to contract but missing customer_contracts entry' as check_name,
    COUNT(DISTINCT iu.kontrak_id) as affected_contracts
FROM inventory_unit iu
WHERE iu.customer_id IS NOT NULL 
  AND iu.kontrak_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM customer_contracts cc
      WHERE cc.customer_id = iu.customer_id
        AND cc.kontrak_id = iu.kontrak_id
  );

-- Show affected units
SELECT 
    iu.no_unit,
    iu.customer_id,
    iu.kontrak_id,
    c.customer_name,
    k.no_kontrak,
    '⚠️ MISSING CUSTOMER_CONTRACTS LINK' as issue
FROM inventory_unit iu
JOIN customers c ON iu.customer_id = c.id
JOIN kontrak k ON iu.kontrak_id = k.id
WHERE NOT EXISTS (
    SELECT 1 FROM customer_contracts cc
    WHERE cc.customer_id = iu.customer_id
      AND cc.kontrak_id = iu.kontrak_id
)
LIMIT 20;

-- Check: Contract total_units matches actual unit count
SELECT 
    '⚠️ Contracts with mismatched total_units' as check_name,
    COUNT(*) as affected_contracts
FROM (
    SELECT 
        k.id,
        k.total_units as recorded_total,
        COUNT(iu.id_inventory_unit) as actual_total
    FROM kontrak k
    LEFT JOIN inventory_unit iu ON k.id = iu.kontrak_id
    GROUP BY k.id, k.total_units
    HAVING k.total_units != COUNT(iu.id_inventory_unit)
) mismatches;

-- Show mismatched contracts
SELECT 
    k.id as kontrak_id,
    k.no_kontrak,
    k.customer_po_number,
    k.total_units as recorded_units,
    COUNT(iu.id_inventory_unit) as actual_units,
    ABS(k.total_units - COUNT(iu.id_inventory_unit)) as difference
FROM kontrak k
LEFT JOIN inventory_unit iu ON k.id = iu.kontrak_id
GROUP BY k.id, k.no_kontrak, k.customer_po_number, k.total_units
HAVING k.total_units != COUNT(iu.id_inventory_unit)
ORDER BY difference DESC
LIMIT 10;


-- ============================================================================
-- SECTION 5: PRICING ANALYSIS
-- ============================================================================

SELECT '========== PRICING ANALYSIS ==========' as ' ';

-- Price distribution
SELECT 
    'Unit Price Distribution' as analysis,
    MIN(harga_sewa_bulanan) as min_price,
    AVG(harga_sewa_bulanan) as avg_price,
    MAX(harga_sewa_bulanan) as max_price,
    SUM(harga_sewa_bulanan) as total_monthly_revenue
FROM inventory_unit
WHERE harga_sewa_bulanan IS NOT NULL;

-- Units by price range
SELECT 
    CASE 
        WHEN harga_sewa_bulanan < 5000000 THEN '< 5M'
        WHEN harga_sewa_bulanan < 10000000 THEN '5M - 10M'
        WHEN harga_sewa_bulanan < 15000000 THEN '10M - 15M'
        WHEN harga_sewa_bulanan < 20000000 THEN '15M - 20M'
        WHEN harga_sewa_bulanan >= 20000000 THEN '>= 20M'
        ELSE 'NULL'
    END as price_range,
    COUNT(*) as unit_count,
    SUM(harga_sewa_bulanan) as revenue_in_range
FROM inventory_unit
GROUP BY 
    CASE 
        WHEN harga_sewa_bulanan < 5000000 THEN '< 5M'
        WHEN harga_sewa_bulanan < 10000000 THEN '5M - 10M'
        WHEN harga_sewa_bulanan < 15000000 THEN '10M - 15M'
        WHEN harga_sewa_bulanan < 20000000 THEN '15M - 20M'
        WHEN harga_sewa_bulanan >= 20000000 THEN '>= 20M'
        ELSE 'NULL'
    END
ORDER BY 
    CASE price_range
        WHEN '< 5M' THEN 1
        WHEN '5M - 10M' THEN 2
        WHEN '10M - 15M' THEN 3
        WHEN '15M - 20M' THEN 4
        WHEN '>= 20M' THEN 5
        ELSE 6
    END;

-- Contract value vs sum of unit prices
SELECT 
    k.id,
    k.no_kontrak,
    k.nilai_total as contract_value,
    SUM(iu.harga_sewa_bulanan) as sum_of_unit_prices,
    k.nilai_total - SUM(iu.harga_sewa_bulanan) as difference,
    ROUND((k.nilai_total - SUM(iu.harga_sewa_bulanan)) / NULLIF(k.nilai_total, 0) * 100, 2) as pct_difference
FROM kontrak k
JOIN inventory_unit iu ON k.id = iu.kontrak_id
WHERE k.nilai_total IS NOT NULL
GROUP BY k.id, k.no_kontrak, k.nilai_total
HAVING ABS(k.nilai_total - SUM(iu.harga_sewa_bulanan)) > 100000  -- difference > 100K
ORDER BY ABS(difference) DESC
LIMIT 20;


-- ============================================================================
-- SECTION 6: TOP CUSTOMERS & CONTRACTS
-- ============================================================================

SELECT '========== TOP CUSTOMERS & CONTRACTS ==========' as ' ';

-- Top 10 customers by unit count
SELECT 
    c.id,
    c.customer_code,
    c.customer_name,
    COUNT(DISTINCT iu.id_inventory_unit) as total_units,
    COUNT(DISTINCT k.id) as total_contracts,
    SUM(iu.harga_sewa_bulanan) as monthly_revenue
FROM customers c
JOIN inventory_unit iu ON c.id = iu.customer_id
LEFT JOIN kontrak k ON iu.kontrak_id = k.id
WHERE iu.customer_id IS NOT NULL
GROUP BY c.id, c.customer_code, c.customer_name
ORDER BY total_units DESC
LIMIT 10;

-- Top 10 contracts by unit count
SELECT 
    k.id,
    k.no_kontrak,
    k.customer_po_number,
    k.status,
    c.customer_name,
    k.total_units,
    COUNT(iu.id_inventory_unit) as actual_units,
    SUM(iu.harga_sewa_bulanan) as monthly_revenue
FROM kontrak k
JOIN customer_locations cl ON k.customer_location_id = cl.id
JOIN customers c ON cl.customer_id = c.id
LEFT JOIN inventory_unit iu ON k.id = iu.kontrak_id
GROUP BY k.id, k.no_kontrak, k.customer_po_number, k.status, c.customer_name, k.total_units
ORDER BY actual_units DESC
LIMIT 10;

-- Contracts expiring within 30 days
SELECT 
    k.id,
    k.no_kontrak,
    k.customer_po_number,
    c.customer_name,
    k.tanggal_berakhir,
    DATEDIFF(k.tanggal_berakhir, CURDATE()) as days_until_expiry,
    k.total_units
FROM kontrak k
JOIN customer_locations cl ON k.customer_location_id = cl.id
JOIN customers c ON cl.customer_id = c.id
WHERE k.status = 'ACTIVE'
  AND k.tanggal_berakhir BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
ORDER BY k.tanggal_berakhir ASC;


-- ============================================================================
-- SECTION 7: AREA DISTRIBUTION
-- ============================================================================

SELECT '========== AREA DISTRIBUTION ==========' as ' ';

-- Units per area
SELECT 
    a.area_name,
    a.area_code,
    COUNT(DISTINCT cl.id) as total_locations,
    COUNT(DISTINCT iu.id_inventory_unit) as total_units,
    COUNT(DISTINCT c.id) as total_customers,
    SUM(iu.harga_sewa_bulanan) as monthly_revenue
FROM areas a
LEFT JOIN customer_locations cl ON a.id = cl.area_id
LEFT JOIN customers c ON cl.customer_id = c.id
LEFT JOIN inventory_unit iu ON cl.id = iu.customer_location_id
WHERE a.is_active = 1
GROUP BY a.id, a.area_name, a.area_code
ORDER BY total_units DESC;


-- ============================================================================
-- SECTION 8: RECOMMENDED ACTIONS
-- ============================================================================

SELECT '========== RECOMMENDED ACTIONS ==========' as ' ';

-- Action 1: Fix contract total_units
SELECT 
    'ACTION 1: Update contract total_units' as action,
    'Run: UPDATE kontrak k SET total_units = (SELECT COUNT(*) FROM inventory_unit WHERE kontrak_id = k.id)' as sql_to_run;

-- Action 2: Create missing customer_contracts
SELECT 
    'ACTION 2: Create missing customer_contracts links' as action,
    'Run: INSERT INTO customer_contracts (customer_id, kontrak_id) SELECT DISTINCT customer_id, kontrak_id FROM inventory_unit WHERE customer_id IS NOT NULL AND kontrak_id IS NOT NULL AND NOT EXISTS (SELECT 1 FROM customer_contracts WHERE ...)' as sql_to_run;

-- Action 3: Review pending contracts
SELECT 
    'ACTION 3: Review and activate pending contracts' as action,
    CONCAT('Found ', COUNT(*), ' contracts with status PENDING that need review') as details
FROM kontrak
WHERE status = 'PENDING';

-- Action 4: Fill missing prices
SELECT 
    'ACTION 4: Fill missing unit prices' as action,
    CONCAT('Found ', COUNT(*), ' assigned units without monthly price') as details
FROM inventory_unit
WHERE customer_id IS NOT NULL AND harga_sewa_bulanan IS NULL;


-- ============================================================================
-- FINAL SUMMARY
-- ============================================================================

SELECT '========== MIGRATION SUCCESS SUMMARY ==========' as ' ';

SELECT 
    'DATA ANALYSIS' as metric,
    'BEFORE' as timing,
    239 as customers,
    468 as locations,
    363 as contracts,
    1195 as assigned_units,
    '24.0%' as assignment_rate
UNION ALL
SELECT 
    'CSV INPUT' as metric,
    'INPUT' as timing,
    NULL as customers,
    NULL as locations,
    NULL as contracts,
    1939 as assigned_units,
    'CSV rows' as assignment_rate
UNION ALL
SELECT 
    'OVERLAP' as metric,
    'ANALYSIS' as timing,
    NULL as customers,
    NULL as locations,
    NULL as contracts,
    947 as assigned_units,
    'Updated' as assignment_rate
UNION ALL
SELECT 
    'NEW ASSIGNMENTS' as metric,
    'FROM CSV' as timing,
    NULL as customers,
    NULL as locations,
    NULL as contracts,
    992 as assigned_units,
    'New' as assignment_rate
UNION ALL
SELECT 
    'AFTER IMPORT' as metric,
    'EXPECTED' as timing,
    (SELECT COUNT(*) FROM customers) as customers,
    (SELECT COUNT(*) FROM customer_locations) as locations,
    (SELECT COUNT(*) FROM kontrak) as contracts,
    2187 as assigned_units,
    '43.8%' as assignment_rate;

-- Explanation
SELECT '
OVERLAP ANALYSIS:
- CSV contains 1,939 units
- Existing DB has 1,195 assigned units  
- OVERLAP: 947 units (exist in BOTH CSV and DB, will be UPDATED)
- NEW: 992 units (only in CSV, will be NEW assignments)
- KEEP: 248 units (only in DB, remain unchanged)
- TOTAL AFTER: 947 + 992 + 248 = 2,187 assigned units (44%)
' as overlap_explanation;

-- ============================================================================
-- END OF VALIDATION SCRIPT
-- ============================================================================
-- Review all sections above for any violations or warnings
-- Address critical issues (marked ❌) before deploying to production
-- Review warnings (marked ⚠️) and decide if action is needed
-- ============================================================================
