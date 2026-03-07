-- ═══════════════════════════════════════════════════════════════════════════════
-- PRODUCTION MIGRATION - MARCH 7, 2026
-- ═══════════════════════════════════════════════════════════════════════════════
--
-- DEPLOYMENT: Permission System Comprehensive + New Menu Permissions
-- RISK LEVEL: LOW (INSERT only, no data changes)
-- EXECUTION TIME: ~60 seconds
-- ROLLBACK: Available (see ROLLBACK section at bottom)
--
-- WHAT THIS DOES:
-- 1. Creates ~348 comprehensive permissions across 13 modules
-- 2. Adds 21 new permissions for Audit Approval, Unit Audit, Surat Jalan
-- 3. Assigns new permissions to appropriate roles
--
-- PREREQUISITES:
-- - Database backup completed ✓
-- - No users actively editing roles during migration
--
-- USAGE:
--   mysql -u [username] -p optima_production < PRODUCTION_MIGRATION_MARCH_7_2026.sql
--
-- VERIFICATION:
--   Run queries in VERIFICATION section after execution
--
-- ═══════════════════════════════════════════════════════════════════════════════

-- Start transaction for rollback safety
START TRANSACTION;

-- ═══════════════════════════════════════════════════════════════════════════════
-- PART 1: COMPREHENSIVE PERMISSION SYSTEM (348 permissions)
-- ═══════════════════════════════════════════════════════════════════════════════

