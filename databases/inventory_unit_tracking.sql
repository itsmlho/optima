-- Script untuk tracking inventory attachment ke unit setelah DI selesai (SAMPAI)
-- Implementasi: Ketika DI status = "SAMPAI", attachment/battery/charger yang dipilih di SPK 
-- harus terhubung dengan inventory_unit untuk tracking

-- 1. Procedure untuk menghubungkan inventory attachment ke unit setelah DI sampai
DELIMITER $$

CREATE OR REPLACE PROCEDURE LinkInventoryToUnitAfterDelivery(
    IN p_di_id INT,
    IN p_spk_id INT,
    IN p_unit_id INT
)
BEGIN
    DECLARE v_spk_spesifikasi JSON;
    DECLARE v_persiapan_battery_id INT DEFAULT NULL;
    DECLARE v_persiapan_charger_id INT DEFAULT NULL;
    DECLARE v_fabrikasi_attachment_id INT DEFAULT NULL;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    -- Ambil data spesifikasi dari SPK
    SELECT spesifikasi INTO v_spk_spesifikasi 
    FROM spk 
    WHERE id = p_spk_id;
    
    -- Extract inventory IDs dari JSON spesifikasi
    SET v_persiapan_battery_id = JSON_UNQUOTE(JSON_EXTRACT(v_spk_spesifikasi, '$.persiapan_battery_id'));
    SET v_persiapan_charger_id = JSON_UNQUOTE(JSON_EXTRACT(v_spk_spesifikasi, '$.persiapan_charger_id'));
    SET v_fabrikasi_attachment_id = JSON_UNQUOTE(JSON_EXTRACT(v_spk_spesifikasi, '$.fabrikasi_attachment_id'));
    
    -- Link battery ke unit jika ada
    IF v_persiapan_battery_id IS NOT NULL AND v_persiapan_battery_id != 'null' THEN
        UPDATE inventory_attachment 
        SET 
            id_inventory_unit = p_unit_id,
            status_unit = 3, -- RENTAL
            lokasi_penyimpanan = NULL,
            updated_at = NOW()
        WHERE id_inventory_attachment = v_persiapan_battery_id
        AND tipe_item = 'battery'
        AND status_unit = 7; -- STOCK ASET
        
        -- Log activity
        INSERT INTO inventory_item_unit_log (
            id_inventory_attachment,
            id_inventory_unit,
            action,
            user_id,
            note,
            created_at
        ) VALUES (
            v_persiapan_battery_id,
            p_unit_id,
            'assign_after_delivery',
            1, -- System user
            CONCAT('Auto-assigned after DI #', p_di_id, ' delivered'),
            NOW()
        );
    END IF;
    
    -- Link charger ke unit jika ada
    IF v_persiapan_charger_id IS NOT NULL AND v_persiapan_charger_id != 'null' THEN
        UPDATE inventory_attachment 
        SET 
            id_inventory_unit = p_unit_id,
            status_unit = 3, -- RENTAL
            lokasi_penyimpanan = NULL,
            updated_at = NOW()
        WHERE id_inventory_attachment = v_persiapan_charger_id
        AND tipe_item = 'charger'
        AND status_unit = 7; -- STOCK ASET
        
        -- Log activity
        INSERT INTO inventory_item_unit_log (
            id_inventory_attachment,
            id_inventory_unit,
            action,
            user_id,
            note,
            created_at
        ) VALUES (
            v_persiapan_charger_id,
            p_unit_id,
            'assign_after_delivery',
            1, -- System user
            CONCAT('Auto-assigned after DI #', p_di_id, ' delivered'),
            NOW()
        );
    END IF;
    
    -- Link attachment ke unit jika ada
    IF v_fabrikasi_attachment_id IS NOT NULL AND v_fabrikasi_attachment_id != 'null' THEN
        UPDATE inventory_attachment 
        SET 
            id_inventory_unit = p_unit_id,
            status_unit = 3, -- RENTAL
            lokasi_penyimpanan = NULL,
            updated_at = NOW()
        WHERE id_inventory_attachment = v_fabrikasi_attachment_id
        AND tipe_item = 'attachment'
        AND status_unit = 7; -- STOCK ASET
        
        -- Log activity
        INSERT INTO inventory_item_unit_log (
            id_inventory_attachment,
            id_inventory_unit,
            action,
            user_id,
            note,
            created_at
        ) VALUES (
            v_fabrikasi_attachment_id,
            p_unit_id,
            'assign_after_delivery',
            1, -- System user
            CONCAT('Auto-assigned after DI #', p_di_id, ' delivered'),
            NOW()
        );
    END IF;
    
    COMMIT;
    
