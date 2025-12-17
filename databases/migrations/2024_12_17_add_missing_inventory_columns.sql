-- Migration: Add missing columns to inventory_unit
-- Date: December 17, 2025

USE optima_ci;

-- Add only missing columns
ALTER TABLE inventory_unit 
ADD COLUMN temporary_for_contract_id INT UNSIGNED NULL COMMENT 'Original contract ID if temporary replacement';

ALTER TABLE inventory_unit 
ADD COLUMN expected_return_date DATETIME NULL COMMENT 'Expected return date for maintenance/temporary';

-- Modify workflow_status ENUM
ALTER TABLE inventory_unit 
MODIFY COLUMN workflow_status ENUM(
    'TERSEDIA','STOCK_ASET','DISEWA','DALAM_PENGIRIMAN',
    'MAINTENANCE_IN_PROGRESS','MAINTENANCE_WITH_REPLACEMENT','UNDER_REPAIR',
    'RELOCATING','TEMPORARY_RENTAL','DECOMMISSIONED','RUSAK','HILANG'
) NULL;

-- Add FK and indexes
ALTER TABLE inventory_unit
ADD CONSTRAINT fk_inventory_unit_temp_contract 
    FOREIGN KEY (temporary_for_contract_id) 
    REFERENCES kontrak(id) ON DELETE SET NULL;

CREATE INDEX idx_inv_temp_cont ON inventory_unit(temporary_for_contract_id);
CREATE INDEX idx_inv_expected_return ON inventory_unit(expected_return_date);

-- Verification
SELECT 'inventory_unit migration complete' AS status;
SHOW COLUMNS FROM inventory_unit WHERE Field LIKE '%temporary%' OR Field LIKE '%expected%';
