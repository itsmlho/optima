-- OptimaPro Activity Logging - PHASE 3: Menu Mapping Reference
-- Date: 2025-09-09  
-- Purpose: Create reference table for menu structure (non-intrusive)

-- PHASE 3: Create menu mapping reference table (completely separate, non-breaking)
CREATE TABLE IF NOT EXISTS system_menu_mapping (
    id INT AUTO_INCREMENT PRIMARY KEY,
    division_code VARCHAR(20) NOT NULL,
    division_name VARCHAR(50) NOT NULL,
    menu_item VARCHAR(100) NOT NULL,
    menu_code VARCHAR(50) NOT NULL,
    module_name VARCHAR(50) NOT NULL, -- Using VARCHAR instead of ENUM for flexibility
    feature_area VARCHAR(100),
    related_tables JSON, -- Tables that this menu interacts with
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_menu (division_code, menu_code),
    INDEX idx_module_feature (module_name, feature_area),
    INDEX idx_menu_code (menu_code)
);

-- Insert menu mapping data based on your structure
INSERT INTO system_menu_mapping (division_code, division_name, menu_item, menu_code, module_name, feature_area, related_tables) VALUES
-- MONITORING Division
('MON', 'MONITORING', 'Dashboard', 'dashboard', 'MONITORING', 'Analytics', '["system_activity_log", "kontrak", "spk", "delivery_instructions"]'),
('MON', 'MONITORING', 'Tracking Delivery', 'tracking_delivery', 'MONITORING', 'Delivery Tracking', '["delivery_instructions", "delivery_items", "spk"]'),
('MON', 'MONITORING', 'Tracking Work Orders', 'tracking_workorders', 'MONITORING', 'Work Order Tracking', '["work_order", "spk", "inventory_unit"]'),

-- MARKETING Division  
('MKT', 'MARKETING', 'Buat Penawaran', 'create_quotation', 'MARKETING', 'Sales Management', '["quotation", "quotation_items"]'),
('MKT', 'MARKETING', 'Kontrak/PO Rental', 'kontrak_rental', 'MARKETING', 'Contract Management', '["kontrak", "kontrak_spesifikasi"]'),
('MKT', 'MARKETING', 'SPK (Surat Perintah Kerja)', 'spk_management', 'MARKETING', 'Work Order Management', '["spk", "spk_items", "kontrak"]'),
('MKT', 'MARKETING', 'Delivery Instructions (DI)', 'delivery_instructions', 'MARKETING', 'Delivery Management', '["delivery_instructions", "delivery_items", "spk"]'),

-- SERVICE Division
('SVC', 'SERVICE', 'SPK Service (Penyiapan Unit)', 'spk_service', 'SERVICE', 'Unit Preparation', '["spk_service", "inventory_unit", "service_items"]'),
('SVC', 'SERVICE', 'Preventive Maintenance (PMPS)', 'pmps', 'SERVICE', 'Maintenance Management', '["maintenance_schedule", "maintenance_log", "inventory_unit"]'),
('SVC', 'SERVICE', 'Work Order', 'work_order', 'SERVICE', 'Service Operations', '["work_order", "work_order_items", "inventory_unit"]'),
('SVC', 'SERVICE', 'Work Order - History', 'work_order_history', 'SERVICE', 'Service History', '["work_order", "work_order_log"]'),
('SVC', 'SERVICE', 'Service Inventory - Unit', 'service_inventory_unit', 'SERVICE', 'Unit Inventory', '["inventory_unit", "service_items"]'),
('SVC', 'SERVICE', 'Service Inventory - Attachment', 'service_inventory_attachment', 'SERVICE', 'Attachment Inventory', '["inventory_attachment", "service_items"]'),
('SVC', 'SERVICE', 'Data Unit', 'data_unit', 'SERVICE', 'Unit Management', '["inventory_unit", "model_unit", "tipe_unit"]'),

-- OPERATIONAL Division
('OPR', 'OPERATIONAL', 'Delivery Process', 'delivery_process', 'OPERATIONAL', 'Operations Management', '["delivery_instructions", "delivery_status", "inventory_unit"]'),

