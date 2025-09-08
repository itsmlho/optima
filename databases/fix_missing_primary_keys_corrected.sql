-- ======================================================================
-- SCRIPT PERBAIKAN PRIMARY KEY UNTUK TABEL YANG BENAR-BENAR BELUM MEMILIKINYA
-- ======================================================================
-- Tanggal: $(date)
-- Berdasarkan analisis struktur tabel yang sebenarnya
-- ======================================================================

USE optima_db;

-- Backup log untuk primary key fixes
CREATE TABLE IF NOT EXISTS primary_key_fixes_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    table_name VARCHAR(100),
    action VARCHAR(200),
    status ENUM('SUCCESS', 'ERROR', 'SKIPPED'),
    error_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 1. forklifts table - menggunakan forklift_id sebagai primary key
ALTER TABLE forklifts ADD PRIMARY KEY (forklift_id);
INSERT INTO primary_key_fixes_log (table_name, action, status) 
VALUES ('forklifts', 'Added PRIMARY KEY (forklift_id)', 'SUCCESS');

-- 2. inventory_item_unit_log table - sudah ada kolom id
ALTER TABLE inventory_item_unit_log ADD PRIMARY KEY (id);
INSERT INTO primary_key_fixes_log (table_name, action, status) 
VALUES ('inventory_item_unit_log', 'Added PRIMARY KEY (id)', 'SUCCESS');

-- 3. inventory_spareparts table - sudah ada kolom id
ALTER TABLE inventory_spareparts ADD PRIMARY KEY (id);
INSERT INTO primary_key_fixes_log (table_name, action, status) 
VALUES ('inventory_spareparts', 'Added PRIMARY KEY (id)', 'SUCCESS');

-- 4. inventory_unit_backup table - menggunakan id_inventory_unit
ALTER TABLE inventory_unit_backup ADD PRIMARY KEY (id_inventory_unit);
INSERT INTO primary_key_fixes_log (table_name, action, status) 
VALUES ('inventory_unit_backup', 'Added PRIMARY KEY (id_inventory_unit)', 'SUCCESS');

-- 5. migrations table - sudah ada kolom id
ALTER TABLE migrations ADD PRIMARY KEY (id);
INSERT INTO primary_key_fixes_log (table_name, action, status) 
VALUES ('migrations', 'Added PRIMARY KEY (id)', 'SUCCESS');

-- 6. permissions table - sudah ada kolom id
ALTER TABLE permissions ADD PRIMARY KEY (id);
INSERT INTO primary_key_fixes_log (table_name, action, status) 
VALUES ('permissions', 'Added PRIMARY KEY (id)', 'SUCCESS');

-- 7. po_items table - menggunakan id_po_item
ALTER TABLE po_items ADD PRIMARY KEY (id_po_item);
INSERT INTO primary_key_fixes_log (table_name, action, status) 
VALUES ('po_items', 'Added PRIMARY KEY (id_po_item)', 'SUCCESS');

-- 8. po_sparepart_items table - sudah ada kolom id
ALTER TABLE po_sparepart_items ADD PRIMARY KEY (id);
INSERT INTO primary_key_fixes_log (table_name, action, status) 
VALUES ('po_sparepart_items', 'Added PRIMARY KEY (id)', 'SUCCESS');

-- 9. po_units table - menggunakan id_po_unit
ALTER TABLE po_units ADD PRIMARY KEY (id_po_unit);
INSERT INTO primary_key_fixes_log (table_name, action, status) 
VALUES ('po_units', 'Added PRIMARY KEY (id_po_unit)', 'SUCCESS');

-- 10. purchase_orders table - menggunakan id_po
ALTER TABLE purchase_orders ADD PRIMARY KEY (id_po);
INSERT INTO primary_key_fixes_log (table_name, action, status) 
VALUES ('purchase_orders', 'Added PRIMARY KEY (id_po)', 'SUCCESS');

-- 11. rbac_audit_log table - sudah ada kolom id
ALTER TABLE rbac_audit_log ADD PRIMARY KEY (id);
INSERT INTO primary_key_fixes_log (table_name, action, status) 
VALUES ('rbac_audit_log', 'Added PRIMARY KEY (id)', 'SUCCESS');

-- 12. rentals table - menggunakan rental_id
ALTER TABLE rentals ADD PRIMARY KEY (rental_id);
INSERT INTO primary_key_fixes_log (table_name, action, status) 
VALUES ('rentals', 'Added PRIMARY KEY (rental_id)', 'SUCCESS');

