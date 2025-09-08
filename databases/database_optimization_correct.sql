-- ======================================================================
-- SCRIPT OPTIMASI DATABASE FINAL - DENGAN NAMA KOLOM YANG BENAR
-- ======================================================================
-- Tanggal: 2025-09-03
-- Berdasarkan analisis struktur tabel yang sebenarnya
-- ======================================================================

USE optima_db;

-- Log table untuk mencatat proses optimasi
CREATE TABLE IF NOT EXISTS optimization_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    operation_type ENUM('FK_CONSTRAINT', 'INDEX', 'TRIGGER', 'PROCEDURE') NOT NULL,
    table_name VARCHAR(100),
    constraint_name VARCHAR(200),
    action VARCHAR(500),
    status ENUM('SUCCESS', 'ERROR', 'SKIPPED') NOT NULL,
    error_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ======================================================================
-- BAGIAN 1: FOREIGN KEY CONSTRAINTS DENGAN NAMA KOLOM YANG BENAR
-- ======================================================================

-- 1. delivery_items -> delivery_instructions (di_id)
ALTER TABLE delivery_items 
ADD CONSTRAINT fk_delivery_items_delivery_instructions 
FOREIGN KEY (di_id) REFERENCES delivery_instructions (id) 
ON DELETE CASCADE ON UPDATE CASCADE;

INSERT INTO optimization_log (operation_type, table_name, constraint_name, action, status) 
VALUES ('FK_CONSTRAINT', 'delivery_items', 'fk_delivery_items_delivery_instructions', 
        'delivery_items.di_id -> delivery_instructions.id', 'SUCCESS');

-- 2. po_items -> purchase_orders
ALTER TABLE po_items 
ADD CONSTRAINT fk_po_items_purchase_orders 
FOREIGN KEY (po_id) REFERENCES purchase_orders (id_po) 
ON DELETE CASCADE ON UPDATE CASCADE;

INSERT INTO optimization_log (operation_type, table_name, constraint_name, action, status) 
VALUES ('FK_CONSTRAINT', 'po_items', 'fk_po_items_purchase_orders', 
        'po_items.po_id -> purchase_orders.id_po', 'SUCCESS');

-- 3. po_items -> inventory_attachment
ALTER TABLE po_items 
ADD CONSTRAINT fk_po_items_attachment 
FOREIGN KEY (attachment_id) REFERENCES inventory_attachment (id) 
ON DELETE SET NULL ON UPDATE CASCADE;

INSERT INTO optimization_log (operation_type, table_name, constraint_name, action, status) 
VALUES ('FK_CONSTRAINT', 'po_items', 'fk_po_items_attachment', 
        'po_items.attachment_id -> inventory_attachment.id', 'SUCCESS');

-- 4. po_sparepart_items -> purchase_orders
ALTER TABLE po_sparepart_items 
ADD CONSTRAINT fk_po_sparepart_items_purchase_orders 
FOREIGN KEY (po_id) REFERENCES purchase_orders (id_po) 
ON DELETE CASCADE ON UPDATE CASCADE;

INSERT INTO optimization_log (operation_type, table_name, constraint_name, action, status) 
VALUES ('FK_CONSTRAINT', 'po_sparepart_items', 'fk_po_sparepart_items_purchase_orders', 
        'po_sparepart_items.po_id -> purchase_orders.id_po', 'SUCCESS');

-- 5. po_units -> purchase_orders
ALTER TABLE po_units 
ADD CONSTRAINT fk_po_units_purchase_orders 
FOREIGN KEY (po_id) REFERENCES purchase_orders (id_po) 
ON DELETE CASCADE ON UPDATE CASCADE;

INSERT INTO optimization_log (operation_type, table_name, constraint_name, action, status) 
VALUES ('FK_CONSTRAINT', 'po_units', 'fk_po_units_purchase_orders', 
        'po_units.po_id -> purchase_orders.id_po', 'SUCCESS');

-- 6. purchase_orders -> suppliers
ALTER TABLE purchase_orders 
ADD CONSTRAINT fk_purchase_orders_suppliers 
FOREIGN KEY (supplier_id) REFERENCES suppliers (id_supplier) 
ON DELETE RESTRICT ON UPDATE CASCADE;

INSERT INTO optimization_log (operation_type, table_name, constraint_name, action, status) 
VALUES ('FK_CONSTRAINT', 'purchase_orders', 'fk_purchase_orders_suppliers', 
        'purchase_orders.supplier_id -> suppliers.id_supplier', 'SUCCESS');

