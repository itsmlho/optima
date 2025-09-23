-- ========================================
-- Create views and stored procedures for DI workflow (Corrected)
-- ========================================

USE optima_db;

-- Create view for contract unit summary
CREATE OR REPLACE VIEW `contract_unit_summary` AS
SELECT 
    k.id as kontrak_id,
    k.no_kontrak,
    k.pelanggan,
    k.lokasi,
    k.status as kontrak_status,
    k.tanggal_mulai,
    k.tanggal_berakhir,
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
    k.no_kontrak,
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

SELECT 'Views created successfully!' as Result;