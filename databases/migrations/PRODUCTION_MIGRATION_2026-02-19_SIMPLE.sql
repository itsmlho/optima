-- ================================================================
-- PRODUCTION DATABASE MIGRATION - SIMPLE VERSION
-- ================================================================
-- Date: 2026-02-19
-- Description: Marketing Module Enhancement - Invoice Automation & Customer Conversion
-- Database: Production MySQL Server
-- Compatibility: MySQL 5.7+ / MariaDB 10.2+
-- By: System Administrator
-- ================================================================
-- 
-- IMPORTANT: This version uses simple SQL without IF NOT EXISTS
-- If columns/indexes already exist, you will see errors - THIS IS OK
-- Just continue with the verification queries at the end
-- ================================================================

-- SAFETY CHECK: Verify database name
SELECT DATABASE() AS current_database;

-- ================================================================
-- PRE-FLIGHT CHECK (RUN THIS FIRST TO SEE WHAT EXISTS)
-- ================================================================
-- Uncomment and run these queries first to check current state:

-- SELECT COLUMN_NAME, COLUMN_TYPE 
-- FROM INFORMATION_SCHEMA.COLUMNS 
-- WHERE TABLE_SCHEMA = DATABASE() 
--   AND TABLE_NAME = 'delivery_instructions' 
--   AND COLUMN_NAME IN ('invoice_generated', 'invoice_generated_at');

-- SELECT INDEX_NAME, COLUMN_NAME 
-- FROM INFORMATION_SCHEMA.STATISTICS 
-- WHERE TABLE_SCHEMA = DATABASE() 
--   AND TABLE_NAME = 'delivery_instructions' 
--   AND INDEX_NAME = 'idx_invoice_automation';

-- SELECT COLUMN_NAME, COLUMN_TYPE 
-- FROM INFORMATION_SCHEMA.COLUMNS 
-- WHERE TABLE_SCHEMA = DATABASE() 
--   AND TABLE_NAME = 'quotations' 
--   AND COLUMN_NAME = 'customer_converted_at';

-- ================================================================
-- STEP 1: ADD COLUMNS TO delivery_instructions
-- ================================================================
-- If these columns already exist, you'll get Error 1060 (Duplicate column name)
-- This is OK - just means migration was already applied

ALTER TABLE delivery_instructions 
ADD COLUMN invoice_generated TINYINT(1) DEFAULT 0 
COMMENT 'Flag: 1 if invoice has been auto-generated';

ALTER TABLE delivery_instructions 
ADD COLUMN invoice_generated_at DATETIME NULL 
COMMENT 'Timestamp when invoice was generated';

-- ================================================================
-- STEP 2: ADD INDEX FOR PERFORMANCE
-- ================================================================
-- If this index already exists, you'll get Error 1061 (Duplicate key name)
-- This is OK - just means index was already created

CREATE INDEX idx_invoice_automation 
ON delivery_instructions (invoice_generated, sampai_tanggal_approve, spk_id, status_di);

-- ================================================================
-- STEP 3: ADD COLUMN TO quotations
-- ================================================================
-- If this column already exists, you'll get Error 1060 (Duplicate column name)
-- This is OK - just means migration was already applied

ALTER TABLE quotations 
ADD COLUMN customer_converted_at DATETIME NULL 
AFTER created_customer_id;

-- ================================================================
-- STEP 4: VERIFY MIGRATION SUCCESS
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
-- STEP 5: CHECK ELIGIBLE DIs FOR INVOICE AUTOMATION
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

-- ================================================================
-- EXPECTED ERRORS (THESE ARE OK IF YOU RE-RUN THE SCRIPT):
-- ================================================================
-- Error 1060: Duplicate column name 'invoice_generated'
-- Error 1060: Duplicate column name 'invoice_generated_at'  
-- Error 1061: Duplicate key name 'idx_invoice_automation'
-- Error 1060: Duplicate column name 'customer_converted_at'
--
-- If you see these errors, it means the migration was already applied.
-- Check the verification queries at the end to confirm everything is ok.
-- ================================================================
