# 🔧 DASHBOARD IMPLEMENTATION GUIDE - TECHNICAL DETAILS
## Query Specifications & Implementation Blueprint

**Tanggal:** 23 Desember 2024  
**Project:** OPTIMA Dashboard Upgrade  
**Dokumen:** Technical Implementation Guide

---

## 📊 SQL QUERIES FOR DASHBOARD METRICS

### 1️⃣ ASSET & INVENTORY METRICS

#### A. Unit Utilization Rate
```sql
-- Asset Utilization Query
SELECT 
    COUNT(CASE WHEN status_unit_id = 2 THEN 1 END) as rented_units,
    COUNT(CASE WHEN status_unit_id IN (1,2) THEN 1 END) as usable_units,
    ROUND(
        (COUNT(CASE WHEN status_unit_id = 2 THEN 1 END) * 100.0 / 
        NULLIF(COUNT(CASE WHEN status_unit_id IN (1,2) THEN 1 END), 0)), 2
    ) as utilization_rate
FROM inventory_unit
WHERE status_unit_id IN (1,2,4,5); -- Exclude scrapped/deleted
```

#### B. Unit Status Distribution
```sql
-- Unit Status Breakdown
SELECT 
    su.nama_status as status_name,
    COUNT(iu.id_inventory_unit) as unit_count,
    ROUND(
        (COUNT(iu.id_inventory_unit) * 100.0 / 
        (SELECT COUNT(*) FROM inventory_unit WHERE status_unit_id IN (1,2,4,5))), 2
    ) as percentage
FROM inventory_unit iu
LEFT JOIN status_unit su ON iu.status_unit_id = su.id_status_unit
WHERE iu.status_unit_id IN (1,2,4,5)
GROUP BY su.nama_status, su.id_status_unit
ORDER BY unit_count DESC;
```

#### C. Unit Aging Analysis
```sql
-- Unit Age Distribution
SELECT 
    CASE 
        WHEN TIMESTAMPDIFF(YEAR, tanggal_pembelian, CURDATE()) < 1 THEN '< 1 year'
        WHEN TIMESTAMPDIFF(YEAR, tanggal_pembelian, CURDATE()) BETWEEN 1 AND 3 THEN '1-3 years'
        WHEN TIMESTAMPDIFF(YEAR, tanggal_pembelian, CURDATE()) BETWEEN 4 AND 5 THEN '4-5 years'
        ELSE '> 5 years'
    END as age_group,
    COUNT(*) as unit_count,
    AVG(TIMESTAMPDIFF(YEAR, tanggal_pembelian, CURDATE())) as avg_age
FROM inventory_unit
WHERE tanggal_pembelian IS NOT NULL
GROUP BY age_group
ORDER BY 
    CASE age_group
        WHEN '< 1 year' THEN 1
        WHEN '1-3 years' THEN 2
        WHEN '4-5 years' THEN 3
        ELSE 4
    END;
```

#### D. Unit Distribution by Area
```sql
-- Top 10 Areas with Most Units
SELECT 
    a.nama_area as area_name,
    a.kota as city,
    COUNT(iu.id_inventory_unit) as unit_count,
    COUNT(CASE WHEN iu.status_unit_id = 2 THEN 1 END) as rented_units,
    COUNT(CASE WHEN iu.status_unit_id = 1 THEN 1 END) as available_units
FROM inventory_unit iu
LEFT JOIN areas a ON iu.area_id = a.id
WHERE a.nama_area IS NOT NULL
GROUP BY a.id, a.nama_area, a.kota
ORDER BY unit_count DESC
LIMIT 10;
```

#### E. Attachment Status
```sql
-- Attachment Inventory Summary
SELECT 
    tipe_item,
    COUNT(*) as total_items,
    COUNT(CASE WHEN attachment_status = 'USED' THEN 1 END) as used_items,
    COUNT(CASE WHEN attachment_status = 'AVAILABLE' THEN 1 END) as available_items,
    ROUND(
        (COUNT(CASE WHEN attachment_status = 'USED' THEN 1 END) * 100.0 / 
        NULLIF(COUNT(*), 0)), 2
    ) as utilization_rate
FROM inventory_attachment
WHERE tipe_item IN ('attachment', 'charger', 'battery')
GROUP BY tipe_item;
```

#### F. Low Stock Spareparts
```sql
-- Sparepart Low Stock Alert
SELECT 
    sp.kode as sparepart_code,
    sp.desc_sparepart as description,
    inv.stok as current_stock,
    sp.minimum_stok as minimum_stock,
    (sp.minimum_stok - inv.stok) as shortage,
    inv.lokasi_rak as location
FROM inventory_spareparts inv
INNER JOIN sparepart sp ON inv.sparepart_id = sp.id_sparepart
WHERE inv.stok < sp.minimum_stok
ORDER BY (sp.minimum_stok - inv.stok) DESC;
```

#### G. Top Used Spareparts in WO
```sql
-- Top 10 Most Used Spareparts
SELECT 
    sp.kode as sparepart_code,
    sp.desc_sparepart as description,
    COUNT(wosp.id) as usage_count,
    SUM(wosp.quantity) as total_quantity_used,
    AVG(wosp.quantity) as avg_quantity_per_wo
FROM work_order_sparepart_usage wosp
INNER JOIN sparepart sp ON wosp.sparepart_id = sp.id_sparepart
WHERE wosp.created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')
GROUP BY sp.id_sparepart, sp.kode, sp.desc_sparepart
ORDER BY usage_count DESC
LIMIT 10;
```

---

### 2️⃣ MARKETING & SALES METRICS

#### A. Quotation Funnel
```sql
-- Quotation Conversion Funnel
SELECT 
    COUNT(*) as total_quotations,
    COUNT(CASE WHEN status = 'Pending' THEN 1 END) as pending,
    COUNT(CASE WHEN status = 'Approved' THEN 1 END) as approved,
    COUNT(CASE WHEN status = 'Rejected' THEN 1 END) as rejected,
    ROUND(
        (COUNT(CASE WHEN status = 'Approved' THEN 1 END) * 100.0 / 
        NULLIF(COUNT(*), 0)), 2
    ) as conversion_rate
FROM quotations
WHERE created_at >= DATE_FORMAT(NOW(), '%Y-%m-01');
```

