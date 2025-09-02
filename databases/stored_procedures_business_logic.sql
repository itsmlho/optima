-- ===============================================================================
-- STORED PROCEDURES UNTUK BUSINESS LOGIC OPTIMIZATION
-- Centralized business logic untuk inventory management
-- ===============================================================================

-- 1. PROCEDURE: Assign Unit ke Kontrak
DELIMITER $$
CREATE PROCEDURE AssignUnitToKontrak(
    IN p_unit_id INT,
    IN p_kontrak_id INT,
    IN p_kontrak_spesifikasi_id INT DEFAULT NULL,
    IN p_user_id INT DEFAULT 1
)
BEGIN
    DECLARE v_kontrak_status VARCHAR(20);
    DECLARE v_unit_status INT;
    DECLARE v_existing_kontrak INT DEFAULT NULL;
    DECLARE v_pelanggan VARCHAR(255);
    DECLARE v_harga_bulanan DECIMAL(15,2) DEFAULT NULL;
    DECLARE v_harga_harian DECIMAL(15,2) DEFAULT NULL;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    -- Validasi kontrak exists dan status
    SELECT status, pelanggan INTO v_kontrak_status, v_pelanggan
    FROM kontrak WHERE id = p_kontrak_id;
    
    IF v_kontrak_status IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Kontrak tidak ditemukan';
    END IF;
    
    IF v_kontrak_status NOT IN ('Aktif', 'Pending') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Kontrak tidak dalam status aktif/pending';
    END IF;
    
    -- Validasi unit exists dan status
    SELECT status_unit_id, kontrak_id INTO v_unit_status, v_existing_kontrak
    FROM inventory_unit WHERE id_inventory_unit = p_unit_id;
    
    IF v_unit_status IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Unit tidak ditemukan';
    END IF;
    
    IF v_unit_status NOT IN (6, 7, 8) THEN -- BOOKING, STOCK ASET, STOCK NON ASET
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Unit tidak tersedia untuk rental (status harus BOOKING/STOCK)';
    END IF;
    
    IF v_existing_kontrak IS NOT NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Unit sudah di-assign ke kontrak lain';
    END IF;
    
    -- Ambil harga dari spesifikasi jika ada
    IF p_kontrak_spesifikasi_id IS NOT NULL THEN
        SELECT harga_per_unit_bulanan, harga_per_unit_harian
        INTO v_harga_bulanan, v_harga_harian
        FROM kontrak_spesifikasi 
        WHERE id = p_kontrak_spesifikasi_id AND kontrak_id = p_kontrak_id;
    END IF;
    
    -- Update unit
    UPDATE inventory_unit 
    SET 
        kontrak_id = p_kontrak_id,
        kontrak_spesifikasi_id = p_kontrak_spesifikasi_id,
        status_unit_id = 3, -- RENTAL
        lokasi_unit = v_pelanggan,
        harga_sewa_bulanan = COALESCE(v_harga_bulanan, harga_sewa_bulanan),
        harga_sewa_harian = COALESCE(v_harga_harian, harga_sewa_harian),
        updated_at = NOW()
    WHERE id_inventory_unit = p_unit_id;
    
    -- Update jumlah unit di kontrak
    UPDATE kontrak 
    SET total_units = total_units + 1,
        diperbarui_pada = NOW()
    WHERE id = p_kontrak_id;
    
    -- Update jumlah tersedia di kontrak_spesifikasi
    IF p_kontrak_spesifikasi_id IS NOT NULL THEN
        UPDATE kontrak_spesifikasi 
        SET jumlah_tersedia = jumlah_tersedia + 1,
            diperbarui_pada = NOW()
        WHERE id = p_kontrak_spesifikasi_id;
    END IF;
    
    COMMIT;
    
    SELECT 
        CONCAT('Unit ', p_unit_id, ' berhasil di-assign ke kontrak ', p_kontrak_id) as message,
        p_unit_id as unit_id,
        p_kontrak_id as kontrak_id,
        'ASSIGNED' as status;
        
END$$
DELIMITER ;

