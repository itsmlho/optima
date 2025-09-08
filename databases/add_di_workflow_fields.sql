-- ===================================================================
-- SCRIPT PERBAIKAN DELIVERY INSTRUCTIONS - MENAMBAHKAN FIELD WORKFLOW
-- ===================================================================
-- Tanggal: 3 September 2025
-- Tujuan: Menambahkan field jenis_perintah_kerja_id, tujuan_perintah_kerja_id,
--         dan status_eksekusi_workflow_id untuk mendukung workflow DI yang lengkap
-- ===================================================================

USE optima_db;

-- Backup original table structure
CREATE TABLE IF NOT EXISTS migration_log_di_workflow (
    id INT AUTO_INCREMENT PRIMARY KEY,
    table_name VARCHAR(100),
    action TEXT,
    status ENUM('SUCCESS', 'ERROR', 'SKIPPED'),
    error_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 1. Add jenis_perintah_kerja_id field
SET @column_exists = 0;
SELECT COUNT(*) INTO @column_exists 
FROM information_schema.columns 
WHERE table_schema = 'optima_db' 
AND table_name = 'delivery_instructions' 
AND column_name = 'jenis_perintah_kerja_id';

SET @sql = IF(@column_exists = 0,
    'ALTER TABLE delivery_instructions ADD COLUMN jenis_perintah_kerja_id INT NULL AFTER status',
    'SELECT "Column jenis_perintah_kerja_id already exists" as message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

INSERT INTO migration_log_di_workflow (table_name, action, status) 
VALUES ('delivery_instructions', 'Add jenis_perintah_kerja_id', IF(@column_exists = 0, 'SUCCESS', 'SKIPPED'));

-- 2. Add tujuan_perintah_kerja_id field
SET @column_exists = 0;
SELECT COUNT(*) INTO @column_exists 
FROM information_schema.columns 
WHERE table_schema = 'optima_db' 
AND table_name = 'delivery_instructions' 
AND column_name = 'tujuan_perintah_kerja_id';

SET @sql = IF(@column_exists = 0,
    'ALTER TABLE delivery_instructions ADD COLUMN tujuan_perintah_kerja_id INT NULL AFTER jenis_perintah_kerja_id',
    'SELECT "Column tujuan_perintah_kerja_id already exists" as message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

INSERT INTO migration_log_di_workflow (table_name, action, status) 
VALUES ('delivery_instructions', 'Add tujuan_perintah_kerja_id', IF(@column_exists = 0, 'SUCCESS', 'SKIPPED'));

-- 3. Add status_eksekusi_workflow_id field
SET @column_exists = 0;
SELECT COUNT(*) INTO @column_exists 
FROM information_schema.columns 
WHERE table_schema = 'optima_db' 
AND table_name = 'delivery_instructions' 
AND column_name = 'status_eksekusi_workflow_id';

SET @sql = IF(@column_exists = 0,
    'ALTER TABLE delivery_instructions ADD COLUMN status_eksekusi_workflow_id INT NULL DEFAULT 1 AFTER tujuan_perintah_kerja_id',
    'SELECT "Column status_eksekusi_workflow_id already exists" as message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

INSERT INTO migration_log_di_workflow (table_name, action, status) 
VALUES ('delivery_instructions', 'Add status_eksekusi_workflow_id', IF(@column_exists = 0, 'SUCCESS', 'SKIPPED'));

-- 4. Add foreign key constraints (only if columns were added)
SET @fk_exists = 0;
SELECT COUNT(*) INTO @fk_exists 
FROM information_schema.KEY_COLUMN_USAGE 
WHERE CONSTRAINT_SCHEMA = 'optima_db' 
AND TABLE_NAME = 'delivery_instructions' 
AND CONSTRAINT_NAME = 'fk_di_jenis_perintah_kerja';

SET @sql = IF(@fk_exists = 0,
    'ALTER TABLE delivery_instructions ADD CONSTRAINT fk_di_jenis_perintah_kerja FOREIGN KEY (jenis_perintah_kerja_id) REFERENCES jenis_perintah_kerja(id)',
    'SELECT "FK fk_di_jenis_perintah_kerja already exists" as message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

INSERT INTO migration_log_di_workflow (table_name, action, status) 
VALUES ('delivery_instructions', 'Add FK jenis_perintah_kerja', IF(@fk_exists = 0, 'SUCCESS', 'SKIPPED'));

-- 5. Add FK for tujuan_perintah_kerja
SET @fk_exists = 0;
SELECT COUNT(*) INTO @fk_exists 
FROM information_schema.KEY_COLUMN_USAGE 
WHERE CONSTRAINT_SCHEMA = 'optima_db' 
AND TABLE_NAME = 'delivery_instructions' 
AND CONSTRAINT_NAME = 'fk_di_tujuan_perintah_kerja';

SET @sql = IF(@fk_exists = 0,
    'ALTER TABLE delivery_instructions ADD CONSTRAINT fk_di_tujuan_perintah_kerja FOREIGN KEY (tujuan_perintah_kerja_id) REFERENCES tujuan_perintah_kerja(id)',
    'SELECT "FK fk_di_tujuan_perintah_kerja already exists" as message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

INSERT INTO migration_log_di_workflow (table_name, action, status) 
VALUES ('delivery_instructions', 'Add FK tujuan_perintah_kerja', IF(@fk_exists = 0, 'SUCCESS', 'SKIPPED'));

-- 6. Add FK for status_eksekusi_workflow
SET @fk_exists = 0;
SELECT COUNT(*) INTO @fk_exists 
FROM information_schema.KEY_COLUMN_USAGE 
WHERE CONSTRAINT_SCHEMA = 'optima_db' 
AND TABLE_NAME = 'delivery_instructions' 
AND CONSTRAINT_NAME = 'fk_di_status_eksekusi_workflow';

SET @sql = IF(@fk_exists = 0,
    'ALTER TABLE delivery_instructions ADD CONSTRAINT fk_di_status_eksekusi_workflow FOREIGN KEY (status_eksekusi_workflow_id) REFERENCES status_eksekusi_workflow(id)',
    'SELECT "FK fk_di_status_eksekusi_workflow already exists" as message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

INSERT INTO migration_log_di_workflow (table_name, action, status) 
VALUES ('delivery_instructions', 'Add FK status_eksekusi_workflow', IF(@fk_exists = 0, 'SUCCESS', 'SKIPPED'));

-- 7. Update existing DI records with default workflow values if needed
UPDATE delivery_instructions 
SET 
    jenis_perintah_kerja_id = (SELECT id FROM jenis_perintah_kerja WHERE kode = 'ANTAR' LIMIT 1),
    tujuan_perintah_kerja_id = (SELECT id FROM tujuan_perintah_kerja WHERE kode = 'ANTAR_BARU' LIMIT 1),
    status_eksekusi_workflow_id = (SELECT id FROM status_eksekusi_workflow WHERE kode = 'BELUM_MULAI' LIMIT 1)
WHERE jenis_perintah_kerja_id IS NULL 
   OR tujuan_perintah_kerja_id IS NULL 
   OR status_eksekusi_workflow_id IS NULL;

INSERT INTO migration_log_di_workflow (table_name, action, status) 
VALUES ('delivery_instructions', 'Update existing records with default workflow values', 'SUCCESS');

-- Display results
SELECT 
    'DELIVERY INSTRUCTIONS WORKFLOW MIGRATION COMPLETED' as status,
    COUNT(*) as total_actions,
    SUM(CASE WHEN status = 'SUCCESS' THEN 1 ELSE 0 END) as success_count,
    SUM(CASE WHEN status = 'ERROR' THEN 1 ELSE 0 END) as error_count,
    SUM(CASE WHEN status = 'SKIPPED' THEN 1 ELSE 0 END) as skipped_count
FROM migration_log_di_workflow;

-- Show updated table structure
DESCRIBE delivery_instructions;
