-- ═══════════════════════════════════════════════════════════════════════════════
-- COMPREHENSIVE PERMISSION MIGRATION - OPTIMA SYSTEM
-- Generated: 2026-03-07
-- Total Permissions: ~300
-- 
-- WARNING: Run this on development first, verify, then production!
-- ═══════════════════════════════════════════════════════════════════════════════

-- ═══════════════════════════════════════════════════════════════════════════════
-- 1. DASHBOARD MODULE (3 permissions)
-- ═══════════════════════════════════════════════════════════════════════════════

INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('dashboard', 'home', 'navigation', 'dashboard.home.navigation', 'Dashboard - Home - Navigation', 'Access dashboard menu'),
('dashboard', 'home', 'view', 'dashboard.home.view', 'Dashboard - Home - View', 'View dashboard widgets'),
('dashboard', 'analytics', 'navigation', 'dashboard.analytics.navigation', 'Dashboard - Analytics - Navigation', 'Access analytics dashboard'),
('dashboard', 'analytics', 'view', 'dashboard.analytics.view', 'Dashboard - Analytics - View', 'View analytics data'),
('dashboard', 'analytics', 'export', 'dashboard.analytics.export', 'Dashboard - Analytics - Export', 'Export analytics report')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- ═══════════════════════════════════════════════════════════════════════════════
-- 2. MARKETING MODULE (50+ permissions)
-- ═══════════════════════════════════════════════════════════════════════════════

-- 2.1 Marketing - Customer
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('marketing', 'customer', 'navigation', 'marketing.customer.navigation', 'Marketing - Customer - Navigation', 'Access customer menu'),
('marketing', 'customer', 'view', 'marketing.customer.view', 'Marketing - Customer - View', 'View customer list and details'),
('marketing', 'customer', 'create', 'marketing.customer.create', 'Marketing - Customer - Create', 'Add new customer'),
('marketing', 'customer', 'edit', 'marketing.customer.edit', 'Marketing - Customer - Edit', 'Edit customer data'),
('marketing', 'customer', 'delete', 'marketing.customer.delete', 'Marketing - Customer - Delete', 'Delete customer'),
('marketing', 'customer', 'export', 'marketing.customer.export', 'Marketing - Customer - Export', 'Export customer list'),
('marketing', 'customer', 'import', 'marketing.customer.import', 'Marketing - Customer - Import', 'Import customer data')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- 2.2 Marketing - Quotation
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

-- 2.3 Marketing - Contract
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('marketing', 'contract', 'navigation', 'marketing.contract.navigation', 'Marketing - Contract - Navigation', 'Access contract menu'),
('marketing', 'contract', 'view', 'marketing.contract.view', 'Marketing - Contract - View', 'View contract list'),
('marketing', 'contract', 'create', 'marketing.contract.create', 'Marketing - Contract - Create', 'Create new contract'),
('marketing', 'contract', 'edit', 'marketing.contract.edit', 'Marketing - Contract - Edit', 'Edit contract'),
('marketing', 'contract', 'delete', 'marketing.contract.delete', 'Marketing - Contract - Delete', 'Delete contract'),
('marketing', 'contract', 'approve', 'marketing.contract.approve', 'Marketing - Contract - Approve', 'Approve contract'),
('marketing', 'contract', 'renew', 'marketing.contract.renew', 'Marketing - Contract - Renew', 'Renew contract'),
('marketing', 'contract', 'terminate', 'marketing.contract.terminate', 'Marketing - Contract - Terminate', 'Terminate contract'),
('marketing', 'contract', 'export', 'marketing.contract.export', 'Marketing - Contract - Export', 'Export contract data'),
('marketing', 'contract', 'print', 'marketing.contract.print', 'Marketing - Contract - Print', 'Print contract document')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- 2.4 Marketing - Audit Approval
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('marketing', 'audit_approval', 'navigation', 'marketing.audit_approval.navigation', 'Marketing - Audit Approval - Navigation', 'Access audit approval menu'),
('marketing', 'audit_approval', 'view', 'marketing.audit_approval.view', 'Marketing - Audit Approval - View', 'View audit approval list'),
('marketing', 'audit_approval', 'approve', 'marketing.audit_approval.approve', 'Marketing - Audit Approval - Approve', 'Approve unit audit'),
('marketing', 'audit_approval', 'reject', 'marketing.audit_approval.reject', 'Marketing - Audit Approval - Reject', 'Reject unit audit'),
('marketing', 'audit_approval', 'export', 'marketing.audit_approval.export', 'Marketing - Audit Approval - Export', 'Export audit data')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- 2.5 Marketing - Performance
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('marketing', 'performance', 'navigation', 'marketing.performance.navigation', 'Marketing - Performance - Navigation', 'Access performance dashboard'),
('marketing', 'performance', 'view', 'marketing.performance.view', 'Marketing - Performance - View', 'View performance metrics'),
('marketing', 'performance', 'export', 'marketing.performance.export', 'Marketing - Performance - Export', 'Export performance report')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- ═══════════════════════════════════════════════════════════════════════════════
-- 3. SERVICE MODULE (45+ permissions)
-- ═══════════════════════════════════════════════════════════════════════════════

