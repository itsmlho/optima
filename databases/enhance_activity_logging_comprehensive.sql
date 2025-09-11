-- OptimaPro Comprehensive Activity Logging Enhancement
-- Date: 2025-09-09
-- Purpose: Extend system_activity_log to support all menu modules

-- Step 1: Backup existing data
CREATE TABLE IF NOT EXISTS system_activity_log_backup AS SELECT * FROM system_activity_log;

-- Step 2: Extend module_name enum to cover all divisions
ALTER TABLE system_activity_log 
MODIFY COLUMN module_name ENUM(
    'MONITORING',
    'MARKETING', 
    'SERVICE',
    'OPERATIONAL',
    'ACCOUNTING',
    'PURCHASING',
    'WAREHOUSE',
    'PERIZINAN',
    'ADMINISTRATION',
    'DASHBOARD',
    'REPORTS',
    'SETTINGS'
) DEFAULT NULL;

-- Step 3: Add submenu field to track specific menu items
ALTER TABLE system_activity_log 
ADD COLUMN submenu_item VARCHAR(100) DEFAULT NULL AFTER module_name,
ADD COLUMN feature_area VARCHAR(100) DEFAULT NULL AFTER submenu_item;

-- Step 4: Add indexes for better performance
CREATE INDEX idx_module_submenu ON system_activity_log(module_name, submenu_item);
CREATE INDEX idx_feature_area ON system_activity_log(feature_area);
CREATE INDEX idx_created_at ON system_activity_log(created_at);

-- Step 5: Extend workflow_stage enum for comprehensive tracking
ALTER TABLE system_activity_log 
MODIFY COLUMN workflow_stage ENUM(
    'DRAFT',
    'CREATED', 
    'UPDATED',
    'DELETED',
    'SUBMITTED',
    'PENDING_APPROVAL',
    'APPROVED',
    'REJECTED',
    'IN_PROGRESS',
    'COMPLETED',
    'CANCELLED',
    'ASSIGNED',
    'UNASSIGNED',
    'DELIVERED',
    'RECEIVED',
    'VERIFIED',
    'VALIDATED',
    'PROCESSED',
    'INVOICED',
    'PAID',
    'MAINTENANCE_SCHEDULED',
    'MAINTENANCE_COMPLETED',
    'EXPIRED',
    'RENEWED',
    'DELETE_CONFIRMED'
) DEFAULT NULL;

-- Step 6: Add user context fields
ALTER TABLE system_activity_log 
ADD COLUMN ip_address VARCHAR(45) DEFAULT NULL AFTER user_id,
ADD COLUMN user_agent TEXT DEFAULT NULL AFTER ip_address,
ADD COLUMN session_id VARCHAR(128) DEFAULT NULL AFTER user_agent;

-- Step 7: Extend business_impact for more granular tracking  
ALTER TABLE system_activity_log 
MODIFY COLUMN business_impact ENUM(
    'MINIMAL',
    'LOW',
    'MEDIUM', 
    'HIGH',
    'CRITICAL',
    'SYSTEM_CRITICAL'
) DEFAULT 'LOW';

-- Step 8: Add financial impact tracking
ALTER TABLE system_activity_log 
ADD COLUMN financial_impact DECIMAL(15,2) DEFAULT NULL AFTER business_impact,
ADD COLUMN currency_code VARCHAR(3) DEFAULT 'IDR' AFTER financial_impact;

-- Step 9: Create comprehensive menu mapping reference table
CREATE TABLE IF NOT EXISTS system_menu_mapping (
    id INT AUTO_INCREMENT PRIMARY KEY,
    division_code VARCHAR(20) NOT NULL,
    division_name VARCHAR(50) NOT NULL,
    menu_item VARCHAR(100) NOT NULL,
    menu_code VARCHAR(50) NOT NULL,
    module_name ENUM(
        'MONITORING',
        'MARKETING', 
        'SERVICE',
        'OPERATIONAL',
        'ACCOUNTING',
        'PURCHASING',
        'WAREHOUSE',
        'PERIZINAN',
        'ADMINISTRATION'
    ) NOT NULL,
    feature_area VARCHAR(100),
    table_names JSON, -- Related database tables
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_menu (division_code, menu_code)
);

