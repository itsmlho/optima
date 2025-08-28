-- Script untuk menambahkan kolom tipe_item pada tabel inventory_attachment
-- Untuk membedakan antara attachment, battery, dan charger

USE `optima_db`;

-- 1. Tambahkan kolom tipe_item untuk kategorisasi
ALTER TABLE `inventory_attachment` 
ADD COLUMN `tipe_item` ENUM('attachment', 'battery', 'charger') NOT NULL DEFAULT 'attachment' 
AFTER `id_inventory_attachment`,
ADD INDEX `idx_tipe_item` (`tipe_item`);

-- 2. Update existing data berdasarkan field yang terisi
-- Jika attachment_id terisi, maka tipe_item = 'attachment'
UPDATE `inventory_attachment` 
SET `tipe_item` = 'attachment' 
WHERE `attachment_id` IS NOT NULL AND `sn_attachment` IS NOT NULL;

-- Jika baterai_id terisi, maka tipe_item = 'battery'
UPDATE `inventory_attachment` 
SET `tipe_item` = 'battery' 
WHERE `baterai_id` IS NOT NULL AND `sn_baterai` IS NOT NULL;

-- Jika charger_id terisi, maka tipe_item = 'charger'
UPDATE `inventory_attachment` 
SET `tipe_item` = 'charger' 
WHERE `charger_id` IS NOT NULL AND `sn_charger` IS NOT NULL;

-- 3. Tambahkan constraint untuk memastikan konsistensi data
-- NOTE: CHECK constraint tidak bisa diterapkan pada kolom dengan foreign key referential action
-- Sebagai gantinya, konsistensi data akan dijaga melalui aplikasi logic

-- Alternatif: Buat trigger untuk memastikan konsistensi
DELIMITER //

CREATE TRIGGER `trg_inventory_attachment_consistency_insert`
BEFORE INSERT ON `inventory_attachment`
FOR EACH ROW
BEGIN
    -- Validasi: pastikan setiap record memiliki item sesuai tipe_item
    IF (NEW.tipe_item = 'attachment' AND (NEW.attachment_id IS NULL OR NEW.sn_attachment IS NULL)) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Attachment tipe_item requires attachment_id and sn_attachment';
    END IF;
    
    IF (NEW.tipe_item = 'battery' AND (NEW.baterai_id IS NULL OR NEW.sn_baterai IS NULL)) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Battery tipe_item requires baterai_id and sn_baterai';
    END IF;
    
    IF (NEW.tipe_item = 'charger' AND (NEW.charger_id IS NULL OR NEW.sn_charger IS NULL)) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Charger tipe_item requires charger_id and sn_charger';
    END IF;
END //

CREATE TRIGGER `trg_inventory_attachment_consistency_update`
BEFORE UPDATE ON `inventory_attachment`
FOR EACH ROW
BEGIN
    -- Validasi: pastikan setiap record memiliki item sesuai tipe_item
    IF (NEW.tipe_item = 'attachment' AND (NEW.attachment_id IS NULL OR NEW.sn_attachment IS NULL)) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Attachment tipe_item requires attachment_id and sn_attachment';
    END IF;
    
    IF (NEW.tipe_item = 'battery' AND (NEW.baterai_id IS NULL OR NEW.sn_baterai IS NULL)) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Battery tipe_item requires baterai_id and sn_baterai';
    END IF;
    
    IF (NEW.tipe_item = 'charger' AND (NEW.charger_id IS NULL OR NEW.sn_charger IS NULL)) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Charger tipe_item requires charger_id and sn_charger';
    END IF;
END //

DELIMITER ;

-- 4. Tampilkan struktur tabel yang sudah diupdate
DESCRIBE `inventory_attachment`;

-- 5. Contoh query untuk mengambil data berdasarkan tipe
-- Attachment saja
SELECT ia.*, a.tipe, a.merk, a.model 
FROM inventory_attachment ia
LEFT JOIN attachment a ON ia.attachment_id = a.id_attachment
WHERE ia.tipe_item = 'attachment';

-- Battery saja  
SELECT ia.*, b.merk_baterai, b.tipe_baterai, b.jenis_baterai
FROM inventory_attachment ia
LEFT JOIN baterai b ON ia.baterai_id = b.id
WHERE ia.tipe_item = 'battery';

-- Charger saja
SELECT ia.*, c.merk_charger, c.tipe_charger
FROM inventory_attachment ia
LEFT JOIN charger c ON ia.charger_id = c.id_charger
WHERE ia.tipe_item = 'charger';
