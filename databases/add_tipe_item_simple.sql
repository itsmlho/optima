-- Script sederhana untuk menambahkan kolom tipe_item tanpa constraint/trigger
-- Konsistensi data akan dijaga melalui aplikasi

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

-- 3. Tampilkan struktur tabel yang sudah diupdate
DESCRIBE `inventory_attachment`;

-- 4. Verifikasi data
SELECT 
    tipe_item,
    COUNT(*) as jumlah,
    COUNT(CASE WHEN attachment_id IS NOT NULL THEN 1 END) as ada_attachment,
    COUNT(CASE WHEN baterai_id IS NOT NULL THEN 1 END) as ada_battery,
    COUNT(CASE WHEN charger_id IS NOT NULL THEN 1 END) as ada_charger
FROM inventory_attachment 
GROUP BY tipe_item;

-- 5. Contoh query untuk mengambil data berdasarkan tipe
-- Attachment saja
SELECT 
    ia.id_inventory_attachment,
    ia.tipe_item,
    ia.sn_attachment,
    a.tipe, 
    a.merk, 
    a.model,
    ia.kondisi_fisik,
    ia.status_unit
FROM inventory_attachment ia
LEFT JOIN attachment a ON ia.attachment_id = a.id_attachment
WHERE ia.tipe_item = 'attachment';

-- Battery saja  
SELECT 
    ia.id_inventory_attachment,
    ia.tipe_item,
    ia.sn_baterai,
    b.merk_baterai, 
    b.tipe_baterai, 
    b.jenis_baterai,
    ia.kondisi_fisik,
    ia.status_unit
FROM inventory_attachment ia
LEFT JOIN baterai b ON ia.baterai_id = b.id
WHERE ia.tipe_item = 'battery';

-- Charger saja
SELECT 
    ia.id_inventory_attachment,
    ia.tipe_item,
    ia.sn_charger,
    c.merk_charger, 
    c.tipe_charger,
    ia.kondisi_fisik,
    ia.status_unit
FROM inventory_attachment ia
LEFT JOIN charger c ON ia.charger_id = c.id_charger
WHERE ia.tipe_item = 'charger';
