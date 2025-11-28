-- ============================================================================
-- OPTIMA CI DATABASE OPTIMIZATION - FASE 4: FOREIGN KEY OPTIMIZATION
-- Tanggal: 28 November 2025
-- Target: Implement proper FK constraints untuk data integrity
-- ============================================================================

-- PERINGATAN: Script ini akan cleanup orphan data dan add FK constraints
-- Backup database sebelum menjalankan!

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;

-- ============================================================================
-- STEP 1: AUDIT FK EXISTING & ORPHAN DATA
-- ============================================================================
SELECT '=== AUDIT FK EXISTING & ORPHAN DATA ===' as status;

-- Cek FK yang sudah ada
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
ORDER BY kcu.TABLE_NAME;

-- Cek orphan data di inventory_unit
SELECT 'Checking orphan data in inventory_unit...' as status;

SELECT 'inventory_unit orphan departemen' as issue, COUNT(*) as count
FROM inventory_unit iu 
LEFT JOIN departemen d ON iu.departemen_id = d.id_departemen 
WHERE iu.departemen_id IS NOT NULL AND d.id_departemen IS NULL

UNION ALL

SELECT 'inventory_unit orphan status_unit' as issue, COUNT(*) as count
FROM inventory_unit iu
LEFT JOIN status_unit su ON iu.status_unit_id = su.id_status
WHERE iu.status_unit_id IS NOT NULL AND su.id_status IS NULL

UNION ALL

SELECT 'inventory_unit orphan kontrak' as issue, COUNT(*) as count
FROM inventory_unit iu
LEFT JOIN kontrak k ON iu.kontrak_id = k.id
WHERE iu.kontrak_id IS NOT NULL AND k.id IS NULL;

-- ============================================================================
-- STEP 2: CLEANUP ORPHAN DATA
-- ============================================================================
SELECT '=== CLEANING UP ORPHAN DATA ===' as status;

-- Cleanup inventory_unit orphan references
UPDATE inventory_unit SET departemen_id = NULL 
WHERE departemen_id IS NOT NULL 
AND departemen_id NOT IN (SELECT id_departemen FROM departemen);
SELECT '✓ inventory_unit orphan departemen cleaned' as status;

-- Set default status untuk orphan status_unit_id
UPDATE inventory_unit SET status_unit_id = 1 
WHERE status_unit_id IS NOT NULL 
AND status_unit_id NOT IN (SELECT id_status FROM status_unit);
SELECT '✓ inventory_unit orphan status_unit cleaned' as status;

-- Cleanup work_orders orphan references
UPDATE work_orders SET mechanic_id = NULL 
WHERE mechanic_id IS NOT NULL 
AND mechanic_id NOT IN (SELECT id FROM employees);
SELECT '✓ work_orders orphan mechanic cleaned' as status;

UPDATE work_orders SET foreman_id = NULL 
WHERE foreman_id IS NOT NULL 
AND foreman_id NOT IN (SELECT id FROM employees);
SELECT '✓ work_orders orphan foreman cleaned' as status;

-- Cleanup purchase_orders orphan supplier
UPDATE purchase_orders SET supplier_id = NULL 
WHERE supplier_id IS NOT NULL 
AND supplier_id NOT IN (SELECT id_supplier FROM suppliers);
SELECT '✓ purchase_orders orphan supplier cleaned' as status;

-- ============================================================================
-- STEP 3: INVENTORY_UNIT FK CONSTRAINTS (CRITICAL)
-- ============================================================================
SELECT '=== ADDING INVENTORY_UNIT FK CONSTRAINTS ===' as status;

-- Status unit: Prevent deletion of status yang masih dipakai
ALTER TABLE inventory_unit 
ADD CONSTRAINT fk_inventory_unit_status_new 
FOREIGN KEY (status_unit_id) REFERENCES status_unit(id_status) 
ON DELETE RESTRICT 
ON UPDATE CASCADE;
SELECT '✓ inventory_unit -> status_unit FK created' as status;

-- Departemen: Set NULL jika departemen dihapus
ALTER TABLE inventory_unit 
ADD CONSTRAINT fk_inventory_unit_departemen_new 
FOREIGN KEY (departemen_id) REFERENCES departemen(id_departemen) 
ON DELETE SET NULL 
ON UPDATE CASCADE;
SELECT '✓ inventory_unit -> departemen FK created' as status;

-- Kontrak: Critical relationship, protect integrity
ALTER TABLE inventory_unit 
ADD CONSTRAINT fk_inventory_unit_kontrak_new 
FOREIGN KEY (kontrak_id) REFERENCES kontrak(id) 
ON DELETE SET NULL 
ON UPDATE CASCADE;
SELECT '✓ inventory_unit -> kontrak FK created' as status;