#### B. Contract Performance
```sql
-- Active Contracts with Growth
SELECT 
    COUNT(*) as total_active,
    COUNT(CASE WHEN DATE(created_at) >= DATE_FORMAT(NOW(), '%Y-%m-01') THEN 1 END) as new_this_month,
    (SELECT COUNT(*) FROM kontrak 
     WHERE status = 'Aktif' 
     AND DATE(created_at) >= DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 MONTH), '%Y-%m-01')
     AND DATE(created_at) < DATE_FORMAT(NOW(), '%Y-%m-01')
    ) as last_month_count,
    ROUND(
        ((COUNT(CASE WHEN DATE(created_at) >= DATE_FORMAT(NOW(), '%Y-%m-01') THEN 1 END) * 100.0) / 
        NULLIF((SELECT COUNT(*) FROM kontrak 
                WHERE status = 'Aktif' 
                AND DATE(created_at) >= DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 MONTH), '%Y-%m-01')
                AND DATE(created_at) < DATE_FORMAT(NOW(), '%Y-%m-01')), 0)), 2
    ) as growth_rate
FROM kontrak
WHERE status = 'Aktif';
```

#### C. Contract Expiry Alerts
```sql
-- Expiring Contracts with Severity
SELECT 
    k.no_kontrak as contract_number,
    c.nama_customer as customer_name,
    k.tanggal_mulai as start_date,
    k.tanggal_berakhir as end_date,
    DATEDIFF(k.tanggal_berakhir, CURDATE()) as days_until_expiry,
    CASE 
        WHEN DATEDIFF(k.tanggal_berakhir, CURDATE()) <= 7 THEN 'CRITICAL'
        WHEN DATEDIFF(k.tanggal_berakhir, CURDATE()) <= 30 THEN 'WARNING'
        WHEN DATEDIFF(k.tanggal_berakhir, CURDATE()) <= 90 THEN 'INFO'
    END as severity,
    (SELECT COUNT(*) FROM kontrak_spesifikasi WHERE kontrak_id = k.id_kontrak) as unit_count
FROM kontrak k
INNER JOIN customers c ON k.id_customer = c.id_customer
WHERE k.status = 'Aktif'
AND k.tanggal_berakhir >= CURDATE()
AND k.tanggal_berakhir <= DATE_ADD(CURDATE(), INTERVAL 90 DAY)
ORDER BY days_until_expiry ASC;
```

#### D. SPK Performance Summary
```sql
-- SPK Performance This Month
SELECT 
    COUNT(*) as total_spk,
    COUNT(CASE WHEN status_spk = 'COMPLETED' THEN 1 END) as completed,
    COUNT(CASE WHEN status_spk = 'IN_PROGRESS' THEN 1 END) as in_progress,
    COUNT(CASE WHEN status_spk = 'PENDING' THEN 1 END) as pending,
    ROUND(
        (COUNT(CASE WHEN status_spk = 'COMPLETED' THEN 1 END) * 100.0 / 
        NULLIF(COUNT(*), 0)), 2
    ) as completion_rate,
    AVG(TIMESTAMPDIFF(DAY, dibuat_pada, 
        CASE WHEN status_spk = 'COMPLETED' THEN updated_at ELSE NULL END)
    ) as avg_completion_days
FROM spk
WHERE DATE(dibuat_pada) >= DATE_FORMAT(NOW(), '%Y-%m-01');
```

#### E. SPK by Type Distribution
```sql
-- SPK Distribution by Work Type
SELECT 
    COALESCE(jpk.nama, s.jenis_spk, 'Unknown') as work_type,
    COUNT(s.id) as spk_count,
    ROUND(
        (COUNT(s.id) * 100.0 / 
        (SELECT COUNT(*) FROM spk WHERE DATE(dibuat_pada) >= DATE_FORMAT(NOW(), '%Y-%m-01'))), 2
    ) as percentage
FROM spk s
LEFT JOIN jenis_perintah_kerja jpk ON s.jenis_perintah_kerja_id = jpk.id
WHERE DATE(s.dibuat_pada) >= DATE_FORMAT(NOW(), '%Y-%m-01')
GROUP BY work_type
ORDER BY spk_count DESC;
```

---

### 3️⃣ SERVICE & MAINTENANCE METRICS

#### A. Work Order Performance Overview
```sql
-- Comprehensive WO Metrics
SELECT 
    COUNT(*) as total_wo,
    COUNT(CASE WHEN wos.is_final_status = 1 THEN 1 END) as completed_wo,
    COUNT(CASE WHEN wos.is_final_status = 0 THEN 1 END) as open_wo,
    ROUND(
        (COUNT(CASE WHEN wos.is_final_status = 1 THEN 1 END) * 100.0 / 
        NULLIF(COUNT(*), 0)), 2
    ) as completion_rate,
    AVG(TIMESTAMPDIFF(HOUR, wo.created_at, wo.completed_at)) as avg_resolution_hours,
    COUNT(CASE WHEN wo.due_date < CURDATE() AND wos.is_final_status = 0 THEN 1 END) as overdue_wo
FROM work_orders wo
LEFT JOIN work_order_statuses wos ON wo.status_id = wos.id
WHERE DATE(wo.created_at) >= DATE_FORMAT(NOW(), '%Y-%m-01');
```

#### B. WO by Priority
```sql
-- Work Orders by Priority Level
SELECT 
    wop.priority_name,
    wop.priority_level,
    COUNT(wo.id) as wo_count,
    COUNT(CASE WHEN wos.is_final_status = 1 THEN 1 END) as completed,
    COUNT(CASE WHEN wos.is_final_status = 0 THEN 1 END) as pending,
    AVG(TIMESTAMPDIFF(HOUR, wo.created_at, wo.completed_at)) as avg_resolution_hours
FROM work_orders wo
LEFT JOIN work_order_priorities wop ON wo.priority_id = wop.id
LEFT JOIN work_order_statuses wos ON wo.status_id = wos.id
WHERE DATE(wo.created_at) >= DATE_FORMAT(NOW(), '%Y-%m-01')
GROUP BY wop.id, wop.priority_name, wop.priority_level
ORDER BY wop.priority_level ASC;
```

