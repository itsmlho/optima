-- ======================================================================
-- SCRIPT PERBAIKAN PRIMARY KEY UNTUK SEMUA TABEL YANG BELUM MEMILIKINYA
-- ======================================================================
-- Tanggal: $(date)
-- Tujuan: Menambahkan primary key pada tabel yang belum memiliki untuk
--         mendukung foreign key constraints yang akan ditambahkan
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

-- 1. divisions table
DELIMITER $$
BEGIN NOT ATOMIC
    DECLARE table_exists INT DEFAULT 0;
    DECLARE has_pk INT DEFAULT 0;
    
    SELECT COUNT(*) INTO table_exists 
    FROM information_schema.tables 
    WHERE table_schema = 'optima_db' AND table_name = 'divisions';
    
    IF table_exists > 0 THEN
        SELECT COUNT(*) INTO has_pk 
        FROM information_schema.table_constraints 
        WHERE table_schema = 'optima_db' AND table_name = 'divisions' AND constraint_type = 'PRIMARY KEY';
        
        IF has_pk = 0 THEN
            ALTER TABLE divisions ADD PRIMARY KEY (id);
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('divisions', 'Added PRIMARY KEY (id)', 'SUCCESS');
        ELSE
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('divisions', 'PRIMARY KEY already exists', 'SKIPPED');
        END IF;
    END IF;
END$$
DELIMITER ;

-- 2. forklifts table
DELIMITER $$
BEGIN NOT ATOMIC
    DECLARE table_exists INT DEFAULT 0;
    DECLARE has_pk INT DEFAULT 0;
    
    SELECT COUNT(*) INTO table_exists 
    FROM information_schema.tables 
    WHERE table_schema = 'optima_db' AND table_name = 'forklifts';
    
    IF table_exists > 0 THEN
        SELECT COUNT(*) INTO has_pk 
        FROM information_schema.table_constraints 
        WHERE table_schema = 'optima_db' AND table_name = 'forklifts' AND constraint_type = 'PRIMARY KEY';
        
        IF has_pk = 0 THEN
            ALTER TABLE forklifts ADD PRIMARY KEY (id);
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('forklifts', 'Added PRIMARY KEY (id)', 'SUCCESS');
        ELSE
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('forklifts', 'PRIMARY KEY already exists', 'SKIPPED');
        END IF;
    END IF;
END$$
DELIMITER ;

-- 3. inventory_item_unit_log table
DELIMITER $$
BEGIN NOT ATOMIC
    DECLARE table_exists INT DEFAULT 0;
    DECLARE has_pk INT DEFAULT 0;
    
    SELECT COUNT(*) INTO table_exists 
    FROM information_schema.tables 
    WHERE table_schema = 'optima_db' AND table_name = 'inventory_item_unit_log';
    
    IF table_exists > 0 THEN
        SELECT COUNT(*) INTO has_pk 
        FROM information_schema.table_constraints 
        WHERE table_schema = 'optima_db' AND table_name = 'inventory_item_unit_log' AND constraint_type = 'PRIMARY KEY';
        
        IF has_pk = 0 THEN
            ALTER TABLE inventory_item_unit_log ADD PRIMARY KEY (id);
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('inventory_item_unit_log', 'Added PRIMARY KEY (id)', 'SUCCESS');
        ELSE
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('inventory_item_unit_log', 'PRIMARY KEY already exists', 'SKIPPED');
        END IF;
    END IF;
END$$
DELIMITER ;

-- 4. inventory_spareparts table
DELIMITER $$
BEGIN NOT ATOMIC
    DECLARE table_exists INT DEFAULT 0;
    DECLARE has_pk INT DEFAULT 0;
    
    SELECT COUNT(*) INTO table_exists 
    FROM information_schema.tables 
    WHERE table_schema = 'optima_db' AND table_name = 'inventory_spareparts';
    
    IF table_exists > 0 THEN
        SELECT COUNT(*) INTO has_pk 
        FROM information_schema.table_constraints 
        WHERE table_schema = 'optima_db' AND table_name = 'inventory_spareparts' AND constraint_type = 'PRIMARY KEY';
        
        IF has_pk = 0 THEN
            ALTER TABLE inventory_spareparts ADD PRIMARY KEY (id);
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('inventory_spareparts', 'Added PRIMARY KEY (id)', 'SUCCESS');
        ELSE
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('inventory_spareparts', 'PRIMARY KEY already exists', 'SKIPPED');
        END IF;
    END IF;
