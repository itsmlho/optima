-- Migration: Change hour_meter to support decimal values
-- Date: 2025-12-20
-- Purpose: Hour meter biasanya ada nilai desimal (contoh: 1250.5 hours)

-- Change inventory_unit.hour_meter from INT to DECIMAL(10,1)
ALTER TABLE `inventory_unit` 
MODIFY COLUMN `hour_meter` DECIMAL(10,1) DEFAULT NULL COMMENT 'Current hour meter reading (supports decimal)';

-- Change work_orders.hm from INT to DECIMAL(10,1)
ALTER TABLE `work_orders` 
MODIFY COLUMN `hm` DECIMAL(10,1) DEFAULT NULL COMMENT 'Hour Meter at time of work order (supports decimal)';