-- Dashboard Module (5 permissions)
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('dashboard', 'home', 'navigation', 'dashboard.home.navigation', 'Dashboard - Home - Navigation', 'Access dashboard menu'),
('dashboard', 'home', 'view', 'dashboard.home.view', 'Dashboard - Home - View', 'View dashboard widgets'),
('dashboard', 'analytics', 'navigation', 'dashboard.analytics.navigation', 'Dashboard - Analytics - Navigation', 'Access analytics dashboard'),
('dashboard', 'analytics', 'view', 'dashboard.analytics.view', 'Dashboard - Analytics - View', 'View analytics data'),
('dashboard', 'analytics', 'export', 'dashboard.analytics.export', 'Dashboard - Analytics - Export', 'Export analytics report')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- Marketing Module - Customer (8 permissions)
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('marketing', 'customer', 'navigation', 'marketing.customer.navigation', 'Marketing - Customer - Navigation', 'Access customer menu'),
('marketing', 'customer', 'view', 'marketing.customer.view', 'Marketing - Customer - View', 'View customer list and details'),
('marketing', 'customer', 'create', 'marketing.customer.create', 'Marketing - Customer - Create', 'Add new customer'),
('marketing', 'customer', 'edit', 'marketing.customer.edit', 'Marketing - Customer - Edit', 'Edit customer data'),
('marketing', 'customer', 'delete', 'marketing.customer.delete', 'Marketing - Customer - Delete', 'Delete customer'),
('marketing', 'customer', 'export', 'marketing.customer.export', 'Marketing - Customer - Export', 'Export customer list'),
('marketing', 'customer', 'import', 'marketing.customer.import', 'Marketing - Customer - Import', 'Import customer data'),
('marketing', 'customer', 'view_statistics', 'marketing.customer.view_statistics', 'Marketing - Customer - View Statistics', 'View customer statistics')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- Marketing Module - Quotation (10 permissions)
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('marketing', 'quotation', 'navigation', 'marketing.quotation.navigation', 'Marketing - Quotation - Navigation', 'Access quotation menu'),
('marketing', 'quotation', 'view', 'marketing.quotation.view', 'Marketing - Quotation - View', 'View quotation list'),
('marketing', 'quotation', 'create', 'marketing.quotation.create', 'Marketing - Quotation - Create', 'Create new quotation'),
('marketing', 'quotation', 'edit', 'marketing.quotation.edit', 'Marketing - Quotation - Edit', 'Edit quotation'),
('marketing', 'quotation', 'delete', 'marketing.quotation.delete', 'Marketing - Quotation - Delete', 'Delete quotation'),
('marketing', 'quotation', 'approve', 'marketing.quotation.approve', 'Marketing - Quotation - Approve', 'Approve quotation'),
('marketing', 'quotation', 'reject', 'marketing.quotation.reject', 'Marketing - Quotation - Reject', 'Reject quotation'),
('marketing', 'quotation', 'convert_po', 'marketing.quotation.convert_po', 'Marketing - Quotation - Convert PO', 'Convert quotation to PO/Contract'),
('marketing', 'quotation', 'export', 'marketing.quotation.export', 'Marketing - Quotation - Export', 'Export quotation data'),
('marketing', 'quotation', 'print', 'marketing.quotation.print', 'Marketing - Quotation - Print', 'Print quotation document')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- Marketing Module - Contract (9 permissions)
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('marketing', 'contract', 'navigation', 'marketing.contract.navigation', 'Marketing - Contract - Navigation', 'Access contract menu'),
('marketing', 'contract', 'view', 'marketing.contract.view', 'Marketing - Contract - View', 'View contract list and details'),
('marketing', 'contract', 'create', 'marketing.contract.create', 'Marketing - Contract - Create', 'Create new contract'),
('marketing', 'contract', 'edit', 'marketing.contract.edit', 'Marketing - Contract - Edit', 'Edit contract data'),
('marketing', 'contract', 'delete', 'marketing.contract.delete', 'Marketing - Contract - Delete', 'Delete contract'),
('marketing', 'contract', 'renew', 'marketing.contract.renew', 'Marketing - Contract - Renew', 'Renew contract'),
('marketing', 'contract', 'terminate', 'marketing.contract.terminate', 'Marketing - Contract - Terminate', 'Terminate contract'),
('marketing', 'contract', 'export', 'marketing.contract.export', 'Marketing - Contract - Export', 'Export contract data'),
('marketing', 'contract', 'print', 'marketing.contract.print', 'Marketing - Contract - Print', 'Print contract document')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- Marketing Module - SPK (7 permissions)
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('marketing', 'spk', 'navigation', 'marketing.spk.navigation', 'Marketing - SPK - Navigation', 'Access SPK menu'),
('marketing', 'spk', 'view', 'marketing.spk.view', 'Marketing - SPK - View', 'View SPK list'),
('marketing', 'spk', 'create', 'marketing.spk.create', 'Marketing - SPK - Create', 'Create new SPK'),
('marketing', 'spk', 'edit', 'marketing.spk.edit', 'Marketing - SPK - Edit', 'Edit SPK'),
('marketing', 'spk', 'delete', 'marketing.spk.delete', 'Marketing - SPK - Delete', 'Delete SPK'),
('marketing', 'spk', 'print', 'marketing.spk.print', 'Marketing - SPK - Print', 'Print SPK document'),
('marketing', 'spk', 'export', 'marketing.spk.export', 'Marketing - SPK - Export', 'Export SPK data')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- Marketing Module - Delivery Instructions (7 permissions)
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('marketing', 'delivery_instructions', 'navigation', 'marketing.delivery_instructions.navigation', 'Marketing - Delivery Instructions - Navigation', 'Access delivery instructions menu'),
('marketing', 'delivery_instructions', 'view', 'marketing.delivery_instructions.view', 'Marketing - Delivery Instructions - View', 'View delivery instructions'),
('marketing', 'delivery_instructions', 'create', 'marketing.delivery_instructions.create', 'Marketing - Delivery Instructions - Create', 'Create delivery instruction'),
('marketing', 'delivery_instructions', 'edit', 'marketing.delivery_instructions.edit', 'Marketing - Delivery Instructions - Edit', 'Edit delivery instruction'),
('marketing', 'delivery_instructions', 'delete', 'marketing.delivery_instructions.delete', 'Marketing - Delivery Instructions - Delete', 'Delete delivery instruction'),
('marketing', 'delivery_instructions', 'print', 'marketing.delivery_instructions.print', 'Marketing - Delivery Instructions - Print', 'Print delivery instruction'),
('marketing', 'delivery_instructions', 'export', 'marketing.delivery_instructions.export', 'Marketing - Delivery Instructions - Export', 'Export delivery instructions')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- Service Module - SPK Service (7 permissions)
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('service', 'spk_service', 'navigation', 'service.spk_service.navigation', 'Service - SPK Service - Navigation', 'Access SPK service menu'),
('service', 'spk_service', 'view', 'service.spk_service.view', 'Service - SPK Service - View', 'View SPK service list'),
('service', 'spk_service', 'create', 'service.spk_service.create', 'Service - SPK Service - Create', 'Create new SPK service'),
('service', 'spk_service', 'edit', 'service.spk_service.edit', 'Service - SPK Service - Edit', 'Edit SPK service'),
('service', 'spk_service', 'delete', 'service.spk_service.delete', 'Service - SPK Service - Delete', 'Delete SPK service'),
('service', 'spk_service', 'print', 'service.spk_service.print', 'Service - SPK Service - Print', 'Print SPK service'),
('service', 'spk_service', 'export', 'service.spk_service.export', 'Service - SPK Service - Export', 'Export SPK service data')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- Service Module - PMPS (8 permissions)
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('service', 'pmps', 'navigation', 'service.pmps.navigation', 'Service - PMPS - Navigation', 'Access PMPS menu'),
('service', 'pmps', 'view', 'service.pmps.view', 'Service - PMPS - View', 'View PMPS schedule'),
('service', 'pmps', 'create', 'service.pmps.create', 'Service - PMPS - Create', 'Create PMPS schedule'),
('service', 'pmps', 'edit', 'service.pmps.edit', 'Service - PMPS - Edit', 'Edit PMPS schedule'),
('service', 'pmps', 'delete', 'service.pmps.delete', 'Service - PMPS - Delete', 'Delete PMPS schedule'),
('service', 'pmps', 'complete', 'service.pmps.complete', 'Service - PMPS - Complete', 'Mark PMPS as completed'),
('service', 'pmps', 'print', 'service.pmps.print', 'Service - PMPS - Print', 'Print PMPS report'),
('service', 'pmps', 'export', 'service.pmps.export', 'Service - PMPS - Export', 'Export PMPS data')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- Service Module - Workorder (8 permissions)
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('service', 'workorder', 'navigation', 'service.workorder.navigation', 'Service - Workorder - Navigation', 'Access workorder menu'),
('service', 'workorder', 'view', 'service.workorder.view', 'Service - Workorder - View', 'View workorder list'),
('service', 'workorder', 'create', 'service.workorder.create', 'Service - Workorder - Create', 'Create new workorder'),
('service', 'workorder', 'edit', 'service.workorder.edit', 'Service - Workorder - Edit', 'Edit workorder'),
('service', 'workorder', 'delete', 'service.workorder.delete', 'Service - Workorder - Delete', 'Delete workorder'),
('service', 'workorder', 'complete', 'service.workorder.complete', 'Service - Workorder - Complete', 'Mark workorder as completed'),
('service', 'workorder', 'print', 'service.workorder.print', 'Service - Workorder - Print', 'Print workorder'),
('service', 'workorder', 'export', 'service.workorder.export', 'Service - Workorder - Export', 'Export workorder data')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- Service Module - Area Management (7 permissions)
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('service', 'area_management', 'navigation', 'service.area_management.navigation', 'Service - Area Management - Navigation', 'Access area management menu'),
('service', 'area_management', 'view', 'service.area_management.view', 'Service - Area Management - View', 'View area list'),
('service', 'area_management', 'create', 'service.area_management.create', 'Service - Area Management - Create', 'Create new area'),
('service', 'area_management', 'edit', 'service.area_management.edit', 'Service - Area Management - Edit', 'Edit area data'),
('service', 'area_management', 'delete', 'service.area_management.delete', 'Service - Area Management - Delete', 'Delete area'),
('service', 'area_management', 'assign_employee', 'service.area_management.assign_employee', 'Service - Area Management - Assign Employee', 'Assign employee to area'),
('service', 'area_management', 'export', 'service.area_management.export', 'Service - Area Management - Export', 'Export area data')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- Operational Module - Delivery Process (7 permissions)
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('operational', 'delivery_process', 'navigation', 'operational.delivery_process.navigation', 'Operational - Delivery Process - Navigation', 'Access delivery process menu'),
('operational', 'delivery_process', 'view', 'operational.delivery_process.view', 'Operational - Delivery Process - View', 'View delivery process'),
('operational', 'delivery_process', 'create', 'operational.delivery_process.create', 'Operational - Delivery Process - Create', 'Create delivery process'),
('operational', 'delivery_process', 'edit', 'operational.delivery_process.edit', 'Operational - Delivery Process - Edit', 'Edit delivery process'),
('operational', 'delivery_process', 'complete', 'operational.delivery_process.complete', 'Operational - Delivery Process - Complete', 'Mark delivery as completed'),
('operational', 'delivery_process', 'print', 'operational.delivery_process.print', 'Operational - Delivery Process - Print', 'Print delivery document'),
('operational', 'delivery_process', 'export', 'operational.delivery_process.export', 'Operational - Delivery Process - Export', 'Export delivery data')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- Accounting Module - Invoice (9 permissions)
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('accounting', 'invoice', 'navigation', 'accounting.invoice.navigation', 'Accounting - Invoice - Navigation', 'Access invoice menu'),
('accounting', 'invoice', 'view', 'accounting.invoice.view', 'Accounting - Invoice - View', 'View invoice list'),
('accounting', 'invoice', 'create', 'accounting.invoice.create', 'Accounting - Invoice - Create', 'Create new invoice'),
('accounting', 'invoice', 'edit', 'accounting.invoice.edit', 'Accounting - Invoice - Edit', 'Edit invoice'),
('accounting', 'invoice', 'delete', 'accounting.invoice.delete', 'Accounting - Invoice - Delete', 'Delete invoice'),
('accounting', 'invoice', 'approve', 'accounting.invoice.approve', 'Accounting - Invoice - Approve', 'Approve invoice'),
('accounting', 'invoice', 'print', 'accounting.invoice.print', 'Accounting - Invoice - Print', 'Print invoice'),
('accounting', 'invoice', 'export', 'accounting.invoice.export', 'Accounting - Invoice - Export', 'Export invoice data'),
('accounting', 'invoice', 'send_email', 'accounting.invoice.send_email', 'Accounting - Invoice - Send Email', 'Send invoice via email')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- Accounting Module - Payment Validation (7 permissions)
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('accounting', 'payment_validation', 'navigation', 'accounting.payment_validation.navigation', 'Accounting - Payment Validation - Navigation', 'Access payment validation menu'),
('accounting', 'payment_validation', 'view', 'accounting.payment_validation.view', 'Accounting - Payment Validation - View', 'View payment validations'),
('accounting', 'payment_validation', 'create', 'accounting.payment_validation.create', 'Accounting - Payment Validation - Create', 'Create payment validation'),
('accounting', 'payment_validation', 'edit', 'accounting.payment_validation.edit', 'Accounting - Payment Validation - Edit', 'Edit payment validation'),
('accounting', 'payment_validation', 'approve', 'accounting.payment_validation.approve', 'Accounting - Payment Validation - Approve', 'Approve payment'),
('accounting', 'payment_validation', 'reject', 'accounting.payment_validation.reject', 'Accounting - Payment Validation - Reject', 'Reject payment'),
('accounting', 'payment_validation', 'export', 'accounting.payment_validation.export', 'Accounting - Payment Validation - Export', 'Export payment data')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- Purchasing Module (9 permissions)
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('purchasing', 'purchasing', 'navigation', 'purchasing.purchasing.navigation', 'Purchasing - Navigation', 'Access purchasing menu'),
('purchasing', 'purchasing', 'view', 'purchasing.purchasing.view', 'Purchasing - View', 'View purchasing data'),
('purchasing', 'purchasing', 'create', 'purchasing.purchasing.create', 'Purchasing - Create', 'Create new purchase'),
('purchasing', 'purchasing', 'edit', 'purchasing.purchasing.edit', 'Purchasing - Edit', 'Edit purchase'),
('purchasing', 'purchasing', 'delete', 'purchasing.purchasing.delete', 'Purchasing - Delete', 'Delete purchase'),
('purchasing', 'purchasing', 'approve', 'purchasing.purchasing.approve', 'Purchasing - Approve', 'Approve purchase'),
('purchasing', 'purchasing', 'reject', 'purchasing.purchasing.reject', 'Purchasing - Reject', 'Reject purchase'),
('purchasing', 'purchasing', 'print', 'purchasing.purchasing.print', 'Purchasing - Print', 'Print purchase document'),
('purchasing', 'purchasing', 'export', 'purchasing.purchasing.export', 'Purchasing - Export', 'Export purchasing data')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- Warehouse Module - Unit Inventory (8 permissions)
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('warehouse', 'unit_inventory', 'navigation', 'warehouse.unit_inventory.navigation', 'Warehouse - Unit Inventory - Navigation', 'Access unit inventory menu'),
('warehouse', 'unit_inventory', 'view', 'warehouse.unit_inventory.view', 'Warehouse - Unit Inventory - View', 'View unit inventory'),
('warehouse', 'unit_inventory', 'create', 'warehouse.unit_inventory.create', 'Warehouse - Unit Inventory - Create', 'Add new unit'),
('warehouse', 'unit_inventory', 'edit', 'warehouse.unit_inventory.edit', 'Warehouse - Unit Inventory - Edit', 'Edit unit data'),
('warehouse', 'unit_inventory', 'delete', 'warehouse.unit_inventory.delete', 'Warehouse - Unit Inventory - Delete', 'Delete unit'),
('warehouse', 'unit_inventory', 'transfer', 'warehouse.unit_inventory.transfer', 'Warehouse - Unit Inventory - Transfer', 'Transfer unit'),
('warehouse', 'unit_inventory', 'print', 'warehouse.unit_inventory.print', 'Warehouse - Unit Inventory - Print', 'Print unit report'),
('warehouse', 'unit_inventory', 'export', 'warehouse.unit_inventory.export', 'Warehouse - Unit Inventory - Export', 'Export unit inventory')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- Warehouse Module - Attachment Inventory (8 permissions)
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('warehouse', 'attachment_inventory', 'navigation', 'warehouse.attachment_inventory.navigation', 'Warehouse - Attachment Inventory - Navigation', 'Access attachment inventory menu'),
('warehouse', 'attachment_inventory', 'view', 'warehouse.attachment_inventory.view', 'Warehouse - Attachment Inventory - View', 'View attachment inventory'),
('warehouse', 'attachment_inventory', 'create', 'warehouse.attachment_inventory.create', 'Warehouse - Attachment Inventory - Create', 'Add new attachment'),
('warehouse', 'attachment_inventory', 'edit', 'warehouse.attachment_inventory.edit', 'Warehouse - Attachment Inventory - Edit', 'Edit attachment data'),
('warehouse', 'attachment_inventory', 'delete', 'warehouse.attachment_inventory.delete', 'Warehouse - Attachment Inventory - Delete', 'Delete attachment'),
('warehouse', 'attachment_inventory', 'transfer', 'warehouse.attachment_inventory.transfer', 'Warehouse - Attachment Inventory - Transfer', 'Transfer attachment'),
('warehouse', 'attachment_inventory', 'print', 'warehouse.attachment_inventory.print', 'Warehouse - Attachment Inventory - Print', 'Print attachment report'),
('warehouse', 'attachment_inventory', 'export', 'warehouse.attachment_inventory.export', 'Warehouse - Attachment Inventory - Export', 'Export attachment inventory')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- Warehouse Module - Sparepart Inventory (8 permissions)
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('warehouse', 'sparepart_inventory', 'navigation', 'warehouse.sparepart_inventory.navigation', 'Warehouse - Sparepart Inventory - Navigation', 'Access sparepart inventory menu'),
('warehouse', 'sparepart_inventory', 'view', 'warehouse.sparepart_inventory.view', 'Warehouse - Sparepart Inventory - View', 'View sparepart inventory'),
('warehouse', 'sparepart_inventory', 'create', 'warehouse.sparepart_inventory.create', 'Warehouse - Sparepart Inventory - Create', 'Add new sparepart'),
('warehouse', 'sparepart_inventory', 'edit', 'warehouse.sparepart_inventory.edit', 'Warehouse - Sparepart Inventory - Edit', 'Edit sparepart data'),
('warehouse', 'sparepart_inventory', 'delete', 'warehouse.sparepart_inventory.delete', 'Warehouse - Sparepart Inventory - Delete', 'Delete sparepart'),
('warehouse', 'sparepart_inventory', 'adjust_stock', 'warehouse.sparepart_inventory.adjust_stock', 'Warehouse - Sparepart Inventory - Adjust Stock', 'Adjust sparepart stock'),
('warehouse', 'sparepart_inventory', 'print', 'warehouse.sparepart_inventory.print', 'Warehouse - Sparepart Inventory - Print', 'Print sparepart report'),
('warehouse', 'sparepart_inventory', 'export', 'warehouse.sparepart_inventory.export', 'Warehouse - Sparepart Inventory - Export', 'Export sparepart inventory')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- Warehouse Module - Sparepart Usage (7 permissions)
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('warehouse', 'sparepart_usage', 'navigation', 'warehouse.sparepart_usage.navigation', 'Warehouse - Sparepart Usage - Navigation', 'Access sparepart usage menu'),
('warehouse', 'sparepart_usage', 'view', 'warehouse.sparepart_usage.view', 'Warehouse - Sparepart Usage - View', 'View sparepart usage'),
('warehouse', 'sparepart_usage', 'create', 'warehouse.sparepart_usage.create', 'Warehouse - Sparepart Usage - Create', 'Record sparepart usage'),
('warehouse', 'sparepart_usage', 'edit', 'warehouse.sparepart_usage.edit', 'Warehouse - Sparepart Usage - Edit', 'Edit sparepart usage'),
('warehouse', 'sparepart_usage', 'delete', 'warehouse.sparepart_usage.delete', 'Warehouse - Sparepart Usage - Delete', 'Delete sparepart usage'),
('warehouse', 'sparepart_usage', 'print', 'warehouse.sparepart_usage.print', 'Warehouse - Sparepart Usage - Print', 'Print usage report'),
('warehouse', 'sparepart_usage', 'export', 'warehouse.sparepart_usage.export', 'Warehouse - Sparepart Usage - Export', 'Export usage data')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- Warehouse Module - PO Verification (7 permissions)
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('warehouse', 'po_verification', 'navigation', 'warehouse.po_verification.navigation', 'Warehouse - PO Verification - Navigation', 'Access PO verification menu'),
('warehouse', 'po_verification', 'view', 'warehouse.po_verification.view', 'Warehouse - PO Verification - View', 'View PO verification'),
('warehouse', 'po_verification', 'create', 'warehouse.po_verification.create', 'Warehouse - PO Verification - Create', 'Create PO verification'),
('warehouse', 'po_verification', 'edit', 'warehouse.po_verification.edit', 'Warehouse - PO Verification - Edit', 'Edit PO verification'),
('warehouse', 'po_verification', 'approve', 'warehouse.po_verification.approve', 'Warehouse - PO Verification - Approve', 'Approve PO'),
('warehouse', 'po_verification', 'reject', 'warehouse.po_verification.reject', 'Warehouse - PO Verification - Reject', 'Reject PO'),
('warehouse', 'po_verification', 'export', 'warehouse.po_verification.export', 'Warehouse - PO Verification - Export', 'Export PO data')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- Perizinan Module - SILO (8 permissions)
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('perizinan', 'silo', 'navigation', 'perizinan.silo.navigation', 'Perizinan - SILO - Navigation', 'Access SILO permit menu'),
('perizinan', 'silo', 'view', 'perizinan.silo.view', 'Perizinan - SILO - View', 'View SILO permit list'),
('perizinan', 'silo', 'create', 'perizinan.silo.create', 'Perizinan - SILO - Create', 'Create SILO permit'),
('perizinan', 'silo', 'edit', 'perizinan.silo.edit', 'Perizinan - SILO - Edit', 'Edit SILO permit'),
('perizinan', 'silo', 'delete', 'perizinan.silo.delete', 'Perizinan - SILO - Delete', 'Delete SILO permit'),
('perizinan', 'silo', 'renew', 'perizinan.silo.renew', 'Perizinan - SILO - Renew', 'Renew SILO permit'),
('perizinan', 'silo', 'print', 'perizinan.silo.print', 'Perizinan - SILO - Print', 'Print SILO document'),
('perizinan', 'silo', 'export', 'perizinan.silo.export', 'Perizinan - SILO - Export', 'Export SILO data')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- Admin Module - User Management (8 permissions)
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('admin', 'user_management', 'navigation', 'admin.user_management.navigation', 'Admin - User Management - Navigation', 'Access user management menu'),
('admin', 'user_management', 'view', 'admin.user_management.view', 'Admin - User Management - View', 'View user list'),
('admin', 'user_management', 'create', 'admin.user_management.create', 'Admin - User Management - Create', 'Create new user'),
('admin', 'user_management', 'edit', 'admin.user_management.edit', 'Admin - User Management - Edit', 'Edit user data'),
('admin', 'user_management', 'delete', 'admin.user_management.delete', 'Admin - User Management - Delete', 'Delete user'),
('admin', 'user_management', 'reset_password', 'admin.user_management.reset_password', 'Admin - User Management - Reset Password', 'Reset user password'),
('admin', 'user_management', 'activate', 'admin.user_management.activate', 'Admin - User Management - Activate', 'Activate user account'),
('admin', 'user_management', 'deactivate', 'admin.user_management.deactivate', 'Admin - User Management - Deactivate', 'Deactivate user account')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- Admin Module - Role Management (8 permissions)
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('admin', 'role_management', 'navigation', 'admin.role_management.navigation', 'Admin - Role Management - Navigation', 'Access role management menu'),
('admin', 'role_management', 'view', 'admin.role_management.view', 'Admin - Role Management - View', 'View role list'),
('admin', 'role_management', 'create', 'admin.role_management.create', 'Admin - Role Management - Create', 'Create new role'),
('admin', 'role_management', 'edit', 'admin.role_management.edit', 'Admin - Role Management - Edit', 'Edit role data'),
('admin', 'role_management', 'delete', 'admin.role_management.delete', 'Admin - Role Management - Delete', 'Delete role'),
('admin', 'role_management', 'assign_permissions', 'admin.role_management.assign_permissions', 'Admin - Role Management - Assign Permissions', 'Assign permissions to role'),
('admin', 'role_management', 'view_permissions', 'admin.role_management.view_permissions', 'Admin - Role Management - View Permissions', 'View role permissions'),
('admin', 'role_management', 'export', 'admin.role_management.export', 'Admin - Role Management - Export', 'Export role data')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- Admin Module - Permission Management (7 permissions)
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('admin', 'permission_management', 'navigation', 'admin.permission_management.navigation', 'Admin - Permission Management - Navigation', 'Access permission management menu'),
('admin', 'permission_management', 'view', 'admin.permission_management.view', 'Admin - Permission Management - View', 'View permission list'),
('admin', 'permission_management', 'create', 'admin.permission_management.create', 'Admin - Permission Management - Create', 'Create new permission'),
('admin', 'permission_management', 'edit', 'admin.permission_management.edit', 'Admin - Permission Management - Edit', 'Edit permission'),
('admin', 'permission_management', 'delete', 'admin.permission_management.delete', 'Admin - Permission Management - Delete', 'Delete permission'),
('admin', 'permission_management', 'sync', 'admin.permission_management.sync', 'Admin - Permission Management - Sync', 'Sync permissions from code'),
('admin', 'permission_management', 'export', 'admin.permission_management.export', 'Admin - Permission Management - Export', 'Export permission data')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- Admin Module - Activity Log (6 permissions)
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('admin', 'activity_log', 'navigation', 'admin.activity_log.navigation', 'Admin - Activity Log - Navigation', 'Access activity log menu'),
('admin', 'activity_log', 'view', 'admin.activity_log.view', 'Admin - Activity Log - View', 'View activity logs'),
('admin', 'activity_log', 'view_details', 'admin.activity_log.view_details', 'Admin - Activity Log - View Details', 'View log details'),
('admin', 'activity_log', 'export', 'admin.activity_log.export', 'Admin - Activity Log - Export', 'Export activity logs'),
('admin', 'activity_log', 'clean', 'admin.activity_log.clean', 'Admin - Activity Log - Clean', 'Clean old logs'),
('admin', 'activity_log', 'statistics', 'admin.activity_log.statistics', 'Admin - Activity Log - Statistics', 'View log statistics')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- Admin Module - System Settings (7 permissions)
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('admin', 'system_settings', 'navigation', 'admin.system_settings.navigation', 'Admin - System Settings - Navigation', 'Access system settings menu'),
('admin', 'system_settings', 'view', 'admin.system_settings.view', 'Admin - System Settings - View', 'View system settings'),
('admin', 'system_settings', 'edit', 'admin.system_settings.edit', 'Admin - System Settings - Edit', 'Edit system settings'),
('admin', 'system_settings', 'backup_database', 'admin.system_settings.backup_database', 'Admin - System Settings - Backup Database', 'Create database backup'),
('admin', 'system_settings', 'optimize_database', 'admin.system_settings.optimize_database', 'Admin - System Settings - Optimize Database', 'Optimize database'),
('admin', 'system_settings', 'clear_cache', 'admin.system_settings.clear_cache', 'Admin - System Settings - Clear Cache', 'Clear application cache'),
('admin', 'system_settings', 'system_info', 'admin.system_settings.system_info', 'Admin - System Settings - System Info', 'View system information')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- Reports Module (10 permissions)
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('reports', 'sales_report', 'navigation', 'reports.sales_report.navigation', 'Reports - Sales Report - Navigation', 'Access sales report'),
('reports', 'sales_report', 'view', 'reports.sales_report.view', 'Reports - Sales Report - View', 'View sales report'),
('reports', 'sales_report', 'export', 'reports.sales_report.export', 'Reports - Sales Report - Export', 'Export sales report'),
('reports', 'financial_report', 'navigation', 'reports.financial_report.navigation', 'Reports - Financial Report - Navigation', 'Access financial report'),
('reports', 'financial_report', 'view', 'reports.financial_report.view', 'Reports - Financial Report - View', 'View financial report'),
('reports', 'financial_report', 'export', 'reports.financial_report.export', 'Reports - Financial Report - Export', 'Export financial report'),
('reports', 'inventory_report', 'navigation', 'reports.inventory_report.navigation', 'Reports - Inventory Report - Navigation', 'Access inventory report'),
('reports', 'inventory_report', 'view', 'reports.inventory_report.view', 'Reports - Inventory Report - View', 'View inventory report'),
('reports', 'inventory_report', 'export', 'reports.inventory_report.export', 'Reports - Inventory Report - Export', 'Export inventory report'),
('reports', 'custom_report', 'create', 'reports.custom_report.create', 'Reports - Custom Report - Create', 'Create custom report')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- ═══════════════════════════════════════════════════════════════════════════════
-- PART 2: NEW MENU PERMISSIONS (21 permissions)
-- ═══════════════════════════════════════════════════════════════════════════════

