-- ========================================
-- FIX INVENTORY UNIT WORKFLOW
-- Perbaikan alur workflow Kontrak -> SPK -> DI
-- ========================================

SET FOREIGN_KEY_CHECKS=0;

-- ========================================
-- 1. PERBAIKI STRUKTUR TABEL INVENTORY_UNIT
-- ========================================

-- Hapus field status_aset yang tidak berfungsi
ALTER TABLE `inventory_unit` DROP COLUMN IF EXISTS `status_aset`;

-- Tambah field aksesoris untuk menyimpan aksesoris dari kontrak_spesifikasi
ALTER TABLE `inventory_unit` 
ADD COLUMN `aksesoris` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Aksesoris yang dibutuhkan dari kontrak spesifikasi' AFTER `valve_id`;

-- Tambah field untuk tracking SPK dan DI
ALTER TABLE `inventory_unit`
ADD COLUMN `spk_id` INT(10) UNSIGNED DEFAULT NULL COMMENT 'FK ke SPK yang membuat unit ini' AFTER `kontrak_spesifikasi_id`,
ADD COLUMN `delivery_instruction_id` INT(10) UNSIGNED DEFAULT NULL COMMENT 'FK ke DI terakhir untuk unit ini' AFTER `spk_id`;

-- Tambah foreign key constraints yang benar
ALTER TABLE `inventory_unit`
ADD CONSTRAINT `fk_inventory_unit_spk` FOREIGN KEY (`spk_id`) REFERENCES `spk` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
ADD CONSTRAINT `fk_inventory_unit_delivery_instruction` FOREIGN KEY (`delivery_instruction_id`) REFERENCES `delivery_instructions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- ========================================
-- 2. UPDATE DATA EXISTING - AKSESORIS
-- ========================================

-- Update aksesoris dari kontrak_spesifikasi ke inventory_unit
UPDATE `inventory_unit` iu
JOIN `kontrak_spesifikasi` ks ON iu.kontrak_spesifikasi_id = ks.id
SET iu.aksesoris = ks.aksesoris
WHERE iu.kontrak_spesifikasi_id IS NOT NULL
AND ks.aksesoris IS NOT NULL;

-- ========================================
-- 3. UPDATE DATA EXISTING - LOKASI_UNIT
-- ========================================

-- Update lokasi_unit dengan data dari kontrak.pelanggan untuk unit yang terhubung kontrak
UPDATE `inventory_unit` iu
JOIN `kontrak` k ON iu.kontrak_id = k.id
SET iu.lokasi_unit = k.pelanggan
WHERE iu.kontrak_id IS NOT NULL;

-- ========================================
-- 4. UPDATE DATA EXISTING - TANGGAL_KIRIM
-- ========================================

-- Update tanggal_kirim dengan data dari delivery_instructions
-- Ambil tanggal kirim dari DI yang terkait dengan SPK unit tersebut
UPDATE `inventory_unit` iu
JOIN `spk` s ON iu.kontrak_spesifikasi_id = s.kontrak_spesifikasi_id
JOIN `delivery_instructions` di ON s.id = di.spk_id
SET iu.tanggal_kirim = di.tanggal_kirim,
    iu.spk_id = s.id,
    iu.delivery_instruction_id = di.id
WHERE di.tanggal_kirim IS NOT NULL
AND di.status IN ('DELIVERED', 'SHIPPED');

-- ========================================
-- 5. PERBAIKI INVENTORY_ATTACHMENT LOGIC
-- ========================================

-- Update lokasi_penyimpanan untuk attachment yang sudah terpasang di unit
UPDATE `inventory_attachment` ia
JOIN `inventory_unit` iu ON ia.id_inventory_unit = iu.id_inventory_unit
SET ia.lokasi_penyimpanan = CONCAT('Terpasang di Unit ', iu.no_unit)
WHERE ia.id_inventory_unit IS NOT NULL;

-- ========================================
-- 6. BUAT TRIGGER UNTUK AUTO UPDATE
-- ========================================

-- Trigger untuk auto update aksesoris saat kontrak_spesifikasi berubah
DELIMITER $$

DROP TRIGGER IF EXISTS `tr_kontrak_spesifikasi_aksesoris_update`$$

CREATE TRIGGER `tr_kontrak_spesifikasi_aksesoris_update`
    AFTER UPDATE ON `kontrak_spesifikasi`
    FOR EACH ROW
BEGIN
    -- Update aksesoris di inventory_unit saat kontrak_spesifikasi berubah
    IF NEW.aksesoris IS NOT NULL AND (OLD.aksesoris IS NULL OR OLD.aksesoris != NEW.aksesoris) THEN
        UPDATE `inventory_unit` 
        SET `aksesoris` = NEW.aksesoris,
            `updated_at` = CURRENT_TIMESTAMP
        WHERE `kontrak_spesifikasi_id` = NEW.id;
    END IF;
END$$

-- Trigger untuk auto update lokasi_penyimpanan saat attachment dipasang/dilepas
DROP TRIGGER IF EXISTS `tr_inventory_attachment_lokasi_update`$$

CREATE TRIGGER `tr_inventory_attachment_lokasi_update`
    AFTER UPDATE ON `inventory_attachment`
    FOR EACH ROW
BEGIN
    -- Jika attachment dipasang ke unit (id_inventory_unit berubah dari NULL ke nilai)
    IF OLD.id_inventory_unit IS NULL AND NEW.id_inventory_unit IS NOT NULL THEN
        UPDATE `inventory_attachment` 
        SET `lokasi_penyimpanan` = (
            SELECT CONCAT('Terpasang di Unit ', iu.no_unit)
            FROM `inventory_unit` iu 
            WHERE iu.id_inventory_unit = NEW.id_inventory_unit
            LIMIT 1
        )
        WHERE `id_inventory_attachment` = NEW.id_inventory_attachment;
        
    -- Jika attachment dilepas dari unit (id_inventory_unit berubah dari nilai ke NULL)
    ELSEIF OLD.id_inventory_unit IS NOT NULL AND NEW.id_inventory_unit IS NULL THEN
        UPDATE `inventory_attachment` 
        SET `lokasi_penyimpanan` = 'Gudang Pusat'
        WHERE `id_inventory_attachment` = NEW.id_inventory_attachment;
        
    -- Jika attachment dipindah ke unit lain
    ELSEIF OLD.id_inventory_unit IS NOT NULL AND NEW.id_inventory_unit IS NOT NULL 
           AND OLD.id_inventory_unit != NEW.id_inventory_unit THEN
        UPDATE `inventory_attachment` 
        SET `lokasi_penyimpanan` = (
            SELECT CONCAT('Terpasang di Unit ', iu.no_unit)
            FROM `inventory_unit` iu 
            WHERE iu.id_inventory_unit = NEW.id_inventory_unit
            LIMIT 1
        )
        WHERE `id_inventory_attachment` = NEW.id_inventory_attachment;
    END IF;
END$$

-- Trigger untuk auto update data inventory_unit saat SPK atau DI berubah
DROP TRIGGER IF EXISTS `tr_delivery_instructions_update_unit`$$

CREATE TRIGGER `tr_delivery_instructions_update_unit`
    AFTER UPDATE ON `delivery_instructions`
    FOR EACH ROW
BEGIN
    -- Update tanggal_kirim dan delivery_instruction_id di inventory_unit
    IF NEW.tanggal_kirim IS NOT NULL AND NEW.spk_id IS NOT NULL 
       AND (OLD.tanggal_kirim IS NULL OR OLD.tanggal_kirim != NEW.tanggal_kirim) THEN
        
        UPDATE `inventory_unit` iu
        JOIN `spk` s ON iu.spk_id = s.id OR iu.kontrak_spesifikasi_id = s.kontrak_spesifikasi_id
        SET iu.tanggal_kirim = NEW.tanggal_kirim,
            iu.delivery_instruction_id = NEW.id,
            iu.updated_at = CURRENT_TIMESTAMP
        WHERE s.id = NEW.spk_id;
    END IF;
END$$

-- Trigger untuk auto update saat unit di-assign ke SPK  
DROP TRIGGER IF EXISTS `tr_spk_update_unit`$$

CREATE TRIGGER `tr_spk_update_unit`
    AFTER UPDATE ON `spk`
    FOR EACH ROW
BEGIN
    -- Update inventory_unit saat SPK status berubah ke READY atau COMPLETED
    IF NEW.status IN ('READY', 'COMPLETED') AND OLD.status != NEW.status THEN
        UPDATE `inventory_unit` iu
        SET iu.spk_id = NEW.id,
            iu.updated_at = CURRENT_TIMESTAMP
        WHERE iu.kontrak_spesifikasi_id = NEW.kontrak_spesifikasi_id
        AND iu.spk_id IS NULL;
    END IF;
END$$

-- Trigger untuk auto update aksesoris saat kontrak_spesifikasi baru dibuat
DROP TRIGGER IF EXISTS `tr_kontrak_spesifikasi_aksesoris_insert`$$

CREATE TRIGGER `tr_kontrak_spesifikasi_aksesoris_insert`
    AFTER INSERT ON `kontrak_spesifikasi`
    FOR EACH ROW
BEGIN
    -- Update aksesoris di inventory_unit yang sudah ada untuk kontrak ini
    IF NEW.aksesoris IS NOT NULL THEN
        UPDATE `inventory_unit` 
        SET `aksesoris` = NEW.aksesoris,
            `kontrak_spesifikasi_id` = NEW.id,
            `updated_at` = CURRENT_TIMESTAMP
        WHERE `kontrak_id` = NEW.kontrak_id 
        AND `kontrak_spesifikasi_id` IS NULL;
    END IF;
END$$

DELIMITER ;

-- ========================================
-- 7. BUAT VIEW UNTUK MONITORING WORKFLOW
-- ========================================

-- View untuk melihat alur workflow lengkap
DROP VIEW IF EXISTS `vw_workflow_kontrak_spk_di`;

CREATE VIEW `vw_workflow_kontrak_spk_di` AS
SELECT 
    iu.id_inventory_unit,
    iu.no_unit,
    iu.serial_number,
    
    -- Data Kontrak
    k.id as kontrak_id,
    k.no_kontrak,
    k.pelanggan,
    k.lokasi as kontrak_lokasi,
    k.status as kontrak_status,
    
    -- Data Kontrak Spesifikasi  
    ks.id as kontrak_spesifikasi_id,
    ks.spek_kode,
    ks.aksesoris as spek_aksesoris,
    
    -- Data SPK
    s.id as spk_id,
    s.nomor_spk,
    s.status as spk_status,
    s.delivery_plan,
    
    -- Data Delivery Instructions
    di.id as delivery_instruction_id,
    di.nomor_di,
    di.tanggal_kirim,
    di.status as di_status,
    
    -- Data Unit
    iu.aksesoris as unit_aksesoris,
    iu.lokasi_unit,
    iu.status_unit_id,
    su.nama_status,
    
    -- Timestamps
    iu.created_at as unit_created,
    iu.updated_at as unit_updated
    
FROM `inventory_unit` iu
LEFT JOIN `kontrak` k ON iu.kontrak_id = k.id
LEFT JOIN `kontrak_spesifikasi` ks ON iu.kontrak_spesifikasi_id = ks.id
LEFT JOIN `spk` s ON iu.spk_id = s.id
LEFT JOIN `delivery_instructions` di ON iu.delivery_instruction_id = di.id
LEFT JOIN `status_unit` su ON iu.status_unit_id = su.id_status
ORDER BY iu.id_inventory_unit;

-- View untuk monitoring attachment yang terpasang
DROP VIEW IF EXISTS `vw_attachment_installed`;

CREATE VIEW `vw_attachment_installed` AS
SELECT 
    ia.id_inventory_attachment,
    ia.tipe_item,
    
    -- Data Attachment
    CASE 
        WHEN ia.tipe_item = 'attachment' THEN a.nama_attachment
        WHEN ia.tipe_item = 'battery' THEN b.jenis_baterai
        WHEN ia.tipe_item = 'charger' THEN c.tipe_charger
    END as item_name,
    
    CASE 
        WHEN ia.tipe_item = 'attachment' THEN ia.sn_attachment
        WHEN ia.tipe_item = 'battery' THEN ia.sn_baterai
        WHEN ia.tipe_item = 'charger' THEN ia.sn_charger
    END as serial_number,
    
    -- Data Unit (jika terpasang)
    iu.id_inventory_unit,
    iu.no_unit,
    iu.serial_number as unit_serial,
    
    -- Lokasi
    ia.lokasi_penyimpanan,
    
    -- Status
    ia.status_unit,
    su.nama_status,
    
    -- Timestamps
    ia.created_at,
    ia.updated_at
    
FROM `inventory_attachment` ia
LEFT JOIN `inventory_unit` iu ON ia.id_inventory_unit = iu.id_inventory_unit
LEFT JOIN `attachment` a ON ia.attachment_id = a.id_attachment
LEFT JOIN `baterai` b ON ia.baterai_id = b.id
LEFT JOIN `charger` c ON ia.charger_id = c.id_charger
LEFT JOIN `status_unit` su ON ia.status_unit = su.id_status
ORDER BY ia.id_inventory_attachment;

-- ========================================
-- 8. STORED PROCEDURE UNTUK WORKFLOW MANAGEMENT
-- ========================================

-- Procedure untuk assign unit ke SPK dengan aksesoris
DELIMITER $$

DROP PROCEDURE IF EXISTS `sp_assign_unit_to_spk`$$

CREATE PROCEDURE `sp_assign_unit_to_spk`(
    IN p_unit_id INT,
    IN p_spk_id INT,
    IN p_kontrak_spesifikasi_id INT
)
BEGIN
    DECLARE v_aksesoris TEXT;
    DECLARE v_kontrak_id INT;
    DECLARE v_pelanggan VARCHAR(255);
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    -- Get aksesoris dari kontrak_spesifikasi
    SELECT ks.aksesoris, ks.kontrak_id, k.pelanggan 
    INTO v_aksesoris, v_kontrak_id, v_pelanggan
    FROM kontrak_spesifikasi ks
    JOIN kontrak k ON ks.kontrak_id = k.id 
    WHERE ks.id = p_kontrak_spesifikasi_id;
    
    -- Update inventory_unit
    UPDATE inventory_unit 
    SET 
        spk_id = p_spk_id,
        kontrak_spesifikasi_id = p_kontrak_spesifikasi_id,
        kontrak_id = v_kontrak_id,
        aksesoris = v_aksesoris,
        lokasi_unit = v_pelanggan,
        updated_at = CURRENT_TIMESTAMP
    WHERE id_inventory_unit = p_unit_id;
    
    COMMIT;
    
END$$

-- Procedure untuk attach item ke unit
DROP PROCEDURE IF EXISTS `sp_attach_item_to_unit`$$

CREATE PROCEDURE `sp_attach_item_to_unit`(
    IN p_attachment_id INT,
    IN p_unit_id INT
)
BEGIN
    DECLARE v_unit_no INT;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    -- Get unit number
    SELECT no_unit INTO v_unit_no
    FROM inventory_unit 
    WHERE id_inventory_unit = p_unit_id;
    
    -- Validation: pastikan unit ada
    IF v_unit_no IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Unit tidak ditemukan';
    END IF;
    
    -- Update inventory_attachment
    UPDATE inventory_attachment 
    SET 
        id_inventory_unit = p_unit_id,
        lokasi_penyimpanan = CONCAT('Terpasang di Unit ', v_unit_no),
        updated_at = CURRENT_TIMESTAMP
    WHERE id_inventory_attachment = p_attachment_id;
    
    COMMIT;
    
END$$

-- Procedure untuk detach item dari unit
DROP PROCEDURE IF EXISTS `sp_detach_item_from_unit`$$

CREATE PROCEDURE `sp_detach_item_from_unit`(
    IN p_attachment_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    -- Update inventory_attachment
    UPDATE inventory_attachment 
    SET 
        id_inventory_unit = NULL,
        lokasi_penyimpanan = 'Gudang Pusat',
        updated_at = CURRENT_TIMESTAMP
    WHERE id_inventory_attachment = p_attachment_id;
    
    COMMIT;
    
END$$

DELIMITER ;

-- ========================================
-- 9. PROCEDURE TAMBAHAN UNTUK SYNC DATA
-- ========================================

DELIMITER $$

-- Procedure untuk sync semua data workflow (manual sync jika ada data yang missing)
DROP PROCEDURE IF EXISTS `sp_sync_workflow_data`$$

CREATE PROCEDURE `sp_sync_workflow_data`()
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    -- Sync aksesoris dari kontrak_spesifikasi
    UPDATE inventory_unit iu
    JOIN kontrak_spesifikasi ks ON iu.kontrak_spesifikasi_id = ks.id
    SET iu.aksesoris = ks.aksesoris,
        iu.updated_at = CURRENT_TIMESTAMP
    WHERE iu.kontrak_spesifikasi_id IS NOT NULL
    AND (iu.aksesoris IS NULL OR iu.aksesoris != ks.aksesoris);
    
    -- Sync lokasi_unit dari kontrak.pelanggan
    UPDATE inventory_unit iu
    JOIN kontrak k ON iu.kontrak_id = k.id
    SET iu.lokasi_unit = k.pelanggan,
        iu.updated_at = CURRENT_TIMESTAMP
    WHERE iu.kontrak_id IS NOT NULL
    AND (iu.lokasi_unit IS NULL OR iu.lokasi_unit != k.pelanggan);
    
    -- Sync tanggal_kirim dari delivery_instructions
    UPDATE inventory_unit iu
    JOIN spk s ON (iu.spk_id = s.id OR iu.kontrak_spesifikasi_id = s.kontrak_spesifikasi_id)
    JOIN delivery_instructions di ON s.id = di.spk_id
    SET iu.tanggal_kirim = di.tanggal_kirim,
        iu.spk_id = COALESCE(iu.spk_id, s.id),
        iu.delivery_instruction_id = di.id,
        iu.updated_at = CURRENT_TIMESTAMP
    WHERE di.tanggal_kirim IS NOT NULL
    AND di.status IN ('DELIVERED', 'SHIPPED')
    AND (iu.tanggal_kirim IS NULL OR iu.tanggal_kirim != di.tanggal_kirim);
    
    -- Sync lokasi_penyimpanan untuk attachment yang terpasang
    UPDATE inventory_attachment ia
    JOIN inventory_unit iu ON ia.id_inventory_unit = iu.id_inventory_unit
    SET ia.lokasi_penyimpanan = CONCAT('Terpasang di Unit ', iu.no_unit),
        ia.updated_at = CURRENT_TIMESTAMP
    WHERE ia.id_inventory_unit IS NOT NULL
    AND (ia.lokasi_penyimpanan IS NULL OR ia.lokasi_penyimpanan != CONCAT('Terpasang di Unit ', iu.no_unit));
    
    COMMIT;
    
    -- Return summary
    SELECT 
        'Workflow data sync completed' as message,
        (SELECT COUNT(*) FROM inventory_unit WHERE aksesoris IS NOT NULL) as units_with_accessories,
        (SELECT COUNT(*) FROM inventory_unit WHERE tanggal_kirim IS NOT NULL) as units_with_delivery_date,
        (SELECT COUNT(*) FROM inventory_attachment WHERE lokasi_penyimpanan LIKE 'Terpasang di Unit%') as attached_items;
    
END$$

DELIMITER ;

SET FOREIGN_KEY_CHECKS=1;

-- ========================================
-- SUMMARY OF CHANGES
-- ========================================
/*
1. Removed unused 'status_aset' field from inventory_unit
2. Added 'aksesoris' field to store accessories from kontrak_spesifikasi
3. Added 'spk_id' and 'delivery_instruction_id' for workflow tracking
4. Updated existing data:
   - lokasi_unit now pulls from kontrak.pelanggan
   - tanggal_kirim now pulls from delivery_instructions.tanggal_kirim
   - aksesoris now pulls from kontrak_spesifikasi.aksesoris
5. Fixed inventory_attachment logic for "Terpasang di Unit X"
6. Added triggers for automatic updates
7. Created views for workflow monitoring
8. Added stored procedures for workflow management

WORKFLOW PATH: Kontrak -> Kontrak_Spesifikasi -> SPK -> Delivery_Instructions -> Inventory_Unit
*/

-- ========================================
-- 10. CONTOH PENGGUNAAN SETELAH SCRIPT DIJALANKAN
-- ========================================

/*
-- 1. Assign unit ID 1 ke SPK ID 27 dengan kontrak spesifikasi ID 19
CALL sp_assign_unit_to_spk(1, 27, 19);

-- 2. Attach inventory attachment ID 2 ke unit ID 1
CALL sp_attach_item_to_unit(2, 1);

-- 3. Detach inventory attachment ID 2 dari unit
CALL sp_detach_item_from_unit(2);

-- 4. Sync semua data workflow (jalankan jika ada data yang tidak tersync)
CALL sp_sync_workflow_data();

-- 5. Lihat workflow lengkap untuk unit tertentu
SELECT * FROM vw_workflow_kontrak_spk_di WHERE no_unit = 1;

-- 6. Lihat semua attachment yang terpasang
SELECT * FROM vw_attachment_installed WHERE no_unit IS NOT NULL;

-- 7. Lihat unit berdasarkan kontrak
SELECT * FROM vw_workflow_kontrak_spk_di WHERE kontrak_id = 44;

-- 8. Cek unit mana yang belum ada aksesoris
SELECT id_inventory_unit, no_unit, kontrak_id 
FROM inventory_unit 
WHERE kontrak_id IS NOT NULL AND aksesoris IS NULL;

-- 9. Cek attachment yang belum terpasang
SELECT * FROM vw_attachment_installed WHERE no_unit IS NULL;
*/

-- ========================================
-- 11. TESTING SCRIPT (OPSIONAL)
-- ========================================

/*
-- Test 1: Cek apakah field aksesoris sudah terisi
SELECT COUNT(*) as units_with_accessories 
FROM inventory_unit 
WHERE aksesoris IS NOT NULL;

-- Test 2: Cek apakah lokasi_unit sudah terisi dari pelanggan
SELECT COUNT(*) as units_with_location 
FROM inventory_unit iu
JOIN kontrak k ON iu.kontrak_id = k.id
WHERE iu.lokasi_unit = k.pelanggan;

-- Test 3: Cek apakah attachment yang terpasang sudah punya lokasi yang benar
SELECT COUNT(*) as correct_attachment_locations
FROM inventory_attachment ia
JOIN inventory_unit iu ON ia.id_inventory_unit = iu.id_inventory_unit
WHERE ia.lokasi_penyimpanan = CONCAT('Terpasang di Unit ', iu.no_unit);

-- Test 4: Validasi foreign key relationships
SELECT 
    COUNT(CASE WHEN iu.kontrak_id IS NOT NULL AND k.id IS NULL THEN 1 END) as invalid_kontrak_fk,
    COUNT(CASE WHEN iu.kontrak_spesifikasi_id IS NOT NULL AND ks.id IS NULL THEN 1 END) as invalid_kontrak_spek_fk,
    COUNT(CASE WHEN iu.spk_id IS NOT NULL AND s.id IS NULL THEN 1 END) as invalid_spk_fk,
    COUNT(CASE WHEN iu.delivery_instruction_id IS NOT NULL AND di.id IS NULL THEN 1 END) as invalid_di_fk
FROM inventory_unit iu
LEFT JOIN kontrak k ON iu.kontrak_id = k.id
LEFT JOIN kontrak_spesifikasi ks ON iu.kontrak_spesifikasi_id = ks.id
LEFT JOIN spk s ON iu.spk_id = s.id
LEFT JOIN delivery_instructions di ON iu.delivery_instruction_id = di.id;
*/

-- ========================================
-- 12. MAINTENANCE QUERIES
-- ========================================

/*
-- Query untuk monitoring workflow harian
CREATE EVENT IF NOT EXISTS ev_daily_workflow_sync
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_DATE + INTERVAL 1 DAY + INTERVAL 2 HOUR
DO
  CALL sp_sync_workflow_data();

-- Query untuk backup data sebelum perubahan besar
CREATE TABLE backup_inventory_unit_$(DATE_FORMAT(NOW(), '%Y%m%d')) AS 
SELECT * FROM inventory_unit;

CREATE TABLE backup_inventory_attachment_$(DATE_FORMAT(NOW(), '%Y%m%d')) AS 
SELECT * FROM inventory_attachment;
*/
