-- ============================================
-- PRODUCTION MIGRATION: Quotation Specifications Enhancement
-- Date: 2026-03-13
-- Purpose: Add spare unit, operator, and technical specs columns
-- ============================================

-- BACKUP FIRST!
-- CREATE TABLE quotation_specifications_backup_20260313 AS SELECT * FROM quotation_specifications;

-- ============================================
-- 1. ALTER quotation_specifications TABLE
-- ============================================

-- Check and add spare_quantity column
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'quotation_specifications' 
  AND COLUMN_NAME = 'spare_quantity';

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE quotation_specifications ADD COLUMN spare_quantity INT(11) DEFAULT 0 AFTER quantity',
    'SELECT "Column spare_quantity already exists" AS message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add is_spare_unit column
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'quotation_specifications' 
  AND COLUMN_NAME = 'is_spare_unit';

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE quotation_specifications ADD COLUMN is_spare_unit TINYINT(1) DEFAULT 0 AFTER spare_quantity',
    'SELECT "Column is_spare_unit already exists" AS message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add include_operator column
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'quotation_specifications' 
  AND COLUMN_NAME = 'include_operator';

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE quotation_specifications ADD COLUMN include_operator TINYINT(1) DEFAULT 0 AFTER is_spare_unit',
    'SELECT "Column include_operator already exists" AS message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add operator_quantity column
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'quotation_specifications' 
  AND COLUMN_NAME = 'operator_quantity';

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE quotation_specifications ADD COLUMN operator_quantity INT(11) DEFAULT 0 AFTER include_operator',
    'SELECT "Column operator_quantity already exists" AS message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add operator_monthly_rate column
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'quotation_specifications' 
  AND COLUMN_NAME = 'operator_monthly_rate';

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE quotation_specifications ADD COLUMN operator_monthly_rate DECIMAL(15,2) DEFAULT 0.00 AFTER operator_quantity',
    'SELECT "Column operator_monthly_rate already exists" AS message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add operator_daily_rate column
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'quotation_specifications' 
  AND COLUMN_NAME = 'operator_daily_rate';

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE quotation_specifications ADD COLUMN operator_daily_rate DECIMAL(15,2) DEFAULT 0.00 AFTER operator_monthly_rate',
    'SELECT "Column operator_daily_rate already exists" AS message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add operator_description column
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'quotation_specifications' 
  AND COLUMN_NAME = 'operator_description';

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE quotation_specifications ADD COLUMN operator_description TEXT NULL AFTER operator_daily_rate',
    'SELECT "Column operator_description already exists" AS message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add operator_certification_required column
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'quotation_specifications' 
  AND COLUMN_NAME = 'operator_certification_required';

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE quotation_specifications ADD COLUMN operator_certification_required TINYINT(1) DEFAULT 0 AFTER operator_description',
    'SELECT "Column operator_certification_required already exists" AS message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add unit_accessories column
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'quotation_specifications' 
  AND COLUMN_NAME = 'unit_accessories';

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE quotation_specifications ADD COLUMN unit_accessories TEXT NULL AFTER operator_certification_required',
    'SELECT "Column unit_accessories already exists" AS message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add attachment_id column
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'quotation_specifications' 
  AND COLUMN_NAME = 'attachment_id';

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE quotation_specifications ADD COLUMN attachment_id INT(11) NULL AFTER unit_accessories',
    'SELECT "Column attachment_id already exists" AS message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================
-- 2. VERIFY COLUMNS ADDED
-- ============================================
SELECT 
    'quotation_specifications' AS table_name,
    GROUP_CONCAT(COLUMN_NAME ORDER BY ORDINAL_POSITION) AS columns
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'quotation_specifications'
  AND COLUMN_NAME IN (
      'spare_quantity', 'is_spare_unit', 'include_operator', 
      'operator_quantity', 'operator_monthly_rate', 'operator_daily_rate',
      'operator_description', 'operator_certification_required',
      'unit_accessories', 'attachment_id'
  );

-- ============================================
-- 3. CHECK MASTER TABLES EXISTENCE
-- ============================================
SELECT 
    TABLE_NAME,
    CASE 
        WHEN TABLE_NAME IN (
            SELECT TABLE_NAME 
            FROM INFORMATION_SCHEMA.TABLES 
            WHERE TABLE_SCHEMA = DATABASE()
        ) THEN '✅ EXISTS'
        ELSE '❌ MISSING'
    END AS status
FROM (
    SELECT 'baterai' AS TABLE_NAME
    UNION SELECT 'charger'
    UNION SELECT 'attachment'
    UNION SELECT 'valve'
    UNION SELECT 'tipe_mast'
    UNION SELECT 'tipe_ban'
    UNION SELECT 'jenis_roda'
) AS required_tables;

-- ============================================
-- NOTES FOR MISSING TABLES:
-- ============================================
-- If any master tables are missing, they need to be created
-- Please refer to your development database structure
-- or contact the development team for the table schemas

-- ============================================
-- ROLLBACK (if needed):
-- ============================================
/*
ALTER TABLE quotation_specifications DROP COLUMN spare_quantity;
ALTER TABLE quotation_specifications DROP COLUMN is_spare_unit;
ALTER TABLE quotation_specifications DROP COLUMN include_operator;
ALTER TABLE quotation_specifications DROP COLUMN operator_quantity;
ALTER TABLE quotation_specifications DROP COLUMN operator_monthly_rate;
ALTER TABLE quotation_specifications DROP COLUMN operator_daily_rate;
ALTER TABLE quotation_specifications DROP COLUMN operator_description;
ALTER TABLE quotation_specifications DROP COLUMN operator_certification_required;
ALTER TABLE quotation_specifications DROP COLUMN unit_accessories;
ALTER TABLE quotation_specifications DROP COLUMN attachment_id;
*/