#### C. WO by Category (Top 10)
```sql
-- Top 10 WO Categories
SELECT 
    woc.category_name,
    COUNT(wo.id) as wo_count,
    ROUND(
        (COUNT(wo.id) * 100.0 / 
        (SELECT COUNT(*) FROM work_orders WHERE DATE(created_at) >= DATE_FORMAT(NOW(), '%Y-%m-01'))), 2
    ) as percentage,
    AVG(TIMESTAMPDIFF(HOUR, wo.created_at, wo.completed_at)) as avg_resolution_hours
FROM work_orders wo
INNER JOIN work_order_categories woc ON wo.category_id = woc.id
WHERE DATE(wo.created_at) >= DATE_FORMAT(NOW(), '%Y-%m-01')
GROUP BY woc.id, woc.category_name
ORDER BY wo_count DESC
LIMIT 10;
```

#### D. WO by Geographic Area
```sql
-- Work Orders by Service Area
SELECT 
    a.nama_area as area_name,
    a.kota as city,
    COUNT(wo.id) as wo_count,
    COUNT(CASE WHEN wos.is_final_status = 1 THEN 1 END) as completed,
    ROUND(
        (COUNT(CASE WHEN wos.is_final_status = 1 THEN 1 END) * 100.0 / 
        NULLIF(COUNT(wo.id), 0)), 2
    ) as completion_rate,
    ROUND(
        (COUNT(wo.id) * 100.0 / 
        (SELECT COUNT(*) FROM work_orders WHERE DATE(created_at) >= DATE_FORMAT(NOW(), '%Y-%m-01'))), 2
    ) as percentage
FROM work_orders wo
LEFT JOIN inventory_unit iu ON wo.unit_id = iu.id_inventory_unit
LEFT JOIN areas a ON iu.area_id = a.id
LEFT JOIN work_order_statuses wos ON wo.status_id = wos.id
WHERE DATE(wo.created_at) >= DATE_FORMAT(NOW(), '%Y-%m-01')
AND a.nama_area IS NOT NULL
GROUP BY a.id, a.nama_area, a.kota
ORDER BY wo_count DESC
LIMIT 10;
```

#### E. PMPS Schedule Tracking
```sql
-- PMPS Schedule with Alerts
SELECT 
    COUNT(*) as total_pmps,
    COUNT(CASE WHEN wo.due_date < CURDATE() AND wos.is_final_status = 0 THEN 1 END) as overdue,
    COUNT(CASE WHEN wo.due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) 
               AND wos.is_final_status = 0 THEN 1 END) as due_7_days,
    COUNT(CASE WHEN wo.due_date BETWEEN DATE_ADD(CURDATE(), INTERVAL 8 DAY) 
               AND DATE_ADD(CURDATE(), INTERVAL 30 DAY) 
               AND wos.is_final_status = 0 THEN 1 END) as due_30_days,
    ROUND(
        (COUNT(CASE WHEN wos.is_final_status = 1 THEN 1 END) * 100.0 / 
        NULLIF(COUNT(*), 0)), 2
    ) as compliance_rate
FROM work_orders wo
LEFT JOIN work_order_statuses wos ON wo.status_id = wos.id
WHERE wo.order_type = 'PMPS';
```

#### F. Mechanic Performance Leaderboard
```sql
-- Top 10 Performing Mechanics
SELECT 
    e.nama_lengkap as mechanic_name,
    COUNT(woa.work_order_id) as assigned_wo,
    COUNT(CASE WHEN wos.is_final_status = 1 THEN 1 END) as completed_wo,
    ROUND(
        (COUNT(CASE WHEN wos.is_final_status = 1 THEN 1 END) * 100.0 / 
        NULLIF(COUNT(woa.work_order_id), 0)), 2
    ) as completion_rate,
    AVG(TIMESTAMPDIFF(HOUR, wo.created_at, wo.completed_at)) as avg_resolution_hours
FROM work_order_assignments woa
INNER JOIN employees e ON woa.employee_id = e.id
INNER JOIN work_orders wo ON woa.work_order_id = wo.id
LEFT JOIN work_order_statuses wos ON wo.status_id = wos.id
WHERE DATE(wo.created_at) >= DATE_FORMAT(NOW(), '%Y-%m-01')
GROUP BY e.id, e.nama_lengkap
HAVING assigned_wo >= 5
ORDER BY completion_rate DESC, avg_resolution_hours ASC
LIMIT 10;
```

---

### 4️⃣ PURCHASING & PROCUREMENT METRICS

#### A. Purchase Order Overview
```sql
-- PO Performance Summary
SELECT 
    COUNT(*) as total_po,
    COUNT(CASE WHEN status = 'draft' THEN 1 END) as draft,
    COUNT(CASE WHEN status = 'submitted' THEN 1 END) as submitted,
    COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved,
    COUNT(CASE WHEN status = 'in_progress' THEN 1 END) as in_progress,
    COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed,
    SUM(total_price) as total_value,
    AVG(total_price) as avg_po_value
FROM purchase_orders
WHERE DATE(order_date) >= DATE_FORMAT(NOW(), '%Y-%m-01');
```

#### B. PO by Type Distribution
```sql
-- PO Distribution by Item Type
SELECT 
    'Units' as po_type,
    COUNT(DISTINCT po.id) as po_count,
    SUM(po.total_price) as total_value
FROM purchase_orders po
INNER JOIN po_units pu ON po.id = pu.purchase_order_id
WHERE DATE(po.order_date) >= DATE_FORMAT(NOW(), '%Y-%m-01')

UNION ALL

SELECT 
    'Attachments' as po_type,
    COUNT(DISTINCT po.id) as po_count,
    SUM(po.total_price) as total_value
FROM purchase_orders po
INNER JOIN po_attachment pa ON po.id = pa.purchase_order_id
WHERE DATE(po.order_date) >= DATE_FORMAT(NOW(), '%Y-%m-01')

UNION ALL

SELECT 
    'Spareparts' as po_type,
    COUNT(DISTINCT po.id) as po_count,
    SUM(po.total_price) as total_value
FROM purchase_orders po
INNER JOIN po_sparepart_items psi ON po.id = psi.purchase_order_id
WHERE DATE(po.order_date) >= DATE_FORMAT(NOW(), '%Y-%m-01');
```

