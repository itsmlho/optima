-- ======================================================================
-- SCRIPT OPTIMASI DATABASE LENGKAP SETELAH PRIMARY KEY DIPERBAIKI
-- ======================================================================
-- Tanggal: $(date)
-- Tujuan: Menambahkan Foreign Key Constraints, Index, dan Optimasi Database
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
-- BAGIAN 1: FOREIGN KEY CONSTRAINTS
-- ======================================================================

-- 1. delivery_items table constraints
SET @sql = 'ALTER TABLE delivery_items ADD CONSTRAINT fk_delivery_items_delivery 
           FOREIGN KEY (delivery_id) REFERENCES delivery (id) ON DELETE CASCADE ON UPDATE CASCADE';
SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE 
                         WHERE CONSTRAINT_SCHEMA = 'optima_db' 
                         AND TABLE_NAME = 'delivery_items' 
                         AND CONSTRAINT_NAME = 'fk_delivery_items_delivery');

IF @constraint_exists = 0 THEN
    SET @sql_check = CONCAT('SELECT COUNT(*) INTO @table_exists FROM information_schema.tables 
                           WHERE table_schema = "optima_db" AND table_name = "delivery_items"');
    PREPARE stmt FROM @sql_check;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    
    IF @table_exists > 0 THEN
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
        INSERT INTO optimization_log (operation_type, table_name, constraint_name, action, status) 
        VALUES ('FK_CONSTRAINT', 'delivery_items', 'fk_delivery_items_delivery', @sql, 'SUCCESS');
    END IF;
END IF;

-- 2. po_items table constraints
SET @sql = 'ALTER TABLE po_items ADD CONSTRAINT fk_po_items_purchase_orders 
           FOREIGN KEY (po_id) REFERENCES purchase_orders (id_po) ON DELETE CASCADE ON UPDATE CASCADE';
SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE 
                         WHERE CONSTRAINT_SCHEMA = 'optima_db' 
                         AND TABLE_NAME = 'po_items' 
                         AND CONSTRAINT_NAME = 'fk_po_items_purchase_orders');

IF @constraint_exists = 0 THEN
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    INSERT INTO optimization_log (operation_type, table_name, constraint_name, action, status) 
    VALUES ('FK_CONSTRAINT', 'po_items', 'fk_po_items_purchase_orders', @sql, 'SUCCESS');
END IF;

-- 3. po_items -> inventory_attachment
SET @sql = 'ALTER TABLE po_items ADD CONSTRAINT fk_po_items_attachment 
           FOREIGN KEY (attachment_id) REFERENCES inventory_attachment (id) ON DELETE SET NULL ON UPDATE CASCADE';
SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE 
                         WHERE CONSTRAINT_SCHEMA = 'optima_db' 
                         AND TABLE_NAME = 'po_items' 
                         AND CONSTRAINT_NAME = 'fk_po_items_attachment');

IF @constraint_exists = 0 THEN
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    INSERT INTO optimization_log (operation_type, table_name, constraint_name, action, status) 
    VALUES ('FK_CONSTRAINT', 'po_items', 'fk_po_items_attachment', @sql, 'SUCCESS');
END IF;

-- 4. po_sparepart_items table constraints
SET @sql = 'ALTER TABLE po_sparepart_items ADD CONSTRAINT fk_po_sparepart_items_purchase_orders 
           FOREIGN KEY (po_id) REFERENCES purchase_orders (id_po) ON DELETE CASCADE ON UPDATE CASCADE';
SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE 
                         WHERE CONSTRAINT_SCHEMA = 'optima_db' 
                         AND TABLE_NAME = 'po_sparepart_items' 
                         AND CONSTRAINT_NAME = 'fk_po_sparepart_items_purchase_orders');

IF @constraint_exists = 0 THEN
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    INSERT INTO optimization_log (operation_type, table_name, constraint_name, action, status) 
    VALUES ('FK_CONSTRAINT', 'po_sparepart_items', 'fk_po_sparepart_items_purchase_orders', @sql, 'SUCCESS');
END IF;

-- 5. po_units table constraints
SET @sql = 'ALTER TABLE po_units ADD CONSTRAINT fk_po_units_purchase_orders 
           FOREIGN KEY (po_id) REFERENCES purchase_orders (id_po) ON DELETE CASCADE ON UPDATE CASCADE';
SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE 
                         WHERE CONSTRAINT_SCHEMA = 'optima_db' 
                         AND TABLE_NAME = 'po_units' 
                         AND CONSTRAINT_NAME = 'fk_po_units_purchase_orders');

