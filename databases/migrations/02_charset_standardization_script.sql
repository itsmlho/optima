-- ============================================================================
-- OPTIMA CI DATABASE OPTIMIZATION - FASE 2: CHARSET STANDARDIZATION
-- Tanggal: 28 November 2025
-- Target: Standardize charset ke utf8mb4_unicode_ci untuk konsistensi
-- ============================================================================

-- PERINGATAN: Jalankan script ini di MAINTENANCE WINDOW!
-- Operasi ini akan rebuild tabel dan membutuhkan waktu lama untuk tabel besar

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;

-- ============================================================================
-- STEP 1: ANALISIS CHARSET SAAT INI
-- ============================================================================
SELECT '=== ANALISIS CHARSET CURRENT ===' as status;

-- Cek database charset
SELECT 
    SCHEMA_NAME as database_name,
    DEFAULT_CHARACTER_SET_NAME as charset,
    DEFAULT_COLLATION_NAME as collation
FROM information_schema.SCHEMATA 
WHERE SCHEMA_NAME = 'optima_ci';

-- Cek table charsets yang berbeda
SELECT 
    TABLE_NAME,
    TABLE_COLLATION,
    ENGINE,
    ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) as size_mb
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'optima_ci' 
    AND TABLE_COLLATION != 'utf8mb4_unicode_ci'
    AND TABLE_TYPE = 'BASE TABLE'
ORDER BY (DATA_LENGTH + INDEX_LENGTH) DESC;

-- ============================================================================
-- STEP 2: SET DATABASE DEFAULT CHARSET
-- ============================================================================
SELECT '=== SETTING DATABASE DEFAULT CHARSET ===' as status;

ALTER DATABASE `optima_ci` 
CHARACTER SET = utf8mb4 
COLLATE = utf8mb4_unicode_ci;

SELECT '✓ Database charset updated' as status;

-- ============================================================================
-- STEP 3: KONVERSI TABEL MASTER/REFERENCE (KECIL)
-- ============================================================================
SELECT '=== CONVERTING MASTER TABLES ===' as status;

-- Activity types
ALTER TABLE `activity_types` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SELECT '✓ activity_types converted' as status;

-- Departments
ALTER TABLE `departemen` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SELECT '✓ departemen converted' as status;

-- Divisions
ALTER TABLE `divisions` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SELECT '✓ divisions converted' as status;

-- Positions
ALTER TABLE `positions` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SELECT '✓ positions converted' as status;

-- Roles & Permissions
ALTER TABLE `roles` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SELECT '✓ roles converted' as status;

ALTER TABLE `permissions` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SELECT '✓ permissions converted' as status;

-- Status tables
ALTER TABLE `status_unit` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SELECT '✓ status_unit converted' as status;

-- Reference tables
ALTER TABLE `jenis_perintah_kerja` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `tujuan_perintah_kerja` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `jenis_roda` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `kapasitas` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `tipe_unit` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `model_unit` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

SELECT '✓ Reference tables converted' as status;

-- ============================================================================
-- STEP 4: KONVERSI TABEL USERS & AUTH
-- ============================================================================
SELECT '=== CONVERTING USER TABLES ===' as status;

ALTER TABLE `users` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SELECT '✓ users converted' as status;

ALTER TABLE `user_sessions` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SELECT '✓ user_sessions converted' as status;

ALTER TABLE `user_otp` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SELECT '✓ user_otp converted' as status;

ALTER TABLE `login_attempts` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SELECT '✓ login_attempts converted' as status;

ALTER TABLE `user_roles` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `user_permissions` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `role_permissions` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

SELECT '✓ User auth tables converted' as status;

-- ============================================================================
-- STEP 5: KONVERSI TABEL MASTER DATA
-- ============================================================================
SELECT '=== CONVERTING MASTER DATA TABLES ===' as status;

ALTER TABLE `customers` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SELECT '✓ customers converted' as status;

ALTER TABLE `customer_locations` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SELECT '✓ customer_locations converted' as status;

ALTER TABLE `suppliers` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SELECT '✓ suppliers converted' as status;

ALTER TABLE `employees` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SELECT '✓ employees converted' as status;

ALTER TABLE `areas` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SELECT '✓ areas converted' as status;

-- ============================================================================
-- STEP 6: KONVERSI TABEL INVENTORY (HATI-HATI - DATA BESAR)
-- ============================================================================
SELECT '=== CONVERTING INVENTORY TABLES ===' as status;
SELECT 'WARNING: These tables may take several minutes...' as warning;

