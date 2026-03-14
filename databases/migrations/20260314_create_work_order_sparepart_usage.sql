-- Migration: Create work_order_sparepart_usage table
-- Date: 2026-03-14
-- Purpose: Track actual sparepart usage from work orders for warehouse reporting

CREATE TABLE IF NOT EXISTS `work_order_sparepart_usage` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `work_order_sparepart_id` INT NOT NULL COMMENT 'FK to work_order_spareparts.id',
    `work_order_id` INT NOT NULL COMMENT 'FK to work_orders.id',
    `quantity_used` INT NOT NULL DEFAULT 0 COMMENT 'Actual quantity used in the work order',
    `quantity_returned` INT NOT NULL DEFAULT 0 COMMENT 'Quantity returned to warehouse',
    `usage_notes` TEXT NULL COMMENT 'Notes about the usage',
    `return_notes` TEXT NULL COMMENT 'Notes about the return',
    `used_at` DATETIME NULL COMMENT 'When the sparepart was actually used',
    `returned_at` DATETIME NULL COMMENT 'When the sparepart was returned',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_work_order_sparepart` (`work_order_sparepart_id`),
    INDEX `idx_work_order` (`work_order_id`),
    INDEX `idx_used_at` (`used_at`),
    CONSTRAINT `fk_usage_sparepart` FOREIGN KEY (`work_order_sparepart_id`) 
        REFERENCES `work_order_spareparts` (`id`) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_usage_work_order` FOREIGN KEY (`work_order_id`) 
        REFERENCES `work_orders` (`id`) 
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tracks actual sparepart usage from work orders';
