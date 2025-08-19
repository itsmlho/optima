-- ========================================
-- Script untuk Mengecek dan Menambahkan Field Approval Workflow
-- pada Tabel delivery_instructions (Safe Version)
-- ========================================

USE optima_db;

-- Cek struktur tabel saat ini
SELECT 'Struktur tabel delivery_instructions saat ini:' as info;
DESCRIBE delivery_instructions;

-- Cek field-field approval workflow yang ada
SELECT 'Field approval workflow yang sudah ada:' as info;
SELECT COLUMN_NAME, DATA_TYPE 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'optima_db' 
  AND TABLE_NAME = 'delivery_instructions' 
  AND COLUMN_NAME LIKE '%perencanaan%' 
   OR COLUMN_NAME LIKE '%berangkat%' 
   OR COLUMN_NAME LIKE '%sampai%'
ORDER BY COLUMN_NAME;

-- ========================================
-- Tambahkan field secara conditional (hanya jika belum ada)
-- ========================================

-- Perencanaan fields
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA='optima_db' AND TABLE_NAME='delivery_instructions' 
       AND COLUMN_NAME='perencanaan_operator') = 0,
    'ALTER TABLE delivery_instructions ADD COLUMN perencanaan_operator VARCHAR(100) NULL COMMENT "Nama operator yang melakukan approval perencanaan"',
    'SELECT "Field perencanaan_operator sudah ada" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA='optima_db' AND TABLE_NAME='delivery_instructions' 
       AND COLUMN_NAME='perencanaan_tanggal_approve') = 0,
    'ALTER TABLE delivery_instructions ADD COLUMN perencanaan_tanggal_approve DATE NULL COMMENT "Tanggal approval perencanaan pengiriman"',
    'SELECT "Field perencanaan_tanggal_approve sudah ada" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA='optima_db' AND TABLE_NAME='delivery_instructions' 
       AND COLUMN_NAME='estimasi_sampai') = 0,
    'ALTER TABLE delivery_instructions ADD COLUMN estimasi_sampai DATE NULL COMMENT "Estimasi tanggal sampai dari perencanaan"',
    'SELECT "Field estimasi_sampai sudah ada" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA='optima_db' AND TABLE_NAME='delivery_instructions' 
       AND COLUMN_NAME='nama_supir') = 0,
    'ALTER TABLE delivery_instructions ADD COLUMN nama_supir VARCHAR(100) NULL COMMENT "Nama supir yang bertugas"',
    'SELECT "Field nama_supir sudah ada" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA='optima_db' AND TABLE_NAME='delivery_instructions' 
       AND COLUMN_NAME='no_hp_supir') = 0,
    'ALTER TABLE delivery_instructions ADD COLUMN no_hp_supir VARCHAR(20) NULL COMMENT "Nomor HP supir"',
    'SELECT "Field no_hp_supir sudah ada" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA='optima_db' AND TABLE_NAME='delivery_instructions' 
       AND COLUMN_NAME='no_sim_supir') = 0,
    'ALTER TABLE delivery_instructions ADD COLUMN no_sim_supir VARCHAR(50) NULL COMMENT "Nomor SIM supir"',
    'SELECT "Field no_sim_supir sudah ada" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA='optima_db' AND TABLE_NAME='delivery_instructions' 
       AND COLUMN_NAME='kendaraan') = 0,
    'ALTER TABLE delivery_instructions ADD COLUMN kendaraan VARCHAR(100) NULL COMMENT "Jenis/merk kendaraan yang digunakan"',
    'SELECT "Field kendaraan sudah ada" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA='optima_db' AND TABLE_NAME='delivery_instructions' 
       AND COLUMN_NAME='no_polisi_kendaraan') = 0,
    'ALTER TABLE delivery_instructions ADD COLUMN no_polisi_kendaraan VARCHAR(20) NULL COMMENT "Nomor polisi kendaraan"',
    'SELECT "Field no_polisi_kendaraan sudah ada" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Berangkat fields
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA='optima_db' AND TABLE_NAME='delivery_instructions' 
       AND COLUMN_NAME='berangkat_operator') = 0,
    'ALTER TABLE delivery_instructions ADD COLUMN berangkat_operator VARCHAR(100) NULL COMMENT "Nama operator yang melakukan approval berangkat"',
    'SELECT "Field berangkat_operator sudah ada" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA='optima_db' AND TABLE_NAME='delivery_instructions' 
       AND COLUMN_NAME='berangkat_tanggal_approve') = 0,
    'ALTER TABLE delivery_instructions ADD COLUMN berangkat_tanggal_approve DATE NULL COMMENT "Tanggal approval berangkat"',
    'SELECT "Field berangkat_tanggal_approve sudah ada" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA='optima_db' AND TABLE_NAME='delivery_instructions' 
       AND COLUMN_NAME='catatan_berangkat') = 0,
    'ALTER TABLE delivery_instructions ADD COLUMN catatan_berangkat TEXT NULL COMMENT "Catatan keberangkatan dan kondisi barang"',
    'SELECT "Field catatan_berangkat sudah ada" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Sampai fields
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA='optima_db' AND TABLE_NAME='delivery_instructions' 
       AND COLUMN_NAME='sampai_operator') = 0,
    'ALTER TABLE delivery_instructions ADD COLUMN sampai_operator VARCHAR(100) NULL COMMENT "Nama operator yang melakukan approval sampai"',
    'SELECT "Field sampai_operator sudah ada" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA='optima_db' AND TABLE_NAME='delivery_instructions' 
       AND COLUMN_NAME='sampai_tanggal_approve') = 0,
    'ALTER TABLE delivery_instructions ADD COLUMN sampai_tanggal_approve DATE NULL COMMENT "Tanggal approval sampai"',
    'SELECT "Field sampai_tanggal_approve sudah ada" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA='optima_db' AND TABLE_NAME='delivery_instructions' 
       AND COLUMN_NAME='catatan_sampai') = 0,
    'ALTER TABLE delivery_instructions ADD COLUMN catatan_sampai TEXT NULL COMMENT "Catatan kedatangan dan konfirmasi penerima"',
    'SELECT "Field catatan_sampai sudah ada" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ========================================
