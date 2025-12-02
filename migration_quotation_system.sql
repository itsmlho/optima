-- ===================================
-- OPTIMA QUOTATION SYSTEM MIGRATION
-- From: kontrak_spesifikasi
-- To: quotation_specifications  
-- ===================================

USE optima_ci;

SET foreign_key_checks = 0;

-- STEP 1: Backup existing data
CREATE TABLE IF NOT EXISTS migration_backup_kontrak AS SELECT * FROM kontrak;
CREATE TABLE IF NOT EXISTS migration_backup_kontrak_spesifikasi AS SELECT * FROM kontrak_spesifikasi;

-- STEP 2: Enhance quotation_specifications table to accommodate kontrak_spesifikasi data
ALTER TABLE quotation_specifications 
ADD COLUMN IF NOT EXISTS spek_kode VARCHAR(50) AFTER specification_name,
ADD COLUMN IF NOT EXISTS jumlah_tersedia INT DEFAULT 0 AFTER quantity,
ADD COLUMN IF NOT EXISTS harga_per_unit_harian DECIMAL(15,2) AFTER unit_price,
ADD COLUMN IF NOT EXISTS departemen_id INT AFTER category,
ADD COLUMN IF NOT EXISTS tipe_unit_id INT AFTER departemen_id, 
ADD COLUMN IF NOT EXISTS kapasitas_id INT AFTER tipe_unit_id,
ADD COLUMN IF NOT EXISTS charger_id INT AFTER kapasitas_id,
ADD COLUMN IF NOT EXISTS mast_id INT AFTER charger_id,
ADD COLUMN IF NOT EXISTS ban_id INT AFTER mast_id,
ADD COLUMN IF NOT EXISTS roda_id INT AFTER ban_id,
ADD COLUMN IF NOT EXISTS valve_id INT AFTER roda_id,
ADD COLUMN IF NOT EXISTS jenis_baterai VARCHAR(100) AFTER valve_id,
ADD COLUMN IF NOT EXISTS attachment_tipe VARCHAR(100) AFTER jenis_baterai,
ADD COLUMN IF NOT EXISTS attachment_merk VARCHAR(100) AFTER attachment_tipe,
ADD COLUMN IF NOT EXISTS original_kontrak_id INT UNSIGNED AFTER id_quotation,
ADD COLUMN IF NOT EXISTS original_kontrak_spek_id INT UNSIGNED AFTER original_kontrak_id,
ADD INDEX IF NOT EXISTS idx_original_kontrak (original_kontrak_id),
ADD INDEX IF NOT EXISTS idx_original_kontrak_spek (original_kontrak_spek_id);

-- STEP 3: Create quotations from existing kontrak (reverse engineering)
INSERT INTO quotations (
    quotation_number,
    prospect_name, 
    prospect_contact_person,
    prospect_phone,
    prospect_email,
    prospect_address,
    prospect_city,
    quotation_title,
    quotation_description,
    quotation_date,
    valid_until,
    currency,
    total_amount,
    stage,
    probability_percent,
    is_deal,
    deal_date,
    created_customer_id,
    created_contract_id,
    created_by,
    created_at,
    updated_at
)
SELECT 
    CONCAT('QUO-MIG-', k.id) as quotation_number,
    COALESCE(c.customer_name, 'Migrated Customer') as prospect_name,
    COALESCE(cl.contact_person, '') as prospect_contact_person, 
    COALESCE(cl.phone, '') as prospect_phone,
    COALESCE(cl.email, '') as prospect_email,
    COALESCE(cl.address, '') as prospect_address,
    COALESCE(cl.city, '') as prospect_city,
    CONCAT('Contract ', k.no_kontrak) as quotation_title,
    CONCAT('Migrated from contract system - Type: ', k.jenis_sewa) as quotation_description,
    k.tanggal_mulai as quotation_date,
    k.tanggal_berakhir as valid_until,
    'IDR' as currency,
    k.nilai_total as total_amount,
    CASE 
        WHEN k.status = 'Aktif' THEN 'ACCEPTED'
        WHEN k.status = 'Pending' THEN 'SENT' 
        WHEN k.status = 'Dibatalkan' THEN 'REJECTED'
        ELSE 'ACCEPTED'
    END as stage,
    100 as probability_percent,
    1 as is_deal,
    k.tanggal_mulai as deal_date,
    cl.customer_id as created_customer_id,
    k.id as created_contract_id,
    COALESCE(k.dibuat_oleh, 1) as created_by,
    COALESCE(k.dibuat_pada, NOW()) as created_at,
    COALESCE(k.diperbarui_pada, NOW()) as updated_at
FROM kontrak k
LEFT JOIN customer_locations cl ON k.customer_location_id = cl.id  
LEFT JOIN customers c ON cl.customer_id = c.id
WHERE NOT EXISTS (
    SELECT 1 FROM quotations q WHERE q.created_contract_id = k.id
);

