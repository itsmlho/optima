-- ============================================================================
-- PRODUCTION: Quick Post-Migration Verification
-- ============================================================================
-- Database: u138256737_optima_db
-- Purpose: Quick health check after migration
-- Usage: Run this immediately after migration to verify success
-- ============================================================================

USE u138256737_optima_db;

-- ============================================================================
-- DATABASE IDENTITY CHECK
-- ============================================================================

SELECT '╔════════════════════════════════════════╗' as border;
SELECT '║   POST-MIGRATION VERIFICATION REPORT   ║' as title;
SELECT '╚════════════════════════════════════════╝' as border;

SELECT 
    DATABASE() as database_name,
    NOW() as verification_time,
    VERSION() as mysql_version;

-- ============================================================================
-- CHECK 1: inventory_unit - New Columns
-- ============================================================================

SELECT '========================================' as separator;
SELECT '✓ CHECK 1: inventory_unit new columns' as check_name;
SELECT '========================================' as separator;

SELECT 
    COLUMN_NAME,
    COLUMN_TYPE,
    IS_NULLABLE,
    IF(COLUMN_NAME IN ('on_hire_date', 'off_hire_date', 'rate_changed_at'), '✓ FOUND', '✗ MISSING') as status
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'u138256737_optima_db'
  AND TABLE_NAME = 'inventory_unit'
  AND COLUMN_NAME IN ('on_hire_date', 'off_hire_date', 'rate_changed_at')
ORDER BY ORDINAL_POSITION;

-- Summary
SELECT 
    CASE 
        WHEN COUNT(*) = 3 THEN '✓ PASS: All 3 columns exist'
        ELSE '✗ FAIL: Missing columns!'
    END as result
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'u138256737_optima_db'
  AND TABLE_NAME = 'inventory_unit'
  AND COLUMN_NAME IN ('on_hire_date', 'off_hire_date', 'rate_changed_at');

-- ============================================================================
-- CHECK 2: inventory_attachment - Old Columns Removed
-- ============================================================================

SELECT '========================================' as separator;
SELECT '✓ CHECK 2: inventory_attachment old columns removed' as check_name;
SELECT '========================================' as separator;

SELECT 
    CASE 
        WHEN COUNT(*) = 0 THEN '✓ PASS: Old columns successfully removed'
        ELSE CONCAT('✗ FAIL: ', COUNT(*), ' old columns still exist!')
    END as result
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'u138256737_optima_db'
  AND TABLE_NAME = 'inventory_attachment'
  AND COLUMN_NAME IN ('status_unit', 'status_attachment_id');

-- ============================================================================
-- CHECK 3: inventory_attachment - New Indexes
-- ============================================================================

SELECT '========================================' as separator;
SELECT '✓ CHECK 3: inventory_attachment indexes' as check_name;
SELECT '========================================' as separator;

SELECT 
    INDEX_NAME,
    COLUMN_NAME,
    CASE NON_UNIQUE 
        WHEN 0 THEN '✓ UNIQUE' 
        WHEN 1 THEN '✓ INDEX' 
    END as type,
    '✓ EXISTS' as status
FROM INFORMATION_SCHEMA.STATISTICS
WHERE TABLE_SCHEMA = 'u138256737_optima_db'
  AND TABLE_NAME = 'inventory_attachment'
  AND INDEX_NAME IN (
    'idx_inventory_attachment_status', 
    'uk_unit_attachment', 
    'uk_unit_charger', 
    'uk_unit_battery'
  )
GROUP BY INDEX_NAME, COLUMN_NAME, NON_UNIQUE
ORDER BY INDEX_NAME;

-- Summary
SELECT 
    CASE 
        WHEN COUNT(DISTINCT INDEX_NAME) = 4 THEN '✓ PASS: All 4 indexes created'
        ELSE CONCAT('✗ FAIL: Only ', COUNT(DISTINCT INDEX_NAME), ' of 4 indexes exist!')
    END as result
FROM INFORMATION_SCHEMA.STATISTICS
WHERE TABLE_SCHEMA = 'u138256737_optima_db'
  AND TABLE_NAME = 'inventory_attachment'
  AND INDEX_NAME IN (
    'idx_inventory_attachment_status', 
    'uk_unit_attachment', 
    'uk_unit_charger', 
    'uk_unit_battery'
  );

-- ============================================================================
-- CHECK 4: Table Integrity - Row Counts
-- ============================================================================

SELECT '========================================' as separator;
SELECT '✓ CHECK 4: Table integrity check' as check_name;
SELECT '========================================' as separator;

SELECT 
    'inventory_unit' as table_name,
    COUNT(*) as current_rows,
    (SELECT COUNT(*) FROM inventory_unit_backup_20260214) as backup_rows,
    CASE 
        WHEN COUNT(*) = (SELECT COUNT(*) FROM inventory_unit_backup_20260214) 
        THEN '✓ MATCH' 
        ELSE '✗ MISMATCH!' 
    END as status
FROM inventory_unit
UNION ALL
SELECT 
    'inventory_attachment',
    COUNT(*),
    (SELECT COUNT(*) FROM inventory_attachment_backup_20260214),
    CASE 
        WHEN COUNT(*) <= (SELECT COUNT(*) FROM inventory_attachment_backup_20260214) 
        THEN '✓ OK (duplicates cleaned)' 
        ELSE '✗ ERROR: More rows than backup!' 
    END