-- 3.1 Service - Work Order
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('service', 'work_order', 'navigation', 'service.work_order.navigation', 'Service - Work Order - Navigation', 'Access work order menu'),
('service', 'work_order', 'view', 'service.work_order.view', 'Service - Work Order - View', 'View work order list'),
('service', 'work_order', 'create', 'service.work_order.create', 'Service - Work Order - Create', 'Create work order'),
('service', 'work_order', 'edit', 'service.work_order.edit', 'Service - Work Order - Edit', 'Edit work order'),
('service', 'work_order', 'delete', 'service.work_order.delete', 'Service - Work Order - Delete', 'Delete work order'),
('service', 'work_order', 'assign', 'service.work_order.assign', 'Service - Work Order - Assign', 'Assign technician'),
('service', 'work_order', 'complete', 'service.work_order.complete', 'Service - Work Order - Complete', 'Mark as completed'),
('service', 'work_order', 'export', 'service.work_order.export', 'Service - Work Order - Export', 'Export work order'),
('service', 'work_order', 'print', 'service.work_order.print', 'Service - Work Order - Print', 'Print work order')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- 3.2 Service - Unit Audit
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('service', 'unit_audit', 'navigation', 'service.unit_audit.navigation', 'Service - Unit Audit - Navigation', 'Access unit audit menu'),
('service', 'unit_audit', 'view', 'service.unit_audit.view', 'Service - Unit Audit - View', 'View audit list'),
('service', 'unit_audit', 'create', 'service.unit_audit.create', 'Service - Unit Audit - Create', 'Create unit audit'),
('service', 'unit_audit', 'edit', 'service.unit_audit.edit', 'Service - Unit Audit - Edit', 'Edit audit'),
('service', 'unit_audit', 'delete', 'service.unit_audit.delete', 'Service - Unit Audit - Delete', 'Delete audit'),
('service', 'unit_audit', 'submit', 'service.unit_audit.submit', 'Service - Unit Audit - Submit', 'Submit for approval'),
('service', 'unit_audit', 'export', 'service.unit_audit.export', 'Service - Unit Audit - Export', 'Export audit data')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- 3.3 Service - Unit Audit Location
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('service', 'unit_audit_location', 'navigation', 'service.unit_audit_location.navigation', 'Service - Audit Location - Navigation', 'Access audit by location menu'),
('service', 'unit_audit_location', 'view', 'service.unit_audit_location.view', 'Service - Audit Location - View', 'View location audit'),
('service', 'unit_audit_location', 'create', 'service.unit_audit_location.create', 'Service - Audit Location - Create', 'Create location audit'),
('service', 'unit_audit_location', 'export', 'service.unit_audit_location.export', 'Service - Audit Location - Export', 'Export location audit')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- 3.4 Service - Maintenance
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('service', 'maintenance', 'navigation', 'service.maintenance.navigation', 'Service - Maintenance - Navigation', 'Access maintenance menu'),
('service', 'maintenance', 'view', 'service.maintenance.view', 'Service - Maintenance - View', 'View maintenance schedule'),
('service', 'maintenance', 'create', 'service.maintenance.create', 'Service - Maintenance - Create', 'Create maintenance'),
('service', 'maintenance', 'edit', 'service.maintenance.edit', 'Service - Maintenance - Edit', 'Edit maintenance'),
('service', 'maintenance', 'delete', 'service.maintenance.delete', 'Service - Maintenance - Delete', 'Delete maintenance'),
('service', 'maintenance', 'complete', 'service.maintenance.complete', 'Service - Maintenance - Complete', 'Mark as completed'),
('service', 'maintenance', 'export', 'service.maintenance.export', 'Service - Maintenance - Export', 'Export maintenance')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- 3.5 Service - Area Management
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('service', 'area_management', 'navigation', 'service.area_management.navigation', 'Service - Area - Navigation', 'Access area management menu'),
('service', 'area_management', 'view', 'service.area_management.view', 'Service - Area - View', 'View service areas'),
('service', 'area_management', 'create', 'service.area_management.create', 'Service - Area - Create', 'Create service area'),
('service', 'area_management', 'edit', 'service.area_management.edit', 'Service - Area - Edit', 'Edit service area'),
('service', 'area_management', 'delete', 'service.area_management.delete', 'Service - Area - Delete', 'Delete service area')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- ═══════════════════════════════════════════════════════════════════════════════
-- 4. WAREHOUSE MODULE (40+ permissions)
-- ═══════════════════════════════════════════════════════════════════════════════

