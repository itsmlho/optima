-- Script untuk memperbaiki struktur database dari migrasi HeidiSQL ke phpMyAdmin
-- Tanggal: 2025-09-01

USE optima_db;

-- 1. Perbaiki tabel attachment - tambahkan PRIMARY KEY dan AUTO_INCREMENT
ALTER TABLE `attachment` 
ADD PRIMARY KEY (`id_attachment`),
MODIFY `id_attachment` int NOT NULL AUTO_INCREMENT;

-- 2. Perbaiki tabel baterai - tambahkan PRIMARY KEY dan AUTO_INCREMENT  
ALTER TABLE `baterai` 
ADD PRIMARY KEY (`id`),
MODIFY `id` int NOT NULL AUTO_INCREMENT;

-- 3. Perbaiki tabel charger - tambahkan PRIMARY KEY dan AUTO_INCREMENT
ALTER TABLE `charger`
ADD PRIMARY KEY (`id_charger`),
MODIFY `id_charger` int NOT NULL AUTO_INCREMENT;

-- 4. Perbaiki tabel spk - ubah kolom spesifikasi dari longtext ke json
ALTER TABLE `spk` 
MODIFY `spesifikasi` json DEFAULT NULL;

-- 5. Pastikan tabel spk memiliki semua index yang diperlukan
ALTER TABLE `spk` 
ADD UNIQUE KEY `uk_nomor_spk` (`nomor_spk`),
ADD KEY `idx_spk_status` (`status`),
ADD KEY `idx_spk_po_kontrak_nomor` (`po_kontrak_nomor`),
ADD KEY `idx_spk_jenis` (`jenis_spk`),
ADD KEY `idx_spk_kontrak_id` (`kontrak_id`),
ADD KEY `fk_spk_kontrak_spesifikasi` (`kontrak_spesifikasi_id`);

-- 6. Tambahkan foreign key constraint untuk spk
ALTER TABLE `spk`
ADD CONSTRAINT `spk_ibfk_1` FOREIGN KEY (`kontrak_spesifikasi_id`) REFERENCES `kontrak_spesifikasi` (`id`) ON DELETE SET NULL;

-- 7. Perbaiki tabel kontrak_spesifikasi - pastikan memiliki aksesoris kolom json
ALTER TABLE `kontrak_spesifikasi`
MODIFY `aksesoris` json DEFAULT NULL COMMENT 'Array aksesoris yang dibutuhkan';

-- 8. Tambahkan kolom yang mungkin hilang pada tabel attachment
ALTER TABLE `attachment`
ADD COLUMN IF NOT EXISTS `tipe` varchar(100) NOT NULL AFTER `id_attachment`,
ADD COLUMN IF NOT EXISTS `merk` varchar(100) NOT NULL AFTER `tipe`,
ADD COLUMN IF NOT EXISTS `model` varchar(100) NOT NULL AFTER `merk`;

-- 9. Tambahkan kolom yang mungkin hilang pada tabel baterai
ALTER TABLE `baterai`
ADD COLUMN IF NOT EXISTS `merk_baterai` varchar(100) NOT NULL AFTER `id`,
ADD COLUMN IF NOT EXISTS `tipe_baterai` varchar(100) NOT NULL AFTER `merk_baterai`,
ADD COLUMN IF NOT EXISTS `jenis_baterai` varchar(100) NOT NULL AFTER `tipe_baterai`;

-- 10. Tambahkan kolom yang mungkin hilang pada tabel charger
ALTER TABLE `charger`
ADD COLUMN IF NOT EXISTS `merk_charger` varchar(100) NOT NULL AFTER `id_charger`,
ADD COLUMN IF NOT EXISTS `tipe_charger` varchar(100) NOT NULL AFTER `merk_charger`;

