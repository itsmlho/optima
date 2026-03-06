-- =====================================================
-- Migration: Add customer_location_id to kontrak_unit
-- Date: 2026-03-05
-- Purpose: Track lokasi/titik penempatan unit dalam kontrak
-- =====================================================

-- Step 1: Add customer_location_id column to kontrak_unit (if not exists)
-- Use MySQL 8.0+ syntax or run conditionally
-- ALTER TABLE kontrak_unit ADD COLUMN IF NOT EXISTS customer_location_id INT UNSIGNED NULL;

-- For compatibility, add column only if it doesn't exist
SET @dbname = DATABASE();
SET @tablename = 'kontrak_unit';
SET @columnname = 'customer_location_id';
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
     WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) = 0,
    'ALTER TABLE kontrak_unit ADD COLUMN customer_location_id INT UNSIGNED NULL COMMENT ''Lokasi/titik penempatan unit dalam kontrak''',
    'SELECT ''Column already exists'''
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 2: Add foreign key constraint (if not exists)
-- ALTER TABLE kontrak_unit ADD CONSTRAINT IF NOT EXISTS fk_kontrak_unit_location FOREIGN KEY ...

-- For compatibility, add FK only if it doesn't exist
SET @constraintname = 'fk_kontrak_unit_location';
SET @sql_fk = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
     WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND CONSTRAINT_NAME = @constraintname) = 0,
    'ALTER TABLE kontrak_unit ADD CONSTRAINT fk_kontrak_unit_location FOREIGN KEY (customer_location_id) REFERENCES customer_locations(id) ON DELETE SET NULL ON UPDATE CASCADE',
    'SELECT ''FK already exists'''
));
PREPARE stmt FROM @sql_fk;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 3: Add index for faster queries (if not exists)
CREATE INDEX IF NOT EXISTS idx_kontrak_unit_location ON kontrak_unit(customer_location_id);

-- =====================================================
-- Verification
-- =====================================================
-- DESCRIBE kontrak_unit;
-- SELECT ku.*, cl.location_name FROM kontrak_unit ku
-- LEFT JOIN customer_locations cl ON cl.id = ku.customer_location_id
-- LIMIT 10;

-- =====================================================
-- Rollback (if needed)
-- =====================================================
-- ALTER TABLE kontrak_unit DROP FOREIGN KEY fk_kontrak_unit_location;
-- ALTER TABLE kontrak_unit DROP COLUMN customer_location_id;
-- DROP INDEX idx_kontrak_unit_location ON kontrak_unit;
