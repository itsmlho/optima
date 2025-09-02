-- ===============================================================================
-- OPTIMASI DATABASE STRUCTURE - REKOMENDASI PERBAIKAN
-- Tanggal: September 1, 2025
-- Analisis: Struktur database inventory_unit dan relasi dengan sistem kontrak/SPK/DI
-- ===============================================================================

-- ISSUE 1: MISSING FOREIGN KEY CONSTRAINTS
-- Problem: Database tidak memiliki foreign key constraints yang proper
-- Impact: Data integrity tidak terjamin, kemungkinan orphaned records
-- Solution: Tambahkan foreign key constraints

-- 1.1 Foreign Keys untuk inventory_unit
ALTER TABLE `inventory_unit` 
ADD CONSTRAINT `fk_inventory_unit_kontrak` 
    FOREIGN KEY (`kontrak_id`) REFERENCES `kontrak`(`id`) ON DELETE SET NULL ON UPDATE CASCADE,
ADD CONSTRAINT `fk_inventory_unit_kontrak_spesifikasi` 
    FOREIGN KEY (`kontrak_spesifikasi_id`) REFERENCES `kontrak_spesifikasi`(`id`) ON DELETE SET NULL ON UPDATE CASCADE,
ADD CONSTRAINT `fk_inventory_unit_departemen` 
    FOREIGN KEY (`departemen_id`) REFERENCES `departemen`(`id`) ON DELETE SET NULL ON UPDATE CASCADE,
ADD CONSTRAINT `fk_inventory_unit_status` 
    FOREIGN KEY (`status_unit_id`) REFERENCES `status_unit`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- 1.2 Foreign Keys untuk delivery_instructions
ALTER TABLE `delivery_instructions`
ADD CONSTRAINT `fk_delivery_instructions_spk`
    FOREIGN KEY (`spk_id`) REFERENCES `spk`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- 1.3 Foreign Keys untuk spk
ALTER TABLE `spk`
ADD CONSTRAINT `fk_spk_kontrak`
    FOREIGN KEY (`kontrak_id`) REFERENCES `kontrak`(`id`) ON DELETE SET NULL ON UPDATE CASCADE,
ADD CONSTRAINT `fk_spk_kontrak_spesifikasi`
    FOREIGN KEY (`kontrak_spesifikasi_id`) REFERENCES `kontrak_spesifikasi`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- ===============================================================================
-- ISSUE 2: REDUNDANT DATA & NORMALIZATION
-- Problem: Data pelanggan, lokasi duplikasi di berbagai tabel
-- Solution: Normalisasi data dan penggunaan foreign keys
-- ===============================================================================

-- 2.1 Buat view untuk Unit Details dengan semua relasi
CREATE OR REPLACE VIEW `v_inventory_unit_details` AS
SELECT 
    iu.id_inventory_unit,
    iu.no_unit,
    iu.serial_number,
    iu.tahun_unit,
    iu.status_unit_id,
    su.nama_status as status_unit_name,
    iu.lokasi_unit,
    iu.departemen_id,
    d.nama_departemen,
    iu.tanggal_kirim,
    iu.harga_sewa_bulanan,
    iu.harga_sewa_harian,
    -- Kontrak Information
    iu.kontrak_id,
    k.no_kontrak,
    k.pelanggan as kontrak_pelanggan,
    k.lokasi as kontrak_lokasi,
    k.pic as kontrak_pic,
    k.kontak as kontrak_kontak,
    k.status as kontrak_status,
    k.tanggal_mulai as kontrak_start,
    k.tanggal_berakhir as kontrak_end,
    -- Kontrak Spesifikasi
    iu.kontrak_spesifikasi_id,
    ks.spek_kode,
    ks.harga_per_unit_bulanan as spek_harga_bulanan,
    ks.harga_per_unit_harian as spek_harga_harian,
    -- Unit Details
    iu.tipe_unit_id,
    tu.nama_tipe as tipe_unit_name,
    iu.model_unit_id,
    mu.merk_unit,
    mu.model_unit,
    iu.kapasitas_unit_id,
    ku.kapasitas,
    -- Delivery Information (dari delivery_items)
    di_info.di_id,
    di_info.nomor_di,
    di_info.tanggal_kirim as di_tanggal_kirim,
    di_info.status as di_status,
    -- SPK Information
    spk_info.spk_id,
    spk_info.nomor_spk,
    spk_info.status as spk_status,
    spk_info.delivery_plan as spk_delivery_plan,
    -- Timestamps
    iu.created_at,
    iu.updated_at