-- 2. PROCEDURE: Unassign Unit dari Kontrak
DELIMITER $$
CREATE PROCEDURE UnassignUnitFromKontrak(
    IN p_unit_id INT,
    IN p_user_id INT DEFAULT 1,
    IN p_reason VARCHAR(255) DEFAULT 'Manual unassign'
)
BEGIN
    DECLARE v_kontrak_id INT DEFAULT NULL;
    DECLARE v_kontrak_spesifikasi_id INT DEFAULT NULL;
    DECLARE v_unit_status INT;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    -- Get current assignment
    SELECT kontrak_id, kontrak_spesifikasi_id, status_unit_id
    INTO v_kontrak_id, v_kontrak_spesifikasi_id, v_unit_status
    FROM inventory_unit WHERE id_inventory_unit = p_unit_id;
    
    IF v_kontrak_id IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Unit tidak di-assign ke kontrak manapun';
    END IF;
    
    -- Update unit back to stock
    UPDATE inventory_unit 
    SET 
        kontrak_id = NULL,
        kontrak_spesifikasi_id = NULL,
        status_unit_id = 7, -- STOCK ASET
        lokasi_unit = 'Warehouse',
        tanggal_kirim = NULL,
        harga_sewa_bulanan = NULL,
        harga_sewa_harian = NULL,
        updated_at = NOW()
    WHERE id_inventory_unit = p_unit_id;
    
    -- Update jumlah unit di kontrak
    UPDATE kontrak 
    SET total_units = GREATEST(0, total_units - 1),
        diperbarui_pada = NOW()
    WHERE id = v_kontrak_id;
    
    -- Update jumlah tersedia di kontrak_spesifikasi
    IF v_kontrak_spesifikasi_id IS NOT NULL THEN
        UPDATE kontrak_spesifikasi 
        SET jumlah_tersedia = GREATEST(0, jumlah_tersedia - 1),
            diperbarui_pada = NOW()
        WHERE id = v_kontrak_spesifikasi_id;
    END IF;
    
    COMMIT;
    
    SELECT 
        CONCAT('Unit ', p_unit_id, ' berhasil di-unassign dari kontrak ', v_kontrak_id) as message,
        p_unit_id as unit_id,
        v_kontrak_id as kontrak_id,
        'UNASSIGNED' as status;
        
END$$
DELIMITER ;

-- 3. PROCEDURE: Get Unit Details dengan semua relasi
DELIMITER $$
CREATE PROCEDURE GetUnitDetails(
    IN p_unit_id INT
)
BEGIN
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
        -- Kontrak spesifikasi
        iu.kontrak_spesifikasi_id,
        ks.spek_kode,
        ks.harga_per_unit_bulanan as spek_harga_bulanan,
        ks.harga_per_unit_harian as spek_harga_harian,
        -- Unit specs
        iu.model_unit_id,
        mu.merk_unit,
        mu.model_unit,
        iu.tipe_unit_id,
        tu.nama_tipe as tipe_unit,
        iu.kapasitas_unit_id,
        ku.kapasitas,
        -- Latest SPK info
        spk_latest.nomor_spk as latest_spk,
        spk_latest.status as spk_status,
        spk_latest.delivery_plan,
        -- Latest DI info
        di_latest.nomor_di as latest_di,
        di_latest.status as di_status,
        di_latest.tanggal_kirim as di_tanggal_kirim,
        -- Timestamps
        iu.created_at,
        iu.updated_at
    FROM inventory_unit iu
    LEFT JOIN status_unit su ON iu.status_unit_id = su.id
    LEFT JOIN departemen d ON iu.departemen_id = d.id
    LEFT JOIN kontrak k ON iu.kontrak_id = k.id
    LEFT JOIN kontrak_spesifikasi ks ON iu.kontrak_spesifikasi_id = ks.id
    LEFT JOIN model_unit mu ON iu.model_unit_id = mu.id
    LEFT JOIN tipe_unit tu ON iu.tipe_unit_id = tu.id
    LEFT JOIN kapasitas_unit ku ON iu.kapasitas_unit_id = ku.id
    -- Latest SPK
    LEFT JOIN (
        SELECT s.*, ROW_NUMBER() OVER (PARTITION BY s.kontrak_id ORDER BY s.dibuat_pada DESC) as rn
        FROM spk s
    ) spk_latest ON iu.kontrak_id = spk_latest.kontrak_id AND spk_latest.rn = 1
    -- Latest DI
    LEFT JOIN (
        SELECT di.*, dit.unit_id, ROW_NUMBER() OVER (PARTITION BY dit.unit_id ORDER BY di.dibuat_pada DESC) as rn
        FROM delivery_instructions di
        JOIN delivery_items dit ON di.id = dit.di_id
        WHERE dit.item_type = 'UNIT'
    ) di_latest ON iu.id_inventory_unit = di_latest.unit_id AND di_latest.rn = 1
    WHERE iu.id_inventory_unit = p_unit_id;
