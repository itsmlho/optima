-- ============================================================================
-- MIGRATION: Create component_audit_log (Unified Audit Trail)
-- Date: 2026-03-14
-- Purpose: Gabung 3 tabel (inventory_item_unit_log, component_timeline, 
--          attachment_transfer_log) menjadi satu tabel terpusat
-- ============================================================================

SET NAMES utf8mb4;
SET foreign_key_checks = 0;

-- ============================================================================
-- STEP 1: Create the new unified table
-- ============================================================================

CREATE TABLE IF NOT EXISTS `component_audit_log` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `component_type` ENUM('ATTACHMENT','BATTERY','CHARGER') NOT NULL COMMENT 'Tipe komponen',
  `component_id` INT UNSIGNED NOT NULL COMMENT 'ID dari inventory_batteries/chargers/attachments',
  `event_type` VARCHAR(50) NOT NULL COMMENT 'ASSIGNED, REMOVED, TRANSFERRED, ATTACHED, DETACHED, REPLACED, BULK_RELEASED',
  `event_category` ENUM('ASSIGNMENT','TRANSFER','MAINTENANCE','STATUS','LOCATION','PURCHASE','DISPOSAL','CONTRACT') DEFAULT 'ASSIGNMENT' COMMENT 'Kategori event',
  `event_title` VARCHAR(255) DEFAULT NULL COMMENT 'Judul event (opsional)',
  `event_description` TEXT DEFAULT NULL COMMENT 'Deskripsi lengkap',
  `from_unit_id` INT UNSIGNED DEFAULT NULL COMMENT 'Unit asal (NULL jika baru assign)',
  `to_unit_id` INT UNSIGNED DEFAULT NULL COMMENT 'Unit tujuan (NULL jika release/detach)',
  `reference_type` VARCHAR(50) DEFAULT NULL COMMENT 'work_order, delivery_instruction, spk, unit_audit, contract, manual',
  `reference_id` INT UNSIGNED DEFAULT NULL COMMENT 'ID dari reference_type',
  `spk_id` INT UNSIGNED DEFAULT NULL COMMENT 'SPK ID jika applicable',
  `di_id` INT UNSIGNED DEFAULT NULL COMMENT 'Delivery Instruction ID jika applicable',
  `work_order_id` INT UNSIGNED DEFAULT NULL COMMENT 'Work Order ID jika applicable',
  `stage_name` VARCHAR(50) DEFAULT NULL COMMENT 'Stage name jika dari SPK workflow',
  `metadata` JSON DEFAULT NULL COMMENT 'Data tambahan dalam format JSON',
  `notes` TEXT DEFAULT NULL COMMENT 'Catatan/keterangan',
  `triggered_by` VARCHAR(100) DEFAULT NULL COMMENT 'Process yang trigger: PERSIAPAN_UNIT, KANIBAL, TRANSFER, etc',
  `performed_by` INT UNSIGNED DEFAULT NULL COMMENT 'User ID yang melakukan aksi',
  `performed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Waktu aksi dilakukan',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cal_component` (`component_type`, `component_id`, `performed_at` DESC),
  KEY `idx_cal_from_unit` (`from_unit_id`, `performed_at` DESC),
  KEY `idx_cal_to_unit` (`to_unit_id`, `performed_at` DESC),
  KEY `idx_cal_reference` (`reference_type`, `reference_id`),
  KEY `idx_cal_event_type` (`event_type`),
  KEY `idx_cal_spk` (`spk_id`),
  KEY `idx_cal_di` (`di_id`),
  KEY `idx_cal_wo` (`work_order_id`),
  KEY `idx_cal_performed_at` (`performed_at`),
  KEY `idx_cal_performed_by` (`performed_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Unified audit log untuk semua perubahan komponen (battery/charger/attachment)';

-- ============================================================================
-- STEP 2: Migrate data from inventory_item_unit_log
-- ============================================================================

INSERT INTO `component_audit_log` (
  `component_type`,
  `component_id`,
  `event_type`,
  `event_category`,
  `event_title`,
  `from_unit_id`,
  `to_unit_id`,
  `notes`,
  `performed_by`,
  `performed_at`,
  `created_at`
)
SELECT 
  'ATTACHMENT' AS component_type,
  `id_inventory_attachment` AS component_id,
  CASE 
    WHEN `action` = 'assign' THEN 'ASSIGNED'
    WHEN `action` = 'remove' THEN 'REMOVED'
    ELSE UPPER(`action`)
  END AS event_type,
  'ASSIGNMENT' AS event_category,
  CASE 
    WHEN `action` = 'assign' THEN CONCAT('Attachment assigned to unit #', `id_inventory_unit`)
    WHEN `action` = 'remove' THEN CONCAT('Attachment removed from unit #', `id_inventory_unit`)
    ELSE CONCAT('Attachment ', `action`, ' for unit #', `id_inventory_unit`)
  END AS event_title,
  CASE WHEN `action` = 'remove' THEN `id_inventory_unit` ELSE NULL END AS from_unit_id,
  CASE WHEN `action` = 'assign' THEN `id_inventory_unit` ELSE NULL END AS to_unit_id,
  `note` AS notes,
  `user_id` AS performed_by,
  COALESCE(`created_at`, NOW()) AS performed_at,
  COALESCE(`created_at`, NOW()) AS created_at
FROM `inventory_item_unit_log`
WHERE EXISTS (SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'inventory_item_unit_log');

-- ============================================================================
-- STEP 3: Migrate data from component_timeline (if exists)
-- ============================================================================

INSERT INTO `component_audit_log` (
  `component_type`,
  `component_id`,
  `event_type`,
  `event_category`,
  `event_title`,
  `event_description`,
  `from_unit_id`,
  `to_unit_id`,
  `reference_type`,
  `reference_id`,
  `metadata`,
  `performed_by`,
  `performed_at`,
  `created_at`
)
SELECT 
  `component_type`,
  `component_id`,
  `event_type`,
  `event_category`,
  `event_title`,
  `event_description`,
  `from_unit_id`,
  `to_unit_id`,
  `reference_type`,
  `reference_id`,
  `metadata`,
  `performed_by`,
  `performed_at`,
  COALESCE(`created_at`, `performed_at`) AS created_at
FROM `component_timeline`
WHERE EXISTS (SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'component_timeline');

-- ============================================================================
-- STEP 4: Migrate data from attachment_transfer_log
-- ============================================================================

INSERT INTO `component_audit_log` (
  `component_type`,
  `component_id`,
  `event_type`,
  `event_category`,
  `event_title`,
  `from_unit_id`,
  `to_unit_id`,
  `spk_id`,
  `di_id`,
  `stage_name`,
  `notes`,
  `triggered_by`,
  `performed_by`,
  `performed_at`,
  `created_at`
)
SELECT 
  COALESCE(UPPER(`component_type`), 'ATTACHMENT') AS component_type,
  `attachment_id` AS component_id,
  CASE 
    WHEN `transfer_type` = 'NEW_ASSIGNMENT' THEN 'ASSIGNED'
    WHEN `transfer_type` = 'TRANSFER' THEN 'TRANSFERRED'
    WHEN `transfer_type` = 'TUKAR' THEN 'TRANSFERRED'
    WHEN `transfer_type` = 'DETACH' THEN 'REMOVED'
    ELSE UPPER(COALESCE(`transfer_type`, 'TRANSFERRED'))
  END AS event_type,
  CASE 
    WHEN `transfer_type` = 'NEW_ASSIGNMENT' THEN 'ASSIGNMENT'
    WHEN `transfer_type` IN ('TRANSFER', 'TUKAR') THEN 'TRANSFER'
    WHEN `transfer_type` = 'DETACH' THEN 'ASSIGNMENT'
    ELSE 'TRANSFER'
  END AS event_category,
  CASE 
    WHEN `transfer_type` = 'NEW_ASSIGNMENT' THEN CONCAT('Component assigned to unit')
    WHEN `transfer_type` IN ('TRANSFER', 'TUKAR') THEN CONCAT('Component transferred between units')
    WHEN `transfer_type` = 'DETACH' THEN 'Component detached from unit'
    ELSE CONCAT('Component ', LOWER(`transfer_type`))
  END AS event_title,
  `from_unit_id`,
  `to_unit_id`,
  `spk_id`,
  `di_id`,
  `stage_name`,
  `notes`,
  `triggered_by`,
  `created_by` AS performed_by,
  `created_at` AS performed_at,
  `created_at`
FROM `attachment_transfer_log`
WHERE EXISTS (SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'attachment_transfer_log');

-- ============================================================================
-- STEP 5: Add system_activity_log security columns (Phase 1)
-- ============================================================================
-- NOTE: Run each ALTER TABLE statement separately if you get "duplicate column" errors.
--       Skip statements for columns that already exist.

-- Check existing columns first:
-- SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
-- WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'system_activity_log';

-- Add ip_address column (skip if already exists)
ALTER TABLE `system_activity_log` ADD COLUMN `ip_address` VARCHAR(45) DEFAULT NULL AFTER `user_id`;

-- Add user_agent column (skip if already exists)
ALTER TABLE `system_activity_log` ADD COLUMN `user_agent` VARCHAR(500) DEFAULT NULL AFTER `ip_address`;

-- Add session_id column (skip if already exists)
ALTER TABLE `system_activity_log` ADD COLUMN `session_id` VARCHAR(100) DEFAULT NULL AFTER `user_agent`;

-- Add indexes (skip if already exists)
ALTER TABLE `system_activity_log` ADD INDEX `idx_sal_ip_address` (`ip_address`);
ALTER TABLE `system_activity_log` ADD INDEX `idx_sal_user_created` (`user_id`, `created_at`);

SET foreign_key_checks = 1;

-- ============================================================================
-- VERIFICATION
-- ============================================================================

SELECT 'Migration complete: component_audit_log created' AS status;
SELECT COUNT(*) AS total_records FROM `component_audit_log`;
