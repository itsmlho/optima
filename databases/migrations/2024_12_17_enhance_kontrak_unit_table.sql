-- Migration: Enhance kontrak_unit table for workflow standardization
-- Date: December 17, 2025
-- Purpose: Add support for temporary replacements and maintenance workflows

USE optima_ci;

-- Step 1: Add new columns to kontrak_unit table (only if not exists)
SET @cols = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = 'optima_ci' 
      AND TABLE_NAME = 'kontrak_unit' 
      AND COLUMN_NAME = 'temporary_replacement_unit_id'
);

SET @sql = IF(@cols = 0, 
    'ALTER TABLE kontrak_unit 
    ADD COLUMN temporary_replacement_unit_id INT UNSIGNED NULL 
        COMMENT "Temp unit ID when original in maintenance",
    ADD COLUMN temporary_replacement_date DATETIME NULL 
        COMMENT "Date temporary replacement started",
    ADD COLUMN maintenance_reason VARCHAR(255) NULL 
        COMMENT "Reason for maintenance pull",
    ADD COLUMN relocation_from_location_id INT NULL 
        COMMENT "Previous location for TARIK_PINDAH_LOKASI tracking",
    ADD COLUMN relocation_to_location_id INT NULL 
        COMMENT "New location for TARIK_PINDAH_LOKASI tracking"',
    'SELECT "Columns already exist" AS status'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 2: Modify status ENUM to include new workflow states
ALTER TABLE kontrak_unit 
MODIFY COLUMN status ENUM(
    'AKTIF',
    'DITARIK',
    'DITUKAR',
    'NON_AKTIF',
    'MAINTENANCE',
    'UNDER_REPAIR',
    'TEMPORARILY_REPLACED',
    'TEMPORARY_ACTIVE',
    'TEMPORARY_ENDED'
) NOT NULL DEFAULT 'AKTIF'
COMMENT 'Contract unit status including temporary and maintenance states';

-- Step 3: Add foreign key constraints for new columns
ALTER TABLE kontrak_unit
ADD CONSTRAINT fk_kontrak_unit_original 
    FOREIGN KEY (original_unit_id) 
    REFERENCES inventory_unit(id_inventory_unit) 
    ON DELETE SET NULL,
ADD CONSTRAINT fk_kontrak_unit_temp_replacement 
    FOREIGN KEY (temporary_replacement_unit_id) 
    REFERENCES inventory_unit(id_inventory_unit) 
    ON DELETE SET NULL,
ADD CONSTRAINT fk_kontrak_unit_relocation_from 
    FOREIGN KEY (relocation_from_location_id) 
    REFERENCES customer_locations(id) 
    ON DELETE SET NULL,
ADD CONSTRAINT fk_kontrak_unit_relocation_to 
    FOREIGN KEY (relocation_to_location_id) 
    REFERENCES customer_locations(id) 
    ON DELETE SET NULL;

-- Step 4: Add indexes for performance
CREATE INDEX idx_kontrak_unit_is_temporary 
    ON kontrak_unit(is_temporary);
CREATE INDEX idx_kontrak_unit_original 
    ON kontrak_unit(original_unit_id);
CREATE INDEX idx_kontrak_unit_temp_replacement 
    ON kontrak_unit(temporary_replacement_unit_id);
CREATE INDEX idx_kontrak_unit_maintenance 
    ON kontrak_unit(maintenance_start);

-- Step 5: Add comments to table
ALTER TABLE kontrak_unit 
COMMENT = 'Contract-Unit junction table with support for temporary replacements and maintenance workflows';

-- Verification Query
SELECT 
    'kontrak_unit schema enhanced' AS status,
    COUNT(*) AS total_records,
    SUM(CASE WHEN is_temporary = TRUE THEN 1 ELSE 0 END) AS temporary_records
FROM kontrak_unit;

-- Show new columns
SHOW COLUMNS FROM kontrak_unit 
WHERE Field IN (
    'is_temporary', 
    'original_unit_id', 
    'temporary_replacement_unit_id',
    'temporary_replacement_date',
    'maintenance_start',
    'maintenance_reason',
    'relocation_from_location_id',
    'relocation_to_location_id',
    'status'
);
