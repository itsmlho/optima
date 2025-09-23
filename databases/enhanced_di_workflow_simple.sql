-- ========================================
-- Enhanced DI Workflow: Core Implementation (Simple)
-- Create essential tables and columns
-- ========================================

USE optima_db;

-- Drop existing tables to start fresh
DROP TABLE IF EXISTS `unit_replacement_log`;
DROP TABLE IF EXISTS `di_workflow_stages`;
DROP TABLE IF EXISTS `contract_disconnection_log`;
DROP TABLE IF EXISTS `unit_workflow_log`;

-- Create core workflow tables
CREATE TABLE `unit_workflow_log` (
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
  INDEX `idx_unit_workflow_stage` (`stage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `contract_disconnection_log` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `kontrak_id` INT(11) NOT NULL,
  `unit_id` INT(10) UNSIGNED NOT NULL,
  `stage` VARCHAR(50) NOT NULL,
  `reason` VARCHAR(100) NULL,
  `disconnected_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `disconnected_by` INT(11) NULL,
  `notes` TEXT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_disconnect_kontrak` (`kontrak_id`),
  INDEX `idx_disconnect_unit` (`unit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `di_workflow_stages` (
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
  INDEX `idx_workflow_stages_code` (`stage_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `unit_replacement_log` (
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
  INDEX `idx_replacement_kontrak` (`kontrak_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SELECT 'Enhanced DI Workflow Tables Created Successfully!' as Result;