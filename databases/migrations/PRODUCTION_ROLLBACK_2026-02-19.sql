-- ================================================================
-- ROLLBACK SCRIPT - PRODUCTION DATABASE
-- ================================================================
-- Date: 2026-02-19
-- Description: Rollback changes from marketing module enhancement migration
-- USE THIS ONLY IF MIGRATION CAUSES ISSUES!
-- ================================================================

-- SAFETY CHECK
SELECT DATABASE() AS current_database;
SELECT NOW() AS rollback_started_at;

-- ================================================================
-- STEP 1: REMOVE INDEX FROM delivery_instructions
-- ================================================================

SELECT 'Removing index idx_invoice_automation...' AS step_1;

SET @index_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'delivery_instructions' 
    AND INDEX_NAME = 'idx_invoice_automation'
);

SET @sql_drop_index = IF(
    @index_exists > 0,
    'ALTER TABLE delivery_instructions DROP INDEX idx_invoice_automation',
    'SELECT "Index idx_invoice_automation does not exist - skipped" AS message'
);

PREPARE stmt_drop_index FROM @sql_drop_index;
EXECUTE stmt_drop_index;
DEALLOCATE PREPARE stmt_drop_index;

-- ================================================================
-- STEP 2: REMOVE COLUMNS FROM delivery_instructions
-- ================================================================

SELECT 'Removing columns from delivery_instructions...' AS step_2;

-- Remove invoice_generated_at
SET @column_exists_1 = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'delivery_instructions' 
    AND COLUMN_NAME = 'invoice_generated_at'
);

SET @sql_drop_col_1 = IF(
    @column_exists_1 > 0,
    'ALTER TABLE delivery_instructions DROP COLUMN invoice_generated_at',
    'SELECT "Column invoice_generated_at does not exist - skipped" AS message'
);

PREPARE stmt_drop_col_1 FROM @sql_drop_col_1;
EXECUTE stmt_drop_col_1;
DEALLOCATE PREPARE stmt_drop_col_1;

-- Remove invoice_generated
SET @column_exists_2 = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'delivery_instructions' 
    AND COLUMN_NAME = 'invoice_generated'
);

SET @sql_drop_col_2 = IF(
    @column_exists_2 > 0,
    'ALTER TABLE delivery_instructions DROP COLUMN invoice_generated',
    'SELECT "Column invoice_generated does not exist - skipped" AS message'
);

PREPARE stmt_drop_col_2 FROM @sql_drop_col_2;
EXECUTE stmt_drop_col_2;
DEALLOCATE PREPARE stmt_drop_col_2;

-- ================================================================
-- STEP 3: REMOVE COLUMN FROM quotations
-- ================================================================

SELECT 'Removing column from quotations...' AS step_3;

SET @column_exists_3 = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'quotations' 
    AND COLUMN_NAME = 'customer_converted_at'
);

SET @sql_drop_col_3 = IF(
    @column_exists_3 > 0,
    'ALTER TABLE quotations DROP COLUMN customer_converted_at',
    'SELECT "Column customer_converted_at does not exist - skipped" AS message'
);

PREPARE stmt_drop_col_3 FROM @sql_drop_col_3;
EXECUTE stmt_drop_col_3;
DEALLOCATE PREPARE stmt_drop_col_3;

-- ================================================================
-- STEP 4: VERIFY ROLLBACK
-- ================================================================

SELECT 'Verifying rollback...' AS step_4;

-- Check delivery_instructions columns removed
SELECT 
    CASE 
        WHEN COUNT(*) = 0 THEN '✅ Columns removed successfully'
        ELSE '❌ Columns still exist'
    END AS delivery_instructions_columns
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'delivery_instructions' 
  AND COLUMN_NAME IN ('invoice_generated', 'invoice_generated_at');

-- Check quotations column removed
SELECT 
    CASE 
        WHEN COUNT(*) = 0 THEN '✅ Column removed successfully'
        ELSE '❌ Column still exists'
    END AS quotations_column
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'quotations' 
  AND COLUMN_NAME = 'customer_converted_at';

-- Check index removed
SELECT 
    CASE 
        WHEN COUNT(*) = 0 THEN '✅ Index removed successfully'
        ELSE '❌ Index still exists'
    END AS index_status
FROM INFORMATION_SCHEMA.STATISTICS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'delivery_instructions' 
  AND INDEX_NAME = 'idx_invoice_automation';

-- ================================================================
-- ROLLBACK COMPLETE
-- ================================================================

SELECT '✅ Rollback completed!' AS status;
SELECT NOW() AS rollback_completed_at;

-- ================================================================
-- NEXT STEPS AFTER ROLLBACK
-- ================================================================
-- 1. Restore application code to previous version
-- 2. Clear application cache
-- 3. Monitor application logs
-- 4. Investigate root cause of issues
-- ================================================================
