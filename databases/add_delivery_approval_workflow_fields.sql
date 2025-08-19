-- ========================================
-- SQL Script untuk Menambahkan Field Approval Workflow 
-- pada Tabel delivery_instructions
-- ========================================
-- 
-- Script ini menambahkan field-field yang dibutuhkan untuk 
-- sistem approval workflow bertingkat pada Delivery Instructions
-- 
-- Workflow: DIAJUKAN → DIPROSES → Perencanaan → Berangkat → Sampai → SAMPAI
-- ========================================

USE optima_db;

-- 1. Update ENUM status untuk menambahkan status DIPROSES (dalam bahasa Indonesia)
ALTER TABLE delivery_instructions 
MODIFY COLUMN `status` ENUM('DIAJUKAN','DIPROSES','DIKIRIM','SAMPAI','DIBATALKAN') 
COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'DIAJUKAN';

-- 2. Tambahkan field-field untuk Perencanaan Pengiriman stage
ALTER TABLE delivery_instructions 
ADD COLUMN `perencanaan_operator` VARCHAR(100) NULL COMMENT 'Nama operator yang melakukan approval perencanaan',
ADD COLUMN `perencanaan_tanggal_approve` DATE NULL COMMENT 'Tanggal approval perencanaan pengiriman',
ADD COLUMN `estimasi_sampai` DATE NULL COMMENT 'Estimasi tanggal sampai dari perencanaan',
ADD COLUMN `nama_supir` VARCHAR(100) NULL COMMENT 'Nama supir yang bertugas',
ADD COLUMN `no_hp_supir` VARCHAR(20) NULL COMMENT 'Nomor HP supir',
ADD COLUMN `no_sim_supir` VARCHAR(50) NULL COMMENT 'Nomor SIM supir',
ADD COLUMN `kendaraan` VARCHAR(100) NULL COMMENT 'Jenis/merk kendaraan yang digunakan',
ADD COLUMN `no_polisi_kendaraan` VARCHAR(20) NULL COMMENT 'Nomor polisi kendaraan';

-- 3. Tambahkan field-field untuk Berangkat stage  
ALTER TABLE delivery_instructions 
ADD COLUMN `berangkat_operator` VARCHAR(100) NULL COMMENT 'Nama operator yang melakukan approval berangkat',
ADD COLUMN `berangkat_tanggal_approve` DATE NULL COMMENT 'Tanggal approval berangkat',
ADD COLUMN `catatan_berangkat` TEXT NULL COMMENT 'Catatan keberangkatan dan kondisi barang';

-- 4. Tambahkan field-field untuk Sampai stage
ALTER TABLE delivery_instructions 
ADD COLUMN `sampai_operator` VARCHAR(100) NULL COMMENT 'Nama operator yang melakukan approval sampai',
ADD COLUMN `sampai_tanggal_approve` DATE NULL COMMENT 'Tanggal approval sampai',
ADD COLUMN `catatan_sampai` TEXT NULL COMMENT 'Catatan kedatangan dan konfirmasi penerima';

-- 5. Tambahkan index untuk performa
ALTER TABLE delivery_instructions 
ADD INDEX `idx_di_perencanaan_approve` (`perencanaan_tanggal_approve`),
ADD INDEX `idx_di_berangkat_approve` (`berangkat_tanggal_approve`),
ADD INDEX `idx_di_sampai_approve` (`sampai_tanggal_approve`);

-- ========================================
-- Verifikasi perubahan struktur tabel
-- ========================================
-- Uncomment baris di bawah ini untuk melihat struktur tabel setelah perubahan:
-- DESCRIBE delivery_instructions;

-- ========================================
-- Contoh Query untuk Testing
-- ========================================
-- 
-- 1. Update status DI ke DIPROSES:
-- UPDATE delivery_instructions SET status = 'DIPROSES' WHERE id = 5;
-- 
-- 2. Approval Perencanaan Pengiriman:
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
-- 
-- 3. Approval Berangkat:
-- UPDATE delivery_instructions SET 
--   berangkat_operator = 'Siti Aminah',
--   berangkat_tanggal_approve = CURDATE(),
--   catatan_berangkat = 'Barang sudah dimuat dengan baik, kondisi unit normal, dokumentasi lengkap. Berangkat pukul 08:30 WIB.'
-- WHERE id = 5;
-- 
-- 4. Approval Sampai:
-- UPDATE delivery_instructions SET 
--   sampai_operator = 'Eko Prasetyo',
--   sampai_tanggal_approve = CURDATE(),
--   catatan_sampai = 'Unit telah sampai di lokasi dengan selamat. Diserahkan kepada Pak Joko (Manager Operasional). Kondisi unit baik, tidak ada kerusakan. Dokumentasi BAST sudah ditandatangani.',
--   status = 'SAMPAI'
-- WHERE id = 5;
-- 
-- 5. Query untuk melihat status workflow:
-- SELECT 
--   nomor_di,
--   status,
--   CASE 
--     WHEN perencanaan_tanggal_approve IS NOT NULL THEN 'Selesai'
--     ELSE 'Menunggu'
--   END as status_perencanaan,
--   CASE 
--     WHEN berangkat_tanggal_approve IS NOT NULL THEN 'Selesai'
--     ELSE 'Menunggu'
--   END as status_berangkat,
--   CASE 
--     WHEN sampai_tanggal_approve IS NOT NULL THEN 'Selesai'
--     ELSE 'Menunggu'
--   END as status_sampai
-- FROM delivery_instructions 
-- WHERE status = 'DIPROSES';

-- ========================================
-- END OF SCRIPT
-- ========================================
