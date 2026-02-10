-- ========================================
-- OPTIMA Rental Management System
-- Database Migration: Phase 1 Final Completion
-- Assumes Steps 1-3 complete and kontrak status already fixed
-- ========================================

-- Create kontrak_status_changes table if needed (required by triggers)
CREATE TABLE IF NOT EXISTS kontrak_status_changes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kontrak_id INT UNSIGNED NOT NULL,
    old_status VARCHAR(50),
    new_status VARCHAR(50),
    changed_at DATETIME NOT NULL,
    UNIQUE KEY uk_kontrak_change (kontrak_id, changed_at),
    KEY idx_kontrak_id (kontrak_id)
) ENGINE=InnoDB;

-- Verify current kontrak status distribution
SELECT 'Current kontrak status' AS Report, status, COUNT(*) as count FROM kontrak GROUP BY status;

-- ========================================
-- STEP 4: Finalize kontrak status ENUM (if not already done)
-- ========================================

-- Check if ENUM is already English-only
SET @enum_values = (SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'kontrak' AND COLUMN_NAME = 'status');

SELECT CONCAT('Current ENUM: ', @enum_values) AS Info;

-- If ENUM still has Indonesian values, convert it
-- This query checks if 'Aktif' exists in ENUM
SET @has_indonesian = (@enum_values LIKE '%Aktif%');

-- Only modify if still has Indonesian values
-- We'll expand first to include both

ALTER TABLE kontrak
MODIFY COLUMN status ENUM(
    'Aktif','Berakhir','Pending','Dibatalkan',
    'ACTIVE','EXPIRED','PENDING','CANCELLED'
) NOT NULL DEFAULT 'PENDING';

-- Convert any remaining Indonesian values to English
UPDATE kontrak SET status = 
    CASE status
        WHEN 'Aktif' THEN 'ACTIVE'
        WHEN 'Berakhir' THEN 'EXPIRED'
        WHEN 'Pending' THEN 'PENDING'
        WHEN 'Dibatalkan' THEN 'CANCELLED'
        ELSE status  -- Keep English values as-is
    END;

-- Finalize to English-only ENUM
ALTER TABLE kontrak
MODIFY COLUMN status ENUM('ACTIVE','EXPIRED','PENDING','CANCELLED') 
NOT NULL DEFAULT 'PENDING'
COMMENT 'Contract status: ACTIVE=running, EXPIRED=ended naturally, PENDING=awaiting activation, CANCELLED=terminated early';

SELECT 'Kontrak status standardized' AS Report, status, COUNT(*) FROM kontrak GROUP BY status;

-- ========================================
-- STEP 5: Standardize kontrak_unit status
-- ========================================

SELECT 'Current kontrak_unit status' AS Report, status, COUNT(*) FROM kontrak_unit GROUP BY status;

-- Expand ENUM to include English values
ALTER TABLE kontrak_unit
MODIFY COLUMN status ENUM(
    'AKTIF','DITARIK','DITUKAR','NON_AKTIF','MAINTENANCE','UNDER_REPAIR',
    'TEMPORARILY_REPLACED','TEMPORARY_ACTIVE','TEMPORARY_ENDED',
    'ACTIVE','PULLED','REPLACED','INACTIVE','TEMP_REPLACED','TEMP_ACTIVE','TEMP_ENDED'
) NOT NULL DEFAULT 'AKTIF';

-- Convert to English
UPDATE kontrak_unit SET status = 
    CASE status
        WHEN 'AKTIF' THEN 'ACTIVE'
        WHEN 'DITARIK' THEN 'PULLED'
        WHEN 'DITUKAR' THEN 'REPLACED'
        WHEN 'NON_AKTIF' THEN 'INACTIVE'
        WHEN 'MAINTENANCE' THEN 'MAINTENANCE'
        WHEN 'UNDER_REPAIR' THEN 'UNDER_REPAIR'
        WHEN 'TEMPORARILY_REPLACED' THEN 'TEMP_REPLACED'
        WHEN 'TEMPORARY_ACTIVE' THEN 'TEMP_ACTIVE'
        WHEN 'TEMPORARY_ENDED' THEN 'TEMP_ENDED'
        ELSE status
    END;