-- STEP 4: Migrate kontrak_spesifikasi to quotation_specifications
INSERT INTO quotation_specifications (
    id_quotation,
    original_kontrak_id,
    original_kontrak_spek_id,
    spek_kode,
    specification_name,
    specification_description,
    category,
    quantity,
    jumlah_tersedia,
    unit,
    unit_price,
    harga_per_unit_harian,
    total_price,
    equipment_type,
    brand,
    model,
    specifications,
    rental_duration,
    rental_rate_type,
    delivery_required,
    installation_required,
    maintenance_included,
    warranty_period,
    notes,
    departemen_id,
    tipe_unit_id,
    kapasitas_id,
    charger_id,
    mast_id,
    ban_id,
    roda_id,
    valve_id,
    jenis_baterai,
    attachment_tipe,
    attachment_merk,
    sort_order,
    is_active,
    created_at,
    updated_at
)
SELECT 
    q.id_quotation,
    ks.kontrak_id as original_kontrak_id,
    ks.id as original_kontrak_spek_id,
    ks.spek_kode,
    COALESCE(ks.spek_kode, 'Migrated Specification') as specification_name,
    ks.catatan_spek as specification_description,
    CONCAT(
        COALESCE(d.nama_departemen, 'Unknown'),
        ' - ',
        COALESCE(tu.nama_unit, 'Unknown'),
        ' - ',
        COALESCE(kap.kapasitas, 'Unknown')
    ) as category,
    ks.jumlah_dibutuhkan as quantity,
    ks.jumlah_tersedia,
    'unit' as unit,
    ks.harga_per_unit_bulanan as unit_price,
    ks.harga_per_unit_harian,
    (ks.jumlah_dibutuhkan * ks.harga_per_unit_bulanan) as total_price,
    COALESCE(ks.tipe_jenis, 'Equipment') as equipment_type,
    ks.merk_unit as brand,
    ks.model_unit as model,
    JSON_OBJECT(
        'attachment_type', ks.attachment_tipe,
        'attachment_brand', ks.attachment_merk,
        'battery_type', ks.jenis_baterai,
        'charger_id', ks.charger_id,
        'mast_id', ks.mast_id, 
        'ban_id', ks.ban_id,
        'roda_id', ks.roda_id,
        'valve_id', ks.valve_id,
        'accessories', ks.aksesoris
    ) as specifications,
    DATEDIFF(k.tanggal_berakhir, k.tanggal_mulai) as rental_duration,
    CASE k.jenis_sewa 
        WHEN 'BULANAN' THEN 'MONTHLY'
        WHEN 'HARIAN' THEN 'DAILY'
        ELSE 'MONTHLY'
    END as rental_rate_type,
    1 as delivery_required,
    1 as installation_required, 
    1 as maintenance_included,
    12 as warranty_period,
    ks.catatan_spek as notes,
    ks.departemen_id,
    ks.tipe_unit_id,
    ks.kapasitas_id,
    ks.charger_id,
    ks.mast_id,
    ks.ban_id,
    ks.roda_id,
    ks.valve_id,
    ks.jenis_baterai,
    ks.attachment_tipe,
    ks.attachment_merk,
    ks.id as sort_order,
    1 as is_active,
    COALESCE(ks.dibuat_pada, NOW()) as created_at,
    COALESCE(ks.diperbarui_pada, NOW()) as updated_at
FROM kontrak_spesifikasi ks
JOIN kontrak k ON ks.kontrak_id = k.id
JOIN quotations q ON q.created_contract_id = k.id
LEFT JOIN departemen d ON ks.departemen_id = d.id
LEFT JOIN tipe_unit tu ON ks.tipe_unit_id = tu.id  
LEFT JOIN kapasitas kap ON ks.kapasitas_id = kap.id;

-- STEP 5: Update SPK references to work with new system
-- SPK table already has kontrak_id and kontrak_spesifikasi_id, so it will work with both systems

-- STEP 6: Create views for backward compatibility
CREATE OR REPLACE VIEW v_kontrak_spesifikasi_legacy AS
SELECT 
    qs.original_kontrak_spek_id as id,
    qs.original_kontrak_id as kontrak_id,
    qs.spek_kode,
    qs.quantity as jumlah_dibutuhkan,
    qs.jumlah_tersedia,
    qs.unit_price as harga_per_unit_bulanan,
    qs.harga_per_unit_harian,
    qs.notes as catatan_spek,
    qs.departemen_id,
    qs.tipe_unit_id,
    qs.equipment_type as tipe_jenis,
    qs.kapasitas_id,
    qs.brand as merk_unit,
    qs.model,
    qs.attachment_tipe,
    qs.attachment_merk,
    qs.jenis_baterai,
    qs.charger_id,
    qs.mast_id,
    qs.ban_id,
    qs.roda_id,
    qs.valve_id,
    JSON_UNQUOTE(JSON_EXTRACT(qs.specifications, '$.accessories')) as aksesoris,
    qs.created_at as dibuat_pada,
    qs.updated_at as diperbarui_pada
FROM quotation_specifications qs
WHERE qs.original_kontrak_spek_id IS NOT NULL;

-- STEP 7: Create summary view for reporting
CREATE OR REPLACE VIEW v_quotation_migration_summary AS
SELECT 
    'Total Quotations Created' as metric,
    COUNT(*) as value
FROM quotations 
WHERE quotation_number LIKE 'QUO-MIG-%'
UNION ALL
SELECT 
    'Total Specifications Migrated' as metric,
    COUNT(*) as value  
FROM quotation_specifications
WHERE original_kontrak_spek_id IS NOT NULL
UNION ALL
SELECT 
    'Original Kontrak Records' as metric,
    COUNT(*) as value
FROM migration_backup_kontrak
UNION ALL  
SELECT
    'Original Kontrak_Spesifikasi Records' as metric,
    COUNT(*) as value
FROM migration_backup_kontrak_spesifikasi;

SET foreign_key_checks = 1;

-- Show migration summary
SELECT * FROM v_quotation_migration_summary;

-- Show sample migrated data
SELECT 
    q.quotation_number,
    q.prospect_name,
    q.stage,
    q.total_amount,
    COUNT(qs.id_specification) as specification_count
FROM quotations q
LEFT JOIN quotation_specifications qs ON q.id_quotation = qs.id_quotation  
WHERE q.quotation_number LIKE 'QUO-MIG-%'
GROUP BY q.id_quotation
LIMIT 5;

COMMIT;