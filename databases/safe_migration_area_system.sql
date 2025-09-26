-- ====================================================================
-- SAFE MIGRATION SCRIPT - AREA BASED STAFF SYSTEM
-- ====================================================================
-- This script safely migrates existing data to new area-based system
-- Run each section carefully and verify results before proceeding
-- ====================================================================

-- STEP 1: Create backup tables
-- ====================================================================
CREATE TABLE backup_kontrak_original AS SELECT * FROM kontrak;
CREATE TABLE backup_work_order_staff_original AS SELECT * FROM work_order_staff;
CREATE TABLE backup_inventory_unit_original AS SELECT * FROM inventory_unit;

-- STEP 2: Check current data before migration
-- ====================================================================
SELECT 'CURRENT KONTRAK DATA' as info;
SELECT COUNT(*) as total_kontrak, 
       COUNT(CASE WHEN status = 'Aktif' THEN 1 END) as aktif_count,
       COUNT(DISTINCT pelanggan) as unique_customers,
       COUNT(DISTINCT lokasi) as unique_locations
FROM kontrak;

SELECT 'CURRENT WORK ORDER STAFF' as info;
SELECT staff_role, COUNT(*) as count 
FROM work_order_staff 
WHERE is_active = 1 
GROUP BY staff_role;

SELECT 'CURRENT INVENTORY UNIT' as info;
SELECT COUNT(*) as total_units,
       COUNT(DISTINCT kontrak_id) as linked_kontrak
FROM inventory_unit 
WHERE is_active = 1;

-- STEP 3: Create new table structure (from main design file)
-- ====================================================================
-- Run: source area_based_staff_system_design.sql
-- Or copy the CREATE TABLE statements from that file

-- STEP 4: Verify new tables created
-- ====================================================================
SHOW TABLES LIKE '%area%';
SHOW TABLES LIKE '%customer%';
SHOW TABLES LIKE 'staff';

-- STEP 5: Sample data for areas (customize based on your regions)
-- ====================================================================
INSERT INTO areas (area_code, area_name, area_description) VALUES
('JKT-A', 'Jakarta Utara & Sunter', 'Meliputi Jakarta Utara, Sunter, Kemayoran'),
('JKT-B', 'Jakarta Selatan & Timur', 'Meliputi Jakarta Selatan, Jakarta Timur'),  
('BKS-A', 'Bekasi & Cikarang', 'Meliputi Bekasi, Cikarang, Cibitung'),
('TNG-A', 'Tangerang & Sekitar', 'Meliputi Tangerang, Tangerang Selatan');

SELECT 'AREAS CREATED:' as info, area_code, area_name FROM areas;

-- STEP 6: Migrate customers with intelligent area assignment
-- ====================================================================

-- First, let's see what locations we have in kontrak
SELECT DISTINCT lokasi FROM kontrak WHERE status = 'Aktif' ORDER BY lokasi;

-- Smart migration based on location patterns
INSERT INTO customers (customer_code, customer_name, area_id, primary_address, city, province, pic_name, pic_phone, contract_type, is_active)
SELECT 
    CONCAT('CUST', LPAD(k.id, 3, '0')) as customer_code,
    k.pelanggan as customer_name,
    CASE 
        WHEN k.lokasi LIKE '%Jakarta Utara%' OR k.lokasi LIKE '%Sunter%' OR k.lokasi LIKE '%Kemayoran%' 
        THEN (SELECT id FROM areas WHERE area_code = 'JKT-A')
        
        WHEN k.lokasi LIKE '%Bekasi%' OR k.lokasi LIKE '%Cikarang%' OR k.lokasi LIKE '%Cibitung%'
        THEN (SELECT id FROM areas WHERE area_code = 'BKS-A')
        
        WHEN k.lokasi LIKE '%Tangerang%'
        THEN (SELECT id FROM areas WHERE area_code = 'TNG-A')
        
        ELSE (SELECT id FROM areas WHERE area_code = 'JKT-B') -- Default
    END as area_id,
    k.lokasi as primary_address,
    CASE 
        WHEN k.lokasi LIKE '%Jakarta%' THEN 'Jakarta'
        WHEN k.lokasi LIKE '%Bekasi%' THEN 'Bekasi' 
        WHEN k.lokasi LIKE '%Tangerang%' THEN 'Tangerang'
        ELSE 'Jakarta'
    END as city,
    'DKI Jakarta' as province,
    COALESCE(k.pic, 'TBA') as pic_name,
    k.kontak as pic_phone,
    CASE 
        WHEN k.jenis_sewa = 'HARIAN' THEN 'RENTAL_HARIAN'
        WHEN k.jenis_sewa = 'BULANAN' THEN 'RENTAL_BULANAN'
        ELSE 'RENTAL_BULANAN'
    END as contract_type,
    CASE WHEN k.status = 'Aktif' THEN 1 ELSE 0 END as is_active