IF @constraint_exists = 0 THEN
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    INSERT INTO optimization_log (operation_type, table_name, constraint_name, action, status) 
    VALUES ('FK_CONSTRAINT', 'po_units', 'fk_po_units_purchase_orders', @sql, 'SUCCESS');
END IF;

-- 6. purchase_orders -> suppliers
SET @sql = 'ALTER TABLE purchase_orders ADD CONSTRAINT fk_purchase_orders_suppliers 
           FOREIGN KEY (supplier_id) REFERENCES suppliers (id_supplier) ON DELETE RESTRICT ON UPDATE CASCADE';
SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE 
                         WHERE CONSTRAINT_SCHEMA = 'optima_db' 
                         AND TABLE_NAME = 'purchase_orders' 
                         AND CONSTRAINT_NAME = 'fk_purchase_orders_suppliers');

IF @constraint_exists = 0 THEN
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    INSERT INTO optimization_log (operation_type, table_name, constraint_name, action, status) 
    VALUES ('FK_CONSTRAINT', 'purchase_orders', 'fk_purchase_orders_suppliers', @sql, 'SUCCESS');
END IF;

-- 7. inventory_attachment table constraints
SET @sql = 'ALTER TABLE inventory_attachment ADD CONSTRAINT fk_inventory_attachment_inventory_unit 
           FOREIGN KEY (id_inventory_unit) REFERENCES inventory_unit (id_inventory_unit) ON DELETE CASCADE ON UPDATE CASCADE';
SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE 
                         WHERE CONSTRAINT_SCHEMA = 'optima_db' 
                         AND TABLE_NAME = 'inventory_attachment' 
                         AND CONSTRAINT_NAME = 'fk_inventory_attachment_inventory_unit');

IF @constraint_exists = 0 THEN
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    INSERT INTO optimization_log (operation_type, table_name, constraint_name, action, status) 
    VALUES ('FK_CONSTRAINT', 'inventory_attachment', 'fk_inventory_attachment_inventory_unit', @sql, 'SUCCESS');
END IF;

-- 8. inventory_item_unit_log table constraints
SET @sql = 'ALTER TABLE inventory_item_unit_log ADD CONSTRAINT fk_inventory_item_unit_log_inventory_unit 
           FOREIGN KEY (id_inventory_unit) REFERENCES inventory_unit (id_inventory_unit) ON DELETE CASCADE ON UPDATE CASCADE';
SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE 
                         WHERE CONSTRAINT_SCHEMA = 'optima_db' 
                         AND TABLE_NAME = 'inventory_item_unit_log' 
                         AND CONSTRAINT_NAME = 'fk_inventory_item_unit_log_inventory_unit');

IF @constraint_exists = 0 THEN
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    INSERT INTO optimization_log (operation_type, table_name, constraint_name, action, status) 
    VALUES ('FK_CONSTRAINT', 'inventory_item_unit_log', 'fk_inventory_item_unit_log_inventory_unit', @sql, 'SUCCESS');
END IF;

-- 9. kontrak_spesifikasi table constraints
SET @sql = 'ALTER TABLE kontrak_spesifikasi ADD CONSTRAINT fk_kontrak_spesifikasi_kontrak 
           FOREIGN KEY (kontrak_id) REFERENCES kontrak (id) ON DELETE CASCADE ON UPDATE CASCADE';
SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE 
                         WHERE CONSTRAINT_SCHEMA = 'optima_db' 
                         AND TABLE_NAME = 'kontrak_spesifikasi' 
                         AND CONSTRAINT_NAME = 'fk_kontrak_spesifikasi_kontrak');

IF @constraint_exists = 0 THEN
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    INSERT INTO optimization_log (operation_type, table_name, constraint_name, action, status) 
    VALUES ('FK_CONSTRAINT', 'kontrak_spesifikasi', 'fk_kontrak_spesifikasi_kontrak', @sql, 'SUCCESS');
END IF;

-- 10. user_roles table constraints
SET @sql = 'ALTER TABLE user_roles ADD CONSTRAINT fk_user_roles_users 
           FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE';
SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE 
                         WHERE CONSTRAINT_SCHEMA = 'optima_db' 
                         AND TABLE_NAME = 'user_roles' 
                         AND CONSTRAINT_NAME = 'fk_user_roles_users');

IF @constraint_exists = 0 THEN
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    INSERT INTO optimization_log (operation_type, table_name, constraint_name, action, status) 
    VALUES ('FK_CONSTRAINT', 'user_roles', 'fk_user_roles_users', @sql, 'SUCCESS');
END IF;

-- 11. user_roles -> roles
SET @sql = 'ALTER TABLE user_roles ADD CONSTRAINT fk_user_roles_roles 
           FOREIGN KEY (role_id) REFERENCES roles (id) ON DELETE CASCADE ON UPDATE CASCADE';
SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE 
                         WHERE CONSTRAINT_SCHEMA = 'optima_db' 
                         AND TABLE_NAME = 'user_roles' 
                         AND CONSTRAINT_NAME = 'fk_user_roles_roles');

IF @constraint_exists = 0 THEN
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    INSERT INTO optimization_log (operation_type, table_name, constraint_name, action, status) 
    VALUES ('FK_CONSTRAINT', 'user_roles', 'fk_user_roles_roles', @sql, 'SUCCESS');
END IF;

-- 12. role_permissions table constraints
SET @sql = 'ALTER TABLE role_permissions ADD CONSTRAINT fk_role_permissions_roles 
           FOREIGN KEY (role_id) REFERENCES roles (id) ON DELETE CASCADE ON UPDATE CASCADE';
SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE 
                         WHERE CONSTRAINT_SCHEMA = 'optima_db' 
                         AND TABLE_NAME = 'role_permissions' 
                         AND CONSTRAINT_NAME = 'fk_role_permissions_roles');

IF @constraint_exists = 0 THEN
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    INSERT INTO optimization_log (operation_type, table_name, constraint_name, action, status) 
    VALUES ('FK_CONSTRAINT', 'role_permissions', 'fk_role_permissions_roles', @sql, 'SUCCESS');
END IF;

-- 13. role_permissions -> permissions
SET @sql = 'ALTER TABLE role_permissions ADD CONSTRAINT fk_role_permissions_permissions 
           FOREIGN KEY (permission_id) REFERENCES permissions (id) ON DELETE CASCADE ON UPDATE CASCADE';
SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE 
                         WHERE CONSTRAINT_SCHEMA = 'optima_db' 
                         AND TABLE_NAME = 'role_permissions' 
                         AND CONSTRAINT_NAME = 'fk_role_permissions_permissions');

IF @constraint_exists = 0 THEN
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    INSERT INTO optimization_log (operation_type, table_name, constraint_name, action, status) 
    VALUES ('FK_CONSTRAINT', 'role_permissions', 'fk_role_permissions_permissions', @sql, 'SUCCESS');
END IF;

-- 14. user_permissions table constraints
SET @sql = 'ALTER TABLE user_permissions ADD CONSTRAINT fk_user_permissions_users 
           FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE';
SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE 
                         WHERE CONSTRAINT_SCHEMA = 'optima_db' 
                         AND TABLE_NAME = 'user_permissions' 
                         AND CONSTRAINT_NAME = 'fk_user_permissions_users');

IF @constraint_exists = 0 THEN
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    INSERT INTO optimization_log (operation_type, table_name, constraint_name, action, status) 
    VALUES ('FK_CONSTRAINT', 'user_permissions', 'fk_user_permissions_users', @sql, 'SUCCESS');
END IF;

-- 15. user_permissions -> permissions
SET @sql = 'ALTER TABLE user_permissions ADD CONSTRAINT fk_user_permissions_permissions 
           FOREIGN KEY (permission_id) REFERENCES permissions (id) ON DELETE CASCADE ON UPDATE CASCADE';
SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE 
                         WHERE CONSTRAINT_SCHEMA = 'optima_db' 
                         AND TABLE_NAME = 'user_permissions' 
                         AND CONSTRAINT_NAME = 'fk_user_permissions_permissions');

IF @constraint_exists = 0 THEN
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    INSERT INTO optimization_log (operation_type, table_name, constraint_name, action, status) 
    VALUES ('FK_CONSTRAINT', 'user_permissions', 'fk_user_permissions_permissions', @sql, 'SUCCESS');
END IF;

-- ======================================================================
-- BAGIAN 2: PERFORMANCE INDEXES
-- ======================================================================

-- Index untuk pencarian berdasarkan status
CREATE INDEX IF NOT EXISTS idx_spk_status ON spk (status);
CREATE INDEX IF NOT EXISTS idx_delivery_status ON delivery (status);
CREATE INDEX IF NOT EXISTS idx_purchase_orders_status ON purchase_orders (status);

-- Index untuk pencarian berdasarkan tanggal
CREATE INDEX IF NOT EXISTS idx_spk_created_at ON spk (dibuat_pada);
CREATE INDEX IF NOT EXISTS idx_delivery_created_at ON delivery (created_at);
CREATE INDEX IF NOT EXISTS idx_po_created_at ON purchase_orders (created_at);

-- Index untuk relasi foreign key yang sering digunakan
CREATE INDEX IF NOT EXISTS idx_delivery_items_delivery_id ON delivery_items (delivery_id);
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
    error_message,
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
