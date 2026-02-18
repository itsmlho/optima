-- =====================================================
-- MASTER MIGRATION SCRIPT - Phase 1 Implementation
-- Date: 2026-02-15
-- Purpose: Execute all Phase 1 migrations in correct order
-- Project: Optima - Marketing Module Enhancements
-- =====================================================

-- Set SQL mode for safer operations
SET SQL_MODE = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
SET FOREIGN_KEY_CHECKS = 0;

-- Start transaction for safety
START TRANSACTION;

-- =====================================================
-- STEP 1: Create New Tables
-- =====================================================

-- 1.1 Create operators table
SOURCE databases/migrations/2026-02-15_create_operators_table.sql;
SELECT 'Step 1.1: operators table created' as status;

-- 1.2 Create contract_operator_assignments table
SOURCE databases/migrations/2026-02-15_create_contract_operator_assignments_table.sql;
SELECT 'Step 1.2: contract_operator_assignments table created' as status;

-- 1.3 Create contract_po_history table
SOURCE databases/migrations/2026-02-15_create_contract_po_history_table.sql;
SELECT 'Step 1.3: contract_po_history table created' as status;

-- =====================================================
-- STEP 2: Alter Existing Tables
-- =====================================================

-- 2.1 Add rental mode fields to kontrak
SOURCE databases/migrations/2026-02-15_alter_kontrak_table_rental_modes.sql;
SELECT 'Step 2.1: kontrak table altered - rental modes added' as status;

-- 2.2 Add operator fields to kontrak_spesifikasi
SOURCE databases/migrations/2026-02-15_alter_kontrak_spesifikasi_operator_fields.sql;
SELECT 'Step 2.2: kontrak_spesifikasi table altered - operator fields added' as status;

-- 2.3 Add operator tracking to invoice_items
SOURCE databases/migrations/2026-02-15_alter_invoice_items_operator_tracking.sql;
SELECT 'Step 2.3: invoice_items table altered - operator tracking added' as status;

-- 2.4 Add PO history reference to invoices
SOURCE databases/migrations/2026-02-15_alter_invoices_po_reference.sql;
SELECT 'Step 2.4: invoices table altered - PO reference added' as status;

-- =====================================================
-- STEP 3: Add Foreign Key Constraints
-- =====================================================

-- Add FK after all tables exist
ALTER TABLE contract_operator_assignments
ADD CONSTRAINT fk_operator_assignment_contract
FOREIGN KEY (contract_id) REFERENCES kontrak(id) ON DELETE CASCADE;

ALTER TABLE contract_operator_assignments
ADD CONSTRAINT fk_operator_assignment_operator
FOREIGN KEY (operator_id) REFERENCES operators(id) ON DELETE RESTRICT;

ALTER TABLE contract_po_history
ADD CONSTRAINT fk_po_history_contract
FOREIGN KEY (contract_id) REFERENCES kontrak(id) ON DELETE CASCADE;

ALTER TABLE invoice_items
ADD CONSTRAINT fk_invoice_items_operator_assignment
FOREIGN KEY (operator_assignment_id) REFERENCES contract_operator_assignments(id) ON DELETE SET NULL;

ALTER TABLE invoices
ADD CONSTRAINT fk_invoices_po_history
FOREIGN KEY (po_history_id) REFERENCES contract_po_history(id) ON DELETE SET NULL;

SELECT 'Step 3: Foreign key constraints added' as status;

-- =====================================================
-- STEP 4: Verification
-- =====================================================

-- Check all new tables exist
SELECT 
    'Table Check' as verification_type,
    COUNT(*) as tables_created 
FROM information_schema.tables 
WHERE table_schema = DATABASE() 
AND table_name IN (
    'operators', 
    'contract_operator_assignments', 
    'contract_po_history'
);

-- Check kontrak table has new columns
SELECT 
    'Kontrak Columns' as verification_type,
    COUNT(*) as columns_added
FROM information_schema.columns
WHERE table_schema = DATABASE()
AND table_name = 'kontrak'
AND column_name IN (
    'rental_mode', 
    'requires_document', 
    'fast_track', 
    'billing_basis',
    'spot_rental_number'
);

-- Check invoice_items has new columns
SELECT 
    'Invoice Items Columns' as verification_type,
    COUNT(*) as columns_added
FROM information_schema.columns
WHERE table_schema = DATABASE()
AND table_name = 'invoice_items'
AND column_name IN (
    'item_type', 
    'operator_assignment_id', 
    'billing_period_start'
);

-- Check sample operators were inserted
SELECT 
    'Sample Operators' as verification_type,
    COUNT(*) as operators_count
FROM operators;

-- =====================================================
-- STEP 5: Commit or Rollback
-- =====================================================

-- If everything looks good, commit:
COMMIT;
SELECT '✅ Phase 1 migrations completed successfully!' as final_status;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- ROLLBACK INSTRUCTIONS (if needed)
-- =====================================================
-- If something went wrong, run these commands:
-- ROLLBACK;
-- SOURCE databases/migrations/2026-02-15_rollback_all.sql;

-- =====================================================
-- USAGE INSTRUCTIONS
-- =====================================================
-- 
-- To execute this migration:
-- 1. Make backup of database first!
--    mysqldump -u root optima > backup_before_phase1.sql
-- 
-- 2. Navigate to project root in terminal
--    cd c:\laragon\www\optima
-- 
-- 3. Execute migration (choose one method):
--    
--    METHOD A - Via MySQL command line:
--    mysql -u root -p optima < databases/migrations/MASTER_MIGRATION_PHASE1.sql
--    
--    METHOD B - Via MySQL Workbench:
--    - Open this file
--    - Execute script
--    
--    METHOD C - Via phpMyAdmin:
--    - Open SQL tab
--    - Load this file
--    - Execute
-- 
-- 4. Verify results:
--    - Check terminal output for errors
--    - Verify all tables created
--    - Check sample data inserted
-- 
-- 5. If errors occur:
--    - Review error messages
--    - Fix issues in individual migration files
--    - Restore backup if needed
--    - Re-run migration
-- 
-- =====================================================
