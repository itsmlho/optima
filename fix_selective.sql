-- Script perbaikan database - tahap 3: Selective Fixes
USE optima_db;

-- 1. Perbaiki tipe data kolom spesifikasi di tabel spk dari longtext ke json
ALTER TABLE `spk` MODIFY `spesifikasi` json DEFAULT NULL;

-- 2. Perbaiki tipe data kolom aksesoris di tabel kontrak_spesifikasi dari longtext ke json  
ALTER TABLE `kontrak_spesifikasi` MODIFY `aksesoris` json DEFAULT NULL COMMENT 'Array aksesoris yang dibutuhkan';

-- 3. Cek dan tambahkan foreign key constraint untuk spk ke kontrak_spesifikasi jika belum ada
SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.table_constraints 
    WHERE constraint_schema = 'optima_db' 
    AND table_name = 'spk' 
    AND constraint_name = 'spk_ibfk_1');

SET @sql = IF(@constraint_exists = 0, 
    'ALTER TABLE `spk` ADD CONSTRAINT `spk_ibfk_1` FOREIGN KEY (`kontrak_spesifikasi_id`) REFERENCES `kontrak_spesifikasi` (`id`) ON DELETE SET NULL',
    'SELECT "Foreign key already exists" as result');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 4. Cek dan tambahkan unique key untuk kontrak_spesifikasi jika belum ada
SET @key_exists = (SELECT COUNT(*) FROM information_schema.statistics 
    WHERE table_schema = 'optima_db' 
    AND table_name = 'kontrak_spesifikasi' 
    AND index_name = 'unique_kontrak_spek');

SET @sql = IF(@key_exists = 0,
    'ALTER TABLE `kontrak_spesifikasi` ADD UNIQUE KEY `unique_kontrak_spek` (`kontrak_id`,`spek_kode`)',
    'SELECT "Unique key already exists" as result');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 5. Perbaiki AUTO_INCREMENT values untuk semua tabel yang membutuhkan
-- Reset auto increment untuk spk
SELECT @max_id := IFNULL(MAX(id), 0) + 1 FROM spk;
SET @sql = CONCAT('ALTER TABLE spk AUTO_INCREMENT = ', @max_id);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Reset auto increment untuk kontrak_spesifikasi
SELECT @max_id := IFNULL(MAX(id), 0) + 1 FROM kontrak_spesifikasi;
SET @sql = CONCAT('ALTER TABLE kontrak_spesifikasi AUTO_INCREMENT = ', @max_id);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Reset auto increment untuk delivery_instructions
SELECT @max_id := IFNULL(MAX(id), 0) + 1 FROM delivery_instructions;
SET @sql = CONCAT('ALTER TABLE delivery_instructions AUTO_INCREMENT = ', @max_id);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SELECT 'Selective database fixes completed successfully' as result;
