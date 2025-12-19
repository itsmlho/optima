-- ============================================================================
-- COMPLETE NOTIFICATION RULES DEPLOYMENT - Phase 1, 2, 3
-- ============================================================================
-- Created: December 19, 2024
-- Purpose: Deploy all notification rules from Phase 1 (CRITICAL), 
--          Phase 2 (HIGH), and Phase 3 (MEDIUM) priorities
-- Total Rules: 39 (8 + 14 + 17)
-- ============================================================================

USE optima_ci;

-- Disable foreign key checks for faster insertion
SET FOREIGN_KEY_CHECKS = 0;

-- Show current state
SELECT CONCAT('Current rules count: ', COUNT(*)) as status FROM notification_rules;

-- ============================================================================
-- PHASE 1: CRITICAL PRIORITY (8 Rules)
-- Finance (2), Purchasing (3), WorkOrder (2), WarehousePO (1)
-- ============================================================================

-- 1. Invoice Created
INSERT IGNORE INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    type,
    priority,
    title_template,
    message_template,
    is_active
) VALUES (
    'Invoice Created Notification',
    'invoice_created',
    'Finance,Accounting,Marketing',
    'Director,Manager,Supervisor,Staff',
    'info',
    1,
    'Invoice Baru Dibuat',
    'Invoice {{invoice_number}} telah dibuat untuk {{customer_name}} dengan nilai {{amount}}. Jatuh tempo: {{due_date}}. Dibuat oleh: {{created_by}}',
    1
);

-- 2. Payment Status Updated
INSERT IGNORE INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    type,
    priority,
    title_template,
    message_template,
    is_active
) VALUES (
    'Payment Status Updated Notification',
    'payment_status_updated',
    'Finance,Accounting,Marketing',
    'Director,Manager,Supervisor',
    'success',
    1,
    'Status Pembayaran Diperbarui',
    'Status pembayaran invoice {{invoice_number}} ({{customer_name}}) telah diubah dari {{old_status}} menjadi {{new_status}}. Nilai: {{amount}}. Tanggal: {{payment_date}}. Diperbarui oleh: {{updated_by}}',
    1
);

-- 3. Purchase Order Created
INSERT IGNORE INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    type,
    priority,
    title_template,
    message_template,
    is_active
) VALUES (
    'Purchase Order Created Notification',
    'po_created',
    'Purchasing,Finance,Accounting',
    'Manager,Supervisor,Staff',
    'info',
    1,
    'Purchase Order Baru Dibuat',
    'PO {{po_number}} telah dibuat untuk supplier {{supplier_name}}. Tipe: {{po_type}}. Nilai Total: {{total_amount}}. Tanggal Kirim: {{delivery_date}}. Dibuat oleh: {{created_by}}',
    1
);

-- 4. Delivery Created
INSERT IGNORE INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    type,
    priority,
    title_template,
    message_template,
    is_active
) VALUES (
    'Delivery Created Notification',
    'delivery_created',
    'Warehouse,Purchasing,QualityControl',
    'Manager,Supervisor,Staff',
    'info',
    1,
    'Surat Jalan Baru Dibuat',
    'Surat Jalan {{delivery_number}} telah dibuat untuk PO {{po_number}}. Supplier: {{supplier_name}}. Jumlah Item: {{item_count}}. Tanggal: {{delivery_date}}. Dibuat oleh: {{created_by}}',
    1
);

-- 5. Delivery Status Changed
INSERT IGNORE INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    type,
    priority,
    title_template,
    message_template,
    is_active
) VALUES (
    'Delivery Status Changed Notification',
    'delivery_status_changed',
    'Warehouse,Purchasing,Finance',
    'Manager,Supervisor',
    'warning',
    1,
    'Status Surat Jalan Berubah',
    'Status Surat Jalan {{delivery_number}} berubah dari {{old_status}} menjadi {{new_status}}. PO: {{po_number}}. Supplier: {{supplier_name}}. Diubah oleh: {{updated_by}} pada {{updated_at}}',
    1
);