FROM inventory_unit iu
LEFT JOIN status_unit su ON iu.status_unit_id = su.id
LEFT JOIN departemen d ON iu.departemen_id = d.id
LEFT JOIN kontrak k ON iu.kontrak_id = k.id
LEFT JOIN kontrak_spesifikasi ks ON iu.kontrak_spesifikasi_id = ks.id
LEFT JOIN tipe_unit tu ON iu.tipe_unit_id = tu.id
LEFT JOIN model_unit mu ON iu.model_unit_id = mu.id
LEFT JOIN kapasitas_unit ku ON iu.kapasitas_unit_id = ku.id
-- Join untuk delivery information
LEFT JOIN (
    SELECT 
        dit.unit_id,
        di.id as di_id,
        di.nomor_di,
        di.tanggal_kirim,
        di.status,
        ROW_NUMBER() OVER (PARTITION BY dit.unit_id ORDER BY di.dibuat_pada DESC) as rn
    FROM delivery_items dit
    JOIN delivery_instructions di ON dit.di_id = di.id
    WHERE dit.item_type = 'UNIT'
) di_info ON iu.id_inventory_unit = di_info.unit_id AND di_info.rn = 1
-- Join untuk SPK information
LEFT JOIN (
    SELECT 
        JSON_EXTRACT(s.spesifikasi, '$.prepared_units[*].unit_id') as unit_ids,
        s.id as spk_id,
        s.nomor_spk,
        s.status,
        s.delivery_plan,
        ROW_NUMBER() OVER (PARTITION BY s.kontrak_id ORDER BY s.dibuat_pada DESC) as rn
    FROM spk s
    WHERE s.spesifikasi IS NOT NULL
) spk_info ON iu.kontrak_id = JSON_UNQUOTE(JSON_EXTRACT(spk_info.unit_ids, CONCAT('$[', iu.id_inventory_unit - 1, ']'))) 
    AND spk_info.rn = 1;

-- ===============================================================================
-- ISSUE 3: INVENTORY_UNIT STATUS WORKFLOW
-- Problem: Status unit tidak mengikuti workflow yang jelas
-- Solution: Tambah trigger untuk auto-update status berdasarkan workflow
-- ===============================================================================

-- 3.1 Trigger untuk auto-update status unit ke RENTAL saat kontrak assigned
DELIMITER $$
CREATE TRIGGER `tr_inventory_unit_status_kontrak` 
BEFORE UPDATE ON `inventory_unit` 
FOR EACH ROW 
BEGIN
    -- Jika kontrak_id diset (unit di-assign ke kontrak), ubah status ke RENTAL (3)
    IF OLD.kontrak_id IS NULL AND NEW.kontrak_id IS NOT NULL THEN
        SET NEW.status_unit_id = 3; -- RENTAL
        -- Set lokasi dari kontrak
        IF NEW.lokasi_unit IS NULL THEN
            SET NEW.lokasi_unit = (
                SELECT pelanggan 
                FROM kontrak 
                WHERE id = NEW.kontrak_id
            );
        END IF;
        -- Set harga sewa dari kontrak_spesifikasi
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
    
    -- Jika kontrak_id di-unset, kembalikan ke status STOCK
    IF OLD.kontrak_id IS NOT NULL AND NEW.kontrak_id IS NULL THEN
        SET NEW.status_unit_id = 7; -- STOCK ASET
        SET NEW.lokasi_unit = 'Warehouse';
        SET NEW.harga_sewa_bulanan = NULL;
        SET NEW.harga_sewa_harian = NULL;
    END IF;
