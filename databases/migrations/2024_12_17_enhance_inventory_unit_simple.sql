-- Migration: Enhance inventory_unit table for workflow standardization (SIMPLIFIED)
-- Date: December 17, 2025
-- Purpose: Add support for temporary assignments and maintenance tracking

USE optima_ci;

-- Add columns one by one with error handling
ALTER TABLE inventory_unit ADD COLUMN is_temporary_assignment BOOLEAN DEFAULT FALSE COMMENT 'True if unit is temporary replacement';
ALTER TABLE inventory_unit ADD COLUMN maintenance_location VARCHAR(100) NULL COMMENT 'Workshop/location during maintenance';
ALTER TABLE inventory_unit ADD COLUMN contract_disconnect_stage VARCHAR(50) NULL COMMENT 'Stage when contract disconnected';
ALTER TABLE inventory_unit ADD COLUMN temporary_for_contract_id INT UNSIGNED NULL COMMENT 'Original contract ID if temporary';
ALTER TABLE inventory_unit ADD COLUMN expected_return_date DATETIME NULL COMMENT 'Expected return date for maintenance';

-- Backup workflow_status
ALTER TABLE inventory_unit ADD COLUMN workflow_status_backup VARCHAR(50) NULL;
UPDATE inventory_unit SET workflow_status_backup = workflow_status WHERE workflow_status_backup IS NULL;

-- Modify workflow_status ENUM
ALTER TABLE inventory_unit 
MODIFY COLUMN workflow_status ENUM(
    'TERSEDIA','STOCK_ASET','DISEWA','DALAM_PENGIRIMAN',
    'MAINTENANCE_IN_PROGRESS','MAINTENANCE_WITH_REPLACEMENT','UNDER_REPAIR',
    'RELOCATING','TEMPORARY_RENTAL','DECOMMISSIONED','RUSAK','HILANG'
) NULL;

-- Add FK constraint
ALTER TABLE inventory_unit
ADD CONSTRAINT fk_inventory_unit_temp_contract 
    FOREIGN KEY (temporary_for_contract_id) 
    REFERENCES kontrak(id) ON DELETE SET NULL;

-- Add indexes
CREATE INDEX idx_inv_is_temp ON inventory_unit(is_temporary_assignment);
CREATE INDEX idx_inv_maint_loc ON inventory_unit(maintenance_location);
CREATE INDEX idx_inv_disconnect ON inventory_unit(contract_disconnect_date);
CREATE INDEX idx_inv_temp_cont ON inventory_unit(temporary_for_contract_id);

-- Verification
SELECT 'inventory_unit enhanced' AS status,
    COUNT(*) AS total_units,
    SUM(CASE WHEN is_temporary_assignment = TRUE THEN 1 ELSE 0 END) AS temp_units
FROM inventory_unit;

SHOW COLUMNS FROM inventory_unit WHERE Field IN (
    'is_temporary_assignment','maintenance_location','contract_disconnect_stage',
    'temporary_for_contract_id','expected_return_date','workflow_status'
);
