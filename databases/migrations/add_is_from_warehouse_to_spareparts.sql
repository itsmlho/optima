-- =====================================================
-- Migration: Add is_from_warehouse to work_order_spareparts
-- Purpose: Differentiate warehouse stock vs non-warehouse (bekas/reuse) spareparts
-- Date: 2026-02-05
-- =====================================================

-- Add new column to track sparepart source
ALTER TABLE work_order_spareparts 
ADD COLUMN is_from_warehouse TINYINT(1) DEFAULT 1 
COMMENT '1=From Warehouse (stock), 0=Non-Warehouse (bekas/reuse)' 
AFTER is_additional;

-- Add index for reporting and filtering
CREATE INDEX idx_from_warehouse ON work_order_spareparts(is_from_warehouse);

-- Update existing records to default warehouse source (backward compatibility)
UPDATE work_order_spareparts 
SET is_from_warehouse = 1 
WHERE is_from_warehouse IS NULL;

-- Verification query
SELECT 
    COUNT(*) as total_records,
    SUM(CASE WHEN is_from_warehouse = 1 THEN 1 ELSE 0 END) as from_warehouse,
    SUM(CASE WHEN is_from_warehouse = 0 THEN 1 ELSE 0 END) as non_warehouse
FROM work_order_spareparts;