END$$
DELIMITER ;

-- 3.2 Trigger untuk set tanggal_kirim dari delivery_instructions
DELIMITER $$
CREATE TRIGGER `tr_inventory_unit_delivery_date` 
AFTER UPDATE ON `delivery_instructions` 
FOR EACH ROW 
BEGIN
    -- Jika status DI berubah ke DELIVERED, update tanggal_kirim di inventory_unit
    IF OLD.status != 'DELIVERED' AND NEW.status = 'DELIVERED' THEN
        UPDATE inventory_unit iu
        JOIN delivery_items di ON iu.id_inventory_unit = di.unit_id
        SET iu.tanggal_kirim = NEW.sampai_tanggal_approve
        WHERE di.di_id = NEW.id AND di.item_type = 'UNIT';
    END IF;
END$$
DELIMITER ;

-- ===============================================================================
-- ISSUE 4: DATA INTEGRITY VALIDATION
-- Problem: Tidak ada validasi untuk business rules
-- Solution: Tambah constraints dan validation triggers
-- ===============================================================================

-- 4.1 Pastikan unit tidak bisa di-assign ke 2 kontrak aktif sekaligus
DELIMITER $$
CREATE TRIGGER `tr_prevent_double_kontrak_assignment` 
BEFORE UPDATE ON `inventory_unit` 
FOR EACH ROW 
BEGIN
    DECLARE existing_kontrak_count INT DEFAULT 0;
    
    IF NEW.kontrak_id IS NOT NULL THEN
        -- Cek apakah unit sudah ada di kontrak aktif lain
        SELECT COUNT(*) INTO existing_kontrak_count
        FROM inventory_unit iu
        JOIN kontrak k ON iu.kontrak_id = k.id
        WHERE iu.id_inventory_unit != NEW.id_inventory_unit
        AND iu.kontrak_id != NEW.kontrak_id
        AND k.status = 'Aktif'
        AND iu.id_inventory_unit = NEW.id_inventory_unit;
        
        IF existing_kontrak_count > 0 THEN
            SIGNAL SQLSTATE '45000' 
            SET MESSAGE_TEXT = 'Unit sudah di-assign ke kontrak aktif lain';
        END IF;
    END IF;
END$$
DELIMITER ;

-- ===============================================================================
-- ISSUE 5: OPTIMIZED INDEXES
-- Problem: Query performance bisa lebih baik dengan indexes yang tepat
-- Solution: Tambah composite indexes untuk query yang sering digunakan
-- ===============================================================================

-- 5.1 Indexes untuk inventory_unit
CREATE INDEX `idx_inventory_unit_status_dept` ON `inventory_unit` (`status_unit_id`, `departemen_id`);
CREATE INDEX `idx_inventory_unit_kontrak` ON `inventory_unit` (`kontrak_id`, `status_unit_id`);
CREATE INDEX `idx_inventory_unit_lokasi` ON `inventory_unit` (`lokasi_unit`, `status_unit_id`);
CREATE INDEX `idx_inventory_unit_tanggal_kirim` ON `inventory_unit` (`tanggal_kirim`, `status_unit_id`);

-- 5.2 Indexes untuk delivery_instructions
CREATE INDEX `idx_delivery_instructions_spk` ON `delivery_instructions` (`spk_id`, `status`);
CREATE INDEX `idx_delivery_instructions_tanggal` ON `delivery_instructions` (`tanggal_kirim`, `status`);

