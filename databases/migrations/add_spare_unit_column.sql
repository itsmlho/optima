-- ============================================================================
-- Migration: Add spare unit flag to quotation specifications
-- Purpose: Allow marking units as spare/backup units with no billing
-- Date: 2026-02-15
-- Author: System
-- ============================================================================

USE optima_ci;

-- Add is_spare_unit column to quotation_specifications
ALTER TABLE quotation_specifications
ADD COLUMN is_spare_unit TINYINT(1) NOT NULL DEFAULT 0 
COMMENT 'Flag for spare/backup units (0=billed, 1=not billed)' 
AFTER quantity;

-- Add index for faster filtering
CREATE INDEX idx_spare_unit ON quotation_specifications(is_spare_unit);

-- Verification query
SELECT 'Spare unit column added successfully!' as status;

SELECT 
    COLUMN_NAME,
    COLUMN_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT,
    COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'optima_ci'
  AND TABLE_NAME = 'quotation_specifications'
  AND COLUMN_NAME = 'is_spare_unit';
