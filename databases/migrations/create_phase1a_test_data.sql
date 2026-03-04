-- =====================================================
-- Phase 1A: Test Data Creation Script
-- =====================================================
-- 
-- This script creates test data for Phase 1A testing:
-- - Test customers and locations
-- - Test inventory units (various states)
-- - Test contracts (active, completed, extended)
-- - Test contract-unit assignments (junction table)
-- - Edge cases (multiple history, no contracts, etc.)
--
-- Usage:
--   mysql -u root -p optima_staging < create_phase1a_test_data.sql
--
-- WARNING: This creates TEST-* prefixed records. Clean up after testing!
--
-- Created: 2026-03-04
-- Updated: 2026-03-04 - Match current production schema
-- =====================================================

USE optima_staging;

START TRANSACTION;

-- =====================================================
-- 1. CREATE TEST CUSTOMERS
-- =====================================================

INSERT INTO customers (customer_code, customer_name, address, phone, email, is_active, created_at, updated_at) VALUES
('TEST-CUST-001', 'PT Test Customer Active', 'Jl. Test Active No. 1, Jakarta', '021-1111111', 'active@test.com', 1, NOW(), NOW()),
('TEST-CUST-002', 'PT Test Customer Inactive', 'Jl. Test Inactive No. 2, Bandung', '022-2222222', 'inactive@test.com', 0, NOW(), NOW()),
('TEST-CUST-003', 'PT Test Customer Multiple Locations', 'Jl. Test Multi No. 3, Surabaya', '031-3333333', 'multi@test.com', 1, NOW(), NOW()),
('TEST-CUST-004', 'PT Test Customer For Transfer', 'Jl. Test Transfer No. 4, Semarang', '024-4444444', 'transfer@test.com', 1, NOW(), NOW());

-- Get last inserted customer IDs
SET @customer_active_id = LAST_INSERT_ID();
SET @customer_inactive_id = @customer_active_id + 1;
SET @customer_multi_id = @customer_active_id + 2;
SET @customer_transfer_id = @customer_active_id + 3;

-- =====================================================
-- 2. CREATE TEST CUSTOMER LOCATIONS
-- =====================================================

INSERT INTO customer_locations (customer_id, location_name, address, contact_person, phone, city, province, is_active, created_at, updated_at) VALUES
-- Active customer locations
(@customer_active_id, 'Jakarta Office Test', 'Jl. Sudirman, Jakarta Pusat', 'Test PIC Jakarta', '081111111111', 'Jakarta', 'DKI Jakarta', 1, NOW(), NOW()),
(@customer_active_id, 'Bandung Branch Test', 'Jl. Asia Afrika, Bandung', 'Test PIC Bandung', '082222222222', 'Bandung', 'Jawa Barat', 1, NOW(), NOW()),

-- Multiple locations customer
(@customer_multi_id, 'Surabaya HQ Test', 'Jl. Tunjungan, Surabaya', 'Test PIC Surabaya', '083333333333', 'Surabaya', 'Jawa Timur', 1, NOW(), NOW()),
(@customer_multi_id, 'Surabaya Warehouse Test', 'Jl. Rungkut Industri, Surabaya', 'Test PIC Warehouse', '083333333334', 0, NOW(), NOW()),

-- Transfer test customer
(@customer_transfer_id, 'Semarang Office Test', 'Jl. Pemuda, Semarang', 'Test PIC Semarang', '084444444444', 0, NOW(), NOW());

-- Get location IDs
SET @location_jakarta_id = LAST_INSERT_ID();
SET @location_bandung_id = @location_jakarta_id + 1;
SET @location_surabaya_hq_id = @location_jakarta_id + 2;
SET @location_surabaya_warehouse_id = @location_jakarta_id + 3;
SET @location_semarang_id = @location_jakarta_id + 4;

-- =====================================================
-- 3. CREATE TEST INVENTORY UNITS
-- =====================================================

-- Get a valid brand_id (use first brand in database)
SET @test_brand_id = (SELECT id FROM brand LIMIT 1);

-- If no brands exist, create one
INSERT INTO brand (nama_brand, is_deleted, created_at, updated_at)
SELECT 'TEST BRAND', 0, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM brand LIMIT 1);

SET @test_brand_id = COALESCE(@test_brand_id, LAST_INSERT_ID());

