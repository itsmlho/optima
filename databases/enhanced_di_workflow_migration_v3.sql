-- ========================================
-- Enhanced DI Workflow: Contract Unit Management & Status Tracking
-- Support for TARIK/TUKAR operations with contract disconnection
-- Fixed foreign key constraints
-- ========================================

USE optima_db;

-- Add workflow tracking field to inventory_unit
ALTER TABLE `inventory_unit` 
ADD COLUMN `di_workflow_id` INT(11) NULL AFTER `delivery_instruction_id`,
ADD COLUMN `workflow_status` VARCHAR(50) NULL AFTER `di_workflow_id`,
ADD COLUMN `contract_disconnect_date` DATETIME NULL AFTER `workflow_status`,
ADD COLUMN `contract_disconnect_stage` VARCHAR(50) NULL AFTER `contract_disconnect_date`;

-- Add indexes for inventory_unit
ALTER TABLE `inventory_unit`
ADD INDEX `idx_unit_workflow` (`di_workflow_id`),
ADD INDEX `idx_unit_workflow_status` (`workflow_status`);

-- Create unit workflow log table
CREATE TABLE IF NOT EXISTS `unit_workflow_log` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `unit_id` INT(10) UNSIGNED NOT NULL,
  `di_id` INT(11) NOT NULL,
  `stage` VARCHAR(50) NOT NULL,
  `jenis_perintah` VARCHAR(20) NOT NULL,
  `old_status` VARCHAR(50) NULL,
  `new_status` VARCHAR(50) NULL,
  `notes` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT(11) NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_unit_workflow_unit` (`unit_id`),
  INDEX `idx_unit_workflow_di` (`di_id`),
  INDEX `idx_unit_workflow_stage` (`stage`),
  INDEX `idx_unit_workflow_jenis` (`jenis_perintah`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create contract disconnection log table (tracking when units are removed from contracts)
CREATE TABLE IF NOT EXISTS `contract_disconnection_log` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `kontrak_id` INT(11) NOT NULL,
  `unit_id` INT(10) UNSIGNED NOT NULL,
  `stage` VARCHAR(50) NOT NULL,
  `reason` VARCHAR(100) NULL,
  `old_kontrak_id` INT(11) NULL,
  `new_kontrak_id` INT(11) NULL,
  `disconnected_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `disconnected_by` INT(11) NULL,
  `notes` TEXT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_disconnect_kontrak` (`kontrak_id`),
  INDEX `idx_disconnect_unit` (`unit_id`),
  INDEX `idx_disconnect_stage` (`stage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create DI workflow stages table
CREATE TABLE IF NOT EXISTS `di_workflow_stages` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `di_id` INT(11) NOT NULL,
  `stage_code` VARCHAR(50) NOT NULL,
  `stage_name` VARCHAR(100) NOT NULL,
  `status` ENUM('PENDING','IN_PROGRESS','COMPLETED','SKIPPED') DEFAULT 'PENDING',
  `started_at` DATETIME NULL,
  `completed_at` DATETIME NULL,
  `notes` TEXT NULL,
  `approved_by` INT(11) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_workflow_stages_di` (`di_id`),
  INDEX `idx_workflow_stages_code` (`stage_code`),
  INDEX `idx_workflow_stages_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add replacement unit tracking for TUKAR operations
