-- OptimaPro Activity Logging - COMPREHENSIVE RELATED FIELDS EXPANSION
-- Date: 2025-09-09
-- Purpose: Add all related_* fields for complete business entity tracking

-- ========================================================================
-- EXISTING RELATED FIELDS (sudah ada):
-- - related_kontrak_id (Marketing - Kontrak)
-- - related_spk_id (Marketing - SPK)  
-- - related_di_id (Marketing - Delivery Instructions)
-- ========================================================================

-- BACKUP FIRST
CREATE TABLE IF NOT EXISTS system_activity_log_backup_related_fields AS 
SELECT * FROM system_activity_log;

-- ========================================================================
-- ADD NEW RELATED FIELDS FOR ALL DIVISIONS
-- ========================================================================

-- MONITORING Division Related Fields
ALTER TABLE system_activity_log 
ADD COLUMN IF NOT EXISTS related_tracking_id INT(10) UNSIGNED DEFAULT NULL COMMENT 'Tracking Delivery/Work Orders',

-- MARKETING Division Related Fields (tambahan)
ADD COLUMN IF NOT EXISTS related_quotation_id INT(10) UNSIGNED DEFAULT NULL COMMENT 'Buat Penawaran',
ADD COLUMN IF NOT EXISTS related_quotation_item_id INT(10) UNSIGNED DEFAULT NULL COMMENT 'Quotation Items',

-- SERVICE Division Related Fields
ADD COLUMN IF NOT EXISTS related_spk_service_id INT(10) UNSIGNED DEFAULT NULL COMMENT 'SPK Service (Penyiapan Unit)',
ADD COLUMN IF NOT EXISTS related_maintenance_id INT(10) UNSIGNED DEFAULT NULL COMMENT 'Preventive Maintenance (PMPS)',
ADD COLUMN IF NOT EXISTS related_work_order_id INT(10) UNSIGNED DEFAULT NULL COMMENT 'Work Order',
ADD COLUMN IF NOT EXISTS related_work_order_item_id INT(10) UNSIGNED DEFAULT NULL COMMENT 'Work Order Items',
ADD COLUMN IF NOT EXISTS related_service_item_id INT(10) UNSIGNED DEFAULT NULL COMMENT 'Service Inventory Items',
ADD COLUMN IF NOT EXISTS related_unit_id INT(10) UNSIGNED DEFAULT NULL COMMENT 'Data Unit/Inventory Unit',

-- OPERATIONAL Division Related Fields  
ADD COLUMN IF NOT EXISTS related_delivery_process_id INT(10) UNSIGNED DEFAULT NULL COMMENT 'Delivery Process',
ADD COLUMN IF NOT EXISTS related_delivery_status_id INT(10) UNSIGNED DEFAULT NULL COMMENT 'Delivery Status',

-- ACCOUNTING Division Related Fields
ADD COLUMN IF NOT EXISTS related_invoice_id INT(10) UNSIGNED DEFAULT NULL COMMENT 'Invoice Management',
ADD COLUMN IF NOT EXISTS related_invoice_item_id INT(10) UNSIGNED DEFAULT NULL COMMENT 'Invoice Items',
ADD COLUMN IF NOT EXISTS related_payment_id INT(10) UNSIGNED DEFAULT NULL COMMENT 'Payment Validation',

-- PURCHASING Division Related Fields
ADD COLUMN IF NOT EXISTS related_po_id INT(10) UNSIGNED DEFAULT NULL COMMENT 'Purchase Order',
ADD COLUMN IF NOT EXISTS related_po_item_id INT(10) UNSIGNED DEFAULT NULL COMMENT 'PO Items',
ADD COLUMN IF NOT EXISTS related_po_unit_id INT(10) UNSIGNED DEFAULT NULL COMMENT 'PO Unit',
ADD COLUMN IF NOT EXISTS related_po_attachment_id INT(10) UNSIGNED DEFAULT NULL COMMENT 'PO Attachment & Battery',
ADD COLUMN IF NOT EXISTS related_po_sparepart_id INT(10) UNSIGNED DEFAULT NULL COMMENT 'PO Sparepart',
ADD COLUMN IF NOT EXISTS related_supplier_id INT(10) UNSIGNED DEFAULT NULL COMMENT 'Supplier',