-- 4.1 Warehouse - Inventory Unit
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('warehouse', 'inventory_unit', 'navigation', 'warehouse.inventory_unit.navigation', 'Warehouse - Inventory - Navigation', 'Access inventory menu'),
('warehouse', 'inventory_unit', 'view', 'warehouse.inventory_unit.view', 'Warehouse - Inventory - View', 'View inventory'),
('warehouse', 'inventory_unit', 'create', 'warehouse.inventory_unit.create', 'Warehouse - Inventory - Create', 'Add unit to inventory'),
('warehouse', 'inventory_unit', 'edit', 'warehouse.inventory_unit.edit', 'Warehouse - Inventory - Edit', 'Edit inventory data'),
('warehouse', 'inventory_unit', 'delete', 'warehouse.inventory_unit.delete', 'Warehouse - Inventory - Delete', 'Delete from inventory'),
('warehouse', 'inventory_unit', 'export', 'warehouse.inventory_unit.export', 'Warehouse - Inventory - Export', 'Export inventory'),
('warehouse', 'inventory_unit', 'print', 'warehouse.inventory_unit.print', 'Warehouse - Inventory - Print', 'Print inventory report'),
('warehouse', 'inventory_unit', 'adjust', 'warehouse.inventory_unit.adjust', 'Warehouse - Inventory - Adjust', 'Adjust stock')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- 4.2 Warehouse - Movements (Surat Jalan)
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('warehouse', 'movements', 'navigation', 'warehouse.movements.navigation', 'Warehouse - Movements - Navigation', 'Access movements menu'),
('warehouse', 'movements', 'view', 'warehouse.movements.view', 'Warehouse - Movements - View', 'View movement history'),
('warehouse', 'movements', 'create', 'warehouse.movements.create', 'Warehouse - Movements - Create', 'Create surat jalan'),
('warehouse', 'movements', 'edit', 'warehouse.movements.edit', 'Warehouse - Movements - Edit', 'Edit movement'),
('warehouse', 'movements', 'confirm_departure', 'warehouse.movements.confirm_departure', 'Warehouse - Movements - Confirm Departure', 'Confirm unit departure'),
('warehouse', 'movements', 'confirm_arrival', 'warehouse.movements.confirm_arrival', 'Warehouse - Movements - Confirm Arrival', 'Confirm unit arrival'),
('warehouse', 'movements', 'cancel', 'warehouse.movements.cancel', 'Warehouse - Movements - Cancel', 'Cancel movement'),
('warehouse', 'movements', 'print', 'warehouse.movements.print', 'Warehouse - Movements - Print', 'Print surat jalan'),
('warehouse', 'movements', 'export', 'warehouse.movements.export', 'Warehouse - Movements - Export', 'Export movement data')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- 4.3 Warehouse - Stock Opname
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('warehouse', 'stock_opname', 'navigation', 'warehouse.stock_opname.navigation', 'Warehouse - Stock Opname - Navigation', 'Access stock opname menu'),
('warehouse', 'stock_opname', 'view', 'warehouse.stock_opname.view', 'Warehouse - Stock Opname - View', 'View stock opname'),
('warehouse', 'stock_opname', 'create', 'warehouse.stock_opname.create', 'Warehouse - Stock Opname - Create', 'Create stock opname'),
('warehouse', 'stock_opname', 'edit', 'warehouse.stock_opname.edit', 'Warehouse - Stock Opname - Edit', 'Edit stock opname'),
('warehouse', 'stock_opname', 'submit', 'warehouse.stock_opname.submit', 'Warehouse - Stock Opname - Submit', 'Submit stock opname'),
('warehouse', 'stock_opname', 'approve', 'warehouse.stock_opname.approve', 'Warehouse - Stock Opname - Approve', 'Approve stock opname'),
('warehouse', 'stock_opname', 'export', 'warehouse.stock_opname.export', 'Warehouse - Stock Opname - Export', 'Export stock opname')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- 4.4 Warehouse - Receiving
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('warehouse', 'receiving', 'navigation', 'warehouse.receiving.navigation', 'Warehouse - Receiving - Navigation', 'Access receiving menu'),
('warehouse', 'receiving', 'view', 'warehouse.receiving.view', 'Warehouse - Receiving - View', 'View receiving list'),
('warehouse', 'receiving', 'create', 'warehouse.receiving.create', 'Warehouse - Receiving - Create', 'Create receiving'),
('warehouse', 'receiving', 'edit', 'warehouse.receiving.edit', 'Warehouse - Receiving - Edit', 'Edit receiving'),
('warehouse', 'receiving', 'verify', 'warehouse.receiving.verify', 'Warehouse - Receiving - Verify', 'Verify received items'),
('warehouse', 'receiving', 'print', 'warehouse.receiving.print', 'Warehouse - Receiving - Print', 'Print receiving note')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- ═══════════════════════════════════════════════════════════════════════════════
-- 5. PURCHASING MODULE (25+ permissions)
-- ═══════════════════════════════════════════════════════════════════════════════

