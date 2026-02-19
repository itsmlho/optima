-- ================================================================
-- DATABASE DIAGNOSTIC SCRIPT
-- ================================================================
-- Purpose: Identify actual table names and structure in production
-- Run this FIRST before migration to discover the real schema
-- ================================================================

-- Show current database
SELECT DATABASE() AS current_database;

-- ================================================================
-- STEP 1: LIST ALL TABLES
-- ================================================================
SELECT 'All tables in database:' AS info;
SHOW TABLES;

-- ================================================================
-- STEP 2: FIND DELIVERY INSTRUCTION TABLE
-- ================================================================
-- Check for various possible table names
SELECT 'Looking for delivery instruction table...' AS info;

-- Try common variations
SELECT TABLE_NAME, TABLE_ROWS, CREATE_TIME
FROM INFORMATION_SCHEMA.TABLES
WHERE TABLE_SCHEMA = DATABASE()
  AND (
    TABLE_NAME LIKE '%delivery%' 
    OR TABLE_NAME LIKE '%instruksi%'
    OR TABLE_NAME LIKE '%surat_jalan%'
    OR TABLE_NAME LIKE '%di%'
    OR TABLE_NAME = 'delivery_instructions'
  )
ORDER BY TABLE_NAME;

-- ================================================================
-- STEP 3: CHECK SPK TABLE (we know this exists)
-- ================================================================
SELECT 'Checking SPK table structure...' AS info;
DESCRIBE spk;

-- Check for DI references in SPK
SELECT 'Columns in SPK that might reference DI:' AS info;
SELECT COLUMN_NAME, COLUMN_TYPE, COLUMN_KEY, COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'spk'
  AND (
    COLUMN_NAME LIKE '%di%'
    OR COLUMN_NAME LIKE '%delivery%'
    OR COLUMN_NAME LIKE '%instruksi%'
  );

-- ================================================================
-- STEP 4: CHECK QUOTATIONS TABLE
-- ================================================================
SELECT 'Checking quotations table...' AS info;
SELECT TABLE_NAME
FROM INFORMATION_SCHEMA.TABLES
WHERE TABLE_SCHEMA = DATABASE()
  AND (
    TABLE_NAME = 'quotations'
    OR TABLE_NAME = 'penawaran'
    OR TABLE_NAME LIKE '%quotation%'
  );

-- ================================================================
-- STEP 5: CHECK KONTRAK TABLE
-- ================================================================
SELECT 'Checking kontrak table structure...' AS info;
DESCRIBE kontrak;

-- ================================================================
-- STEP 6: SEARCH FOR INVOICE_GENERATED COLUMN
-- ================================================================
-- Check if migration was already applied with different table name
SELECT 'Searching for invoice_generated column in all tables...' AS info;
SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND COLUMN_NAME IN ('invoice_generated', 'invoice_generated_at');

-- ================================================================
-- STEP 7: CHECK FOR STATUS_DI COLUMN (to identify DI table)
-- ================================================================
SELECT 'Tables with status_di column:' AS info;
SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND COLUMN_NAME = 'status_di';

-- ================================================================
-- STEP 8: CHECK FOR NOMOR_DI COLUMN
-- ================================================================
SELECT 'Tables with nomor_di column:' AS info;
SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND COLUMN_NAME = 'nomor_di';

-- ================================================================
-- STEP 9: CHECK FOR SAMPAI_TANGGAL_APPROVE COLUMN
-- ================================================================
SELECT 'Tables with sampai_tanggal_approve column:' AS info;
SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND COLUMN_NAME = 'sampai_tanggal_approve';

-- ================================================================
-- STEP 10: LIST ALL FOREIGN KEYS
-- ================================================================
SELECT 'Foreign keys in database:' AS info;
SELECT 
    TABLE_NAME,
    COLUMN_NAME,
    CONSTRAINT_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = DATABASE()
  AND REFERENCED_TABLE_NAME IS NOT NULL
ORDER BY TABLE_NAME, COLUMN_NAME;

-- ================================================================
-- RESULTS INTERPRETATION
-- ================================================================
-- After running this script, look for:
-- 1. The actual name of the delivery instructions table
-- 2. Whether the table exists at all
-- 3. If columns like status_di, nomor_di, sampai_tanggal_approve exist
-- 4. What the actual table structure looks like
-- ================================================================