-- WAREHOUSE & ASSETS Division Related Fields
ADD COLUMN IF NOT EXISTS related_inventory_unit_id INT(10) UNSIGNED DEFAULT NULL COMMENT 'Inventory - Unit',
ADD COLUMN IF NOT EXISTS related_inventory_attachment_id INT(10) UNSIGNED DEFAULT NULL COMMENT 'Inventory - Attachment & Battery',
ADD COLUMN IF NOT EXISTS related_inventory_sparepart_id INT(10) UNSIGNED DEFAULT NULL COMMENT 'Inventory - Sparepart',
ADD COLUMN IF NOT EXISTS related_po_verification_id INT(10) UNSIGNED DEFAULT NULL COMMENT 'PO Verification',
ADD COLUMN IF NOT EXISTS related_location_id INT(10) UNSIGNED DEFAULT NULL COMMENT 'Warehouse Location',

-- PERIZINAN Division Related Fields
ADD COLUMN IF NOT EXISTS related_silo_id INT(10) UNSIGNED DEFAULT NULL COMMENT 'SILO (Surat Izin Layak Operasi)',
ADD COLUMN IF NOT EXISTS related_emisi_id INT(10) UNSIGNED DEFAULT NULL COMMENT 'EMISI (Surat Izin Emisi Gas Buang)',
ADD COLUMN IF NOT EXISTS related_license_id INT(10) UNSIGNED DEFAULT NULL COMMENT 'License Management',

-- ADMINISTRATION Division Related Fields
ADD COLUMN IF NOT EXISTS related_user_id INT(10) UNSIGNED DEFAULT NULL COMMENT 'User Management',
ADD COLUMN IF NOT EXISTS related_role_id INT(10) UNSIGNED DEFAULT NULL COMMENT 'Role Management', 
ADD COLUMN IF NOT EXISTS related_permission_id INT(10) UNSIGNED DEFAULT NULL COMMENT 'Permission Management',
ADD COLUMN IF NOT EXISTS related_setting_id INT(10) UNSIGNED DEFAULT NULL COMMENT 'System Settings',
ADD COLUMN IF NOT EXISTS related_config_id INT(10) UNSIGNED DEFAULT NULL COMMENT 'Configuration';

-- ========================================================================
-- CREATE INDEXES FOR PERFORMANCE
-- ========================================================================

-- Core business entity indexes
CREATE INDEX IF NOT EXISTS idx_related_kontrak ON system_activity_log(related_kontrak_id);
CREATE INDEX IF NOT EXISTS idx_related_spk ON system_activity_log(related_spk_id);
CREATE INDEX IF NOT EXISTS idx_related_di ON system_activity_log(related_di_id);

-- New related field indexes
CREATE INDEX IF NOT EXISTS idx_related_quotation ON system_activity_log(related_quotation_id);
CREATE INDEX IF NOT EXISTS idx_related_work_order ON system_activity_log(related_work_order_id);
CREATE INDEX IF NOT EXISTS idx_related_invoice ON system_activity_log(related_invoice_id);
CREATE INDEX IF NOT EXISTS idx_related_po ON system_activity_log(related_po_id);
CREATE INDEX IF NOT EXISTS idx_related_unit ON system_activity_log(related_unit_id);
CREATE INDEX IF NOT EXISTS idx_related_maintenance ON system_activity_log(related_maintenance_id);
CREATE INDEX IF NOT EXISTS idx_related_payment ON system_activity_log(related_payment_id);
CREATE INDEX IF NOT EXISTS idx_related_supplier ON system_activity_log(related_supplier_id);
CREATE INDEX IF NOT EXISTS idx_related_silo ON system_activity_log(related_silo_id);
CREATE INDEX IF NOT EXISTS idx_related_emisi ON system_activity_log(related_emisi_id);
CREATE INDEX IF NOT EXISTS idx_related_user ON system_activity_log(related_user_id);

-- Composite indexes for common queries
CREATE INDEX IF NOT EXISTS idx_module_related_kontrak ON system_activity_log(module_name, related_kontrak_id);
CREATE INDEX IF NOT EXISTS idx_module_related_spk ON system_activity_log(module_name, related_spk_id);
CREATE INDEX IF NOT EXISTS idx_action_related_kontrak ON system_activity_log(action_type, related_kontrak_id);