END$$

DELIMITER ;

-- 2. Trigger untuk otomatis memanggil procedure ketika DI status berubah ke "SAMPAI"
DELIMITER $$

CREATE OR REPLACE TRIGGER tr_delivery_instructions_status_update
AFTER UPDATE ON delivery_instructions
FOR EACH ROW
BEGIN
    DECLARE v_unit_id INT DEFAULT NULL;
    
    -- Jika status berubah menjadi SAMPAI
    IF OLD.status != 'SAMPAI' AND NEW.status = 'SAMPAI' AND NEW.spk_id IS NOT NULL THEN
        
        -- Cari unit_id dari delivery_items yang terkait dengan DI ini
        SELECT unit_id INTO v_unit_id
        FROM delivery_items 
        WHERE di_id = NEW.id 
        AND item_type = 'UNIT' 
        AND unit_id IS NOT NULL
        LIMIT 1;
        
        -- Jika ada unit, panggil procedure untuk link inventory
        IF v_unit_id IS NOT NULL THEN
            CALL LinkInventoryToUnitAfterDelivery(NEW.id, NEW.spk_id, v_unit_id);
        END IF;
        
    END IF;
END$$

DELIMITER ;

-- 3. Query untuk melihat tracking inventory attachment per unit
-- Contoh query untuk melihat unit mana yang menggunakan attachment/battery/charger tertentu
/*
SELECT 
    iu.no_unit,
    iu.serial_number,
    ia.tipe_item,
    ia.id_inventory_attachment,
    CASE 
        WHEN ia.tipe_item = 'battery' THEN CONCAT(b.merk_baterai, ' ', b.tipe_baterai, ' (SN: ', ia.sn_baterai, ')')
        WHEN ia.tipe_item = 'charger' THEN CONCAT(c.merk_charger, ' ', c.tipe_charger, ' (SN: ', ia.sn_charger, ')')
        WHEN ia.tipe_item = 'attachment' THEN CONCAT(a.tipe, ' ', a.merk, ' ', a.model, ' (SN: ', ia.sn_attachment, ')')
    END as item_info,
    ia.status_unit,
    ia.lokasi_penyimpanan,
    ia.updated_at as linked_at
FROM inventory_unit iu
LEFT JOIN inventory_attachment ia ON ia.id_inventory_unit = iu.id_inventory_unit
LEFT JOIN baterai b ON b.id = ia.baterai_id AND ia.tipe_item = 'battery'
LEFT JOIN charger c ON c.id_charger = ia.charger_id AND ia.tipe_item = 'charger'  
LEFT JOIN attachment a ON a.id_attachment = ia.attachment_id AND ia.tipe_item = 'attachment'
WHERE ia.id_inventory_attachment IS NOT NULL
ORDER BY iu.no_unit, ia.tipe_item;
*/

-- 4. Query untuk melihat history log assignment
/*
SELECT 
    log.created_at,
    log.action,
    log.note,
    iu.no_unit,
    ia.tipe_item,
    CASE 
        WHEN ia.tipe_item = 'battery' THEN CONCAT(b.merk_baterai, ' ', b.tipe_baterai)
        WHEN ia.tipe_item = 'charger' THEN CONCAT(c.merk_charger, ' ', c.tipe_charger)
        WHEN ia.tipe_item = 'attachment' THEN CONCAT(a.tipe, ' ', a.merk, ' ', a.model)
    END as item_name
FROM inventory_item_unit_log log
JOIN inventory_attachment ia ON ia.id_inventory_attachment = log.id_inventory_attachment
JOIN inventory_unit iu ON iu.id_inventory_unit = log.id_inventory_unit
LEFT JOIN baterai b ON b.id = ia.baterai_id AND ia.tipe_item = 'battery'
LEFT JOIN charger c ON c.id_charger = ia.charger_id AND ia.tipe_item = 'charger'
LEFT JOIN attachment a ON a.id_attachment = ia.attachment_id AND ia.tipe_item = 'attachment'
ORDER BY log.created_at DESC;
*/
