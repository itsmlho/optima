-- ============================================================================
-- INVENTORY TABLES DATABASE CHANGES
-- ============================================================================
-- Date: February 2026 (Sprint 1-3 + Status Cleanup)
-- Tables Modified: inventory_unit, inventory_attachment
-- Purpose: 
--   1. Support billing schedules, rate tracking, and hire date tracking
--   2. Clean up unused status columns in inventory_attachment
--   3. Add UNIQUE constraints to prevent duplicates
-- ============================================================================

USE optima_ci;

-- ============================================================================
-- PART 1: CHANGES TO inventory_unit TABLE
-- ============================================================================

-- 1. Add on_hire_date column
-- Purpose: Track when unit was hired out to customer
-- Used in: Unit billing schedules, rental period calculation
ALTER TABLE `inventory_unit` 
ADD COLUMN IF NOT EXISTS `on_hire_date` 
    DATE DEFAULT NULL 
    COMMENT 'Date unit was hired out to customer' 
    AFTER `kontrak_id`;

-- 2. Add off_hire_date column
-- Purpose: Track when unit was returned from customer
-- Used in: Rental period calculation, unit availability tracking
ALTER TABLE `inventory_unit` 
ADD COLUMN IF NOT EXISTS `off_hire_date` 
    DATE DEFAULT NULL 
    COMMENT 'Date unit was returned from customer' 
    AFTER `on_hire_date`;

-- 3. Add rate_changed_at column
-- Purpose: Track last rate change timestamp for amendments
-- Used in: Contract amendments, rate history tracking
ALTER TABLE `inventory_unit` 
ADD COLUMN IF NOT EXISTS `rate_changed_at` 
    DATETIME DEFAULT NULL 
    COMMENT 'Timestamp of last rate change' 
    AFTER `harga_sewa_bulanan`;

-- ============================================================================
-- PART 2: CHANGES TO inventory_attachment TABLE
-- ============================================================================

-- 1. Drop unused foreign key constraints
-- These FKs reference old status tables that are no longer used

-- Drop FK for status_unit
SET @exist_fk_status_unit := (SELECT COUNT(*) 
    FROM information_schema.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = 'optima_ci' 
    AND TABLE_NAME = 'inventory_attachment' 
    AND CONSTRAINT_NAME = 'fk_inventory_attachment_status_unit');

SET @sqlstmt := IF(@exist_fk_status_unit > 0, 
    'ALTER TABLE inventory_attachment DROP FOREIGN KEY fk_inventory_attachment_status_unit',
    'SELECT "FK status_unit not found, skipping..." as message');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Drop FK for status_attachment
SET @exist_fk_status_attachment := (SELECT COUNT(*) 
    FROM information_schema.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = 'optima_ci' 
    AND TABLE_NAME = 'inventory_attachment' 
    AND CONSTRAINT_NAME = 'fk_inventory_attachment_status_attachment');

SET @sqlstmt := IF(@exist_fk_status_attachment > 0, 
    'ALTER TABLE inventory_attachment DROP FOREIGN KEY fk_inventory_attachment_status_attachment',
    'SELECT "FK status_attachment not found, skipping..." as message');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2. Drop unused columns
-- Purpose: These status columns were replaced by attachment_status field
ALTER TABLE inventory_attachment DROP COLUMN IF EXISTS status_unit;
ALTER TABLE inventory_attachment DROP COLUMN IF EXISTS status_attachment_id;

-- 3. Add index for attachment_status (performance optimization)
SET @exist := (SELECT COUNT(*) 
               FROM information_schema.statistics 
               WHERE table_schema = 'optima_ci' 
               AND table_name = 'inventory_attachment' 
               AND index_name = 'idx_inventory_attachment_status');

SET @sqlstmt := IF(@exist = 0, 
    'CREATE INDEX idx_inventory_attachment_status ON inventory_attachment(attachment_status)',
    'SELECT "Index already exists, skipping..." as message');

PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 4. Add UNIQUE constraints to prevent duplicate attachments
-- Purpose: Prevent same attachment/charger/battery being assigned to same unit twice

-- Check and add UNIQUE constraint for unit + attachment
SET @exist_uk_attachment := (SELECT COUNT(*) 
    FROM information_schema.statistics 
    WHERE table_schema = 'optima_ci' 
    AND table_name = 'inventory_attachment' 
    AND index_name = 'uk_unit_attachment');

SET @sqlstmt := IF(@exist_uk_attachment = 0, 
    'ALTER TABLE inventory_attachment ADD UNIQUE KEY uk_unit_attachment (id_inventory_unit, attachment_id)',
    'SELECT "UNIQUE key uk_unit_attachment already exists, skipping..." as message');

PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add UNIQUE constraint for unit + charger
SET @exist_uk_charger := (SELECT COUNT(*) 
    FROM information_schema.statistics 
    WHERE table_schema = 'optima_ci' 
    AND table_name = 'inventory_attachment' 
    AND index_name = 'uk_unit_charger');

