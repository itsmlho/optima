-- =====================================================
-- MIGRATION SCRIPT: NEW WORKFLOW IMPLEMENTATION
-- Date: September 3, 2025
-- Version: 1.0
-- Description: Implementasi alur kerja baru dengan 3 komponen:
--              1. Jenis Perintah Kerja
--              2. Tujuan Perintah  
--              3. Status Eksekusi
-- =====================================================

-- Backup existing data before migration
CREATE TABLE IF NOT EXISTS delivery_instructions_backup_20250903 AS 
SELECT * FROM delivery_instructions;

CREATE TABLE IF NOT EXISTS spk_backup_20250903 AS 
SELECT * FROM spk;

-- =====================================================
-- 1. UPDATE DELIVERY_INSTRUCTIONS TABLE
-- =====================================================

-- Add new columns for the new workflow
ALTER TABLE delivery_instructions 
ADD COLUMN jenis_perintah ENUM('ANTAR','TARIK','TUKAR','RELOKASI') NULL COMMENT 'Jenis perintah kerja utama',
ADD COLUMN tujuan_perintah VARCHAR(255) NULL COMMENT 'Alasan/konteks dari perintah kerja',
ADD COLUMN status_eksekusi ENUM('Direncanakan','Persiapan Unit','Siap Kirim','Siap Ambil','Dalam Perjalanan','Selesai','Dibatalkan') NULL COMMENT 'Status progres real-time';

-- Migrate existing data based on current status
-- Default migration logic: assume most existing DI are 'ANTAR' for 'Unit Baru'
UPDATE delivery_instructions SET 
    jenis_perintah = 'ANTAR',
    tujuan_perintah = 'Unit Baru (Kontrak Baru)',
    status_eksekusi = CASE 
        WHEN status = 'SUBMITTED' THEN 'Direncanakan'
        WHEN status = 'PROCESSED' THEN 'Persiapan Unit'
        WHEN status = 'SHIPPED' THEN 'Dalam Perjalanan'
        WHEN status = 'DELIVERED' THEN 'Selesai'
        WHEN status = 'CANCELLED' THEN 'Dibatalkan'
        ELSE 'Direncanakan'
    END
WHERE jenis_perintah IS NULL;

-- Make new columns required after data migration
ALTER TABLE delivery_instructions 
MODIFY COLUMN jenis_perintah ENUM('ANTAR','TARIK','TUKAR','RELOKASI') NOT NULL,
MODIFY COLUMN tujuan_perintah VARCHAR(255) NOT NULL,
MODIFY COLUMN status_eksekusi ENUM('Direncanakan','Persiapan Unit','Siap Kirim','Siap Ambil','Dalam Perjalanan','Selesai','Dibatalkan') NOT NULL DEFAULT 'Direncanakan';

-- Keep old status column for backward compatibility (temporarily)
-- Can be dropped after full migration testing
-- ALTER TABLE delivery_instructions DROP COLUMN status;

-- Add indexes for better performance
CREATE INDEX idx_di_jenis_perintah ON delivery_instructions(jenis_perintah);
CREATE INDEX idx_di_status_eksekusi ON delivery_instructions(status_eksekusi);
CREATE INDEX idx_di_jenis_status ON delivery_instructions(jenis_perintah, status_eksekusi);

-- =====================================================
-- 2. UPDATE SPK TABLE
-- =====================================================

-- Add link to delivery_instructions for auto-generation tracking
ALTER TABLE spk 
ADD COLUMN delivery_instruction_id INT UNSIGNED NULL COMMENT 'FK ke DI yang auto-generate SPK ini',
ADD COLUMN auto_generated BOOLEAN DEFAULT FALSE COMMENT 'Flag apakah SPK ini auto-generated dari DI';

-- Add foreign key constraint
ALTER TABLE spk 
ADD CONSTRAINT fk_spk_delivery_instruction 
FOREIGN KEY (delivery_instruction_id) REFERENCES delivery_instructions(id) 
ON DELETE SET NULL ON UPDATE CASCADE;

-- Update existing SPK that have spk_id in delivery_instructions
UPDATE spk s 
JOIN delivery_instructions di ON di.spk_id = s.id 
SET s.delivery_instruction_id = di.id,
    s.auto_generated = TRUE;

-- Add index for performance
CREATE INDEX idx_spk_delivery_instruction ON spk(delivery_instruction_id);
CREATE INDEX idx_spk_auto_generated ON spk(auto_generated);

-- =====================================================
-- 3. CREATE LOOKUP TABLES FOR DYNAMIC DROPDOWNS
-- =====================================================