-- 7. inventory_attachment -> inventory_unit
ALTER TABLE inventory_attachment 
ADD CONSTRAINT fk_inventory_attachment_inventory_unit 
FOREIGN KEY (id_inventory_unit) REFERENCES inventory_unit (id_inventory_unit) 
ON DELETE CASCADE ON UPDATE CASCADE;

INSERT INTO optimization_log (operation_type, table_name, constraint_name, action, status) 
VALUES ('FK_CONSTRAINT', 'inventory_attachment', 'fk_inventory_attachment_inventory_unit', 
        'inventory_attachment.id_inventory_unit -> inventory_unit.id_inventory_unit', 'SUCCESS');

-- 8. inventory_item_unit_log -> inventory_unit
ALTER TABLE inventory_item_unit_log 
ADD CONSTRAINT fk_inventory_item_unit_log_inventory_unit 
FOREIGN KEY (id_inventory_unit) REFERENCES inventory_unit (id_inventory_unit) 
ON DELETE CASCADE ON UPDATE CASCADE;

INSERT INTO optimization_log (operation_type, table_name, constraint_name, action, status) 
VALUES ('FK_CONSTRAINT', 'inventory_item_unit_log', 'fk_inventory_item_unit_log_inventory_unit', 
        'inventory_item_unit_log.id_inventory_unit -> inventory_unit.id_inventory_unit', 'SUCCESS');

-- 9. inventory_item_unit_log -> inventory_attachment
ALTER TABLE inventory_item_unit_log 
ADD CONSTRAINT fk_inventory_item_unit_log_inventory_attachment 
FOREIGN KEY (id_inventory_attachment) REFERENCES inventory_attachment (id) 
ON DELETE CASCADE ON UPDATE CASCADE;

INSERT INTO optimization_log (operation_type, table_name, constraint_name, action, status) 
VALUES ('FK_CONSTRAINT', 'inventory_item_unit_log', 'fk_inventory_item_unit_log_inventory_attachment', 
        'inventory_item_unit_log.id_inventory_attachment -> inventory_attachment.id', 'SUCCESS');

-- 10. kontrak_spesifikasi -> kontrak
ALTER TABLE kontrak_spesifikasi 
ADD CONSTRAINT fk_kontrak_spesifikasi_kontrak 
FOREIGN KEY (kontrak_id) REFERENCES kontrak (id) 
ON DELETE CASCADE ON UPDATE CASCADE;

INSERT INTO optimization_log (operation_type, table_name, constraint_name, action, status) 
VALUES ('FK_CONSTRAINT', 'kontrak_spesifikasi', 'fk_kontrak_spesifikasi_kontrak', 
        'kontrak_spesifikasi.kontrak_id -> kontrak.id', 'SUCCESS');

-- 11. user_roles -> users
ALTER TABLE user_roles 
ADD CONSTRAINT fk_user_roles_users 
FOREIGN KEY (user_id) REFERENCES users (id) 
ON DELETE CASCADE ON UPDATE CASCADE;

INSERT INTO optimization_log (operation_type, table_name, constraint_name, action, status) 
VALUES ('FK_CONSTRAINT', 'user_roles', 'fk_user_roles_users', 
        'user_roles.user_id -> users.id', 'SUCCESS');

-- 12. user_roles -> roles
ALTER TABLE user_roles 
ADD CONSTRAINT fk_user_roles_roles 
FOREIGN KEY (role_id) REFERENCES roles (id) 
ON DELETE CASCADE ON UPDATE CASCADE;

INSERT INTO optimization_log (operation_type, table_name, constraint_name, action, status) 
VALUES ('FK_CONSTRAINT', 'user_roles', 'fk_user_roles_roles', 
        'user_roles.role_id -> roles.id', 'SUCCESS');

-- 13. role_permissions -> roles
ALTER TABLE role_permissions 
ADD CONSTRAINT fk_role_permissions_roles 
FOREIGN KEY (role_id) REFERENCES roles (id) 
ON DELETE CASCADE ON UPDATE CASCADE;

INSERT INTO optimization_log (operation_type, table_name, constraint_name, action, status) 
VALUES ('FK_CONSTRAINT', 'role_permissions', 'fk_role_permissions_roles', 
        'role_permissions.role_id -> roles.id', 'SUCCESS');

