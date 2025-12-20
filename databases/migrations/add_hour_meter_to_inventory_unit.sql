-- Migration: Add hour_meter column to inventory_unit table
-- Date: 2025-01-XX
-- Purpose: Track current hour meter reading for each unit

-- Add hour_meter column
ALTER TABLE `inventory_unit` 
ADD COLUMN `hour_meter` INT(11) DEFAULT NULL COMMENT 'Current hour meter reading' 
AFTER `workflow_status`;

-- Add index for faster queries
ALTER TABLE `inventory_unit` 
ADD INDEX `idx_hour_meter` (`hour_meter`);

-- Optional: Update existing units with hour meter from their latest work order
UPDATE `inventory_unit` iu
INNER JOIN (
    SELECT 
        unit_id,
        MAX(hm) as latest_hm
    FROM `work_orders`
    WHERE hm IS NOT NULL
    GROUP BY unit_id
) wo ON iu.id_inventory_unit = wo.unit_id
SET iu.hour_meter = wo.latest_hm
WHERE iu.hour_meter IS NULL;
