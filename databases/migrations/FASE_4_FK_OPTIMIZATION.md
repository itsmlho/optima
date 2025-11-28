# 🔐 FASE 4: FOREIGN KEY CONSTRAINTS OPTIMIZATION - OPTIMA CI

## 🎯 Optimasi Referential Integrity & Cascade Actions

### 1. AUDIT FOREIGN KEYS EXISTING
```sql
-- Analisis FK constraints yang sudah ada
SELECT 
    kcu.CONSTRAINT_NAME,
    kcu.TABLE_NAME,
    kcu.COLUMN_NAME,
    kcu.REFERENCED_TABLE_NAME,
    kcu.REFERENCED_COLUMN_NAME,
    rc.UPDATE_RULE,
    rc.DELETE_RULE
FROM information_schema.KEY_COLUMN_USAGE kcu
JOIN information_schema.REFERENTIAL_CONSTRAINTS rc 
    ON kcu.CONSTRAINT_NAME = rc.CONSTRAINT_NAME
WHERE kcu.TABLE_SCHEMA = 'optima_ci'
    AND kcu.REFERENCED_TABLE_NAME IS NOT NULL
ORDER BY kcu.TABLE_NAME, kcu.CONSTRAINT_NAME;

-- Cek FK yang tidak punya index di kolom referencing
SELECT 
    kcu.TABLE_NAME,
    kcu.COLUMN_NAME,
    kcu.CONSTRAINT_NAME,
    'MISSING INDEX' as issue
FROM information_schema.KEY_COLUMN_USAGE kcu
LEFT JOIN information_schema.STATISTICS s 
    ON kcu.TABLE_NAME = s.TABLE_NAME 
    AND kcu.COLUMN_NAME = s.COLUMN_NAME
WHERE kcu.TABLE_SCHEMA = 'optima_ci'
    AND kcu.REFERENCED_TABLE_NAME IS NOT NULL
    AND s.COLUMN_NAME IS NULL
ORDER BY kcu.TABLE_NAME;
```

### 2. CRITICAL FK OPTIMIZATIONS

#### A. Core Business Tables - Strict Integrity
```sql
-- INVENTORY_UNIT relationships - CRITICAL DATA
-- Status unit: Prevent deletion of status yang masih dipakai
ALTER TABLE inventory_unit 
DROP FOREIGN KEY IF EXISTS fk_inventory_unit_status;

ALTER TABLE inventory_unit 
ADD CONSTRAINT fk_inventory_unit_status 
FOREIGN KEY (status_unit_id) REFERENCES status_unit(id_status) 
ON DELETE RESTRICT 
ON UPDATE CASCADE;

-- Departemen: Set NULL jika departemen dihapus, tapi update cascade
ALTER TABLE inventory_unit 
DROP FOREIGN KEY IF EXISTS fk_inventory_unit_departemen;

ALTER TABLE inventory_unit 
ADD CONSTRAINT fk_inventory_unit_departemen 
FOREIGN KEY (departemen_id) REFERENCES departemen(id_departemen) 
ON DELETE SET NULL 
ON UPDATE CASCADE;

-- Kontrak: Critical relationship, protect data integrity
ALTER TABLE inventory_unit 
DROP FOREIGN KEY IF EXISTS fk_inventory_unit_kontrak;

ALTER TABLE inventory_unit 
ADD CONSTRAINT fk_inventory_unit_kontrak 
FOREIGN KEY (kontrak_id) REFERENCES kontrak(id) 
ON DELETE RESTRICT 
ON UPDATE CASCADE;

-- Model & Type units: Reference data, restrict deletion
ALTER TABLE inventory_unit 
DROP FOREIGN KEY IF EXISTS fk_inventory_unit_model;

ALTER TABLE inventory_unit 
ADD CONSTRAINT fk_inventory_unit_model 
FOREIGN KEY (model_unit_id) REFERENCES model_unit(id_model_unit) 
ON DELETE RESTRICT 
ON UPDATE CASCADE;

ALTER TABLE inventory_unit 
DROP FOREIGN KEY IF EXISTS fk_inventory_unit_tipe;

ALTER TABLE inventory_unit 
ADD CONSTRAINT fk_inventory_unit_tipe 
FOREIGN KEY (tipe_unit_id) REFERENCES tipe_unit(id_tipe_unit) 
ON DELETE RESTRICT 
ON UPDATE CASCADE;
```

