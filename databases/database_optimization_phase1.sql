-- ===============================================================================
-- IMPLEMENTASI BERTAHAP DATABASE OPTIMIZATION
-- Fase 1: Critical Fixes & Foreign Keys (PRIORITAS TINGGI)
-- ===============================================================================

-- BACKUP TABLES TERLEBIH DAHULU
CREATE TABLE inventory_unit_backup_$(date +%Y%m%d) AS SELECT * FROM inventory_unit;
CREATE TABLE kontrak_backup_$(date +%Y%m%d) AS SELECT * FROM kontrak;
CREATE TABLE delivery_instructions_backup_$(date +%Y%m%d) AS SELECT * FROM delivery_instructions;

-- 1. TAMBAH FOREIGN KEY CONSTRAINTS (Paling Penting)
-- Cek dulu apakah ada orphaned records
SELECT 'Orphaned inventory_unit.kontrak_id:' as check_type, COUNT(*) as count
FROM inventory_unit iu 
LEFT JOIN kontrak k ON iu.kontrak_id = k.id 
WHERE iu.kontrak_id IS NOT NULL AND k.id IS NULL

UNION ALL

SELECT 'Orphaned inventory_unit.departemen_id:', COUNT(*)
FROM inventory_unit iu 
LEFT JOIN departemen d ON iu.departemen_id = d.id 
WHERE iu.departemen_id IS NOT NULL AND d.id IS NULL

UNION ALL

SELECT 'Orphaned delivery_instructions.spk_id:', COUNT(*)
FROM delivery_instructions di
LEFT JOIN spk s ON di.spk_id = s.id
WHERE di.spk_id IS NOT NULL AND s.id IS NULL;

-- Jika tidak ada orphaned records, tambahkan foreign keys:
-- ALTER TABLE inventory_unit 
-- ADD CONSTRAINT fk_inventory_unit_kontrak 
--     FOREIGN KEY (kontrak_id) REFERENCES kontrak(id) ON DELETE SET NULL ON UPDATE CASCADE;

-- 2. FIX DATA INCONSISTENCIES
-- Update status unit yang punya kontrak tapi statusnya bukan RENTAL
UPDATE inventory_unit 
SET status_unit_id = 3, -- RENTAL
    updated_at = NOW()
WHERE kontrak_id IS NOT NULL 
AND status_unit_id != 3;

-- Update lokasi_unit dari kontrak.pelanggan
UPDATE inventory_unit iu
JOIN kontrak k ON iu.kontrak_id = k.id
SET iu.lokasi_unit = k.pelanggan,
    iu.updated_at = NOW()
WHERE iu.kontrak_id IS NOT NULL 
AND (iu.lokasi_unit IS NULL OR iu.lokasi_unit = 'Warehouse' OR iu.lokasi_unit = 'POS 1');

-- ===============================================================================
-- Fase 2: Business Logic Triggers (PRIORITAS SEDANG)
-- ===============================================================================

-- Trigger untuk auto-update status saat assign/unassign kontrak
DELIMITER $$
CREATE TRIGGER tr_inventory_unit_kontrak_status 
BEFORE UPDATE ON inventory_unit 
FOR EACH ROW 
BEGIN
    -- Jika kontrak di-assign (dari NULL ke ada value)
    IF OLD.kontrak_id IS NULL AND NEW.kontrak_id IS NOT NULL THEN
        SET NEW.status_unit_id = 3; -- RENTAL
        
        -- Set lokasi dari kontrak jika belum ada
        IF NEW.lokasi_unit IS NULL OR NEW.lokasi_unit IN ('Warehouse', 'POS 1') THEN
            SET NEW.lokasi_unit = (
                SELECT pelanggan FROM kontrak WHERE id = NEW.kontrak_id
            );
        END IF;
        
        -- Set harga sewa dari spesifikasi jika ada
        IF NEW.kontrak_spesifikasi_id IS NOT NULL THEN
            SELECT 
                harga_per_unit_bulanan,
                harga_per_unit_harian
            INTO 
                NEW.harga_sewa_bulanan,
                NEW.harga_sewa_harian
            FROM kontrak_spesifikasi 
            WHERE id = NEW.kontrak_spesifikasi_id;
        END IF;
    END IF;
    
    -- Jika kontrak di-unassign (dari ada value ke NULL)
    IF OLD.kontrak_id IS NOT NULL AND NEW.kontrak_id IS NULL THEN
        SET NEW.status_unit_id = 7; -- STOCK ASET
        SET NEW.lokasi_unit = 'Warehouse';
        SET NEW.harga_sewa_bulanan = NULL;
        SET NEW.harga_sewa_harian = NULL;
        SET NEW.tanggal_kirim = NULL;
    END IF;
