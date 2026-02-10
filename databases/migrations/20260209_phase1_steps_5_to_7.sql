-- ========================================
-- OPTIMA Rental Management System
-- Database Migration: Phase 1 - Only Steps 5-7
-- kontrak status already standardized (Step 4 done)
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

SELECT '══════════════════════════════════════════' AS '';
SELECT 'Phase 1 Migration - Steps 5-7' AS '';
SELECT '══════════════════════════════════════════' AS '';

-- Skip Step 4 - kontrak status already standardized
SELECT 'Step 4: SKIPPED' AS 'Status', 'kontrak.status already in English' AS 'Note';
SELECT status AS 'Current Values', COUNT(*) AS 'Count' FROM kontrak GROUP BY status;

-- ========================================
-- STEP 5: Standardize kontrak_unit status
-- ========================================

SELECT '----------------------------------------' AS '';
SELECT 'Step 5: Standardizing kontrak_unit status' AS '';
SELECT '----------------------------------------' AS '';

SELECT 'Before:' AS '', status, COUNT(*) AS count FROM kontrak_unit GROUP BY status;

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

SELECT 'After:' AS '', status, COUNT(*) AS count FROM kontrak_unit GROUP BY status;
SELECT 'Step 5: COMPLETED ✓' AS 'Status';

-- ========================================
-- STEP 6: Add helpful indexes
-- ========================================

SELECT '----------------------------------------' AS '';
SELECT 'Step 6: Adding performance indexes' AS '';
SELECT '----------------------------------------' AS '';

-- Check and add idx_dates if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'kontrak' AND INDEX_NAME = 'idx_dates');

SELECT IF(@idx_exists = 0, 'Creating idx_dates...', 'idx_dates already exists - skipping') AS 'Action';

SET @sql = IF(@idx_exists = 0, 
    'ALTER TABLE kontrak ADD INDEX idx_dates (tanggal_mulai, tanggal_berakhir)',
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add idx_jenis_sewa if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'kontrak' AND INDEX_NAME = 'idx_jenis_sewa');

SELECT IF(@idx_exists = 0, 'Creating idx_jenis_sewa...', 'idx_jenis_sewa already exists - skipping') AS 'Action';

SET @sql = IF(@idx_exists = 0,
    'ALTER TABLE kontrak ADD INDEX idx_jenis_sewa (jenis_sewa)',
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SELECT 'Step 6: COMPLETED ✓' AS 'Status';

-- ========================================
-- STEP 7: Update column comments for documentation
-- ========================================

SELECT '----------------------------------------' AS '';
SELECT 'Step 7: Adding column comments' AS '';
SELECT '----------------------------------------' AS '';

ALTER TABLE kontrak 
MODIFY COLUMN rental_type ENUM('CONTRACT','PO_ONLY','DAILY_SPOT') NOT NULL DEFAULT 'CONTRACT'
    COMMENT 'Rental classification: CONTRACT=formal contract with/without PO, PO_ONLY=PO-based no formal contract, DAILY_SPOT=daily rental no contract/PO',
MODIFY COLUMN customer_po_number VARCHAR(100) NULL
    COMMENT 'Customer Purchase Order number (external PO from customer, NOT internal PO marketing)',
MODIFY COLUMN jenis_sewa ENUM('BULANAN','HARIAN')
    COMMENT 'Billing period: BULANAN=monthly rate, HARIAN=daily rate',
MODIFY COLUMN status ENUM('ACTIVE','EXPIRED','PENDING','CANCELLED') 
    NOT NULL DEFAULT 'PENDING'
    COMMENT 'Contract status: ACTIVE=running, EXPIRED=ended naturally, PENDING=awaiting activation, CANCELLED=terminated early';

SELECT 'Step 7: COMPLETED ✓' AS 'Status';

-- ========================================
-- FINAL VERIFICATION & SUMMARY
-- ========================================

SELECT '════════════════════════════════════════════════' AS '';
SELECT 'PHASE 1 MIGRATION COMPLETE!' AS '';
SELECT '════════════════════════════════════════════════' AS '';
SELECT '' AS '';
SELECT '📊 SUMMARY REPORT' AS '';
SELECT '════════════════════════════════════════════════' AS '';

SELECT '' AS '';
SELECT '1️⃣  RENTAL TYPE DISTRIBUTION' AS '';
SELECT 
    rental_type AS 'Type',
    COUNT(*) AS 'Contracts',
    SUM(total_units) AS 'Units',
    CONCAT('Rp ', FORMAT(SUM(nilai_total), 0)) AS 'Total Value',
    CONCAT(ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM kontrak), 1), '%') AS 'Percentage'
FROM kontrak
GROUP BY rental_type;

SELECT '' AS '';
SELECT '2️⃣  CONTRACT STATUS DISTRIBUTION' AS '';
SELECT 
    status AS 'Status',
    COUNT(*) AS 'Count',
    CONCAT(ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM kontrak), 1), '%') AS 'Percentage'
FROM kontrak
GROUP BY status
ORDER BY FIELD(status, 'ACTIVE', 'PENDING', 'EXPIRED', 'CANCELLED');

SELECT '' AS '';
SELECT '3️⃣  UNIT STATUS DISTRIBUTION' AS '';
SELECT 
    status AS 'Status',
    COUNT(*) AS 'Count',
    CONCAT(ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM kontrak_unit), 1), '%') AS 'Percentage'
FROM kontrak_unit
GROUP BY status;

SELECT '' AS '';
SELECT '4️⃣  CUSTOMER PO DISTRIBUTION' AS '';
SELECT 
    IF(customer_po_number IS NOT NULL, 'With Customer PO', 'Without PO') AS 'Status',
    COUNT(*) AS 'Contracts',
    CONCAT(ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM kontrak), 1), '%') AS 'Percentage'
FROM kontrak
GROUP BY IF(customer_po_number IS NOT NULL, 'With Customer PO', 'Without PO');

SELECT '' AS '';
SELECT '════════════════════════════════════════════════' AS '';
SELECT NOW() AS 'Completed At';
SELECT '✅ All migration steps successful!' AS 'Status';
SELECT 'rental_type added | fields renamed | status standardized | indexes optimized | documentation updated' AS 'Changes';
SELECT '════════════════════════════════════════════════' AS '';
