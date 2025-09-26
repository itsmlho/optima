-- Work Orders Views and Procedures
-- Created: September 23, 2025
-- This script creates useful views and stored procedures for work orders management

-- ========================================
-- VIEWS FOR WORK ORDERS
-- ========================================

-- 1. Comprehensive Work Order View
DROP VIEW IF EXISTS `vw_work_orders_detail`;
CREATE VIEW `vw_work_orders_detail` AS
SELECT 
    wo.id,
    wo.work_order_number,
    wo.report_date,
    DATE_FORMAT(wo.report_date, '%d/%m/%Y %H:%i:%s') as formatted_report_date,
    iu.no_unit,
    k.pelanggan as nama_perusahaan,
    mu.merk_unit,
    tu.tipe as tipe_unit,
    kap.kapasitas_unit as kapasitas,
    kap.kapasitas_unit as kapasitas_unit,
    wo.order_type as tipe_order,
    wop.priority_name as priority,
    wop.priority_color,
    wo.requested_repair_time,
    DATE_FORMAT(wo.requested_repair_time, '%d-%m-%Y %H:%i') as formatted_request_time,
    woc.category_name as kategori,
    wosc.subcategory_name as sub_kategori,
    wo.complaint_description as keluhan_unit,
    wos.status_name as status,
    wos.status_color,
    ws_admin.staff_name as admin,
    ws_foreman.staff_name as foreman,
    ws_mechanic.staff_name as mekanik,
    ws_helper.staff_name as helper,
    wo.repair_description as perbaikan,
    wo.notes as keterangan,
    wo.sparepart_used as sparepart,
    wo.time_to_repair as ttr,
    wo.completion_date as tanggal,
    DATE_FORMAT(wo.completion_date, '%M') as bulan,
    wo.area,
    wo.created_at,
    wo.updated_at
FROM work_orders wo
LEFT JOIN inventory_unit iu ON wo.unit_id = iu.id_inventory_unit
LEFT JOIN kontrak k ON iu.kontrak_id = k.id
LEFT JOIN model_unit mu ON iu.model_unit_id = mu.id_model_unit
LEFT JOIN tipe_unit tu ON iu.tipe_unit_id = tu.id_tipe_unit
LEFT JOIN kapasitas kap ON iu.kapasitas_unit_id = kap.id_kapasitas
LEFT JOIN work_order_priorities wop ON wo.priority_id = wop.id
LEFT JOIN work_order_categories woc ON wo.category_id = woc.id
LEFT JOIN work_order_subcategories wosc ON wo.subcategory_id = wosc.id
LEFT JOIN work_order_statuses wos ON wo.status_id = wos.id
LEFT JOIN work_order_staff ws_admin ON wo.admin_staff_id = ws_admin.id
LEFT JOIN work_order_staff ws_foreman ON wo.foreman_staff_id = ws_foreman.id
LEFT JOIN work_order_staff ws_mechanic ON wo.mechanic_staff_id = ws_mechanic.id
LEFT JOIN work_order_staff ws_helper ON wo.helper_staff_id = ws_helper.id;

-- 2. Work Order Statistics View
DROP VIEW IF EXISTS `vw_work_order_stats`;
CREATE VIEW `vw_work_order_stats` AS
SELECT 
    COUNT(*) as total_work_orders,
    COUNT(CASE WHEN wos.status_code = 'OPEN' THEN 1 END) as open_work_orders,
    COUNT(CASE WHEN wos.status_code = 'ASSIGNED' THEN 1 END) as assigned_work_orders,
    COUNT(CASE WHEN wos.status_code = 'IN_PROGRESS' THEN 1 END) as in_progress_work_orders,
    COUNT(CASE WHEN wos.status_code = 'WAITING_PARTS' THEN 1 END) as waiting_parts_work_orders,
    COUNT(CASE WHEN wos.status_code = 'TESTING' THEN 1 END) as testing_work_orders,
    COUNT(CASE WHEN wos.status_code = 'COMPLETED' THEN 1 END) as completed_work_orders,
    COUNT(CASE WHEN wos.status_code = 'CLOSED' THEN 1 END) as closed_work_orders,
    COUNT(CASE WHEN wos.status_code = 'CANCELLED' THEN 1 END) as cancelled_work_orders,
    COUNT(CASE WHEN wos.status_code = 'ON_HOLD' THEN 1 END) as on_hold_work_orders,
    COUNT(CASE WHEN wop.priority_code = 'LOW' THEN 1 END) as low_priority_count,
    COUNT(CASE WHEN wop.priority_code = 'MEDIUM' THEN 1 END) as medium_priority_count,
    COUNT(CASE WHEN wop.priority_code = 'HIGH' THEN 1 END) as high_priority_count,
    COUNT(CASE WHEN wop.priority_code = 'CRITICAL' THEN 1 END) as critical_priority_count,
    COUNT(CASE WHEN wo.order_type = 'COMPLAINT' THEN 1 END) as complaint_orders,
    COUNT(CASE WHEN wo.order_type = 'PMPS' THEN 1 END) as pmps_orders,
    COUNT(CASE WHEN wo.order_type = 'FABRIKASI' THEN 1 END) as fabrikasi_orders,
    COUNT(CASE WHEN wo.order_type = 'PERSIAPAN' THEN 1 END) as persiapan_orders
