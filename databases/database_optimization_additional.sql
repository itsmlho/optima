-- ======================================================================
-- SCRIPT OPTIMASI DATABASE TAMBAHAN - MELENGKAPI YANG BELUM ADA
-- ======================================================================
-- Tanggal: 2025-09-03
-- Status: Database sudah memiliki 23 FK, 53 tabel dengan PK, 89 index
-- Tujuan: Menambahkan FK dan Index yang masih kurang
-- ======================================================================

USE optima_db;

-- Log table untuk mencatat proses optimasi
CREATE TABLE IF NOT EXISTS optimization_additional_log (
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
-- TAMBAHKAN FOREIGN KEY YANG MASIH KURANG
-- ======================================================================

-- 1. po_sparepart_items -> purchase_orders (jika belum ada)
SET @fk_exists = (SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE 
                  WHERE CONSTRAINT_SCHEMA = 'optima_db' 
                  AND TABLE_NAME = 'po_sparepart_items' 
                  AND COLUMN_NAME = 'po_id'
                  AND REFERENCED_TABLE_NAME = 'purchase_orders');

-- Jika FK belum ada, tambahkan
SELECT @fk_exists as 'FK po_sparepart_items->purchase_orders exists';

-- 2. po_units -> purchase_orders (jika belum ada)
SET @fk_exists = (SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE 
                  WHERE CONSTRAINT_SCHEMA = 'optima_db' 
                  AND TABLE_NAME = 'po_units' 
                  AND COLUMN_NAME = 'po_id'
                  AND REFERENCED_TABLE_NAME = 'purchase_orders');

SELECT @fk_exists as 'FK po_units->purchase_orders exists';

-- 3. purchase_orders -> suppliers (jika belum ada)
SET @fk_exists = (SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE 
                  WHERE CONSTRAINT_SCHEMA = 'optima_db' 
                  AND TABLE_NAME = 'purchase_orders' 
                  AND COLUMN_NAME = 'supplier_id'
                  AND REFERENCED_TABLE_NAME = 'suppliers');

SELECT @fk_exists as 'FK purchase_orders->suppliers exists';

-- 4. user_roles -> users (jika belum ada)
SET @fk_exists = (SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE 
                  WHERE CONSTRAINT_SCHEMA = 'optima_db' 
                  AND TABLE_NAME = 'user_roles' 
                  AND COLUMN_NAME = 'user_id'
                  AND REFERENCED_TABLE_NAME = 'users');

SELECT @fk_exists as 'FK user_roles->users exists';

-- 5. user_roles -> roles (jika belum ada)
SET @fk_exists = (SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE 
                  WHERE CONSTRAINT_SCHEMA = 'optima_db' 
                  AND TABLE_NAME = 'user_roles' 
                  AND COLUMN_NAME = 'role_id'
                  AND REFERENCED_TABLE_NAME = 'roles');

SELECT @fk_exists as 'FK user_roles->roles exists';

-- 6. role_permissions -> roles (jika belum ada)
SET @fk_exists = (SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE 
                  WHERE CONSTRAINT_SCHEMA = 'optima_db' 
                  AND TABLE_NAME = 'role_permissions' 
                  AND COLUMN_NAME = 'role_id'
                  AND REFERENCED_TABLE_NAME = 'roles');

SELECT @fk_exists as 'FK role_permissions->roles exists';

-- 7. role_permissions -> permissions (jika belum ada)
SET @fk_exists = (SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE 
                  WHERE CONSTRAINT_SCHEMA = 'optima_db' 
                  AND TABLE_NAME = 'role_permissions' 
                  AND COLUMN_NAME = 'permission_id'
                  AND REFERENCED_TABLE_NAME = 'permissions');

SELECT @fk_exists as 'FK role_permissions->permissions exists';

-- 8. user_permissions -> users (jika belum ada)
SET @fk_exists = (SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE 
                  WHERE CONSTRAINT_SCHEMA = 'optima_db' 
                  AND TABLE_NAME = 'user_permissions' 
                  AND COLUMN_NAME = 'user_id'
                  AND REFERENCED_TABLE_NAME = 'users');

SELECT @fk_exists as 'FK user_permissions->users exists';

-- 9. user_permissions -> permissions (jika belum ada)
SET @fk_exists = (SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE 
                  WHERE CONSTRAINT_SCHEMA = 'optima_db' 
                  AND TABLE_NAME = 'user_permissions' 
                  AND COLUMN_NAME = 'permission_id'
                  AND REFERENCED_TABLE_NAME = 'permissions');

SELECT @fk_exists as 'FK user_permissions->permissions exists';

-- 10. inventory_item_unit_log -> inventory_attachment (jika belum ada)
SET @fk_exists = (SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE 
                  WHERE CONSTRAINT_SCHEMA = 'optima_db' 
                  AND TABLE_NAME = 'inventory_item_unit_log' 
                  AND COLUMN_NAME = 'id_inventory_attachment'
                  AND REFERENCED_TABLE_NAME = 'inventory_attachment');

SELECT @fk_exists as 'FK inventory_item_unit_log->inventory_attachment exists';

-- ======================================================================
-- TAMBAHKAN FK BARU YANG BELUM ADA SAMA SEKALI
-- ======================================================================

-- Hanya tambahkan jika FK belum ada, dengan pengecekan error handling
DELIMITER $$

-- 1. Coba tambahkan po_sparepart_items -> purchase_orders
BEGIN NOT ATOMIC
    DECLARE fk_count INT DEFAULT 0;
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION 
    BEGIN
        INSERT INTO optimization_additional_log (operation_type, table_name, constraint_name, action, status, error_message) 
        VALUES ('FK_CONSTRAINT', 'po_sparepart_items', 'fk_po_sparepart_items_purchase_orders', 
                'po_sparepart_items.po_id -> purchase_orders.id_po', 'ERROR', 'FK already exists or error');
    END;
    
    SELECT COUNT(*) INTO fk_count FROM information_schema.KEY_COLUMN_USAGE 
    WHERE CONSTRAINT_SCHEMA = 'optima_db' 
    AND TABLE_NAME = 'po_sparepart_items' 
    AND COLUMN_NAME = 'po_id'
    AND REFERENCED_TABLE_NAME = 'purchase_orders';
    
    IF fk_count = 0 THEN
        ALTER TABLE po_sparepart_items 
        ADD CONSTRAINT fk_po_sparepart_items_purchase_orders 
        FOREIGN KEY (po_id) REFERENCES purchase_orders (id_po) 
        ON DELETE CASCADE ON UPDATE CASCADE;
        
        INSERT INTO optimization_additional_log (operation_type, table_name, constraint_name, action, status) 
        VALUES ('FK_CONSTRAINT', 'po_sparepart_items', 'fk_po_sparepart_items_purchase_orders', 
                'po_sparepart_items.po_id -> purchase_orders.id_po', 'SUCCESS');
    ELSE
        INSERT INTO optimization_additional_log (operation_type, table_name, constraint_name, action, status) 
        VALUES ('FK_CONSTRAINT', 'po_sparepart_items', 'fk_po_sparepart_items_purchase_orders', 
                'po_sparepart_items.po_id -> purchase_orders.id_po', 'SKIPPED');
    END IF;
END$$

-- 2. Coba tambahkan po_units -> purchase_orders
BEGIN NOT ATOMIC
    DECLARE fk_count INT DEFAULT 0;
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION 
    BEGIN
        INSERT INTO optimization_additional_log (operation_type, table_name, constraint_name, action, status, error_message) 
        VALUES ('FK_CONSTRAINT', 'po_units', 'fk_po_units_purchase_orders', 
                'po_units.po_id -> purchase_orders.id_po', 'ERROR', 'FK already exists or error');
    END;
    
    SELECT COUNT(*) INTO fk_count FROM information_schema.KEY_COLUMN_USAGE 
    WHERE CONSTRAINT_SCHEMA = 'optima_db' 
    AND TABLE_NAME = 'po_units' 
    AND COLUMN_NAME = 'po_id'
    AND REFERENCED_TABLE_NAME = 'purchase_orders';
    
    IF fk_count = 0 THEN
        ALTER TABLE po_units 
        ADD CONSTRAINT fk_po_units_purchase_orders 
        FOREIGN KEY (po_id) REFERENCES purchase_orders (id_po) 
        ON DELETE CASCADE ON UPDATE CASCADE;
        
        INSERT INTO optimization_additional_log (operation_type, table_name, constraint_name, action, status) 
        VALUES ('FK_CONSTRAINT', 'po_units', 'fk_po_units_purchase_orders', 
                'po_units.po_id -> purchase_orders.id_po', 'SUCCESS');
    ELSE
        INSERT INTO optimization_additional_log (operation_type, table_name, constraint_name, action, status) 
        VALUES ('FK_CONSTRAINT', 'po_units', 'fk_po_units_purchase_orders', 
                'po_units.po_id -> purchase_orders.id_po', 'SKIPPED');
    END IF;
END$$

-- 3. Coba tambahkan purchase_orders -> suppliers
BEGIN NOT ATOMIC
    DECLARE fk_count INT DEFAULT 0;
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION 
    BEGIN
        INSERT INTO optimization_additional_log (operation_type, table_name, constraint_name, action, status, error_message) 
        VALUES ('FK_CONSTRAINT', 'purchase_orders', 'fk_purchase_orders_suppliers', 
                'purchase_orders.supplier_id -> suppliers.id_supplier', 'ERROR', 'FK already exists or error');
    END;
    
    SELECT COUNT(*) INTO fk_count FROM information_schema.KEY_COLUMN_USAGE 
    WHERE CONSTRAINT_SCHEMA = 'optima_db' 
    AND TABLE_NAME = 'purchase_orders' 
    AND COLUMN_NAME = 'supplier_id'
    AND REFERENCED_TABLE_NAME = 'suppliers';
    
    IF fk_count = 0 THEN
        ALTER TABLE purchase_orders 
        ADD CONSTRAINT fk_purchase_orders_suppliers 
        FOREIGN KEY (supplier_id) REFERENCES suppliers (id_supplier) 
        ON DELETE RESTRICT ON UPDATE CASCADE;
        
        INSERT INTO optimization_additional_log (operation_type, table_name, constraint_name, action, status) 
        VALUES ('FK_CONSTRAINT', 'purchase_orders', 'fk_purchase_orders_suppliers', 
                'purchase_orders.supplier_id -> suppliers.id_supplier', 'SUCCESS');
    ELSE
        INSERT INTO optimization_additional_log (operation_type, table_name, constraint_name, action, status) 
        VALUES ('FK_CONSTRAINT', 'purchase_orders', 'fk_purchase_orders_suppliers', 
                'purchase_orders.supplier_id -> suppliers.id_supplier', 'SKIPPED');
    END IF;
END$$

DELIMITER ;

-- ======================================================================
-- TAMBAHKAN INDEX PERFORMANCE YANG MASIH KURANG
-- ======================================================================

-- Index untuk pencarian berdasarkan status
CREATE INDEX IF NOT EXISTS idx_purchase_orders_status ON purchase_orders (status);
CREATE INDEX IF NOT EXISTS idx_po_items_status_verifikasi ON po_items (status_verifikasi);
CREATE INDEX IF NOT EXISTS idx_po_sparepart_status_verifikasi ON po_sparepart_items (status_verifikasi);

-- Index untuk pencarian berdasarkan tanggal
CREATE INDEX IF NOT EXISTS idx_po_tanggal_po ON purchase_orders (tanggal_po);
CREATE INDEX IF NOT EXISTS idx_po_items_created_at ON po_items (created_at);

-- Index untuk relasi foreign key yang sering digunakan  
CREATE INDEX IF NOT EXISTS idx_po_sparepart_items_po_id ON po_sparepart_items (po_id);
CREATE INDEX IF NOT EXISTS idx_purchase_orders_supplier_id ON purchase_orders (supplier_id);

-- Index untuk pencarian nomor/identifier
CREATE INDEX IF NOT EXISTS idx_purchase_orders_no_po ON purchase_orders (no_po);
CREATE INDEX IF NOT EXISTS idx_purchase_orders_invoice_no ON purchase_orders (invoice_no);

-- Index untuk komponen user management system
CREATE INDEX IF NOT EXISTS idx_user_roles_user_id ON user_roles (user_id);
CREATE INDEX IF NOT EXISTS idx_user_roles_role_id ON user_roles (role_id);
CREATE INDEX IF NOT EXISTS idx_role_permissions_role_id ON role_permissions (role_id);
CREATE INDEX IF NOT EXISTS idx_role_permissions_permission_id ON role_permissions (permission_id);
CREATE INDEX IF NOT EXISTS idx_user_permissions_user_id ON user_permissions (user_id);
CREATE INDEX IF NOT EXISTS idx_user_permissions_permission_id ON user_permissions (permission_id);

INSERT INTO optimization_additional_log (operation_type, action, status) 
VALUES ('INDEX', 'Created additional performance indexes', 'SUCCESS');

-- ======================================================================
-- LAPORAN HASIL OPTIMASI TAMBAHAN
-- ======================================================================

-- Tampilkan ringkasan optimasi tambahan
SELECT 
    'ADDITIONAL DATABASE OPTIMIZATION COMPLETED' as status,
    COUNT(*) as total_operations,
    SUM(CASE WHEN status = 'SUCCESS' THEN 1 ELSE 0 END) as success_count,
    SUM(CASE WHEN status = 'ERROR' THEN 1 ELSE 0 END) as error_count,
    SUM(CASE WHEN status = 'SKIPPED' THEN 1 ELSE 0 END) as skipped_count
FROM optimization_additional_log
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MINUTE);

