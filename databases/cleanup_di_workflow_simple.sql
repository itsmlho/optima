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

-- Remove added columns from inventory_unit (ignore errors if not exist)
ALTER TABLE `inventory_unit` DROP COLUMN `di_workflow_id`;
ALTER TABLE `inventory_unit` DROP COLUMN `workflow_status`;
ALTER TABLE `inventory_unit` DROP COLUMN `contract_disconnect_date`;
ALTER TABLE `inventory_unit` DROP COLUMN `contract_disconnect_stage`;

-- Drop indexes (ignore errors if not exist)
ALTER TABLE `inventory_unit` DROP INDEX `idx_unit_workflow`;
ALTER TABLE `inventory_unit` DROP INDEX `idx_unit_workflow_status`;
ALTER TABLE `delivery_instructions` DROP INDEX `idx_delivery_instructions_workflow`;
ALTER TABLE `inventory_unit` DROP INDEX `idx_inventory_unit_kontrak_workflow`;

SET foreign_key_checks = 1;

SELECT 'Cleanup completed - ready for fresh migration' as Result;