-- 6. Work Order Created
INSERT IGNORE INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    type,
    priority,
    title_template,
    message_template,
    is_active
) VALUES (
    'Work Order Created Notification',
    'workorder_created',
    'Operations,Workshop,Maintenance',
    'Manager,Supervisor,Mechanic',
    'info',
    1,
    'Work Order Baru Dibuat',
    'WO {{wo_number}} dibuat untuk unit {{unit_code}}. Kategori: {{category}}. Prioritas: {{priority}}. Target Selesai: {{target_date}}. Mekanik: {{assigned_mechanic}}. Dibuat oleh: {{created_by}}',
    1
);

-- 7. Work Order Status Changed
INSERT IGNORE INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    type,
    priority,
    title_template,
    message_template,
    is_active
) VALUES (
    'Work Order Status Changed Notification',
    'workorder_status_changed',
    'Operations,Workshop,Fleet',
    'Manager,Supervisor,FleetManager',
    'success',
    1,
    'Status Work Order Berubah',
    'Status WO {{wo_number}} (Unit {{unit_code}}) berubah dari {{old_status}} menjadi {{new_status}}. Kategori: {{category}}. Progress: {{progress}}%. Diubah oleh: {{updated_by}}',
    1
);

-- 8. PO Verification Updated
INSERT IGNORE INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    type,
    priority,
    title_template,
    message_template,
    is_active
) VALUES (
    'PO Verification Updated Notification',
    'po_verification_updated',
    'Warehouse,Purchasing,QualityControl',
    'Manager,Supervisor,QC',
    'warning',
    1,
    'Verifikasi PO Diperbarui',
    'Verifikasi PO {{po_number}} ({{delivery_number}}) diperbarui. Status: {{verification_status}}. Item Terverifikasi: {{verified_items}}/{{total_items}}. Catatan: {{notes}}. Diverifikasi oleh: {{verified_by}}',
    1
);

SELECT 'Phase 1 (CRITICAL): 8 rules inserted' as status;

-- ============================================================================
-- PHASE 2: HIGH PRIORITY (14 Rules)
-- Marketing (4), WorkOrder Extended (4), Service Assignments (3), Security (3)
-- ============================================================================

-- Marketing / Quotation (4)
INSERT IGNORE INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    type,
    priority,
    title_template,
    message_template,
    is_active
) VALUES (
    'Quotation Created Notification',
    'quotation_created',
    'Marketing,Management',
    'Director,Manager,Supervisor,Sales',
    'info',
    2,
    'Quotation Baru: {{quotation_number}}',
    'Quotation {{quotation_number}} dibuat untuk {{customer_name}}. Nilai: {{total_value}}. Stage: {{stage}}. Dibuat oleh: {{created_by}}',
    1
);

INSERT IGNORE INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    type,
    priority,
    title_template,
    message_template,
    is_active
) VALUES (
    'Quotation Updated Notification',
    'quotation_updated',
    'Marketing,Management',
    'Director,Manager,Supervisor',
    'info',
    2,
    'Quotation Diperbarui: {{quotation_number}}',
    'Quotation {{quotation_number}} ({{customer_name}}) telah diperbarui. Perubahan: {{changes}}. Diperbarui oleh: {{updated_by}}',
    1
);

INSERT IGNORE INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    type,
    priority,
    title_template,
    message_template,
    is_active
) VALUES (
    'Quotation Approved Notification',
    'quotation_approved',
    'Marketing,Finance,Management',
    'Director,Manager',
    'success',
    2,
    'Quotation Disetujui: {{quotation_number}}',
    'Quotation {{quotation_number}} ({{customer_name}}) telah disetujui. Nilai: {{total_value}}. Disetujui oleh: {{approved_by}} pada {{approved_at}}',
    1
);