-- 11. Pastikan tabel delivery_instructions memiliki semua kolom yang diperlukan
ALTER TABLE `delivery_instructions`
ADD COLUMN IF NOT EXISTS `nomor_di` varchar(100) NOT NULL AFTER `id`,
ADD COLUMN IF NOT EXISTS `spk_id` int unsigned DEFAULT NULL AFTER `nomor_di`,
ADD COLUMN IF NOT EXISTS `po_kontrak_nomor` varchar(100) DEFAULT NULL AFTER `spk_id`,
ADD COLUMN IF NOT EXISTS `pelanggan` varchar(255) NOT NULL AFTER `po_kontrak_nomor`,
ADD COLUMN IF NOT EXISTS `lokasi` varchar(255) DEFAULT NULL AFTER `pelanggan`,
ADD COLUMN IF NOT EXISTS `tanggal_kirim` date DEFAULT NULL AFTER `lokasi`,
ADD COLUMN IF NOT EXISTS `catatan` text AFTER `tanggal_kirim`,
ADD COLUMN IF NOT EXISTS `status` enum('SUBMITTED','PROCESSED','SHIPPED','DELIVERED','CANCELLED') NOT NULL DEFAULT 'SUBMITTED' AFTER `catatan`,
ADD COLUMN IF NOT EXISTS `dibuat_oleh` int unsigned DEFAULT NULL AFTER `status`,
ADD COLUMN IF NOT EXISTS `dibuat_pada` datetime DEFAULT CURRENT_TIMESTAMP AFTER `dibuat_oleh`,
ADD COLUMN IF NOT EXISTS `diperbarui_pada` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `dibuat_pada`;

-- 12. Tambahkan semua kolom delivery workflow yang diperlukan pada delivery_instructions
ALTER TABLE `delivery_instructions`
ADD COLUMN IF NOT EXISTS `perencanaan_tanggal_approve` date DEFAULT NULL COMMENT 'Tanggal approval perencanaan pengiriman' AFTER `diperbarui_pada`,
ADD COLUMN IF NOT EXISTS `estimasi_sampai` date DEFAULT NULL COMMENT 'Estimasi tanggal sampai dari perencanaan' AFTER `perencanaan_tanggal_approve`,
ADD COLUMN IF NOT EXISTS `nama_supir` varchar(100) DEFAULT NULL COMMENT 'Nama supir yang bertugas' AFTER `estimasi_sampai`,
ADD COLUMN IF NOT EXISTS `no_hp_supir` varchar(20) DEFAULT NULL COMMENT 'Nomor HP supir' AFTER `nama_supir`,
ADD COLUMN IF NOT EXISTS `no_sim_supir` varchar(50) DEFAULT NULL COMMENT 'Nomor SIM supir' AFTER `no_hp_supir`,
ADD COLUMN IF NOT EXISTS `kendaraan` varchar(100) DEFAULT NULL COMMENT 'Jenis/merk kendaraan yang digunakan' AFTER `no_sim_supir`,
ADD COLUMN IF NOT EXISTS `no_polisi_kendaraan` varchar(20) DEFAULT NULL COMMENT 'Nomor polisi kendaraan' AFTER `kendaraan`,
ADD COLUMN IF NOT EXISTS `berangkat_tanggal_approve` date DEFAULT NULL COMMENT 'Tanggal approval berangkat' AFTER `no_polisi_kendaraan`,
ADD COLUMN IF NOT EXISTS `catatan_berangkat` text COMMENT 'Catatan keberangkatan dan kondisi barang' AFTER `berangkat_tanggal_approve`,
ADD COLUMN IF NOT EXISTS `sampai_tanggal_approve` date DEFAULT NULL COMMENT 'Tanggal approval sampai' AFTER `catatan_berangkat`,
ADD COLUMN IF NOT EXISTS `catatan_sampai` text COMMENT 'Catatan kedatangan dan konfirmasi penerima' AFTER `sampai_tanggal_approve`;

-- 13. Tambahkan indexes untuk delivery_instructions
ALTER TABLE `delivery_instructions`
ADD UNIQUE KEY IF NOT EXISTS `uk_nomor_di` (`nomor_di`),
ADD KEY IF NOT EXISTS `idx_di_status` (`status`),
ADD KEY IF NOT EXISTS `idx_di_tanggal_kirim` (`tanggal_kirim`),
ADD KEY IF NOT EXISTS `idx_di_po_kontrak_nomor` (`po_kontrak_nomor`),
ADD KEY IF NOT EXISTS `idx_di_spk_id` (`spk_id`),
ADD KEY IF NOT EXISTS `idx_di_perencanaan_approve` (`perencanaan_tanggal_approve`),
ADD KEY IF NOT EXISTS `idx_di_berangkat_approve` (`berangkat_tanggal_approve`),
ADD KEY IF NOT EXISTS `idx_di_sampai_approve` (`sampai_tanggal_approve`);

