-- ================================================================
-- INVOICE AUTOMATION - DATABASE MIGRATION
-- ================================================================
-- Description: Add flags to track invoice generation for DIs
-- Date: 2026-02-19
-- Author: System
-- Schema: Adjusted for actual optima_ci database structure
-- ================================================================

-- Add columns to delivery_instructions table
ALTER TABLE delivery_instructions 
ADD COLUMN invoice_generated TINYINT(1) DEFAULT 0 COMMENT 'Flag: 1 if invoice has been auto-generated',
ADD COLUMN invoice_generated_at DATETIME NULL COMMENT 'Timestamp when invoice was generated';

-- Add index for performance
-- Note: Using actual column names from schema (sampai_tanggal_approve, status_di)
-- Join through spk.kontrak_id for contract relationship
ALTER TABLE delivery_instructions
ADD INDEX idx_invoice_automation (invoice_generated, sampai_tanggal_approve, spk_id, status_di);

-- ================================================================
-- DATA MIGRATION: Mark existing DIs with invoices as generated
-- ================================================================
-- This prevents duplicate invoice generation for historical data
-- Note: Adjust table/column names based on actual invoice table structure

-- Uncomment and adjust this if invoice table exists:
-- UPDATE delivery_instructions di
-- INNER JOIN invoices inv ON inv.nomor_di = di.nomor_di
-- SET 
--     di.invoice_generated = 1,
--     di.invoice_generated_at = inv.dibuat_pada
-- WHERE di.invoice_generated = 0;

-- ================================================================
-- VERIFICATION QUERIES
-- ================================================================
-- Run these to verify migration success:

-- Check columns added successfully
SELECT COLUMN_NAME, COLUMN_TYPE, COLUMN_DEFAULT, COLUMN_COMMENT 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'optima_ci'
AND TABLE_NAME = 'delivery_instructions' 
AND COLUMN_NAME IN ('invoice_generated', 'invoice_generated_at');

-- Check index created
SHOW INDEX FROM delivery_instructions WHERE Key_name = 'idx_invoice_automation';

-- Count DIs that would be eligible for invoicing
SELECT 
    COUNT(*) as total_selesai_dis,
    SUM(invoice_generated) as already_generated,
    SUM(CASE WHEN invoice_generated = 0 THEN 1 ELSE 0 END) as pending_generation,
    SUM(CASE WHEN invoice_generated = 0 
             AND sampai_tanggal_approve IS NOT NULL 
             AND DATE_ADD(sampai_tanggal_approve, INTERVAL 30 DAY) <= NOW() 
        THEN 1 ELSE 0 END) as eligible_for_auto_invoice
FROM delivery_instructions di
INNER JOIN spk s ON s.id = di.spk_id
WHERE di.status_di = 'SELESAI' 
  AND s.kontrak_id IS NOT NULL;

-- ================================================================
-- ROLLBACK (if needed)
-- ================================================================
-- Uncomment and run these queries to rollback the migration:

-- DROP INDEX idx_invoice_automation ON delivery_instructions;
-- ALTER TABLE delivery_instructions DROP COLUMN invoice_generated_at;
-- ALTER TABLE delivery_instructions DROP COLUMN invoice_generated;

-- ================================================================
-- END OF MIGRATION
-- ================================================================
