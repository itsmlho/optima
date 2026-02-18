-- ========================================
-- FIX CUSTOMER NAMES FOR ACCOUNTING DATA MATCH
-- ========================================
-- Purpose: Update customer names to match data_from_acc.csv exactly
-- Date: 2026-02-18
-- Expected Result: 110 missing units → 0 missing units
-- NOTE: Select database 'u138256737_optima_db' in PHPMyAdmin before running

-- ========================================
-- STEP 1: UPDATE EXISTING CUSTOMERS
-- ========================================

-- 1. PT Indah Kiat Pulp & Paper (Remove "Tbk")
-- Affected: ~90 units from accounting
UPDATE customers 
SET customer_name = 'PT Indah Kiat Pulp & Paper',
    updated_at = NOW()
WHERE id = 45 AND customer_name = 'PT Indah Kiat Pulp & Paper Tbk';

SELECT '✅ Updated: PT Indah Kiat Pulp & Paper (removed Tbk)' as status;

-- 2. PURINUSA EKA PERSADA (Remove "PT", fix spacing)
-- Affected: 1 unit (3641)
UPDATE customers 
SET customer_name = 'PURINUSA EKA PERSADA',
    updated_at = NOW()
WHERE id = 81 AND customer_name = 'PT Purinusa Ekapersada';

SELECT '✅ Updated: PURINUSA EKA PERSADA (removed PT, fixed spacing)' as status;

-- 3. PT KALDU SARI NABATI (Remove "Indonesia")
-- Affected: 3 units (3, 3464, 3481)
UPDATE customers 
SET customer_name = 'PT KALDU SARI NABATI',
    updated_at = NOW()
WHERE id = 55 AND customer_name = 'PT Kaldu Sari Nabati Indonesia';

SELECT '✅ Updated: PT KALDU SARI NABATI (removed Indonesia)' as status;

-- 4. PT Indofood CBP Sukses (Remove "Makmur")
-- Affected: 1 unit (3858)
UPDATE customers 
SET customer_name = 'PT Indofood CBP Sukses',
    updated_at = NOW()
WHERE id = 49 AND customer_name = 'PT Indofood CBP Sukses Makmur';

SELECT '✅ Updated: PT Indofood CBP Sukses (removed Makmur)' as status;

-- ========================================
-- STEP 2: INSERT MISSING CUSTOMERS
-- ========================================

-- 5. PT Galvanis Tri Lestari (NEW)
-- Affected: 2 units (3158, 3736)
INSERT INTO customers (
    customer_code,
    customer_name,
    is_active,
    created_at,
    updated_at
) VALUES (
    'CUST-GALVANIS',
    'PT Galvanis Tri Lestari',
    1,
    NOW(),
    NOW()
);

SELECT '✅ Inserted: PT Galvanis Tri Lestari' as status, LAST_INSERT_ID() as customer_id;

-- 6. HISYS ENGINEERING INDONESIA (NEW)
-- Affected: 1 unit (3209)
INSERT INTO customers (
    customer_code,
    customer_name,
    is_active,
    created_at,
    updated_at
) VALUES (
    'CUST-HISYS',
    'HISYS ENGINEERING INDONESIA',
    1,
    NOW(),
    NOW()
);

SELECT '✅ Inserted: HISYS ENGINEERING INDONESIA' as status, LAST_INSERT_ID() as customer_id;

-- ========================================
-- VERIFICATION
-- ========================================

SELECT '========================================' as '';
SELECT 'VERIFICATION - Updated Customer Names' as '';
SELECT '========================================' as '';

SELECT id, customer_name, updated_at
FROM customers
WHERE id IN (45, 49, 55, 81)
   OR customer_name IN ('PT Galvanis Tri Lestari', 'HISYS ENGINEERING INDONESIA')
ORDER BY id;

-- ========================================
-- CUSTOMER NAMES FIXED!
-- ========================================

SELECT '✅ Customer names fixed! Ready for next step.' as result;
