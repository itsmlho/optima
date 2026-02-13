-- ============================================================================
-- PRODUCTION: Cleanup Duplicate Attachments
-- ============================================================================
-- Date: February 14, 2026
-- Database: u138256737_optima_db
-- Purpose: Clean up duplicate attachments BEFORE adding UNIQUE constraints
-- IMPORTANT: Run this BEFORE the main migration script
-- ============================================================================

USE u138256737_optima_db;

-- ============================================================================
-- STEP 1: IDENTIFY DUPLICATES
-- ============================================================================

SELECT '========================================' as separator;
SELECT 'STEP 1: Checking for duplicates...' as step;
SELECT '========================================' as separator;

-- Check for duplicate BATTERY records
SELECT 
    'BATTERY DUPLICATES' as type,
    id_inventory_unit,
    baterai_id,
    COUNT(*) as duplicate_count,
    GROUP_CONCAT(id_inventory_attachment ORDER BY id_inventory_attachment) as attachment_ids
FROM inventory_attachment
WHERE id_inventory_unit IS NOT NULL 
  AND tipe_item = 'BATTERY'
  AND baterai_id IS NOT NULL
GROUP BY id_inventory_unit, baterai_id
HAVING COUNT(*) > 1;

-- Check for duplicate CHARGER records
SELECT 
    'CHARGER DUPLICATES' as type,
    id_inventory_unit,
    charger_id,
    COUNT(*) as duplicate_count,
    GROUP_CONCAT(id_inventory_attachment ORDER BY id_inventory_attachment) as attachment_ids
FROM inventory_attachment
WHERE id_inventory_unit IS NOT NULL 
  AND tipe_item = 'CHARGER'
  AND charger_id IS NOT NULL
GROUP BY id_inventory_unit, charger_id
HAVING COUNT(*) > 1;

-- Check for duplicate ATTACHMENT records
SELECT 
    'ATTACHMENT DUPLICATES' as type,
    id_inventory_unit,
    attachment_id,
    COUNT(*) as duplicate_count,
    GROUP_CONCAT(id_inventory_attachment ORDER BY id_inventory_attachment) as attachment_ids
FROM inventory_attachment
WHERE id_inventory_unit IS NOT NULL 
  AND tipe_item = 'ATTACHMENT'
  AND attachment_id IS NOT NULL
GROUP BY id_inventory_unit, attachment_id
HAVING COUNT(*) > 1;

-- ============================================================================
-- STEP 2: BACKUP BEFORE CLEANUP
-- ============================================================================

SELECT '========================================' as separator;
SELECT 'STEP 2: Creating backup before cleanup...' as step;
SELECT '========================================' as separator;

-- Backup duplicate records before deletion
DROP TABLE IF EXISTS inventory_attachment_duplicates_backup_20260214;

CREATE TABLE inventory_attachment_duplicates_backup_20260214 AS
SELECT ia.*
FROM inventory_attachment ia
WHERE EXISTS (
    SELECT 1 
    FROM inventory_attachment ia2
    WHERE ia2.id_inventory_unit = ia.id_inventory_unit
      AND (
        (ia2.tipe_item = 'BATTERY' AND ia2.baterai_id = ia.baterai_id AND ia2.baterai_id IS NOT NULL)
        OR (ia2.tipe_item = 'CHARGER' AND ia2.charger_id = ia.charger_id AND ia2.charger_id IS NOT NULL)
        OR (ia2.tipe_item = 'ATTACHMENT' AND ia2.attachment_id = ia.attachment_id AND ia2.attachment_id IS NOT NULL)
      )
    GROUP BY 
      ia2.id_inventory_unit,
      CASE ia2.tipe_item
        WHEN 'BATTERY' THEN ia2.baterai_id
        WHEN 'CHARGER' THEN ia2.charger_id
        WHEN 'ATTACHMENT' THEN ia2.attachment_id
      END
    HAVING COUNT(*) > 1
);

SELECT COUNT(*) as duplicates_backed_up 
FROM inventory_attachment_duplicates_backup_20260214;

-- ============================================================================
-- STEP 3: CLEAN UP DUPLICATES (Keep oldest record)
-- ============================================================================

