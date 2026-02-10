-- ========================================
-- OPTIMA Rental Management System
-- Database Migration: Phase 1 - Rental Type Classification
-- Date: 2026-02-09
-- Description: Add rental_type distinction, rename fields, standardize status values
-- ========================================

-- SAFETY: Create backups before making changes
CREATE TABLE IF NOT EXISTS kontrak_backup_20260209 AS SELECT * FROM kontrak;
CREATE TABLE IF NOT EXISTS kontrak_unit_backup_20260209 AS SELECT * FROM kontrak_unit;

-- Verify backups
SELECT 'Backup created' AS status, COUNT(*) AS kontrak_records FROM kontrak_backup_20260209;
SELECT 'Backup created' AS status, COUNT(*) AS kontrak_unit_records FROM kontrak_unit_backup_20260209;

-- ========================================
-- STEP 1: Add rental_type column
-- ========================================
ALTER TABLE kontrak 
ADD COLUMN rental_type ENUM('CONTRACT','PO_ONLY','DAILY_SPOT') NOT NULL DEFAULT 'CONTRACT' 
COMMENT 'Type: CONTRACT=formal contract+PO, PO_ONLY=PO without formal contract, DAILY_SPOT=short-term no PO'
AFTER no_kontrak;

-- Add index for performance
ALTER TABLE kontrak ADD INDEX idx_rental_type (rental_type);

-- ========================================
-- STEP 2: Classify existing data
-- ========================================
-- Update existing contracts based on business logic:
-- - Has PO + Monthly = CONTRACT (formal contract with PO)
-- - No PO + Monthly = CONTRACT (treat as formal contract, can be reclassified later)
-- - Daily = DAILY_SPOT (short-term rental)

UPDATE kontrak 
SET rental_type = CASE 
    WHEN no_po_marketing IS NOT NULL AND jenis_sewa = 'BULANAN' THEN 'CONTRACT'
    WHEN no_po_marketing IS NULL AND jenis_sewa = 'BULANAN' THEN 'CONTRACT'
    WHEN jenis_sewa = 'HARIAN' THEN 'DAILY_SPOT'
    ELSE 'CONTRACT'
END;

-- Verify classification
SELECT 
    rental_type,
    jenis_sewa,
    COUNT(*) AS count,
    SUM(CASE WHEN no_po_marketing IS NOT NULL THEN 1 ELSE 0 END) AS with_po,
    SUM(CASE WHEN no_po_marketing IS NULL THEN 1 ELSE 0 END) AS without_po
FROM kontrak
GROUP BY rental_type, jenis_sewa;

-- ========================================
-- STEP 3: Rename confusing column names
-- ========================================
ALTER TABLE kontrak 
CHANGE COLUMN no_po_marketing customer_po_number VARCHAR(100)
COMMENT 'Customer PO number (optional for daily rentals, can be NULL)';

-- Add index for PO lookups
ALTER TABLE kontrak ADD INDEX idx_customer_po (customer_po_number);

-- ========================================
-- STEP 4: Standardize status values (kontrak table)
-- ========================================
-- Strategy: Update data first (case-insensitive), then modify ENUM

-- Update existing data to uppercase English directly
UPDATE kontrak SET status = 
    CASE UPPER(status)
        WHEN 'AKTIF' THEN 'ACTIVE'
        WHEN 'BERAKHIR' THEN 'EXPIRED'
        WHEN 'PENDING' THEN 'PENDING'
        WHEN 'DIBATALKAN' THEN 'CANCELLED'
        ELSE 'PENDING'
    END;

-- Now modify ENUM to only English values (data already updated)
ALTER TABLE kontrak
MODIFY COLUMN status ENUM('ACTIVE','EXPIRED','PENDING','CANCELLED') 
NOT NULL DEFAULT 'PENDING';

-- Add index for status filtering
ALTER TABLE kontrak ADD INDEX idx_status (status);

-- ========================================
-- STEP 5: Standardize status values (kontrak_unit table)
-- ========================================
-- First, add English equivalents to ENUM
ALTER TABLE kontrak_unit
MODIFY COLUMN status ENUM(
    'AKTIF','DITARIK','DITUKAR','NON_AKTIF','MAINTENANCE',
    'UNDER_REPAIR','TEMPORARILY_REPLACED','TEMPORARY_ACTIVE','TEMPORARY_ENDED',
    'ACTIVE','PULLED','REPLACED','INACTIVE','TEMP_REPLACED','TEMP_ACTIVE','TEMP_ENDED'
) NOT NULL DEFAULT 'AKTIF';

-- Update existing data to English
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

