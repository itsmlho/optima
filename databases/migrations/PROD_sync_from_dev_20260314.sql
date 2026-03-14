-- ============================================================================
-- PRODUCTION SYNC - JALANKAN 2 KALI (copy-paste terpisah)
-- ============================================================================
-- phpMyAdmin bermasalah jika tabel + procedure digabung. Jalankan satu per satu.
--
-- LANGKAH 1: Copy dari baris SET NAMES sampai SET foreign_key_checks = 1;
--            Paste di tab SQL, Delimiter KOSONGKAN, klik Go
--
-- LANGKAH 2: Copy dari baris DROP PROCEDURE sampai SELECT status$$
--            Paste di tab SQL, Delimiter isi: $$  lalu klik Go
-- ============================================================================

-- ==================== MULAI LANGKAH 1 (copy dari sini) ====================
SET NAMES utf8mb4;
SET foreign_key_checks = 0;

-- 1.1 attachment_transfer_log
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

-- 1.2 unit_audit_locations
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

-- 1.3 unit_audit_location_items
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

-- 1.4 unit_verification_history
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
-- ==================== AKHIR LANGKAH 1 (copy sampai sini) ====================

-- ==================== MULAI LANGKAH 2 (Delimiter: $$) ====================
DROP PROCEDURE IF EXISTS auto_assign_employees_to_work_order$$
CREATE PROCEDURE auto_assign_employees_to_work_order(IN p_unit_id INT, IN p_departemen_id INT, OUT p_admin_id INT, OUT p_foreman_id INT, OUT p_mechanic_id INT, OUT p_helper_id INT)
BEGIN
    DECLARE unit_area_id INT;
    
    SELECT a.id INTO unit_area_id
    FROM inventory_unit iu
    JOIN kontrak_unit ku ON iu.id_inventory_unit = ku.unit_id
    JOIN customer_locations cl ON ku.customer_location_id = cl.id
    JOIN customers c ON cl.customer_id = c.id
    JOIN areas a ON c.area_id = a.id
    WHERE iu.id_inventory_unit = p_unit_id
    LIMIT 1;
    
    SELECT e.id INTO p_admin_id
    FROM employees e
    JOIN area_employee_assignments aea ON e.id = aea.employee_id
    WHERE aea.area_id = unit_area_id 
    AND e.departemen_id = p_departemen_id
    AND e.staff_role = 'ADMIN'
    AND e.is_active = 1 
    LIMIT 1;
    
    SELECT e.id INTO p_foreman_id
    FROM employees e
    JOIN area_employee_assignments aea ON e.id = aea.employee_id
    WHERE aea.area_id = unit_area_id
    AND e.departemen_id = p_departemen_id
    AND e.staff_role = 'FOREMAN'
    AND e.is_active = 1
    LIMIT 1;
    
    SELECT e.id INTO p_mechanic_id
    FROM employees e
    JOIN area_employee_assignments aea ON e.id = aea.employee_id
    WHERE aea.area_id = unit_area_id
    AND e.departemen_id = p_departemen_id
    AND e.staff_role = 'MECHANIC'
    AND e.is_active = 1
    LIMIT 1;
    
    SELECT e.id INTO p_helper_id
    FROM employees e
    JOIN area_employee_assignments aea ON e.id = aea.employee_id
    WHERE aea.area_id = unit_area_id
    AND e.departemen_id = p_departemen_id
    AND e.staff_role = 'HELPER'
    AND e.is_active = 1
    LIMIT 1;
END$$

-- 2.2 auto_fill_accessories (TAMBAH - hanya ada di DEV)
DROP PROCEDURE IF EXISTS auto_fill_accessories$$
CREATE PROCEDURE auto_fill_accessories(IN p_kontrak_spesifikasi_id INT, IN p_spk_id INT)
BEGIN
    DECLARE v_aksesoris VARCHAR(255);
    DECLARE v_kontrak_id INT;
    DECLARE v_pelanggan VARCHAR(255);
    
    START TRANSACTION;
    
    SELECT ks.aksesoris, ks.kontrak_id, c.customer_name 
    INTO v_aksesoris, v_kontrak_id, v_pelanggan
    FROM kontrak_spesifikasi ks
    JOIN kontrak k ON ks.kontrak_id = k.id 
    JOIN customers c ON k.customer_id = c.id
    WHERE ks.id = p_kontrak_spesifikasi_id;
    
    UPDATE inventory_unit 
    SET 
        spk_id = p_spk_id,
        kontrak_spesifikasi_id = p_kontrak_spesifikasi_id,
        aksesoris = v_aksesoris,
        kontrak_id = v_kontrak_id,
        lokasi_unit = v_pelanggan,
        updated_at = CURRENT_TIMESTAMP
    WHERE kontrak_spesifikasi_id = p_kontrak_spesifikasi_id
    AND (spk_id IS NULL OR spk_id = 0)
    AND status_unit_id = 1;
    
    COMMIT;
END$$

