-- Migration: Enhance inventory_unit table for workflow standardization
-- Date: December 17, 2025
-- Purpose: Add support for temporary assignments and maintenance tracking

USE optima_ci;

-- Step 1: Check current workflow_status ENUM values
SELECT COLUMN_TYPE 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'optima_ci' 
  AND TABLE_NAME = 'inventory_unit' 
  AND COLUMN_NAME = 'workflow_status';

-- Step 2: Add new columns to inventory_unit table (only if not exists)
SET @cols = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = 'optima_ci' 
      AND TABLE_NAME = 'inventory_unit' 
      AND COLUMN_NAME IN ('is_temporary_assignment', 'maintenance_location', 'contract_disconnect_stage', 'temporary_for_contract_id', 'expected_return_date')
);

SET @sql = IF(@cols < 5, 
    'ALTER TABLE inventory_unit
    ADD COLUMN IF NOT EXISTS is_temporary_assignment BOOLEAN DEFAULT FALSE 
        COMMENT "True if unit is temporary replacement (TUKAR_MAINTENANCE)",
    ADD COLUMN IF NOT EXISTS maintenance_location VARCHAR(100) NULL 
        COMMENT "Workshop/location during maintenance",
    ADD COLUMN IF NOT EXISTS contract_disconnect_stage VARCHAR(50) NULL 
        COMMENT "Stage when contract disconnected (e.g., HABIS_KONTRAK, DECOMMISSION)",
    ADD COLUMN IF NOT EXISTS temporary_for_contract_id INT UNSIGNED NULL 
        COMMENT "Original contract ID if this is temporary replacement",
    ADD COLUMN IF NOT EXISTS expected_return_date DATETIME NULL 
        COMMENT "Expected return date for maintenance/temporary assignments"',
    'SELECT "All columns already exist" AS status'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 3: Modify workflow_status ENUM to include new states
-- Backup existing workflow_status before modification
SET @backup_col = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = 'optima_ci' 
      AND TABLE_NAME = 'inventory_unit' 
      AND COLUMN_NAME = 'workflow_status_backup'
);

SET @sql_backup = IF(@backup_col = 0, 
    'ALTER TABLE inventory_unit ADD COLUMN workflow_status_backup VARCHAR(50) NULL',
    'SELECT "Backup column exists" AS status'
);

PREPARE stmt FROM @sql_backup;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Backup current values
UPDATE inventory_unit 
SET workflow_status_backup = workflow_status 
WHERE workflow_status_backup IS NULL;

-- Modify workflow_status ENUM with new values
ALTER TABLE inventory_unit 
MODIFY COLUMN workflow_status ENUM(
    'TERSEDIA',
    'STOCK_ASET',
    'DISEWA',
    'DALAM_PENGIRIMAN',
    'MAINTENANCE_IN_PROGRESS',
    'MAINTENANCE_WITH_REPLACEMENT',
    'UNDER_REPAIR',
    'RELOCATING',
    'TEMPORARY_RENTAL',
    'DECOMMISSIONED',
    'RUSAK',
    'HILANG'
) NULL
COMMENT 'Unit workflow status including maintenance and temporary states';

-- Step 4: Add foreign key constraint for temporary contract tracking
ALTER TABLE inventory_unit
ADD CONSTRAINT fk_inventory_unit_temp_contract 
    FOREIGN KEY (temporary_for_contract_id) 
    REFERENCES kontrak(id) 
    ON DELETE SET NULL;

-- Step 5: Add indexes for performance
CREATE INDEX idx_inventory_unit_is_temporary 
    ON inventory_unit(is_temporary_assignment);
CREATE INDEX idx_inventory_unit_maintenance_location 
    ON inventory_unit(maintenance_location);
CREATE INDEX idx_inventory_unit_contract_disconnect 
    ON inventory_unit(contract_disconnect_date);
CREATE INDEX idx_inventory_unit_temp_contract 
    ON inventory_unit(temporary_for_contract_id);

-- Step 6: Add comments to table
ALTER TABLE inventory_unit 
COMMENT = 'Inventory unit table with support for temporary assignments and maintenance workflows';

-- Verification Query
SELECT 
    'inventory_unit schema enhanced' AS status,
    COUNT(*) AS total_units,
    SUM(CASE WHEN is_temporary_assignment = TRUE THEN 1 ELSE 0 END) AS temporary_units,
    SUM(CASE WHEN maintenance_location IS NOT NULL THEN 1 ELSE 0 END) AS units_in_maintenance
FROM inventory_unit;

-- Show new columns
SHOW COLUMNS FROM inventory_unit 
WHERE Field IN (
    'is_temporary_assignment',
    'maintenance_location',
    'contract_disconnect_date',
    'contract_disconnect_stage',
    'temporary_for_contract_id',
    'expected_return_date',
    'workflow_status',
    'workflow_status_backup'
);

-- Display existing workflow_status values to ensure migration safety
SELECT DISTINCT workflow_status, COUNT(*) as count
FROM inventory_unit
GROUP BY workflow_status
ORDER BY count DESC;