-- 5.1 Purchasing - Unit
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('purchasing', 'unit', 'navigation', 'purchasing.unit.navigation', 'Purchasing - Unit - Navigation', 'Access purchase unit menu'),
('purchasing', 'unit', 'view', 'purchasing.unit.view', 'Purchasing - Unit - View', 'View purchase list'),
('purchasing', 'unit', 'create', 'purchasing.unit.create', 'Purchasing - Unit - Create', 'Create purchase'),
('purchasing', 'unit', 'edit', 'purchasing.unit.edit', 'Purchasing - Unit - Edit', 'Edit purchase'),
('purchasing', 'unit', 'delete', 'purchasing.unit.delete', 'Purchasing - Unit - Delete', 'Delete purchase'),
('purchasing', 'unit', 'approve', 'purchasing.unit.approve', 'Purchasing - Unit - Approve', 'Approve purchase'),
('purchasing', 'unit', 'export', 'purchasing.unit.export', 'Purchasing - Unit - Export', 'Export purchase data')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- 5.2 Purchasing - Vendor
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('purchasing', 'vendor', 'navigation', 'purchasing.vendor.navigation', 'Purchasing - Vendor - Navigation', 'Access vendor menu'),
('purchasing', 'vendor', 'view', 'purchasing.vendor.view', 'Purchasing - Vendor - View', 'View vendor list'),
('purchasing', 'vendor', 'create', 'purchasing.vendor.create', 'Purchasing - Vendor - Create', 'Add vendor'),
('purchasing', 'vendor', 'edit', 'purchasing.vendor.edit', 'Purchasing - Vendor - Edit', 'Edit vendor'),
('purchasing', 'vendor', 'delete', 'purchasing.vendor.delete', 'Purchasing - Vendor - Delete', 'Delete vendor'),
('purchasing', 'vendor', 'export', 'purchasing.vendor.export', 'Purchasing - Vendor - Export', 'Export vendor list')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- 5.3 Purchasing - PO
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('purchasing', 'po', 'navigation', 'purchasing.po.navigation', 'Purchasing - PO - Navigation', 'Access PO menu'),
('purchasing', 'po', 'view', 'purchasing.po.view', 'Purchasing - PO - View', 'View PO list'),
('purchasing', 'po', 'create', 'purchasing.po.create', 'Purchasing - PO - Create', 'Create PO'),
('purchasing', 'po', 'edit', 'purchasing.po.edit', 'Purchasing - PO - Edit', 'Edit PO'),
('purchasing', 'po', 'delete', 'purchasing.po.delete', 'Purchasing - PO - Delete', 'Delete PO'),
('purchasing', 'po', 'approve', 'purchasing.po.approve', 'Purchasing - PO - Approve', 'Approve PO'),
('purchasing', 'po', 'print', 'purchasing.po.print', 'Purchasing - PO - Print', 'Print PO')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- ═══════════════════════════════════════════════════════════════════════════════
-- 6. FINANCE MODULE (30+ permissions)
-- ═══════════════════════════════════════════════════════════════════════════════