END$$
DELIMITER ;

-- 5. inventory_unit_backup table
DELIMITER $$
BEGIN NOT ATOMIC
    DECLARE table_exists INT DEFAULT 0;
    DECLARE has_pk INT DEFAULT 0;
    
    SELECT COUNT(*) INTO table_exists 
    FROM information_schema.tables 
    WHERE table_schema = 'optima_db' AND table_name = 'inventory_unit_backup';
    
    IF table_exists > 0 THEN
        SELECT COUNT(*) INTO has_pk 
        FROM information_schema.table_constraints 
        WHERE table_schema = 'optima_db' AND table_name = 'inventory_unit_backup' AND constraint_type = 'PRIMARY KEY';
        
        IF has_pk = 0 THEN
            ALTER TABLE inventory_unit_backup ADD PRIMARY KEY (id_inventory_unit);
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('inventory_unit_backup', 'Added PRIMARY KEY (id_inventory_unit)', 'SUCCESS');
        ELSE
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('inventory_unit_backup', 'PRIMARY KEY already exists', 'SKIPPED');
        END IF;
    END IF;
END$$
DELIMITER ;

-- 6. migrations table
DELIMITER $$
BEGIN NOT ATOMIC
    DECLARE table_exists INT DEFAULT 0;
    DECLARE has_pk INT DEFAULT 0;
    
    SELECT COUNT(*) INTO table_exists 
    FROM information_schema.tables 
    WHERE table_schema = 'optima_db' AND table_name = 'migrations';
    
    IF table_exists > 0 THEN
        SELECT COUNT(*) INTO has_pk 
        FROM information_schema.table_constraints 
        WHERE table_schema = 'optima_db' AND table_name = 'migrations' AND constraint_type = 'PRIMARY KEY';
        
        IF has_pk = 0 THEN
            ALTER TABLE migrations ADD PRIMARY KEY (id);
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('migrations', 'Added PRIMARY KEY (id)', 'SUCCESS');
        ELSE
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('migrations', 'PRIMARY KEY already exists', 'SKIPPED');
        END IF;
    END IF;
END$$
DELIMITER ;

-- 7. permissions table
DELIMITER $$
BEGIN NOT ATOMIC
    DECLARE table_exists INT DEFAULT 0;
    DECLARE has_pk INT DEFAULT 0;
    
    SELECT COUNT(*) INTO table_exists 
    FROM information_schema.tables 
    WHERE table_schema = 'optima_db' AND table_name = 'permissions';
    
    IF table_exists > 0 THEN
        SELECT COUNT(*) INTO has_pk 
        FROM information_schema.table_constraints 
        WHERE table_schema = 'optima_db' AND table_name = 'permissions' AND constraint_type = 'PRIMARY KEY';
        
        IF has_pk = 0 THEN
            ALTER TABLE permissions ADD PRIMARY KEY (id);
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('permissions', 'Added PRIMARY KEY (id)', 'SUCCESS');
        ELSE
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('permissions', 'PRIMARY KEY already exists', 'SKIPPED');
        END IF;
    END IF;
END$$
DELIMITER ;

-- 8. po_items table
DELIMITER $$
BEGIN NOT ATOMIC
    DECLARE table_exists INT DEFAULT 0;
    DECLARE has_pk INT DEFAULT 0;
    
    SELECT COUNT(*) INTO table_exists 
    FROM information_schema.tables 
    WHERE table_schema = 'optima_db' AND table_name = 'po_items';
    
    IF table_exists > 0 THEN
        SELECT COUNT(*) INTO has_pk 
        FROM information_schema.table_constraints 
        WHERE table_schema = 'optima_db' AND table_name = 'po_items' AND constraint_type = 'PRIMARY KEY';
        
        IF has_pk = 0 THEN
            ALTER TABLE po_items ADD PRIMARY KEY (id);
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('po_items', 'Added PRIMARY KEY (id)', 'SUCCESS');
        ELSE
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('po_items', 'PRIMARY KEY already exists', 'SKIPPED');
        END IF;
    END IF;
