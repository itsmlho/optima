-- Perbaikan Status DI untuk Menghilangkan Duplikasi
-- Menggabungkan status utama dengan status_eksekusi yang lebih informatif

-- 1. Tambah kolom status_temp dengan ENUM baru
ALTER TABLE delivery_instructions 
ADD COLUMN status_temp ENUM(
    'DIAJUKAN',           -- DI sudah dibuat, menunggu persetujuan
    'DISETUJUI',          -- DI disetujui, persiapan dimulai  
    'PERSIAPAN_UNIT',     -- Tim sedang mempersiapkan unit
    'SIAP_KIRIM',         -- Unit sudah siap, menunggu pengiriman
    'DALAM_PERJALANAN',   -- Unit sedang dalam perjalanan
    'SAMPAI_LOKASI',      -- Unit sudah sampai di lokasi tujuan
    'SELESAI',            -- DI selesai dengan sukses
    'DIBATALKAN'          -- DI dibatalkan
) DEFAULT 'DIAJUKAN';

-- 2. Migrate data dari status lama ke status baru
UPDATE delivery_instructions SET status_temp = 'DIAJUKAN' WHERE status = 'SUBMITTED';
UPDATE delivery_instructions SET status_temp = 'DISETUJUI' WHERE status = 'PROCESSED'; 
UPDATE delivery_instructions SET status_temp = 'DALAM_PERJALANAN' WHERE status = 'SHIPPED';
UPDATE delivery_instructions SET status_temp = 'SAMPAI_LOKASI' WHERE status = 'DELIVERED';
UPDATE delivery_instructions SET status_temp = 'DIBATALKAN' WHERE status = 'CANCELLED';

-- 3. Hapus kolom status lama dan rename status_temp ke status
ALTER TABLE delivery_instructions DROP COLUMN status;
ALTER TABLE delivery_instructions CHANGE COLUMN status_temp status ENUM(
    'DIAJUKAN',           
    'DISETUJUI',          
    'PERSIAPAN_UNIT',     
    'SIAP_KIRIM',         
    'DALAM_PERJALANAN',   
    'SAMPAI_LOKASI',      
    'SELESAI',            
    'DIBATALKAN'          
) NOT NULL DEFAULT 'DIAJUKAN' 
COMMENT 'Status lengkap DI dari pengajuan hingga selesai';

-- 3. Hapus kolom status_eksekusi yang redundant (jika ada)
SET @column_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                     WHERE TABLE_SCHEMA = 'optima_db' 
                     AND TABLE_NAME = 'delivery_instructions' 
                     AND COLUMN_NAME = 'status_eksekusi');

SET @sql = IF(@column_exists > 0, 
    'ALTER TABLE delivery_instructions DROP COLUMN status_eksekusi',
    'SELECT "Column status_eksekusi does not exist" as message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 4. Hapus kolom status_eksekusi_workflow_id yang tidak terpakai (jika ada)
SET @column_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                     WHERE TABLE_SCHEMA = 'optima_db' 
                     AND TABLE_NAME = 'delivery_instructions' 
                     AND COLUMN_NAME = 'status_eksekusi_workflow_id');

SET @sql = IF(@column_exists > 0, 
    'ALTER TABLE delivery_instructions DROP COLUMN status_eksekusi_workflow_id',
    'SELECT "Column status_eksekusi_workflow_id does not exist" as message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 5. Log perubahan
INSERT INTO migration_log (table_name, operation, details, executed_at) 
VALUES ('delivery_instructions', 'Optimize DI Status', 'Menggabungkan status dan menghilangkan duplikasi', NOW());

SELECT 'Status DI berhasil dioptimasi - duplikasi dihapus, status lebih informatif' as result;
