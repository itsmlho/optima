-- ============================================================================
-- PRODUCTION FIX: View, Procedure, Function - Perbaikan & Penambahan
-- ============================================================================
-- Tanggal: 2026-03-14
-- Tujuan: Perbaiki objek yang salah + tambah yang hilang
-- 
-- INSTRUKSI:
-- 1. Backup database Production
-- 2. Jalankan PROD_sync_from_dev_20260314.sql dan PROD_improvement_20260314.sql dulu
-- 3. Pilih database Production di phpMyAdmin
-- 4. Buka tab SQL, paste seluruh script ini, klik Go
-- ============================================================================

-- ============================================================================
-- BAGIAN 1: FIX sp_assign_staff_to_work_order
-- ============================================================================
-- MASALAH: Procedure memakai admin_staff_id, foreman_staff_id, dll
--          Tabel work_orders memakai admin_id, foreman_id, mechanic_id, helper_id
-- SOLUSI: Update procedure agar memakai nama kolom yang benar
-- ============================================================================

DROP PROCEDURE IF EXISTS sp_assign_staff_to_work_order;

DELIMITER $$

CREATE PROCEDURE sp_assign_staff_to_work_order(
    IN p_work_order_id INT,
    IN p_admin_staff_id INT,
    IN p_foreman_staff_id INT,
    IN p_mechanic_staff_id INT,
    IN p_helper_staff_id INT,
    IN p_assigned_by INT
)
BEGIN
    UPDATE work_orders 
    SET admin_id = p_admin_staff_id,
        foreman_id = p_foreman_staff_id,
        mechanic_id = p_mechanic_staff_id,
        helper_id = p_helper_staff_id,
        updated_at = NOW()
    WHERE id = p_work_order_id;
    
    IF p_mechanic_staff_id IS NOT NULL THEN
        CALL sp_update_work_order_status(p_work_order_id, 2, p_assigned_by, 'Staff assigned to work order');
    END IF;
END$$

DELIMITER ;

-- ============================================================================
-- BAGIAN 2: CREATE vw_area_staff_summary (HILANG - dipakai AreaModel)
-- ============================================================================
-- Digunakan oleh: app/Models/AreaModel.php getAreaStaffSummary()
-- ============================================================================

DROP VIEW IF EXISTS vw_area_staff_summary;

CREATE VIEW vw_area_staff_summary AS
SELECT 
    a.id AS area_id,
    a.area_code,
    a.area_name,
    a.area_type,
    a.departemen_id AS area_departemen_id,
    d.id_departemen,
    d.nama_departemen,
    e.id AS employee_id,
    e.staff_name,
    e.staff_role,
    aea.assignment_type,
    aea.start_date,
    aea.end_date,
    aea.department_scope
FROM areas a
LEFT JOIN area_employee_assignments aea ON a.id = aea.area_id AND aea.is_active = 1 AND aea.deleted_at IS NULL
LEFT JOIN employees e ON aea.employee_id = e.id AND e.is_active = 1
LEFT JOIN departemen d ON e.departemen_id = d.id_departemen
WHERE a.is_active = 1 AND a.deleted_at IS NULL
ORDER BY a.area_name, e.staff_role, e.staff_name;

-- ============================================================================
-- BAGIAN 3: CREATE vw_staff_performance (HILANG - dipakai WorkOrderModel)
-- ============================================================================
-- Digunakan oleh: app/Models/WorkOrderModel.php getStaffPerformance()
-- Alias ke vw_employee_performance (struktur sama)
-- ============================================================================

DROP VIEW IF EXISTS vw_staff_performance;

CREATE VIEW vw_staff_performance AS
SELECT 
    e.id AS employee_id,
    e.staff_name AS employee_name,
    e.staff_role AS employee_role,
    d.nama_departemen AS department,
    COUNT(wo.id) AS total_work_orders,
    COUNT(CASE WHEN wos.status_name = 'COMPLETED' THEN wo.id END) AS completed_orders,
    AVG(CASE WHEN wo.time_to_repair IS NOT NULL THEN wo.time_to_repair END) AS avg_repair_time
FROM employees e
JOIN departemen d ON e.departemen_id = d.id_departemen
LEFT JOIN work_orders wo ON (e.id = wo.admin_id OR e.id = wo.foreman_id OR e.id = wo.mechanic_id OR e.id = wo.helper_id)
LEFT JOIN work_order_statuses wos ON wo.status_id = wos.id
WHERE e.is_active = 1
GROUP BY e.id, e.staff_name, e.staff_role, d.nama_departemen;

-- ============================================================================
-- BAGIAN 4: CREATE sp_generate_invoice_number (HILANG - dipakai InvoiceModel)
-- ============================================================================
-- Digunakan oleh: app/Models/InvoiceModel.php generateInvoiceNumber()
-- Format: INV/YYYYMM/NNN (contoh: INV/202603/001)
-- ============================================================================

DROP PROCEDURE IF EXISTS sp_generate_invoice_number;

DELIMITER $$

CREATE PROCEDURE sp_generate_invoice_number(OUT p_invoice_number VARCHAR(50))
BEGIN
    DECLARE v_prefix VARCHAR(20);
    DECLARE v_seq INT DEFAULT 1;
    DECLARE v_max_seq INT;
    
    SET v_prefix = CONCAT('INV/', DATE_FORMAT(NOW(), '%Y%m'), '/');
    
    SELECT COALESCE(MAX(
        CAST(SUBSTRING(invoice_number, LENGTH(v_prefix) + 1) AS UNSIGNED)
    ), 0) INTO v_max_seq
    FROM invoices
    WHERE invoice_number LIKE CONCAT(v_prefix, '%');
    
    SET v_seq = v_max_seq + 1;
    SET p_invoice_number = CONCAT(v_prefix, LPAD(v_seq, 3, '0'));
END$$

DELIMITER ;

-- ============================================================================
-- CATATAN: AreaModel sudah diupdate untuk pakai where('area_id', $areaId)
-- ============================================================================

-- ============================================================================
-- PERINGATAN: Procedure yang bergantung tabel di cleanup_unused_tables.sql
-- ============================================================================
-- Jika nanti menjalankan cleanup_unused_tables.sql, procedure berikut akan RUSAK:
--
-- 1. sp_safe_spk_rollback  -> memakai tabel spk_units (akan di-DROP)
-- 2. update_unit_status (procedure) -> memakai unit_status_log (akan di-DROP)
-- 3. update_unit_status (function)   -> memakai unit_status_log (akan di-DROP)
-- 4. ProcessUnitTarik               -> memakai unit_workflow_log, inventory_unit.kontrak_id
--
-- Sebelum cleanup: buat script DROP/ALTER procedure tersebut, atau jangan
-- jalankan cleanup sampai procedure sudah direfactor.
-- ============================================================================

-- ============================================================================
-- SELESAI
-- ============================================================================

SELECT 'Fix selesai: sp_assign_staff_to_work_order, vw_area_staff_summary, vw_staff_performance, sp_generate_invoice_number' AS status;