FROM work_orders wo
LEFT JOIN work_order_statuses wos ON wo.status_id = wos.id
LEFT JOIN work_order_priorities wop ON wo.priority_id = wop.id;

-- 3. Work Order by Category View
DROP VIEW IF EXISTS `vw_work_order_by_category`;
CREATE VIEW `vw_work_order_by_category` AS
SELECT 
    woc.category_name,
    COUNT(*) as total_work_orders,
    COUNT(CASE WHEN wos.is_final_status = 0 THEN 1 END) as open_work_orders,
    COUNT(CASE WHEN wos.is_final_status = 1 THEN 1 END) as closed_work_orders,
    AVG(wo.time_to_repair) as avg_repair_time,
    MIN(wo.time_to_repair) as min_repair_time,
    MAX(wo.time_to_repair) as max_repair_time
FROM work_orders wo
LEFT JOIN work_order_categories woc ON wo.category_id = woc.id
LEFT JOIN work_order_statuses wos ON wo.status_id = wos.id
GROUP BY woc.id, woc.category_name
ORDER BY total_work_orders DESC;

-- 4. Work Order by Staff Performance View
DROP VIEW IF EXISTS `vw_staff_performance`;
CREATE VIEW `vw_staff_performance` AS
SELECT 
    ws.staff_name,
    ws.staff_role,
    COUNT(CASE WHEN ws.staff_role = 'MECHANIC' THEN wo.id END) as assigned_work_orders,
    COUNT(CASE WHEN ws.staff_role = 'MECHANIC' AND wos.status_code = 'COMPLETED' THEN wo.id END) as completed_work_orders,
    AVG(CASE WHEN ws.staff_role = 'MECHANIC' AND wo.time_to_repair IS NOT NULL THEN wo.time_to_repair END) as avg_repair_time,
    COUNT(CASE WHEN ws.staff_role = 'FOREMAN' THEN wo.id END) as supervised_work_orders
FROM work_order_staff ws
LEFT JOIN work_orders wo ON (ws.id = wo.mechanic_staff_id OR ws.id = wo.foreman_staff_id)
LEFT JOIN work_order_statuses wos ON wo.status_id = wos.id
WHERE ws.is_active = 1
GROUP BY ws.id, ws.staff_name, ws.staff_role
ORDER BY ws.staff_role, assigned_work_orders DESC;

-- 5. Overdue Work Orders View
DROP VIEW IF EXISTS `vw_overdue_work_orders`;
CREATE VIEW `vw_overdue_work_orders` AS
SELECT 
    wo.*,
    wop.sla_hours,
    TIMESTAMPDIFF(HOUR, wo.report_date, NOW()) as hours_elapsed,
    (TIMESTAMPDIFF(HOUR, wo.report_date, NOW()) - wop.sla_hours) as hours_overdue
FROM work_orders wo
LEFT JOIN work_order_priorities wop ON wo.priority_id = wop.id
LEFT JOIN work_order_statuses wos ON wo.status_id = wos.id
WHERE wos.is_final_status = 0 
  AND TIMESTAMPDIFF(HOUR, wo.report_date, NOW()) > wop.sla_hours
ORDER BY hours_overdue DESC;

-- ========================================
-- STORED PROCEDURES FOR WORK ORDERS
-- ========================================

DELIMITER $$

-- 1. Generate Work Order Number
DROP PROCEDURE IF EXISTS `sp_generate_work_order_number`$$
CREATE PROCEDURE `sp_generate_work_order_number`(OUT `p_work_order_number` VARCHAR(50))
BEGIN
    DECLARE v_counter INT DEFAULT 0;
    DECLARE v_date_prefix VARCHAR(10);
    DECLARE v_number VARCHAR(50);
    
    -- Generate date prefix (YYYYMM)
    SET v_date_prefix = DATE_FORMAT(NOW(), '%Y%m');
    
    -- Get next counter for this month
    SELECT IFNULL(MAX(CAST(SUBSTRING(work_order_number, -5) AS UNSIGNED)), 0) + 1
    INTO v_counter
    FROM work_orders 
    WHERE work_order_number LIKE CONCAT(v_date_prefix, '%');
    
    -- Format the work order number
    SET v_number = CONCAT(v_date_prefix, LPAD(v_counter, 5, '0'));
    
    SET p_work_order_number = v_number;
