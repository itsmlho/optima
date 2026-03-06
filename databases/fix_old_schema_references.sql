-- Fix SQL Dump: Update Stored Procedures and VIEWs
-- Purpose: Replace k.customer_location_id references (removed column)
-- Date: 2026-03-06

-- =========================================
-- DROP OUTDATED VIEWS
-- =========================================

-- This VIEW is NO LONGER NEEDED (uses old schema)
DROP VIEW IF EXISTS contract_unit_summary;

-- =========================================
-- FIX STORED PROCEDURE: auto_assign_employees_to_work_order
-- =========================================

DROP PROCEDURE IF EXISTS auto_assign_employees_to_work_order;

DELIMITER $$
CREATE PROCEDURE auto_assign_employees_to_work_order(
    IN p_unit_id INT,
    IN p_departemen_id INT,
    OUT p_admin_id INT,
    OUT p_foreman_id INT,
    OUT p_mechanic_id INT,
    OUT p_helper_id INT
)
BEGIN
    DECLARE unit_area_id INT;
    
    -- FIXED: Get area from kontrak_unit.customer_location_id instead of kontrak.customer_location_id
    SELECT a.id INTO unit_area_id
    FROM inventory_unit iu
    JOIN kontrak_unit ku ON iu.id_inventory_unit = ku.unit_id
    JOIN customer_locations cl ON ku.customer_location_id = cl.id
    JOIN customers c ON cl.customer_id = c.id
    JOIN areas a ON c.area_id = a.id
    WHERE iu.id_inventory_unit = p_unit_id
    LIMIT 1;
    
    -- Get Admin for this area + departemen
    SELECT e.id INTO p_admin_id
    FROM employees e
    JOIN area_employee_assignments aea ON e.id = aea.employee_id
    WHERE aea.area_id = unit_area_id 
    AND e.departemen_id = p_departemen_id
    AND e.staff_role = 'ADMIN'
    AND e.is_active = 1 
    LIMIT 1;
    
    -- Get Foreman
    SELECT e.id INTO p_foreman_id
    FROM employees e
    JOIN area_employee_assignments aea ON e.id = aea.employee_id
    WHERE aea.area_id = unit_area_id
    AND e.departemen_id = p_departemen_id
    AND e.staff_role = 'FOREMAN'
    AND e.is_active = 1
    LIMIT 1;
    
    -- Get Mechanic
    SELECT e.id INTO p_mechanic_id
    FROM employees e
    JOIN area_employee_assignments aea ON e.id = aea.employee_id
    WHERE aea.area_id = unit_area_id
    AND e.departemen_id = p_departemen_id
    AND e.staff_role = 'MECHANIC'
    AND e.is_active = 1
    LIMIT 1;
    
    -- Get Helper
    SELECT e.id INTO p_helper_id
    FROM employees e
    JOIN area_employee_assignments aea ON e.id = aea.employee_id
    WHERE aea.area_id = unit_area_id
    AND e.departemen_id = p_departemen_id
    AND e.staff_role = 'HELPER'
    AND e.is_active = 1
    LIMIT 1;
END$$
DELIMITER ;

-- =========================================
-- FIX STORED PROCEDURE: auto_fill_accessories
-- =========================================

DROP PROCEDURE IF EXISTS auto_fill_accessories;

DELIMITER $$
CREATE PROCEDURE auto_fill_accessories(IN p_kontrak_spesifikasi_id INT, IN p_spk_id INT)
BEGIN
    DECLARE v_aksesoris VARCHAR(255);
    DECLARE v_kontrak_id INT;
    DECLARE v_pelanggan VARCHAR(255);
    
    START TRANSACTION;
    
    -- FIXED: Get customer from kontrak.customer_id directly
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
DELIMITER ;

-- =========================================
-- FIX STORED PROCEDURE: sync_unit_denormalized_fields
-- =========================================

DROP PROCEDURE IF EXISTS sync_unit_denormalized_fields;

DELIMITER $$
CREATE PROCEDURE sync_unit_denormalized_fields()
BEGIN
    -- Update aksesoris from kontrak_spesifikasi
    UPDATE inventory_unit iu
    JOIN kontrak_spesifikasi ks ON iu.kontrak_spesifikasi_id = ks.id
    SET iu.aksesoris = ks.aksesoris,
        iu.updated_at = CURRENT_TIMESTAMP
    WHERE iu.kontrak_spesifikasi_id IS NOT NULL
    AND (iu.aksesoris IS NULL OR iu.aksesoris != ks.aksesoris);
    
    -- REMOVED: lokasi_unit sync - kontrak no longer has customer_location_id
    -- Note: lokasi_unit should be synced from kontrak_unit.customer_location_id instead
    -- This requires business logic to determine which location to use if unit is in multiple contracts
    
END$$
DELIMITER ;

-- =========================================
-- SUCCESS MESSAGE
-- =========================================

SELECT 'All stored procedures updated successfully!' AS Status;
