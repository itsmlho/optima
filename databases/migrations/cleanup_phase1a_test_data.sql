-- =====================================================
-- Phase 1A: Test Data Cleanup Script
-- =====================================================
-- 
-- This script removes all test data created by create_phase1a_test_data.sql
-- 
-- Usage:
--   mysql -u root -p optima_staging < cleanup_phase1a_test_data.sql
--
-- WARNING: This will permanently delete all TEST-* records!
--
-- Created: 2026-03-04
-- =====================================================

USE optima_staging;

START TRANSACTION;

-- Display what will be deleted
SELECT '=== TEST DATA TO BE DELETED ===' as '';

SELECT 'Contract-Unit Assignments' as item, COUNT(*) as count
FROM kontrak_unit ku
INNER JOIN inventory_unit iu ON iu.id_inventory_unit = ku.unit_id
WHERE iu.nomor_mesin LIKE 'TEST-UNIT-%';

SELECT 'Test Contracts' as item, COUNT(*) as count
FROM kontrak WHERE nomor_kontrak LIKE 'TEST-K-%';

SELECT 'Test Units' as item, COUNT(*) as count
FROM inventory_unit WHERE nomor_mesin LIKE 'TEST-UNIT-%';

SELECT 'Test Customer Locations' as item, COUNT(*) as count
FROM customer_locations WHERE nama_area LIKE '%Test%';

SELECT 'Test Customers' as item, COUNT(*) as count
FROM customers WHERE nama_customer LIKE 'PT Test%';

-- Disable FK checks temporarily
SET FOREIGN_KEY_CHECKS = 0;

-- Delete in reverse order of creation to respect dependencies

-- 1. Delete contract-unit assignments for test units
DELETE ku FROM kontrak_unit ku
INNER JOIN inventory_unit iu ON iu.id_inventory_unit = ku.unit_id
WHERE iu.nomor_mesin LIKE 'TEST-UNIT-%';

SELECT 'Deleted contract-unit assignments' as status;

-- 2. Delete test contracts
DELETE FROM kontrak WHERE nomor_kontrak LIKE 'TEST-K-%';

SELECT 'Deleted test contracts' as status;

-- 3. Delete test inventory units
DELETE FROM inventory_unit WHERE nomor_mesin LIKE 'TEST-UNIT-%';

SELECT 'Deleted test inventory units' as status;

-- 4. Delete test customer locations
DELETE FROM customer_locations WHERE nama_area LIKE '%Test%';

SELECT 'Deleted test customer locations' as status;

-- 5. Delete test customers
DELETE FROM customers WHERE nama_customer LIKE 'PT Test%';

SELECT 'Deleted test customers' as status;

-- 6. Delete test brand if it was created
DELETE FROM brand WHERE nama_brand = 'TEST BRAND';

SELECT 'Deleted test brand (if existed)' as status;

-- Re-enable FK checks
SET FOREIGN_KEY_CHECKS = 1;

COMMIT;

SELECT '=== TEST DATA CLEANUP COMPLETE ===' as '';
SELECT 'All TEST-* records have been removed from the database.' as result;