FROM kontrak k
WHERE k.pelanggan IS NOT NULL AND k.pelanggan != '';

-- Verify customer migration
SELECT 'CUSTOMERS MIGRATED:' as info;
SELECT c.customer_name, a.area_name, c.city, c.is_active
FROM customers c 
JOIN areas a ON c.area_id = a.id
ORDER BY a.area_code, c.customer_name;

-- STEP 7: Create customer locations
-- ====================================================================
INSERT INTO customer_locations (customer_id, location_name, address, city, province, is_primary, is_active)
SELECT 
    c.id as customer_id,
    CASE 
        WHEN c.primary_address LIKE '%Kantor%' THEN 'Kantor Utama'
        WHEN c.primary_address LIKE '%Pabrik%' THEN 'Pabrik'
        WHEN c.primary_address LIKE '%Gudang%' THEN 'Gudang'
        ELSE 'Lokasi Utama'
    END as location_name,
    c.primary_address as address,
    c.city,
    c.province,
    1 as is_primary,
    c.is_active
FROM customers c;

SELECT 'CUSTOMER LOCATIONS CREATED:' as info, COUNT(*) as total FROM customer_locations;

-- STEP 8: Create customer_contracts relationship 
-- ====================================================================
-- Link customers to their original kontrak records
INSERT INTO customer_contracts (customer_id, kontrak_id, is_active)
SELECT 
    c.id as customer_id,
    k.id as kontrak_id,
    CASE WHEN k.status = 'Aktif' THEN 1 ELSE 0 END as is_active
FROM customers c
JOIN kontrak k ON CONCAT('CUST', LPAD(k.id, 3, '0')) = c.customer_code
WHERE c.customer_code IS NOT NULL;

SELECT 'CUSTOMER-CONTRACTS LINKS CREATED:' as info, COUNT(*) as total FROM customer_contracts;

-- STEP 9: Migrate staff from work_order_staff
-- ====================================================================
INSERT INTO staff (staff_code, staff_name, staff_role, is_active, created_at)
SELECT 
    CONCAT('STF', LPAD(wos.id, 3, '0')) as staff_code,
    wos.staff_name,
    wos.staff_role,
    wos.is_active,
    COALESCE(wos.created_at, NOW()) as created_at
FROM work_order_staff wos;

-- Verify staff migration  
SELECT 'STAFF MIGRATED:' as info;
SELECT staff_role, COUNT(*) as count, 
       GROUP_CONCAT(staff_name SEPARATOR ', ') as names
FROM staff 
WHERE is_active = 1 
GROUP BY staff_role;

-- STEP 10: Update inventory_unit with customer links
-- ====================================================================

-- Add the new columns if not already added
ALTER TABLE inventory_unit 
ADD COLUMN IF NOT EXISTS customer_id int(11) NULL AFTER kontrak_id,
ADD COLUMN IF NOT EXISTS customer_location_id int(11) NULL AFTER customer_id;

-- Link inventory units to customers based on kontrak
UPDATE inventory_unit iu
JOIN kontrak k ON iu.kontrak_id = k.id  
JOIN customers c ON CONCAT('CUST', LPAD(k.id, 3, '0')) = c.customer_code
SET iu.customer_id = c.id
WHERE iu.is_active = 1;

-- Link to primary customer locations
UPDATE inventory_unit iu
JOIN customer_locations cl ON iu.customer_id = cl.customer_id AND cl.is_primary = 1
SET iu.customer_location_id = cl.id
WHERE iu.customer_id IS NOT NULL;

-- Verify inventory unit links
SELECT 'INVENTORY UNIT LINKS:' as info;
SELECT 
    COUNT(*) as total_units,
    COUNT(customer_id) as linked_to_customer,
    COUNT(customer_location_id) as linked_to_location,
    COUNT(CASE WHEN customer_id IS NULL THEN 1 END) as unlinked
FROM inventory_unit 
WHERE is_active = 1;

-- STEP 11: Create sample staff area assignments
-- ====================================================================

-- Assign one admin per area
INSERT INTO area_staff_assignments (area_id, staff_id, assignment_type, start_date, is_active)
SELECT 
    a.id as area_id,
    s.id as staff_id,
    'PRIMARY' as assignment_type,
    CURDATE() as start_date,
    1 as is_active