INSERT INTO inventory_unit (
    nomor_mesin, 
    nomor_rangka, 
    tahun_pembuatan, 
    status, 
    brand_id,
    kondisi,
    is_deleted,
    created_at,
    updated_at
) VALUES
-- Unit 1: Available/Ready (no active contract)
('TEST-UNIT-001', 'TEST-FRAME-001', 2024, 'READY', @test_brand_id, 'BAIK', 0, NOW(), NOW()),

-- Unit 2: Currently rented (active contract)
('TEST-UNIT-002', 'TEST-FRAME-002', 2024, 'TERSEWA', @test_brand_id, 'BAIK', 0, NOW(), NOW()),

-- Unit 3: Has contract history (for testing transfer)
('TEST-UNIT-003', 'TEST-FRAME-003', 2023, 'TERSEWA', @test_brand_id, 'BAIK', 0, NOW(), NOW()),

-- Unit 4: Recently returned (was rented, now available)
('TEST-UNIT-004', 'TEST-FRAME-004', 2023, 'READY', @test_brand_id, 'BAIK', 0, NOW(), NOW()),

-- Unit 5: Temporary assignment
('TEST-UNIT-005', 'TEST-FRAME-005', 2024, 'TERSEWA', @test_brand_id, 'BAIK', 0, NOW(), NOW()),

-- Unit 6: Extended contract
('TEST-UNIT-006', 'TEST-FRAME-006', 2023, 'TERSEWA', @test_brand_id, 'BAIK', 0, NOW(), NOW()),

-- Unit 7: Multiple historical contracts
('TEST-UNIT-007', 'TEST-FRAME-007', 2022, 'TERSEWA', @test_brand_id, 'BAIK', 0, NOW(), NOW()),

-- Unit 8-10: Batch of available units for dropdown testing
('TEST-UNIT-008', 'TEST-FRAME-008', 2024, 'READY', @test_brand_id, 'BAIK', 0, NOW(), NOW()),
('TEST-UNIT-009', 'TEST-FRAME-009', 2024, 'READY', @test_brand_id, 'BAIK', 0, NOW(), NOW()),
('TEST-UNIT-010', 'TEST-FRAME-010', 2024, 'READY', @test_brand_id, 'BAIK', 0, NOW(), NOW());

-- Get unit IDs
SET @unit_001_id = (SELECT id_inventory_unit FROM inventory_unit WHERE nomor_mesin = 'TEST-UNIT-001');
SET @unit_002_id = (SELECT id_inventory_unit FROM inventory_unit WHERE nomor_mesin = 'TEST-UNIT-002');
SET @unit_003_id = (SELECT id_inventory_unit FROM inventory_unit WHERE nomor_mesin = 'TEST-UNIT-003');
SET @unit_004_id = (SELECT id_inventory_unit FROM inventory_unit WHERE nomor_mesin = 'TEST-UNIT-004');
SET @unit_005_id = (SELECT id_inventory_unit FROM inventory_unit WHERE nomor_mesin = 'TEST-UNIT-005');
SET @unit_006_id = (SELECT id_inventory_unit FROM inventory_unit WHERE nomor_mesin = 'TEST-UNIT-006');
SET @unit_007_id = (SELECT id_inventory_unit FROM inventory_unit WHERE nomor_mesin = 'TEST-UNIT-007');

-- =====================================================
-- 4. CREATE TEST CONTRACTS
-- =====================================================

INSERT INTO kontrak (
    nomor_kontrak,
    customer_location_id,
    jenis_sewa,
    harga_sewa,
    tanggal_mulai,
    tanggal_selesai,
    status,
    is_deleted,
    created_at,
    updated_at
) VALUES
-- Contract 1: Active contract (Jakarta)
(
    'TEST-K-001',
    @location_jakarta_id,
    'BULANAN',
    5000000,
    '2026-01-01',
    '2026-12-31',
    'AKTIF',
    0,
    NOW(),
    NOW()
),

-- Contract 2: Extended contract (Bandung)
(
    'TEST-K-002',
    @location_bandung_id,
    'BULANAN',
    4500000,
    '2025-12-01',
    '2026-11-30',
    'DIPERPANJANG',
    0,
    NOW(),
    NOW()
),

-- Contract 3: Completed contract (historical)
(
    'TEST-K-003',
    @location_surabaya_hq_id,
    'HARIAN',
    150000,
    '2025-11-01',
    '2025-11-30',
    'SELESAI',
    0,
    NOW(),
    NOW()
),

-- Contract 4: Old completed contract (for history testing)
(
    'TEST-K-004',
    @location_jakarta_id,
    'BULANAN',
    5000000,
    '2025-06-01',
    '2025-11-30',
    'SELESAI',
    0,
    NOW(),
    NOW()
),

