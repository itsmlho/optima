-- ============================================================================
-- PRODUCTION SYNC - BAGIAN TABEL SAJA (4 tabel)
-- ============================================================================
-- Jalankan di phpMyAdmin tab SQL - copy-paste lalu Go
-- Setelah ini berhasil, jalankan PROD_sync_PROCEDURES_only.sql via tab Import
-- ============================================================================

SET NAMES utf8mb4;
SET foreign_key_checks = 0;

CREATE TABLE IF NOT EXISTS `attachment_transfer_log` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `attachment_id` int UNSIGNED NOT NULL COMMENT 'ID from inventory_batteries/chargers/attachments',
  `component_type` enum('battery','charger','attachment') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Type of component being transferred',
  `from_unit_id` int UNSIGNED DEFAULT NULL COMMENT 'Source unit ID (NULL for new assignments)',
  `to_unit_id` int UNSIGNED DEFAULT NULL COMMENT 'Destination unit ID (NULL for unassignments)',
  `transfer_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'NEW_ASSIGNMENT' COMMENT 'Type: NEW_ASSIGNMENT, TRANSFER, TUKAR, DETACH',
  `triggered_by` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Process that triggered transfer',
  `spk_id` int UNSIGNED DEFAULT NULL COMMENT 'Related SPK ID if applicable',
  `stage_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'SPK stage name if applicable',
  `di_id` int UNSIGNED DEFAULT NULL COMMENT 'Delivery Instruction ID if applicable',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Additional notes about the transfer',
  `created_by` int UNSIGNED DEFAULT NULL COMMENT 'User ID who performed the action',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_attachment_id` (`attachment_id`),
  KEY `idx_from_unit` (`from_unit_id`),
  KEY `idx_to_unit` (`to_unit_id`),
  KEY `idx_spk_id` (`spk_id`),
  KEY `idx_transfer_type` (`transfer_type`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_component_type` (`component_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Audit log for component (battery/charger/attachment) transfers between units';

CREATE TABLE IF NOT EXISTS `unit_audit_locations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `audit_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'AUDLOC-YYYYMMDD-NNNN',
  `customer_id` int UNSIGNED NOT NULL,
  `customer_location_id` int UNSIGNED NOT NULL COMMENT 'The location being audited',
  `kontrak_id` int UNSIGNED DEFAULT NULL COMMENT 'Contract covering this location',
  `audit_date` date NOT NULL,
  `audit_completed_date` date DEFAULT NULL,
  `audited_by` int UNSIGNED DEFAULT NULL COMMENT 'Mechanic who performed audit',
  `mechanic_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nama mekanik yang mengecek',
  `status` enum('DRAFT','PRINTED','IN_PROGRESS','RESULTS_ENTERED','PENDING_APPROVAL','APPROVED','REJECTED') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DRAFT',
  `kontrak_total_units` int NOT NULL DEFAULT '0',
  `kontrak_spare_units` int NOT NULL DEFAULT '0',
  `kontrak_has_operator` tinyint(1) NOT NULL DEFAULT '0',
  `actual_total_units` int NOT NULL DEFAULT '0',
  `actual_spare_units` int NOT NULL DEFAULT '0',
  `actual_has_operator` tinyint(1) NOT NULL DEFAULT '0',
  `has_discrepancy` tinyint(1) NOT NULL DEFAULT '0',
  `unit_difference` int NOT NULL DEFAULT '0',
  `price_per_unit` decimal(15,2) DEFAULT NULL,
  `total_price_adjustment` decimal(15,2) DEFAULT NULL,
  `mechanic_notes` text COLLATE utf8mb4_unicode_ci,
  `service_notes` text COLLATE utf8mb4_unicode_ci,
  `marketing_notes` text COLLATE utf8mb4_unicode_ci,
  `submitted_by` int UNSIGNED NOT NULL,
  `reviewed_by` int UNSIGNED DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_number` (`audit_number`),
  KEY `customer_id_customer_location_id` (`customer_id`,`customer_location_id`),
  KEY `status` (`status`),
  KEY `audit_date` (`audit_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `unit_audit_location_items` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `audit_location_id` int UNSIGNED NOT NULL,
  `kontrak_unit_id` int UNSIGNED DEFAULT NULL COMMENT 'Link to kontrak_unit (if found in contract)',
  `unit_id` int UNSIGNED NOT NULL,
  `expected_no_unit` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expected_serial` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expected_merk` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expected_model` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expected_is_spare` tinyint(1) NOT NULL DEFAULT '0',
  `expected_status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `actual_no_unit` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `actual_serial` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `actual_merk` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `actual_model` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `actual_is_spare` tinyint(1) NOT NULL DEFAULT '0',
  `actual_operator_present` tinyint(1) NOT NULL DEFAULT '0',
  `result` enum('MATCH','NO_UNIT_IN_KONTRAK','EXTRA_UNIT','ADD_UNIT','MISMATCH_NO_UNIT','MISMATCH_SERIAL','MISMATCH_SPEC','MISMATCH_SPARE') COLLATE utf8mb4_unicode_ci DEFAULT 'MATCH',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_location_id` (`audit_location_id`),
  KEY `unit_id` (`unit_id`),
  KEY `result` (`result`),
  CONSTRAINT `unit_audit_location_items_audit_location_id_foreign` FOREIGN KEY (`audit_location_id`) REFERENCES `unit_audit_locations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `unit_audit_location_items_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `inventory_unit` (`id_inventory_unit`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `unit_verification_history` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `unit_id` int UNSIGNED NOT NULL COMMENT 'FK to inventory_unit.id_inventory_unit',
  `work_order_id` int NOT NULL COMMENT 'FK to work_orders.id',
  `verified_by` int NOT NULL COMMENT 'FK to employees.id (mechanic who verified)',
  `verified_at` datetime NOT NULL COMMENT 'Timestamp when verification was done',
  `verification_data` json DEFAULT NULL COMMENT 'Store all verification field values',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_unit_id` (`unit_id`),
  KEY `idx_work_order_id` (`work_order_id`),
  KEY `idx_verified_by` (`verified_by`),
  KEY `idx_verified_at` (`verified_at`),
  CONSTRAINT `unit_verification_history_ibfk_1` FOREIGN KEY (`unit_id`) REFERENCES `inventory_unit` (`id_inventory_unit`) ON DELETE CASCADE,
  CONSTRAINT `unit_verification_history_ibfk_2` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `unit_verification_history_ibfk_3` FOREIGN KEY (`verified_by`) REFERENCES `employees` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='History of unit verifications linked to work orders';

SET foreign_key_checks = 1;

SELECT '4 tabel berhasil dibuat' AS status;
