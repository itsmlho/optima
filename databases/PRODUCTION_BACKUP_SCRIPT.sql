-- ============================================================================
-- PRODUCTION DATABASE BACKUP SCRIPT
-- ============================================================================
-- Date: February 14, 2026
-- Database: u138256737_optima_db
-- Purpose: Create backups before migration
-- ============================================================================

USE u138256737_optima_db;

-- Verify database
SELECT DATABASE() as current_db, 
       'Creating backup tables...' as status;

-- ============================================================================
-- CREATE BACKUP TABLES
-- ============================================================================

-- Backup inventory_unit
DROP TABLE IF EXISTS inventory_unit_backup_20260214;
CREATE TABLE inventory_unit_backup_20260214 AS SELECT * FROM inventory_unit;

-- Backup inventory_attachment
DROP TABLE IF EXISTS inventory_attachment_backup_20260214;
CREATE TABLE inventory_attachment_backup_20260214 AS SELECT * FROM inventory_attachment;

-- ============================================================================
-- VERIFY BACKUPS
-- ============================================================================

SELECT '========================================' as separator;
SELECT 'BACKUP VERIFICATION' as step;
SELECT '========================================' as separator;

-- Compare row counts
SELECT 
    'inventory_unit' as original_table,
    COUNT(*) as row_count
FROM inventory_unit
UNION ALL
SELECT 
    'inventory_unit_backup_20260214' as backup_table,
    COUNT(*) as row_count
FROM inventory_unit_backup_20260214
UNION ALL
SELECT 
    'inventory_attachment' as original_table,
    COUNT(*) as row_count
FROM inventory_attachment
UNION ALL
SELECT 
    'inventory_attachment_backup_20260214' as backup_table,
    COUNT(*) as row_count
FROM inventory_attachment_backup_20260214;

-- Show backup table structure
SELECT '========================================' as separator;
SELECT 'Backup tables created successfully!' as status;
SELECT 'Keep these backup tables for at least 30 days.' as recommendation;
SELECT '========================================' as separator;

-- ============================================================================
-- BACKUP TABLE SIZES
-- ============================================================================

SELECT 
    table_name,
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb,
    table_rows
FROM information_schema.TABLES
WHERE table_schema = 'u138256737_optima_db'
  AND table_name LIKE '%backup_20260214'
ORDER BY size_mb DESC;

SELECT '========================================' as separator;
SELECT 'BACKUP COMPLETED - You can now proceed with migration' as final_status;
SELECT '========================================' as separator;
