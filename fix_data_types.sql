-- Script perbaikan database - tahap 2: Data Types dan Missing Columns
USE optima_db;

-- 1. Perbaiki tipe data kolom spesifikasi di tabel spk dari longtext ke json
ALTER TABLE `spk` MODIFY `spesifikasi` json DEFAULT NULL;

-- 2. Perbaiki tipe data kolom aksesoris di tabel kontrak_spesifikasi dari longtext ke json
ALTER TABLE `kontrak_spesifikasi` MODIFY `aksesoris` json DEFAULT NULL COMMENT 'Array aksesoris yang dibutuhkan';

-- 3. Tambahkan unique key untuk spk jika belum ada
ALTER TABLE `spk` ADD UNIQUE KEY `uk_nomor_spk` (`nomor_spk`);

-- 4. Tambahkan indexes untuk spk jika belum ada
ALTER TABLE `spk` ADD KEY `idx_spk_status` (`status`);
ALTER TABLE `spk` ADD KEY `idx_spk_po_kontrak_nomor` (`po_kontrak_nomor`);
ALTER TABLE `spk` ADD KEY `idx_spk_jenis` (`jenis_spk`);
ALTER TABLE `spk` ADD KEY `idx_spk_kontrak_id` (`kontrak_id`);
ALTER TABLE `spk` ADD KEY `fk_spk_kontrak_spesifikasi` (`kontrak_spesifikasi_id`);

-- 5. Tambahkan foreign key constraint untuk spk ke kontrak_spesifikasi
ALTER TABLE `spk` ADD CONSTRAINT `spk_ibfk_1` FOREIGN KEY (`kontrak_spesifikasi_id`) REFERENCES `kontrak_spesifikasi` (`id`) ON DELETE SET NULL;

-- 6. Tambahkan unique key untuk kontrak_spesifikasi
ALTER TABLE `kontrak_spesifikasi` ADD UNIQUE KEY `unique_kontrak_spek` (`kontrak_id`,`spek_kode`);

-- 7. Perbaiki delivery_instructions - tambahkan unique key untuk nomor_di
ALTER TABLE `delivery_instructions` ADD UNIQUE KEY `uk_nomor_di` (`nomor_di`);

-- 8. Tambahkan indexes untuk delivery_instructions
ALTER TABLE `delivery_instructions` ADD KEY `idx_di_status` (`status`);
ALTER TABLE `delivery_instructions` ADD KEY `idx_di_tanggal_kirim` (`tanggal_kirim`);
ALTER TABLE `delivery_instructions` ADD KEY `idx_di_po_kontrak_nomor` (`po_kontrak_nomor`);
ALTER TABLE `delivery_instructions` ADD KEY `idx_di_spk_id` (`spk_id`);

SELECT 'Data types and indexes fixed successfully' as result;