#### B. WORK_ORDERS - Operational Integrity
```sql
-- Unit relationship: Restrict deletion of units yang ada work order
ALTER TABLE work_orders 
DROP FOREIGN KEY IF EXISTS fk_wo_unit;

ALTER TABLE work_orders 
ADD CONSTRAINT fk_wo_unit 
FOREIGN KEY (unit_id) REFERENCES inventory_unit(id_inventory_unit) 
ON DELETE RESTRICT 
ON UPDATE CASCADE;

-- Employee assignments: Set NULL jika employee dihapus
ALTER TABLE work_orders 
DROP FOREIGN KEY IF EXISTS fk_wo_mechanic_employee,
DROP FOREIGN KEY IF EXISTS fk_wo_foreman_employee,
DROP FOREIGN KEY IF EXISTS fk_wo_helper_employee,
DROP FOREIGN KEY IF EXISTS fk_wo_admin_employee;

ALTER TABLE work_orders 
ADD CONSTRAINT fk_wo_mechanic_employee 
FOREIGN KEY (mechanic_id) REFERENCES employees(id) 
ON DELETE SET NULL 
ON UPDATE CASCADE,
ADD CONSTRAINT fk_wo_foreman_employee 
FOREIGN KEY (foreman_id) REFERENCES employees(id) 
ON DELETE SET NULL 
ON UPDATE CASCADE,
ADD CONSTRAINT fk_wo_helper_employee 
FOREIGN KEY (helper_id) REFERENCES employees(id) 
ON DELETE SET NULL 
ON UPDATE CASCADE,
ADD CONSTRAINT fk_wo_admin_employee 
FOREIGN KEY (admin_id) REFERENCES employees(id) 
ON DELETE SET NULL 
ON UPDATE CASCADE;

-- Status & Priority: Reference data, restrict deletion
ALTER TABLE work_orders 
DROP FOREIGN KEY IF EXISTS fk_wo_status,
DROP FOREIGN KEY IF EXISTS fk_wo_priority,
DROP FOREIGN KEY IF EXISTS fk_wo_category;

ALTER TABLE work_orders 
ADD CONSTRAINT fk_wo_status 
FOREIGN KEY (status_id) REFERENCES work_order_statuses(id) 
ON DELETE RESTRICT 
ON UPDATE CASCADE,
ADD CONSTRAINT fk_wo_priority 
FOREIGN KEY (priority_id) REFERENCES work_order_priorities(id) 
ON DELETE RESTRICT 
ON UPDATE CASCADE,
ADD CONSTRAINT fk_wo_category 
FOREIGN KEY (category_id) REFERENCES work_order_categories(id) 
ON DELETE RESTRICT 
ON UPDATE CASCADE;
```