END$$
DELIMITER ;

-- 9. po_sparepart_items table
DELIMITER $$
BEGIN NOT ATOMIC
    DECLARE table_exists INT DEFAULT 0;
    DECLARE has_pk INT DEFAULT 0;
    
    SELECT COUNT(*) INTO table_exists 
    FROM information_schema.tables 
    WHERE table_schema = 'optima_db' AND table_name = 'po_sparepart_items';
    
    IF table_exists > 0 THEN
        SELECT COUNT(*) INTO has_pk 
        FROM information_schema.table_constraints 
        WHERE table_schema = 'optima_db' AND table_name = 'po_sparepart_items' AND constraint_type = 'PRIMARY KEY';
        
        IF has_pk = 0 THEN
            ALTER TABLE po_sparepart_items ADD PRIMARY KEY (id);
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('po_sparepart_items', 'Added PRIMARY KEY (id)', 'SUCCESS');
        ELSE
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('po_sparepart_items', 'PRIMARY KEY already exists', 'SKIPPED');
        END IF;
    END IF;
END$$
DELIMITER ;

-- 10. po_units table
DELIMITER $$
BEGIN NOT ATOMIC
    DECLARE table_exists INT DEFAULT 0;
    DECLARE has_pk INT DEFAULT 0;
    
    SELECT COUNT(*) INTO table_exists 
    FROM information_schema.tables 
    WHERE table_schema = 'optima_db' AND table_name = 'po_units';
    
    IF table_exists > 0 THEN
        SELECT COUNT(*) INTO has_pk 
        FROM information_schema.table_constraints 
        WHERE table_schema = 'optima_db' AND table_name = 'po_units' AND constraint_type = 'PRIMARY KEY';
        
        IF has_pk = 0 THEN
            ALTER TABLE po_units ADD PRIMARY KEY (id);
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('po_units', 'Added PRIMARY KEY (id)', 'SUCCESS');
        ELSE
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('po_units', 'PRIMARY KEY already exists', 'SKIPPED');
        END IF;
    END IF;
END$$
DELIMITER ;

-- 11. purchase_orders table
DELIMITER $$
BEGIN NOT ATOMIC
    DECLARE table_exists INT DEFAULT 0;
    DECLARE has_pk INT DEFAULT 0;
    
    SELECT COUNT(*) INTO table_exists 
    FROM information_schema.tables 
    WHERE table_schema = 'optima_db' AND table_name = 'purchase_orders';
    
    IF table_exists > 0 THEN
        SELECT COUNT(*) INTO has_pk 
        FROM information_schema.table_constraints 
        WHERE table_schema = 'optima_db' AND table_name = 'purchase_orders' AND constraint_type = 'PRIMARY KEY';
        
        IF has_pk = 0 THEN
            ALTER TABLE purchase_orders ADD PRIMARY KEY (id);
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('purchase_orders', 'Added PRIMARY KEY (id)', 'SUCCESS');
        ELSE
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('purchase_orders', 'PRIMARY KEY already exists', 'SKIPPED');
        END IF;
    END IF;
END$$
DELIMITER ;

-- 12. rbac_audit_log table
DELIMITER $$
BEGIN NOT ATOMIC
    DECLARE table_exists INT DEFAULT 0;
    DECLARE has_pk INT DEFAULT 0;
    
    SELECT COUNT(*) INTO table_exists 
    FROM information_schema.tables 
    WHERE table_schema = 'optima_db' AND table_name = 'rbac_audit_log';
    
    IF table_exists > 0 THEN
        SELECT COUNT(*) INTO has_pk 
        FROM information_schema.table_constraints 
        WHERE table_schema = 'optima_db' AND table_name = 'rbac_audit_log' AND constraint_type = 'PRIMARY KEY';
        
        IF has_pk = 0 THEN
            ALTER TABLE rbac_audit_log ADD PRIMARY KEY (id);
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('rbac_audit_log', 'Added PRIMARY KEY (id)', 'SUCCESS');
        ELSE
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('rbac_audit_log', 'PRIMARY KEY already exists', 'SKIPPED');
        END IF;
    END IF;
END$$
DELIMITER ;