INSERT IGNORE INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    type,
    priority,
    title_template,
    message_template,
    is_active
) VALUES (
    'Quotation Rejected Notification',
    'quotation_rejected',
    'Marketing,Management',
    'Director,Manager,Sales',
    'error',
    2,
    'Quotation Ditolak: {{quotation_number}}',
    'Quotation {{quotation_number}} ({{customer_name}}) telah ditolak. Alasan: {{rejection_reason}}. Ditolak oleh: {{rejected_by}} pada {{rejected_at}}',
    1
);

-- WorkOrder Extended (4)
INSERT IGNORE INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    type,
    priority,
    title_template,
    message_template,
    is_active
) VALUES (
    'Work Order Assigned Notification',
    'workorder_assigned',
    'Operations,Workshop',
    'Manager,Supervisor,Mechanic',
    'info',
    2,
    'Work Order Ditugaskan: {{wo_number}}',
    'WO {{wo_number}} (Unit {{unit_code}}) ditugaskan kepada {{assigned_to}}. Prioritas: {{priority}}. Target: {{target_date}}',
    1
);

INSERT IGNORE INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    type,
    priority,
    title_template,
    message_template,
    is_active
) VALUES (
    'Work Order Completed Notification',
    'workorder_completed',
    'Operations,Fleet,Management',
    'Director,Manager,FleetManager',
    'success',
    2,
    'Work Order Selesai: {{wo_number}}',
    'WO {{wo_number}} (Unit {{unit_code}}) telah selesai. Durasi: {{duration}} jam. Sparepart: {{parts_used}}. Biaya: {{total_cost}}. Oleh: {{completed_by}}',
    1
);

INSERT IGNORE INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    type,
    priority,
    title_template,
    message_template,
    is_active
) VALUES (
    'Work Order Delayed Notification',
    'workorder_delayed',
    'Operations,Management',
    'Director,Manager',
    'warning',
    2,
    'Work Order Terlambat: {{wo_number}}',
    'WO {{wo_number}} (Unit {{unit_code}}) terlambat {{delay_days}} hari dari target. Target: {{target_date}}. Status: {{current_status}}',
    1
);

INSERT IGNORE INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    type,
    priority,
    title_template,
    message_template,
    is_active
) VALUES (
    'Work Order Sparepart Added Notification',
    'workorder_sparepart_added',
    'Operations,Warehouse,Finance',
    'Manager,Supervisor',
    'info',
    2,
    'Sparepart Ditambahkan: WO {{wo_number}}',
    'Sparepart {{part_name}} (Qty: {{quantity}}) ditambahkan ke WO {{wo_number}}. Harga: {{unit_price}}. Total: {{total_price}}',
    1
);

-- Service Assignments (3)
INSERT IGNORE INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    type,
    priority,
    title_template,
    message_template,
    is_active
) VALUES (
    'Service Area Assignment Created Notification',
    'service_assignment_created',
    'Operations,Fleet,Marketing',
    'Manager,Supervisor',
    'info',
    2,
    'Penugasan Area Baru: {{unit_code}}',
    'Unit {{unit_code}} ditugaskan ke area {{area_name}} untuk customer {{customer_name}}. Mulai: {{start_date}}. Selesai: {{end_date}}',
    1
);

INSERT IGNORE INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    type,
    priority,
    title_template,
    message_template,
    is_active
) VALUES (
    'Service Area Assignment Updated Notification',
    'service_assignment_updated',
    'Operations,Fleet,Marketing',
    'Manager,Supervisor',
    'info',
    2,
    'Penugasan Area Diperbarui: {{unit_code}}',
    'Penugasan unit {{unit_code}} (Area {{area_name}}) diperbarui. Perubahan: {{changes}}. Oleh: {{updated_by}}',
    1
);