-- Contract 5: Another old completed contract (for history testing)
(
    'TEST-K-005',
    @location_bandung_id,
    'BULANAN',
    4800000,
    '2025-01-01',
    '2025-05-31',
    'SELESAI',
    0,
    NOW(),
    NOW()
),

-- Contract 6: Active contract for transfer test
(
    'TEST-K-006',
    @location_semarang_id,
    'BULANAN',
    5200000,
    '2026-02-01',
    '2027-01-31',
    'AKTIF',
    0,
    NOW(),
    NOW()
);

-- Get contract IDs
SET @contract_001_id = (SELECT id FROM kontrak WHERE nomor_kontrak = 'TEST-K-001');
SET @contract_002_id = (SELECT id FROM kontrak WHERE nomor_kontrak = 'TEST-K-002');
SET @contract_003_id = (SELECT id FROM kontrak WHERE nomor_kontrak = 'TEST-K-003');
SET @contract_004_id = (SELECT id FROM kontrak WHERE nomor_kontrak = 'TEST-K-004');
SET @contract_005_id = (SELECT id FROM kontrak WHERE nomor_kontrak = 'TEST-K-005');
SET @contract_006_id = (SELECT id FROM kontrak WHERE nomor_kontrak = 'TEST-K-006');

-- =====================================================
-- 5. CREATE TEST CONTRACT-UNIT ASSIGNMENTS
-- =====================================================

-- Get a test marketing user ID (or create one)
SET @test_marketing_user = (SELECT nama FROM users WHERE role = 'marketing' LIMIT 1);
SET @test_marketing_user = COALESCE(@test_marketing_user, 'Test Marketing User');

INSERT INTO kontrak_unit (
    kontrak_id,
    unit_id,
    status,
    is_temporary,
    start_date,
    end_date,
    marketing_name,
    created_at,
    updated_at
) VALUES
-- TEST-UNIT-002: Active contract (permanent assignment)
(
    @contract_001_id,
    @unit_002_id,
    'AKTIF',
    0,
    '2026-01-01',
    NULL,
    @test_marketing_user,
    NOW(),
    NOW()
),

-- TEST-UNIT-003: Current active contract (with history below)
(
    @contract_002_id,
    @unit_003_id,
    'DIPERPANJANG',
    0,
    '2025-12-01',
    NULL,
    @test_marketing_user,
    NOW(),
    NOW()
),

-- TEST-UNIT-004: Completed contract (recently returned)
(
    @contract_003_id,
    @unit_004_id,
    'SELESAI',
    0,
    '2025-11-01',
    '2025-11-30',
    @test_marketing_user,
    NOW(),
    NOW()
),

-- TEST-UNIT-005: Temporary assignment
(
    @contract_001_id,
    @unit_005_id,
    'AKTIF',
    1,  -- is_temporary = 1
    '2026-03-01',
    '2026-03-15',
    @test_marketing_user,
    NOW(),
    NOW()
),

-- TEST-UNIT-006: Extended contract
(
    @contract_002_id,
    @unit_006_id,
    'DIPERPANJANG',
    0,
    '2025-12-01',
    NULL,
    @test_marketing_user,
    NOW(),
    NOW()
),

-- TEST-UNIT-007: Multiple historical contracts (oldest first)
-- Contract 1 (Jan-May 2025)
(
    @contract_005_id,
    @unit_007_id,
    'SELESAI',
    0,
    '2025-01-01',
    '2025-05-31',
    @test_marketing_user,
    NOW(),
    NOW()
),

-- Contract 2 (Jun-Nov 2025)
(
    @contract_004_id,
    @unit_007_id,
    'SELESAI',
    0,
    '2025-06-01',
    '2025-11-30',
    @test_marketing_user,
    NOW(),
    NOW()
),

-- Contract 3 (Current - Dec 2025 onwards)
(
    @contract_002_id,
    @unit_007_id,
    'DIPERPANJANG',
    0,
    '2025-12-01',
    NULL,
    @test_marketing_user,
    NOW(),
    NOW()
),

-- TEST-UNIT-003: Historical contract (for transfer testing)
(
    @contract_004_id,
    @unit_003_id,
    'SELESAI',
    0,
    '2025-06-01',
    '2025-11-30',
    @test_marketing_user,
    NOW(),
    NOW()
);

-- =====================================================
-- 6. VERIFY TEST DATA CREATED
-- =====================================================