END$$
DELIMITER ;

-- 4. PROCEDURE: Get Available Units untuk kontrak
DELIMITER $$
CREATE PROCEDURE GetAvailableUnitsForKontrak(
    IN p_tipe_unit_id INT DEFAULT NULL,
    IN p_departemen_id INT DEFAULT NULL,
    IN p_merk_unit VARCHAR(100) DEFAULT NULL,
    IN p_kapasitas_id INT DEFAULT NULL
)
BEGIN
    SELECT 
        iu.id_inventory_unit,
        iu.no_unit,
        iu.serial_number,
        su.nama_status as status_name,
        iu.lokasi_unit,
        d.nama_departemen,
        mu.merk_unit,
        mu.model_unit,
        tu.nama_tipe as tipe_unit,
        ku.kapasitas,
        iu.created_at
    FROM inventory_unit iu
    LEFT JOIN status_unit su ON iu.status_unit_id = su.id
    LEFT JOIN departemen d ON iu.departemen_id = d.id
    LEFT JOIN model_unit mu ON iu.model_unit_id = mu.id
    LEFT JOIN tipe_unit tu ON iu.tipe_unit_id = tu.id
    LEFT JOIN kapasitas_unit ku ON iu.kapasitas_unit_id = ku.id
    WHERE iu.status_unit_id IN (6, 7, 8) -- BOOKING, STOCK ASET, STOCK NON ASET
    AND iu.kontrak_id IS NULL
    AND (p_tipe_unit_id IS NULL OR iu.tipe_unit_id = p_tipe_unit_id)
    AND (p_departemen_id IS NULL OR iu.departemen_id = p_departemen_id)
    AND (p_merk_unit IS NULL OR mu.merk_unit = p_merk_unit)
    AND (p_kapasitas_id IS NULL OR iu.kapasitas_unit_id = p_kapasitas_id)
    ORDER BY iu.no_unit;
END$$
DELIMITER ;

-- 5. PROCEDURE: Update Unit Status setelah Delivery
DELIMITER $$
CREATE PROCEDURE UpdateUnitAfterDelivery(
    IN p_di_id INT,
    IN p_user_id INT DEFAULT 1
)
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_unit_id INT;
    DECLARE v_tanggal_sampai DATE;
    
    DECLARE unit_cursor CURSOR FOR
        SELECT dit.unit_id
        FROM delivery_items dit
        WHERE dit.di_id = p_di_id AND dit.item_type = 'UNIT';
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    -- Get tanggal sampai dari DI
    SELECT sampai_tanggal_approve INTO v_tanggal_sampai
    FROM delivery_instructions WHERE id = p_di_id;
    
    -- Update semua units dalam DI ini
    OPEN unit_cursor;
    read_loop: LOOP
        FETCH unit_cursor INTO v_unit_id;
        IF done THEN
            LEAVE read_loop;
        END IF;
        
        UPDATE inventory_unit
        SET tanggal_kirim = v_tanggal_sampai,
            updated_at = NOW()
        WHERE id_inventory_unit = v_unit_id;
        
    END LOOP;
    CLOSE unit_cursor;
    
    COMMIT;
    
    SELECT 
        CONCAT('Units dalam DI ', p_di_id, ' berhasil diupdate tanggal kirim') as message,
        v_tanggal_sampai as tanggal_kirim;
        
END$$
DELIMITER ;

