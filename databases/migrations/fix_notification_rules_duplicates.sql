-- ============================================================================
-- Fix Notification Rules - Remove Duplicates & Update Data
-- ============================================================================
-- Created: 2025-12-19
-- Purpose: Clean duplicate rules and fix missing data
-- ============================================================================

-- Backup table first
CREATE TABLE IF NOT EXISTS notification_rules_backup_before_fix AS 
SELECT * FROM notification_rules;

-- ============================================================================
-- STEP 1: Remove Duplicate Rules (Keep the older/better one)
-- ============================================================================

-- Delete duplicate: Delivery Created (keep 80, delete 107)
DELETE FROM notification_rules WHERE id = 107;

-- Delete duplicate: Invoice Created (keep 86, delete 104)
DELETE FROM notification_rules WHERE id = 104;

-- Delete duplicate: Quotation Created (keep 38, delete 112)
DELETE FROM notification_rules WHERE id = 112;

-- Delete duplicate: SPK Created (keep 21, delete 139)
DELETE FROM notification_rules WHERE id = 139;

-- Delete duplicate: Customer Updated (keep 28, delete 127)
DELETE FROM notification_rules WHERE id = 127;

-- Delete duplicate: Work Order Created (keep 44, delete 109)
DELETE FROM notification_rules WHERE id = 109;

-- Delete duplicate: Unit Prep Completed (keep 42, delete 102)
DELETE FROM notification_rules WHERE id = 102;

-- Delete duplicate: Unit Prep Started (keep 41, delete 101)
DELETE FROM notification_rules WHERE id = 101;

-- ============================================================================
-- STEP 2: Update URL Templates (Fill NULL values)
-- ============================================================================

-- Attachment URLs
UPDATE notification_rules SET url_template = '/warehouse/inventory/invent_attachment/{{attachment_id}}' WHERE id = 60 AND url_template IS NULL OR url_template = '/warehouse/inventory/invent_attachment';
UPDATE notification_rules SET url_template = '/service/units/{{unit_id}}' WHERE id = 61 AND url_template IS NULL OR url_template = '/warehouse/inventory/invent_attachment';

-- Budget & Contract
UPDATE notification_rules SET url_template = '/finance/budget/{{budget_id}}' WHERE id = 138;
UPDATE notification_rules SET url_template = '/marketing/contracts/{{contract_id}}' WHERE id = 125;

-- Customer
UPDATE notification_rules SET url_template = '/marketing/customers/{{customer_id}}' WHERE id = 126;
UPDATE notification_rules SET url_template = '/marketing/customers/{{customer_id}}' WHERE id = 128;

-- Delivery & DI
UPDATE notification_rules SET url_template = '/operational/delivery/{{delivery_id}}' WHERE id = 107 OR trigger_event = 'delivery_created';
UPDATE notification_rules SET url_template = '/operational/delivery/{{delivery_id}}' WHERE id = 108 OR trigger_event = 'delivery_status_changed';

-- Inspection & Maintenance
UPDATE notification_rules SET url_template = '/fleet/inspection/{{inspection_id}}' WHERE id = 132;
UPDATE notification_rules SET url_template = '/fleet/inspection/{{inspection_id}}' WHERE id = 133;
UPDATE notification_rules SET url_template = '/service/maintenance/{{maintenance_id}}' WHERE id = 134;
UPDATE notification_rules SET url_template = '/service/maintenance/{{maintenance_id}}' WHERE id = 135;

-- Invoice & Payment
UPDATE notification_rules SET url_template = '/accounting/invoices/{{invoice_id}}' WHERE id = 105;
UPDATE notification_rules SET url_template = '/accounting/payments/{{payment_id}}' WHERE id = 136;
UPDATE notification_rules SET url_template = '/accounting/invoices/{{invoice_id}}' WHERE id = 137;

-- PO
UPDATE notification_rules SET url_template = '/purchasing/po/{{po_id}}' WHERE id = 106;
UPDATE notification_rules SET url_template = '/warehouse/verification/{{po_id}}' WHERE id = 111;

