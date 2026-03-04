<?php
/**
 * Fix all 8 broken views in optima_ci database
 * 
 * Group A: Replace inventory_unit.kontrak_id JOIN with kontrak_unit junction
 *   1. contract_unit_summary
 *   2. v_customer_activity
 *   3. v_unit_availability
 *   4. vw_work_orders_detail (USED BY APP — WorkOrderModel.php)
 *
 * Group B: Fix table references (inventory_attachment → inventory_attachments etc.)
 *   5. inventory_unit_components (USED BY APP — Operational.php)
 *   6. vw_attachment_installed
 *   7. vw_attachment_status
 *   8. vw_work_order_sparepart_summary
 */

$pdo = new PDO('mysql:host=localhost;dbname=optima_ci', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$success = 0;
$fail = 0;

function fixView($pdo, $name, $sql, &$success, &$fail) {
    try {
        $pdo->exec("DROP VIEW IF EXISTS `$name`");
        $pdo->exec($sql);
        // Verify
        $pdo->query("SELECT 1 FROM `$name` LIMIT 0");
        echo "  OK: $name\n";
        $success++;
    } catch (PDOException $e) {
        echo "  FAIL: $name => " . $e->getMessage() . "\n";
        $fail++;
    }
}

// ============================================================
// GROUP A: Fix kontrak_id → kontrak_unit junction
// ============================================================
echo "=== GROUP A: Fix kontrak_id JOINs ===\n";

// 1. contract_unit_summary
fixView($pdo, 'contract_unit_summary', "
CREATE VIEW contract_unit_summary AS
SELECT 
    k.id AS kontrak_id,
    k.no_kontrak,
    c.customer_name AS pelanggan,
    cl.location_name AS lokasi,
    cl.address AS alamat_lengkap,
    k.status AS kontrak_status,
    k.tanggal_mulai,
    k.tanggal_berakhir,
    k.total_units AS kontrak_total_units,
    COUNT(iu.id_inventory_unit) AS active_units,
    COUNT(CASE WHEN iu.workflow_status LIKE '%TARIK%' THEN 1 END) AS tarik_units,
    COUNT(CASE WHEN iu.workflow_status LIKE '%TUKAR%' THEN 1 END) AS tukar_units,
    COUNT(CASE WHEN su.status_unit IN ('DISEWA','BEROPERASI') THEN 1 END) AS operational_units,
    COUNT(CASE WHEN iu.workflow_status IS NOT NULL THEN 1 END) AS workflow_units,
    k.nilai_total,
    k.jenis_sewa,
    k.dibuat_pada AS created_at
FROM kontrak k
LEFT JOIN customer_locations cl ON k.customer_location_id = cl.id
LEFT JOIN customers c ON cl.customer_id = c.id
LEFT JOIN kontrak_unit ku ON k.id = ku.kontrak_id AND ku.status = 'ACTIVE'
LEFT JOIN inventory_unit iu ON ku.unit_id = iu.id_inventory_unit
LEFT JOIN status_unit su ON iu.status_unit_id = su.id_status
GROUP BY k.id, k.no_kontrak, c.customer_name, cl.location_name, cl.address,
    k.status, k.tanggal_mulai, k.tanggal_berakhir, k.total_units,
    k.nilai_total, k.jenis_sewa, k.dibuat_pada
", $success, $fail);

// 2. v_customer_activity
fixView($pdo, 'v_customer_activity', "
CREATE VIEW v_customer_activity AS
SELECT 
    c.id AS customer_id,
    c.customer_name,
    c.customer_code,
    c.is_active,
    COUNT(DISTINCT cl.id) AS location_count,
    COUNT(DISTINCT k.id) AS contract_count,
    COALESCE(SUM(k.nilai_total), 0) AS total_contract_value,
    MAX(k.dibuat_pada) AS last_contract_date,
    COUNT(DISTINCT CASE WHEN k.status = 'ACTIVE' THEN k.id END) AS active_contracts,
    COUNT(DISTINCT ku.unit_id) AS total_units,
    CASE 
        WHEN COALESCE(SUM(k.nilai_total), 0) > 10000000 THEN 'premium'
        WHEN COALESCE(SUM(k.nilai_total), 0) > 5000000 THEN 'gold'
        WHEN COALESCE(SUM(k.nilai_total), 0) > 1000000 THEN 'silver'
        ELSE 'bronze'
    END AS customer_tier,
    TO_DAYS(CURDATE()) - TO_DAYS(MAX(k.dibuat_pada)) AS days_since_last_contract
FROM customers c
LEFT JOIN customer_locations cl ON c.id = cl.customer_id
LEFT JOIN kontrak k ON cl.id = k.customer_location_id
LEFT JOIN kontrak_unit ku ON k.id = ku.kontrak_id AND ku.status = 'ACTIVE'
GROUP BY c.id, c.customer_name, c.customer_code, c.is_active
", $success, $fail);

// 3. v_unit_availability
fixView($pdo, 'v_unit_availability', "
CREATE VIEW v_unit_availability AS
SELECT 
    iu.id_inventory_unit,
    iu.serial_number,
    iu.no_unit,
    ku.kontrak_id,
    iu.lokasi_unit,
    iu.workflow_status,
    CASE 
        WHEN ku.kontrak_id IS NULL THEN 'available'
        WHEN ku.kontrak_id IS NOT NULL THEN 'contracted'
        ELSE 'unknown'
    END AS availability_status,
    k.no_kontrak,
    k.status AS kontrak_status,
    c.customer_name
FROM inventory_unit iu
LEFT JOIN kontrak_unit ku ON iu.id_inventory_unit = ku.unit_id AND ku.status = 'ACTIVE'
LEFT JOIN kontrak k ON ku.kontrak_id = k.id
LEFT JOIN customer_locations cl ON k.customer_location_id = cl.id
LEFT JOIN customers c ON cl.customer_id = c.id
", $success, $fail);

// 4. vw_work_orders_detail (USED BY APP)
fixView($pdo, 'vw_work_orders_detail', "
CREATE VIEW vw_work_orders_detail AS
SELECT 
    wo.id,
    wo.work_order_number,
    wo.report_date,
    DATE_FORMAT(wo.report_date, '%d/%m/%Y %H:%i:%s') AS formatted_report_date,
    iu.no_unit,
    c.customer_name AS nama_perusahaan,
    mu.merk_unit,
    tu.tipe AS tipe_unit,
    kap.kapasitas_unit AS kapasitas,
    kap.kapasitas_unit AS kapasitas_unit,
    wo.order_type AS tipe_order,
    wop.priority_name AS priority,
    wop.priority_color,
    wo.requested_repair_time,
    DATE_FORMAT(wo.requested_repair_time, '%d-%m-%Y %H:%i') AS formatted_request_time,
    woc.category_name AS kategori,
    wosc.subcategory_name AS sub_kategori,
    wo.complaint_description AS keluhan_unit,
    wos.status_name AS status,
    wos.status_color,
    e_admin.staff_name AS admin,
    e_foreman.staff_name AS foreman,
    e_mechanic.staff_name AS mekanik,
    e_helper.staff_name AS helper,
    wo.repair_description AS perbaikan,
    wo.notes AS keterangan,
    wo.sparepart_used AS sparepart,
    wo.time_to_repair AS ttr,
    wo.completion_date AS tanggal,
    DATE_FORMAT(wo.completion_date, '%M') AS bulan,
    wo.area,
    wo.created_at,
    wo.updated_at
FROM work_orders wo
LEFT JOIN inventory_unit iu ON wo.unit_id = iu.id_inventory_unit
LEFT JOIN kontrak_unit ku ON iu.id_inventory_unit = ku.unit_id AND ku.status = 'ACTIVE'
LEFT JOIN kontrak k ON ku.kontrak_id = k.id
LEFT JOIN customer_locations cl ON k.customer_location_id = cl.id
LEFT JOIN customers c ON cl.customer_id = c.id
LEFT JOIN model_unit mu ON iu.model_unit_id = mu.id_model_unit
LEFT JOIN tipe_unit tu ON iu.tipe_unit_id = tu.id_tipe_unit
LEFT JOIN kapasitas kap ON iu.kapasitas_unit_id = kap.id_kapasitas
LEFT JOIN work_order_priorities wop ON wo.priority_id = wop.id
LEFT JOIN work_order_categories woc ON wo.category_id = woc.id
LEFT JOIN work_order_subcategories wosc ON wo.subcategory_id = wosc.id
LEFT JOIN work_order_statuses wos ON wo.status_id = wos.id
LEFT JOIN employees e_admin ON wo.admin_id = e_admin.id
LEFT JOIN employees e_foreman ON wo.foreman_id = e_foreman.id
LEFT JOIN employees e_mechanic ON wo.mechanic_id = e_mechanic.id
LEFT JOIN employees e_helper ON wo.helper_id = e_helper.id
", $success, $fail);

// ============================================================
// GROUP B: Fix table references
// ============================================================
echo "\n=== GROUP B: Fix table references ===\n";

// 5. inventory_unit_components (USED BY APP — Operational.php)
fixView($pdo, 'inventory_unit_components', "
CREATE VIEW inventory_unit_components AS
SELECT 
    iu.id_inventory_unit,
    iu.no_unit,
    iu.serial_number,
    ib.battery_type_id AS model_baterai_id,
    ib.serial_number AS sn_baterai,
    b.merk_baterai,
    b.tipe_baterai,
    b.jenis_baterai,
    ic.charger_type_id AS model_charger_id,
    ic.serial_number AS sn_charger,
    ch.merk_charger,
    ch.tipe_charger,
    ia.attachment_type_id AS model_attachment_id,
    ia.serial_number AS sn_attachment,
    a.tipe AS attachment_tipe,
    a.merk AS attachment_merk,
    a.model AS attachment_model
FROM inventory_unit iu
LEFT JOIN inventory_batteries ib ON iu.id_inventory_unit = ib.inventory_unit_id AND ib.status = 'IN_USE'
LEFT JOIN baterai b ON ib.battery_type_id = b.id
LEFT JOIN inventory_chargers ic ON iu.id_inventory_unit = ic.inventory_unit_id AND ic.status = 'IN_USE'
LEFT JOIN charger ch ON ic.charger_type_id = ch.id_charger
LEFT JOIN inventory_attachments ia ON iu.id_inventory_unit = ia.inventory_unit_id AND ia.status = 'IN_USE'
LEFT JOIN attachment a ON ia.attachment_type_id = a.id_attachment
", $success, $fail);

// 6. vw_attachment_installed (not used by app, but useful for reporting)
fixView($pdo, 'vw_attachment_installed', "
CREATE VIEW vw_attachment_installed AS
SELECT 
    ia.id AS id_inventory_attachment,
    'attachment' AS tipe_item,
    CONCAT(COALESCE(a.tipe,''), ' - ', COALESCE(a.merk,''), ' ', COALESCE(a.model,'')) AS item_name,
    ia.serial_number,
    iu.no_unit,
    ia.status AS status_unit
FROM inventory_attachments ia
LEFT JOIN inventory_unit iu ON ia.inventory_unit_id = iu.id_inventory_unit
LEFT JOIN attachment a ON ia.attachment_type_id = a.id_attachment

UNION ALL

SELECT 
    ib.id,
    'battery',
    CONCAT(COALESCE(b.jenis_baterai,''), ' - ', COALESCE(b.merk_baterai,''), ' ', COALESCE(b.tipe_baterai,'')),
    ib.serial_number,
    iu.no_unit,
    ib.status
FROM inventory_batteries ib
LEFT JOIN inventory_unit iu ON ib.inventory_unit_id = iu.id_inventory_unit
LEFT JOIN baterai b ON ib.battery_type_id = b.id

UNION ALL

SELECT 
    ic.id,
    'charger',
    CONCAT(COALESCE(ch.tipe_charger,''), ' - ', COALESCE(ch.merk_charger,'')),
    ic.serial_number,
    iu.no_unit,
    ic.status
FROM inventory_chargers ic
LEFT JOIN inventory_unit iu ON ic.inventory_unit_id = iu.id_inventory_unit
LEFT JOIN charger ch ON ic.charger_type_id = ch.id_charger
", $success, $fail);

// 7. vw_attachment_status (not used by app, reporting view)
fixView($pdo, 'vw_attachment_status', "
CREATE VIEW vw_attachment_status AS
SELECT 
    ia.id AS id_inventory_attachment,
    'attachment' AS tipe_item,
    ia.purchase_order_id AS po_id,
    ia.inventory_unit_id AS id_inventory_unit,
    ia.attachment_type_id AS attachment_id,
    ia.serial_number AS sn_attachment,
    NULL AS baterai_id,
    NULL AS sn_baterai,
    NULL AS charger_id,
    NULL AS sn_charger,
    ia.physical_condition AS kondisi_fisik,
    ia.completeness AS kelengkapan,
    ia.physical_notes AS catatan_fisik,
    ia.storage_location AS lokasi_penyimpanan,
    ia.status AS status_unit,
    ia.received_at AS tanggal_masuk,
    ia.notes AS catatan_inventory,
    ia.created_at,
    ia.updated_at,
    CASE WHEN ia.inventory_unit_id IS NOT NULL THEN 'USED' ELSE 'AVAILABLE' END AS simple_status
FROM inventory_attachments ia

UNION ALL

SELECT 
    ib.id,
    'battery',
    ib.purchase_order_id,
    ib.inventory_unit_id,
    NULL,
    NULL,
    ib.battery_type_id,
    ib.serial_number,
    NULL,
    NULL,
    ib.physical_condition,
    ib.completeness,
    ib.physical_notes,
    ib.storage_location,
    ib.status,
    ib.received_at,
    ib.notes,
    ib.created_at,
    ib.updated_at,
    CASE WHEN ib.inventory_unit_id IS NOT NULL THEN 'USED' ELSE 'AVAILABLE' END
FROM inventory_batteries ib

UNION ALL

SELECT 
    ic.id,
    'charger',
    ic.purchase_order_id,
    ic.inventory_unit_id,
    NULL,
    NULL,
    NULL,
    NULL,
    ic.charger_type_id,
    ic.serial_number,
    ic.physical_condition,
    ic.completeness,
    ic.physical_notes,
    ic.storage_location,
    ic.status,
    ic.received_at,
    ic.notes,
    ic.created_at,
    ic.updated_at,
    CASE WHEN ic.inventory_unit_id IS NOT NULL THEN 'USED' ELSE 'AVAILABLE' END
FROM inventory_chargers ic
", $success, $fail);

// 8. vw_work_order_sparepart_summary
fixView($pdo, 'vw_work_order_sparepart_summary', "
CREATE VIEW vw_work_order_sparepart_summary AS
SELECT 
    wo.id AS work_order_id,
    wo.work_order_number,
    wos.id AS sparepart_id,
    wos.sparepart_code,
    wos.sparepart_name,
    wos.quantity_brought,
    wos.satuan,
    COALESCE(wor.quantity_used, 0) AS quantity_used,
    COALESCE(wor.quantity_return, 0) AS quantity_returned,
    (wos.quantity_brought - COALESCE(wor.quantity_used, 0)) AS quantity_available,
    wor.return_notes AS usage_notes,
    wor.return_notes,
    wor.created_at AS used_at,
    wor.confirmed_at AS returned_at,
    CASE 
        WHEN wor.quantity_used IS NULL THEN 'BROUGHT'
        WHEN wor.quantity_used > 0 AND wor.confirmed_at IS NULL THEN 'USED'
        WHEN wor.confirmed_at IS NOT NULL THEN 'COMPLETED'
        ELSE 'PENDING'
    END AS status
FROM work_orders wo
JOIN work_order_spareparts wos ON wo.id = wos.work_order_id
LEFT JOIN work_order_sparepart_returns wor ON wos.id = wor.work_order_sparepart_id
ORDER BY wo.id, wos.sparepart_code
", $success, $fail);

// ============================================================
// VERIFY ALL VIEWS
// ============================================================
echo "\n=== VERIFICATION ===\n";

$r = $pdo->query("SELECT table_name FROM information_schema.views WHERE table_schema='optima_ci' ORDER BY table_name");
$allViews = $r->fetchAll(PDO::FETCH_COLUMN);

$broken = 0;
$ok = 0;
foreach ($allViews as $view) {
    try {
        $r = $pdo->query("SELECT COUNT(*) c FROM `$view`");
        $count = $r->fetch()['c'];
        echo "  OK: " . str_pad($view, 40) . " ($count rows)\n";
        $ok++;
    } catch (PDOException $e) {
        echo "  BROKEN: " . str_pad($view, 36) . " => " . substr($e->getMessage(), 0, 80) . "\n";
        $broken++;
    }
}

echo "\n=== SUMMARY ===\n";
echo "Views fixed: $success | Failed: $fail\n";
echo "All views: $ok working, $broken broken\n";
