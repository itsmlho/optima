-- ============================================================================
-- Migration: Add spare_quantity field to quotation specifications
-- Purpose: Allow specifying number of spare/backup units separately from billable quantity
-- Business Logic: 
--   - quantity = billable units (charged monthly)
--   - spare_quantity = backup units (not charged, for zero downtime)
--   - total_delivered = quantity + spare_quantity
-- Date: 2026-03-10
-- Author: System
-- ============================================================================

USE optima_ci;

-- Add spare_quantity column to quotation_specifications
ALTER TABLE quotation_specifications
ADD COLUMN spare_quantity INT NOT NULL DEFAULT 0 
COMMENT 'Number of spare/backup units (not billed, for operational continuity)' 
AFTER is_spare_unit;

-- Update existing records: if is_spare_unit = 1, move quantity to spare_quantity
-- This maintains backward compatibility with old spare unit logic
UPDATE quotation_specifications
SET spare_quantity = quantity,
    quantity = 0
WHERE is_spare_unit = 1;

-- Add comment for quantity column for clarity
ALTER TABLE quotation_specifications
MODIFY COLUMN quantity INT NOT NULL DEFAULT 1
COMMENT 'Number of billable units (charged monthly)';

-- Verification query
SELECT 'Spare quantity column added successfully!' as status;

SELECT 
    COLUMN_NAME,
    COLUMN_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT,
    COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'optima_ci'
  AND TABLE_NAME = 'quotation_specifications'
  AND COLUMN_NAME IN ('quantity', 'is_spare_unit', 'spare_quantity')
ORDER BY ORDINAL_POSITION;

-- Show sample data to verify migration
SELECT 
    id_specification,
    specification_name,
    quantity as billable_units,
    spare_quantity as spare_units,
    (quantity + spare_quantity) as total_units,
    is_spare_unit,
    unit_price,
    (quantity * unit_price) as total_billable_price
FROM quotation_specifications
LIMIT 10;