INSERT IGNORE INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    type,
    priority,
    title_template,
    message_template,
    is_active
) VALUES (
    'Service Area Assignment Completed Notification',
    'service_assignment_completed',
    'Operations,Fleet,Marketing,Finance',
    'Director,Manager',
    'success',
    2,
    'Penugasan Selesai: {{unit_code}}',
    'Penugasan unit {{unit_code}} di area {{area_name}} (Customer {{customer_name}}) telah selesai. Durasi: {{duration}} hari. Diselesaikan: {{completed_at}}',
    1
);

-- Security/Admin (3)
INSERT IGNORE INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    type,
    priority,
    title_template,
    message_template,
    is_active
) VALUES (
    'Unit Location Updated Notification',
    'unit_location_updated',
    'Operations,Fleet,Tracking',
    'Manager,Supervisor,FleetManager',
    'info',
    2,
    'Lokasi Unit Diperbarui: {{unit_code}}',
    'Lokasi unit {{unit_code}} diperbarui. Dari: {{old_location}} ke {{new_location}}. Koordinat: {{coordinates}}. Oleh: {{updated_by}}',
    1
);

INSERT IGNORE INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    type,
    priority,
    title_template,
    message_template,
    is_active
) VALUES (
    'Warehouse Unit Updated Notification',
    'warehouse_unit_updated',
    'Warehouse,Operations,Fleet',
    'Manager,Supervisor',
    'info',
    2,
    'Unit Warehouse Diperbarui: {{unit_code}}',
    'Status warehouse unit {{unit_code}} diperbarui. Perubahan: {{changes}}. Oleh: {{updated_by}}',
    1
);

INSERT IGNORE INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    type,
    priority,
    title_template,
    message_template,
    is_active
) VALUES (
    'Contract Created Notification',
    'contract_created',
    'Marketing,Finance,Legal,Management',
    'Director,Manager',
    'success',
    2,
    'Kontrak Baru: {{contract_number}}',
    'Kontrak {{contract_number}} dibuat untuk {{customer_name}}. Nilai: {{total_value}}. Periode: {{start_date}} - {{end_date}}. Oleh: {{created_by}}',
    1
);

SELECT 'Phase 2 (HIGH): 14 rules inserted' as status;

-- ============================================================================
-- PHASE 3: MEDIUM PRIORITY (17 Rules)
-- Customer (3), Warehouse (3), Operations (4), Finance (3), SPK (2), Marketing (2)
-- ============================================================================

-- Customer Management (3)
INSERT IGNORE INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    type,
    priority,
    title_template,
    message_template,
    is_active
) VALUES (
    'Customer Created Notification',
    'customer_created',
    'Marketing,Management',
    'Director,Manager,Supervisor,Sales',
    'info',
    3,
    'Customer Baru: {{customer_name}}',
    'Customer {{customer_name}} ({{customer_code}}) telah dibuat. Tipe: {{customer_type}}. Kontak: {{phone}} / {{email}}. Oleh: {{created_by}}',
    1
);

INSERT IGNORE INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    type,
    priority,
    title_template,
    message_template,
    is_active
) VALUES (
    'Customer Updated Notification',
    'customer_updated',
    'Marketing,Management',
    'Manager,Supervisor,Sales',
    'info',
    3,
    'Customer Diperbarui: {{customer_name}}',
    'Customer {{customer_name}} ({{customer_code}}) diperbarui. Perubahan: {{changes}}. Oleh: {{updated_by}}',
    1
);

INSERT IGNORE INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    type,
    priority,
    title_template,
    message_template,
    is_active
) VALUES (
    'Customer Status Changed Notification',
    'customer_status_changed',
    'Marketing,Management',
    'Director,Manager,Sales',
    'warning',
    2,
    'Status Customer Berubah: {{customer_name}}',
    'Status customer {{customer_name}} ({{customer_code}}) berubah dari {{old_status}} ke {{new_status}}. Alasan: {{reason}}. Oleh: {{changed_by}}',
    1
);

