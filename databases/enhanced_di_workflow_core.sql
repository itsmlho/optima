-- ========================================
-- Enhanced DI Workflow: Core Tables Only (No FK)
-- Create tables without foreign keys first
-- ========================================

USE optima_db;

-- Create tables without foreign keys
CREATE TABLE IF NOT EXISTS `unit_workflow_log` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `unit_id` INT(10) UNSIGNED NOT NULL,
  `di_id` INT(11) NOT NULL,
  `stage` VARCHAR(50) NOT NULL,
  `jenis_perintah` VARCHAR(20) NOT NULL,
  `old_status` VARCHAR(50) NULL,
  `new_status` VARCHAR(50) NULL,
  `notes` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT(11) NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_unit_workflow_unit` (`unit_id`),
  INDEX `idx_unit_workflow_di` (`di_id`),
  INDEX `idx_unit_workflow_stage` (`stage`),
  INDEX `idx_unit_workflow_jenis` (`jenis_perintah`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `contract_disconnection_log` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `kontrak_id` INT(11) NOT NULL,
  `unit_id` INT(10) UNSIGNED NOT NULL,
  `stage` VARCHAR(50) NOT NULL,
  `reason` VARCHAR(100) NULL,
  `old_kontrak_id` INT(11) NULL,
  `new_kontrak_id` INT(11) NULL,
  `disconnected_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `disconnected_by` INT(11) NULL,
  `notes` TEXT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_disconnect_kontrak` (`kontrak_id`),
  INDEX `idx_disconnect_unit` (`unit_id`),
  INDEX `idx_disconnect_stage` (`stage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `di_workflow_stages` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `di_id` INT(11) NOT NULL,
  `stage_code` VARCHAR(50) NOT NULL,
  `stage_name` VARCHAR(100) NOT NULL,
  `status` ENUM('PENDING','IN_PROGRESS','COMPLETED','SKIPPED') DEFAULT 'PENDING',
  `started_at` DATETIME NULL,
  `completed_at` DATETIME NULL,
  `notes` TEXT NULL,
  `approved_by` INT(11) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_workflow_stages_di` (`di_id`),
  INDEX `idx_workflow_stages_code` (`stage_code`),
  INDEX `idx_workflow_stages_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `unit_replacement_log` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `di_id` INT(11) NOT NULL,
  `old_unit_id` INT(10) UNSIGNED NOT NULL,
  `new_unit_id` INT(10) UNSIGNED NOT NULL,
  `kontrak_id` INT(11) NOT NULL,
  `stage` VARCHAR(50) NOT NULL,
  `replacement_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `replaced_by` INT(11) NULL,
  `notes` TEXT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_replacement_di` (`di_id`),
  INDEX `idx_replacement_old_unit` (`old_unit_id`),
  INDEX `idx_replacement_new_unit` (`new_unit_id`),
  INDEX `idx_replacement_kontrak` (`kontrak_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add workflow columns to inventory_unit if they don't exist
SET @sql = CONCAT('ALTER TABLE inventory_unit ADD COLUMN di_workflow_id INT(11) NULL AFTER delivery_instruction_id');
SET @sql_check = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_NAME = 'inventory_unit' AND COLUMN_NAME = 'di_workflow_id' AND TABLE_SCHEMA = DATABASE());
SET @sql = IF(@sql_check > 0, 'SELECT "Column di_workflow_id already exists" as msg', @sql);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = CONCAT('ALTER TABLE inventory_unit ADD COLUMN workflow_status VARCHAR(50) NULL AFTER di_workflow_id');
SET @sql_check = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_NAME = 'inventory_unit' AND COLUMN_NAME = 'workflow_status' AND TABLE_SCHEMA = DATABASE());
SET @sql = IF(@sql_check > 0, 'SELECT "Column workflow_status already exists" as msg', @sql);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = CONCAT('ALTER TABLE inventory_unit ADD COLUMN contract_disconnect_date DATETIME NULL AFTER workflow_status');
SET @sql_check = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_NAME = 'inventory_unit' AND COLUMN_NAME = 'contract_disconnect_date' AND TABLE_SCHEMA = DATABASE());
SET @sql = IF(@sql_check > 0, 'SELECT "Column contract_disconnect_date already exists" as msg', @sql);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = CONCAT('ALTER TABLE inventory_unit ADD COLUMN contract_disconnect_stage VARCHAR(50) NULL AFTER contract_disconnect_date');
SET @sql_check = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_NAME = 'inventory_unit' AND COLUMN_NAME = 'contract_disconnect_stage' AND TABLE_SCHEMA = DATABASE());
SET @sql = IF(@sql_check > 0, 'SELECT "Column contract_disconnect_stage already exists" as msg', @sql);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add indexes
CREATE INDEX IF NOT EXISTS `idx_unit_workflow` ON `inventory_unit` (`di_workflow_id`);
CREATE INDEX IF NOT EXISTS `idx_unit_workflow_status` ON `inventory_unit` (`workflow_status`);

-- Insert migration record
INSERT INTO `migrations` (`table_name`, `description`, `status`, `notes`, `executed_at`) VALUES
('enhanced_di_workflow_core', 'Enhanced DI Workflow - Core implementation', 'SUCCESS', 'Core workflow system created without foreign keys for compatibility', NOW());

SELECT 'Enhanced DI Workflow Core Created Successfully!' as Result;