-- 14. role_permissions -> permissions
ALTER TABLE role_permissions 
ADD CONSTRAINT fk_role_permissions_permissions 
FOREIGN KEY (permission_id) REFERENCES permissions (id) 
ON DELETE CASCADE ON UPDATE CASCADE;

INSERT INTO optimization_log (operation_type, table_name, constraint_name, action, status) 
VALUES ('FK_CONSTRAINT', 'role_permissions', 'fk_role_permissions_permissions', 
        'role_permissions.permission_id -> permissions.id', 'SUCCESS');

-- 15. user_permissions -> users
ALTER TABLE user_permissions 
ADD CONSTRAINT fk_user_permissions_users 
FOREIGN KEY (user_id) REFERENCES users (id) 
ON DELETE CASCADE ON UPDATE CASCADE;

INSERT INTO optimization_log (operation_type, table_name, constraint_name, action, status) 
VALUES ('FK_CONSTRAINT', 'user_permissions', 'fk_user_permissions_users', 
        'user_permissions.user_id -> users.id', 'SUCCESS');

-- 16. user_permissions -> permissions
ALTER TABLE user_permissions 
ADD CONSTRAINT fk_user_permissions_permissions 
FOREIGN KEY (permission_id) REFERENCES permissions (id) 
ON DELETE CASCADE ON UPDATE CASCADE;

INSERT INTO optimization_log (operation_type, table_name, constraint_name, action, status) 
VALUES ('FK_CONSTRAINT', 'user_permissions', 'fk_user_permissions_permissions', 
        'user_permissions.permission_id -> permissions.id', 'SUCCESS');

-- 17. roles -> divisions (jika ada)
ALTER TABLE roles 
ADD CONSTRAINT fk_roles_divisions 
FOREIGN KEY (division_id) REFERENCES divisions (id) 
ON DELETE SET NULL ON UPDATE CASCADE;

INSERT INTO optimization_log (operation_type, table_name, constraint_name, action, status) 
VALUES ('FK_CONSTRAINT', 'roles', 'fk_roles_divisions', 
        'roles.division_id -> divisions.id', 'SUCCESS');

-- 18. user_permissions -> divisions (jika ada)
ALTER TABLE user_permissions 
ADD CONSTRAINT fk_user_permissions_divisions 
FOREIGN KEY (division_id) REFERENCES divisions (id) 
ON DELETE SET NULL ON UPDATE CASCADE;

INSERT INTO optimization_log (operation_type, table_name, constraint_name, action, status) 
VALUES ('FK_CONSTRAINT', 'user_permissions', 'fk_user_permissions_divisions', 
        'user_permissions.division_id -> divisions.id', 'SUCCESS');

-- 19. user_roles -> divisions (jika ada)
ALTER TABLE user_roles 
ADD CONSTRAINT fk_user_roles_divisions 
FOREIGN KEY (division_id) REFERENCES divisions (id) 
ON DELETE SET NULL ON UPDATE CASCADE;

INSERT INTO optimization_log (operation_type, table_name, constraint_name, action, status) 
VALUES ('FK_CONSTRAINT', 'user_roles', 'fk_user_roles_divisions', 
        'user_roles.division_id -> divisions.id', 'SUCCESS');

-- 20. delivery_items -> inventory_unit (unit_id)
ALTER TABLE delivery_items 
ADD CONSTRAINT fk_delivery_items_inventory_unit 
FOREIGN KEY (unit_id) REFERENCES inventory_unit (id_inventory_unit) 
ON DELETE SET NULL ON UPDATE CASCADE;

INSERT INTO optimization_log (operation_type, table_name, constraint_name, action, status) 
VALUES ('FK_CONSTRAINT', 'delivery_items', 'fk_delivery_items_inventory_unit', 
        'delivery_items.unit_id -> inventory_unit.id_inventory_unit', 'SUCCESS');

-- 21. delivery_items -> inventory_attachment (attachment_id)
ALTER TABLE delivery_items 
ADD CONSTRAINT fk_delivery_items_inventory_attachment 
FOREIGN KEY (attachment_id) REFERENCES inventory_attachment (id) 
ON DELETE SET NULL ON UPDATE CASCADE;

INSERT INTO optimization_log (operation_type, table_name, constraint_name, action, status) 
VALUES ('FK_CONSTRAINT', 'delivery_items', 'fk_delivery_items_inventory_attachment', 
        'delivery_items.attachment_id -> inventory_attachment.id', 'SUCCESS');