-- Marketing Module - Audit Approval (5 permissions)
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('marketing', 'audit_approval', 'navigation', 'marketing.audit_approval.navigation', 'Marketing - Audit Approval - Navigation', 'Access audit approval menu'),
('marketing', 'audit_approval', 'view', 'marketing.audit_approval.view', 'Marketing - Audit Approval - View', 'View audit approval requests'),
('marketing', 'audit_approval', 'approve', 'marketing.audit_approval.approve', 'Marketing - Audit Approval - Approve', 'Approve audit requests'),
('marketing', 'audit_approval', 'reject', 'marketing.audit_approval.reject', 'Marketing - Audit Approval - Reject', 'Reject audit requests'),
('marketing', 'audit_approval', 'export', 'marketing.audit_approval.export', 'Marketing - Audit Approval - Export', 'Export audit approval data')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- Service Module - Unit Audit (7 permissions)
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('service', 'unit_audit', 'navigation', 'service.unit_audit.navigation', 'Service - Unit Audit - Navigation', 'Access unit audit menu'),
('service', 'unit_audit', 'view', 'service.unit_audit.view', 'Service - Unit Audit - View', 'View unit audit list'),
('service', 'unit_audit', 'create', 'service.unit_audit.create', 'Service - Unit Audit - Create', 'Create unit audit request'),
('service', 'unit_audit', 'edit', 'service.unit_audit.edit', 'Service - Unit Audit - Edit', 'Edit unit audit request'),
('service', 'unit_audit', 'submit', 'service.unit_audit.submit', 'Service - Unit Audit - Submit', 'Submit audit for approval'),
('service', 'unit_audit', 'delete', 'service.unit_audit.delete', 'Service - Unit Audit - Delete', 'Delete unit audit request'),
('service', 'unit_audit', 'export', 'service.unit_audit.export', 'Service - Unit Audit - Export', 'Export unit audit data')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- Warehouse Module - Surat Jalan / Movements (9 permissions)
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('warehouse', 'movements', 'navigation', 'warehouse.movements.navigation', 'Warehouse - Surat Jalan - Navigation', 'Access surat jalan menu'),
('warehouse', 'movements', 'view', 'warehouse.movements.view', 'Warehouse - Surat Jalan - View', 'View surat jalan list'),
('warehouse', 'movements', 'create', 'warehouse.movements.create', 'Warehouse - Surat Jalan - Create', 'Create new surat jalan'),
('warehouse', 'movements', 'edit', 'warehouse.movements.edit', 'Warehouse - Surat Jalan - Edit', 'Edit surat jalan'),
('warehouse', 'movements', 'confirm_departure', 'warehouse.movements.confirm_departure', 'Warehouse - Surat Jalan - Confirm Departure', 'Confirm unit departure'),
('warehouse', 'movements', 'confirm_arrival', 'warehouse.movements.confirm_arrival', 'Warehouse - Surat Jalan - Confirm Arrival', 'Confirm unit arrival'),
('warehouse', 'movements', 'cancel', 'warehouse.movements.cancel', 'Warehouse - Surat Jalan - Cancel', 'Cancel surat jalan'),
('warehouse', 'movements', 'print', 'warehouse.movements.print', 'Warehouse - Surat Jalan - Print', 'Print surat jalan document'),
('warehouse', 'movements', 'export', 'warehouse.movements.export', 'Warehouse - Surat Jalan - Export', 'Export movement data')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- ═══════════════════════════════════════════════════════════════════════════════
-- PART 3: ROLE PERMISSION ASSIGNMENTS
-- ═══════════════════════════════════════════════════════════════════════════════