-- Step 10: Insert menu mapping data
INSERT INTO system_menu_mapping (division_code, division_name, menu_item, menu_code, module_name, feature_area, table_names) VALUES
-- MONITORING
('MON', 'MONITORING', 'Dashboard', 'dashboard', 'MONITORING', 'Analytics', '["system_activity_log", "kontrak", "spk", "delivery_instructions"]'),
('MON', 'MONITORING', 'Tracking Delivery', 'tracking_delivery', 'MONITORING', 'Delivery Tracking', '["delivery_instructions", "delivery_items", "spk"]'),
('MON', 'MONITORING', 'Tracking Work Orders', 'tracking_workorders', 'MONITORING', 'Work Order Tracking', '["work_order", "spk", "inventory_unit"]'),

-- MARKETING  
('MKT', 'MARKETING', 'Buat Penawaran', 'create_quotation', 'MARKETING', 'Sales Management', '["quotation", "quotation_items"]'),
('MKT', 'MARKETING', 'Kontrak/PO Rental', 'kontrak_rental', 'MARKETING', 'Contract Management', '["kontrak", "kontrak_spesifikasi"]'),
('MKT', 'MARKETING', 'SPK (Surat Perintah Kerja)', 'spk_management', 'MARKETING', 'Work Order Management', '["spk", "spk_items", "kontrak"]'),
('MKT', 'MARKETING', 'Delivery Instructions (DI)', 'delivery_instructions', 'MARKETING', 'Delivery Management', '["delivery_instructions", "delivery_items", "spk"]'),

-- SERVICE
('SVC', 'SERVICE', 'SPK Service (Penyiapan Unit)', 'spk_service', 'SERVICE', 'Unit Preparation', '["spk_service", "inventory_unit", "service_items"]'),
('SVC', 'SERVICE', 'Preventive Maintenance (PMPS)', 'pmps', 'SERVICE', 'Maintenance Management', '["maintenance_schedule", "maintenance_log", "inventory_unit"]'),
('SVC', 'SERVICE', 'Work Order', 'work_order', 'SERVICE', 'Service Operations', '["work_order", "work_order_items", "inventory_unit"]'),
('SVC', 'SERVICE', 'Work Order - History', 'work_order_history', 'SERVICE', 'Service History', '["work_order", "work_order_log"]'),
('SVC', 'SERVICE', 'Service Inventory - Unit', 'service_inventory_unit', 'SERVICE', 'Unit Inventory', '["inventory_unit", "service_items"]'),
('SVC', 'SERVICE', 'Service Inventory - Attachment', 'service_inventory_attachment', 'SERVICE', 'Attachment Inventory', '["inventory_attachment", "service_items"]'),
('SVC', 'SERVICE', 'Data Unit', 'data_unit', 'SERVICE', 'Unit Management', '["inventory_unit", "model_unit", "tipe_unit"]'),

-- OPERATIONAL
('OPR', 'OPERATIONAL', 'Delivery Process', 'delivery_process', 'OPERATIONAL', 'Operations Management', '["delivery_instructions", "delivery_status", "inventory_unit"]'),

-- ACCOUNTING
('ACC', 'ACCOUNTING', 'Invoice Management', 'invoice_management', 'ACCOUNTING', 'Financial Management', '["invoice", "invoice_items", "kontrak"]'),
('ACC', 'ACCOUNTING', 'Payment Validation', 'payment_validation', 'ACCOUNTING', 'Payment Processing', '["payment", "invoice", "kontrak"]'),

-- PURCHASING
('PUR', 'PURCHASING', 'Buat PO', 'create_po', 'PURCHASING', 'Purchase Management', '["purchase_order", "po_items"]'),
('PUR', 'PURCHASING', 'PO Unit', 'po_unit', 'PURCHASING', 'Unit Purchasing', '["po_unit", "inventory_unit", "supplier"]'),
('PUR', 'PURCHASING', 'PO Attachment & Battery', 'po_attachment', 'PURCHASING', 'Attachment Purchasing', '["po_attachment", "inventory_attachment", "supplier"]'),
('PUR', 'PURCHASING', 'PO Sparepart', 'po_sparepart', 'PURCHASING', 'Sparepart Purchasing', '["po_sparepart", "inventory_sparepart", "supplier"]'),