-- Quotation
UPDATE notification_rules SET url_template = '/marketing/quotations/{{quotation_id}}' WHERE id = 113;
UPDATE notification_rules SET url_template = '/marketing/quotations/{{quotation_id}}' WHERE id = 114;
UPDATE notification_rules SET url_template = '/marketing/quotations/{{quotation_id}}' WHERE id = 115;
UPDATE notification_rules SET url_template = '/marketing/quotations/{{quotation_id}}' WHERE id = 141;
UPDATE notification_rules SET url_template = '/marketing/quotations/{{quotation_id}}' WHERE id = 142;

-- Service Assignment
UPDATE notification_rules SET url_template = '/service/assignments/{{assignment_id}}' WHERE id = 120;
UPDATE notification_rules SET url_template = '/service/assignments/{{assignment_id}}' WHERE id = 121;
UPDATE notification_rules SET url_template = '/service/assignments/{{assignment_id}}' WHERE id = 122;

-- SPK
UPDATE notification_rules SET url_template = '/service/spk/{{spk_id}}' WHERE id = 140;

-- Unit & Location
UPDATE notification_rules SET url_template = '/fleet/tracking/{{unit_id}}' WHERE id = 123;
UPDATE notification_rules SET url_template = '/warehouse/units/{{unit_id}}' WHERE id = 124;

-- Warehouse
UPDATE notification_rules SET url_template = '/warehouse/stock/{{item_id}}' WHERE id = 129;
UPDATE notification_rules SET url_template = '/warehouse/transfer/{{transfer_id}}' WHERE id = 130;
UPDATE notification_rules SET url_template = '/warehouse/stocktake/{{stocktake_id}}' WHERE id = 131;

-- Work Order
UPDATE notification_rules SET url_template = '/service/workorders/{{wo_id}}' WHERE id = 110;
UPDATE notification_rules SET url_template = '/service/workorders/{{wo_id}}' WHERE id = 116;
UPDATE notification_rules SET url_template = '/service/workorders/{{wo_id}}' WHERE id = 117;
UPDATE notification_rules SET url_template = '/service/workorders/{{wo_id}}' WHERE id = 118;
UPDATE notification_rules SET url_template = '/service/workorders/{{wo_id}}' WHERE id = 119;

-- ============================================================================
-- STEP 3: Update Title Templates (Add Department for Service Division)
-- ============================================================================

-- SPK: Add department to title
UPDATE notification_rules 
SET title_template = 'SPK Baru [{{departemen}}]: {{nomor_spk}} - {{pelanggan}}'
WHERE id = 21;

UPDATE notification_rules 
SET title_template = 'SPK Selesai [{{departemen}}]: {{nomor_spk}}'
WHERE id = 140;

UPDATE notification_rules 
SET title_template = 'SPK Assigned [{{departemen}}]: {{nomor_spk}} ke {{mechanic_name}}'
WHERE id = 100;

UPDATE notification_rules 
SET title_template = 'SPK Dibatalkan [{{departemen}}]: {{nomor_spk}}'
WHERE id = 103;

-- Work Order: Add department to title
UPDATE notification_rules 
SET title_template = 'WO Baru [{{departemen}}]: {{nomor_wo}} - {{unit_no}}'
WHERE id = 44;

UPDATE notification_rules 
SET title_template = 'WO Assigned [{{departemen}}]: {{nomor_wo}} ke {{mechanic_name}}'
WHERE id = 45;

UPDATE notification_rules 
SET title_template = 'WO Dikerjakan [{{departemen}}]: {{nomor_wo}}'
WHERE id = 46;

UPDATE notification_rules 
SET title_template = 'WO Selesai [{{departemen}}]: {{nomor_wo}}'
WHERE id = 47;

UPDATE notification_rules 
SET title_template = 'WO Dibatalkan [{{departemen}}]: {{nomor_wo}}'
WHERE id = 48;