-- 6. FUNCTION: Get Unit Rental Days
DELIMITER $$
CREATE FUNCTION GetUnitRentalDays(p_unit_id INT)
RETURNS INT
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE v_tanggal_kirim DATE;
    DECLARE v_days INT DEFAULT 0;
    
    SELECT tanggal_kirim INTO v_tanggal_kirim
    FROM inventory_unit WHERE id_inventory_unit = p_unit_id;
    
    IF v_tanggal_kirim IS NOT NULL THEN
        SET v_days = DATEDIFF(CURDATE(), v_tanggal_kirim);
    END IF;
    
    RETURN v_days;
END$$
DELIMITER ;

-- 7. FUNCTION: Calculate Unit Revenue
DELIMITER $$
CREATE FUNCTION CalculateUnitRevenue(p_unit_id INT, p_calculation_type ENUM('ACTUAL', 'POTENTIAL'))
RETURNS DECIMAL(15,2)
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE v_harga_bulanan DECIMAL(15,2) DEFAULT 0;
    DECLARE v_harga_harian DECIMAL(15,2) DEFAULT 0;
    DECLARE v_tanggal_kirim DATE;
    DECLARE v_jenis_sewa VARCHAR(20);
    DECLARE v_days INT DEFAULT 0;
    DECLARE v_months INT DEFAULT 0;
    DECLARE v_revenue DECIMAL(15,2) DEFAULT 0;
    
    SELECT 
        iu.harga_sewa_bulanan,
        iu.harga_sewa_harian,
        iu.tanggal_kirim,
        k.jenis_sewa
    INTO 
        v_harga_bulanan,
        v_harga_harian,
        v_tanggal_kirim,
        v_jenis_sewa
    FROM inventory_unit iu
    LEFT JOIN kontrak k ON iu.kontrak_id = k.id
    WHERE iu.id_inventory_unit = p_unit_id;
    
    IF p_calculation_type = 'ACTUAL' AND v_tanggal_kirim IS NOT NULL THEN
        SET v_days = DATEDIFF(CURDATE(), v_tanggal_kirim);
        
        IF v_jenis_sewa = 'BULANAN' AND v_harga_bulanan > 0 THEN
            SET v_months = CEILING(v_days / 30);
            SET v_revenue = v_months * v_harga_bulanan;
        ELSEIF v_jenis_sewa = 'HARIAN' AND v_harga_harian > 0 THEN
            SET v_revenue = v_days * v_harga_harian;
        END IF;
    END IF;
    
    RETURN v_revenue;
END$$
DELIMITER ;

-- ===============================================================================
-- USAGE EXAMPLES
-- ===============================================================================

-- Example 1: Assign unit ke kontrak
-- CALL AssignUnitToKontrak(1, 1, 4, 1);

-- Example 2: Unassign unit dari kontrak  
-- CALL UnassignUnitFromKontrak(1, 1, 'Unit dikembalikan');

-- Example 3: Get unit details lengkap
-- CALL GetUnitDetails(1);

-- Example 4: Get available units for rental
-- CALL GetAvailableUnitsForKontrak(6, 2, 'HELI', NULL);

-- Example 5: Update units after delivery
-- CALL UpdateUnitAfterDelivery(1, 1);

-- Example 6: Calculate unit revenue
-- SELECT id_inventory_unit, no_unit, CalculateUnitRevenue(id_inventory_unit, 'ACTUAL') as actual_revenue
-- FROM inventory_unit WHERE kontrak_id IS NOT NULL;

-- ===============================================================================
-- GRANT PERMISSIONS (jika menggunakan user khusus)
-- ===============================================================================

-- GRANT EXECUTE ON PROCEDURE smloptima.AssignUnitToKontrak TO 'app_user'@'%';
-- GRANT EXECUTE ON PROCEDURE smloptima.UnassignUnitFromKontrak TO 'app_user'@'%';
-- GRANT EXECUTE ON PROCEDURE smloptima.GetUnitDetails TO 'app_user'@'%';
-- GRANT EXECUTE ON PROCEDURE smloptima.GetAvailableUnitsForKontrak TO 'app_user'@'%';
-- GRANT EXECUTE ON PROCEDURE smloptima.UpdateUnitAfterDelivery TO 'app_user'@'%';
-- GRANT EXECUTE ON FUNCTION smloptima.GetUnitRentalDays TO 'app_user'@'%';
-- GRANT EXECUTE ON FUNCTION smloptima.CalculateUnitRevenue TO 'app_user'@'%';