-- Remove old Indonesian values from ENUM
ALTER TABLE kontrak_unit
MODIFY COLUMN status ENUM(
    'ACTIVE',
    'PULLED',
    'REPLACED',
    'INACTIVE',
    'MAINTENANCE',
    'UNDER_REPAIR',
    'TEMP_REPLACED',
    'TEMP_ACTIVE',
    'TEMP_ENDED'
) NOT NULL DEFAULT 'ACTIVE';

-- Add index for status filtering
ALTER TABLE kontrak_unit ADD INDEX idx_unit_status (status);

-- ========================================
-- STEP 6: Add helpful indexes for performance
-- ========================================
ALTER TABLE kontrak ADD INDEX idx_customer_location (customer_location_id);
ALTER TABLE kontrak ADD INDEX idx_dates (tanggal_mulai, tanggal_berakhir);
ALTER TABLE kontrak ADD INDEX idx_jenis_sewa (jenis_sewa);

-- ========================================
-- STEP 7: Update column comments for documentation
-- ========================================
ALTER TABLE kontrak 
MODIFY COLUMN jenis_sewa ENUM('BULANAN','HARIAN')
    COMMENT 'Billing period: BULANAN=monthly rate, HARIAN=daily rate',
MODIFY COLUMN status ENUM('ACTIVE','EXPIRED','PENDING','CANCELLED') 
    NOT NULL DEFAULT 'PENDING'
    COMMENT 'Contract status: ACTIVE=currently active, EXPIRED=ended, PENDING=awaiting activation, CANCELLED=terminated early';

ALTER TABLE kontrak_unit
MODIFY COLUMN status ENUM(
    'ACTIVE','PULLED','REPLACED','INACTIVE','MAINTENANCE',
    'UNDER_REPAIR','TEMP_REPLACED','TEMP_ACTIVE','TEMP_ENDED'
) NOT NULL DEFAULT 'ACTIVE'
    COMMENT 'Unit status in contract: ACTIVE=in use, PULLED=returned, REPLACED=permanently swapped, INACTIVE=not in use, MAINTENANCE=under servicing, UNDER_REPAIR=being fixed, TEMP_REPLACED=temporarily swapped out, TEMP_ACTIVE=temporary replacement unit active, TEMP_ENDED=temporary period finished';

-- ========================================
-- VERIFICATION QUERIES
-- ========================================

-- Verify rental_type distribution
SELECT 
    'Rental Type Distribution' ASReport,
    rental_type,
    COUNT(*) AS total_contracts,
    SUM(total_units) AS total_units_assigned,
    SUM(nilai_total) AS total_revenue,
    ROUND(AVG(nilai_total), 2) AS avg_contract_value
FROM kontrak
GROUP BY rental_type
ORDER BY total_contracts DESC;

-- Verify status standardization
SELECT 
    'Contract Status Distribution' AS Report,
    status,
    COUNT(*) AS count,
    SUM(total_units) AS units
FROM kontrak
GROUP BY status;

SELECT 
    'Unit Status Distribution' AS Report,
    status,
    COUNT(*) AS count
FROM kontrak_unit
GROUP BY status;

-- Verify renamed column
SELECT 
    'Customer PO Distribution' AS Report,
    COUNT(*) AS total_contracts,
    SUM(CASE WHEN customer_po_number IS NOT NULL THEN 1 ELSE 0 END) AS with_po,
    SUM(CASE WHEN customer_po_number IS NULL THEN 1 ELSE 0 END) AS without_po,
    ROUND(SUM(CASE WHEN customer_po_number IS NOT NULL THEN 1 ELSE 0 END) * 100.0 / COUNT(*), 2) AS po_percentage
FROM kontrak;

-- ========================================
-- ROLLBACK INSTRUCTIONS (if needed)
-- ========================================
/*
-- To rollback, execute these commands:

DROP TABLE IF EXISTS kontrak;
DROP TABLE IF EXISTS kontrak_unit;

RENAME TABLE kontrak_backup_20260209 TO kontrak;
RENAME TABLE kontrak_unit_backup_20260209 TO kontrak_unit;

-- Verify rollback
SELECT 'Rollback complete' AS status;
SELECT COUNT(*) AS kontrak_count FROM kontrak;
SELECT COUNT(*) AS kontrak_unit_count FROM kontrak_unit;
*/

-- ========================================
-- MIGRATION COMPLETE
-- ========================================
SELECT 
    'Migration Phase 1 Complete' AS Status,
    NOW() AS CompletedAt,
    'rental_type added, fields renamed, status standardized' AS Changes;