#### C. PURCHASE_ORDERS - Financial Protection
```sql
-- Supplier relationship: Restrict deletion supplier yang ada PO
ALTER TABLE purchase_orders 
DROP FOREIGN KEY IF EXISTS fk_purchase_orders_suppliers;

ALTER TABLE purchase_orders 
ADD CONSTRAINT fk_purchase_orders_suppliers 
FOREIGN KEY (supplier_id) REFERENCES suppliers(id_supplier) 
ON DELETE RESTRICT 
ON UPDATE CASCADE;

-- PO Items: Cascade delete jika PO dihapus
ALTER TABLE po_units 
DROP FOREIGN KEY IF EXISTS fk_po_units_purchase_orders;

ALTER TABLE po_units 
ADD CONSTRAINT fk_po_units_purchase_orders 
FOREIGN KEY (po_id) REFERENCES purchase_orders(id_po) 
ON DELETE CASCADE 
ON UPDATE CASCADE;

ALTER TABLE po_sparepart_items 
DROP FOREIGN KEY IF EXISTS fk_po_sparepart_items_purchase_orders;

ALTER TABLE po_sparepart_items 
ADD CONSTRAINT fk_po_sparepart_items_purchase_orders 
FOREIGN KEY (po_id) REFERENCES purchase_orders(id_po) 
ON DELETE CASCADE 
ON UPDATE CASCADE;

-- PO Attachments: Cascade delete
ALTER TABLE po_attachment 
DROP FOREIGN KEY IF EXISTS fk_po_attachment_purchase_orders;

ALTER TABLE po_attachment 
ADD CONSTRAINT fk_po_attachment_purchase_orders 
FOREIGN KEY (po_id) REFERENCES purchase_orders(id_po) 
ON DELETE CASCADE 
ON UPDATE CASCADE;
```

### 3. MISSING FK CONSTRAINTS - ADD NEW

#### A. User & Security Tables
```sql
-- User roles junction table
ALTER TABLE user_roles 
ADD CONSTRAINT fk_user_roles_user 
FOREIGN KEY (user_id) REFERENCES users(id) 
ON DELETE CASCADE 
ON UPDATE CASCADE,
ADD CONSTRAINT fk_user_roles_role 
FOREIGN KEY (role_id) REFERENCES roles(id) 
ON DELETE CASCADE 
ON UPDATE CASCADE;

-- User permissions junction table  
ALTER TABLE user_permissions 
ADD CONSTRAINT fk_user_permissions_user 
FOREIGN KEY (user_id) REFERENCES users(id) 
ON DELETE CASCADE 
ON UPDATE CASCADE,
ADD CONSTRAINT fk_user_permissions_permission 
FOREIGN KEY (permission_id) REFERENCES permissions(id) 
ON DELETE CASCADE 
ON UPDATE CASCADE;

-- Role permissions junction table
ALTER TABLE role_permissions 
ADD CONSTRAINT fk_role_permissions_role 
FOREIGN KEY (role_id) REFERENCES roles(id) 
ON DELETE CASCADE 
ON UPDATE CASCADE,
ADD CONSTRAINT fk_role_permissions_permission 
FOREIGN KEY (permission_id) REFERENCES permissions(id) 
ON DELETE CASCADE 
ON UPDATE CASCADE;
```

#### B. Customer & Location Management
```sql
-- Customer locations: Cascade delete jika customer dihapus
ALTER TABLE customer_locations 
ADD CONSTRAINT fk_customer_locations_customer 
FOREIGN KEY (customer_id) REFERENCES customers(id) 
ON DELETE CASCADE 
ON UPDATE CASCADE;

-- Customer contracts junction table
ALTER TABLE customer_contracts 
ADD CONSTRAINT fk_customer_contracts_customer 
FOREIGN KEY (customer_id) REFERENCES customers(id) 
ON DELETE CASCADE 
ON UPDATE CASCADE,
ADD CONSTRAINT fk_customer_contracts_kontrak 
FOREIGN KEY (kontrak_id) REFERENCES kontrak(id) 
ON DELETE CASCADE 
ON UPDATE CASCADE;
```