-- ACCOUNTING Division
('ACC', 'ACCOUNTING', 'Invoice Management', 'invoice_management', 'ACCOUNTING', 'Financial Management', '["invoice", "invoice_items", "kontrak"]'),
('ACC', 'ACCOUNTING', 'Payment Validation', 'payment_validation', 'ACCOUNTING', 'Payment Processing', '["payment", "invoice", "kontrak"]'),

-- PURCHASING Division
('PUR', 'PURCHASING', 'Buat PO', 'create_po', 'PURCHASING', 'Purchase Management', '["purchase_order", "po_items"]'),
('PUR', 'PURCHASING', 'PO Unit', 'po_unit', 'PURCHASING', 'Unit Purchasing', '["po_unit", "inventory_unit", "supplier"]'),
('PUR', 'PURCHASING', 'PO Attachment & Battery', 'po_attachment', 'PURCHASING', 'Attachment Purchasing', '["po_attachment", "inventory_attachment", "supplier"]'),
('PUR', 'PURCHASING', 'PO Sparepart', 'po_sparepart', 'PURCHASING', 'Sparepart Purchasing', '["po_sparepart", "inventory_sparepart", "supplier"]'),

-- WAREHOUSE & ASSETS Division
('WHS', 'WAREHOUSE', 'Inventory - Unit', 'inventory_unit', 'WAREHOUSE', 'Unit Management', '["inventory_unit", "unit_status", "location"]'),
('WHS', 'WAREHOUSE', 'Inventory - Attachment & Battery', 'inventory_attachment', 'WAREHOUSE', 'Attachment Management', '["inventory_attachment", "attachment_status"]'),
('WHS', 'WAREHOUSE', 'Inventory - Sparepart', 'inventory_sparepart', 'WAREHOUSE', 'Sparepart Management', '["inventory_sparepart", "sparepart_category"]'),
('WHS', 'WAREHOUSE', 'PO Verification - PO Unit', 'po_verification_unit', 'WAREHOUSE', 'PO Verification', '["po_verification", "po_unit", "inventory_unit"]'),
('WHS', 'WAREHOUSE', 'PO Verification - PO Attachment', 'po_verification_attachment', 'WAREHOUSE', 'PO Verification', '["po_verification", "po_attachment"]'),
('WHS', 'WAREHOUSE', 'PO Verification - PO Sparepart', 'po_verification_sparepart', 'WAREHOUSE', 'PO Verification', '["po_verification", "po_sparepart"]'),

-- PERIZINAN Division
('LIC', 'PERIZINAN', 'SILO (Surat Izin Layak Operasi)', 'silo_management', 'PERIZINAN', 'License Management', '["silo", "inventory_unit", "license_status"]'),
('LIC', 'PERIZINAN', 'EMISI (Surat Izin Emisi Gas Buang)', 'emisi_management', 'PERIZINAN', 'Emission License', '["emisi", "inventory_unit", "license_status"]'),

-- ADMINISTRATION Division
('ADM', 'ADMINISTRATION', 'User Management', 'user_management', 'ADMINISTRATION', 'System Administration', '["users", "user_roles"]'),
('ADM', 'ADMINISTRATION', 'Role Management', 'role_management', 'ADMINISTRATION', 'System Administration', '["roles", "role_permissions"]'),
('ADM', 'ADMINISTRATION', 'Permission Management', 'permission_management', 'ADMINISTRATION', 'System Administration', '["permissions", "role_permissions"]'),
('ADM', 'ADMINISTRATION', 'System Settings', 'system_settings', 'ADMINISTRATION', 'System Configuration', '["system_settings", "configuration"]'),
('ADM', 'ADMINISTRATION', 'Activity Log', 'activity_log', 'ADMINISTRATION', 'System Monitoring', '["system_activity_log"]'),
('ADM', 'ADMINISTRATION', 'Configuration', 'configuration', 'ADMINISTRATION', 'System Configuration', '["system_configuration", "application_settings"]');

-- Verification
SELECT 'Menu mapping created successfully' as status;
SELECT COUNT(*) as total_menu_items FROM system_menu_mapping;
SELECT division_name, COUNT(*) as menu_count FROM system_menu_mapping GROUP BY division_name;