-- Warehouse Extended (3)
INSERT IGNORE INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    type,
    priority,
    title_template,
    message_template,
    is_active
) VALUES (
    'Warehouse Stock Alert Notification',
    'warehouse_stock_alert',
    'Warehouse,Purchasing,Management',
    'Manager,Supervisor,Procurement',
    'warning',
    2,
    'Alert Stok Rendah: {{item_name}}',
    'URGENT: Stok {{item_name}} rendah. Saat ini: {{current_stock}} {{unit}}. Minimum: {{minimum_stock}} {{unit}}. Warehouse: {{warehouse_name}}',
    1
);

INSERT IGNORE INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    type,
    priority,
    title_template,
    message_template,
    is_active
) VALUES (
    'Warehouse Transfer Completed Notification',
    'warehouse_transfer_completed',
    'Warehouse,Management',
    'Director,Manager',
    'success',
    3,
    'Transfer Selesai: {{transfer_code}}',
    'Transfer {{transfer_code}} selesai. Dari {{from_warehouse}} ke {{to_warehouse}}. Items: {{item_count}}. Oleh: {{completed_by}}',
    1
);

INSERT IGNORE INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    type,
    priority,
    title_template,
    message_template,
    is_active
) VALUES (
    'Warehouse Stocktake Completed Notification',
    'warehouse_stocktake_completed',
    'Warehouse,Finance,Management',
    'Director,Manager,Accounting',
    'success',
    2,
    'Stocktake Selesai: {{warehouse_name}}',
    'Stocktake {{stocktake_code}} selesai untuk {{warehouse_name}}. Items: {{items_counted}}. Selisih: {{discrepancies}}. Oleh: {{completed_by}}',
    1
);

-- Operations (4)
INSERT IGNORE INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    type,
    priority,
    title_template,
    message_template,
    is_active
) VALUES (
    'Inspection Scheduled Notification',
    'inspection_scheduled',
    'Operations,QualityControl',
    'Manager,Supervisor,QC',
    'info',
    3,
    'Inspeksi Dijadwalkan: {{unit_code}}',
    'Inspeksi unit {{unit_code}} dijadwalkan. Tipe: {{inspection_type}}. Tanggal: {{scheduled_date}}. Assigned: {{assigned_to}}. Prioritas: {{priority}}',
    1
);

INSERT IGNORE INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    type,
    priority,
    title_template,
    message_template,
    is_active
) VALUES (
    'Inspection Completed Notification',
    'inspection_completed',
    'Operations,Fleet,Management',
    'Director,Manager,FleetManager',
    'success',
    2,
    'Inspeksi Selesai: {{unit_code}}',
    'Inspeksi unit {{unit_code}} selesai. Tipe: {{inspection_type}}. Hasil: {{result}}. Temuan: {{findings_count}}. Oleh: {{completed_by}}',
    1
);

INSERT IGNORE INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    type,
    priority,
    title_template,
    message_template,
    is_active
) VALUES (
    'Maintenance Scheduled Notification',
    'maintenance_scheduled',
    'Operations,Maintenance',
    'Manager,Supervisor,Mechanic',
    'info',
    3,
    'Maintenance Dijadwalkan: {{unit_code}}',
    'Maintenance unit {{unit_code}} dijadwalkan. Tipe: {{maintenance_type}}. Tanggal: {{scheduled_date}}. Est: {{estimated_hours}}h. Mekanik: {{assigned_mechanic}}',
    1
);

INSERT IGNORE INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    type,
    priority,
    title_template,
    message_template,
    is_active
) VALUES (
    'Maintenance Completed Notification',
    'maintenance_completed',
    'Operations,Finance,Management',
    'Director,Manager',
    'success',
    2,
    'Maintenance Selesai: {{unit_code}}',
    'Maintenance unit {{unit_code}} selesai. Tipe: {{maintenance_type}}. Durasi: {{actual_hours}}h. Parts: {{parts_replaced}}. Biaya: Rp {{total_cost}}. Oleh: {{completed_by}}',
    1
);

