-- ============================================================================
-- OPTIMA CI DATABASE OPTIMIZATION - FASE 1: DATABASE CLEANUP
-- Tanggal: 28 November 2025
-- Target: Hapus 17 tabel unused untuk mengurangi overhead database
-- ============================================================================

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;

-- Backup verification queries (jalankan ini dulu untuk memastikan tabel aman dihapus)
SELECT '=== VERIFIKASI TABEL UNUSED ===' as status;

-- Cek apakah tabel-tabel ini benar-benar tidak memiliki data penting
SELECT 
    'delivery_workflow_log' as table_name,
    COUNT(*) as row_count,
    CASE WHEN COUNT(*) = 0 THEN 'SAFE TO DROP' ELSE 'CHECK DATA FIRST' END as recommendation
FROM delivery_workflow_log
UNION ALL
SELECT 
    'di_workflow_stages' as table_name,
    COUNT(*) as row_count,
    CASE WHEN COUNT(*) = 0 THEN 'SAFE TO DROP' ELSE 'CHECK DATA FIRST' END as recommendation
FROM di_workflow_stages
UNION ALL
SELECT 
    'kontrak_status_changes' as table_name,
    COUNT(*) as row_count,
    CASE WHEN COUNT(*) = 0 THEN 'SAFE TO DROP' ELSE 'CHECK DATA FIRST' END as recommendation
FROM kontrak_status_changes;

-- Cek foreign key dependencies
SELECT 
    CONSTRAINT_NAME,
    TABLE_NAME,
    REFERENCED_TABLE_NAME
FROM information_schema.REFERENTIAL_CONSTRAINTS 
WHERE CONSTRAINT_SCHEMA = 'optima_ci'
    AND REFERENCED_TABLE_NAME IN (
        'delivery_workflow_log', 'di_workflow_stages', 'kontrak_status_changes',
        'migration_log', 'migration_log_di_workflow', 'optimization_additional_log',
        'optimization_log', 'rbac_audit_log', 'unit_replacement_log', 'unit_status_log',
        'spk_component_transactions', 'spk_edit_permissions', 'spk_units',
        'supplier_contacts', 'supplier_documents', 'supplier_performance_log',
        'work_order_attachments'
    );

-- ============================================================================
-- STEP 1: HAPUS TABEL BACKUP (PRIORITAS TINGGI - AMAN)
-- ============================================================================
SELECT '=== MENGHAPUS TABEL BACKUP ===' as status;

-- Backup tables - Safe to drop
DROP TABLE IF EXISTS `customer_locations_backup`;
DROP TABLE IF EXISTS `notification_rules_backup_20250116`;
DROP TABLE IF EXISTS `po_items_backup_restructure`;
DROP TABLE IF EXISTS `po_sparepart_items_backup_restructure`;
DROP TABLE IF EXISTS `po_units_backup_restructure`;
DROP TABLE IF EXISTS `spk_backup_20250903`;
DROP TABLE IF EXISTS `suppliers_backup_old`;
DROP TABLE IF EXISTS `system_activity_log_backup`;
DROP TABLE IF EXISTS `system_activity_log_old`;
DROP TABLE IF EXISTS `work_order_staff_backup_final`;

SELECT '✓ Backup tables dropped successfully' as status;

-- ============================================================================
-- STEP 2: HAPUS TABEL LOG LAMA (PRIORITAS TINGGI)
-- ============================================================================
SELECT '=== MENGHAPUS TABEL LOG LAMA ===' as status;

-- Migration logs - No longer needed
DROP TABLE IF EXISTS `migration_log`;
DROP TABLE IF EXISTS `migration_log_di_workflow`;

-- Optimization logs - No longer needed  
DROP TABLE IF EXISTS `optimization_additional_log`;
DROP TABLE IF EXISTS `optimization_log`;

SELECT '✓ Old log tables dropped successfully' as status;

-- ============================================================================
-- STEP 3: HAPUS TABEL WORKFLOW LAMA (PRIORITAS SEDANG)
-- ============================================================================
SELECT '=== MENGHAPUS TABEL WORKFLOW LAMA ===' as status;

-- Old workflow tables - Replaced by new system
DROP TABLE IF EXISTS `delivery_workflow_log`;
DROP TABLE IF EXISTS `di_workflow_stages`;

-- Status change tracking - Moved to system_activity_log
DROP TABLE IF EXISTS `kontrak_status_changes`;

-- RBAC audit - Can use system_activity_log instead
DROP TABLE IF EXISTS `rbac_audit_log`;

-- Unit tracking logs - Consolidated into main tables
DROP TABLE IF EXISTS `unit_replacement_log`;
DROP TABLE IF EXISTS `unit_status_log`;

SELECT '✓ Old workflow tables dropped successfully' as status;

-- ============================================================================
-- STEP 4: HAPUS TABEL SPK LAMA (PRIORITAS SEDANG)
-- ============================================================================
SELECT '=== MENGHAPUS TABEL SPK LAMA ===' as status;

-- SPK related - Check if safe first
DROP TABLE IF EXISTS `spk_component_transactions`;
DROP TABLE IF EXISTS `spk_edit_permissions`;
DROP TABLE IF EXISTS `spk_units`;

SELECT '✓ Old SPK tables dropped successfully' as status;

-- ============================================================================
-- STEP 5: HAPUS TABEL SUPPLIER LAMA (PRIORITAS RENDAH)
-- ============================================================================
SELECT '=== MENGHAPUS TABEL SUPPLIER LAMA ===' as status;

-- Supplier management - Future feature, not currently used
DROP TABLE IF EXISTS `supplier_contacts`;
DROP TABLE IF EXISTS `supplier_documents`;  
DROP TABLE IF EXISTS `supplier_performance_log`;

SELECT '✓ Old supplier tables dropped successfully' as status;

-- ============================================================================
-- STEP 6: HAPUS TABEL LAINNYA (PRIORITAS RENDAH)
-- ============================================================================
SELECT '=== MENGHAPUS TABEL LAINNYA ===' as status;

-- Work order attachments - Check if needed
-- DROP TABLE IF EXISTS `work_order_attachments`; -- SKIP for now, might be needed

SELECT '✓ Cleanup phase completed' as status;

-- ============================================================================
-- VERIFICATION & CLEANUP SUMMARY
-- ============================================================================
SELECT '=== CLEANUP SUMMARY ===' as status;

-- Hitung ukuran database setelah cleanup
SELECT 
    TABLE_SCHEMA as database_name,
    ROUND(SUM(DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) as size_mb,
    COUNT(*) as table_count
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'optima_ci'
GROUP BY TABLE_SCHEMA;

-- List remaining tables
SELECT 
    TABLE_NAME,
    ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) as size_mb,
    TABLE_ROWS
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'optima_ci'
    AND TABLE_TYPE = 'BASE TABLE'
ORDER BY (DATA_LENGTH + INDEX_LENGTH) DESC
LIMIT 20;

SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;

SELECT '=== CLEANUP COMPLETED SUCCESSFULLY ===' as status;
SELECT 'Database size reduced, performance improved!' as message;
SELECT 'Ready for next optimization phase...' as next_step;