-- Table untuk menyimpan mapping jenis perintah ke tujuan perintah
CREATE TABLE IF NOT EXISTS workflow_tujuan_perintah (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    jenis_perintah ENUM('ANTAR','TARIK','TUKAR','RELOKASI') NOT NULL,
    tujuan_kode VARCHAR(50) NOT NULL,
    tujuan_label VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_jenis_tujuan (jenis_perintah, tujuan_kode)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert data untuk dropdown dinamis
INSERT INTO workflow_tujuan_perintah (jenis_perintah, tujuan_kode, tujuan_label) VALUES
-- Untuk ANTAR
('ANTAR', 'unit_baru_kontrak_baru', 'Unit Baru (Kontrak Baru)'),
('ANTAR', 'penambahan_unit_existing', 'Penambahan Unit (Kontrak Existing)'),
('ANTAR', 'unit_trial', 'Unit Trial'),
('ANTAR', 'unit_spare', 'Unit Spare'),
('ANTAR', 'unit_pengganti_sementara', 'Unit Pengganti Sementara'),

-- Untuk TARIK
('TARIK', 'selesai_kontrak', 'Selesai Kontrak'),
('TARIK', 'putus_kontrak', 'Putus Kontrak'),
('TARIK', 'selesai_trial', 'Selesai Trial'),
('TARIK', 'pengambilan_unit_rusak', 'Pengambilan Unit Rusak'),
('TARIK', 'pengambilan_unit_tukar_guling', 'Pengambilan Unit Tukar Guling'),

-- Untuk TUKAR
('TUKAR', 'ganti_spesifikasi', 'Ganti Spesifikasi'),
('TUKAR', 'pengganti_unit_rusak', 'Pengganti Unit Rusak'),
('TUKAR', 'peremajaan_unit', 'Peremajaan Unit'),

-- Untuk RELOKASI
('RELOKASI', 'pindah_lokasi_customer', 'Pindah Lokasi Customer');

-- =====================================================
-- 4. CREATE AUDIT LOG TABLE
-- =====================================================

CREATE TABLE IF NOT EXISTS workflow_status_log (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    delivery_instruction_id INT UNSIGNED NOT NULL,
    old_status_eksekusi VARCHAR(50) NULL,
    new_status_eksekusi VARCHAR(50) NOT NULL,
    changed_by INT UNSIGNED NULL,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes TEXT NULL,
    FOREIGN KEY (delivery_instruction_id) REFERENCES delivery_instructions(id) ON DELETE CASCADE,
    INDEX idx_di_status_log (delivery_instruction_id, changed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- 5. CREATE TRIGGERS FOR AUTO-LOGGING
-- =====================================================

DELIMITER $$

-- Trigger untuk auto-log perubahan status eksekusi
DROP TRIGGER IF EXISTS tr_di_status_change_log$$
CREATE TRIGGER tr_di_status_change_log
    AFTER UPDATE ON delivery_instructions
    FOR EACH ROW
BEGIN
    IF OLD.status_eksekusi != NEW.status_eksekusi THEN
        INSERT INTO workflow_status_log (
            delivery_instruction_id,
            old_status_eksekusi,
            new_status_eksekusi,
            changed_by,
            notes
        ) VALUES (
            NEW.id,
            OLD.status_eksekusi,
            NEW.status_eksekusi,
            1, -- Default user ID, should be replaced with actual user
            CONCAT('Status changed from ', OLD.status_eksekusi, ' to ', NEW.status_eksekusi)
        );
    END IF;
END$$

-- Trigger untuk auto-generate SPK saat DI dibuat dengan jenis ANTAR atau TUKAR
DROP TRIGGER IF EXISTS tr_di_auto_generate_spk$$
CREATE TRIGGER tr_di_auto_generate_spk
    AFTER INSERT ON delivery_instructions
    FOR EACH ROW
BEGIN
    DECLARE spk_nomor VARCHAR(100);
    DECLARE spk_id INT;
    
    -- Hanya auto-generate SPK untuk jenis yang membutuhkan persiapan unit
    IF NEW.jenis_perintah IN ('ANTAR', 'TUKAR') THEN
        -- Generate nomor SPK
        SET spk_nomor = CONCAT('SPK/', DATE_FORMAT(NOW(), '%Y%m'), '/', 
                              LPAD((SELECT COALESCE(MAX(SUBSTRING(nomor_spk, -3)), 0) + 1 
                                   FROM spk 
                                   WHERE nomor_spk LIKE CONCAT('SPK/', DATE_FORMAT(NOW(), '%Y%m'), '/%')), 3, '0'));
        
        -- Insert SPK baru
        INSERT INTO spk (
            nomor_spk,
            jenis_spk,
            po_kontrak_nomor,
            pelanggan,
            lokasi,
            delivery_plan,
            status,
            delivery_instruction_id,
            auto_generated,
            dibuat_oleh,
            dibuat_pada
        ) VALUES (
            spk_nomor,
            'UNIT',
            NEW.po_kontrak_nomor,
            NEW.pelanggan,
            NEW.lokasi,
            NEW.tanggal_kirim,
            'SUBMITTED',
            NEW.id,
            TRUE,
            NEW.dibuat_oleh,
            NOW()
        );
        
        -- Update DI dengan SPK ID
        SET spk_id = LAST_INSERT_ID();
        UPDATE delivery_instructions 
        SET spk_id = spk_id 
        WHERE id = NEW.id;
        
        -- Update status eksekusi ke Persiapan Unit
        UPDATE delivery_instructions 
        SET status_eksekusi = 'Persiapan Unit' 
        WHERE id = NEW.id;
    END IF;
END$$

DELIMITER ;

-- =====================================================
-- 6. CREATE VIEWS FOR EASY REPORTING
-- =====================================================

-- View untuk dashboard Service (hanya SPK yang perlu persiapan)
CREATE OR REPLACE VIEW v_service_dashboard AS
SELECT 
    s.id as spk_id,
    s.nomor_spk,
    s.jenis_spk,
    s.pelanggan,
    s.lokasi,
    s.delivery_plan,
    s.status as spk_status,
    di.id as di_id,
    di.nomor_di,
    di.jenis_perintah,
    di.tujuan_perintah,
    di.status_eksekusi,
    di.tanggal_kirim,
    s.auto_generated,
    s.dibuat_pada as spk_created,
    di.dibuat_pada as di_created
FROM spk s
LEFT JOIN delivery_instructions di ON s.delivery_instruction_id = di.id
WHERE s.auto_generated = TRUE
   OR s.delivery_instruction_id IS NOT NULL;

-- View untuk dashboard Operasional (semua DI yang siap eksekusi)
CREATE OR REPLACE VIEW v_operasional_dashboard AS
SELECT 
    di.id as di_id,
    di.nomor_di,
    di.jenis_perintah,
    di.tujuan_perintah,
    di.status_eksekusi,
    di.pelanggan,
    di.lokasi,
    di.tanggal_kirim,
    di.catatan,
    di.po_kontrak_nomor,
    s.id as spk_id,
    s.nomor_spk,
    s.status as spk_status,
    CASE 
        WHEN di.jenis_perintah IN ('ANTAR', 'TUKAR') AND s.status = 'COMPLETED' THEN 'Siap Kirim'
        WHEN di.jenis_perintah IN ('TARIK', 'RELOKASI') THEN 'Siap Ambil'
        ELSE di.status_eksekusi
    END as display_status,
    di.dibuat_pada as di_created
FROM delivery_instructions di
LEFT JOIN spk s ON di.spk_id = s.id
WHERE di.status_eksekusi NOT IN ('Dibatalkan', 'Selesai');

-- =====================================================
-- 7. INSERT SAMPLE DATA FOR TESTING
-- =====================================================

-- Sample DI untuk testing different scenarios
INSERT INTO delivery_instructions (
    nomor_di, jenis_perintah, tujuan_perintah, status_eksekusi,
    po_kontrak_nomor, pelanggan, lokasi, tanggal_kirim, 
    dibuat_oleh, dibuat_pada
) VALUES 
-- Test ANTAR (akan auto-generate SPK)
('DI/TEST/001', 'ANTAR', 'Unit Baru (Kontrak Baru)', 'Direncanakan', 
 'PO-TEST-001', 'PT Test Customer', 'Jakarta', '2025-09-05', 1, NOW()),

-- Test TARIK (tidak akan auto-generate SPK)  
('DI/TEST/002', 'TARIK', 'Selesai Kontrak', 'Direncanakan',
 'PO-TEST-002', 'PT Test Customer 2', 'Bekasi', '2025-09-06', 1, NOW()),

-- Test TUKAR (akan auto-generate SPK)
('DI/TEST/003', 'TUKAR', 'Ganti Spesifikasi', 'Direncanakan',
 'PO-TEST-003', 'PT Test Customer 3', 'Tangerang', '2025-09-07', 1, NOW());

-- =====================================================
-- 8. VERIFICATION QUERIES
-- =====================================================

-- Check migration results
SELECT 'Migration completed successfully' as status;

-- Verify DI migration
SELECT jenis_perintah, tujuan_perintah, status_eksekusi, COUNT(*) as count
FROM delivery_instructions 
GROUP BY jenis_perintah, tujuan_perintah, status_eksekusi;

-- Verify SPK auto-generation
SELECT COUNT(*) as auto_generated_spk_count
FROM spk 
WHERE auto_generated = TRUE;

-- Verify lookup table
SELECT jenis_perintah, COUNT(*) as tujuan_count
FROM workflow_tujuan_perintah 
GROUP BY jenis_perintah;

-- Show views data
SELECT 'Service Dashboard' as dashboard, COUNT(*) as record_count FROM v_service_dashboard
UNION ALL
SELECT 'Operasional Dashboard' as dashboard, COUNT(*) as record_count FROM v_operasional_dashboard;

-- =====================================================
-- END OF MIGRATION SCRIPT
-- =====================================================
