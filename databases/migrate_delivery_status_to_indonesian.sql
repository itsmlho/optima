-- ========================================
-- SQL Script untuk Migrasi Status Delivery Instructions
-- dari Bahasa Inggris ke Bahasa Indonesia
-- ========================================
-- 
-- Script ini akan safely migrate status yang sudah ada
-- dari bahasa Inggris ke bahasa Indonesia
-- ========================================

USE optima_db;

-- STEP 1: Backup data existing (optional, untuk safety)
-- CREATE TABLE delivery_instructions_backup AS SELECT * FROM delivery_instructions;

-- STEP 2: Tambahkan kolom temporary untuk status baru
ALTER TABLE delivery_instructions 
ADD COLUMN `status_indo` ENUM('DIAJUKAN','DIPROSES','DIKIRIM','SAMPAI','DIBATALKAN') 
COLLATE utf8mb4_general_ci DEFAULT NULL;

-- STEP 3: Migrate data dari status lama ke status baru
UPDATE delivery_instructions SET status_indo = 'DIAJUKAN' WHERE status = 'SUBMITTED';
UPDATE delivery_instructions SET status_indo = 'DIKIRIM' WHERE status = 'DISPATCHED';
UPDATE delivery_instructions SET status_indo = 'SAMPAI' WHERE status = 'ARRIVED';
UPDATE delivery_instructions SET status_indo = 'DIBATALKAN' WHERE status = 'CANCELLED';

-- Jika ada status yang tidak standar, set ke DIAJUKAN sebagai default
UPDATE delivery_instructions SET status_indo = 'DIAJUKAN' WHERE status_indo IS NULL;

-- STEP 4: Verifikasi migrasi data
SELECT 
    status as status_lama, 
    status_indo as status_baru, 
    COUNT(*) as jumlah
FROM delivery_instructions 
GROUP BY status, status_indo
ORDER BY status;

-- STEP 5: Drop kolom status lama
ALTER TABLE delivery_instructions DROP COLUMN status;

-- STEP 6: Rename kolom status_indo menjadi status
ALTER TABLE delivery_instructions CHANGE COLUMN status_indo status 
ENUM('DIAJUKAN','DIPROSES','DIKIRIM','SAMPAI','DIBATALKAN') 
COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'DIAJUKAN';

-- STEP 7: Tambahkan field-field workflow approval
ALTER TABLE delivery_instructions 
ADD COLUMN `perencanaan_operator` VARCHAR(100) NULL COMMENT 'Nama operator yang melakukan approval perencanaan',
ADD COLUMN `perencanaan_tanggal_approve` DATE NULL COMMENT 'Tanggal approval perencanaan pengiriman',
ADD COLUMN `estimasi_sampai` DATE NULL COMMENT 'Estimasi tanggal sampai dari perencanaan',
ADD COLUMN `nama_supir` VARCHAR(100) NULL COMMENT 'Nama supir yang bertugas',
ADD COLUMN `no_hp_supir` VARCHAR(20) NULL COMMENT 'Nomor HP supir',
ADD COLUMN `no_sim_supir` VARCHAR(50) NULL COMMENT 'Nomor SIM supir',
ADD COLUMN `kendaraan` VARCHAR(100) NULL COMMENT 'Jenis/merk kendaraan yang digunakan',
ADD COLUMN `no_polisi_kendaraan` VARCHAR(20) NULL COMMENT 'Nomor polisi kendaraan';

-- STEP 8: Tambahkan field-field untuk Berangkat stage  
ALTER TABLE delivery_instructions 
ADD COLUMN `berangkat_operator` VARCHAR(100) NULL COMMENT 'Nama operator yang melakukan approval berangkat',
ADD COLUMN `berangkat_tanggal_approve` DATE NULL COMMENT 'Tanggal approval berangkat',
ADD COLUMN `catatan_berangkat` TEXT NULL COMMENT 'Catatan keberangkatan dan kondisi barang';

-- STEP 9: Tambahkan field-field untuk Sampai stage
ALTER TABLE delivery_instructions 
ADD COLUMN `sampai_operator` VARCHAR(100) NULL COMMENT 'Nama operator yang melakukan approval sampai',
ADD COLUMN `sampai_tanggal_approve` DATE NULL COMMENT 'Tanggal approval sampai',
ADD COLUMN `catatan_sampai` TEXT NULL COMMENT 'Catatan kedatangan dan konfirmasi penerima';

-- STEP 10: Tambahkan index untuk performa
ALTER TABLE delivery_instructions 
ADD INDEX `idx_di_perencanaan_approve` (`perencanaan_tanggal_approve`),
ADD INDEX `idx_di_berangkat_approve` (`berangkat_tanggal_approve`),
ADD INDEX `idx_di_sampai_approve` (`sampai_tanggal_approve`);

-- ========================================
-- Verifikasi hasil migrasi
-- ========================================
SELECT 'Verifikasi Status setelah migrasi:' as info;
SELECT status, COUNT(*) as jumlah FROM delivery_instructions GROUP BY status;

SELECT 'Struktur tabel setelah migrasi:' as info;
DESCRIBE delivery_instructions;

-- ========================================
-- Contoh Query untuk Testing setelah migrasi
-- ========================================
-- 
-- 1. Cari DI dengan status DIAJUKAN:
-- SELECT * FROM delivery_instructions WHERE status = 'DIAJUKAN';
-- 
-- 2. Update status ke DIPROSES:
-- UPDATE delivery_instructions SET status = 'DIPROSES' WHERE id = 5;
-- 
-- 3. Approval Perencanaan Pengiriman:
-- UPDATE delivery_instructions SET 
--   perencanaan_operator = 'Ahmad Supardi',
--   perencanaan_tanggal_approve = CURDATE(),
--   tanggal_kirim = '2025-12-20',
--   estimasi_sampai = '2025-12-21',
--   nama_supir = 'Budi Santoso',
--   no_hp_supir = '081234567890',
--   no_sim_supir = 'A123456789',
--   kendaraan = 'Truck Fuso Fighter',
--   no_polisi_kendaraan = 'B 1234 XYZ'
-- WHERE id = 5;

-- ========================================
-- END OF MIGRATION SCRIPT
-- ========================================