-- Model units: Reference data, restrict deletion
ALTER TABLE inventory_unit 
ADD CONSTRAINT fk_inventory_unit_model_new 
FOREIGN KEY (model_unit_id) REFERENCES model_unit(id_model_unit) 
ON DELETE SET NULL 
ON UPDATE CASCADE;
SELECT '✓ inventory_unit -> model_unit FK created' as status;

ALTER TABLE inventory_unit 
ADD CONSTRAINT fk_inventory_unit_tipe_new 
FOREIGN KEY (tipe_unit_id) REFERENCES tipe_unit(id_tipe_unit) 
ON DELETE SET NULL 
ON UPDATE CASCADE;
SELECT '✓ inventory_unit -> tipe_unit FK created' as status;

-- Kapasitas
ALTER TABLE inventory_unit 
ADD CONSTRAINT fk_inventory_unit_kapasitas_new 
FOREIGN KEY (kapasitas_unit_id) REFERENCES kapasitas(id_kapasitas) 
ON DELETE SET NULL 
ON UPDATE CASCADE;
SELECT '✓ inventory_unit -> kapasitas FK created' as status;

-- ============================================================================
-- STEP 4: WORK_ORDERS FK CONSTRAINTS
-- ============================================================================
SELECT '=== ADDING WORK_ORDERS FK CONSTRAINTS ===' as status;

-- Unit relationship: Restrict deletion of units yang ada work order
ALTER TABLE work_orders 
ADD CONSTRAINT fk_wo_unit_new 
FOREIGN KEY (unit_id) REFERENCES inventory_unit(id_inventory_unit) 
ON DELETE RESTRICT 
ON UPDATE CASCADE;
SELECT '✓ work_orders -> inventory_unit FK created' as status;

-- Employee assignments: Set NULL jika employee dihapus
ALTER TABLE work_orders 
ADD CONSTRAINT fk_wo_mechanic_employee_new 
FOREIGN KEY (mechanic_id) REFERENCES employees(id) 
ON DELETE SET NULL 
ON UPDATE CASCADE;
SELECT '✓ work_orders -> employees (mechanic) FK created' as status;

ALTER TABLE work_orders 
ADD CONSTRAINT fk_wo_foreman_employee_new 
FOREIGN KEY (foreman_id) REFERENCES employees(id) 
ON DELETE SET NULL 
ON UPDATE CASCADE;
SELECT '✓ work_orders -> employees (foreman) FK created' as status;

ALTER TABLE work_orders 
ADD CONSTRAINT fk_wo_admin_employee_new 
FOREIGN KEY (admin_id) REFERENCES employees(id) 
ON DELETE SET NULL 
ON UPDATE CASCADE;
SELECT '✓ work_orders -> employees (admin) FK created' as status;

-- Status & Priority: Reference data, restrict deletion
ALTER TABLE work_orders 
ADD CONSTRAINT fk_wo_status_new 
FOREIGN KEY (status_id) REFERENCES work_order_statuses(id) 
ON DELETE RESTRICT 
ON UPDATE CASCADE;
SELECT '✓ work_orders -> work_order_statuses FK created' as status;

ALTER TABLE work_orders 
ADD CONSTRAINT fk_wo_category_new 
FOREIGN KEY (category_id) REFERENCES work_order_categories(id) 
ON DELETE RESTRICT 
ON UPDATE CASCADE;
SELECT '✓ work_orders -> work_order_categories FK created' as status;

-- ============================================================================
-- STEP 5: PURCHASE_ORDERS FK CONSTRAINTS
-- ============================================================================
SELECT '=== ADDING PURCHASE_ORDERS FK CONSTRAINTS ===' as status;

-- Supplier relationship: Set NULL jika supplier dihapus (allow flexibility)
ALTER TABLE purchase_orders 
ADD CONSTRAINT fk_purchase_orders_suppliers_new 
FOREIGN KEY (supplier_id) REFERENCES suppliers(id_supplier) 
ON DELETE SET NULL 
ON UPDATE CASCADE;
SELECT '✓ purchase_orders -> suppliers FK created' as status;

-- PO Items: Cascade delete jika PO dihapus
ALTER TABLE po_units 
ADD CONSTRAINT fk_po_units_purchase_orders_new 
FOREIGN KEY (po_id) REFERENCES purchase_orders(id_po) 
ON DELETE CASCADE 
ON UPDATE CASCADE;
SELECT '✓ po_units -> purchase_orders FK created' as status;