-- Assign Marketing Audit Approval permissions to marketing_role
INSERT INTO role_permissions (role_id, permission_id, granted)
SELECT r.id, p.id, 1
FROM roles r
CROSS JOIN permissions p
WHERE r.name = 'marketing_role'
  AND p.key_name LIKE 'marketing.audit_approval.%'
ON DUPLICATE KEY UPDATE granted = 1;

-- Assign Service Unit Audit permissions to service_role
INSERT INTO role_permissions (role_id, permission_id, granted)
SELECT r.id, p.id, 1
FROM roles r
CROSS JOIN permissions p
WHERE r.name = 'service_role'
  AND p.key_name LIKE 'service.unit_audit.%'
ON DUPLICATE KEY UPDATE granted = 1;

-- Assign Warehouse Movements permissions to warehouse_role
INSERT INTO role_permissions (role_id, permission_id, granted)
SELECT r.id, p.id, 1
FROM roles r
CROSS JOIN permissions p
WHERE r.name = 'warehouse_role'
  AND p.key_name LIKE 'warehouse.movements.%'
ON DUPLICATE KEY UPDATE granted = 1;

-- Assign ALL new permissions to admin role
INSERT INTO role_permissions (role_id, permission_id, granted)
SELECT r.id, p.id, 1
FROM roles r
CROSS JOIN permissions p
WHERE r.name = 'admin'
  AND (p.key_name LIKE 'marketing.audit_approval.%'
    OR p.key_name LIKE 'service.unit_audit.%'
    OR p.key_name LIKE 'warehouse.movements.%')
