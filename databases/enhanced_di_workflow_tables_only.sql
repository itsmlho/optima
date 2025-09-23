-- ========================================
-- Enhanced DI Workflow: Final Implementation
-- Safe migration that checks for existing columns
-- ========================================

USE optima_db;

-- Create tables first (these will be new)
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
  INDEX `idx_unit_workflow_jenis` (`jenis_perintah`),
  FOREIGN KEY (`unit_id`) REFERENCES `inventory_unit` (`id_inventory_unit`) ON DELETE CASCADE,
  FOREIGN KEY (`di_id`) REFERENCES `delivery_instructions` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
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
  INDEX `idx_disconnect_stage` (`stage`),
  FOREIGN KEY (`kontrak_id`) REFERENCES `kontrak` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`unit_id`) REFERENCES `inventory_unit` (`id_inventory_unit`) ON DELETE CASCADE,
  FOREIGN KEY (`disconnected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
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
  INDEX `idx_workflow_stages_status` (`status`),
  FOREIGN KEY (`di_id`) REFERENCES `delivery_instructions` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
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
  INDEX `idx_replacement_kontrak` (`kontrak_id`),
  FOREIGN KEY (`di_id`) REFERENCES `delivery_instructions` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`old_unit_id`) REFERENCES `inventory_unit` (`id_inventory_unit`) ON DELETE CASCADE,
  FOREIGN KEY (`new_unit_id`) REFERENCES `inventory_unit` (`id_inventory_unit`) ON DELETE CASCADE,
  FOREIGN KEY (`kontrak_id`) REFERENCES `kontrak` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`replaced_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert migration record
INSERT INTO `migrations` (`table_name`, `description`, `status`, `notes`, `executed_at`) VALUES
('enhanced_di_workflow_tables', 'Enhanced DI Workflow - Core tables created', 'SUCCESS', 'Core workflow tracking tables created successfully', NOW());

SELECT 'Enhanced DI Workflow Tables Created Successfully!' as Result;