#### C. Supplier Performance
```sql
-- Top 5 Suppliers by Volume & Performance
SELECT 
    s.supplier_name,
    COUNT(DISTINCT po.id) as po_count,
    SUM(po.total_price) as total_value,
    COUNT(DISTINCT pod.id) as delivery_count,
    COUNT(CASE WHEN pod.actual_delivery_date <= pod.expected_delivery_date THEN 1 END) as on_time_deliveries,
    ROUND(
        (COUNT(CASE WHEN pod.actual_delivery_date <= pod.expected_delivery_date THEN 1 END) * 100.0 / 
        NULLIF(COUNT(DISTINCT pod.id), 0)), 2
    ) as on_time_rate
FROM suppliers s
INNER JOIN purchase_orders po ON s.id = po.supplier_id
LEFT JOIN po_deliveries pod ON po.id = pod.purchase_order_id
WHERE DATE(po.order_date) >= DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 3 MONTH), '%Y-%m-01')
GROUP BY s.id, s.supplier_name
ORDER BY po_count DESC
LIMIT 5;
```

#### D. Delivery Tracking
```sql
-- Delivery Status Summary
SELECT 
    COUNT(*) as total_deliveries,
    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
    COUNT(CASE WHEN status = 'partial' THEN 1 END) as partial,
    COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed,
    COUNT(CASE WHEN actual_delivery_date <= expected_delivery_date THEN 1 END) as on_time,
    ROUND(
        (COUNT(CASE WHEN actual_delivery_date <= expected_delivery_date THEN 1 END) * 100.0 / 
        NULLIF(COUNT(*), 0)), 2
    ) as on_time_rate,
    AVG(DATEDIFF(actual_delivery_date, expected_delivery_date)) as avg_delay_days
FROM po_deliveries
WHERE DATE(created_at) >= DATE_FORMAT(NOW(), '%Y-%m-01');
```

#### E. Warehouse Verification Status
```sql
-- Verification Tracking
SELECT 
    COUNT(DISTINCT pov.id) as total_verifications,
    COUNT(CASE WHEN pov.verification_status = 'pending' THEN 1 END) as pending,
    COUNT(CASE WHEN pov.verification_status = 'verified' THEN 1 END) as verified,
    COUNT(CASE WHEN pov.verification_status = 'rejected' THEN 1 END) as rejected,
    ROUND(
        (COUNT(CASE WHEN pov.verification_status = 'rejected' THEN 1 END) * 100.0 / 
        NULLIF(COUNT(*), 0)), 2
    ) as rejection_rate
FROM po_verification pov
WHERE DATE(pov.verification_date) >= DATE_FORMAT(NOW(), '%Y-%m-01');
```

---

### 5️⃣ OPERATIONAL METRICS (SPK & DI)

#### A. Delivery Instruction Performance
```sql
-- DI Overview with Metrics
SELECT 
    COUNT(*) as total_di,
    COUNT(CASE WHEN status_di = 'DRAFT' THEN 1 END) as draft,
    COUNT(CASE WHEN status_di = 'PENDING' THEN 1 END) as pending,
    COUNT(CASE WHEN status_di = 'APPROVED' THEN 1 END) as approved,
    COUNT(CASE WHEN status_di = 'IN_TRANSIT' THEN 1 END) as in_transit,
    COUNT(CASE WHEN status_di = 'SELESAI' THEN 1 END) as completed,
    ROUND(
        (COUNT(CASE WHEN status_di = 'SELESAI' THEN 1 END) * 100.0 / 
        NULLIF(COUNT(*), 0)), 2
    ) as completion_rate,
    AVG(TIMESTAMPDIFF(DAY, tanggal_kirim, 
        CASE WHEN status_di = 'SELESAI' THEN updated_at ELSE NULL END)
    ) as avg_completion_days
FROM delivery_instructions
WHERE DATE(tanggal_kirim) >= DATE_FORMAT(NOW(), '%Y-%m-01');
```

#### B. DI by Type Distribution
```sql
-- DI by Work Type
SELECT 
    COALESCE(jpk.nama, 'Unknown') as work_type,
    COUNT(di.id) as di_count,
    ROUND(
        (COUNT(di.id) * 100.0 / 
        (SELECT COUNT(*) FROM delivery_instructions WHERE DATE(tanggal_kirim) >= DATE_FORMAT(NOW(), '%Y-%m-01'))), 2
    ) as percentage
FROM delivery_instructions di
LEFT JOIN jenis_perintah_kerja jpk ON di.jenis_perintah_kerja_id = jpk.id
WHERE DATE(di.tanggal_kirim) >= DATE_FORMAT(NOW(), '%Y-%m-01')
GROUP BY work_type
ORDER BY di_count DESC;
```

#### C. Top Delivery Locations
```sql
-- Top 10 Delivery Destinations
SELECT 
    lokasi as location,
    COUNT(*) as delivery_count,
    COUNT(CASE WHEN status_di = 'SELESAI' THEN 1 END) as completed,
    ROUND(
        (COUNT(CASE WHEN status_di = 'SELESAI' THEN 1 END) * 100.0 / 
        NULLIF(COUNT(*), 0)), 2
    ) as completion_rate
FROM delivery_instructions
WHERE DATE(tanggal_kirim) >= DATE_FORMAT(NOW(), '%Y-%m-01')
AND lokasi IS NOT NULL
GROUP BY lokasi
ORDER BY delivery_count DESC
LIMIT 10;
```