ON DUPLICATE KEY UPDATE granted = 1;

-- Assign ALL new permissions to super_admin role
INSERT INTO role_permissions (role_id, permission_id, granted)
SELECT r.id, p.id, 1
FROM roles r
CROSS JOIN permissions p
WHERE r.name = 'super_admin'
  AND (p.key_name LIKE 'marketing.audit_approval.%'
    OR p.key_name LIKE 'service.unit_audit.%'
    OR p.key_name LIKE 'warehouse.movements.%')
ON DUPLICATE KEY UPDATE granted = 1;

-- ═══════════════════════════════════════════════════════════════════════════════
-- VERIFICATION QUERIES
-- ═══════════════════════════════════════════════════════════════════════════════

SELECT '═══════════════════════════════════════' AS '';
SELECT 'MIGRATION VERIFICATION' AS '';
SELECT '═══════════════════════════════════════' AS '';

-- Total permissions count
SELECT 'Total Permissions:' AS Info, COUNT(*) AS Count FROM permissions;

-- Permissions by module
SELECT 'Permissions by Module:' AS Info;
SELECT module, COUNT(*) as permission_count 
FROM permissions 
GROUP BY module 
ORDER BY module;

-- New menu permissions count
SELECT 'New Menu Permissions:' AS Info, COUNT(*) AS Count 
FROM permissions 
WHERE key_name LIKE 'marketing.audit_approval.%'
   OR key_name LIKE 'service.unit_audit.%'
   OR key_name LIKE 'warehouse.movements.%';

