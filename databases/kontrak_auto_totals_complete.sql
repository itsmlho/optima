-- Complete Implementation: Tambah field harga sewa ke inventory_unit
ALTER TABLE `inventory_unit` 
ADD COLUMN `harga_sewa_bulanan` DECIMAL(15,2) NULL DEFAULT NULL COMMENT 'Harga sewa per bulan' AFTER `keterangan`,
ADD COLUMN `harga_sewa_harian` DECIMAL(15,2) NULL DEFAULT NULL COMMENT 'Harga sewa per hari' AFTER `harga_sewa_bulanan`;

-- Tambah field untuk jenis sewa di kontrak (bulanan/harian)
ALTER TABLE `kontrak` 
ADD COLUMN `jenis_sewa` ENUM('BULANAN', 'HARIAN') DEFAULT 'BULANAN' COMMENT 'Jenis periode sewa' AFTER `total_units`;

-- Drop existing triggers jika ada
DROP TRIGGER IF EXISTS update_kontrak_totals_after_unit_insert;
DROP TRIGGER IF EXISTS update_kontrak_totals_after_unit_update;
DROP TRIGGER IF EXISTS update_kontrak_totals_after_unit_delete;

-- Trigger otomatis update kontrak totals (MySQL) - Updated untuk handle harga harian/bulanan
DELIMITER $$

CREATE TRIGGER update_kontrak_totals_after_unit_insert
AFTER INSERT ON inventory_unit
FOR EACH ROW
BEGIN
    IF NEW.kontrak_id IS NOT NULL THEN
        CALL update_kontrak_totals_proc(NEW.kontrak_id);
    END IF;
END$$

CREATE TRIGGER update_kontrak_totals_after_unit_update  
AFTER UPDATE ON inventory_unit
FOR EACH ROW
BEGIN
    -- Update old kontrak if changed
    IF OLD.kontrak_id IS NOT NULL AND (OLD.kontrak_id != NEW.kontrak_id OR NEW.kontrak_id IS NULL) THEN
        CALL update_kontrak_totals_proc(OLD.kontrak_id);
    END IF;
    
    -- Update new kontrak
    IF NEW.kontrak_id IS NOT NULL THEN
        CALL update_kontrak_totals_proc(NEW.kontrak_id);
    END IF;
END$$

CREATE TRIGGER update_kontrak_totals_after_unit_delete
AFTER DELETE ON inventory_unit  
FOR EACH ROW
BEGIN
    IF OLD.kontrak_id IS NOT NULL THEN
        CALL update_kontrak_totals_proc(OLD.kontrak_id);
    END IF;
END$$

DELIMITER ;

-- Stored procedure untuk calculate kontrak totals
DELIMITER $$

CREATE PROCEDURE update_kontrak_totals_proc(IN kontrak_id_param INT UNSIGNED)
BEGIN
    DECLARE total_units_count INT DEFAULT 0;
    DECLARE nilai_total_amount DECIMAL(15,2) DEFAULT 0;
    DECLARE jenis_sewa_kontrak VARCHAR(10) DEFAULT 'BULANAN';
    
    -- Get kontrak jenis_sewa
    SELECT jenis_sewa INTO jenis_sewa_kontrak 
    FROM kontrak 
    WHERE id = kontrak_id_param;
    
    -- Set default jika NULL
    IF jenis_sewa_kontrak IS NULL THEN
        SET jenis_sewa_kontrak = 'BULANAN';
    END IF;
    
    -- Calculate totals berdasarkan jenis sewa
    IF jenis_sewa_kontrak = 'HARIAN' THEN
        SELECT 
            COUNT(*) as total_units,
            COALESCE(SUM(harga_sewa_harian), 0) as nilai_total
        INTO total_units_count, nilai_total_amount
        FROM inventory_unit 
        WHERE kontrak_id = kontrak_id_param;
    ELSE
        -- Default BULANAN
        SELECT 
            COUNT(*) as total_units,
            COALESCE(SUM(harga_sewa_bulanan), 0) as nilai_total
        INTO total_units_count, nilai_total_amount
        FROM inventory_unit 
        WHERE kontrak_id = kontrak_id_param;
    END IF;
    
    -- Update kontrak
    UPDATE kontrak SET 
        total_units = total_units_count,
        nilai_total = nilai_total_amount
    WHERE id = kontrak_id_param;
    
END$$

DELIMITER ;

-- Update existing data - set default jenis_sewa untuk kontrak yang sudah ada
UPDATE kontrak SET jenis_sewa = 'BULANAN' WHERE jenis_sewa IS NULL;

-- Optional: Set default harga untuk unit yang sudah ada kontrak_id (misal 8jt/bulan, 300rb/hari)
-- UPDATE inventory_unit SET 
--     harga_sewa_bulanan = 8000000,
--     harga_sewa_harian = 300000
-- WHERE kontrak_id IS NOT NULL AND harga_sewa_bulanan IS NULL;

-- Manual trigger untuk update semua kontrak yang sudah ada
-- (jalankan setelah set harga default di atas)
/*
UPDATE kontrak k SET 
    total_units = (
        SELECT COUNT(*) 
        FROM inventory_unit iu 
        WHERE iu.kontrak_id = k.id
    ),
    nilai_total = (
        SELECT CASE 
            WHEN k.jenis_sewa = 'HARIAN' THEN COALESCE(SUM(iu.harga_sewa_harian), 0)
            ELSE COALESCE(SUM(iu.harga_sewa_bulanan), 0)
        END
        FROM inventory_unit iu 
        WHERE iu.kontrak_id = k.id
    );
*/