#### D. Temporary Units Tracking
```sql
-- Temporary Unit Status
SELECT 
    COUNT(*) as total_temporary_units,
    COUNT(CASE WHEN return_date IS NULL THEN 1 END) as still_temporary,
    COUNT(CASE WHEN return_date IS NOT NULL THEN 1 END) as returned,
    COUNT(CASE WHEN return_date IS NULL AND expected_return_date < CURDATE() THEN 1 END) as overdue_returns,
    AVG(DATEDIFF(COALESCE(return_date, CURDATE()), assignment_date)) as avg_days_temporary
FROM (
    -- Query to get temporary unit assignments
    SELECT 
        di.id,
        di.tanggal_kirim as assignment_date,
        di.expected_return_date,
        di.return_date
    FROM delivery_instructions di
    WHERE di.is_temporary = 1
) temp_units;
```

---

### 6️⃣ CUSTOMER METRICS

#### A. Customer Portfolio Overview
```sql
-- Customer Overview with Growth
SELECT 
    COUNT(*) as total_customers,
    COUNT(CASE WHEN DATE(created_at) >= DATE_FORMAT(NOW(), '%Y-%m-01') THEN 1 END) as new_this_month,
    COUNT(CASE WHEN EXISTS(
        SELECT 1 FROM kontrak k 
        WHERE k.id_customer = customers.id_customer 
        AND k.status = 'Aktif'
    ) THEN 1 END) as active_customers,
    (SELECT COUNT(*) FROM customers 
     WHERE DATE(created_at) >= DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 MONTH), '%Y-%m-01')
     AND DATE(created_at) < DATE_FORMAT(NOW(), '%Y-%m-01')
    ) as last_month_new
FROM customers;
```

#### B. Top 10 Customers by Value
```sql
-- Top Customers Ranked by Contract Value
SELECT 
    c.nama_customer as customer_name,
    COUNT(DISTINCT k.id_kontrak) as active_contracts,
    COUNT(DISTINCT ks.id_inventory_unit) as rented_units,
    SUM(k.nilai_kontrak) as total_contract_value,
    COUNT(DISTINCT wo.id) as total_wo,
    AVG(TIMESTAMPDIFF(HOUR, wo.created_at, wo.completed_at)) as avg_wo_resolution_hours
FROM customers c
LEFT JOIN kontrak k ON c.id_customer = k.id_customer AND k.status = 'Aktif'
LEFT JOIN kontrak_spesifikasi ks ON k.id_kontrak = ks.kontrak_id
LEFT JOIN inventory_unit iu ON ks.id_inventory_unit = iu.id_inventory_unit
LEFT JOIN work_orders wo ON iu.id_inventory_unit = wo.unit_id
GROUP BY c.id_customer, c.nama_customer
ORDER BY total_contract_value DESC
LIMIT 10;
```

#### C. Customer Satisfaction Metrics
```sql
-- Customer Service Quality Indicators
SELECT 
    c.nama_customer,
    COUNT(DISTINCT wo.id) as total_wo,
    AVG(TIMESTAMPDIFF(HOUR, wo.created_at, wo.completed_at)) as avg_resolution_hours,
    COUNT(CASE WHEN wo.priority_id = 1 THEN 1 END) as critical_wo,
    COUNT(CASE WHEN wo.due_date < wo.completed_at THEN 1 END) as late_completions,
    ROUND(
        (COUNT(CASE WHEN wo.due_date >= wo.completed_at OR wo.due_date IS NULL THEN 1 END) * 100.0 / 
        NULLIF(COUNT(*), 0)), 2
    ) as sla_compliance_rate
FROM customers c
LEFT JOIN kontrak k ON c.id_customer = k.id_customer
LEFT JOIN kontrak_spesifikasi ks ON k.id_kontrak = ks.kontrak_id
LEFT JOIN inventory_unit iu ON ks.id_inventory_unit = iu.id_inventory_unit
LEFT JOIN work_orders wo ON iu.id_inventory_unit = wo.unit_id
WHERE DATE(wo.created_at) >= DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 3 MONTH), '%Y-%m-01')
GROUP BY c.id_customer, c.nama_customer
HAVING total_wo > 0
ORDER BY sla_compliance_rate DESC;
```

---

### 7️⃣ HUMAN RESOURCES METRICS

#### A. Employee Distribution
```sql
-- Workforce by Division
SELECT 
    d.nama_divisi as division_name,
    COUNT(e.id) as employee_count,
    COUNT(CASE WHEN e.status = 'active' THEN 1 END) as active_employees,
    COUNT(DISTINCT p.id) as positions
FROM employees e
LEFT JOIN divisions d ON e.division_id = d.id
LEFT JOIN positions p ON e.position_id = p.id
GROUP BY d.id, d.nama_divisi
ORDER BY employee_count DESC;
```

#### B. Service Team Workload
```sql
-- Mechanic Workload Distribution
SELECT 
    e.nama_lengkap as mechanic_name,
    COUNT(woa.work_order_id) as assigned_wo,
    COUNT(CASE WHEN wos.is_final_status = 0 THEN 1 END) as pending_wo,
    COUNT(CASE WHEN wos.is_final_status = 1 THEN 1 END) as completed_wo,
    STRING_AGG(DISTINCT a.nama_area, ', ') as assigned_areas
FROM employees e
LEFT JOIN work_order_assignments woa ON e.id = woa.employee_id
LEFT JOIN work_orders wo ON woa.work_order_id = wo.id
LEFT JOIN work_order_statuses wos ON wo.status_id = wos.id
LEFT JOIN area_employee_assignments aea ON e.id = aea.employee_id
LEFT JOIN areas a ON aea.area_id = a.id
WHERE e.position_id IN (SELECT id FROM positions WHERE nama_posisi LIKE '%Mechanic%')
AND (woa.work_order_id IS NULL OR DATE(wo.created_at) >= DATE_FORMAT(NOW(), '%Y-%m-01'))
GROUP BY e.id, e.nama_lengkap
ORDER BY pending_wo DESC, assigned_wo DESC;
```

---

### 8️⃣ SYSTEM HEALTH METRICS

