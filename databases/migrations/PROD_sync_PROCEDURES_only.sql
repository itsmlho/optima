-- ============================================================================
-- PRODUCTION SYNC - BAGIAN PROCEDURES SAJA (9 stored procedures)
-- ============================================================================
-- PENTING: Jalankan via phpMyAdmin tab IMPORT (unggah file), BUKAN tab SQL!
-- Tab SQL akan error karena DELIMITER. Tab Import menangani dengan benar.
-- ============================================================================

DELIMITER $$

DROP PROCEDURE IF EXISTS `auto_assign_employees_to_work_order`$$
CREATE PROCEDURE `auto_assign_employees_to_work_order` (IN `p_unit_id` INT, IN `p_departemen_id` INT, OUT `p_admin_id` INT, OUT `p_foreman_id` INT, OUT `p_mechanic_id` INT, OUT `p_helper_id` INT)
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
    WHERE aea.area_id = unit_area_id AND e.departemen_id = p_departemen_id AND e.staff_role = 'ADMIN' AND e.is_active = 1 LIMIT 1;
    SELECT e.id INTO p_foreman_id
    FROM employees e
    JOIN area_employee_assignments aea ON e.id = aea.employee_id
    WHERE aea.area_id = unit_area_id AND e.departemen_id = p_departemen_id AND e.staff_role = 'FOREMAN' AND e.is_active = 1 LIMIT 1;
    SELECT e.id INTO p_mechanic_id
    FROM employees e
    JOIN area_employee_assignments aea ON e.id = aea.employee_id
    WHERE aea.area_id = unit_area_id AND e.departemen_id = p_departemen_id AND e.staff_role = 'MECHANIC' AND e.is_active = 1 LIMIT 1;
    SELECT e.id INTO p_helper_id
    FROM employees e
    JOIN area_employee_assignments aea ON e.id = aea.employee_id
    WHERE aea.area_id = unit_area_id AND e.departemen_id = p_departemen_id AND e.staff_role = 'HELPER' AND e.is_active = 1 LIMIT 1;
END$$

DROP PROCEDURE IF EXISTS `auto_fill_accessories`$$
CREATE PROCEDURE `auto_fill_accessories` (IN `p_kontrak_spesifikasi_id` INT, IN `p_spk_id` INT)
BEGIN
    DECLARE v_aksesoris VARCHAR(255);
    DECLARE v_kontrak_id INT;
    DECLARE v_pelanggan VARCHAR(255);
    START TRANSACTION;
    SELECT ks.aksesoris, ks.kontrak_id, c.customer_name INTO v_aksesoris, v_kontrak_id, v_pelanggan
    FROM kontrak_spesifikasi ks
    JOIN kontrak k ON ks.kontrak_id = k.id 
    JOIN customers c ON k.customer_id = c.id
    WHERE ks.id = p_kontrak_spesifikasi_id;
    UPDATE inventory_unit SET spk_id = p_spk_id, kontrak_spesifikasi_id = p_kontrak_spesifikasi_id, aksesoris = v_aksesoris, kontrak_id = v_kontrak_id, lokasi_unit = v_pelanggan, updated_at = CURRENT_TIMESTAMP
    WHERE kontrak_spesifikasi_id = p_kontrak_spesifikasi_id AND (spk_id IS NULL OR spk_id = 0) AND status_unit_id = 1;
    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `sync_unit_denormalized_fields`$$
CREATE PROCEDURE `sync_unit_denormalized_fields` ()
BEGIN
    UPDATE inventory_unit iu
    JOIN kontrak_spesifikasi ks ON iu.kontrak_spesifikasi_id = ks.id
    SET iu.aksesoris = ks.aksesoris, iu.updated_at = CURRENT_TIMESTAMP
    WHERE iu.kontrak_spesifikasi_id IS NOT NULL AND (iu.aksesoris IS NULL OR iu.aksesoris != ks.aksesoris);
END$$

DROP PROCEDURE IF EXISTS `sp_attach_attachment_to_unit`$$
CREATE PROCEDURE `sp_attach_attachment_to_unit` (IN `p_attachment_id` INT, IN `p_unit_id` INT)
BEGIN
    DECLARE v_unit_no VARCHAR(50);
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    START TRANSACTION;
    SELECT no_unit INTO v_unit_no FROM inventory_unit WHERE id_inventory_unit = p_unit_id;
    IF v_unit_no IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Unit tidak ditemukan'; END IF;
    UPDATE inventory_attachments SET inventory_unit_id = p_unit_id, storage_location = CONCAT('Terpasang di Unit ', v_unit_no), status = 'IN_USE', updated_at = CURRENT_TIMESTAMP WHERE id = p_attachment_id;
    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `sp_attach_battery_to_unit`$$
CREATE PROCEDURE `sp_attach_battery_to_unit` (IN `p_battery_id` INT, IN `p_unit_id` INT)
BEGIN
    DECLARE v_unit_no VARCHAR(50);
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    START TRANSACTION;
    SELECT no_unit INTO v_unit_no FROM inventory_unit WHERE id_inventory_unit = p_unit_id;
    IF v_unit_no IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Unit tidak ditemukan'; END IF;
    UPDATE inventory_batteries SET inventory_unit_id = p_unit_id, storage_location = CONCAT('Terpasang di Unit ', v_unit_no), status = 'IN_USE', updated_at = CURRENT_TIMESTAMP WHERE id = p_battery_id;
    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `sp_attach_charger_to_unit`$$
CREATE PROCEDURE `sp_attach_charger_to_unit` (IN `p_charger_id` INT, IN `p_unit_id` INT)
BEGIN
    DECLARE v_unit_no VARCHAR(50);
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    START TRANSACTION;
    SELECT no_unit INTO v_unit_no FROM inventory_unit WHERE id_inventory_unit = p_unit_id;
    IF v_unit_no IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Unit tidak ditemukan'; END IF;
    UPDATE inventory_chargers SET inventory_unit_id = p_unit_id, storage_location = CONCAT('Terpasang di Unit ', v_unit_no), status = 'IN_USE', updated_at = CURRENT_TIMESTAMP WHERE id = p_charger_id;
    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `sp_detach_attachment_from_unit`$$
CREATE PROCEDURE `sp_detach_attachment_from_unit` (IN `p_attachment_id` INT)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    START TRANSACTION;
    UPDATE inventory_attachments SET inventory_unit_id = NULL, storage_location = 'Gudang Pusat', status = 'AVAILABLE', updated_at = CURRENT_TIMESTAMP WHERE id = p_attachment_id;
    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `sp_detach_battery_from_unit`$$
CREATE PROCEDURE `sp_detach_battery_from_unit` (IN `p_battery_id` INT)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    START TRANSACTION;
    UPDATE inventory_batteries SET inventory_unit_id = NULL, storage_location = 'Gudang Pusat', status = 'AVAILABLE', updated_at = CURRENT_TIMESTAMP WHERE id = p_battery_id;
    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `sp_detach_charger_from_unit`$$
CREATE PROCEDURE `sp_detach_charger_from_unit` (IN `p_charger_id` INT)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    START TRANSACTION;
    UPDATE inventory_chargers SET inventory_unit_id = NULL, storage_location = 'Gudang Pusat', status = 'AVAILABLE', updated_at = CURRENT_TIMESTAMP WHERE id = p_charger_id;
    COMMIT;
END$$

DELIMITER ;
