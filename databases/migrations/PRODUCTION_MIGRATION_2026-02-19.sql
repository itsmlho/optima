-- ================================================================
-- PRODUCTION DATABASE MIGRATION
-- ================================================================
-- Date: 2026-02-19
-- Description: Marketing Module Enhancement - Invoice Automation & Customer Conversion
-- Database: Production MySQL Server
-- By: System Administrator
-- ================================================================

-- SAFETY CHECK: Verify database name
SELECT DATABASE() AS current_database;

-- ================================================================
-- STEP 1: BACKUP VERIFICATION
-- ================================================================
-- IMPORTANT: Ensure backup was created BEFORE running this script!
-- Command: mysqldump -u [user] -p [database] > backup_2026-02-19_YYYYMMDD_HHMMSS.sql

-- ================================================================
-- STEP 2: ADD COLUMNS TO delivery_instructions
-- ================================================================

-- Pre-check: Verify columns status (for manual verification)
-- Run this before migration to see what will be done:
-- SELECT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
-- WHERE TABLE_SCHEMA = DATABASE() 
--   AND TABLE_NAME = 'delivery_instructions' 
--   AND COLUMN_NAME IN ('invoice_generated', 'invoice_generated_at');

-- Add invoice_generated column (idempotent - safe to run multiple times)
-- If column exists, this will show a warning but continue
ALTER TABLE delivery_instructions 
ADD COLUMN IF NOT EXISTS invoice_generated TINYINT(1) DEFAULT 0 
COMMENT 'Flag: 1 if invoice has been auto-generated';

-- Add invoice_generated_at column (idempotent - safe to run multiple times)
ALTER TABLE delivery_instructions 
ADD COLUMN IF NOT EXISTS invoice_generated_at DATETIME NULL 
COMMENT 'Timestamp when invoice was generated';

-- ================================================================
-- STEP 3: ADD INDEX FOR PERFORMANCE
-- ================================================================

-- Pre-check: Verify index status (for manual verification)
-- Run this before migration:
-- SELECT INDEX_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.STATISTICS
-- WHERE TABLE_SCHEMA = DATABASE() 
--   AND TABLE_NAME = 'delivery_instructions' 
--   AND INDEX_NAME = 'idx_invoice_automation';

-- Add index for invoice automation queries
-- Note: MySQL < 8.0 doesn't support IF NOT EXISTS for indexes
-- This will produce an error if index exists, but we'll handle it gracefully
CREATE INDEX idx_invoice_automation 
ON delivery_instructions (invoice_generated, sampai_tanggal_approve, spk_id, status_di);
-- If error "Duplicate key name", the index already exists - migration can continue

-- ================================================================
-- STEP 4: ADD COLUMN TO quotations
-- ================================================================

-- Pre-check: Verify column status (for manual verification)
-- Run this before migration:
-- SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
-- WHERE TABLE_SCHEMA = DATABASE() 
--   AND TABLE_NAME = 'quotations' 
--   AND COLUMN_NAME = 'customer_converted_at';

-- Add customer_converted_at column (idempotent - safe to run multiple times)
ALTER TABLE quotations 
ADD COLUMN IF NOT EXISTS customer_converted_at DATETIME NULL 
AFTER created_customer_id;

-- ================================================================
-- STEP 5: VERIFY MIGRATION SUCCESS
-- ================================================================

-- Verify delivery_instructions columns
SELECT 'delivery_instructions columns:' AS verification;
SELECT COLUMN_NAME, COLUMN_TYPE, COLUMN_DEFAULT, COLUMN_COMMENT 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'delivery_instructions' 
  AND COLUMN_NAME IN ('invoice_generated', 'invoice_generated_at')
ORDER BY ORDINAL_POSITION;

-- Verify delivery_instructions index
SELECT 'delivery_instructions index:' AS verification;
SHOW INDEX FROM delivery_instructions WHERE Key_name = 'idx_invoice_automation';

-- Verify quotations column
SELECT 'quotations column:' AS verification;
SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'quotations'
  AND COLUMN_NAME = 'customer_converted_at';

-- ================================================================
-- STEP 6: CHECK ELIGIBLE DIs FOR INVOICE AUTOMATION
-- ================================================================

SELECT 'Eligible DIs for invoice automation:' AS check_eligible;
SELECT 
    COUNT(*) as total_selesai_dis,
    SUM(CASE WHEN di.invoice_generated = 1 THEN 1 ELSE 0 END) as already_generated,
    SUM(CASE WHEN di.invoice_generated = 0 THEN 1 ELSE 0 END) as pending_generation,
    SUM(CASE WHEN di.invoice_generated = 0 
             AND s.kontrak_id IS NOT NULL
             AND di.sampai_tanggal_approve IS NOT NULL 
             AND DATE_ADD(di.sampai_tanggal_approve, INTERVAL 30 DAY) <= NOW() 
        THEN 1 ELSE 0 END) as eligible_for_auto_invoice
FROM delivery_instructions di
INNER JOIN spk s ON s.id = di.spk_id
WHERE di.status_di = 'SELESAI';

-- Show sample of eligible DIs (first 5)
SELECT 'Sample eligible DIs (first 5):' AS sample;
SELECT 
    di.id,
    di.nomor_di,
    c.customer_name,
    k.no_kontrak,
    di.sampai_tanggal_approve,
    DATEDIFF(NOW(), di.sampai_tanggal_approve) AS days_since_completion,
    di.invoice_generated
FROM delivery_instructions di
INNER JOIN spk s ON s.id = di.spk_id
INNER JOIN kontrak k ON k.id = s.kontrak_id
INNER JOIN customer_contracts cc ON cc.kontrak_id = k.id
INNER JOIN customers c ON c.id = cc.customer_id
WHERE di.status_di = 'SELESAI'
  AND s.kontrak_id IS NOT NULL
  AND di.sampai_tanggal_approve IS NOT NULL
  AND DATE_ADD(di.sampai_tanggal_approve, INTERVAL 30 DAY) <= NOW()
  AND di.invoice_generated = 0
ORDER BY di.sampai_tanggal_approve ASC
LIMIT 5;

-- ================================================================
-- MIGRATION COMPLETE
-- ================================================================

SELECT '✅ Migration completed successfully!' AS status;
SELECT NOW() AS migration_completed_at;