-- 5.3 Indexes untuk spk
CREATE INDEX `idx_spk_kontrak_status` ON `spk` (`kontrak_id`, `status`);
CREATE INDEX `idx_spk_delivery_plan` ON `spk` (`delivery_plan`, `status`);

-- ===============================================================================
-- ISSUE 6: STORED PROCEDURES UNTUK BUSINESS LOGIC
-- Problem: Logic bisnis tersebar di aplikasi, sulit maintain
-- Solution: Centralized business logic di stored procedures
-- ===============================================================================

-- 6.1 Procedure untuk assign unit ke kontrak
DELIMITER $$
CREATE PROCEDURE `AssignUnitToKontrak`(
    IN p_unit_id INT,
    IN p_kontrak_id INT,
    IN p_kontrak_spesifikasi_id INT,
    IN p_user_id INT
)
BEGIN
    DECLARE v_kontrak_status VARCHAR(20);
    DECLARE v_unit_status INT;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    -- Validasi kontrak masih aktif
    SELECT status INTO v_kontrak_status
    FROM kontrak WHERE id = p_kontrak_id;
    
    IF v_kontrak_status != 'Aktif' THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Kontrak tidak dalam status aktif';
    END IF;
    
    -- Validasi unit tersedia untuk rental
    SELECT status_unit_id INTO v_unit_status
    FROM inventory_unit WHERE id_inventory_unit = p_unit_id;
    
    IF v_unit_status NOT IN (6, 7, 8) THEN -- BOOKING, STOCK ASET, STOCK NON ASET
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Unit tidak tersedia untuk rental';
    END IF;
    
    -- Update unit
    UPDATE inventory_unit 
    SET 
        kontrak_id = p_kontrak_id,
        kontrak_spesifikasi_id = p_kontrak_spesifikasi_id,
        status_unit_id = 3, -- RENTAL
        updated_at = NOW()
    WHERE id_inventory_unit = p_unit_id;
    
    -- Log activity
    INSERT INTO unit_assignment_log (
        unit_id, kontrak_id, action, user_id, created_at
    ) VALUES (
        p_unit_id, p_kontrak_id, 'ASSIGN', p_user_id, NOW()
    );
    
    COMMIT;
END$$
DELIMITER ;

-- 6.2 Procedure untuk unassign unit dari kontrak
DELIMITER $$
CREATE PROCEDURE `UnassignUnitFromKontrak`(
    IN p_unit_id INT,
    IN p_user_id INT,
    IN p_reason VARCHAR(255)
)
BEGIN
    DECLARE v_kontrak_id INT;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    -- Get current kontrak_id
    SELECT kontrak_id INTO v_kontrak_id
    FROM inventory_unit WHERE id_inventory_unit = p_unit_id;
    
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
    
    -- Log activity
    INSERT INTO unit_assignment_log (
        unit_id, kontrak_id, action, user_id, note, created_at
    ) VALUES (
        p_unit_id, v_kontrak_id, 'UNASSIGN', p_user_id, p_reason, NOW()
    );
    
    COMMIT;
END$$
DELIMITER ;

-- ===============================================================================
-- ISSUE 7: AUDIT LOG TABLE
-- Problem: Tidak ada tracking perubahan data penting
-- Solution: Buat audit log table
-- ===============================================================================