-- 13. rentals table
DELIMITER $$
BEGIN NOT ATOMIC
    DECLARE table_exists INT DEFAULT 0;
    DECLARE has_pk INT DEFAULT 0;
    
    SELECT COUNT(*) INTO table_exists 
    FROM information_schema.tables 
    WHERE table_schema = 'optima_db' AND table_name = 'rentals';
    
    IF table_exists > 0 THEN
        SELECT COUNT(*) INTO has_pk 
        FROM information_schema.table_constraints 
        WHERE table_schema = 'optima_db' AND table_name = 'rentals' AND constraint_type = 'PRIMARY KEY';
        
        IF has_pk = 0 THEN
            ALTER TABLE rentals ADD PRIMARY KEY (id);
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('rentals', 'Added PRIMARY KEY (id)', 'SUCCESS');
        ELSE
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('rentals', 'PRIMARY KEY already exists', 'SKIPPED');
        END IF;
    END IF;
END$$
DELIMITER ;

-- 14. reports table
DELIMITER $$
BEGIN NOT ATOMIC
    DECLARE table_exists INT DEFAULT 0;
    DECLARE has_pk INT DEFAULT 0;
    
    SELECT COUNT(*) INTO table_exists 
    FROM information_schema.tables 
    WHERE table_schema = 'optima_db' AND table_name = 'reports';
    
    IF table_exists > 0 THEN
        SELECT COUNT(*) INTO has_pk 
        FROM information_schema.table_constraints 
        WHERE table_schema = 'optima_db' AND table_name = 'reports' AND constraint_type = 'PRIMARY KEY';
        
        IF has_pk = 0 THEN
            ALTER TABLE reports ADD PRIMARY KEY (id);
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('reports', 'Added PRIMARY KEY (id)', 'SUCCESS');
        ELSE
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('reports', 'PRIMARY KEY already exists', 'SKIPPED');
        END IF;
    END IF;
END$$
DELIMITER ;

-- 15. roles table
DELIMITER $$
BEGIN NOT ATOMIC
    DECLARE table_exists INT DEFAULT 0;
    DECLARE has_pk INT DEFAULT 0;
    
    SELECT COUNT(*) INTO table_exists 
    FROM information_schema.tables 
    WHERE table_schema = 'optima_db' AND table_name = 'roles';
    
    IF table_exists > 0 THEN
        SELECT COUNT(*) INTO has_pk 
        FROM information_schema.table_constraints 
        WHERE table_schema = 'optima_db' AND table_name = 'roles' AND constraint_type = 'PRIMARY KEY';
        
        IF has_pk = 0 THEN
            ALTER TABLE roles ADD PRIMARY KEY (id);
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('roles', 'Added PRIMARY KEY (id)', 'SUCCESS');
        ELSE
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('roles', 'PRIMARY KEY already exists', 'SKIPPED');
        END IF;
    END IF;
END$$
DELIMITER ;

-- 16. role_permissions table
DELIMITER $$
BEGIN NOT ATOMIC
    DECLARE table_exists INT DEFAULT 0;
    DECLARE has_pk INT DEFAULT 0;
    
    SELECT COUNT(*) INTO table_exists 
    FROM information_schema.tables 
    WHERE table_schema = 'optima_db' AND table_name = 'role_permissions';
    
    IF table_exists > 0 THEN
        SELECT COUNT(*) INTO has_pk 
        FROM information_schema.table_constraints 
        WHERE table_schema = 'optima_db' AND table_name = 'role_permissions' AND constraint_type = 'PRIMARY KEY';
        
        IF has_pk = 0 THEN
            ALTER TABLE role_permissions ADD PRIMARY KEY (id);
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('role_permissions', 'Added PRIMARY KEY (id)', 'SUCCESS');
        ELSE
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('role_permissions', 'PRIMARY KEY already exists', 'SKIPPED');
        END IF;
    END IF;
END$$
DELIMITER ;

