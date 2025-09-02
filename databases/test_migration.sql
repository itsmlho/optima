-- Script untuk testing konsolidasi komponen
-- Jalankan setelah migration untuk memverifikasi data integrity

-- Test 1: Check migration log
SELECT '=== MIGRATION LOG ===' as test_section;
SELECT * FROM migration_log
WHERE migration_name = 'consolidate_components_to_inventory_attachment'
ORDER BY executed_at DESC;

-- Test 2: Check data distribution
SELECT '=== DATA DISTRIBUTION ===' as test_section;
SELECT
    tipe_item,
    status_unit,
    COUNT(*) as jumlah
FROM inventory_attachment
WHERE tipe_item IN ('battery', 'charger', 'attachment')
GROUP BY tipe_item, status_unit
ORDER BY tipe_item, status_unit;

-- Test 3: Check unit-component relationships
SELECT '=== UNIT-COMPONENT RELATIONSHIPS ===' as test_section;
SELECT
    iu.id_inventory_unit,
    iu.no_unit,
    COUNT(CASE WHEN ia.tipe_item = 'battery' THEN 1 END) as battery_count,
    COUNT(CASE WHEN ia.tipe_item = 'charger' THEN 1 END) as charger_count,
    COUNT(CASE WHEN ia.tipe_item = 'attachment' THEN 1 END) as attachment_count
FROM inventory_unit iu
LEFT JOIN inventory_attachment ia ON iu.id_inventory_unit = ia.id_inventory_unit
    AND ia.status_unit = 8
    AND ia.tipe_item IN ('battery', 'charger', 'attachment')
GROUP BY iu.id_inventory_unit, iu.no_unit
HAVING battery_count > 0 OR charger_count > 0 OR attachment_count > 0
ORDER BY iu.id_inventory_unit;

-- Test 4: Check SPK specifications update
SELECT '=== SPK SPECIFICATIONS CHECK ===' as test_section;
SELECT
    id,
    nomor_spk,
    JSON_EXTRACT(spesifikasi, '$.persiapan_battery_inventory_id') as battery_inv_id,
    JSON_EXTRACT(spesifikasi, '$.persiapan_charger_inventory_id') as charger_inv_id,
    JSON_EXTRACT(spesifikasi, '$.fabrikasi_attachment_inventory_id') as attachment_inv_id
FROM spk
WHERE spesifikasi IS NOT NULL
    AND (JSON_EXTRACT(spesifikasi, '$.persiapan_battery_inventory_id') IS NOT NULL
         OR JSON_EXTRACT(spesifikasi, '$.persiapan_charger_inventory_id') IS NOT NULL
         OR JSON_EXTRACT(spesifikasi, '$.fabrikasi_attachment_inventory_id') IS NOT NULL)
ORDER BY id DESC
LIMIT 10;

-- Test 5: Check for orphaned records
SELECT '=== ORPHANED RECORDS CHECK ===' as test_section;

-- Check inventory_attachment pointing to non-existent units
SELECT COUNT(*) as orphaned_attachments
FROM inventory_attachment ia
LEFT JOIN inventory_unit iu ON ia.id_inventory_unit = iu.id_inventory_unit
WHERE ia.id_inventory_unit IS NOT NULL
    AND iu.id_inventory_unit IS NULL;

-- Check units with status 8 but no corresponding inventory_attachment
SELECT COUNT(*) as missing_inventory_links
FROM inventory_unit iu
WHERE iu.status_unit_id = 8
    AND NOT EXISTS (
        SELECT 1 FROM inventory_attachment ia
        WHERE ia.id_inventory_unit = iu.id_inventory_unit
            AND ia.status_unit = 8
            AND ia.tipe_item IN ('battery', 'charger', 'attachment')
    );

-- Test 6: Check view functionality
SELECT '=== VIEW FUNCTIONALITY TEST ===' as test_section;
SELECT * FROM inventory_unit_components LIMIT 5;

-- Test 7: Check helper functions
SELECT '=== HELPER FUNCTIONS TEST ===' as test_section;

-- Test battery function
SELECT
    iu.id_inventory_unit,
    iu.no_unit,
    get_unit_battery_info(iu.id_inventory_unit) as battery_info
FROM inventory_unit iu
WHERE iu.departemen_id = 2 -- Electric units
    AND EXISTS (
        SELECT 1 FROM inventory_attachment ia
        WHERE ia.id_inventory_unit = iu.id_inventory_unit
            AND ia.tipe_item = 'battery'
            AND ia.status_unit = 8
    )
LIMIT 3;

-- Test charger function
SELECT
    iu.id_inventory_unit,
    iu.no_unit,
    get_unit_charger_info(iu.id_inventory_unit) as charger_info
FROM inventory_unit iu
WHERE iu.departemen_id = 2 -- Electric units
    AND EXISTS (
        SELECT 1 FROM inventory_attachment ia
        WHERE ia.id_inventory_unit = iu.id_inventory_unit
            AND ia.tipe_item = 'charger'
            AND ia.status_unit = 8
    )
LIMIT 3;

-- Test 8: Performance check - query time comparison
SELECT '=== PERFORMANCE CHECK ===' as test_section;

-- Old way (would require multiple joins)
EXPLAIN SELECT
    iu.id_inventory_unit,
    iu.no_unit,
    b.merk_baterai,
    b.tipe_baterai,
    c.merk_charger,
    c.tipe_charger,
    a.tipe as attachment_tipe,
    a.merk as attachment_merk
FROM inventory_unit iu
LEFT JOIN baterai b ON iu.model_baterai_id = b.id
LEFT JOIN charger c ON iu.model_charger_id = c.id_charger
LEFT JOIN attachment a ON iu.model_attachment_id = a.id_attachment
WHERE iu.id_inventory_unit = 1;

-- New way (single table)
EXPLAIN SELECT * FROM inventory_unit_components WHERE id_inventory_unit = 1;

-- Test 9: Summary report
SELECT '=== MIGRATION SUMMARY ===' as test_section;

SELECT
    'Total Units' as metric,
    COUNT(*) as value
FROM inventory_unit
UNION ALL
SELECT
    'Units with Components' as metric,
    COUNT(DISTINCT iu.id_inventory_unit) as value
FROM inventory_unit iu
JOIN inventory_attachment ia ON iu.id_inventory_unit = ia.id_inventory_unit
    AND ia.status_unit = 8
    AND ia.tipe_item IN ('battery', 'charger', 'attachment')
UNION ALL
SELECT
    'Total Components' as metric,
    COUNT(*) as value
FROM inventory_attachment
WHERE tipe_item IN ('battery', 'charger', 'attachment')
UNION ALL
SELECT
    'Active Components' as metric,
    COUNT(*) as value
FROM inventory_attachment
WHERE tipe_item IN ('battery', 'charger', 'attachment')
    AND status_unit = 8
UNION ALL
SELECT
    'Available Components' as metric,
    COUNT(*) as value
FROM inventory_attachment
WHERE tipe_item IN ('battery', 'charger', 'attachment')
    AND status_unit = 7;

SELECT '=== TESTING COMPLETE ===' as test_section;
SELECT 'Jika semua query di atas berjalan tanpa error, migration berhasil!' as message;