-- Inventory unit - TABEL BESAR
SELECT 'Converting inventory_unit... (may take 5-10 minutes)' as status;
ALTER TABLE `inventory_unit` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SELECT '✓ inventory_unit converted' as status;

-- Inventory attachment
SELECT 'Converting inventory_attachment...' as status;
ALTER TABLE `inventory_attachment` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SELECT '✓ inventory_attachment converted' as status;

-- Sparepart
SELECT 'Converting sparepart...' as status;
ALTER TABLE `sparepart` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SELECT '✓ sparepart converted' as status;

-- ============================================================================
-- STEP 7: KONVERSI TABEL TRANSAKSI (MAINTENANCE WINDOW!)
-- ============================================================================
SELECT '=== CONVERTING TRANSACTION TABLES ===' as status;
SELECT 'WARNING: Large tables - ensure maintenance window!' as warning;

-- Purchase Orders
SELECT 'Converting purchase_orders... (may take 10-15 minutes)' as status;
ALTER TABLE `purchase_orders` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SELECT '✓ purchase_orders converted' as status;

ALTER TABLE `po_units` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `po_sparepart_items` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `po_attachment` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SELECT '✓ PO related tables converted' as status;

-- Delivery Instructions
ALTER TABLE `delivery_instructions` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `delivery_items` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SELECT '✓ Delivery tables converted' as status;

-- Work Orders
SELECT 'Converting work_orders... (may take 5-10 minutes)' as status;
ALTER TABLE `work_orders` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `work_order_spareparts` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `work_order_categories` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `work_order_subcategories` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `work_order_priorities` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `work_order_statuses` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SELECT '✓ Work order tables converted' as status;

-- SPK
SELECT 'Converting spk...' as status;
ALTER TABLE `spk` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `spk_status_history` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SELECT '✓ SPK tables converted' as status;

-- Kontrak
ALTER TABLE `kontrak` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `kontrak_spesifikasi` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SELECT '✓ Kontrak tables converted' as status;

-- ============================================================================
-- STEP 8: KONVERSI TABEL LOG & AUDIT
-- ============================================================================
SELECT '=== CONVERTING LOG TABLES ===' as status;

-- System activity log - BISA LAMA
SELECT 'Converting system_activity_log... (may take 15-20 minutes)' as status;
ALTER TABLE `system_activity_log` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SELECT '✓ system_activity_log converted' as status;

-- Notifications
ALTER TABLE `notifications` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SELECT '✓ notifications converted' as status;

-- ============================================================================
-- STEP 9: VERIFIKASI KONVERSI
-- ============================================================================
SELECT '=== VERIFIKASI KONVERSI ===' as status;

-- Cek tabel yang belum terkonversi
SELECT 
    'REMAINING NON-UTF8MB4_UNICODE_CI TABLES' as status,
    TABLE_NAME,
    TABLE_COLLATION
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'optima_ci' 
    AND TABLE_COLLATION != 'utf8mb4_unicode_ci'
    AND TABLE_TYPE = 'BASE TABLE'
ORDER BY TABLE_NAME;

-- Cek kolom yang belum terkonversi
SELECT 
    'REMAINING NON-UTF8MB4_UNICODE_CI COLUMNS' as status,
    TABLE_NAME,
    COLUMN_NAME,
    COLLATION_NAME
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'optima_ci' 
    AND CHARACTER_SET_NAME IS NOT NULL
    AND COLLATION_NAME != 'utf8mb4_unicode_ci'
ORDER BY TABLE_NAME, COLUMN_NAME
LIMIT 10;

-- ============================================================================
-- STEP 10: UPDATE CONNECTION CONFIG
-- ============================================================================
SELECT '=== UPDATE CODEIGNITER CONFIG ===' as status;
SELECT 'MANUAL ACTION REQUIRED:' as action;
SELECT 'Update app/Config/Database.php:' as instruction;
SELECT "charset => 'utf8mb4'" as setting1;
SELECT "DBCollat => 'utf8mb4_unicode_ci'" as setting2;

SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;

SELECT '=== CHARSET STANDARDIZATION COMPLETED ===' as status;
SELECT 'All tables converted to utf8mb4_unicode_ci!' as message;
SELECT 'Unicode support enabled (emoji, international chars)' as benefit;
SELECT 'Ready for indexing optimization...' as next_step;