-- Quick Implementation: Tambah harga field ke inventory_unit
ALTER TABLE `inventory_unit` 
ADD COLUMN `harga_sewa_bulanan` DECIMAL(15,2) NULL DEFAULT NULL COMMENT 'Harga sewa per bulan' AFTER `keterangan`,
ADD COLUMN `harga_sewa_harian` DECIMAL(15,2) NULL DEFAULT NULL COMMENT 'Harga sewa per hari' AFTER `harga_sewa_bulanan`;

-- Trigger otomatis update kontrak totals (MySQL)
DELIMITER $$
CREATE TRIGGER update_kontrak_totals_after_unit_insert
AFTER INSERT ON inventory_unit
FOR EACH ROW
BEGIN
    IF NEW.kontrak_id IS NOT NULL THEN
        UPDATE kontrak SET 
            total_units = (SELECT COUNT(*) FROM inventory_unit WHERE kontrak_id = NEW.kontrak_id),
            nilai_total = (SELECT COALESCE(SUM(harga_sewa_bulanan), 0) FROM inventory_unit WHERE kontrak_id = NEW.kontrak_id)
        WHERE id = NEW.kontrak_id;
    END IF;
END$$

CREATE TRIGGER update_kontrak_totals_after_unit_update  
AFTER UPDATE ON inventory_unit
FOR EACH ROW
BEGIN
    -- Update old kontrak if changed
    IF OLD.kontrak_id IS NOT NULL AND OLD.kontrak_id != NEW.kontrak_id THEN
        UPDATE kontrak SET 
            total_units = (SELECT COUNT(*) FROM inventory_unit WHERE kontrak_id = OLD.kontrak_id),
            nilai_total = (SELECT COALESCE(SUM(harga_sewa_bulanan), 0) FROM inventory_unit WHERE kontrak_id = OLD.kontrak_id)
        WHERE id = OLD.kontrak_id;
    END IF;
    
    -- Update new kontrak
    IF NEW.kontrak_id IS NOT NULL THEN
        UPDATE kontrak SET 
            total_units = (SELECT COUNT(*) FROM inventory_unit WHERE kontrak_id = NEW.kontrak_id),
            nilai_total = (SELECT COALESCE(SUM(harga_sewa_bulanan), 0) FROM inventory_unit WHERE kontrak_id = NEW.kontrak_id)
        WHERE id = NEW.kontrak_id;
    END IF;
END$$

CREATE TRIGGER update_kontrak_totals_after_unit_delete
AFTER DELETE ON inventory_unit  
FOR EACH ROW
BEGIN
    IF OLD.kontrak_id IS NOT NULL THEN
        UPDATE kontrak SET 
            total_units = (SELECT COUNT(*) FROM inventory_unit WHERE kontrak_id = OLD.kontrak_id),
            nilai_total = (SELECT COALESCE(SUM(harga_sewa_bulanan), 0) FROM inventory_unit WHERE kontrak_id = OLD.kontrak_id)
        WHERE id = OLD.kontrak_id;
    END IF;
END$$
DELIMITER ;
