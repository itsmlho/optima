-- ========================================
-- Add workflow columns to inventory_unit
-- ========================================

USE optima_db;

-- Add workflow columns to inventory_unit
ALTER TABLE `inventory_unit` 
ADD COLUMN `di_workflow_id` INT(11) NULL AFTER `delivery_instruction_id`,
ADD COLUMN `workflow_status` VARCHAR(50) NULL AFTER `di_workflow_id`,
ADD COLUMN `contract_disconnect_date` DATETIME NULL AFTER `workflow_status`,
ADD COLUMN `contract_disconnect_stage` VARCHAR(50) NULL AFTER `contract_disconnect_date`;

-- Add indexes
ALTER TABLE `inventory_unit`
ADD INDEX `idx_unit_workflow` (`di_workflow_id`),
ADD INDEX `idx_unit_workflow_status` (`workflow_status`);

SELECT 'Workflow columns added to inventory_unit successfully!' as Result;