-- WAREHOUSE & ASSETS
('WHS', 'WAREHOUSE', 'Inventory - Unit', 'inventory_unit', 'WAREHOUSE', 'Unit Management', '["inventory_unit", "unit_status", "location"]'),
('WHS', 'WAREHOUSE', 'Inventory - Attachment & Battery', 'inventory_attachment', 'WAREHOUSE', 'Attachment Management', '["inventory_attachment", "attachment_status"]'),
('WHS', 'WAREHOUSE', 'Inventory - Sparepart', 'inventory_sparepart', 'WAREHOUSE', 'Sparepart Management', '["inventory_sparepart", "sparepart_category"]'),
('WHS', 'WAREHOUSE', 'PO Verification - PO Unit', 'po_verification_unit', 'WAREHOUSE', 'PO Verification', '["po_verification", "po_unit", "inventory_unit"]'),
('WHS', 'WAREHOUSE', 'PO Verification - PO Attachment', 'po_verification_attachment', 'WAREHOUSE', 'PO Verification', '["po_verification", "po_attachment"]'),
('WHS', 'WAREHOUSE', 'PO Verification - PO Sparepart', 'po_verification_sparepart', 'WAREHOUSE', 'PO Verification', '["po_verification", "po_sparepart"]'),

-- PERIZINAN
('LIC', 'PERIZINAN', 'SILO (Surat Izin Layak Operasi)', 'silo_management', 'PERIZINAN', 'License Management', '["silo", "inventory_unit", "license_status"]'),
('LIC', 'PERIZINAN', 'EMISI (Surat Izin Emisi Gas Buang)', 'emisi_management', 'PERIZINAN', 'Emission License', '["emisi", "inventory_unit", "license_status"]'),

-- ADMINISTRATION
('ADM', 'ADMINISTRATION', 'User Management', 'user_management', 'ADMINISTRATION', 'System Administration', '["users", "user_roles"]'),
('ADM', 'ADMINISTRATION', 'Role Management', 'role_management', 'ADMINISTRATION', 'System Administration', '["roles", "role_permissions"]'),
('ADM', 'ADMINISTRATION', 'Permission Management', 'permission_management', 'ADMINISTRATION', 'System Administration', '["permissions", "role_permissions"]'),
('ADM', 'ADMINISTRATION', 'System Settings', 'system_settings', 'ADMINISTRATION', 'System Configuration', '["system_settings", "configuration"]'),
('ADM', 'ADMINISTRATION', 'Activity Log', 'activity_log', 'ADMINISTRATION', 'System Monitoring', '["system_activity_log"]'),
('ADM', 'ADMINISTRATION', 'Configuration', 'configuration', 'ADMINISTRATION', 'System Configuration', '["system_configuration", "application_settings"]');

-- Step 11: Create indexes for the mapping table
CREATE INDEX idx_module_feature ON system_menu_mapping(module_name, feature_area);
CREATE INDEX idx_menu_code ON system_menu_mapping(menu_code);

-- Step 12: Update existing log entries to use new module names (where applicable)
UPDATE system_activity_log SET module_name = 'WAREHOUSE' WHERE module_name = 'WAREHOUSE';
UPDATE system_activity_log SET submenu_item = 'kontrak_rental' WHERE table_name = 'kontrak';

-- Step 13: Create view for easy reporting
CREATE OR REPLACE VIEW v_activity_log_detailed AS
SELECT 
    sal.id,
    sal.table_name,
    sal.record_id,
    sal.action_type,
    sal.action_description,
    sal.workflow_stage,
    sal.module_name,
    sal.submenu_item,
    sal.feature_area,
    sal.business_impact,
    sal.financial_impact,
    sal.currency_code,
    sal.is_critical,
    sal.user_id,
    sal.ip_address,
    sal.created_at,
    smm.division_name,
    smm.menu_item,
    smm.division_code
FROM system_activity_log sal
LEFT JOIN system_menu_mapping smm ON sal.module_name = smm.module_name 
    AND sal.submenu_item = smm.menu_code;

-- Verification queries
SELECT 'Enhanced system_activity_log structure created successfully' as status;
SELECT COUNT(*) as total_menu_items FROM system_menu_mapping;
SELECT DISTINCT module_name FROM system_menu_mapping ORDER BY module_name;
