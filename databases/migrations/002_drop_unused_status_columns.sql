-- =====================================================
-- INVENTORY ATTACHMENT STATUS CLEANUP - PHASE 2
-- =====================================================
-- Date: 2026-02-02
-- Purpose: Drop unused status columns after data migration
-- IMPORTANT: Run this ONLY AFTER Phase 1 migration and application code update
-- =====================================================

USE optima_ci;

-- 1. Final verification before dropping columns
SELECT 'FINAL DATA VERIFICATION' as check_type;

-- Check if all records have valid attachment_status
SELECT 
    attachment_status,
    COUNT(*) as count,
    ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM inventory_attachment), 2) as percentage
FROM inventory_attachment 
GROUP BY attachment_status
ORDER BY count DESC;

-- Check if there are any NULL attachment_status
SELECT COUNT(*) as null_attachment_status_count
FROM inventory_attachment 
WHERE attachment_status IS NULL OR attachment_status = '';

-- 2. Check if any applications still using old columns (should be 0)
-- Note: This is informational - manual code review required
SELECT 'CHECK OLD COLUMN USAGE' as check_type;

-- Show sample data with old columns for comparison
SELECT id_inventory_attachment, tipe_item, attachment_status, status_unit, status_attachment_id
FROM inventory_attachment 
LIMIT 5;

-- 3. Drop unused columns (DESTRUCTIVE - Cannot be undone easily)
-- Ready to execute after testing

-- First, drop foreign key constraints if they exist
SET @exist_fk_status_unit := (SELECT COUNT(*) 
    FROM information_schema.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = 'optima_ci' 
    AND TABLE_NAME = 'inventory_attachment' 
    AND CONSTRAINT_NAME = 'fk_inventory_attachment_status_unit');

SET @sqlstmt := IF(@exist_fk_status_unit > 0, 
    'ALTER TABLE inventory_attachment DROP FOREIGN KEY fk_inventory_attachment_status_unit',
    'SELECT "FK status_unit not found, skipping..." as message');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @exist_fk_status_attachment := (SELECT COUNT(*) 
    FROM information_schema.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = 'optima_ci' 
    AND TABLE_NAME = 'inventory_attachment' 
    AND CONSTRAINT_NAME = 'fk_inventory_attachment_status_attachment');

SET @sqlstmt := IF(@exist_fk_status_attachment > 0, 
    'ALTER TABLE inventory_attachment DROP FOREIGN KEY fk_inventory_attachment_status_attachment',
    'SELECT "FK status_attachment not found, skipping..." as message');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Now drop the columns
ALTER TABLE inventory_attachment DROP COLUMN status_unit;
ALTER TABLE inventory_attachment DROP COLUMN status_attachment_id;

-- 4. Add constraints for attachment_status (Optional but recommended)
-- This ensures data integrity going forward

-- Create index for better query performance (only if not exists)
SET @exist := (SELECT COUNT(*) 
               FROM information_schema.statistics 
               WHERE table_schema = 'optima_ci' 
               AND table_name = 'inventory_attachment' 
               AND index_name = 'idx_inventory_attachment_status');

SET @sqlstmt := IF(@exist = 0, 
    'CREATE INDEX idx_inventory_attachment_status ON inventory_attachment(attachment_status)',
    'SELECT "Index already exists, skipping..." as message');

PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 5. Final table structure verification
SELECT 'FINAL TABLE STRUCTURE' as check_type;

DESCRIBE inventory_attachment;

-- 6. Clean up backup table (Optional - keep for safety)
-- DROP TABLE inventory_attachment_backup_20260202;

-- 7. Update table comments
ALTER TABLE inventory_attachment 
COMMENT = 'Inventory attachment table - cleaned up status fields 2026-02-02';

SELECT 'MIGRATION PHASE 2 COMPLETED' as status;