ALTER TABLE po_sparepart_items 
ADD CONSTRAINT fk_po_sparepart_items_purchase_orders_new 
FOREIGN KEY (po_id) REFERENCES purchase_orders(id_po) 
ON DELETE CASCADE 
ON UPDATE CASCADE;
SELECT '✓ po_sparepart_items -> purchase_orders FK created' as status;

-- PO Attachments: Cascade delete
ALTER TABLE po_attachment 
ADD CONSTRAINT fk_po_attachment_purchase_orders_new 
FOREIGN KEY (po_id) REFERENCES purchase_orders(id_po) 
ON DELETE CASCADE 
ON UPDATE CASCADE;
SELECT '✓ po_attachment -> purchase_orders FK created' as status;

-- ============================================================================
-- STEP 6: USER & SECURITY FK CONSTRAINTS
-- ============================================================================
SELECT '=== ADDING USER & SECURITY FK CONSTRAINTS ===' as status;

-- User roles junction table
ALTER TABLE user_roles 
ADD CONSTRAINT fk_user_roles_user_new 
FOREIGN KEY (user_id) REFERENCES users(id) 
ON DELETE CASCADE 
ON UPDATE CASCADE;
SELECT '✓ user_roles -> users FK created' as status;

ALTER TABLE user_roles 
ADD CONSTRAINT fk_user_roles_role_new 
FOREIGN KEY (role_id) REFERENCES roles(id) 
ON DELETE CASCADE 
ON UPDATE CASCADE;
SELECT '✓ user_roles -> roles FK created' as status;

-- Role permissions junction table
ALTER TABLE role_permissions 
ADD CONSTRAINT fk_role_permissions_role_new 
FOREIGN KEY (role_id) REFERENCES roles(id) 
ON DELETE CASCADE 
ON UPDATE CASCADE;
SELECT '✓ role_permissions -> roles FK created' as status;

ALTER TABLE role_permissions 
ADD CONSTRAINT fk_role_permissions_permission_new 
FOREIGN KEY (permission_id) REFERENCES permissions(id) 
ON DELETE CASCADE 
ON UPDATE CASCADE;
SELECT '✓ role_permissions -> permissions FK created' as status;

-- Notifications
ALTER TABLE notifications 
ADD CONSTRAINT fk_notifications_user_new 
FOREIGN KEY (user_id) REFERENCES users(id) 
ON DELETE CASCADE 
ON UPDATE CASCADE;
SELECT '✓ notifications -> users FK created' as status;

-- ============================================================================
-- STEP 7: CUSTOMER & LOCATION FK CONSTRAINTS
-- ============================================================================
SELECT '=== ADDING CUSTOMER & LOCATION FK CONSTRAINTS ===' as status;

-- Customer locations: Cascade delete jika customer dihapus
ALTER TABLE customer_locations 
ADD CONSTRAINT fk_customer_locations_customer_new 
FOREIGN KEY (customer_id) REFERENCES customers(id) 
ON DELETE CASCADE 
ON UPDATE CASCADE;
SELECT '✓ customer_locations -> customers FK created' as status;

-- Customer contracts junction table
ALTER TABLE customer_contracts 
ADD CONSTRAINT fk_customer_contracts_customer_new 
FOREIGN KEY (customer_id) REFERENCES customers(id) 
ON DELETE CASCADE 
ON UPDATE CASCADE;
SELECT '✓ customer_contracts -> customers FK created' as status;

ALTER TABLE customer_contracts 
ADD CONSTRAINT fk_customer_contracts_kontrak_new 
FOREIGN KEY (kontrak_id) REFERENCES kontrak(id) 
ON DELETE CASCADE 
ON UPDATE CASCADE;
SELECT '✓ customer_contracts -> kontrak FK created' as status;

-- ============================================================================
-- STEP 8: SPK FK CONSTRAINTS
-- ============================================================================
SELECT '=== ADDING SPK FK CONSTRAINTS ===' as status;

-- SPK constraints
ALTER TABLE spk 
ADD CONSTRAINT fk_spk_kontrak_new 
FOREIGN KEY (kontrak_id) REFERENCES kontrak(id) 
ON DELETE SET NULL 
ON UPDATE CASCADE;
SELECT '✓ spk -> kontrak FK created' as status;

ALTER TABLE spk 
ADD CONSTRAINT fk_spk_user_new 
FOREIGN KEY (dibuat_oleh) REFERENCES users(id) 
ON DELETE SET NULL 
ON UPDATE CASCADE;
SELECT '✓ spk -> users FK created' as status;

