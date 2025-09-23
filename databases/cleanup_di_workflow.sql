-- ========================================
-- Clean existing DI workflow additions and start fresh
-- ========================================

USE optima_db;

-- Drop foreign key constraints first if they exist
SET foreign_key_checks = 0;

-- Drop tables if they exist
DROP TABLE IF EXISTS `unit_replacement_log`;
DROP TABLE IF EXISTS `di_workflow_stages`;
DROP TABLE IF EXISTS `contract_disconnection_log`;
DROP TABLE IF EXISTS `unit_workflow_log`;

-- Drop views if they exist
DROP VIEW IF EXISTS `unit_workflow_status`;
DROP VIEW IF EXISTS `contract_unit_summary`;

-- Drop procedures if they exist
DROP PROCEDURE IF EXISTS `ProcessUnitTarik`;

-- Drop trigger if it exists
DROP TRIGGER IF EXISTS `tr_di_create_workflow_stages`;

-- Remove added columns from inventory_unit if they exist
SET @sql = '';
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_NAME = 'inventory_unit' 
AND COLUMN_NAME = 'di_workflow_id'
AND TABLE_SCHEMA = 'optima_db';

IF @col_exists > 0 THEN
    ALTER TABLE `inventory_unit` DROP COLUMN `di_workflow_id`;
END IF;

SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_NAME = 'inventory_unit' 
AND COLUMN_NAME = 'workflow_status'
AND TABLE_SCHEMA = 'optima_db';

IF @col_exists > 0 THEN
    ALTER TABLE `inventory_unit` DROP COLUMN `workflow_status`;
END IF;

SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_NAME = 'inventory_unit' 
AND COLUMN_NAME = 'contract_disconnect_date'
AND TABLE_SCHEMA = 'optima_db';

IF @col_exists > 0 THEN
    ALTER TABLE `inventory_unit` DROP COLUMN `contract_disconnect_date`;
END IF;

SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_NAME = 'inventory_unit' 
AND COLUMN_NAME = 'contract_disconnect_stage'
AND TABLE_SCHEMA = 'optima_db';

IF @col_exists > 0 THEN
    ALTER TABLE `inventory_unit` DROP COLUMN `contract_disconnect_stage`;
END IF;

-- Drop indexes if they exist
DROP INDEX IF EXISTS `idx_unit_workflow` ON `inventory_unit`;
DROP INDEX IF EXISTS `idx_unit_workflow_status` ON `inventory_unit`;
DROP INDEX IF EXISTS `idx_delivery_instructions_workflow` ON `delivery_instructions`;
DROP INDEX IF EXISTS `idx_inventory_unit_kontrak_workflow` ON `inventory_unit`;

SET foreign_key_checks = 1;

SELECT 'Cleanup completed - ready for fresh migration' as Result;