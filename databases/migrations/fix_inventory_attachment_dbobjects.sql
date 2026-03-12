-- ============================================================================
-- FIX DATABASE OBJECTS USING OLD inventory_attachment TABLE
-- ============================================================================
-- Created: March 12, 2026
-- Purpose: Drop/recreate triggers and stored procedures to use new tables:
--          - inventory_batteries
--          - inventory_chargers  
--          - inventory_attachments
-- ============================================================================

USE optima_ci;

-- ============================================================================
-- STEP 1: DROP OLD TRIGGER USING inventory_attachment
-- ============================================================================

DROP TRIGGER IF EXISTS tr_inventory_unit_attachment_sync;

-- ============================================================================
-- STEP 2: CREATE NEW TRIGGERS FOR 3 SEPARATE TABLES
-- ============================================================================

DELIMITER $$

-- Trigger to sync inventory_unit status changes to inventory_batteries
CREATE TRIGGER tr_inventory_unit_battery_sync
AFTER UPDATE ON inventory_unit
FOR EACH ROW
BEGIN
    IF OLD.status_unit_id != NEW.status_unit_id THEN
        UPDATE inventory_batteries
        SET status = CASE NEW.status_unit_id
                WHEN 1 THEN 'AVAILABLE'
                WHEN 2 THEN 'IN_USE'
                WHEN 7 THEN 'IN_USE'
                ELSE 'MAINTENANCE'
            END,
            updated_at = NOW()
        WHERE inventory_unit_id = NEW.id_inventory_unit;
    END IF;
END$$

-- Trigger to sync inventory_unit status changes to inventory_chargers
CREATE TRIGGER tr_inventory_unit_charger_sync
AFTER UPDATE ON inventory_unit
FOR EACH ROW
BEGIN
    IF OLD.status_unit_id != NEW.status_unit_id THEN
        UPDATE inventory_chargers
        SET status = CASE NEW.status_unit_id
                WHEN 1 THEN 'AVAILABLE'
                WHEN 2 THEN 'IN_USE'
                WHEN 7 THEN 'IN_USE'
                ELSE 'MAINTENANCE'
            END,
            updated_at = NOW()
        WHERE inventory_unit_id = NEW.id_inventory_unit;
    END IF;
END$$

-- Trigger to sync inventory_unit status changes to inventory_attachments  
CREATE TRIGGER tr_inventory_unit_attachments_sync
AFTER UPDATE ON inventory_unit
FOR EACH ROW
BEGIN
    IF OLD.status_unit_id != NEW.status_unit_id THEN
        UPDATE inventory_attachments
        SET status = CASE NEW.status_unit_id
                WHEN 1 THEN 'AVAILABLE'
                WHEN 2 THEN 'IN_USE'
                WHEN 7 THEN 'IN_USE'
                ELSE 'MAINTENANCE'
            END,
            updated_at = NOW()
        WHERE inventory_unit_id = NEW.id_inventory_unit;
    END IF;
END$$

-- ============================================================================
-- STEP 3: DROP OLD STORED PROCEDURES
-- ============================================================================

DROP PROCEDURE IF EXISTS sp_attach_item_to_unit$$
DROP PROCEDURE IF EXISTS sp_detach_item_from_unit$$
DROP PROCEDURE IF EXISTS sp_sync_workflow_data$$

-- ============================================================================
-- STEP 4: CREATE NEW STORED PROCEDURES FOR COMPONENT MANAGEMENT
-- ============================================================================

-- Procedure to attach battery to unit
CREATE PROCEDURE sp_attach_battery_to_unit(
    IN p_battery_id INT, 
    IN p_unit_id INT
)
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

-- Procedure to detach battery from unit
CREATE PROCEDURE sp_detach_battery_from_unit(IN p_battery_id INT)
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

-- Procedure to attach charger to unit
CREATE PROCEDURE sp_attach_charger_to_unit(
    IN p_charger_id INT, 
    IN p_unit_id INT
)
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

-- Procedure to detach charger from unit
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

-- Procedure to attach attachment to unit
CREATE PROCEDURE sp_attach_attachment_to_unit(
    IN p_attachment_id INT, 
    IN p_unit_id INT
)
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

-- Procedure to detach attachment from unit
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

-- ============================================================================
-- STEP 5: RECREATE sp_sync_workflow_data WITH NEW TABLES
-- ============================================================================