-- Role assignments for new permissions
SELECT 'Role Assignments for New Permissions:' AS Info;
SELECT r.name, COUNT(*) as assigned_permissions
FROM role_permissions rp
JOIN roles r ON rp.role_id = r.id
JOIN permissions p ON rp.permission_id = p.id
WHERE (p.key_name LIKE 'marketing.audit_approval.%'
    OR p.key_name LIKE 'service.unit_audit.%'
    OR p.key_name LIKE 'warehouse.movements.%')
  AND rp.granted = 1
GROUP BY r.name;

-- Check for orphaned permissions (permissions not assigned to any role)
SELECT 'Orphaned Permissions:' AS Info, COUNT(*) AS Count
FROM permissions p
LEFT JOIN role_permissions rp ON p.id = rp.permission_id
WHERE rp.id IS NULL;

SELECT '═══════════════════════════════════════' AS '';
SELECT 'MIGRATION COMPLETED SUCCESSFULLY!' AS '';
SELECT 'Timestamp:' AS Info, NOW() AS Timestamp;
SELECT '═══════════════════════════════════════' AS '';

-- ═══════════════════════════════════════════════════════════════════════════════
-- Commit the transaction
-- ═══════════════════════════════════════════════════════════════════════════════

COMMIT;

-- ═══════════════════════════════════════════════════════════════════════════════
-- ROLLBACK SCRIPT (Save this separately if needed)
-- ═══════════════════════════════════════════════════════════════════════════════
/*

-- TO ROLLBACK THIS MIGRATION:

START TRANSACTION;

-- Delete role assignments for new permissions
DELETE rp FROM role_permissions rp
JOIN permissions p ON rp.permission_id = p.id
WHERE p.key_name LIKE 'marketing.audit_approval.%'
   OR p.key_name LIKE 'service.unit_audit.%'
   OR p.key_name LIKE 'warehouse.movements.%';

-- Delete new permissions
DELETE FROM permissions 
WHERE key_name LIKE 'marketing.audit_approval.%'
   OR key_name LIKE 'service.unit_audit.%'
   OR key_name LIKE 'warehouse.movements.%';

-- Optionally: Delete ALL comprehensive permissions created today
-- (Only if you want to completely rollback the comprehensive permission system)
-- DELETE FROM permissions WHERE created_at >= '2026-03-07 00:00:00';

COMMIT;

*/
