-- ========================================
-- PRODUCTION SCHEMA CHECK
-- ========================================
-- Run this first to understand production schema

-- Check kontrak table structure
SHOW COLUMNS FROM kontrak;

-- Check what columns exist
SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'u138256737_optima_db'
  AND TABLE_NAME = 'kontrak'
ORDER BY ORDINAL_POSITION;