-- 6.1 Finance - Invoice
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('finance', 'invoice', 'navigation', 'finance.invoice.navigation', 'Finance - Invoice - Navigation', 'Access invoice menu'),
('finance', 'invoice', 'view', 'finance.invoice.view', 'Finance - Invoice - View', 'View invoice list'),
('finance', 'invoice', 'create', 'finance.invoice.create', 'Finance - Invoice - Create', 'Create invoice'),
('finance', 'invoice', 'edit', 'finance.invoice.edit', 'Finance - Invoice - Edit', 'Edit invoice'),
('finance', 'invoice', 'delete', 'finance.invoice.delete', 'Finance - Invoice - Delete', 'Delete invoice'),
('finance', 'invoice', 'approve', 'finance.invoice.approve', 'Finance - Invoice - Approve', 'Approve invoice'),
('finance', 'invoice', 'print', 'finance.invoice.print', 'Finance - Invoice - Print', 'Print invoice'),
('finance', 'invoice', 'export', 'finance.invoice.export', 'Finance - Invoice - Export', 'Export invoice')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- 6.2 Finance - Payment
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('finance', 'payment', 'navigation', 'finance.payment.navigation', 'Finance - Payment - Navigation', 'Access payment menu'),
('finance', 'payment', 'view', 'finance.payment.view', 'Finance - Payment - View', 'View payment list'),
('finance', 'payment', 'create', 'finance.payment.create', 'Finance - Payment - Create', 'Record payment'),
('finance', 'payment', 'edit', 'finance.payment.edit', 'Finance - Payment - Edit', 'Edit payment'),
('finance', 'payment', 'delete', 'finance.payment.delete', 'Finance - Payment - Delete', 'Delete payment'),
('finance', 'payment', 'verify', 'finance.payment.verify', 'Finance - Payment - Verify', 'Verify payment'),
('finance', 'payment', 'export', 'finance.payment.export', 'Finance - Payment - Export', 'Export payment')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- 6.3 Finance - Billing
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('finance', 'billing', 'navigation', 'finance.billing.navigation', 'Finance - Billing - Navigation', 'Access billing menu'),
('finance', 'billing', 'view', 'finance.billing.view', 'Finance - Billing - View', 'View billing'),
('finance', 'billing', 'create', 'finance.billing.create', 'Finance - Billing - Create', 'Create billing'),
('finance', 'billing', 'edit', 'finance.billing.edit', 'Finance - Billing - Edit', 'Edit billing'),
('finance', 'billing', 'send', 'finance.billing.send', 'Finance - Billing - Send', 'Send billing to customer'),
('finance', 'billing', 'export', 'finance.billing.export', 'Finance - Billing - Export', 'Export billing')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- ═══════════════════════════════════════════════════════════════════════════════
-- 7. OPERATIONAL MODULE (35+ permissions)
-- ═══════════════════════════════════════════════════════════════════════════════