-- 13. reports table - sudah ada kolom id
ALTER TABLE reports ADD PRIMARY KEY (id);
INSERT INTO primary_key_fixes_log (table_name, action, status) 
VALUES ('reports', 'Added PRIMARY KEY (id)', 'SUCCESS');

-- 14. roles table - sudah ada kolom id
ALTER TABLE roles ADD PRIMARY KEY (id);
INSERT INTO primary_key_fixes_log (table_name, action, status) 
VALUES ('roles', 'Added PRIMARY KEY (id)', 'SUCCESS');

-- 15. role_permissions table - sudah ada kolom id
ALTER TABLE role_permissions ADD PRIMARY KEY (id);
INSERT INTO primary_key_fixes_log (table_name, action, status) 
VALUES ('role_permissions', 'Added PRIMARY KEY (id)', 'SUCCESS');

-- 16. spk_backup_20250903 table - sudah ada kolom id
ALTER TABLE spk_backup_20250903 ADD PRIMARY KEY (id);
INSERT INTO primary_key_fixes_log (table_name, action, status) 
VALUES ('spk_backup_20250903', 'Added PRIMARY KEY (id)', 'SUCCESS');

-- 17. spk_status_history table - sudah ada kolom id
ALTER TABLE spk_status_history ADD PRIMARY KEY (id);
INSERT INTO primary_key_fixes_log (table_name, action, status) 
VALUES ('spk_status_history', 'Added PRIMARY KEY (id)', 'SUCCESS');

-- 18. spk_units table - sudah ada kolom id
ALTER TABLE spk_units ADD PRIMARY KEY (id);
INSERT INTO primary_key_fixes_log (table_name, action, status) 
VALUES ('spk_units', 'Added PRIMARY KEY (id)', 'SUCCESS');

-- 19. tipe_ban table - menggunakan id_ban
ALTER TABLE tipe_ban ADD PRIMARY KEY (id_ban);
INSERT INTO primary_key_fixes_log (table_name, action, status) 
VALUES ('tipe_ban', 'Added PRIMARY KEY (id_ban)', 'SUCCESS');

-- 20. tipe_mast table - menggunakan id_mast
ALTER TABLE tipe_mast ADD PRIMARY KEY (id_mast);
INSERT INTO primary_key_fixes_log (table_name, action, status) 
VALUES ('tipe_mast', 'Added PRIMARY KEY (id_mast)', 'SUCCESS');

-- 21. user_permissions table - sudah ada kolom id
ALTER TABLE user_permissions ADD PRIMARY KEY (id);
INSERT INTO primary_key_fixes_log (table_name, action, status) 
VALUES ('user_permissions', 'Added PRIMARY KEY (id)', 'SUCCESS');

-- 22. user_roles table - sudah ada kolom id
ALTER TABLE user_roles ADD PRIMARY KEY (id);
INSERT INTO primary_key_fixes_log (table_name, action, status) 
VALUES ('user_roles', 'Added PRIMARY KEY (id)', 'SUCCESS');

-- 23. valve table - menggunakan id_valve
ALTER TABLE valve ADD PRIMARY KEY (id_valve);
INSERT INTO primary_key_fixes_log (table_name, action, status) 
VALUES ('valve', 'Added PRIMARY KEY (id_valve)', 'SUCCESS');

-- Tampilkan hasil perbaikan
SELECT 
    'PRIMARY KEY FIXES COMPLETED' as status,
    COUNT(*) as total_fixes,
    SUM(CASE WHEN status = 'SUCCESS' THEN 1 ELSE 0 END) as success_count,
    SUM(CASE WHEN status = 'ERROR' THEN 1 ELSE 0 END) as error_count,
    SUM(CASE WHEN status = 'SKIPPED' THEN 1 ELSE 0 END) as skipped_count
FROM primary_key_fixes_log
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MINUTE);

-- Detail hasil
SELECT * FROM primary_key_fixes_log 
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MINUTE)
ORDER BY created_at;

-- Verifikasi: Cek tabel yang masih belum punya primary key
SELECT 
    t.table_name as 'Tabel Tanpa Primary Key'
FROM information_schema.tables t
WHERE t.table_schema = 'optima_db' 
AND t.table_type = 'BASE TABLE'
AND NOT EXISTS (
    SELECT 1 FROM information_schema.table_constraints tc
    WHERE tc.table_schema = 'optima_db' 
    AND tc.table_name = t.table_name 
    AND tc.constraint_type = 'PRIMARY KEY'
)
ORDER BY t.table_name;