CREATE TABLE IF NOT EXISTS `unit_replacement_log` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `di_id` INT(11) NOT NULL,
  `old_unit_id` INT(10) UNSIGNED NOT NULL,
  `new_unit_id` INT(10) UNSIGNED NOT NULL,
  `kontrak_id` INT(11) NOT NULL,
  `stage` VARCHAR(50) NOT NULL,
  `replacement_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `replaced_by` INT(11) NULL,
  `notes` TEXT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_replacement_di` (`di_id`),
  INDEX `idx_replacement_old_unit` (`old_unit_id`),
  INDEX `idx_replacement_new_unit` (`new_unit_id`),
  INDEX `idx_replacement_kontrak` (`kontrak_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add foreign key constraints after tables are created
ALTER TABLE `inventory_unit` 
ADD CONSTRAINT `fk_inventory_unit_di_workflow` 
FOREIGN KEY (`di_workflow_id`) REFERENCES `delivery_instructions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `unit_workflow_log`
ADD CONSTRAINT `fk_unit_workflow_unit` 
FOREIGN KEY (`unit_id`) REFERENCES `inventory_unit` (`id_inventory_unit`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_unit_workflow_di` 
FOREIGN KEY (`di_id`) REFERENCES `delivery_instructions` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_unit_workflow_user` 
FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

ALTER TABLE `contract_disconnection_log`
ADD CONSTRAINT `fk_disconnect_kontrak` 
FOREIGN KEY (`kontrak_id`) REFERENCES `kontrak` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_disconnect_unit` 
FOREIGN KEY (`unit_id`) REFERENCES `inventory_unit` (`id_inventory_unit`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_disconnect_user` 
FOREIGN KEY (`disconnected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

ALTER TABLE `di_workflow_stages`
ADD CONSTRAINT `fk_workflow_stages_di` 
FOREIGN KEY (`di_id`) REFERENCES `delivery_instructions` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_workflow_stages_user` 
FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

ALTER TABLE `unit_replacement_log`
ADD CONSTRAINT `fk_replacement_di` 
FOREIGN KEY (`di_id`) REFERENCES `delivery_instructions` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_replacement_old_unit` 
FOREIGN KEY (`old_unit_id`) REFERENCES `inventory_unit` (`id_inventory_unit`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_replacement_new_unit` 
FOREIGN KEY (`new_unit_id`) REFERENCES `inventory_unit` (`id_inventory_unit`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_replacement_kontrak` 
FOREIGN KEY (`kontrak_id`) REFERENCES `kontrak` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_replacement_user` 
FOREIGN KEY (`replaced_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- Create view for contract unit summary
CREATE OR REPLACE VIEW `contract_unit_summary` AS
SELECT 
    k.id as kontrak_id,
    k.nomor_kontrak,
    k.pelanggan,
    k.lokasi,
    k.status as kontrak_status,
    k.tanggal_mulai,
    k.tanggal_selesai,
    COUNT(iu.id_inventory_unit) as total_units,
    COUNT(CASE WHEN iu.kontrak_id = k.id THEN 1 END) as active_units,
    COUNT(CASE WHEN iu.workflow_status LIKE '%TARIK%' THEN 1 END) as tarik_units,
    COUNT(CASE WHEN iu.workflow_status LIKE '%TUKAR%' THEN 1 END) as tukar_units,
    COUNT(CASE WHEN su.nama IN ('DISEWA', 'BEROPERASI') THEN 1 END) as operational_units,
    COUNT(CASE WHEN iu.workflow_status IS NOT NULL THEN 1 END) as workflow_units
FROM kontrak k
LEFT JOIN inventory_unit iu ON k.id = iu.kontrak_id
LEFT JOIN status_unit su ON iu.status_unit_id = su.id
GROUP BY k.id;

-- Create view for unit workflow status
CREATE OR REPLACE VIEW `unit_workflow_status` AS
SELECT 
    iu.id_inventory_unit,
    iu.no_unit,
    su.nama as current_status,
    iu.workflow_status,
    iu.di_workflow_id,
    iu.kontrak_id,
    k.nomor_kontrak,
    k.pelanggan,
    di.nomor_di,
    di.status as di_status,
    jpk.kode as jenis_perintah,
    tpk.kode as tujuan_perintah,
    CASE 
        WHEN iu.workflow_status IS NOT NULL THEN 'IN_WORKFLOW'
        WHEN su.nama IN ('DISEWA', 'BEROPERASI') THEN 'OPERATIONAL'
        WHEN su.nama IN ('TERSEDIA', 'STOCK') THEN 'AVAILABLE'
        ELSE 'OTHER'
    END as workflow_category
FROM inventory_unit iu
LEFT JOIN status_unit su ON iu.status_unit_id = su.id
LEFT JOIN kontrak k ON iu.kontrak_id = k.id
LEFT JOIN delivery_instructions di ON iu.di_workflow_id = di.id
LEFT JOIN jenis_perintah_kerja jpk ON di.jenis_perintah_kerja_id = jpk.id
LEFT JOIN tujuan_perintah_kerja tpk ON di.tujuan_perintah_kerja_id = tpk.id;

-- Create stored procedure for processing unit TARIK
DELIMITER //
CREATE PROCEDURE ProcessUnitTarik(
    IN p_unit_ids JSON,
    IN p_di_id INT,
    IN p_stage VARCHAR(50),
    IN p_user_id INT,
    OUT p_result VARCHAR(1000)
)
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_unit_id INT;
    DECLARE v_current_status VARCHAR(50);
    DECLARE v_new_status VARCHAR(50);
    DECLARE v_kontrak_id INT;
    DECLARE unit_cursor CURSOR FOR 
        SELECT id_inventory_unit, workflow_status, kontrak_id
        FROM inventory_unit 
        WHERE id_inventory_unit IN (
            SELECT JSON_UNQUOTE(JSON_EXTRACT(p_unit_ids, CONCAT('$[', idx, ']')))
            FROM (SELECT 0 AS idx UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4) t
            WHERE JSON_UNQUOTE(JSON_EXTRACT(p_unit_ids, CONCAT('$[', idx, ']'))) IS NOT NULL
        );
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_result = 'ERROR: Transaction failed';
    END;

    START TRANSACTION;

    -- Determine new status based on stage
    CASE p_stage
        WHEN 'DISETUJUI' THEN SET v_new_status = 'UNIT_AKAN_DITARIK';
        WHEN 'UNIT_DITARIK' THEN SET v_new_status = 'UNIT_SEDANG_DITARIK';
        WHEN 'SAMPAI_KANTOR' THEN SET v_new_status = 'STOCK_ASET';
        ELSE SET v_new_status = NULL;
    END CASE;

    OPEN unit_cursor;
    read_loop: LOOP
        FETCH unit_cursor INTO v_unit_id, v_current_status, v_kontrak_id;
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Update unit status if new status is defined
        IF v_new_status IS NOT NULL THEN
            UPDATE inventory_unit 
            SET workflow_status = v_new_status, 
                di_workflow_id = p_di_id,
                updated_at = NOW()
            WHERE id_inventory_unit = v_unit_id;
        END IF;

        -- Log workflow activity
        INSERT INTO unit_workflow_log (
            unit_id, di_id, stage, jenis_perintah, 
            old_status, new_status, created_by
        ) VALUES (
            v_unit_id, p_di_id, p_stage, 'TARIK',
            v_current_status, v_new_status, p_user_id
        );

        -- Handle contract disconnection for final stages
        IF p_stage IN ('SAMPAI_KANTOR', 'SELESAI') THEN
            -- Disconnect unit from contract
            UPDATE inventory_unit 
            SET kontrak_id = NULL,
                contract_disconnect_date = NOW(),
                contract_disconnect_stage = p_stage,
                updated_at = NOW()
            WHERE id_inventory_unit = v_unit_id;

            -- Log contract disconnection
            INSERT INTO contract_disconnection_log (
                kontrak_id, unit_id, stage, reason, disconnected_by
            ) VALUES (
                v_kontrak_id, v_unit_id, p_stage, 'TARIK_PROCESS', p_user_id
            );
        END IF;

    END LOOP;
    CLOSE unit_cursor;

    COMMIT;
    SET p_result = 'SUCCESS: Unit TARIK processed successfully';

END //
DELIMITER ;

-- Create trigger to auto-create workflow stages when DI is created
DELIMITER //
CREATE TRIGGER `tr_di_create_workflow_stages` 
AFTER INSERT ON `delivery_instructions`
FOR EACH ROW
BEGIN
    DECLARE v_jenis_kode VARCHAR(20);
    
    -- Get jenis perintah kode
    SELECT kode INTO v_jenis_kode 
    FROM jenis_perintah_kerja 
    WHERE id = NEW.jenis_perintah_kerja_id;
    
    -- Create workflow stages based on jenis perintah
    IF v_jenis_kode = 'TARIK' THEN
        INSERT INTO di_workflow_stages (di_id, stage_code, stage_name, status) VALUES
        (NEW.id, 'DIAJUKAN', 'DI Diajukan', 'COMPLETED'),
        (NEW.id, 'DISETUJUI', 'DI Disetujui', 'PENDING'),
        (NEW.id, 'PERSIAPAN_UNIT', 'Persiapan Tim & Transportasi', 'PENDING'),
        (NEW.id, 'DALAM_PERJALANAN', 'Dalam Perjalanan ke Lokasi', 'PENDING'),
        (NEW.id, 'UNIT_DITARIK', 'Unit Berhasil Ditarik', 'PENDING'),
        (NEW.id, 'UNIT_PULANG', 'Unit Dalam Perjalanan Pulang', 'PENDING'),
        (NEW.id, 'SAMPAI_KANTOR', 'Unit Sampai di Kantor/Workshop', 'PENDING'),
        (NEW.id, 'SELESAI', 'Proses Penarikan Selesai', 'PENDING');
    ELSEIF v_jenis_kode = 'TUKAR' THEN
        INSERT INTO di_workflow_stages (di_id, stage_code, stage_name, status) VALUES
        (NEW.id, 'DIAJUKAN', 'DI Diajukan', 'COMPLETED'),
        (NEW.id, 'DISETUJUI', 'DI Disetujui', 'PENDING'),
        (NEW.id, 'PERSIAPAN_UNIT', 'Persiapan Unit Baru & Tim', 'PENDING'),
        (NEW.id, 'DALAM_PERJALANAN', 'Dalam Perjalanan ke Lokasi', 'PENDING'),
        (NEW.id, 'UNIT_DITUKAR', 'Unit Berhasil Ditukar', 'PENDING'),
        (NEW.id, 'UNIT_LAMA_PULANG', 'Unit Lama Dalam Perjalanan Pulang', 'PENDING'),
        (NEW.id, 'SAMPAI_KANTOR', 'Unit Lama Sampai di Kantor', 'PENDING'),
        (NEW.id, 'SELESAI', 'Proses Penukaran Selesai', 'PENDING');
    END IF;
END //
DELIMITER ;

-- Add indexes for performance
CREATE INDEX `idx_delivery_instructions_workflow` ON `delivery_instructions` (`jenis_perintah_kerja_id`, `tujuan_perintah_kerja_id`, `status`);
CREATE INDEX `idx_inventory_unit_kontrak_workflow` ON `inventory_unit` (`kontrak_id`, `workflow_status`, `di_workflow_id`);

-- Insert migration record
INSERT INTO `migrations` (`table_name`, `description`, `status`, `notes`, `executed_at`) VALUES
('enhanced_di_workflow_v3', 'Enhanced DI Workflow for TARIK/TUKAR with contract management - Fixed FK constraints', 'SUCCESS', 'Added workflow tracking, contract disconnection, and status management with proper foreign keys', NOW());

-- Update completed
SELECT 'Enhanced DI Workflow Migration V3 Completed Successfully!' as Result;