-- ========================================================================
-- VERIFICATION AND DOCUMENTATION
-- ========================================================================

-- Show all related fields
SELECT 'All related_* fields in system_activity_log:' as info;
SHOW COLUMNS FROM system_activity_log WHERE Field LIKE 'related_%';

-- Count related fields
SELECT COUNT(*) as total_related_fields 
FROM information_schema.COLUMNS 
WHERE TABLE_NAME = 'system_activity_log' 
AND TABLE_SCHEMA = 'optima_db' 
AND COLUMN_NAME LIKE 'related_%';

-- Show field mapping documentation
SELECT 'RELATED FIELD MAPPING:' as documentation
UNION ALL SELECT '=========================='
UNION ALL SELECT 'EXISTING:'
UNION ALL SELECT '- related_kontrak_id: Marketing Kontrak/PO Rental'
UNION ALL SELECT '- related_spk_id: Marketing SPK (Surat Perintah Kerja)'  
UNION ALL SELECT '- related_di_id: Marketing Delivery Instructions'
UNION ALL SELECT ''
UNION ALL SELECT 'MONITORING:'
UNION ALL SELECT '- related_tracking_id: Tracking Delivery/Work Orders'
UNION ALL SELECT ''
UNION ALL SELECT 'MARKETING (additional):'
UNION ALL SELECT '- related_quotation_id: Buat Penawaran'
UNION ALL SELECT '- related_quotation_item_id: Quotation Items'
UNION ALL SELECT ''
UNION ALL SELECT 'SERVICE:'
UNION ALL SELECT '- related_spk_service_id: SPK Service (Penyiapan Unit)'
UNION ALL SELECT '- related_maintenance_id: Preventive Maintenance (PMPS)'
UNION ALL SELECT '- related_work_order_id: Work Order'
UNION ALL SELECT '- related_work_order_item_id: Work Order Items'
UNION ALL SELECT '- related_service_item_id: Service Inventory Items'
UNION ALL SELECT '- related_unit_id: Data Unit/Inventory Unit'
UNION ALL SELECT ''
UNION ALL SELECT 'OPERATIONAL:'
UNION ALL SELECT '- related_delivery_process_id: Delivery Process'
UNION ALL SELECT '- related_delivery_status_id: Delivery Status'
UNION ALL SELECT ''
UNION ALL SELECT 'ACCOUNTING:'
UNION ALL SELECT '- related_invoice_id: Invoice Management'
UNION ALL SELECT '- related_invoice_item_id: Invoice Items'
UNION ALL SELECT '- related_payment_id: Payment Validation'
UNION ALL SELECT ''
UNION ALL SELECT 'PURCHASING:'
UNION ALL SELECT '- related_po_id: Purchase Order'
UNION ALL SELECT '- related_po_item_id: PO Items'
UNION ALL SELECT '- related_po_unit_id: PO Unit'
UNION ALL SELECT '- related_po_attachment_id: PO Attachment & Battery'
UNION ALL SELECT '- related_po_sparepart_id: PO Sparepart'
UNION ALL SELECT '- related_supplier_id: Supplier'
UNION ALL SELECT ''
UNION ALL SELECT 'WAREHOUSE:'
UNION ALL SELECT '- related_inventory_unit_id: Inventory Unit'
UNION ALL SELECT '- related_inventory_attachment_id: Inventory Attachment'
UNION ALL SELECT '- related_inventory_sparepart_id: Inventory Sparepart'
UNION ALL SELECT '- related_po_verification_id: PO Verification'
UNION ALL SELECT '- related_location_id: Warehouse Location'
UNION ALL SELECT ''
UNION ALL SELECT 'PERIZINAN:'
UNION ALL SELECT '- related_silo_id: SILO License'
UNION ALL SELECT '- related_emisi_id: EMISI License'
UNION ALL SELECT '- related_license_id: License Management'
UNION ALL SELECT ''
UNION ALL SELECT 'ADMINISTRATION:'
UNION ALL SELECT '- related_user_id: User Management'
UNION ALL SELECT '- related_role_id: Role Management'
UNION ALL SELECT '- related_permission_id: Permission Management'
UNION ALL SELECT '- related_setting_id: System Settings'
UNION ALL SELECT '- related_config_id: Configuration';

SELECT 'Related fields expansion completed successfully!' as status;
