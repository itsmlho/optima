-- Migration: Add customer_converted_at column to quotations table
-- Date: 2026-02-19
-- Purpose: Track when a prospect was converted to a permanent customer

-- Add column if not exists (for MySQL/MariaDB)
SET @column_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'quotations' 
    AND COLUMN_NAME = 'customer_converted_at'
);

SET @sql = IF(
    @column_exists = 0,
    'ALTER TABLE quotations ADD COLUMN customer_converted_at DATETIME NULL AFTER created_customer_id',
    'SELECT "Column customer_converted_at already exists" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Verification query
SELECT 
    COLUMN_NAME, 
    DATA_TYPE, 
    IS_NULLABLE, 
    COLUMN_DEFAULT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'quotations'
    AND COLUMN_NAME IN ('created_customer_id', 'customer_converted_at')
ORDER BY ORDINAL_POSITION;