UPDATE notification_rules 
SET title_template = 'WO Selesai [{{departemen}}]: {{wo_number}}'
WHERE id = 117;

UPDATE notification_rules 
SET title_template = 'WO Terlambat [{{departemen}}]: {{wo_number}}'
WHERE id = 118;

-- PMPS: Add department
UPDATE notification_rules 
SET title_template = 'PMPS Due [{{departemen}}]: {{unit_no}}'
WHERE id = 49;

UPDATE notification_rules 
SET title_template = 'PMPS OVERDUE [{{departemen}}]: {{unit_no}}'
WHERE id = 50;

UPDATE notification_rules 
SET title_template = 'PMPS Selesai [{{departemen}}]: {{unit_no}}'
WHERE id = 51;

-- ============================================================================
-- STEP 4: Update Message Templates (More concise but clear)
-- ============================================================================

-- DI
UPDATE notification_rules 
SET message_template = 'Delivery Instruction {{nomor_di}} telah dibuat oleh {{creator_name}} untuk customer {{customer_name}}. Segera proses.'
WHERE id = 33;

UPDATE notification_rules 
SET message_template = 'DI {{nomor_di}} siap diproses oleh tim Operational.'
WHERE id = 3;

-- SPK
UPDATE notification_rules 
SET message_template = 'SPK {{nomor_spk}} telah dibuat untuk {{pelanggan}} departemen {{departemen}}. Unit: {{unit_no}}'
WHERE id = 21;

-- Quotation
UPDATE notification_rules 
SET message_template = 'Quotation {{quotation_number}} telah dibuat untuk {{customer_name}} dengan nilai {{total_amount}}'
WHERE id = 38;

-- Customer & Contract
UPDATE notification_rules 
SET message_template = 'Customer baru {{customer_name}} telah ditambahkan. CP: {{contact_person}}, Phone: {{phone}}'
WHERE id = 126;

UPDATE notification_rules 
SET message_template = 'Kontrak {{contract_number}} telah dibuat untuk {{customer_name}} dengan nilai {{total_amount}}'
WHERE id = 125;

-- PO Reject (Critical)
UPDATE notification_rules 
SET message_template = 'PO {{nomor_po}} DITOLAK oleh {{rejected_by}}. Alasan: {{rejection_reason}}'
WHERE id = 74;

-- Invoice Overdue (Critical)
UPDATE notification_rules 
SET message_template = 'Invoice {{invoice_number}} OVERDUE sejak {{due_date}}. Customer: {{customer_name}}. Segera follow up!'
WHERE id = 89;

-- ============================================================================
-- STEP 5: Set Target Departments for Service Division Rules
-- ============================================================================

-- SPK, Work Order, PMPS untuk Service division - set departments
UPDATE notification_rules 
SET target_departments = 'Electric,Diesel'
WHERE trigger_event IN ('spk_created', 'spk_assigned', 'spk_completed', 'spk_cancelled',
                        'work_order_created', 'work_order_assigned', 'work_order_in_progress', 
                        'work_order_completed', 'work_order_cancelled',
                        'pmps_due_soon', 'pmps_overdue', 'pmps_completed')
AND target_divisions LIKE '%service%';

-- ============================================================================
-- Verification Query
-- ============================================================================
SELECT 'Backup created' as status, COUNT(*) as count FROM notification_rules_backup_before_fix
UNION ALL
SELECT 'Current rules' as status, COUNT(*) as count FROM notification_rules
UNION ALL
SELECT 'Duplicates removed' as status, 
       (SELECT COUNT(*) FROM notification_rules_backup_before_fix) - COUNT(*) as count 
FROM notification_rules;

-- Check Service rules have departments
SELECT id, name, trigger_event, target_divisions, target_departments 
FROM notification_rules 
WHERE target_divisions LIKE '%service%' 
AND trigger_event IN ('spk_created', 'work_order_created', 'pmps_due_soon')
ORDER BY trigger_event;

-- ============================================================================
-- DONE
-- ============================================================================