-- 7.1 Operational - Unit Rolling
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('operational', 'unit_rolling', 'navigation', 'operational.unit_rolling.navigation', 'Operational - Rolling - Navigation', 'Access unit rolling menu'),
('operational', 'unit_rolling', 'view', 'operational.unit_rolling.view', 'Operational - Rolling - View', 'View rolling schedule'),
('operational', 'unit_rolling', 'create', 'operational.unit_rolling.create', 'Operational - Rolling - Create', 'Create rolling'),
('operational', 'unit_rolling', 'edit', 'operational.unit_rolling.edit', 'Operational - Rolling - Edit', 'Edit rolling'),
('operational', 'unit_rolling', 'approve', 'operational.unit_rolling.approve', 'Operational - Rolling - Approve', 'Approve rolling'),
('operational', 'unit_rolling', 'execute', 'operational.unit_rolling.execute', 'Operational - Rolling - Execute', 'Execute rolling'),
('operational', 'unit_rolling', 'export', 'operational.unit_rolling.export', 'Operational - Rolling - Export', 'Export rolling data')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- 7.2 Operational - Unit Asset
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('operational', 'unit_asset', 'navigation', 'operational.unit_asset.navigation', 'Operational - Asset - Navigation', 'Access unit asset menu'),
('operational', 'unit_asset', 'view', 'operational.unit_asset.view', 'Operational - Asset - View', 'View asset list'),
('operational', 'unit_asset', 'create', 'operational.unit_asset.create', 'Operational - Asset - Create', 'Register asset'),
('operational', 'unit_asset', 'edit', 'operational.unit_asset.edit', 'Operational - Asset - Edit', 'Edit asset'),
('operational', 'unit_asset', 'delete', 'operational.unit_asset.delete', 'Operational - Asset - Delete', 'Delete asset'),
('operational', 'unit_asset', 'transfer', 'operational.unit_asset.transfer', 'Operational - Asset - Transfer', 'Transfer asset'),
('operational', 'unit_asset', 'export', 'operational.unit_asset.export', 'Operational - Asset - Export', 'Export asset')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- 7.3 Operational - Perizinan
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('operational', 'perizinan', 'navigation', 'operational.perizinan.navigation', 'Operational - Perizinan - Navigation', 'Access perizinan menu'),
('operational', 'perizinan', 'view', 'operational.perizinan.view', 'Operational - Perizinan - View', 'View perizinan'),
('operational', 'perizinan', 'create', 'operational.perizinan.create', 'Operational - Perizinan - Create', 'Create perizinan'),
('operational', 'perizinan', 'edit', 'operational.perizinan.edit', 'Operational - Perizinan - Edit', 'Edit perizinan'),
('operational', 'perizinan', 'renew', 'operational.perizinan.renew', 'Operational - Perizinan - Renew', 'Renew perizinan'),
('operational', 'perizinan', 'export', 'operational.perizinan.export', 'Operational - Perizinan - Export', 'Export perizinan')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- ═══════════════════════════════════════════════════════════════════════════════
-- 8. REPORTS MODULE (15+ permissions)
-- ═══════════════════════════════════════════════════════════════════════════════

INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('reports', 'contract', 'navigation', 'reports.contract.navigation', 'Reports - Contract - Navigation', 'Access contract reports'),
('reports', 'contract', 'view', 'reports.contract.view', 'Reports - Contract - View', 'View contract reports'),
('reports', 'contract', 'export', 'reports.contract.export', 'Reports - Contract - Export', 'Export contract reports'),
('reports', 'revenue', 'navigation', 'reports.revenue.navigation', 'Reports - Revenue - Navigation', 'Access revenue reports'),
('reports', 'revenue', 'view', 'reports.revenue.view', 'Reports - Revenue - View', 'View revenue reports'),
('reports', 'revenue', 'export', 'reports.revenue.export', 'Reports - Revenue - Export', 'Export revenue reports'),
('reports', 'unit', 'navigation', 'reports.unit.navigation', 'Reports - Unit - Navigation', 'Access unit reports'),
('reports', 'unit', 'view', 'reports.unit.view', 'Reports - Unit - View', 'View unit reports'),
('reports', 'unit', 'export', 'reports.unit.export', 'Reports - Unit - Export', 'Export unit reports'),
('reports', 'performance', 'navigation', 'reports.performance.navigation', 'Reports - Performance - Navigation', 'Access performance reports'),
('reports', 'performance', 'view', 'reports.performance.view', 'Reports - Performance - View', 'View performance reports'),
('reports', 'performance', 'export', 'reports.performance.export', 'Reports - Performance - Export', 'Export performance reports')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- ═══════════════════════════════════════════════════════════════════════════════
-- 9. SETTINGS MODULE (40+ permissions)
-- ═══════════════════════════════════════════════════════════════════════════════