-- 2.3 sync_unit_denormalized_fields (TAMBAH - hanya ada di DEV)
DROP PROCEDURE IF EXISTS sync_unit_denormalized_fields$$
CREATE PROCEDURE sync_unit_denormalized_fields()
BEGIN
    UPDATE inventory_unit iu
    JOIN kontrak_spesifikasi ks ON iu.kontrak_spesifikasi_id = ks.id
    SET iu.aksesoris = ks.aksesoris,
        iu.updated_at = CURRENT_TIMESTAMP
    WHERE iu.kontrak_spesifikasi_id IS NOT NULL
    AND (iu.aksesoris IS NULL OR iu.aksesoris != ks.aksesoris);
END$$

-- 2.4 sp_attach_attachment_to_unit (TAMBAH - DEV punya terpisah per tipe)
DROP PROCEDURE IF EXISTS sp_attach_attachment_to_unit$$
CREATE PROCEDURE sp_attach_attachment_to_unit(IN p_attachment_id INT, IN p_unit_id INT)
BEGIN
    DECLARE v_unit_no VARCHAR(50);
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    SELECT no_unit INTO v_unit_no
    FROM inventory_unit 
    WHERE id_inventory_unit = p_unit_id;
    
    IF v_unit_no IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Unit tidak ditemukan';
    END IF;
    
    UPDATE inventory_attachments
    SET 
        inventory_unit_id = p_unit_id,
        storage_location = CONCAT('Terpasang di Unit ', v_unit_no),
        status = 'IN_USE',
        updated_at = CURRENT_TIMESTAMP
    WHERE id = p_attachment_id;
    
    COMMIT;
END$$

-- 2.5 sp_attach_battery_to_unit
DROP PROCEDURE IF EXISTS sp_attach_battery_to_unit$$
CREATE PROCEDURE sp_attach_battery_to_unit(IN p_battery_id INT, IN p_unit_id INT)
BEGIN
    DECLARE v_unit_no VARCHAR(50);
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    SELECT no_unit INTO v_unit_no
    FROM inventory_unit 
    WHERE id_inventory_unit = p_unit_id;
    
    IF v_unit_no IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Unit tidak ditemukan';
    END IF;
    
    UPDATE inventory_batteries
    SET 
        inventory_unit_id = p_unit_id,
        storage_location = CONCAT('Terpasang di Unit ', v_unit_no),
        status = 'IN_USE',
        updated_at = CURRENT_TIMESTAMP
    WHERE id = p_battery_id;
    
    COMMIT;
END$$

-- 2.6 sp_attach_charger_to_unit
DROP PROCEDURE IF EXISTS sp_attach_charger_to_unit$$
CREATE PROCEDURE sp_attach_charger_to_unit(IN p_charger_id INT, IN p_unit_id INT)
BEGIN
    DECLARE v_unit_no VARCHAR(50);
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    SELECT no_unit INTO v_unit_no
    FROM inventory_unit 
    WHERE id_inventory_unit = p_unit_id;
    
    IF v_unit_no IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Unit tidak ditemukan';
    END IF;
    
    UPDATE inventory_chargers
    SET 
        inventory_unit_id = p_unit_id,
        storage_location = CONCAT('Terpasang di Unit ', v_unit_no),
        status = 'IN_USE',
        updated_at = CURRENT_TIMESTAMP
    WHERE id = p_charger_id;
    
    COMMIT;
END$$

-- 2.7 sp_detach_attachment_from_unit
DROP PROCEDURE IF EXISTS sp_detach_attachment_from_unit$$
CREATE PROCEDURE sp_detach_attachment_from_unit(IN p_attachment_id INT)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;
    
    UPDATE inventory_attachments
    SET 
        inventory_unit_id = NULL,
        storage_location = 'Gudang Pusat',
        status = 'AVAILABLE',
        updated_at = CURRENT_TIMESTAMP
    WHERE id = p_attachment_id;
    
    COMMIT;
END$$

-- 2.8 sp_detach_battery_from_unit
DROP PROCEDURE IF EXISTS `sp_detach_battery_from_unit`$$
CREATE PROCEDURE `sp_detach_battery_from_unit` (IN `p_battery_id` INT)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;
    
    UPDATE inventory_batteries
    SET 
        inventory_unit_id = NULL,
        storage_location = 'Gudang Pusat',
        status = 'AVAILABLE',
        updated_at = CURRENT_TIMESTAMP
    WHERE id = p_battery_id;
    
    COMMIT;
END$$

-- 2.9 sp_detach_charger_from_unit
DROP PROCEDURE IF EXISTS sp_detach_charger_from_unit$$
CREATE PROCEDURE sp_detach_charger_from_unit(IN p_charger_id INT)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;
    
    UPDATE inventory_chargers
    SET 
        inventory_unit_id = NULL,
        storage_location = 'Gudang Pusat',
        status = 'AVAILABLE',
        updated_at = CURRENT_TIMESTAMP
    WHERE id = p_charger_id;
    
    COMMIT;
END$$

SELECT 'Sync lengkap selesai: 4 tabel + 9 stored procedures' AS status$$
-- ==================== AKHIR LANGKAH 2 (copy sampai sini) ====================