-- Display summary
SELECT '=== TEST DATA CREATION SUMMARY ===' as '';

SELECT 
    'Customers Created' as item,
    COUNT(*) as count
FROM customers
WHERE nama_customer LIKE 'PT Test%';

SELECT 
    'Customer Locations Created' as item,
    COUNT(*) as count
FROM customer_locations
WHERE nama_area LIKE '%Test%';

SELECT 
    'Test Units Created' as item,
    COUNT(*) as count
FROM inventory_unit
WHERE nomor_mesin LIKE 'TEST-UNIT-%';

SELECT 
    'Test Contracts Created' as item,
    COUNT(*) as count
FROM kontrak
WHERE nomor_kontrak LIKE 'TEST-K-%';

SELECT 
    'Contract-Unit Assignments Created' as item,
    COUNT(*) as count
FROM kontrak_unit
WHERE marketing_name = @test_marketing_user
  AND created_at >= DATE_SUB(NOW(), INTERVAL 5 MINUTE);

-- =====================================================
-- 7. VERIFY JUNCTION TABLE PATTERN
-- =====================================================

SELECT '=== JUNCTION TABLE VERIFICATION ===' as '';

-- Units with active contracts (should be 4: UNIT-002, 003, 006, 007)
SELECT 
    'Units with Active Contracts' as item,
    COUNT(DISTINCT ku.unit_id) as count
FROM kontrak_unit ku
WHERE ku.status IN ('AKTIF', 'DIPERPANJANG')
  AND ku.is_temporary = 0
  AND ku.unit_id IN (
      SELECT id_inventory_unit 
      FROM inventory_unit 
      WHERE nomor_mesin LIKE 'TEST-UNIT-%'
  );

-- Units available (should be 5: UNIT-001, 004, 008, 009, 010)
SELECT 
    'Units Available (no active contract)' as item,
    COUNT(*) as count
FROM inventory_unit iu
LEFT JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit
    AND ku.status IN ('AKTIF', 'DIPERPANJANG')
    AND ku.is_temporary = 0
WHERE iu.nomor_mesin LIKE 'TEST-UNIT-%'
  AND ku.id IS NULL;

-- Unit with multiple history (should find UNIT-007 with 3 contracts)
SELECT 
    iu.nomor_mesin,
    COUNT(*) as contract_count,
    GROUP_CONCAT(k.nomor_kontrak ORDER BY ku.start_date) as contract_history
FROM inventory_unit iu
INNER JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit
INNER JOIN kontrak k ON k.id = ku.kontrak_id
WHERE iu.nomor_mesin = 'TEST-UNIT-007'
GROUP BY iu.id_inventory_unit, iu.nomor_mesin;

-- =====================================================
-- 8. EDGE CASE VERIFICATION
-- =====================================================

SELECT '=== EDGE CASE VERIFICATION ===' as '';

-- Temporary assignment (should find UNIT-005)
SELECT 
    iu.nomor_mesin,
    k.nomor_kontrak,
    ku.is_temporary
FROM inventory_unit iu
INNER JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit
INNER JOIN kontrak k ON k.id = ku.kontrak_id
WHERE iu.nomor_mesin LIKE 'TEST-UNIT-%'
  AND ku.is_temporary = 1;

-- Extended contract (should find UNIT-006, UNIT-007)
SELECT 
    iu.nomor_mesin,
    k.nomor_kontrak,
    ku.status
FROM inventory_unit iu
INNER JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit
INNER JOIN kontrak k ON k.id = ku.kontrak_id
WHERE iu.nomor_mesin LIKE 'TEST-UNIT-%'
  AND ku.status = 'DIPERPANJANG'
  AND ku.is_temporary = 0;

-- Completed contracts (historical data)
SELECT 
    iu.nomor_mesin,
    k.nomor_kontrak,
    ku.status,
    ku.start_date,
    ku.end_date
FROM inventory_unit iu
INNER JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit
INNER JOIN kontrak k ON k.id = ku.kontrak_id
WHERE iu.nomor_mesin LIKE 'TEST-UNIT-%'
  AND ku.status = 'SELESAI'
ORDER BY iu.nomor_mesin, ku.start_date DESC;

COMMIT;

SELECT '=== TEST DATA CREATION COMPLETE ===' as '';
SELECT 'To cleanup test data, run: DELETE FROM inventory_unit WHERE nomor_mesin LIKE "TEST-UNIT-%";' as cleanup_command;