-- 14. Perbaiki tabel delivery_items
ALTER TABLE `delivery_items`
ADD COLUMN IF NOT EXISTS `di_id` int unsigned NOT NULL AFTER `id`,
ADD COLUMN IF NOT EXISTS `item_type` enum('UNIT','ATTACHMENT') NOT NULL DEFAULT 'UNIT' AFTER `di_id`,
ADD COLUMN IF NOT EXISTS `unit_id` int unsigned DEFAULT NULL AFTER `item_type`,
ADD COLUMN IF NOT EXISTS `attachment_id` int unsigned DEFAULT NULL AFTER `unit_id`,
ADD COLUMN IF NOT EXISTS `keterangan` varchar(255) DEFAULT NULL AFTER `attachment_id`;

-- 15. Tambahkan indexes untuk delivery_items
ALTER TABLE `delivery_items`
ADD KEY IF NOT EXISTS `idx_di_items_di` (`di_id`),
ADD KEY IF NOT EXISTS `idx_di_items_type` (`item_type`);

-- 16. Perbaiki tabel departemen
ALTER TABLE `departemen`
ADD COLUMN IF NOT EXISTS `nama_departemen` varchar(100) NOT NULL AFTER `id_departemen`;

-- 17. Pastikan divisions table memiliki semua kolom
ALTER TABLE `divisions`
ADD COLUMN IF NOT EXISTS `name` varchar(100) NOT NULL AFTER `id`,
ADD COLUMN IF NOT EXISTS `code` varchar(20) NOT NULL AFTER `name`,
ADD COLUMN IF NOT EXISTS `description` text AFTER `code`,
ADD COLUMN IF NOT EXISTS `is_active` tinyint(1) DEFAULT '1' AFTER `description`,
ADD COLUMN IF NOT EXISTS `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP AFTER `is_active`,
ADD COLUMN IF NOT EXISTS `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

-- 18. Tambahkan indexes untuk divisions
ALTER TABLE `divisions`
ADD UNIQUE KEY IF NOT EXISTS `code` (`code`),
ADD KEY IF NOT EXISTS `idx_divisions_code` (`code`),
ADD KEY IF NOT EXISTS `idx_divisions_active` (`is_active`);

-- 19. Reset auto increment values untuk tabel yang membutuhkan
SELECT @max_id := IFNULL(MAX(id_attachment), 0) + 1 FROM attachment;
SET @sql = CONCAT('ALTER TABLE attachment AUTO_INCREMENT = ', @max_id);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SELECT @max_id := IFNULL(MAX(id), 0) + 1 FROM baterai;
SET @sql = CONCAT('ALTER TABLE baterai AUTO_INCREMENT = ', @max_id);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SELECT @max_id := IFNULL(MAX(id_charger), 0) + 1 FROM charger;
SET @sql = CONCAT('ALTER TABLE charger AUTO_INCREMENT = ', @max_id);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 20. Perbaiki constraint dan foreign keys yang mungkin hilang
-- Hapus foreign key yang ada terlebih dahulu jika ada
SET FOREIGN_KEY_CHECKS = 0;

-- Tambahkan kembali foreign key dengan benar
ALTER TABLE `delivery_instructions`
ADD CONSTRAINT IF NOT EXISTS `fk_di_spk` FOREIGN KEY (`spk_id`) REFERENCES `spk` (`id`) ON DELETE SET NULL;

ALTER TABLE `delivery_items`
ADD CONSTRAINT IF NOT EXISTS `fk_di_items_di` FOREIGN KEY (`di_id`) REFERENCES `delivery_instructions` (`id`) ON DELETE CASCADE;

SET FOREIGN_KEY_CHECKS = 1;

-- Tampilkan hasil
SELECT 'Database structure fix completed successfully' as result;