-- Finance Extended (3)
INSERT IGNORE INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    type,
    priority,
    title_template,
    message_template,
    is_active
) VALUES (
    'Payment Received Notification',
    'payment_received',
    'Finance,Accounting,Marketing',
    'Director,Manager,Accounting',
    'success',
    3,
    'Pembayaran Diterima: {{invoice_number}}',
    'Pembayaran untuk invoice {{invoice_number}} diterima. Customer: {{customer_name}}. Jumlah: Rp {{amount}}. Metode: {{payment_method}}. Oleh: {{received_by}}',
    1
);

INSERT IGNORE INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    type,
    priority,
    title_template,
    message_template,
    is_active
) VALUES (
    'Payment Overdue Notification',
    'payment_overdue',
    'Finance,Management,Marketing',
    'Director,Manager,Accounting,Sales',
    'critical',
    1,
    'PEMBAYARAN TERLAMBAT: {{invoice_number}}',
    'URGENT: Invoice {{invoice_number}} terlambat {{days_overdue}} hari. Customer: {{customer_name}}. Outstanding: Rp {{outstanding_balance}}. Jatuh tempo: {{due_date}}',
    1
);

INSERT IGNORE INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    type,
    priority,
    title_template,
    message_template,
    is_active
) VALUES (
    'Budget Threshold Exceeded Notification',
    'budget_threshold_exceeded',
    'Finance,Management',
    'Director,Manager,Accounting',
    'warning',
    2,
    'Alert Budget: {{budget_name}}',
    'PERINGATAN: Budget "{{budget_name}}" ({{department}}) melebihi {{threshold}}% threshold. Alokasi: Rp {{allocated_amount}}. Terpakai: Rp {{spent_amount}} ({{percentage_used}}%)',
    1
);

-- SPK Management (2)
INSERT IGNORE INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    type,
    priority,
    title_template,
    message_template,
    is_active
) VALUES (
    'SPK Created Notification',
    'spk_created',
    'Operations,Workshop',
    'Manager,Supervisor,Mechanic',
    'info',
    3,
    'SPK Baru: {{spk_number}}',
    'SPK {{spk_number}} dibuat untuk unit {{unit_code}}. Jenis pekerjaan: {{work_type}}. Assigned: {{assigned_to}}. Target: {{target_date}}. Prioritas: {{priority}}',
    1
);

INSERT IGNORE INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    type,
    priority,
    title_template,
    message_template,
    is_active
) VALUES (
    'SPK Completed Notification',
    'spk_completed',
    'Operations,Fleet,Management',
    'Director,Manager,FleetManager',
    'success',
    2,
    'SPK Selesai: {{spk_number}}',
    'SPK {{spk_number}} (Unit {{unit_code}}) selesai. Jenis: {{work_type}}. Durasi: {{actual_duration}}h. Hasil: {{result}}. Oleh: {{completed_by}}',
    1
);

-- Additional Marketing (2)
INSERT IGNORE INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    type,
    priority,
    title_template,
    message_template,
    is_active
) VALUES (
    'Quotation Sent to Customer Notification',
    'quotation_sent_to_customer',
    'Marketing,Management',
    'Manager,Supervisor,Sales',
    'info',
    3,
    'Quotation Terkirim: {{quote_number}}',
    'Quotation {{quote_number}} telah dikirim ke {{customer_name}} via {{sent_method}}. Email: {{customer_email}}. Oleh: {{sent_by}}',
    1
);

INSERT IGNORE INTO notification_rules (
    name,
    trigger_event,
    target_divisions,
    target_roles,
    type,
    priority,
    title_template,
    message_template,
    is_active
) VALUES (
    'Quotation Follow-up Required Notification',
    'quotation_follow_up_required',
    'Marketing,Management',
    'Manager,Sales',
    'warning',
    3,
    'Follow-up Diperlukan: {{quote_number}}',
    'Quotation {{quote_number}} untuk {{customer_name}} perlu follow-up. Sudah {{days_since_sent}} hari sejak dikirim. Last contact: {{last_contact}}. Assigned: {{assigned_to}}',
    1
);