-- 17. spk_backup_20250903 table
DELIMITER $$
BEGIN NOT ATOMIC
    DECLARE table_exists INT DEFAULT 0;
    DECLARE has_pk INT DEFAULT 0;
    
    SELECT COUNT(*) INTO table_exists 
    FROM information_schema.tables 
    WHERE table_schema = 'optima_db' AND table_name = 'spk_backup_20250903';
    
    IF table_exists > 0 THEN
        SELECT COUNT(*) INTO has_pk 
        FROM information_schema.table_constraints 
        WHERE table_schema = 'optima_db' AND table_name = 'spk_backup_20250903' AND constraint_type = 'PRIMARY KEY';
        
        IF has_pk = 0 THEN
            ALTER TABLE spk_backup_20250903 ADD PRIMARY KEY (id);
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('spk_backup_20250903', 'Added PRIMARY KEY (id)', 'SUCCESS');
        ELSE
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('spk_backup_20250903', 'PRIMARY KEY already exists', 'SKIPPED');
        END IF;
    END IF;
END$$
DELIMITER ;

-- 18. spk_status_history table
DELIMITER $$
BEGIN NOT ATOMIC
    DECLARE table_exists INT DEFAULT 0;
    DECLARE has_pk INT DEFAULT 0;
    
    SELECT COUNT(*) INTO table_exists 
    FROM information_schema.tables 
    WHERE table_schema = 'optima_db' AND table_name = 'spk_status_history';
    
    IF table_exists > 0 THEN
        SELECT COUNT(*) INTO has_pk 
        FROM information_schema.table_constraints 
        WHERE table_schema = 'optima_db' AND table_name = 'spk_status_history' AND constraint_type = 'PRIMARY KEY';
        
        IF has_pk = 0 THEN
            ALTER TABLE spk_status_history ADD PRIMARY KEY (id);
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('spk_status_history', 'Added PRIMARY KEY (id)', 'SUCCESS');
        ELSE
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('spk_status_history', 'PRIMARY KEY already exists', 'SKIPPED');
        END IF;
    END IF;
END$$
DELIMITER ;

-- 19. spk_units table
DELIMITER $$
BEGIN NOT ATOMIC
    DECLARE table_exists INT DEFAULT 0;
    DECLARE has_pk INT DEFAULT 0;
    
    SELECT COUNT(*) INTO table_exists 
    FROM information_schema.tables 
    WHERE table_schema = 'optima_db' AND table_name = 'spk_units';
    
    IF table_exists > 0 THEN
        SELECT COUNT(*) INTO has_pk 
        FROM information_schema.table_constraints 
        WHERE table_schema = 'optima_db' AND table_name = 'spk_units' AND constraint_type = 'PRIMARY KEY';
        
        IF has_pk = 0 THEN
            ALTER TABLE spk_units ADD PRIMARY KEY (id);
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('spk_units', 'Added PRIMARY KEY (id)', 'SUCCESS');
        ELSE
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('spk_units', 'PRIMARY KEY already exists', 'SKIPPED');
        END IF;
    END IF;
END$$
DELIMITER ;

-- 20. tipe_ban table
DELIMITER $$
BEGIN NOT ATOMIC
    DECLARE table_exists INT DEFAULT 0;
    DECLARE has_pk INT DEFAULT 0;
    
    SELECT COUNT(*) INTO table_exists 
    FROM information_schema.tables 
    WHERE table_schema = 'optima_db' AND table_name = 'tipe_ban';
    
    IF table_exists > 0 THEN
        SELECT COUNT(*) INTO has_pk 
        FROM information_schema.table_constraints 
        WHERE table_schema = 'optima_db' AND table_name = 'tipe_ban' AND constraint_type = 'PRIMARY KEY';
        
        IF has_pk = 0 THEN
            ALTER TABLE tipe_ban ADD PRIMARY KEY (id);
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('tipe_ban', 'Added PRIMARY KEY (id)', 'SUCCESS');
        ELSE
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('tipe_ban', 'PRIMARY KEY already exists', 'SKIPPED');
        END IF;
    END IF;
END$$
DELIMITER ;