-- 7.1 Table untuk audit log unit assignment
CREATE TABLE IF NOT EXISTS `unit_assignment_log` (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
    `unit_id` int UNSIGNED NOT NULL,
    `kontrak_id` int UNSIGNED DEFAULT NULL,
    `action` enum('ASSIGN','UNASSIGN','MODIFY') NOT NULL,
    `user_id` int UNSIGNED NOT NULL,
    `note` varchar(255) DEFAULT NULL,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_unit_assignment_log_unit` (`unit_id`, `created_at`),
    KEY `idx_unit_assignment_log_kontrak` (`kontrak_id`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ===============================================================================
-- ISSUE 8: DATA CLEANUP & MIGRATION
-- Problem: Data existing mungkin tidak konsisten
-- Solution: Script cleanup dan migration
-- ===============================================================================

-- 8.1 Update status unit yang sudah punya kontrak_id tapi statusnya bukan RENTAL
UPDATE inventory_unit 
SET status_unit_id = 3 -- RENTAL
WHERE kontrak_id IS NOT NULL 
AND status_unit_id != 3;

-- 8.2 Update lokasi_unit dari kontrak.pelanggan untuk unit yang sudah di-assign
UPDATE inventory_unit iu
JOIN kontrak k ON iu.kontrak_id = k.id
SET iu.lokasi_unit = k.pelanggan
WHERE iu.kontrak_id IS NOT NULL 
AND (iu.lokasi_unit IS NULL OR iu.lokasi_unit = 'Warehouse');

-- 8.3 Set harga sewa dari kontrak_spesifikasi
UPDATE inventory_unit iu
JOIN kontrak_spesifikasi ks ON iu.kontrak_spesifikasi_id = ks.id
SET 
    iu.harga_sewa_bulanan = ks.harga_per_unit_bulanan,
    iu.harga_sewa_harian = ks.harga_per_unit_harian
WHERE iu.kontrak_spesifikasi_id IS NOT NULL
AND (iu.harga_sewa_bulanan IS NULL OR iu.harga_sewa_harian IS NULL);

-- ===============================================================================
-- PERFORMANCE OPTIMIZATION QUERIES
-- Query-query optimized untuk operasi yang sering digunakan
-- ===============================================================================

-- Query untuk dashboard unit status
SELECT 
    su.nama_status,
    COUNT(*) as jumlah_unit,
    COUNT(CASE WHEN iu.kontrak_id IS NOT NULL THEN 1 END) as unit_berkontrak
FROM inventory_unit iu
LEFT JOIN status_unit su ON iu.status_unit_id = su.id
GROUP BY iu.status_unit_id, su.nama_status
ORDER BY iu.status_unit_id;

-- Query untuk units ready for rental (optimized)
SELECT 
    iu.id_inventory_unit,
    iu.no_unit,
    mu.merk_unit,
    mu.model_unit,
    iu.lokasi_unit,
    d.nama_departemen
FROM inventory_unit iu
LEFT JOIN model_unit mu ON iu.model_unit_id = mu.id
LEFT JOIN departemen d ON iu.departemen_id = d.id
WHERE iu.status_unit_id IN (6, 7, 8) -- BOOKING, STOCK ASET, STOCK NON ASET
AND iu.kontrak_id IS NULL
ORDER BY iu.no_unit;

-- Query untuk units dalam kontrak (dengan detail lengkap)
SELECT 
    iu.id_inventory_unit,
    iu.no_unit,
    k.no_kontrak,
    k.pelanggan,
    k.lokasi,
    iu.harga_sewa_bulanan,
    iu.tanggal_kirim,
    su.nama_status,
    DATEDIFF(CURDATE(), iu.tanggal_kirim) as hari_sewa
FROM inventory_unit iu
JOIN kontrak k ON iu.kontrak_id = k.id
JOIN status_unit su ON iu.status_unit_id = su.id
WHERE iu.kontrak_id IS NOT NULL
ORDER BY k.no_kontrak, iu.no_unit;

-- ===============================================================================
-- SUMMARY REKOMENDASI:
-- 1. ✅ Foreign Key Constraints - Untuk data integrity
-- 2. ✅ Triggers - Auto-update status dan business rules
-- 3. ✅ View - Simplified querying dengan joins
-- 4. ✅ Indexes - Performance optimization
-- 5. ✅ Stored Procedures - Centralized business logic
-- 6. ✅ Audit Log - Tracking changes
-- 7. ✅ Data Cleanup - Fix existing inconsistencies
-- 8. ✅ Optimized Queries - Common operations
-- ===============================================================================
