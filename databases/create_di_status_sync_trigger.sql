-- Trigger untuk sinkronisasi status_temp delivery_instructions
-- Agar status_temp mengikuti pergerakan status operasional secara dinamis

USE optima_db;

-- Drop existing trigger jika ada
DROP TRIGGER IF EXISTS sync_di_status_temp_on_update;

-- Buat trigger untuk auto-update status_temp berdasarkan kondisi operasional
DELIMITER $$

CREATE TRIGGER sync_di_status_temp_on_update
    BEFORE UPDATE ON delivery_instructions
    FOR EACH ROW
BEGIN
    -- Logika untuk menentukan status_temp berdasarkan field operasional yang diupdate
    
    -- Jika ada perubahan pada field operasional, update status_temp accordingly
    IF NEW.status != OLD.status OR 
       NEW.nama_supir != OLD.nama_supir OR 
       NEW.kendaraan != OLD.kendaraan OR
       NEW.berangkat_tanggal_approve != OLD.berangkat_tanggal_approve OR
       NEW.sampai_tanggal_approve != OLD.sampai_tanggal_approve THEN
        
        -- Tentukan status_temp berdasarkan kondisi field
        CASE 
            -- Jika sudah ada approval sampai, maka SELESAI
            WHEN NEW.sampai_tanggal_approve IS NOT NULL THEN
                SET NEW.status_temp = 'SELESAI';
            
            -- Jika sudah ada approval berangkat tapi belum sampai, maka DALAM_PERJALANAN
            WHEN NEW.berangkat_tanggal_approve IS NOT NULL AND NEW.sampai_tanggal_approve IS NULL THEN
                SET NEW.status_temp = 'DALAM_PERJALANAN';
            
            -- Jika sudah ada supir dan kendaraan tapi belum berangkat, maka SIAP_KIRIM
            WHEN NEW.nama_supir IS NOT NULL AND NEW.kendaraan IS NOT NULL AND NEW.berangkat_tanggal_approve IS NULL THEN
                SET NEW.status_temp = 'SIAP_KIRIM';
            
            -- Jika status = PROCESSED dan belum ada supir/kendaraan, maka PERSIAPAN_UNIT
            WHEN NEW.status = 'PROCESSED' AND (NEW.nama_supir IS NULL OR NEW.kendaraan IS NULL) THEN
                SET NEW.status_temp = 'PERSIAPAN_UNIT';
            
            -- Jika status = PROCESSED dan sudah ada supir/kendaraan, maka DISETUJUI
            WHEN NEW.status = 'PROCESSED' AND NEW.nama_supir IS NOT NULL AND NEW.kendaraan IS NOT NULL THEN
                SET NEW.status_temp = 'DISETUJUI';
            
            -- Jika status = DELIVERED, maka SELESAI
            WHEN NEW.status = 'DELIVERED' THEN
                SET NEW.status_temp = 'SELESAI';
            
            -- Jika status = CANCELLED, maka DIBATALKAN
            WHEN NEW.status = 'CANCELLED' THEN
                SET NEW.status_temp = 'DIBATALKAN';
            
            -- Default untuk status SUBMITTED
            ELSE
                SET NEW.status_temp = 'DIAJUKAN';
        END CASE;
    END IF;
    
    -- Update timestamp
    SET NEW.diperbarui_pada = NOW();
END$$

DELIMITER ;

-- Buat stored procedure untuk sync manual existing data
DELIMITER $$

CREATE PROCEDURE sync_existing_di_status_temp()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE di_id INT;
    DECLARE cur CURSOR FOR SELECT id FROM delivery_instructions;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    OPEN cur;
    
    read_loop: LOOP
        FETCH cur INTO di_id;
        IF done THEN
            LEAVE read_loop;
        END IF;
        
        -- Update setiap record untuk trigger auto-sync
        UPDATE delivery_instructions 
        SET diperbarui_pada = NOW() 
        WHERE id = di_id;
        
    END LOOP;
    
    CLOSE cur;
END$$

DELIMITER ;

-- Jalankan sync untuk data yang sudah ada
CALL sync_existing_di_status_temp();

-- Drop procedure setelah digunakan
DROP PROCEDURE sync_existing_di_status_temp;

-- Tampilkan hasil
SELECT 
    id,
    nomor_di,
    status as status_utama,
    status_temp,
    nama_supir,
    kendaraan,
    berangkat_tanggal_approve,
    sampai_tanggal_approve
FROM delivery_instructions 
ORDER BY id DESC 
LIMIT 10;
