-- ============================================================================
-- CLEANUP SQL: HAPUS TABEL YANG TIDAK DIGUNAKAN DAN TABEL BACKUP
-- ============================================================================
-- Dibuat: 28 November 2025
-- Tujuan: Membersihkan database dari tabel yang tidak digunakan untuk optimasi
-- 
-- PERINGATAN: Backup database terlebih dahulu sebelum menjalankan script ini!
-- ============================================================================

-- Matikan foreign key checks untuk menghindari masalah dependency
SET foreign_key_checks = 0;

-- ============================================================================
-- HAPUS TABEL YANG TIDAK DIGUNAKAN (17 tabel)
-- ============================================================================

-- Log dan workflow tables yang tidak digunakan
DROP TABLE IF EXISTS `delivery_workflow_log`;
DROP TABLE IF EXISTS `di_workflow_stages`;
DROP TABLE IF EXISTS `migration_log`;
DROP TABLE IF EXISTS `migration_log_di_workflow`;
DROP TABLE IF EXISTS `optimization_additional_log`;
DROP TABLE IF EXISTS `optimization_log`;
DROP TABLE IF EXISTS `rbac_audit_log`;

-- SPK related unused tables
DROP TABLE IF EXISTS `spk_component_transactions`;
DROP TABLE IF EXISTS `spk_edit_permissions`;
DROP TABLE IF EXISTS `spk_units`;

-- Supplier related unused tables
DROP TABLE IF EXISTS `supplier_contacts`;
DROP TABLE IF EXISTS `supplier_documents`;
DROP TABLE IF EXISTS `supplier_performance_log`;

-- Unit related unused tables
DROP TABLE IF EXISTS `unit_replacement_log`;
DROP TABLE IF EXISTS `unit_status_log`;

-- Work order unused tables
DROP TABLE IF EXISTS `work_order_attachments`;

-- Kontrak unused table
DROP TABLE IF EXISTS `kontrak_status_changes`;

-- ============================================================================
-- HAPUS TABEL BACKUP (10 tabel)
-- ============================================================================

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

-- Nyalakan kembali foreign key checks
SET foreign_key_checks = 1;

-- ============================================================================
-- OPTIMASI SETELAH CLEANUP
-- ============================================================================

-- Optimize semua tabel yang tersisa
SET @sql = '';
SELECT GROUP_CONCAT('OPTIMIZE TABLE `', table_name, '`;' SEPARATOR ' ') INTO @sql
FROM information_schema.tables 
WHERE table_schema = DATABASE() 
AND table_type = 'BASE TABLE';

-- Jalankan OPTIMIZE TABLE untuk semua tabel
-- (Uncomment baris berikut jika ingin auto-optimize)
-- PREPARE stmt FROM @sql;
-- EXECUTE stmt;
-- DEALLOCATE PREPARE stmt;

-- ============================================================================
-- SUMMARY
-- ============================================================================
SELECT CONCAT(
    '✅ CLEANUP SELESAI - ',
    'Dihapus 27 tabel (17 unused + 10 backup) untuk optimasi database'
) AS cleanup_summary;

SELECT 
    COUNT(*) as total_tables_remaining,
    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS database_size_mb
FROM information_schema.tables 
WHERE table_schema = DATABASE() 
AND table_type = 'BASE TABLE';

-- ============================================================================
-- NOTES:
-- 1. Script ini akan menghapus tabel yang tidak digunakan secara permanen
-- 2. Pastikan backup database sudah dibuat sebelum menjalankan
-- 3. Jika ragu, comment tabel tertentu dengan menambahkan -- di depan
-- 4. Setelah cleanup, jalankan OPTIMIZE TABLE untuk optimasi storage
-- ============================================================================