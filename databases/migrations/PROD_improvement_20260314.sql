-- ============================================================================
-- PRODUCTION IMPROVEMENT: Index & Optimasi
-- ============================================================================
-- Tanggal: 2026-03-14
-- Tujuan: Tambah index untuk performa query
-- 
-- URUTAN JALANKAN:
-- 1. PROD_sync_from_dev_20260314.sql (tabel + procedure)
-- 2. PROD_improvement_20260314.sql (index - file ini)
-- 3. PROD_fix_views_procedures_20260314.sql (perbaikan view/procedure)
--
-- INSTRUKSI:
-- 1. Backup database Production
-- 2. Pilih database Production di phpMyAdmin
-- 3. Buka tab SQL, paste seluruh script ini, klik Go
-- 4. Jika ada error "Duplicate key name" = index sudah ada, aman diabaikan
-- ============================================================================

-- ============================================================================
-- 1. work_orders - Index untuk filter customer_location_id
-- ============================================================================
ALTER TABLE `work_orders` 
ADD INDEX `idx_customer_location_id` (`customer_location_id`);

-- ============================================================================
-- 2. work_orders - Index untuk report completion_date (jika belum ada)
-- ============================================================================
ALTER TABLE `work_orders` 
ADD INDEX `idx_completion_date` (`completion_date`);

-- ============================================================================
-- 3. inventory_unit - Index untuk filter status_unit_id
-- ============================================================================
ALTER TABLE `inventory_unit` 
ADD INDEX `idx_status_unit_id` (`status_unit_id`);

-- ============================================================================
-- 4. work_order_spareparts - Index untuk query quantity_used
-- ============================================================================
ALTER TABLE `work_order_spareparts` 
ADD INDEX `idx_quantity_used` (`quantity_used`);

-- ============================================================================
-- 5. system_activity_log - Index untuk lookup per entitas (opsional)
-- ============================================================================
ALTER TABLE `system_activity_log` 
ADD INDEX `idx_table_record` (`table_name`, `record_id`);

-- ============================================================================
-- SELESAI
-- ============================================================================
SELECT 'Improvement selesai: Index performa berhasil ditambahkan' AS status;