-- Detail hasil optimasi tambahan
SELECT 
    operation_type,
    table_name,
    constraint_name,
    status,
    error_message,
    created_at
FROM optimization_additional_log 
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MINUTE)
ORDER BY created_at;

-- Status akhir database
SELECT 
    (SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE 
     WHERE CONSTRAINT_SCHEMA = 'optima_db' AND REFERENCED_TABLE_NAME IS NOT NULL) as 'Total FK',
    (SELECT COUNT(*) FROM information_schema.tables 
     WHERE table_schema = 'optima_db' AND table_type = 'BASE TABLE') as 'Total Tabel',
    (SELECT COUNT(*) FROM information_schema.statistics 
     WHERE table_schema = 'optima_db') as 'Total Index';

-- Tampilkan foreign key terakhir
SELECT 
    'FOREIGN KEYS FINAL LIST' as section,
    TABLE_NAME as 'Tabel',
    COLUMN_NAME as 'Kolom',
    REFERENCED_TABLE_NAME as 'Rujukan',
    REFERENCED_COLUMN_NAME as 'Kolom Rujukan'
FROM information_schema.KEY_COLUMN_USAGE 
WHERE CONSTRAINT_SCHEMA = 'optima_db' 
AND REFERENCED_TABLE_NAME IS NOT NULL
ORDER BY TABLE_NAME;