END$$
DELIMITER ;

-- ===============================================================================
-- Fase 3: Performance Indexes (PRIORITAS SEDANG)
-- ===============================================================================

-- Indexes untuk query yang sering digunakan
CREATE INDEX idx_inventory_unit_status_dept ON inventory_unit (status_unit_id, departemen_id);
CREATE INDEX idx_inventory_unit_kontrak_status ON inventory_unit (kontrak_id, status_unit_id);
CREATE INDEX idx_inventory_unit_no_unit ON inventory_unit (no_unit);

CREATE INDEX idx_delivery_instructions_spk_status ON delivery_instructions (spk_id, status);
CREATE INDEX idx_spk_kontrak_status ON spk (kontrak_id, status);

-- ===============================================================================
-- Fase 4: Utility Views (PRIORITAS RENDAH)
-- ===============================================================================

-- View untuk unit details lengkap
CREATE OR REPLACE VIEW v_unit_details AS
SELECT 
    iu.id_inventory_unit,
    iu.no_unit,
    iu.serial_number,
    iu.status_unit_id,
    su.nama_status as status_name,
    iu.lokasi_unit,
    iu.departemen_id,
    d.nama_departemen,
    iu.tanggal_kirim,
    iu.harga_sewa_bulanan,
    iu.harga_sewa_harian,
    -- Kontrak info
    iu.kontrak_id,
    k.no_kontrak,
    k.pelanggan as kontrak_pelanggan,
    k.lokasi as kontrak_lokasi,
    k.pic as kontrak_pic,
    k.kontak as kontrak_kontak,
    k.status as kontrak_status,
    k.tanggal_mulai as kontrak_start,
    k.tanggal_berakhir as kontrak_end,
    -- Unit specs
    iu.model_unit_id,
    mu.merk_unit,
    mu.model_unit,
    iu.tipe_unit_id,
    tu.nama_tipe as tipe_unit,
    iu.kapasitas_unit_id,
    ku.kapasitas,
    -- Timestamps
    iu.created_at,
    iu.updated_at
FROM inventory_unit iu
LEFT JOIN status_unit su ON iu.status_unit_id = su.id
LEFT JOIN departemen d ON iu.departemen_id = d.id
LEFT JOIN kontrak k ON iu.kontrak_id = k.id
LEFT JOIN model_unit mu ON iu.model_unit_id = mu.id
LEFT JOIN tipe_unit tu ON iu.tipe_unit_id = tu.id
LEFT JOIN kapasitas_unit ku ON iu.kapasitas_unit_id = ku.id;

-- ===============================================================================
-- TESTING QUERIES
-- Query untuk test hasil optimasi
-- ===============================================================================

-- 1. Test unit dengan kontrak
SELECT 
    'Units dengan kontrak:' as test_type,
    COUNT(*) as count,
    GROUP_CONCAT(DISTINCT status_unit_id) as status_ids
FROM inventory_unit 
WHERE kontrak_id IS NOT NULL;

-- 2. Test lokasi unit sudah sesuai kontrak
SELECT 
    'Lokasi mismatch:' as test_type,
    COUNT(*) as count
FROM inventory_unit iu
JOIN kontrak k ON iu.kontrak_id = k.id
WHERE iu.lokasi_unit != k.pelanggan;

-- 3. Test units tersedia untuk rental
SELECT 
    'Units tersedia rental:' as test_type,
    COUNT(*) as count,
    GROUP_CONCAT(DISTINCT status_unit_id) as status_ids
FROM inventory_unit 
WHERE status_unit_id IN (6, 7, 8) AND kontrak_id IS NULL;

-- 4. Test performance dengan index
EXPLAIN SELECT * FROM inventory_unit 
WHERE status_unit_id = 7 AND departemen_id = 2;

-- ===============================================================================
-- ROLLBACK SCRIPT (Jika diperlukan)
-- ===============================================================================

-- Uncomment jika perlu rollback:
-- DROP TRIGGER IF EXISTS tr_inventory_unit_kontrak_status;
-- DROP VIEW IF EXISTS v_unit_details;
-- DROP INDEX IF EXISTS idx_inventory_unit_status_dept ON inventory_unit;
-- DROP INDEX IF EXISTS idx_inventory_unit_kontrak_status ON inventory_unit;
-- DROP INDEX IF EXISTS idx_inventory_unit_no_unit ON inventory_unit;

-- RESTORE dari backup:
-- TRUNCATE TABLE inventory_unit;
-- INSERT INTO inventory_unit SELECT * FROM inventory_unit_backup_YYYYMMDD;