FROM inventory_attachment;

-- ============================================================================
-- CHECK 5: Foreign Keys Integrity
-- ============================================================================

SELECT '========================================' as separator;
SELECT '✓ CHECK 5: Foreign key integrity' as check_name;
SELECT '========================================' as separator;

-- Count orphaned records (should be 0)
SELECT 
    'Orphaned inventory_attachment → inventory_unit' as check_type,
    COUNT(*) as orphaned_count,
    CASE 
        WHEN COUNT(*) = 0 THEN '✓ PASS: No orphans'
        ELSE CONCAT('⚠ WARNING: ', COUNT(*), ' orphaned records')
    END as status
FROM inventory_attachment ia
LEFT JOIN inventory_unit iu ON ia.id_inventory_unit = iu.id_inventory_unit
WHERE ia.id_inventory_unit IS NOT NULL 
  AND iu.id_inventory_unit IS NULL;

-- ============================================================================
-- CHECK 6: Duplicate Check (Should be 0 after cleanup)
-- ============================================================================

SELECT '========================================' as separator;
SELECT '✓ CHECK 6: Duplicate attachment check' as check_name;
SELECT '========================================' as separator;

-- Check for any remaining duplicates
SELECT 
    COUNT(*) as duplicate_groups,
    CASE 
        WHEN COUNT(*) = 0 THEN '✓ PASS: No duplicates found'
        ELSE CONCAT('✗ FAIL: ', COUNT(*), ' duplicate groups exist!')
    END as status
FROM (
    SELECT 
        id_inventory_unit,
        tipe_item,
        CASE tipe_item
            WHEN 'BATTERY' THEN baterai_id
            WHEN 'CHARGER' THEN charger_id
            WHEN 'ATTACHMENT' THEN attachment_id
        END as item_id,
        COUNT(*) as dup_count
    FROM inventory_attachment
    WHERE id_inventory_unit IS NOT NULL
    GROUP BY 
        id_inventory_unit, 
        tipe_item,
        CASE tipe_item
            WHEN 'BATTERY' THEN baterai_id
            WHEN 'CHARGER' THEN charger_id
            WHEN 'ATTACHMENT' THEN attachment_id
        END
    HAVING COUNT(*) > 1
) duplicates;

-- ============================================================================
-- CHECK 7: Application Critical Tables
-- ============================================================================

SELECT '========================================' as separator;
SELECT '✓ CHECK 7: Critical dependencies' as check_name;
SELECT '========================================' as separator;

-- Verify related tables are accessible
SELECT 
    'kontrak' as related_table,
    COUNT(*) as row_count,
    '✓ OK' as status
FROM kontrak
UNION ALL
SELECT 
    'unit',
    COUNT(*),
    '✓ OK'
FROM unit
UNION ALL
SELECT 
    'customer',
    COUNT(*),
    '✓ OK'
FROM customer;

-- ============================================================================
-- OVERALL MIGRATION STATUS
-- ============================================================================

SELECT '╔════════════════════════════════════════╗' as border;
SELECT '║      OVERALL MIGRATION STATUS          ║' as title;
SELECT '╚════════════════════════════════════════╝' as border;

SELECT 
    CASE 
        WHEN (
            -- All inventory_unit columns exist
            (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
             WHERE TABLE_SCHEMA = 'u138256737_optima_db' 
             AND TABLE_NAME = 'inventory_unit' 
             AND COLUMN_NAME IN ('on_hire_date', 'off_hire_date', 'rate_changed_at')) = 3
            AND
            -- No old inventory_attachment columns
            (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
             WHERE TABLE_SCHEMA = 'u138256737_optima_db' 
             AND TABLE_NAME = 'inventory_attachment' 
             AND COLUMN_NAME IN ('status_unit', 'status_attachment_id')) = 0
            AND
            -- All indexes exist
            (SELECT COUNT(DISTINCT INDEX_NAME) FROM INFORMATION_SCHEMA.STATISTICS 
             WHERE TABLE_SCHEMA = 'u138256737_optima_db' 
             AND TABLE_NAME = 'inventory_attachment' 
             AND INDEX_NAME IN ('idx_inventory_attachment_status', 'uk_unit_attachment', 'uk_unit_charger', 'uk_unit_battery')) = 4
        ) THEN '✓✓✓ MIGRATION SUCCESSFUL ✓✓✓'
        ELSE '✗✗✗ MIGRATION INCOMPLETE - CHECK ERRORS ABOVE ✗✗✗'
    END as final_status;

-- ============================================================================
-- NEXT STEPS
-- ============================================================================

SELECT '========================================' as separator;
SELECT 'NEXT STEPS:' as section;
SELECT '========================================' as separator;

SELECT '1. Test application critical pages' as step, 'https://optima.sml.co.id/warehouse/inventory/invent_unit' as url
UNION ALL SELECT '2. Check browser console for errors', 'Press F12 in browser'
UNION ALL SELECT '3. Monitor application logs', 'Check writable/logs/ directory'
UNION ALL SELECT '4. Keep backup tables for 30 days', 'DROP after verification period'
UNION ALL SELECT '5. Document any issues found', 'Report to development team';

SELECT '========================================' as separator;
SELECT NOW() as verification_completed_at;
SELECT '========================================' as separator;