-- 9.1 Settings - System
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('settings', 'system', 'navigation', 'settings.system.navigation', 'Settings - System - Navigation', 'Access system settings'),
('settings', 'system', 'view', 'settings.system.view', 'Settings - System - View', 'View system settings'),
('settings', 'system', 'edit', 'settings.system.edit', 'Settings - System - Edit', 'Edit system settings')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- 9.2 Settings - User
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('settings', 'user', 'navigation', 'settings.user.navigation', 'Settings - User - Navigation', 'Access user management'),
('settings', 'user', 'view', 'settings.user.view', 'Settings - User - View', 'View user list'),
('settings', 'user', 'create', 'settings.user.create', 'Settings - User - Create', 'Create user'),
('settings', 'user', 'edit', 'settings.user.edit', 'Settings - User - Edit', 'Edit user'),
('settings', 'user', 'delete', 'settings.user.delete', 'Settings - User - Delete', 'Delete user'),
('settings', 'user', 'reset_password', 'settings.user.reset_password', 'Settings - User - Reset Password', 'Reset user password'),
('settings', 'user', 'assign_role', 'settings.user.assign_role', 'Settings - User - Assign Role', 'Assign role to user'),
('settings', 'user', 'assign_permission', 'settings.user.assign_permission', 'Settings - User - Assign Permission', 'Assign custom permission')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- 9.3 Settings - Role
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('settings', 'role', 'navigation', 'settings.role.navigation', 'Settings - Role - Navigation', 'Access role management'),
('settings', 'role', 'view', 'settings.role.view', 'Settings - Role - View', 'View role list'),
('settings', 'role', 'create', 'settings.role.create', 'Settings - Role - Create', 'Create role'),
('settings', 'role', 'edit', 'settings.role.edit', 'Settings - Role - Edit', 'Edit role'),
('settings', 'role', 'delete', 'settings.role.delete', 'Settings - Role - Delete', 'Delete role'),
('settings', 'role', 'assign_permission', 'settings.role.assign_permission', 'Settings - Role - Assign Permission', 'Assign permissions to role')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- 9.4 Settings - Permission
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('settings', 'permission', 'navigation', 'settings.permission.navigation', 'Settings - Permission - Navigation', 'Access permission management'),
('settings', 'permission', 'view', 'settings.permission.view', 'Settings - Permission - View', 'View permission list'),
('settings', 'permission', 'create', 'settings.permission.create', 'Settings - Permission - Create', 'Create custom permission'),
('settings', 'permission', 'edit', 'settings.permission.edit', 'Settings - Permission - Edit', 'Edit permission'),
('settings', 'permission', 'delete', 'settings.permission.delete', 'Settings - Permission - Delete', 'Delete permission')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- 9.5 Settings - Division
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('settings', 'division', 'navigation', 'settings.division.navigation', 'Settings - Division - Navigation', 'Access division management'),
('settings', 'division', 'view', 'settings.division.view', 'Settings - Division - View', 'View division list'),
('settings', 'division', 'create', 'settings.division.create', 'Settings - Division - Create', 'Create division'),
('settings', 'division', 'edit', 'settings.division.edit', 'Settings - Division - Edit', 'Edit division'),
('settings', 'division', 'delete', 'settings.division.delete', 'Settings - Division - Delete', 'Delete division')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- 9.6 Settings - Notification
INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('settings', 'notification', 'navigation', 'settings.notification.navigation', 'Settings - Notification - Navigation', 'Access notification settings'),
('settings', 'notification', 'view', 'settings.notification.view', 'Settings - Notification - View', 'View notification settings'),
('settings', 'notification', 'edit', 'settings.notification.edit', 'Settings - Notification - Edit', 'Edit notification rules')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- ═══════════════════════════════════════════════════════════════════════════════
-- 10. ACTIVITY LOG MODULE (4 permissions)
-- ═══════════════════════════════════════════════════════════════════════════════

INSERT INTO permissions (module, page, action, key_name, display_name, description) VALUES
('activity', 'log', 'navigation', 'activity.log.navigation', 'Activity - Log - Navigation', 'Access activity log'),
('activity', 'log', 'view', 'activity.log.view', 'Activity - Log - View', 'View activity log'),
('activity', 'log', 'export', 'activity.log.export', 'Activity - Log - Export', 'Export activity log'),
('activity', 'log', 'delete', 'activity.log.delete', 'Activity - Log - Delete', 'Delete old logs')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), description = VALUES(description);

-- ═══════════════════════════════════════════════════════════════════════════════
-- VERIFICATION
-- ═══════════════════════════════════════════════════════════════════════════════

SELECT 
    module,
    COUNT(*) as total_permissions
FROM permissions
GROUP BY module
ORDER BY module;

SELECT COUNT(*) as total_permissions FROM permissions;

