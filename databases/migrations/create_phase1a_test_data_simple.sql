-- ==============================================================================
-- Phase 1A: Simple Test Data Creation Script
-- ==============================================================================
-- Creates minimal test data for Phase 1A testing
-- Uses actual current production schema (mixed Indonesian/English column names)
--
-- Usage:
--   mysql -u root optima_staging < create_phase1a_test_data_simple.sql
--
-- Created: 2026-03-04
-- ==============================================================================

USE optima_staging;

START TRANSACTION;

-- =============================================================================
-- 1. TEST CUSTOMERS (customers table uses ENGLISH column names)
-- =============================================================================

INSERT INTO customers (customer_code, customer_name, address, phone, email, is_active) VALUES
('TEST-CUST-001', 'PT Test Customer Alpha', 'Jl. Test No. 1, Jakarta', '021-1234567', 'alpha@test.com', 1),
('TEST-CUST-002', 'PT Test Customer Beta', 'Jl. Test No. 2, Bandung', '022-7654321', 'beta@test.com', 1);

SET @cust_alpha = LAST_INSERT_ID();
SET @cust_beta = @cust_alpha + 1;

-- =============================================================================
-- 2. TEST CUSTOMER LOCATIONS (customer_locations uses ENGLISH column names)
-- =============================================================================

INSERT INTO customer_locations (customer_id, location_name, address, contact_person, phone, city, province, is_active) VALUES
(@cust_alpha, 'Test Location Alpha-1', 'Jl. Sudirman, Jakarta', 'PIC Alpha', '08111111111', 'Jakarta', 'DKI Jakarta', 1),
(@cust_beta, 'Test Location Beta-1', 'Jl. Asia Afrika, Bandung', 'PIC Beta', '08222222222', 'Bandung', 'Jawa Barat', 1);

SET @loc_alpha = LAST_INSERT_ID();
SET @loc_beta = @loc_alpha + 1;

-- =============================================================================
-- 3. TEST INVENTORY UNITS (inventory_unit uses INDONESIAN column names)
-- =============================================================================

-- Note: inventory_unit has many required foreign keys. Let's use existing reference data.
-- Get valid IDs from existing data
SET @valid_tipe_unit = (SELECT id_tipe_unit FROM tipe_unit LIMIT 1);
SET @valid_model = (SELECT id_model_unit FROM model_unit LIMIT 1);
SET @valid_status_unit = (SELECT id_status FROM status_unit LIMIT 1);

-- Create test units with minimal required fields
INSERT INTO inventory_unit (
    no_unit_na,
    serial_number,
    tahun_unit,
    tipe_unit_id,
    model_unit_id,
    status_unit_id
) VALUES
('TEST-UNIT-001', 'TEST-SN-001', 2024, @valid_tipe_unit, @valid_model, @valid_status_unit),
('TEST-UNIT-002', 'TEST-SN-002', 2024, @valid_tipe_unit, @valid_model, @valid_status_unit),
('TEST-UNIT-003', 'TEST-SN-003', 2023, @valid_tipe_unit, @valid_model, @valid_status_unit);

SET @unit_001 = LAST_INSERT_ID();
SET @unit_002 = @unit_001 + 1;
SET @unit_003 = @unit_001 + 2;

-- =============================================================================
-- 4. TEST CONTRACTS (kontrak table uses INDONESIAN column names)
-- =============================================================================

INSERT INTO kontrak (
    no_kontrak,
    customer_id,
    customer_location_id,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status
) VALUES
-- Active contract for customer Alpha
('TEST-K-001', @cust_alpha, @loc_alpha, 'CONTRACT', '2026-01-01', '2026-12-31', 'ACTIVE'),

-- Active contract for customer Beta
('TEST-K-002', @cust_beta, @loc_beta, 'CONTRACT', '2026-02-01', '2026-12-31', 'ACTIVE');

SET @kontrak_001 = LAST_INSERT_ID();
SET @kontrak_002 = @kontrak_001 + 1;

-- =============================================================================
-- 5. TEST CONTRACT-UNIT ASSIGNMENTS (kontrak_unit junction table)
-- =============================================================================

-- This is the SOURCE OF TRUTH for unit-contract relationships
INSERT INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) VALUES
-- Unit 001 assigned to contract 001 (ACTIVE)
(@kontrak_001, @unit_001, '2026-01-01', NULL, 'ACTIVE'),

-- Unit 002 assigned to contract 001 (ACTIVE)
(@kontrak_001, @unit_002, '2026-01-15', NULL, 'ACTIVE'),

-- Unit 003 assigned to contract 002 (ACTIVE)
(@kontrak_002, @unit_003, '2026-02-01', NULL, 'ACTIVE');

COMMIT;

-- =============================================================================
-- VERIFICATION QUERIES
-- =============================================================================

SELECT 'Test data created successfully!' AS Status;

SELECT 
    'Customers' AS Table_Name,
    COUNT(*) AS Test_Records
FROM customers 
WHERE customer_code LIKE 'TEST-%'

UNION ALL

SELECT 
    'Locations',
    COUNT(*)
FROM customer_locations 
WHERE location_name LIKE 'Test%'

UNION ALL

SELECT 
    'Units',
    COUNT(*)
FROM inventory_unit 
WHERE no_unit_na LIKE 'TEST-%'

UNION ALL

SELECT 
    'Contracts',
    COUNT(*)
FROM kontrak 
WHERE no_kontrak LIKE 'TEST-%'

UNION ALL

SELECT 
    'Contract-Unit Assignments',
    COUNT(*)
FROM kontrak_unit ku
JOIN kontrak k ON ku.kontrak_id = k.id
WHERE k.no_kontrak LIKE 'TEST-%';