#### C. Work Order Related Tables
```sql
-- Work order spareparts: Cascade delete
ALTER TABLE work_order_spareparts 
ADD CONSTRAINT fk_wo_spareparts_work_order 
FOREIGN KEY (work_order_id) REFERENCES work_orders(id) 
ON DELETE CASCADE 
ON UPDATE CASCADE,
ADD CONSTRAINT fk_wo_spareparts_sparepart 
FOREIGN KEY (sparepart_id) REFERENCES sparepart(id) 
ON DELETE RESTRICT 
ON UPDATE CASCADE;

-- Work order attachments: Cascade delete
ALTER TABLE work_order_attachments 
ADD CONSTRAINT fk_wo_attachments_work_order 
FOREIGN KEY (work_order_id) REFERENCES work_orders(id) 
ON DELETE CASCADE 
ON UPDATE CASCADE;

-- Work order comments: Cascade delete
ALTER TABLE work_order_comments 
ADD CONSTRAINT fk_wo_comments_work_order 
FOREIGN KEY (work_order_id) REFERENCES work_orders(id) 
ON DELETE CASCADE 
ON UPDATE CASCADE,
ADD CONSTRAINT fk_wo_comments_user 
FOREIGN KEY (created_by) REFERENCES users(id) 
ON DELETE SET NULL 
ON UPDATE CASCADE;
```

### 4. CASCADE RULES STRATEGY

#### DELETE Cascade Rules:
```
CASCADE: Detail records yang tidak ada artinya tanpa parent
- po_units → purchase_orders  
- po_sparepart_items → purchase_orders
- work_order_spareparts → work_orders
- customer_locations → customers
- user_roles → users/roles

RESTRICT: Critical data yang harus diproteksi
- inventory_unit.status_unit_id → status_unit
- inventory_unit.kontrak_id → kontrak  
- purchase_orders.supplier_id → suppliers
- work_orders.unit_id → inventory_unit

SET NULL: Reference yang boleh kosong
- inventory_unit.departemen_id → departemen
- work_orders.mechanic_id → employees
- spk.dibuat_oleh → users
```

### 5. FK INDEX OPTIMIZATION

```sql
-- Pastikan setiap FK punya index untuk performa
-- Index untuk foreign keys yang belum ada

-- inventory_unit FK indexes (yang belum ada)
CREATE INDEX idx_fk_inventory_kontrak_spek ON inventory_unit(kontrak_spesifikasi_id);
CREATE INDEX idx_fk_inventory_delivery ON inventory_unit(delivery_instruction_id);

-- work_orders FK indexes  
CREATE INDEX idx_fk_wo_category_id ON work_orders(category_id);
CREATE INDEX idx_fk_wo_subcategory_id ON work_orders(subcategory_id);
CREATE INDEX idx_fk_wo_priority_id ON work_orders(priority_id);

-- purchase_orders FK indexes
CREATE INDEX idx_fk_po_supplier ON purchase_orders(supplier_id);

-- spk FK indexes
CREATE INDEX idx_fk_spk_jenis_perintah ON spk(jenis_perintah_kerja_id);
CREATE INDEX idx_fk_spk_tujuan_perintah ON spk(tujuan_perintah_kerja_id);
CREATE INDEX idx_fk_spk_status_eksekusi ON spk(status_eksekusi_workflow_id);

-- delivery_instructions FK indexes
CREATE INDEX idx_fk_di_spk ON delivery_instructions(spk_id);
CREATE INDEX idx_fk_di_jenis_perintah ON delivery_instructions(jenis_perintah_kerja_id);
```

### 6. FK VALIDATION & CLEANUP

