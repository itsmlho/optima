-- ========================================
-- Create views and stored procedures for DI workflow
-- ========================================

USE optima_db;

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
    IN p_unit_ids TEXT,
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

    -- Process each unit (simplified - assuming comma separated IDs)
    -- In real implementation, this would parse the unit_ids properly
    IF p_unit_ids IS NOT NULL AND v_new_status IS NOT NULL THEN
        -- Update unit status
        UPDATE inventory_unit 
        SET workflow_status = v_new_status, 
            di_workflow_id = p_di_id,
            updated_at = NOW()
        WHERE FIND_IN_SET(id_inventory_unit, p_unit_ids);

        -- Handle contract disconnection for final stages
        IF p_stage IN ('SAMPAI_KANTOR', 'SELESAI') THEN
            -- Get contract info before disconnection
            INSERT INTO contract_disconnection_log (
                kontrak_id, unit_id, stage, reason, disconnected_by
            ) 
            SELECT 
                kontrak_id, id_inventory_unit, p_stage, 'TARIK_PROCESS', p_user_id
            FROM inventory_unit 
            WHERE FIND_IN_SET(id_inventory_unit, p_unit_ids) AND kontrak_id IS NOT NULL;

            -- Disconnect unit from contract
            UPDATE inventory_unit 
            SET kontrak_id = NULL,
                contract_disconnect_date = NOW(),
                contract_disconnect_stage = p_stage,
                updated_at = NOW()
            WHERE FIND_IN_SET(id_inventory_unit, p_unit_ids);
        END IF;
    END IF;

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

SELECT 'Views, procedures, and triggers created successfully!' as Result;