FROM areas a
CROSS JOIN staff s 
WHERE s.staff_role = 'ADMIN' AND s.is_active = 1
LIMIT (SELECT COUNT(*) FROM areas WHERE is_active = 1);

-- Assign foreman, mechanics, helpers to areas (customize as needed)
-- This is a sample - adjust based on your actual staff
INSERT INTO area_staff_assignments (area_id, staff_id, assignment_type, start_date, is_active)
SELECT 
    a.id as area_id,
    s.id as staff_id,
    'PRIMARY' as assignment_type,
    CURDATE() as start_date,
    1 as is_active
FROM areas a
CROSS JOIN staff s
WHERE s.staff_role IN ('FOREMAN', 'MECHANIC', 'HELPER') 
  AND s.is_active = 1
ORDER BY a.id, s.staff_role
LIMIT 20; -- Adjust limit based on your staff count

-- Verify staff assignments
SELECT 'STAFF AREA ASSIGNMENTS:' as info;
SELECT 
    a.area_name,
    s.staff_role,
    COUNT(*) as staff_count,
    GROUP_CONCAT(s.staff_name SEPARATOR ', ') as staff_names
FROM area_staff_assignments asa
JOIN areas a ON asa.area_id = a.id  
JOIN staff s ON asa.staff_id = s.id
WHERE asa.is_active = 1
GROUP BY a.area_name, s.staff_role
ORDER BY a.area_name, s.staff_role;

-- STEP 12: Test the new system with views
-- ====================================================================

-- Test area summary view
SELECT 'AREA SUMMARY VIEW:' as info;
SELECT * FROM vw_area_staff_summary;

-- Test unit complete info (limit for readability)
SELECT 'UNIT COMPLETE INFO SAMPLE:' as info;
SELECT 
    no_unit, customer_name, area_name, 
    admin_staff, foreman_staff, mechanic_staff, helper_staff
FROM vw_unit_complete_info 
LIMIT 5;

-- Test staff assignment function
SELECT 'STAFF ASSIGNMENT FUNCTION TEST:' as info;
SELECT 
    iu.no_unit,
    GetAreaStaffByRole(iu.id_inventory_unit, 'ADMIN') as admin_id,
    GetAreaStaffByRole(iu.id_inventory_unit, 'FOREMAN') as foreman_id,
    GetAreaStaffByRole(iu.id_inventory_unit, 'MECHANIC') as mechanic_id
FROM inventory_unit iu 
WHERE iu.is_active = 1 
  AND iu.customer_id IS NOT NULL
LIMIT 3;

-- STEP 13: Final verification queries
-- ====================================================================
SELECT '=== MIGRATION SUMMARY ===' as info;

SELECT 'Areas:' as table_name, COUNT(*) as count FROM areas WHERE is_active = 1
UNION ALL
SELECT 'Customers:', COUNT(*) FROM customers WHERE is_active = 1  
UNION ALL
SELECT 'Customer Locations:', COUNT(*) FROM customer_locations WHERE is_active = 1
UNION ALL  
SELECT 'Staff:', COUNT(*) FROM staff WHERE is_active = 1
UNION ALL
SELECT 'Area Staff Assignments:', COUNT(*) FROM area_staff_assignments WHERE is_active = 1
UNION ALL
SELECT 'Linked Inventory Units:', COUNT(*) FROM inventory_unit WHERE customer_id IS NOT NULL AND is_active = 1;

-- Check for any orphaned data
SELECT 'ORPHANED DATA CHECK:' as info;
SELECT 'Units without customer:' as issue, COUNT(*) as count 
FROM inventory_unit 
WHERE customer_id IS NULL AND kontrak_id IS NOT NULL AND is_active = 1
UNION ALL
SELECT 'Customers without area:', COUNT(*) 
FROM customers 
WHERE area_id IS NULL AND is_active = 1
UNION ALL  
SELECT 'Staff without assignments:', COUNT(*)
FROM staff s
LEFT JOIN area_staff_assignments asa ON s.id = asa.staff_id AND asa.is_active = 1
WHERE asa.staff_id IS NULL AND s.is_active = 1;

-- ====================================================================
-- MIGRATION COMPLETE - READY FOR APPLICATION CODE UPDATES
-- ====================================================================

SELECT '=== MIGRATION COMPLETED SUCCESSFULLY ===' as status;
SELECT 'Next steps:' as info, 'Update application models and controllers' as action;