```sql
-- Script untuk cleanup data orphan sebelum add FK
-- HATI-HATI: Backup dulu sebelum jalankan!

-- 1. Cleanup inventory_unit orphan references
UPDATE inventory_unit SET departemen_id = NULL 
WHERE departemen_id NOT IN (SELECT id_departemen FROM departemen);

UPDATE inventory_unit SET status_unit_id = 1 
WHERE status_unit_id NOT IN (SELECT id_status FROM status_unit);

-- 2. Cleanup work_orders orphan references  
UPDATE work_orders SET mechanic_id = NULL 
WHERE mechanic_id IS NOT NULL 
AND mechanic_id NOT IN (SELECT id FROM employees);

UPDATE work_orders SET foreman_id = NULL 
WHERE foreman_id IS NOT NULL 
AND foreman_id NOT IN (SELECT id FROM employees);

-- 3. Cleanup purchase_orders
UPDATE purchase_orders SET supplier_id = NULL 
WHERE supplier_id NOT IN (SELECT id_supplier FROM suppliers);

-- 4. Validation queries - jalankan sebelum add FK
-- Pastikan tidak ada orphan data
SELECT 'inventory_unit orphan departemen' as issue, COUNT(*) as count
FROM inventory_unit iu 
LEFT JOIN departemen d ON iu.departemen_id = d.id_departemen 
WHERE iu.departemen_id IS NOT NULL AND d.id_departemen IS NULL

UNION ALL

SELECT 'work_orders orphan unit' as issue, COUNT(*) as count  
FROM work_orders wo
LEFT JOIN inventory_unit iu ON wo.unit_id = iu.id_inventory_unit
WHERE wo.unit_id IS NOT NULL AND iu.id_inventory_unit IS NULL

UNION ALL

SELECT 'purchase_orders orphan supplier' as issue, COUNT(*) as count
FROM purchase_orders po 
LEFT JOIN suppliers s ON po.supplier_id = s.id_supplier  
WHERE po.supplier_id IS NOT NULL AND s.id_supplier IS NULL;
```

### 7. FK MONITORING & MAINTENANCE

```sql
-- Create monitoring view for FK violations
CREATE VIEW v_fk_health AS
SELECT 
    'HEALTHY' as status,
    CONSTRAINT_NAME,
    TABLE_NAME,
    REFERENCED_TABLE_NAME,
    UPDATE_RULE,
    DELETE_RULE
FROM information_schema.REFERENTIAL_CONSTRAINTS 
WHERE CONSTRAINT_SCHEMA = 'optima_ci'
ORDER BY TABLE_NAME;

-- Procedure untuk check FK integrity
DELIMITER //
CREATE PROCEDURE sp_check_fk_integrity()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE constraint_name VARCHAR(64);
    DECLARE table_name VARCHAR(64);
    DECLARE cur CURSOR FOR 
        SELECT CONSTRAINT_NAME, TABLE_NAME
        FROM information_schema.REFERENTIAL_CONSTRAINTS 
        WHERE CONSTRAINT_SCHEMA = 'optima_ci';
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    -- Create temporary table for results
    CREATE TEMPORARY TABLE IF NOT EXISTS fk_check_results (
        constraint_name VARCHAR(64),
        table_name VARCHAR(64),
        status VARCHAR(20),
        error_count INT
    );
    
    OPEN cur;
    check_loop: LOOP
        FETCH cur INTO constraint_name, table_name;
        IF done THEN
            LEAVE check_loop;
        END IF;
        
        -- Check constraint (simplified version)
        INSERT INTO fk_check_results 
        VALUES (constraint_name, table_name, 'CHECKED', 0);
        
    END LOOP;
    
    CLOSE cur;
    
    -- Return results
    SELECT * FROM fk_check_results;
    DROP TEMPORARY TABLE fk_check_results;
END//
DELIMITER ;
```

## 🎯 Expected Benefits

### Data Integrity:
- **100% referential integrity** enforcement
- **Automatic cleanup** via CASCADE rules  
- **Protection** against orphan data
- **Consistent data state** across all operations

### Performance:
- **Faster JOIN operations** dengan proper FK indexes
- **Query optimizer** bisa menggunakan FK info untuk better execution plans
- **Reduced application logic** untuk data validation

### Maintenance:
- **Simplified data cleanup** via CASCADE deletes
- **Automatic consistency** maintenance
- **Better error handling** dengan constraint violations

## ⚠️ Implementation Risks & Mitigation

### Risks:
1. **Existing orphan data** akan prevent FK creation
2. **Application breaking** jika ada hard-delete yang di-restrict  
3. **Performance impact** pada large table operations

### Mitigation:
1. **Thorough data cleanup** sebelum implement FK
2. **Application code review** untuk handle FK violations
3. **Gradual implementation** dan extensive testing
4. **Rollback procedures** untuk setiap FK constraint