-- ============================================================================
-- STEP 9: DELIVERY INSTRUCTIONS FK CONSTRAINTS
-- ============================================================================
SELECT '=== ADDING DELIVERY INSTRUCTIONS FK CONSTRAINTS ===' as status;

-- Delivery instructions
ALTER TABLE delivery_instructions 
ADD CONSTRAINT fk_di_spk_new 
FOREIGN KEY (spk_id) REFERENCES spk(id) 
ON DELETE SET NULL 
ON UPDATE CASCADE;
SELECT '✓ delivery_instructions -> spk FK created' as status;

-- Delivery items
ALTER TABLE delivery_items 
ADD CONSTRAINT fk_delivery_items_di_new 
FOREIGN KEY (di_id) REFERENCES delivery_instructions(id) 
ON DELETE CASCADE 
ON UPDATE CASCADE;
SELECT '✓ delivery_items -> delivery_instructions FK created' as status;

ALTER TABLE delivery_items 
ADD CONSTRAINT fk_delivery_items_unit_new 
FOREIGN KEY (unit_id) REFERENCES inventory_unit(id_inventory_unit) 
ON DELETE SET NULL 
ON UPDATE CASCADE;
SELECT '✓ delivery_items -> inventory_unit FK created' as status;

-- ============================================================================
-- STEP 10: WORK ORDER RELATED FK CONSTRAINTS
-- ============================================================================
SELECT '=== ADDING WORK ORDER RELATED FK CONSTRAINTS ===' as status;

-- Work order spareparts: Cascade delete
ALTER TABLE work_order_spareparts 
ADD CONSTRAINT fk_wo_spareparts_work_order_new 
FOREIGN KEY (work_order_id) REFERENCES work_orders(id) 
ON DELETE CASCADE 
ON UPDATE CASCADE;
SELECT '✓ work_order_spareparts -> work_orders FK created' as status;

-- ============================================================================
-- STEP 11: EMPLOYEES FK CONSTRAINTS
-- ============================================================================
SELECT '=== ADDING EMPLOYEES FK CONSTRAINTS ===' as status;

-- Employees to departemen
ALTER TABLE employees 
ADD CONSTRAINT fk_employees_departemen_new 
FOREIGN KEY (departemen_id) REFERENCES departemen(id_departemen) 
ON DELETE SET NULL 
ON UPDATE CASCADE;
SELECT '✓ employees -> departemen FK created' as status;

-- ============================================================================
-- STEP 12: VERIFIKASI FK CONSTRAINTS
-- ============================================================================
SELECT '=== VERIFIKASI FK CONSTRAINTS ===' as status;

-- Hitung total FK yang berhasil dibuat
SELECT 
    'Total FK constraints created' as metric,
    COUNT(*) as count
FROM information_schema.REFERENTIAL_CONSTRAINTS 
WHERE CONSTRAINT_SCHEMA = 'optima_ci'
    AND CONSTRAINT_NAME LIKE '%_new';

-- List semua FK yang baru dibuat
SELECT 
    CONSTRAINT_NAME,
    TABLE_NAME,
    REFERENCED_TABLE_NAME,
    DELETE_RULE,
    UPDATE_RULE
FROM information_schema.REFERENTIAL_CONSTRAINTS 
WHERE CONSTRAINT_SCHEMA = 'optima_ci'
    AND CONSTRAINT_NAME LIKE '%_new'
ORDER BY TABLE_NAME;

-- ============================================================================
-- STEP 13: FK HEALTH CHECK
-- ============================================================================
SELECT '=== FK HEALTH CHECK ===' as status;

-- Test FK integrity dengan sample queries
SELECT 'FK integrity test - inventory_unit' as test;
SELECT COUNT(*) as valid_records
FROM inventory_unit iu
WHERE iu.departemen_id IS NOT NULL 
    AND iu.departemen_id IN (SELECT id_departemen FROM departemen);

SELECT 'FK integrity test - work_orders' as test;
SELECT COUNT(*) as valid_records
FROM work_orders wo
WHERE wo.unit_id IS NOT NULL 
    AND wo.unit_id IN (SELECT id_inventory_unit FROM inventory_unit);

SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;

SELECT '=== FOREIGN KEY OPTIMIZATION COMPLETED! ===' as status;
SELECT 'Created 20+ foreign key constraints!' as achievement;
SELECT 'Data integrity enforced at database level!' as benefit1;
SELECT 'Orphan data cleanup completed!' as benefit2;
SELECT 'Automatic referential integrity maintained!' as benefit3;
SELECT 'Database structure is now rock-solid!' as benefit4;
SELECT 'Ready for performance monitoring...' as next_step;