#### A. System Activity Overview
```sql
-- System Usage Statistics
SELECT 
    COUNT(*) as total_activities,
    COUNT(DISTINCT user_id) as active_users,
    COUNT(DISTINCT DATE(created_at)) as active_days,
    action_type,
    COUNT(*) as action_count
FROM system_activity_log
WHERE DATE(created_at) >= DATE_FORMAT(NOW(), '%Y-%m-01')
GROUP BY action_type
ORDER BY action_count DESC
LIMIT 10;
```

#### B. Active User Sessions
```sql
-- Current Active Sessions
SELECT 
    COUNT(*) as total_sessions,
    COUNT(CASE WHEN last_activity >= DATE_SUB(NOW(), INTERVAL 15 MINUTE) THEN 1 END) as active_now,
    COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as today_sessions,
    AVG(TIMESTAMPDIFF(MINUTE, created_at, last_activity)) as avg_session_duration_min
FROM user_sessions
WHERE is_active = 1;
```

#### C. Notification Performance
```sql
-- Notification Statistics
SELECT 
    COUNT(*) as total_notifications,
    COUNT(CASE WHEN is_read = 1 THEN 1 END) as read_notifications,
    COUNT(CASE WHEN is_read = 0 THEN 1 END) as unread_notifications,
    AVG(TIMESTAMPDIFF(MINUTE, created_at, read_at)) as avg_read_time_minutes,
    event_type,
    COUNT(*) as count_by_type
FROM notifications
WHERE DATE(created_at) >= DATE_FORMAT(NOW(), '%Y-%m-01')
GROUP BY event_type
ORDER BY count_by_type DESC;
```

---

## 📊 CONTROLLER METHOD STRUCTURE

### Dashboard Controller Enhancement

```php
<?php
namespace App\Controllers;

class DashboardEnhanced extends BaseController
{
    protected $db;
    protected $cacheService;
    
    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->cacheService = \Config\Services::cache();
    }
    
    public function index()
    {
        $data = [
            'title' => 'Executive Dashboard',
            
            // Phase 1: Critical Metrics
            'kpi_summary' => $this->getKPISummary(),
            'asset_metrics' => $this->getAssetMetrics(),
            'service_metrics' => $this->getServiceMetrics(),
            'operational_metrics' => $this->getOperationalMetrics(),
            
            // Phase 2: Detailed Analytics
            'marketing_metrics' => $this->getMarketingMetrics(),
            'purchasing_metrics' => $this->getPurchasingMetrics(),
            'customer_metrics' => $this->getCustomerMetrics(),
            
            // Phase 3: Advanced Insights
            'trends' => $this->getTrendAnalysis(),
            'alerts' => $this->getCriticalAlerts(),
            
            // System
            'last_updated' => date('Y-m-d H:i:s'),
        ];
        
        return view('dashboard_enhanced', $data);
    }
    
    /**
     * KPI Summary - Top Level Metrics
     */
    private function getKPISummary()
    {
        return $this->getCachedData('kpi_summary', function() {
            return [
                'asset_utilization' => $this->calculateAssetUtilization(),
                'wo_completion_rate' => $this->calculateWOCompletionRate(),
                'customer_satisfaction' => $this->calculateCustomerSatisfaction(),
                'revenue_growth' => $this->calculateRevenueGrowth(),
                'pmps_compliance' => $this->calculatePMPSCompliance(),
            ];
        }, 300); // 5 minutes cache
    }
    
    /**
     * Asset Metrics with Detailed Breakdown
     */
    private function getAssetMetrics()
    {
        return $this->getCachedData('asset_metrics', function() {
            return [
                'unit_status' => $this->getUnitStatusDistribution(),
                'unit_utilization' => $this->getUnitUtilizationRate(),
                'unit_by_area' => $this->getUnitDistributionByArea(),
                'unit_aging' => $this->getUnitAgingAnalysis(),
                'attachment_status' => $this->getAttachmentStatus(),
                'sparepart_alerts' => $this->getSparepartLowStockAlerts(),
                'top_used_spareparts' => $this->getTopUsedSpareparts(),
            ];
        }, 300);
    }
    
    /**
     * Service & Maintenance Metrics
     */
    private function getServiceMetrics()
    {
        return $this->getCachedData('service_metrics', function() {
            return [
                'wo_overview' => $this->getWOOverview(),
                'wo_by_priority' => $this->getWOByPriority(),
                'wo_by_category' => $this->getWOByCategory(),
                'wo_by_area' => $this->getWOByArea(),
                'pmps_schedule' => $this->getPMPSSchedule(),
                'mechanic_performance' => $this->getMechanicPerformance(),
                'response_time_metrics' => $this->getResponseTimeMetrics(),
            ];
        }, 300);
    }
    
    /**
     * Operational Metrics (SPK & DI)
     */
    private function getOperationalMetrics()
    {
        return $this->getCachedData('operational_metrics', function() {
            return [
                'di_overview' => $this->getDIOverview(),
                'di_by_type' => $this->getDIByType(),
                'di_top_locations' => $this->getDITopLocations(),
                'temporary_units' => $this->getTemporaryUnitsTracking(),
                'spk_performance' => $this->getSPKPerformance(),
            ];
        }, 300);
    }
    
    /**
     * Helper: Get cached data with fallback
     */
    private function getCachedData($key, $callback, $ttl = 300)
    {
        $cacheKey = 'dashboard_' . $key;
        
        try {
            if ($cached = $this->cacheService->get($cacheKey)) {
                return $cached;
            }
            
            $data = $callback();
            $this->cacheService->save($cacheKey, $data, $ttl);
            
            return $data;
        } catch (\Exception $e) {
            log_message('error', 'Dashboard cache error: ' . $e->getMessage());
            return $callback(); // Fallback to direct execution
        }
    }
    
    /**
     * Calculate Asset Utilization Rate
     */
    private function calculateAssetUtilization()
    {
        $query = "
            SELECT 
                COUNT(CASE WHEN status_unit_id = 2 THEN 1 END) as rented_units,
                COUNT(CASE WHEN status_unit_id IN (1,2) THEN 1 END) as usable_units,
                ROUND(
                    (COUNT(CASE WHEN status_unit_id = 2 THEN 1 END) * 100.0 / 
                    NULLIF(COUNT(CASE WHEN status_unit_id IN (1,2) THEN 1 END), 0)), 2
                ) as utilization_rate
            FROM inventory_unit
            WHERE status_unit_id IN (1,2,4,5)
        ";
        
        $result = $this->db->query($query)->getRowArray();
        
        return [
            'rate' => $result['utilization_rate'] ?? 0,
            'rented' => $result['rented_units'] ?? 0,
            'usable' => $result['usable_units'] ?? 0,
            'target' => 75, // Target utilization
            'status' => $this->getStatusIndicator($result['utilization_rate'] ?? 0, 75, 60),
        ];
    }
    
    /**
     * Get status indicator based on threshold
     */
    private function getStatusIndicator($value, $good_threshold, $warning_threshold)
    {
        if ($value >= $good_threshold) {
            return 'success';
        } elseif ($value >= $warning_threshold) {
            return 'warning';
        } else {
            return 'danger';
        }
    }
    
    /**
     * Get Critical Alerts for immediate attention
     */
    private function getCriticalAlerts()
    {
        $alerts = [];
        
        // Overdue PMPS
        $overdue_pmps = $this->db->query("
            SELECT COUNT(*) as count 
            FROM work_orders wo
            LEFT JOIN work_order_statuses wos ON wo.status_id = wos.id
            WHERE wo.order_type = 'PMPS'
            AND wos.is_final_status = 0
            AND wo.due_date < CURDATE()
        ")->getRow()->count;
        
        if ($overdue_pmps > 0) {
            $alerts[] = [
                'type' => 'critical',
                'icon' => 'fa-exclamation-triangle',
                'title' => 'Overdue PMPS',
                'message' => "$overdue_pmps PMPS work orders are overdue",
                'link' => '/service/work-orders?filter=overdue_pmps',
            ];
        }
        
        // Low stock spareparts
        $low_stock = $this->db->query("
            SELECT COUNT(*) as count
            FROM inventory_spareparts inv
            INNER JOIN sparepart sp ON inv.sparepart_id = sp.id_sparepart
            WHERE inv.stok < sp.minimum_stok
        ")->getRow()->count;
        
        if ($low_stock > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'fa-box',
                'title' => 'Low Stock Alert',
                'message' => "$low_stock spareparts below minimum stock",
                'link' => '/warehouse/inventory/spareparts?filter=low_stock',
            ];
        }
        
        // Expiring contracts (7 days)
        $expiring = $this->db->query("
            SELECT COUNT(*) as count
            FROM kontrak
            WHERE status = 'Aktif'
            AND tanggal_berakhir BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
        ")->getRow()->count;
        
        if ($expiring > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'fa-file-contract',
                'title' => 'Contracts Expiring Soon',
                'message' => "$expiring contracts expire in 7 days",
                'link' => '/marketing/contracts?filter=expiring',
            ];
        }
        
        return $alerts;
    }
}
```