SELECT 'Phase 3 (MEDIUM): 17 rules inserted' as status;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================================
-- VERIFICATION
-- ============================================================================

SELECT '============================================================================' as separator;
SELECT 'DEPLOYMENT COMPLETE' as status;
SELECT '============================================================================' as separator;

-- Show final count
SELECT CONCAT('Total notification rules: ', COUNT(*)) as final_count FROM notification_rules;

-- Show Phase breakdown
SELECT 'Phase Breakdown:' as info;
SELECT 
    CASE 
        WHEN priority = 1 THEN 'Phase 1 (CRITICAL)'
        WHEN priority = 2 THEN 'Phase 2 (HIGH)'
        WHEN priority = 3 THEN 'Phase 3 (MEDIUM)'
        ELSE 'Other'
    END as phase,
    COUNT(*) as rule_count
FROM notification_rules
WHERE trigger_event IN (
    'invoice_created', 'payment_status_updated', 'po_created', 'delivery_created', 'delivery_status_changed',
    'workorder_created', 'workorder_status_changed', 'po_verification_updated',
    'quotation_created', 'quotation_updated', 'quotation_approved', 'quotation_rejected',
    'workorder_assigned', 'workorder_completed', 'workorder_delayed', 'workorder_sparepart_added',
    'service_assignment_created', 'service_assignment_updated', 'service_assignment_completed',
    'unit_location_updated', 'warehouse_unit_updated', 'contract_created',
    'customer_created', 'customer_updated', 'customer_status_changed',
    'warehouse_stock_alert', 'warehouse_transfer_completed', 'warehouse_stocktake_completed',
    'inspection_scheduled', 'inspection_completed', 'maintenance_scheduled', 'maintenance_completed',
    'payment_received', 'payment_overdue', 'budget_threshold_exceeded',
    'spk_created', 'spk_completed',
    'quotation_sent_to_customer', 'quotation_follow_up_required'
)
GROUP BY phase
ORDER BY priority;

-- List all deployed rules
SELECT 'Deployed Rules:' as info;
SELECT 
    id,
    name,
    trigger_event,
    CASE 
        WHEN priority = 1 THEN 'CRITICAL'
        WHEN priority = 2 THEN 'HIGH'
        WHEN priority = 3 THEN 'MEDIUM'
        ELSE 'LOW'
    END as priority_label,
    is_active
FROM notification_rules
WHERE trigger_event IN (
    'invoice_created', 'payment_status_updated', 'po_created', 'delivery_created', 'delivery_status_changed',
    'workorder_created', 'workorder_status_changed', 'po_verification_updated',
    'quotation_created', 'quotation_updated', 'quotation_approved', 'quotation_rejected',
    'workorder_assigned', 'workorder_completed', 'workorder_delayed', 'workorder_sparepart_added',
    'service_assignment_created', 'service_assignment_updated', 'service_assignment_completed',
    'unit_location_updated', 'warehouse_unit_updated', 'contract_created',
    'customer_created', 'customer_updated', 'customer_status_changed',
    'warehouse_stock_alert', 'warehouse_transfer_completed', 'warehouse_stocktake_completed',
    'inspection_scheduled', 'inspection_completed', 'maintenance_scheduled', 'maintenance_completed',
    'payment_received', 'payment_overdue', 'budget_threshold_exceeded',
    'spk_created', 'spk_completed',
    'quotation_sent_to_customer', 'quotation_follow_up_required'
)
ORDER BY priority, trigger_event;

SELECT '============================================================================' as separator;
SELECT 'All notification rules have been deployed successfully!' as message;
SELECT 'Total rules inserted: 39 (8 CRITICAL + 14 HIGH + 17 MEDIUM)' as summary;
SELECT '============================================================================' as separator;
