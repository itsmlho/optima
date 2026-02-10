-- ========================================
-- OPTIMA Rental Management System
-- Database Migration: Phase 1 Continuation
-- Continue from Step 4 (Steps 1-3 already completed)
-- ========================================

-- ========================================
-- PRE-STEP: Handle kontrak table triggers
-- ========================================

-- Create kontrak_status_changes table if it doesn't exist (required by trigger)
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
-- STEP 4: Standardize status values (kontrak table)
-- ========================================

-- First expand ENUM to include both Indonesian and English values temporarily
ALTER TABLE kontrak
MODIFY COLUMN status ENUM(
    'Aktif','Berakhir','Pending','Dibatalkan',
    'ACTIVE','EXPIRED','CANCELLED'
) NOT NULL DEFAULT 'Pending';

-- Update existing data to English
UPDATE kontrak SET status = 
    CASE status
        WHEN 'Aktif' THEN 'ACTIVE'
        WHEN 'Berakhir' THEN 'EXPIRED'
        WHEN 'Pending' THEN 'PENDING'
        WHEN 'Dibatalkan' THEN 'CANCELLED'
        ELSE 'PENDING'
    END;

-- Verify before changing ENUM
SELECT 'Status distribution before ENUM change' AS Info, status, COUNT(*) AS count FROM kontrak GROUP BY status;

-- Now modify ENUM to only English values (data already updated)
ALTER TABLE kontrak
MODIFY COLUMN status ENUM('ACTIVE','EXPIRED','PENDING','CANCELLED') 
NOT NULL DEFAULT 'PENDING';

-- Add index for status filtering
-- Note: Skipping - idx_kontrak_status already exists

-- ========================================
-- STEP 5: Standardize status values (kontrak_unit table)
-- ========================================

-- Check current distinct statuses
SELECT 'Current kontrak_unit statuses' AS Info, status, COUNT(*) as count FROM kontrak_unit GROUP BY status;

-- First expand ENUM to include both Indonesian and English values
ALTER TABLE kontrak_unit
MODIFY COLUMN status ENUM(
    'AKTIF','DITARIK','DITUKAR','NON_AKTIF','MAINTENANCE','UNDER_REPAIR',
    'TEMPORARILY_REPLACED','TEMPORARY_ACTIVE','TEMPORARY_ENDED',
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
        ELSE 'ACTIVE'
    END;

-- Modify ENUM to English values
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
-- Note: Skipping - idx_kontrak_unit_status already exists

-- ========================================
-- STEP 6: Add helpful indexes for performance
-- ========================================
-- Note: Most indexes already exist from previous migrations
-- idx_kontrak_customer_location already exists
-- Adding missing indexes:
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
    'Rental Type Distribution' AS Report,
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
-- MIGRATION COMPLETE
-- ========================================
SELECT 
    'Migration Phase 1 Complete' AS Status,
    NOW() AS CompletedAt,
    'rental_type added, fields renamed, status standardized' AS Changes;