SELECT '========================================' as separator;
SELECT 'STEP 3: Cleaning up duplicates (keeping oldest record)...' as step;
SELECT '========================================' as separator;

-- Clean up duplicate BATTERY records (keep oldest)
DELETE ia1 FROM inventory_attachment ia1
INNER JOIN (
    SELECT 
        id_inventory_unit,
        baterai_id,
        MIN(id_inventory_attachment) as keep_id
    FROM inventory_attachment
    WHERE id_inventory_unit IS NOT NULL 
      AND tipe_item = 'BATTERY'
      AND baterai_id IS NOT NULL
    GROUP BY id_inventory_unit, baterai_id
    HAVING COUNT(*) > 1
) ia2 ON ia1.id_inventory_unit = ia2.id_inventory_unit 
    AND ia1.baterai_id = ia2.baterai_id
    AND ia1.id_inventory_attachment > ia2.keep_id;

SELECT ROW_COUNT() as battery_duplicates_removed;

-- Clean up duplicate CHARGER records (keep oldest)
DELETE ia1 FROM inventory_attachment ia1
INNER JOIN (
    SELECT 
        id_inventory_unit,
        charger_id,
        MIN(id_inventory_attachment) as keep_id
    FROM inventory_attachment
    WHERE id_inventory_unit IS NOT NULL 
      AND tipe_item = 'CHARGER'
      AND charger_id IS NOT NULL
    GROUP BY id_inventory_unit, charger_id
    HAVING COUNT(*) > 1
) ia2 ON ia1.id_inventory_unit = ia2.id_inventory_unit 
    AND ia1.charger_id = ia2.charger_id
    AND ia1.id_inventory_attachment > ia2.keep_id;

SELECT ROW_COUNT() as charger_duplicates_removed;

-- Clean up duplicate ATTACHMENT records (keep oldest)
DELETE ia1 FROM inventory_attachment ia1
INNER JOIN (
    SELECT 
        id_inventory_unit,
        attachment_id,
        MIN(id_inventory_attachment) as keep_id
    FROM inventory_attachment
    WHERE id_inventory_unit IS NOT NULL 
      AND tipe_item = 'ATTACHMENT'
      AND attachment_id IS NOT NULL
    GROUP BY id_inventory_unit, attachment_id
    HAVING COUNT(*) > 1
) ia2 ON ia1.id_inventory_unit = ia2.id_inventory_unit 
    AND ia1.attachment_id = ia2.attachment_id
    AND ia1.id_inventory_attachment > ia2.keep_id;

SELECT ROW_COUNT() as attachment_duplicates_removed;

-- ============================================================================
-- STEP 4: VERIFY NO DUPLICATES REMAIN
-- ============================================================================

SELECT '========================================' as separator;
SELECT 'STEP 4: Verifying cleanup (should be 0 duplicates)...' as step;
SELECT '========================================' as separator;

-- This should return 0 rows
SELECT 
    id_inventory_unit,
    tipe_item,
    COUNT(*) as duplicate_count
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
HAVING COUNT(*) > 1;

-- ============================================================================
-- FINAL STATUS
-- ============================================================================

SELECT '========================================' as separator;
SELECT 'DUPLICATE CLEANUP COMPLETED!' as status;
SELECT '========================================' as separator;

SELECT 
    'Total records now' as description,
    COUNT(*) as count
FROM inventory_attachment
UNION ALL
SELECT 
    'Duplicates backed up',
    COUNT(*)
FROM inventory_attachment_duplicates_backup_20260214;

SELECT '========================================' as separator;
SELECT 'You can now proceed with the main migration script.' as next_step;
SELECT 'IMPORTANT: Verify no duplicates remain before adding UNIQUE constraints!' as warning;
SELECT '========================================' as separator;

-- ============================================================================
-- ROLLBACK SCRIPT (if needed)
-- ============================================================================
-- To restore deleted duplicates:
-- INSERT INTO inventory_attachment SELECT * FROM inventory_attachment_duplicates_backup_20260214 
--   WHERE id_inventory_attachment NOT IN (SELECT id_inventory_attachment FROM inventory_attachment);
-- ============================================================================
