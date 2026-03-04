-- ============================================================
-- OPTIMA: Drop Deprecated Tables
-- ============================================================
-- Purpose: Remove inventory_attachment deprecated table
-- Impact: MEDIUM - Cleanup, reduces database size by ~2-3 MB
-- Execution: Run AFTER verifying data migrated to new tables
-- Rollback: Restore from _final_backup_inventory_attachment_20260302
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
START TRANSACTION;

SELECT '========================================' AS 'DROP DEPRECATED TABLES';
SELECT 'Removing deprecated inventory_attachment table...' AS 'Status';
SELECT NOW() AS 'Timestamp';
SELECT '========================================' AS '';

-- ============================================================
-- 1. FINAL BACKUP
-- ============================================================
SELECT '1. Creating final backup...' AS 'Step';

-- Temporarily allow invalid dates for backup
SET @old_sql_mode = @@sql_mode;
SET sql_mode = 'NO_ENGINE_SUBSTITUTION';

DROP TABLE IF EXISTS _final_backup_inventory_attachment_20260303;

CREATE TABLE _final_backup_inventory_attachment_20260303 AS 
SELECT * FROM inventory_attachment;

-- Restore original sql_mode
SET sql_mode = @old_sql_mode;

SELECT COUNT(*) AS 'Records Backed Up' 
FROM _final_backup_inventory_attachment_20260303;

-- ============================================================
-- 2. VERIFY MIGRATION COMPLETE
-- ============================================================
SELECT '2. Verifying migration to new tables...' AS 'Step';

SELECT 
    (SELECT COUNT(*) FROM inventory_batteries) AS 'Batteries Count',
    (SELECT COUNT(*) FROM inventory_chargers) AS 'Chargers Count',
    (SELECT COUNT(*) FROM inventory_attachments_new) AS 'Attachments Count',
    (SELECT COUNT(*) FROM _final_backup_inventory_attachment_20260303) AS 'Original Count';

-- ============================================================
-- 3. CHECK FOR CODE REFERENCES
-- ============================================================
SELECT '3. Checking for code references...' AS 'Step';

-- This is a manual check - developers should verify:
-- grep -r "inventory_attachment" app/
-- grep -r "InventoryAttachmentModel" app/
-- Should only find: app/Models/InventoryAttachmentModel.php (which should be marked deprecated)

SELECT 'MANUAL VERIFICATION REQUIRED:' AS 'Warning';
SELECT 'Check code for references to inventory_attachment table' AS 'Action';
SELECT 'Run: grep -r "inventory_attachment" app/' AS 'Command';

-- ============================================================
-- 4. DROP DEPRECATED TABLE
-- ============================================================
SELECT '4. Dropping inventory_attachment table...' AS 'Step';

DROP TABLE IF EXISTS inventory_attachment;

SELECT 'Table dropped' AS 'Result';

-- ============================================================
-- 5. VERIFY TABLE REMOVED
-- ============================================================
SELECT '5. Verifying table removed...' AS 'Step';

SELECT COUNT(*) AS 'Should be 0 (table not found)'
FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_NAME = 'inventory_attachment' 
  AND TABLE_SCHEMA = DATABASE();

-- ============================================================
-- VERIFICATION
-- ============================================================
SELECT '========================================' AS '';
SELECT 'VERIFICATION' AS '';
SELECT '========================================' AS '';

-- Check replacement tables have data
SELECT 
    'inventory_batteries' AS 'Table',
    COUNT(*) AS 'Records'
FROM inventory_batteries
UNION ALL
SELECT 
    'inventory_chargers',
    COUNT(*)
FROM inventory_chargers
UNION ALL
SELECT 
    'inventory_attachments_new',
    COUNT(*)
FROM inventory_attachments_new;

-- Check backup exists
SELECT 
    '_final_backup_inventory_attachment_20260303' AS 'Backup Table',
    COUNT(*) AS 'Records Available for Rollback'
FROM _final_backup_inventory_attachment_20260303;

-- ============================================================
-- SUMMARY
-- ============================================================
SELECT '========================================' AS '';
SELECT 'DROP SUMMARY' AS '';
SELECT '========================================' AS '';

SELECT 
    '✅ COMPLETED' AS 'Status',
    'inventory_attachment table dropped' AS 'Result',
    'Backup available for 30 days' AS 'Rollback';

SELECT '========================================' AS '';
SELECT 'Next: Drop backup table after 30 days of stable operation' AS 'Recommendation';
SELECT 'DROP TABLE _final_backup_inventory_attachment_20260303;' AS 'Command (April 2, 2026)';
SELECT '========================================' AS '';

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- COMMIT OR ROLLBACK
-- ============================================================
SELECT '⚠️  REVIEW RESULTS ABOVE' AS 'WARNING';
SELECT 'Type COMMIT; to finalize or ROLLBACK; to undo' AS 'Action Required';

-- COMMIT;

-- ============================================================
-- ROLLBACK SCRIPT (if needed)
-- ============================================================
/*
-- To restore the dropped table:
CREATE TABLE inventory_attachment LIKE _final_backup_inventory_attachment_20260303;
INSERT INTO inventory_attachment SELECT * FROM _final_backup_inventory_attachment_20260303;
*/