-- Finalize to English-only ENUM
ALTER TABLE kontrak_unit
MODIFY COLUMN status ENUM(
    'ACTIVE','PULLED','REPLACED','INACTIVE','MAINTENANCE',
    'UNDER_REPAIR','TEMP_REPLACED','TEMP_ACTIVE','TEMP_ENDED'
) NOT NULL DEFAULT 'ACTIVE'
COMMENT 'Unit status: ACTIVE=in use, PULLED=returned, REPLACED=swapped, INACTIVE=not in use, MAINTENANCE=servicing, UNDER_REPAIR=being fixed, TEMP_REPLACED=temporarily swapped, TEMP_ACTIVE=temp unit active, TEMP_ENDED=temp period finished';

SELECT 'Kontrak_unit status standardized' AS Report, status, COUNT(*) FROM kontrak_unit GROUP BY status;

-- ========================================
-- STEP 6: Add helpful indexes (skip if exist)
-- ========================================

-- Check and add idx_dates if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'kontrak' AND INDEX_NAME = 'idx_dates');

SET @sql = IF(@idx_exists = 0, 
    'ALTER TABLE kontrak ADD INDEX idx_dates (tanggal_mulai, tanggal_berakhir)',
    'SELECT "idx_dates already exists" AS Info');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add idx_jenis_sewa if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'kontrak' AND INDEX_NAME = 'idx_jenis_sewa');

SET @sql = IF(@idx_exists = 0,
    'ALTER TABLE kontrak ADD INDEX idx_jenis_sewa (jenis_sewa)',
    'SELECT "idx_jenis_sewa already exists" AS Info');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ========================================
-- STEP 7: Update column comments
-- ========================================

ALTER TABLE kontrak 
MODIFY COLUMN rental_type ENUM('CONTRACT','PO_ONLY','DAILY_SPOT') NOT NULL DEFAULT 'CONTRACT'
    COMMENT 'Rental classification: CONTRACT=formal contract with/without PO, PO_ONLY=PO-based no formal contract, DAILY_SPOT=daily rental no contract/PO',
MODIFY COLUMN customer_po_number VARCHAR(100) NULL
    COMMENT 'Customer Purchase Order number (external PO from customer, NOT internal PO marketing)',
MODIFY COLUMN jenis_sewa ENUM('BULANAN','HARIAN')
    COMMENT 'Billing period: BULANAN=monthly rate, HARIAN=daily rate';

-- ========================================
-- FINAL VERIFICATION
-- ========================================

SELECT '═══════════════════════════════════' AS '';
SELECT 'MIGRATION COMPLETE - FINAL SUMMARY' AS '';
SELECT '═══════════════════════════════════' AS '';

SELECT 
    rental_type AS 'Rental Type',
    COUNT(*) AS 'Contracts',
    SUM(total_units) AS 'Units',
    CONCAT('Rp ', FORMAT(SUM(nilai_total), 0)) AS 'Total Value'
FROM kontrak
GROUP BY rental_type;

SELECT 
    status AS 'Kontrak Status',
    COUNT(*) AS 'Count',
    CONCAT(ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM kontrak), 1), '%') AS 'Percentage'
FROM kontrak
GROUP BY status;

SELECT 
    status AS 'Unit Status',
    COUNT(*) AS 'Count'
FROM kontrak_unit
GROUP BY status;

SELECT 
    IF(customer_po_number IS NOT NULL, 'With Customer PO', 'Without PO') AS 'PO Status',
    COUNT(*) AS 'Contracts',
    CONCAT(ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM kontrak), 1), '%') AS 'Percentage'
FROM kontrak
GROUP BY IF(customer_po_number IS NOT NULL, 'With Customer PO', 'Without PO');

SELECT NOW() AS 'Completed At', 'Phase 1 Migration Successful!' AS 'Status';