CREATE PROCEDURE sp_sync_workflow_data()
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    -- Sync accessories from kontrak_spesifikasi to inventory_unit
    UPDATE inventory_unit iu
    JOIN kontrak_spesifikasi ks ON iu.kontrak_spesifikasi_id = ks.id
    SET iu.aksesoris = ks.aksesoris,
        iu.updated_at = CURRENT_TIMESTAMP
    WHERE iu.kontrak_spesifikasi_id IS NOT NULL
    AND (iu.aksesoris IS NULL OR iu.aksesoris != ks.aksesoris);
    
    -- Sync location from customer to inventory_unit
    UPDATE inventory_unit iu
    JOIN kontrak k ON iu.kontrak_id = k.id
    JOIN customers c ON k.customer_id = c.id
    SET iu.lokasi_unit = c.customer_name,
        iu.updated_at = CURRENT_TIMESTAMP
    WHERE iu.kontrak_id IS NOT NULL
    AND (iu.lokasi_unit IS NULL OR iu.lokasi_unit != c.customer_name);
    
    -- Sync delivery date from delivery_instructions
    UPDATE inventory_unit iu
    JOIN spk s ON (iu.spk_id = s.id OR iu.kontrak_spesifikasi_id = s.kontrak_spesifikasi_id)
    JOIN delivery_instructions di ON s.id = di.spk_id
    SET iu.tanggal_kirim = di.tanggal_kirim,
        iu.spk_id = COALESCE(iu.spk_id, s.id),
        iu.delivery_instruction_id = di.id,
        iu.updated_at = CURRENT_TIMESTAMP
    WHERE di.tanggal_kirim IS NOT NULL
    AND di.status_di IN ('SELESAI', 'SAMPAI_LOKASI', 'DALAM_PERJALANAN')
    AND (iu.tanggal_kirim IS NULL OR iu.tanggal_kirim != di.tanggal_kirim);
    
    -- Sync storage location for batteries
    UPDATE inventory_batteries ib
    JOIN inventory_unit iu ON ib.inventory_unit_id = iu.id_inventory_unit
    SET ib.storage_location = CONCAT('Terpasang di Unit ', iu.no_unit),
        ib.updated_at = CURRENT_TIMESTAMP
    WHERE ib.inventory_unit_id IS NOT NULL
    AND (ib.storage_location IS NULL OR ib.storage_location != CONCAT('Terpasang di Unit ', iu.no_unit));
    
    -- Sync storage location for chargers
    UPDATE inventory_chargers ic
    JOIN inventory_unit iu ON ic.inventory_unit_id = iu.id_inventory_unit
    SET ic.storage_location = CONCAT('Terpasang di Unit ', iu.no_unit),
        ic.updated_at = CURRENT_TIMESTAMP
    WHERE ic.inventory_unit_id IS NOT NULL
    AND (ic.storage_location IS NULL OR ic.storage_location != CONCAT('Terpasang di Unit ', iu.no_unit));
    
    -- Sync storage location for attachments
    UPDATE inventory_attachments ia
    JOIN inventory_unit iu ON ia.inventory_unit_id = iu.id_inventory_unit
    SET ia.storage_location = CONCAT('Terpasang di Unit ', iu.no_unit),
        ia.updated_at = CURRENT_TIMESTAMP
    WHERE ia.inventory_unit_id IS NOT NULL
    AND (ia.storage_location IS NULL OR ia.storage_location != CONCAT('Terpasang di Unit ', iu.no_unit));
    
    COMMIT;
    
    -- Return summary
    SELECT 
        'Workflow data sync completed' as message,
        (SELECT COUNT(*) FROM inventory_unit WHERE aksesoris IS NOT NULL) as units_with_accessories,
        (SELECT COUNT(*) FROM inventory_unit WHERE tanggal_kirim IS NOT NULL) as units_with_delivery_date,
        (SELECT COUNT(*) FROM inventory_batteries WHERE storage_location LIKE 'Terpasang di Unit%') +
        (SELECT COUNT(*) FROM inventory_chargers WHERE storage_location LIKE 'Terpasang di Unit%') +
        (SELECT COUNT(*) FROM inventory_attachments WHERE storage_location LIKE 'Terpasang di Unit%') as attached_items;
    
END$$

DELIMITER ;

-- ============================================================================
-- VERIFICATION QUERIES
-- ============================================================================

-- Show new triggers
SHOW TRIGGERS WHERE `Table` = 'inventory_unit';

-- Show new procedures
SHOW PROCEDURE STATUS WHERE Db = 'optima_ci' 
AND Name LIKE '%attach%' OR Name LIKE '%sync_workflow%';

-- ============================================================================
-- END OF MIGRATION
-- ============================================================================
