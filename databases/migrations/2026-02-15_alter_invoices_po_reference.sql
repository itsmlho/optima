-- =====================================================
-- Migration: Alter invoices - Add PO History Reference
-- Date: 2026-02-15
-- Purpose: Link invoices to specific PO from history
-- Dependencies: invoices, contract_po_history tables
-- =====================================================

-- Add reference to PO history
ALTER TABLE invoices
ADD COLUMN IF NOT EXISTS po_history_id INT NULL AFTER customer_po_number
COMMENT 'Reference to contract_po_history table (correct PO for billing period)';

-- Add snapshot of PO details (for historical accuracy)
ALTER TABLE invoices
ADD COLUMN IF NOT EXISTS po_number_snapshot VARCHAR(100) NULL AFTER po_history_id
COMMENT 'PO number snapshot at time of invoice creation';

ALTER TABLE invoices
ADD COLUMN IF NOT EXISTS po_date_snapshot DATE NULL AFTER po_number_snapshot
COMMENT 'PO date snapshot';

-- Add foreign key constraint (after contract_po_history table exists)
-- ALTER TABLE invoices
-- ADD CONSTRAINT fk_invoices_po_history
-- FOREIGN KEY (po_history_id) REFERENCES contract_po_history(id)
-- ON DELETE SET NULL;

-- Create index for PO lookups
CREATE INDEX idx_invoices_po_history ON invoices(po_history_id);
CREATE INDEX idx_invoices_po_snapshot ON invoices(po_number_snapshot);

-- =====================================================
-- Data Migration: Link existing invoices to PO
-- =====================================================

-- For existing invoices, copy customer_po_number to snapshot
UPDATE invoices
SET po_number_snapshot = customer_po_number
WHERE customer_po_number IS NOT NULL 
  AND customer_po_number != ''
  AND po_number_snapshot IS NULL;

-- =====================================================
-- Verification Queries
-- =====================================================

-- Check column additions
-- SHOW COLUMNS FROM invoices LIKE '%po%';

-- Count invoices with PO history link
-- SELECT 
--     COUNT(*) as total_invoices,
--     SUM(CASE WHEN po_history_id IS NOT NULL THEN 1 ELSE 0 END) as with_po_history,
--     SUM(CASE WHEN customer_po_number IS NOT NULL THEN 1 ELSE 0 END) as with_po_number
-- FROM invoices;

-- List invoices with PO details
-- SELECT 
--     i.invoice_number, i.invoice_date,
--     i.customer_po_number as legacy_po,
--     i.po_number_snapshot as snapshot_po,
--     cph.po_number as current_po,
--     cph.effective_from, cph.effective_to
-- FROM invoices i
-- LEFT JOIN contract_po_history cph ON cph.id = i.po_history_id
-- WHERE i.contract_id IS NOT NULL
-- ORDER BY i.invoice_date DESC
-- LIMIT 20;

-- Find invoices without PO but contract requires one
-- SELECT 
--     i.invoice_number, i.invoice_date,
--     k.nomor_kontrak, k.rental_mode
-- FROM invoices i
-- JOIN kontrak k ON k.id = i.contract_id
-- WHERE k.rental_mode IN ('PO_ONLY', 'FORMAL_CONTRACT')
--   AND i.po_history_id IS NULL
--   AND i.customer_po_number IS NULL;

-- =====================================================
-- Rollback (if needed)
-- =====================================================
-- ALTER TABLE invoices DROP FOREIGN KEY IF EXISTS fk_invoices_po_history;
-- ALTER TABLE invoices DROP COLUMN IF EXISTS po_history_id;
-- ALTER TABLE invoices DROP COLUMN IF EXISTS po_number_snapshot;
-- ALTER TABLE invoices DROP COLUMN IF EXISTS po_date_snapshot;
-- DROP INDEX IF EXISTS idx_invoices_po_history ON invoices;
-- DROP INDEX IF EXISTS idx_invoices_po_snapshot ON invoices;