---

## 🎨 VIEW COMPONENT EXAMPLES

### Stats Card Component
```php
<!-- views/components/stats_card.php -->
<div class="stats-card <?= $class ?? '' ?>">
    <div class="stats-icon <?= $color ?? 'primary' ?>">
        <i class="fas fa-<?= $icon ?>"></i>
    </div>
    <div class="stats-content">
        <h6 class="stats-label"><?= $label ?></h6>
        <h3 class="stats-value">
            <?= $value ?>
            <?php if (isset($trend)): ?>
                <span class="stats-trend <?= $trend > 0 ? 'up' : 'down' ?>">
                    <i class="fas fa-arrow-<?= $trend > 0 ? 'up' : 'down' ?>"></i>
                    <?= abs($trend) ?>%
                </span>
            <?php endif; ?>
        </h3>
        <?php if (isset($subtitle)): ?>
            <small class="text-muted"><?= $subtitle ?></small>
        <?php endif; ?>
    </div>
</div>
```

### Chart Card Component
```php
<!-- views/components/chart_card.php -->
<div class="chart-card">
    <div class="chart-header">
        <h6 class="chart-title">
            <i class="fas fa-<?= $icon ?? 'chart-bar' ?> me-2"></i>
            <?= $title ?>
        </h6>
        <?php if (isset($subtitle)): ?>
            <small class="text-muted"><?= $subtitle ?></small>
        <?php endif; ?>
    </div>
    <div class="chart-body">
        <canvas id="<?= $chartId ?>" height="<?= $height ?? '200' ?>"></canvas>
    </div>
    <?php if (isset($footer)): ?>
        <div class="chart-footer">
            <?= $footer ?>
        </div>
    <?php endif; ?>
</div>
```

### Alert Card Component
```php
<!-- views/components/alert_card.php -->
<div class="alert-card alert-<?= $type ?? 'info' ?>">
    <div class="alert-icon">
        <i class="fas fa-<?= $icon ?? 'info-circle' ?>"></i>
    </div>
    <div class="alert-content">
        <h6 class="alert-title"><?= $title ?></h6>
        <p class="alert-message"><?= $message ?></p>
        <?php if (isset($link)): ?>
            <a href="<?= $link ?>" class="alert-action">View Details →</a>
        <?php endif; ?>
    </div>
</div>
```

---

## 📱 JAVASCRIPT CHART INITIALIZATION

