-- Migration: Create unit_verification_history table
-- Date: 2026-03-13
-- Purpose: Track unit verification history for work orders

-- Create table if not exists
CREATE TABLE IF NOT EXISTS `unit_verification_history` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `unit_id` INT UNSIGNED NOT NULL COMMENT 'FK to inventory_unit.id_inventory_unit',
    `work_order_id` INT NOT NULL COMMENT 'FK to work_orders.id',
    `verified_by` INT NOT NULL COMMENT 'FK to employees.id (mechanic who verified)',
    `verified_at` DATETIME NOT NULL COMMENT 'Timestamp when verification was done',
    `verification_data` JSON NULL COMMENT 'Store all verification field values',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (`unit_id`) REFERENCES `inventory_unit`(`id_inventory_unit`) ON DELETE CASCADE,
    FOREIGN KEY (`work_order_id`) REFERENCES `work_orders`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`verified_by`) REFERENCES `employees`(`id`) ON DELETE RESTRICT,
    
    INDEX `idx_unit_id` (`unit_id`),
    INDEX `idx_work_order_id` (`work_order_id`),
    INDEX `idx_verified_by` (`verified_by`),
    INDEX `idx_verified_at` (`verified_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='History of unit verifications linked to work orders';

-- Insert sample comment for documentation
-- This table stores every unit verification performed during work order completion
-- Allows tracking when unit was last verified and by whom