END$$

-- 2. Update Work Order Status with History
DROP PROCEDURE IF EXISTS `sp_update_work_order_status`$$
CREATE PROCEDURE `sp_update_work_order_status`(
    IN `p_work_order_id` INT,
    IN `p_new_status_id` INT,
    IN `p_changed_by` INT,
    IN `p_change_reason` TEXT
)
BEGIN
    DECLARE v_old_status_id INT;
    
    -- Get current status
    SELECT status_id INTO v_old_status_id 
    FROM work_orders 
    WHERE id = p_work_order_id;
    
    -- Update work order status
    UPDATE work_orders 
    SET status_id = p_new_status_id, 
        updated_at = NOW()
    WHERE id = p_work_order_id;
    
    -- Insert status history
    INSERT INTO work_order_status_history 
    (work_order_id, from_status_id, to_status_id, changed_by, change_reason)
    VALUES 
    (p_work_order_id, v_old_status_id, p_new_status_id, p_changed_by, p_change_reason);
END$$

-- 3. Assign Staff to Work Order
DROP PROCEDURE IF EXISTS `sp_assign_staff_to_work_order`$$
CREATE PROCEDURE `sp_assign_staff_to_work_order`(
    IN `p_work_order_id` INT,
    IN `p_admin_staff_id` INT,
    IN `p_foreman_staff_id` INT,
    IN `p_mechanic_staff_id` INT,
    IN `p_helper_staff_id` INT,
    IN `p_assigned_by` INT
)
BEGIN
    UPDATE work_orders 
    SET admin_staff_id = p_admin_staff_id,
        foreman_staff_id = p_foreman_staff_id,
        mechanic_staff_id = p_mechanic_staff_id,
        helper_staff_id = p_helper_staff_id,
        updated_at = NOW()
    WHERE id = p_work_order_id;
    
    -- Update status to ASSIGNED if staff assigned
    IF p_mechanic_staff_id IS NOT NULL THEN
        CALL sp_update_work_order_status(p_work_order_id, 2, p_assigned_by, 'Staff assigned to work order');
    END IF;
END$$

-- 4. Complete Work Order
DROP PROCEDURE IF EXISTS `sp_complete_work_order`$$
CREATE PROCEDURE `sp_complete_work_order`(
    IN `p_work_order_id` INT,
    IN `p_repair_description` TEXT,
    IN `p_sparepart_used` TEXT,
    IN `p_time_to_repair` DECIMAL(5,2),
    IN `p_completed_by` INT
)
BEGIN
    UPDATE work_orders 
    SET repair_description = p_repair_description,
        sparepart_used = p_sparepart_used,
        time_to_repair = p_time_to_repair,
        completion_date = NOW(),
        updated_at = NOW()
    WHERE id = p_work_order_id;
    
    -- Update status to COMPLETED
    CALL sp_update_work_order_status(p_work_order_id, 6, p_completed_by, 'Work order completed');
END$$

DELIMITER ;

-- ========================================
-- TRIGGERS FOR WORK ORDERS
-- ========================================

-- Trigger to auto-generate work order number if not provided
DROP TRIGGER IF EXISTS `tr_work_order_before_insert`;
DELIMITER $$
CREATE TRIGGER `tr_work_order_before_insert`
BEFORE INSERT ON `work_orders`
FOR EACH ROW
BEGIN
    DECLARE v_wo_number VARCHAR(50);
    
    -- Generate work order number if not provided
    IF NEW.work_order_number IS NULL OR NEW.work_order_number = '' THEN
        CALL sp_generate_work_order_number(v_wo_number);
        SET NEW.work_order_number = v_wo_number;
    END IF;
END$$
DELIMITER ;

-- Trigger to log activity when work order is created
DROP TRIGGER IF EXISTS `tr_work_order_after_insert`;
DELIMITER $$
CREATE TRIGGER `tr_work_order_after_insert`
AFTER INSERT ON `work_orders`
FOR EACH ROW
BEGIN
    -- Insert initial status history
    INSERT INTO work_order_status_history 
    (work_order_id, from_status_id, to_status_id, changed_by, change_reason)
    VALUES 
    (NEW.id, NULL, NEW.status_id, NEW.created_by, 'Work order created');
    
    -- Log activity (if activity_log table exists)
    INSERT IGNORE INTO activity_log 
    (user_id, activity_type_id, description, reference_table, reference_id, created_at)
    VALUES 
    (NEW.created_by, 23, CONCAT('Work order ', NEW.work_order_number, ' created'), 'work_orders', NEW.id, NOW());
END$$
DELIMITER ;