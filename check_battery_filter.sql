-- ============================================================================
-- BATTERY FILTER INVESTIGATION
-- ============================================================================
-- Untuk melihat struktur data battery dan kenapa filter tidak bekerja
-- ============================================================================

USE optima_ci;

-- 1. STRUKTUR TABEL inventory_attachment
SELECT '========== 1. STRUKTUR TABEL inventory_attachment ==========' AS section;
DESCRIBE inventory_attachment;

-- 2. STRUKTUR TABEL baterai
SELECT '========== 2. STRUKTUR TABEL baterai ==========' AS section;
DESCRIBE baterai;

-- 3. STATISTIK DATA BATTERY
SELECT '========== 3. STATISTIK DATA BATTERY ==========' AS section;
SELECT 
    COUNT(*) as total_battery_records,
    SUM(CASE WHEN baterai_id IS NULL THEN 1 ELSE 0 END) as null_baterai_id,
    SUM(CASE WHEN baterai_id IS NOT NULL THEN 1 ELSE 0 END) as has_baterai_id,
    SUM(CASE WHEN sn_baterai IS NULL THEN 1 ELSE 0 END) as null_sn_baterai,
    SUM(CASE WHEN sn_baterai IS NOT NULL THEN 1 ELSE 0 END) as has_sn_baterai
FROM inventory_attachment 
WHERE LOWER(tipe_item) = 'battery';

-- 4. SAMPLE DATA BATTERY (10 records)
SELECT '========== 4. SAMPLE DATA BATTERY (dengan JOIN ke baterai) ==========' AS section;
SELECT 
    ia.id_inventory_attachment,
    ia.tipe_item,
    ia.baterai_id,
    ia.sn_baterai,
    ia.attachment_id,
    b.id as baterai_table_id,
    b.merk_baterai,
    b.tipe_baterai,
    b.jenis_baterai,
    a.merk as attachment_merk,
    a.tipe as attachment_tipe,
    a.model as attachment_model
FROM inventory_attachment ia
LEFT JOIN baterai b ON ia.baterai_id = b.id
LEFT JOIN attachment a ON ia.attachment_id = a.id_attachment
WHERE LOWER(ia.tipe_item) = 'battery'
LIMIT 10;

-- 5. CEK SEMUA NILAI jenis_baterai DI TABEL baterai
SELECT '========== 5. SEMUA NILAI jenis_baterai DI TABEL baterai ==========' AS section;
SELECT 
    jenis_baterai,
    COUNT(*) as jumlah,
    GROUP_CONCAT(DISTINCT merk_baterai SEPARATOR ', ') as sample_merk
FROM baterai
WHERE jenis_baterai IS NOT NULL
GROUP BY jenis_baterai
ORDER BY COUNT(*) DESC;

-- 6. CEK DATA ATTACHMENT untuk battery (kolom model dan tipe)
SELECT '========== 6. DATA ATTACHMENT untuk battery ==========' AS section;
SELECT 
    a.model,
    a.tipe,
    COUNT(*) as jumlah
FROM inventory_attachment ia
LEFT JOIN attachment a ON ia.attachment_id = a.id_attachment
WHERE LOWER(ia.tipe_item) = 'battery'
AND ia.attachment_id IS NOT NULL
GROUP BY a.model, a.tipe
LIMIT 20;

-- 7. TEST QUERY: Cari battery dengan LITHIUM (seperti di filter)
SELECT '========== 7. TEST QUERY: Battery dengan LITHIUM ==========' AS section;
SELECT COUNT(*) as count_lithium
FROM inventory_attachment ia
LEFT JOIN baterai b ON ia.baterai_id = b.id
WHERE LOWER(ia.tipe_item) = 'battery'
AND (
    (ia.baterai_id IS NOT NULL 
     AND b.jenis_baterai IS NOT NULL 
     AND UPPER(b.jenis_baterai) LIKE '%LITHIUM%')
);

-- 8. TEST QUERY: Cari battery dengan LEAD (seperti di filter)
SELECT '========== 8. TEST QUERY: Battery dengan LEAD ==========' AS section;
SELECT COUNT(*) as count_lead
FROM inventory_attachment ia
LEFT JOIN baterai b ON ia.baterai_id = b.id
WHERE LOWER(ia.tipe_item) = 'battery'
AND (
    (ia.baterai_id IS NOT NULL 
     AND b.jenis_baterai IS NOT NULL 
     AND UPPER(b.jenis_baterai) LIKE '%LEAD%')
);

-- 9. SAMPLE 5 BATTERY RECORDS dengan semua kolom
SELECT '========== 9. SAMPLE 5 BATTERY (semua detail) ==========' AS section;
SELECT 
    ia.*,
    b.merk_baterai,
    b.tipe_baterai,
    b.jenis_baterai,
    a.merk as att_merk,
    a.tipe as att_tipe,
    a.model as att_model
FROM inventory_attachment ia
LEFT JOIN baterai b ON ia.baterai_id = b.id
LEFT JOIN attachment a ON ia.attachment_id = a.id_attachment
WHERE LOWER(ia.tipe_item) = 'battery'
LIMIT 5;

-- 10. CEK apakah kolom tipe_item benar-benar 'battery' atau 'BATTERY'
SELECT '========== 10. DISTINCT NILAI tipe_item ==========' AS section;
SELECT 
    tipe_item,
    COUNT(*) as jumlah
FROM inventory_attachment
GROUP BY tipe_item;
