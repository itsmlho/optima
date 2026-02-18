-- =====================================================
-- Migration: Alter invoice_items - Add Operator Tracking
-- Date: 2026-02-15
-- Purpose: Track operator service in invoice line items
-- Dependencies: invoice_items, contract_operator_assignments tables
-- =====================================================

-- Add item type classification
ALTER TABLE invoice_items ADD COLUMN item_type ENUM('UNIT_RENTAL','OPERATOR_SERVICE','MAINTENANCE','DELIVERY','DEMOBILIZATION','SPARE_PARTS','LATE_FEE','ADJUSTMENT','OTHER') DEFAULT 'UNIT_RENTAL';

-- Link to operator assignment
ALTER TABLE invoice_items ADD COLUMN operator_assignment_id INT UNSIGNED NULL;
ALTER TABLE invoice_items ADD COLUMN operator_name VARCHAR(100) NULL;

-- Link to unit for unit rental items
ALTER TABLE invoice_items ADD COLUMN unit_id INT UNSIGNED NULL;
ALTER TABLE invoice_items ADD COLUMN unit_number VARCHAR(50) NULL;

-- Billing period for recurring items
ALTER TABLE invoice_items ADD COLUMN billing_period_start DATE NULL;
ALTER TABLE invoice_items ADD COLUMN billing_period_end DATE NULL;
ALTER TABLE invoice_items ADD COLUMN billing_days INT NULL;

-- Add foreign key (if contract_operator_assignments exists)
-- ALTER TABLE invoice_items
-- ADD CONSTRAINT fk_invoice_items_operator
-- FOREIGN KEY (operator_assignment_id) REFERENCES contract_operator_assignments(id)
-- ON DELETE SET NULL;

-- Create indexes
CREATE INDEX idx_invoice_items_type ON invoice_items(item_type);
CREATE INDEX idx_invoice_items_operator ON invoice_items(operator_assignment_id);
CREATE INDEX idx_invoice_items_unit ON invoice_items(unit_id);
CREATE INDEX idx_invoice_items_period ON invoice_items(billing_period_start, billing_period_end);

-- =====================================================
-- Verification Queries
-- =====================================================

-- Check column additions
-- SHOW COLUMNS FROM invoice_items LIKE '%item_type%';
-- SHOW COLUMNS FROM invoice_items LIKE '%operator%';

-- Count invoice items by type
-- SELECT item_type, COUNT(*) as total, SUM(subtotal) as total_amount
-- FROM invoice_items
-- GROUP BY item_type;

-- List operator service charges
-- SELECT 
--     ii.invoice_id, i.invoice_number,
--     ii.description, ii.operator_name,
--     ii.quantity, ii.unit_price, ii.subtotal,
--     ii.billing_period_start, ii.billing_period_end
-- FROM invoice_items ii
-- JOIN invoices i ON i.id = ii.invoice_id
-- WHERE ii.item_type = 'OPERATOR_SERVICE'
-- ORDER BY i.invoice_number DESC;

-- Compare unit rental vs operator service revenue
-- SELECT 
--     DATE_FORMAT(i.invoice_date, '%Y-%m') as month,
--     SUM(CASE WHEN ii.item_type = 'UNIT_RENTAL' THEN ii.subtotal ELSE 0 END) as unit_revenue,
--     SUM(CASE WHEN ii.item_type = 'OPERATOR_SERVICE' THEN ii.subtotal ELSE 0 END) as operator_revenue,
--     SUM(ii.subtotal) as total_revenue
-- FROM invoices i
-- JOIN invoice_items ii ON ii.invoice_id = i.id
-- WHERE i.status_invoice IN ('SENT', 'PAID')
-- GROUP BY DATE_FORMAT(i.invoice_date, '%Y-%m')
-- ORDER BY month DESC;

-- =====================================================
-- Rollback (if needed)
-- =====================================================
-- ALTER TABLE invoice_items DROP FOREIGN KEY IF EXISTS fk_invoice_items_operator;
-- ALTER TABLE invoice_items DROP COLUMN IF EXISTS item_type;
-- ALTER TABLE invoice_items DROP COLUMN IF EXISTS operator_assignment_id;
-- ALTER TABLE invoice_items DROP COLUMN IF EXISTS operator_name;
-- ALTER TABLE invoice_items DROP COLUMN IF EXISTS unit_id;
-- ALTER TABLE invoice_items DROP COLUMN IF EXISTS unit_number;
-- ALTER TABLE invoice_items DROP COLUMN IF EXISTS billing_period_start;
-- ALTER TABLE invoice_items DROP COLUMN IF EXISTS billing_period_end;
-- ALTER TABLE invoice_items DROP COLUMN IF EXISTS billing_days;
-- DROP INDEX IF EXISTS idx_invoice_items_type ON invoice_items;
-- DROP INDEX IF EXISTS idx_invoice_items_operator ON invoice_items;
-- DROP INDEX IF EXISTS idx_invoice_items_unit ON invoice_items;
-- DROP INDEX IF EXISTS idx_invoice_items_period ON invoice_items;
