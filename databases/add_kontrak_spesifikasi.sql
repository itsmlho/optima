-- Phase 2: Multi-Specification Kontrak Enhancement
-- Support multiple specifications per contract with different unit counts and pricing

-- 1. Create kontrak_spesifikasi table
CREATE TABLE `kontrak_spesifikasi` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `kontrak_id` INT UNSIGNED NOT NULL,
    `spek_kode` VARCHAR(50) NOT NULL COMMENT 'Kode unik spesifikasi dalam kontrak (A, B, C)',
    `jumlah_dibutuhkan` INT NOT NULL DEFAULT 1 COMMENT 'Jumlah unit yang dibutuhkan untuk spek ini',
    `jumlah_tersedia` INT NOT NULL DEFAULT 0 COMMENT 'Jumlah unit yang sudah di-assign',
    `harga_per_unit_bulanan` DECIMAL(15,2) DEFAULT NULL COMMENT 'Harga sewa bulanan per unit',
    `harga_per_unit_harian` DECIMAL(15,2) DEFAULT NULL COMMENT 'Harga sewa harian per unit',
    `catatan_spek` TEXT DEFAULT NULL COMMENT 'Catatan khusus untuk spesifikasi ini',
    
    -- Spesifikasi Detail (dari form SPK yang ada)
    `departemen_id` INT DEFAULT NULL,
    `tipe_unit_id` INT DEFAULT NULL,
    `tipe_jenis` VARCHAR(100) DEFAULT NULL,
    `kapasitas_id` INT DEFAULT NULL,
    `merk_unit` VARCHAR(100) DEFAULT NULL,
    `model_unit` VARCHAR(100) DEFAULT NULL,
    `attachment_tipe` VARCHAR(100) DEFAULT NULL,
    `attachment_merk` VARCHAR(100) DEFAULT NULL,
    `jenis_baterai` VARCHAR(100) DEFAULT NULL,
    `charger_id` INT DEFAULT NULL,
    `mast_id` INT DEFAULT NULL,
    `ban_id` INT DEFAULT NULL,
    `roda_id` INT DEFAULT NULL,
    `valve_id` INT DEFAULT NULL,
    `aksesoris` JSON DEFAULT NULL COMMENT 'Array aksesoris yang dibutuhkan',
    
    `dibuat_pada` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `diperbarui_pada` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_kontrak_spek` (`kontrak_id`, `spek_kode`),
    FOREIGN KEY (`kontrak_id`) REFERENCES `kontrak` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`departemen_id`) REFERENCES `departemen` (`id_departemen`) ON DELETE SET NULL,
    FOREIGN KEY (`tipe_unit_id`) REFERENCES `tipe_unit` (`id_tipe_unit`) ON DELETE SET NULL,
    FOREIGN KEY (`kapasitas_id`) REFERENCES `kapasitas` (`id_kapasitas`) ON DELETE SET NULL,
    FOREIGN KEY (`charger_id`) REFERENCES `charger` (`id_charger`) ON DELETE SET NULL,
    FOREIGN KEY (`mast_id`) REFERENCES `tipe_mast` (`id_mast`) ON DELETE SET NULL,
    FOREIGN KEY (`ban_id`) REFERENCES `tipe_ban` (`id_ban`) ON DELETE SET NULL,
    FOREIGN KEY (`roda_id`) REFERENCES `jenis_roda` (`id_roda`) ON DELETE SET NULL,
    FOREIGN KEY (`valve_id`) REFERENCES `valve` (`id_valve`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Update tabel spk - tambah reference ke kontrak_spesifikasi
ALTER TABLE `spk` 
ADD COLUMN `kontrak_spesifikasi_id` INT UNSIGNED DEFAULT NULL COMMENT 'FK ke kontrak_spesifikasi' AFTER `kontrak_id`,
ADD COLUMN `jumlah_unit` INT DEFAULT 1 COMMENT 'Jumlah unit dalam SPK ini' AFTER `kontrak_spesifikasi_id`,
ADD FOREIGN KEY `fk_spk_kontrak_spesifikasi` (`kontrak_spesifikasi_id`) REFERENCES `kontrak_spesifikasi` (`id`) ON DELETE SET NULL;

-- 3. Update tabel inventory_unit - tambah reference ke kontrak_spesifikasi
ALTER TABLE `inventory_unit`
ADD COLUMN `kontrak_spesifikasi_id` INT UNSIGNED DEFAULT NULL COMMENT 'FK ke kontrak_spesifikasi untuk tracking spek mana' AFTER `kontrak_id`,
ADD FOREIGN KEY `fk_inventory_unit_kontrak_spesifikasi` (`kontrak_spesifikasi_id`) REFERENCES `kontrak_spesifikasi` (`id`) ON DELETE SET NULL;

-- 4. Update stored procedure untuk handle kontrak_spesifikasi
DROP PROCEDURE IF EXISTS update_kontrak_totals_proc;

DELIMITER $$

CREATE PROCEDURE update_kontrak_totals_proc(IN kontrak_id_param INT UNSIGNED)
BEGIN
    DECLARE total_units_count INT DEFAULT 0;
    DECLARE nilai_total_amount DECIMAL(15,2) DEFAULT 0;
    DECLARE jenis_sewa_kontrak VARCHAR(10) DEFAULT 'BULANAN';
    
    -- Get kontrak jenis_sewa
    SELECT jenis_sewa INTO jenis_sewa_kontrak 
    FROM kontrak 
    WHERE id = kontrak_id_param;
    
    -- Set default jika NULL
    IF jenis_sewa_kontrak IS NULL THEN
        SET jenis_sewa_kontrak = 'BULANAN';
    END IF;
    
    -- Update jumlah_tersedia di kontrak_spesifikasi
    UPDATE kontrak_spesifikasi ks SET 
        jumlah_tersedia = (
            SELECT COUNT(*) 
            FROM inventory_unit iu 
            WHERE iu.kontrak_spesifikasi_id = ks.id
        )
    WHERE ks.kontrak_id = kontrak_id_param;
    
    -- Calculate totals dari kontrak_spesifikasi (lebih akurat dari inventory_unit langsung)
    IF jenis_sewa_kontrak = 'HARIAN' THEN
        SELECT 
            COALESCE(SUM(ks.jumlah_dibutuhkan), 0) as total_units,
            COALESCE(SUM(ks.jumlah_dibutuhkan * ks.harga_per_unit_harian), 0) as nilai_total
        INTO total_units_count, nilai_total_amount
        FROM kontrak_spesifikasi ks
        WHERE ks.kontrak_id = kontrak_id_param;
    ELSE
        -- Default BULANAN
        SELECT 
            COALESCE(SUM(ks.jumlah_dibutuhkan), 0) as total_units,
            COALESCE(SUM(ks.jumlah_dibutuhkan * ks.harga_per_unit_bulanan), 0) as nilai_total
        INTO total_units_count, nilai_total_amount
        FROM kontrak_spesifikasi ks
        WHERE ks.kontrak_id = kontrak_id_param;
    END IF;
    
    -- Update kontrak
    UPDATE kontrak SET 
        total_units = total_units_count,
        nilai_total = nilai_total_amount
    WHERE id = kontrak_id_param;
    
END$$

DELIMITER ;

-- 5. Add triggers untuk kontrak_spesifikasi table
DELIMITER $$

CREATE TRIGGER update_kontrak_totals_after_spek_insert
AFTER INSERT ON kontrak_spesifikasi
FOR EACH ROW
BEGIN
    CALL update_kontrak_totals_proc(NEW.kontrak_id);
END$$

CREATE TRIGGER update_kontrak_totals_after_spek_update  
AFTER UPDATE ON kontrak_spesifikasi
FOR EACH ROW
BEGIN
    CALL update_kontrak_totals_proc(NEW.kontrak_id);
    
    -- If kontrak_id changed, update old kontrak too
    IF OLD.kontrak_id != NEW.kontrak_id THEN
        CALL update_kontrak_totals_proc(OLD.kontrak_id);
    END IF;
END$$

CREATE TRIGGER update_kontrak_totals_after_spek_delete
AFTER DELETE ON kontrak_spesifikasi  
FOR EACH ROW
BEGIN
    CALL update_kontrak_totals_proc(OLD.kontrak_id);
END$$

DELIMITER ;

-- 6. Update existing triggers untuk inventory_unit (gunakan kontrak_spesifikasi jika ada)
DROP TRIGGER IF EXISTS update_kontrak_totals_after_unit_insert;
DROP TRIGGER IF EXISTS update_kontrak_totals_after_unit_update;
DROP TRIGGER IF EXISTS update_kontrak_totals_after_unit_delete;

DELIMITER $$

CREATE TRIGGER update_kontrak_totals_after_unit_insert
AFTER INSERT ON inventory_unit
FOR EACH ROW
BEGIN
    IF NEW.kontrak_id IS NOT NULL THEN
        CALL update_kontrak_totals_proc(NEW.kontrak_id);
    END IF;
END$$

CREATE TRIGGER update_kontrak_totals_after_unit_update  
AFTER UPDATE ON inventory_unit
FOR EACH ROW
BEGIN
    -- Update old kontrak if changed
    IF OLD.kontrak_id IS NOT NULL AND (OLD.kontrak_id != NEW.kontrak_id OR NEW.kontrak_id IS NULL) THEN
        CALL update_kontrak_totals_proc(OLD.kontrak_id);
    END IF;
    
    -- Update new kontrak
    IF NEW.kontrak_id IS NOT NULL THEN
        CALL update_kontrak_totals_proc(NEW.kontrak_id);
    END IF;
END$$

CREATE TRIGGER update_kontrak_totals_after_unit_delete
AFTER DELETE ON inventory_unit  
FOR EACH ROW
BEGIN
    IF OLD.kontrak_id IS NOT NULL THEN
        CALL update_kontrak_totals_proc(OLD.kontrak_id);
    END IF;
END$$

DELIMITER ;

-- 7. Sample data untuk testing
/*
-- Contoh: Kontrak dengan 3 spesifikasi berbeda
INSERT INTO kontrak_spesifikasi (kontrak_id, spek_kode, jumlah_dibutuhkan, harga_per_unit_bulanan, departemen_id, tipe_jenis, kapasitas_id) VALUES
(1, 'A', 2, 8000000.00, 1, 'DIESEL', 1),  -- 2 unit Diesel 3T @ 8jt
(1, 'B', 3, 10000000.00, 2, 'ELECTRIC', 2), -- 3 unit Electric 5T @ 10jt  
(1, 'C', 5, 12000000.00, 1, 'DIESEL', 3);   -- 5 unit Diesel 7T @ 12jt
*/
