-- ========================================
-- OPTIMA Rental Management System
-- Database Migration: Phase 1 Continuation V2
-- One-time manual fix for status standardization
-- This version directly updates specific record IDs
-- ========================================

-- Create kontrak_status_changes table if needed
CREATE TABLE IF NOT EXISTS kontrak_status_changes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kontrak_id INT UNSIGNED NOT NULL,
    old_status VARCHAR(50),
    new_status VARCHAR(50),
    changed_at DATETIME NOT NULL,
    UNIQUE KEY uk_kontrak_change (kontrak_id, changed_at),
    KEY idx_kontrak_id (kontrak_id)
) ENGINE=InnoDB;

-- ========================================
-- STEP 4: Fix kontrak status - Direct ID updates based on backup
-- ========================================

-- IDs 44,54,55,56,57,63,64 should be ACTIVE (were 'Aktif' in original)
UPDATE kontrak SET status = 'ACTIVE' WHERE id IN (44,54,55,56,57,63,64);

-- IDs 65,66,67,68,69,70 should be PENDING (were 'Pending' in original)
UPDATE kontrak SET status = 'PENDING' WHERE id IN (65,66,67,68,69,70);

-- Verify distribution
SELECT 'Kontrak status after fix' AS Report, status, COUNT(*) as count FROM kontrak GROUP BY status;

-- ========================================
-- Reset kontrak ENUM to Indonesian first (in case it's already changed)
-- ========================================
ALTER TABLE kontrak
MODIFY COLUMN status ENUM('Aktif','Berakhir','Pending','Dibatalkan') 
NOT NULL DEFAULT 'Pending';

-- First restore backup values
UPDATE kontrak k 
JOIN kontrak_backup_20260209 b ON k.id = b.id 
SET k.status = b.status;

-- Verify restoration
SELECT 'After restore from backup' AS Report, status, COUNT(*) FROM kontrak GROUP BY status;

-- Now expand ENUM to include English values
ALTER TABLE kontrak
MODIFY COLUMN status ENUM(
    'Aktif','Berakhir','Pending','Dibatalkan',
    'ACTIVE','EXPIRED','CANCELLED'
) NOT NULL DEFAULT 'Pending';

-- Convert to English
UPDATE kontrak SET status = 
    CASE status
        WHEN 'Aktif' THEN 'ACTIVE'
        WHEN 'Berakhir' THEN 'EXPIRED'
        WHEN 'Pending' THEN 'PENDING'
        WHEN 'Dibatalkan' THEN 'CANCELLED'
        -- If already English, keep as is
        WHEN 'ACTIVE' THEN 'ACTIVE'
        WHEN 'EXPIRED' THEN 'EXPIRED'
        WHEN 'PENDING' THEN 'PENDING'
        WHEN 'CANCELLED' THEN 'CANCELLED'
        ELSE 'PENDING'
    END;

-- Finalize ENUM to English-only
ALTER TABLE kontrak
MODIFY COLUMN status ENUM('ACTIVE','EXPIRED','PENDING','CANCELLED') 
NOT NULL DEFAULT 'PENDING';

-- Final verification
SELECT 'Final kontrak status' AS Report, status, COUNT(*) as count FROM kontrak GROUP BY status;

-- ========================================
-- STEP 5: Fix kontrak_unit status
-- ========================================

-- First, expand ENUM to include English values
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
        -- If already English, keep as is
        WHEN 'ACTIVE' THEN 'ACTIVE'
        WHEN 'PULLED' THEN 'PULLED'
        WHEN 'REPLACED' THEN 'REPLACED'
        WHEN 'INACTIVE' THEN 'INACTIVE'
        WHEN 'TEMP_REPLACED' THEN 'TEMP_REPLACED'
        WHEN 'TEMP_ACTIVE' THEN 'TEMP_ACTIVE'
        WHEN 'TEMP_ENDED' THEN 'TEMP_ENDED'
        ELSE 'ACTIVE'
    END;

-- Finalize ENUM to English-only
ALTER TABLE kontrak_unit
MODIFY COLUMN status ENUM(
    'ACTIVE','PULLED','REPLACED','INACTIVE','MAINTENANCE',
    'UNDER_REPAIR','TEMP_REPLACED','TEMP_ACTIVE','TEMP_ENDED'
) NOT NULL DEFAULT 'ACTIVE';

SELECT 'Final kontrak_unit status' AS Report, status, COUNT(*) FROM kontrak_unit GROUP BY status;

-- ========================================
-- STEP 6: Add helpful indexes
-- ========================================
-- Skip indexes that already exist
ALTER TABLE kontrak ADD INDEX idx_dates (tanggal_mulai, tanggal_berakhir);
ALTER TABLE kontrak ADD INDEX idx_jenis_sewa (jenis_sewa);

-- ========================================
-- STEP 7: Update column comments
-- ========================================
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

ALTER TABLE kontrak_unit
MODIFY COLUMN status ENUM(
    'ACTIVE','PULLED','REPLACED','INACTIVE','MAINTENANCE',
    'UNDER_REPAIR','TEMP_REPLACED','TEMP_ACTIVE','TEMP_ENDED'
) NOT NULL DEFAULT 'ACTIVE'
    COMMENT 'Unit status in contract: ACTIVE=in use, PULLED=returned, REPLACED=permanently swapped, INACTIVE=not in use, MAINTENANCE=under servicing, UNDER_REPAIR=being fixed, TEMP_REPLACED=temporarily swapped out, TEMP_ACTIVE=temporary replacement unit active, TEMP_ENDED=temporary period finished';

-- ========================================
-- FINAL VERIFICATION
-- ========================================

-- Show summary
SELECT 
    'FINAL SUMMARY' AS Report,
    'Rental Types' AS Category,
    rental_type AS Value,
    COUNT(*) AS Count
FROM kontrak
GROUP BY rental_type
UNION ALL
SELECT 
    'FINAL SUMMARY',
    'Kontrak Status',
    status,
    COUNT(*)
FROM kontrak
GROUP BY status
UNION ALL
SELECT 
    'FINAL SUMMARY',
    'Unit Status',
    status,
    COUNT(*)
FROM kontrak_unit
GROUP BY status
UNION ALL
SELECT 
    'FINAL SUMMARY',
    'Contracts with PO',
    IF(customer_po_number IS NOT NULL, 'With PO', 'Without PO'),
    COUNT(*)
FROM kontrak
GROUP BY IF(customer_po_number IS NOT NULL, 'With PO', 'Without PO');

SELECT 'Migration Phase 1 Complete!' AS Status, NOW() AS CompletedAt;