### Chart.js Configuration Template
```javascript
// dashboard_charts.js

// Chart defaults
Chart.defaults.font.family = 'Inter, sans-serif';
Chart.defaults.font.size = 12;
Chart.defaults.color = '#6c757d';

// Color palette
const CHART_COLORS = {
    primary: '#0061f2',
    success: '#28a745',
    warning: '#ffc107',
    danger: '#dc3545',
    info: '#17a2b8',
    secondary: '#6c757d',
};

/**
 * Initialize Unit Status Donut Chart
 */
function initUnitStatusChart(data) {
    const ctx = document.getElementById('unitStatusChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Available', 'Rented', 'Maintenance', 'Out of Service'],
            datasets: [{
                data: [
                    data.available,
                    data.rented,
                    data.maintenance,
                    data.out_of_service
                ],
                backgroundColor: [
                    CHART_COLORS.primary,
                    CHART_COLORS.success,
                    CHART_COLORS.warning,
                    CHART_COLORS.danger
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true,
                        font: { size: 11 }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            },
            cutout: '65%'
        }
    });
}

/**
 * Initialize WO Category Bar Chart
 */
function initWOCategoryChart(data) {
    const ctx = document.getElementById('woCategoryChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(item => item.category),
            datasets: [{
                label: 'Work Orders',
                data: data.map(item => item.count),
                backgroundColor: CHART_COLORS.danger,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                },
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            }
        }
    });
}

/**
 * Initialize Trend Line Chart
 */
function initTrendChart(elementId, data, label) {
    const ctx = document.getElementById(elementId).getContext('2d');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [{
                label: label,
                data: data.values,
                borderColor: CHART_COLORS.primary,
                backgroundColor: 'rgba(0, 97, 242, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

/**
 * Initialize Gauge Chart for KPI
 */
function initGaugeChart(elementId, value, target) {
    const ctx = document.getElementById(elementId).getContext('2d');
    
    const data = {
        datasets: [{
            data: [value, target - value, 100 - target],
            backgroundColor: [
                value >= target ? CHART_COLORS.success : 
                value >= target * 0.8 ? CHART_COLORS.warning : 
                CHART_COLORS.danger,
                '#e9ecef',
                '#f8f9fa'
            ],
            borderWidth: 0
        }]
    };
    
    new Chart(ctx, {
        type: 'doughnut',
        data: data,
        options: {
            responsive: true,
            circumference: 180,
            rotation: -90,
            cutout: '75%',
            plugins: {
                legend: { display: false },
                tooltip: { enabled: false }
            }
        },
        plugins: [{
            afterDraw: function(chart) {
                const ctx = chart.ctx;
                const centerX = chart.width / 2;
                const centerY = chart.height;
                
                ctx.font = 'bold 24px Inter';
                ctx.fillStyle = '#212529';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(value + '%', centerX, centerY - 20);
                
                ctx.font = '12px Inter';
                ctx.fillStyle = '#6c757d';
                ctx.fillText('Target: ' + target + '%', centerX, centerY);
            }
        }]
    });
}

/**
 * Auto-refresh dashboard data
 */
function refreshDashboard() {
    fetch('/dashboard/refresh', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        // Update stats cards
        updateStatsCards(data);
        
        // Update charts
        updateCharts(data);
        
        // Update last refresh time
        document.getElementById('lastRefresh').textContent = 
            'Last updated: ' + new Date().toLocaleTimeString();
    })
    .catch(error => {
        console.error('Error refreshing dashboard:', error);
    });
}

// Auto-refresh every 5 minutes
setInterval(refreshDashboard, 300000);
```

---

## 🗄️ DATABASE OPTIMIZATION

### Recommended Indexes
```sql
-- Asset & Inventory Indexes
CREATE INDEX idx_inventory_unit_status ON inventory_unit(status_unit_id);
CREATE INDEX idx_inventory_unit_area ON inventory_unit(area_id);
CREATE INDEX idx_inventory_attachment_status ON inventory_attachment(attachment_status);
CREATE INDEX idx_inventory_attachment_type ON inventory_attachment(tipe_item);

-- Work Order Indexes
CREATE INDEX idx_work_orders_status ON work_orders(status_id);
CREATE INDEX idx_work_orders_priority ON work_orders(priority_id);
CREATE INDEX idx_work_orders_category ON work_orders(category_id);
CREATE INDEX idx_work_orders_created ON work_orders(created_at);
CREATE INDEX idx_work_orders_due ON work_orders(due_date);
CREATE INDEX idx_work_orders_type ON work_orders(order_type);

-- Contract Indexes
CREATE INDEX idx_kontrak_status ON kontrak(status);
CREATE INDEX idx_kontrak_customer ON kontrak(id_customer);
CREATE INDEX idx_kontrak_dates ON kontrak(tanggal_mulai, tanggal_berakhir);

-- Purchase Order Indexes
CREATE INDEX idx_purchase_orders_status ON purchase_orders(status);
CREATE INDEX idx_purchase_orders_supplier ON purchase_orders(supplier_id);
CREATE INDEX idx_purchase_orders_date ON purchase_orders(order_date);

-- Delivery Instruction Indexes
CREATE INDEX idx_delivery_instructions_status ON delivery_instructions(status_di);
CREATE INDEX idx_delivery_instructions_date ON delivery_instructions(tanggal_kirim);

-- System Activity Indexes
CREATE INDEX idx_system_activity_user ON system_activity_log(user_id);
CREATE INDEX idx_system_activity_date ON system_activity_log(created_at);
CREATE INDEX idx_system_activity_action ON system_activity_log(action_type);
```

### Materialized View for Dashboard
```sql
-- Create materialized view for dashboard metrics
CREATE VIEW vw_dashboard_metrics AS
SELECT 
    'asset_utilization' as metric_name,
    ROUND(
        (COUNT(CASE WHEN status_unit_id = 2 THEN 1 END) * 100.0 / 
        NULLIF(COUNT(CASE WHEN status_unit_id IN (1,2) THEN 1 END), 0)), 2
    ) as metric_value,
    NOW() as calculated_at
FROM inventory_unit
WHERE status_unit_id IN (1,2,4,5)

UNION ALL

SELECT 
    'wo_completion_rate' as metric_name,
    ROUND(
        (COUNT(CASE WHEN wos.is_final_status = 1 THEN 1 END) * 100.0 / 
        NULLIF(COUNT(*), 0)), 2
    ) as metric_value,
    NOW() as calculated_at
FROM work_orders wo
LEFT JOIN work_order_statuses wos ON wo.status_id = wos.id
WHERE DATE(wo.created_at) >= DATE_FORMAT(NOW(), '%Y-%m-01')

-- Add more metrics...
```

---

**Document End**

*This technical guide provides the SQL queries, controller methods, and implementation details needed to build the comprehensive dashboard as specified in the main audit document.*