-- 21. tipe_mast table
DELIMITER $$
BEGIN NOT ATOMIC
    DECLARE table_exists INT DEFAULT 0;
    DECLARE has_pk INT DEFAULT 0;
    
    SELECT COUNT(*) INTO table_exists 
    FROM information_schema.tables 
    WHERE table_schema = 'optima_db' AND table_name = 'tipe_mast';
    
    IF table_exists > 0 THEN
        SELECT COUNT(*) INTO has_pk 
        FROM information_schema.table_constraints 
        WHERE table_schema = 'optima_db' AND table_name = 'tipe_mast' AND constraint_type = 'PRIMARY KEY';
        
        IF has_pk = 0 THEN
            ALTER TABLE tipe_mast ADD PRIMARY KEY (id);
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('tipe_mast', 'Added PRIMARY KEY (id)', 'SUCCESS');
        ELSE
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('tipe_mast', 'PRIMARY KEY already exists', 'SKIPPED');
        END IF;
    END IF;
END$$
DELIMITER ;

-- 22. user_permissions table
DELIMITER $$
BEGIN NOT ATOMIC
    DECLARE table_exists INT DEFAULT 0;
    DECLARE has_pk INT DEFAULT 0;
    
    SELECT COUNT(*) INTO table_exists 
    FROM information_schema.tables 
    WHERE table_schema = 'optima_db' AND table_name = 'user_permissions';
    
    IF table_exists > 0 THEN
        SELECT COUNT(*) INTO has_pk 
        FROM information_schema.table_constraints 
        WHERE table_schema = 'optima_db' AND table_name = 'user_permissions' AND constraint_type = 'PRIMARY KEY';
        
        IF has_pk = 0 THEN
            ALTER TABLE user_permissions ADD PRIMARY KEY (id);
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('user_permissions', 'Added PRIMARY KEY (id)', 'SUCCESS');
        ELSE
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('user_permissions', 'PRIMARY KEY already exists', 'SKIPPED');
        END IF;
    END IF;
END$$
DELIMITER ;

-- 23. user_roles table
DELIMITER $$
BEGIN NOT ATOMIC
    DECLARE table_exists INT DEFAULT 0;
    DECLARE has_pk INT DEFAULT 0;
    
    SELECT COUNT(*) INTO table_exists 
    FROM information_schema.tables 
    WHERE table_schema = 'optima_db' AND table_name = 'user_roles';
    
    IF table_exists > 0 THEN
        SELECT COUNT(*) INTO has_pk 
        FROM information_schema.table_constraints 
        WHERE table_schema = 'optima_db' AND table_name = 'user_roles' AND constraint_type = 'PRIMARY KEY';
        
        IF has_pk = 0 THEN
            ALTER TABLE user_roles ADD PRIMARY KEY (id);
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('user_roles', 'Added PRIMARY KEY (id)', 'SUCCESS');
        ELSE
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('user_roles', 'PRIMARY KEY already exists', 'SKIPPED');
        END IF;
    END IF;
END$$
DELIMITER ;

-- 24. valve table
DELIMITER $$
BEGIN NOT ATOMIC
    DECLARE table_exists INT DEFAULT 0;
    DECLARE has_pk INT DEFAULT 0;
    
    SELECT COUNT(*) INTO table_exists 
    FROM information_schema.tables 
    WHERE table_schema = 'optima_db' AND table_name = 'valve';
    
    IF table_exists > 0 THEN
        SELECT COUNT(*) INTO has_pk 
        FROM information_schema.table_constraints 
        WHERE table_schema = 'optima_db' AND table_name = 'valve' AND constraint_type = 'PRIMARY KEY';
        
        IF has_pk = 0 THEN
            ALTER TABLE valve ADD PRIMARY KEY (id);
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('valve', 'Added PRIMARY KEY (id)', 'SUCCESS');
        ELSE
            INSERT INTO primary_key_fixes_log (table_name, action, status) 
            VALUES ('valve', 'PRIMARY KEY already exists', 'SKIPPED');
        END IF;
    END IF;
END$$
DELIMITER ;

-- Tampilkan hasil perbaikan
SELECT 
    'PRIMARY KEY FIXES COMPLETED' as status,
    COUNT(*) as total_fixes,
    SUM(CASE WHEN status = 'SUCCESS' THEN 1 ELSE 0 END) as success_count,
    SUM(CASE WHEN status = 'ERROR' THEN 1 ELSE 0 END) as error_count,
    SUM(CASE WHEN status = 'SKIPPED' THEN 1 ELSE 0 END) as skipped_count
FROM primary_key_fixes_log;

-- Detail hasil
SELECT * FROM primary_key_fixes_log ORDER BY created_at;

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