-- Verifikasi hasil akhir
-- ========================================
SELECT 'Struktur tabel setelah penambahan field:' as info;
DESCRIBE delivery_instructions;

SELECT 'Field approval workflow yang sekarang ada:' as info;
SELECT COLUMN_NAME, DATA_TYPE 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'optima_db' 
  AND TABLE_NAME = 'delivery_instructions' 
  AND (COLUMN_NAME LIKE '%perencanaan%' 
       OR COLUMN_NAME LIKE '%berangkat%' 
       OR COLUMN_NAME LIKE '%sampai%')
ORDER BY COLUMN_NAME;

-- Tambahkan index untuk performa (jika belum ada)
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA='optima_db' AND TABLE_NAME='delivery_instructions' 
       AND INDEX_NAME='idx_di_perencanaan_approve') = 0,
    'ALTER TABLE delivery_instructions ADD INDEX idx_di_perencanaan_approve (perencanaan_tanggal_approve)',
    'SELECT "Index idx_di_perencanaan_approve sudah ada" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA='optima_db' AND TABLE_NAME='delivery_instructions' 
       AND INDEX_NAME='idx_di_berangkat_approve') = 0,
    'ALTER TABLE delivery_instructions ADD INDEX idx_di_berangkat_approve (berangkat_tanggal_approve)',
    'SELECT "Index idx_di_berangkat_approve sudah ada" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA='optima_db' AND TABLE_NAME='delivery_instructions' 
       AND INDEX_NAME='idx_di_sampai_approve') = 0,
    'ALTER TABLE delivery_instructions ADD INDEX idx_di_sampai_approve (sampai_tanggal_approve)',
    'SELECT "Index idx_di_sampai_approve sudah ada" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SELECT 'Script selesai dijalankan. Silakan test approval workflow.' as status;
