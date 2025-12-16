-- Attachment Transfer Audit Log Table
-- Purpose: Track all attachment transfers (KANIBAL mode) and assignments
-- Date: 2024-12-16

CREATE TABLE IF NOT EXISTS `attachment_transfer_log` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `attachment_id` INT(11) NOT NULL COMMENT 'FK to inventory_attachment.id_inventory_attachment',
    `from_unit_id` INT(11) UNSIGNED DEFAULT NULL COMMENT 'Source unit (NULL for new assignment)',
    `to_unit_id` INT(11) UNSIGNED NOT NULL COMMENT 'Target unit',
    `transfer_type` ENUM('NEW_ASSIGNMENT', 'TRANSFER', 'DETACH') NOT NULL DEFAULT 'NEW_ASSIGNMENT',
    `triggered_by` VARCHAR(50) NOT NULL COMMENT 'PERSIAPAN_UNIT, KANIBAL_FABRIKASI, PAINTING, etc',
    `spk_id` INT(11) UNSIGNED DEFAULT NULL COMMENT 'Related SPK ID',
    `stage_name` VARCHAR(50) DEFAULT NULL COMMENT 'Stage name (persiapan_unit, fabrikasi, painting)',
    `old_unit_no` VARCHAR(50) DEFAULT NULL COMMENT 'Source unit number (for reference)',
    `new_unit_no` VARCHAR(50) DEFAULT NULL COMMENT 'Target unit number (for reference)',
    `notes` TEXT DEFAULT NULL COMMENT 'Additional notes',
    `created_by` INT(11) NOT NULL COMMENT 'User ID who triggered',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_attachment_id` (`attachment_id`),
    KEY `idx_from_unit` (`from_unit_id`),
    KEY `idx_to_unit` (`to_unit_id`),
    KEY `idx_spk_id` (`spk_id`),
    KEY `idx_transfer_type` (`transfer_type`),
    KEY `idx_created_at` (`created_at`),
    CONSTRAINT `fk_atl_attachment` FOREIGN KEY (`attachment_id`) REFERENCES `inventory_attachment` (`id_inventory_attachment`) ON DELETE CASCADE,
    CONSTRAINT `fk_atl_from_unit` FOREIGN KEY (`from_unit_id`) REFERENCES `inventory_unit` (`id_inventory_unit`) ON DELETE SET NULL,
    CONSTRAINT `fk_atl_to_unit` FOREIGN KEY (`to_unit_id`) REFERENCES `inventory_unit` (`id_inventory_unit`) ON DELETE CASCADE,
    CONSTRAINT `fk_atl_spk` FOREIGN KEY (`spk_id`) REFERENCES `spk` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Audit log for all attachment transfers and assignments';

-- Create index for common queries
CREATE INDEX `idx_transfer_date` ON `attachment_transfer_log` (`created_at` DESC);
CREATE INDEX `idx_kanibal_transfers` ON `attachment_transfer_log` (`transfer_type`, `triggered_by`);

-- Insert comment for table documentation
ALTER TABLE `attachment_transfer_log` 
COMMENT = 'Tracks all attachment movements including KANIBAL transfers, new assignments, and detachments. Used for audit trail and tracking attachment history.';