SET @sqlstmt := IF(@exist_uk_charger = 0, 
    'ALTER TABLE inventory_attachment ADD UNIQUE KEY uk_unit_charger (id_inventory_unit, charger_id)',
    'SELECT "UNIQUE key uk_unit_charger already exists, skipping..." as message');

PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add UNIQUE constraint for unit + battery
SET @exist_uk_battery := (SELECT COUNT(*) 
    FROM information_schema.statistics 
    WHERE table_schema = 'optima_ci' 
    AND table_name = 'inventory_attachment' 
    AND index_name = 'uk_unit_battery');

SET @sqlstmt := IF(@exist_uk_battery = 0, 
    'ALTER TABLE inventory_attachment ADD UNIQUE KEY uk_unit_battery (id_inventory_unit, baterai_id)',
    'SELECT "UNIQUE key uk_unit_battery already exists, skipping..." as message');

PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================================================
-- VERIFICATION QUERIES
-- ============================================================================

-- Verify inventory_unit columns
SELECT 'INVENTORY_UNIT COLUMNS:' AS verification_type;
-- Verify inventory_unit columns
SELECT 'INVENTORY_UNIT COLUMNS:' AS verification_type;

SELECT 
    COLUMN_NAME,
    COLUMN_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT,
    COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'optima_ci'
  AND TABLE_NAME = 'inventory_unit'
  AND COLUMN_NAME IN ('on_hire_date', 'off_hire_date', 'rate_changed_at')
ORDER BY ORDINAL_POSITION;

-- Verify inventory_attachment dropped columns
SELECT 'INVENTORY_ATTACHMENT - DROPPED COLUMNS (should be empty):' AS verification_type;

SELECT 
    COLUMN_NAME
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'optima_ci'
  AND TABLE_NAME = 'inventory_attachment'
  AND COLUMN_NAME IN ('status_unit', 'status_attachment_id');

-- Verify inventory_attachment indexes
SELECT 'INVENTORY_ATTACHMENT - INDEXES:' AS verification_type;

SELECT 
    INDEX_NAME,
    COLUMN_NAME,
    NON_UNIQUE,
    INDEX_TYPE
FROM INFORMATION_SCHEMA.STATISTICS
WHERE TABLE_SCHEMA = 'optima_ci'
  AND TABLE_NAME = 'inventory_attachment'
  AND INDEX_NAME IN ('idx_inventory_attachment_status', 'uk_unit_attachment', 'uk_unit_charger', 'uk_unit_battery')
ORDER BY INDEX_NAME, SEQ_IN_INDEX;

-- ============================================================================
-- NOTES
-- ============================================================================
-- 
-- INVENTORY_UNIT CHANGES:
-- 1. Added 3 columns for billing and rate tracking:
--    - on_hire_date: When unit was hired out
--    - off_hire_date: When unit was returned
--    - rate_changed_at: Last rate change timestamp
-- 2. All columns are NULLABLE and backward compatible
-- 3. Support Sprint 1-3 advanced billing features
--
-- INVENTORY_ATTACHMENT CHANGES:
-- 1. DROPPED columns (no longer used):
--    - status_unit (replaced by attachment_status)
--    - status_attachment_id (replaced by attachment_status)
-- 2. DROPPED foreign keys:
--    - fk_inventory_attachment_status_unit
--    - fk_inventory_attachment_status_attachment
-- 3. ADDED index:
--    - idx_inventory_attachment_status (for query performance)
-- 4. ADDED UNIQUE constraints (prevent duplicates):
--    - uk_unit_attachment: One unit can have one specific attachment
--    - uk_unit_charger: One unit can have one specific charger
--    - uk_unit_battery: One unit can have one specific battery
--
-- MIGRATION SAFETY:
-- - All operations use IF EXISTS / IF NOT EXISTS checks
-- - Foreign keys dropped before columns
-- - UNIQUE constraints prevent data duplication
-- - No existing data modified (except duplicate cleanup in separate migration)
-- 
-- ============================================================================

-- ============================================================================
-- RELATED TABLES (for reference, not included in this migration)
-- ============================================================================
-- New tables created in Sprint 1-3:
-- - unit_billing_schedules (tracks per-unit billing)
-- - contract_amendments (tracks rate changes)
-- - amendment_unit_rates (per-unit rate changes)
-- - contract_renewal_workflow (renewal process tracking)
-- - contract_renewal_unit_map (unit mapping during renewal)
-- ============================================================================

SELECT 'Migration completed successfully!' AS status,
       'inventory_unit: 3 columns added' AS inventory_unit_changes,
       'inventory_attachment: 2 columns dropped, 1 index added, 3 UNIQUE keys added' AS inventory_attachment_changes;