-- ======================================================================
-- BAGIAN 2: PERFORMANCE INDEXES
-- ======================================================================

-- Index untuk pencarian berdasarkan status
CREATE INDEX IF NOT EXISTS idx_spk_status ON spk (status);
CREATE INDEX IF NOT EXISTS idx_delivery_instructions_status ON delivery_instructions (status);
CREATE INDEX IF NOT EXISTS idx_purchase_orders_status ON purchase_orders (status);

-- Index untuk pencarian berdasarkan tanggal
CREATE INDEX IF NOT EXISTS idx_spk_created_at ON spk (dibuat_pada);
CREATE INDEX IF NOT EXISTS idx_delivery_instructions_created_at ON delivery_instructions (dibuat_pada);
CREATE INDEX IF NOT EXISTS idx_po_created_at ON purchase_orders (created_at);

-- Index untuk relasi foreign key yang sering digunakan
CREATE INDEX IF NOT EXISTS idx_delivery_items_di_id ON delivery_items (di_id);
CREATE INDEX IF NOT EXISTS idx_po_items_po_id ON po_items (po_id);
CREATE INDEX IF NOT EXISTS idx_po_units_po_id ON po_units (po_id);
CREATE INDEX IF NOT EXISTS idx_inventory_attachment_unit_id ON inventory_attachment (id_inventory_unit);

-- Index untuk pencarian user dan permission
CREATE INDEX IF NOT EXISTS idx_user_roles_user_id ON user_roles (user_id);
CREATE INDEX IF NOT EXISTS idx_user_roles_role_id ON user_roles (role_id);
CREATE INDEX IF NOT EXISTS idx_role_permissions_role_id ON role_permissions (role_id);
CREATE INDEX IF NOT EXISTS idx_user_permissions_user_id ON user_permissions (user_id);

-- Index untuk nomor kontrak dan SPK
CREATE INDEX IF NOT EXISTS idx_spk_nomor_spk ON spk (nomor_spk);
CREATE INDEX IF NOT EXISTS idx_kontrak_nomor_kontrak ON kontrak (nomor_kontrak);
CREATE INDEX IF NOT EXISTS idx_delivery_instructions_nomor_di ON delivery_instructions (nomor_di);

-- Index untuk pencarian supplier
CREATE INDEX IF NOT EXISTS idx_purchase_orders_supplier_id ON purchase_orders (supplier_id);

INSERT INTO optimization_log (operation_type, action, status) 
VALUES ('INDEX', 'Created performance indexes for key tables', 'SUCCESS');

-- ======================================================================
-- BAGIAN 3: LAPORAN HASIL OPTIMASI
-- ======================================================================

-- Tampilkan ringkasan optimasi
SELECT 
    'DATABASE OPTIMIZATION COMPLETED' as status,
    COUNT(*) as total_operations,
    SUM(CASE WHEN status = 'SUCCESS' THEN 1 ELSE 0 END) as success_count,
    SUM(CASE WHEN status = 'ERROR' THEN 1 ELSE 0 END) as error_count,
    SUM(CASE WHEN status = 'SKIPPED' THEN 1 ELSE 0 END) as skipped_count
FROM optimization_log
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MINUTE);

-- Detail hasil optimasi
SELECT 
    operation_type,
    table_name,
    constraint_name,
    status,
    created_at
FROM optimization_log 
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MINUTE)
ORDER BY created_at;

-- Verifikasi: Total Foreign Key yang ada sekarang
SELECT 
    COUNT(*) as total_foreign_keys,
    'Foreign keys created' as description
FROM information_schema.KEY_COLUMN_USAGE 
WHERE CONSTRAINT_SCHEMA = 'optima_db' 
AND REFERENCED_TABLE_NAME IS NOT NULL;

-- Tampilkan foreign key yang berhasil ditambahkan
SELECT 
    TABLE_NAME as 'Tabel',
    COLUMN_NAME as 'Kolom',
    CONSTRAINT_NAME as 'Nama FK',
    REFERENCED_TABLE_NAME as 'Tabel Rujukan',
    REFERENCED_COLUMN_NAME as 'Kolom Rujukan'
FROM information_schema.KEY_COLUMN_USAGE 
WHERE CONSTRAINT_SCHEMA = 'optima_db' 
AND REFERENCED_TABLE_NAME IS NOT NULL
ORDER BY TABLE_NAME, CONSTRAINT_NAME;
