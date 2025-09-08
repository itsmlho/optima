-- Comprehensive System Activity Logging Infrastructure
-- Supports ALL modules: Purchasing, Warehouse, Marketing, Service, Operational, Accounting, Perizinan
-- Scalable, Fast, and Complete activity tracking

-- First, let's enhance the existing table to support all modules
ALTER TABLE `system_activity_log` 
ADD COLUMN `module_name` ENUM(
    'PURCHASING', 'WAREHOUSE', 'MARKETING', 'SERVICE', 
    'OPERATIONAL', 'ACCOUNTING', 'PERIZINAN', 'ADMIN', 
    'DASHBOARD', 'REPORTS', 'SETTINGS', 'USER_MANAGEMENT'
) NULL COMMENT 'Application module where activity occurred',

ADD COLUMN `feature_name` VARCHAR(100) NULL COMMENT 'Specific feature/page within module',
ADD COLUMN `sub_feature` VARCHAR(100) NULL COMMENT 'Sub-feature or section within page',

-- Enhanced business context
ADD COLUMN `business_impact` ENUM('LOW','MEDIUM','HIGH','CRITICAL') DEFAULT 'LOW' COMMENT 'Business impact level',
ADD COLUMN `compliance_relevant` BOOLEAN DEFAULT FALSE COMMENT 'Relevant for compliance/audit',
ADD COLUMN `financial_impact` DECIMAL(15,2) NULL COMMENT 'Financial impact of this activity',

-- Extended references for all modules
ADD COLUMN `related_purchase_order_id` INT UNSIGNED NULL COMMENT 'Related PO for purchasing module',
ADD COLUMN `related_vendor_id` INT UNSIGNED NULL COMMENT 'Related vendor/supplier',
ADD COLUMN `related_customer_id` INT UNSIGNED NULL COMMENT 'Related customer',
ADD COLUMN `related_invoice_id` INT UNSIGNED NULL COMMENT 'Related invoice for accounting',
ADD COLUMN `related_payment_id` INT UNSIGNED NULL COMMENT 'Related payment record',
ADD COLUMN `related_permit_id` INT UNSIGNED NULL COMMENT 'Related permit for perizinan',
ADD COLUMN `related_warehouse_id` INT UNSIGNED NULL COMMENT 'Related warehouse location',

-- Additional metadata for comprehensive tracking
ADD COLUMN `device_type` ENUM('DESKTOP','MOBILE','TABLET','API') NULL COMMENT 'Device type used',
ADD COLUMN `browser_name` VARCHAR(50) NULL COMMENT 'Browser name',
ADD COLUMN `operating_system` VARCHAR(50) NULL COMMENT 'Operating system',
ADD COLUMN `referrer_url` VARCHAR(255) NULL COMMENT 'Previous page URL',

-- Performance and debugging
ADD COLUMN `memory_usage_mb` FLOAT NULL COMMENT 'Memory usage during action',
ADD COLUMN `query_count` INT UNSIGNED NULL COMMENT 'Number of DB queries executed',
ADD COLUMN `cache_hit` BOOLEAN NULL COMMENT 'Whether cache was hit',

-- Batch operations support
ADD COLUMN `batch_id` VARCHAR(36) NULL COMMENT 'UUID for grouping related activities',
ADD COLUMN `batch_sequence` INT UNSIGNED NULL COMMENT 'Sequence number in batch',
ADD COLUMN `parent_activity_id` INT UNSIGNED NULL COMMENT 'Reference to parent activity',

-- Indexes for all new fields
ADD INDEX `idx_module_feature` (`module_name`, `feature_name`),
ADD INDEX `idx_business_impact` (`business_impact`),
ADD INDEX `idx_compliance` (`compliance_relevant`),
ADD INDEX `idx_batch_id` (`batch_id`),
ADD INDEX `idx_parent_activity` (`parent_activity_id`),
ADD INDEX `idx_related_po` (`related_purchase_order_id`),
ADD INDEX `idx_related_vendor` (`related_vendor_id`),
ADD INDEX `idx_related_customer` (`related_customer_id`),
ADD INDEX `idx_related_invoice` (`related_invoice_id`),
ADD INDEX `idx_related_permit` (`related_permit_id`),
ADD INDEX `idx_device_type` (`device_type`);

-- Create module-specific activity type enums for better categorization
CREATE TABLE `activity_types` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `module_name` VARCHAR(50) NOT NULL,
  `type_code` VARCHAR(50) NOT NULL,
  `type_name` VARCHAR(100) NOT NULL,
  `description` TEXT NULL,
  `business_impact_default` ENUM('LOW','MEDIUM','HIGH','CRITICAL') DEFAULT 'LOW',
  `is_active` BOOLEAN DEFAULT TRUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_module_type` (`module_name`, `type_code`)
);

-- Insert predefined activity types for all modules
INSERT INTO `activity_types` (`module_name`, `type_code`, `type_name`, `description`, `business_impact_default`) VALUES
-- PURCHASING Module
('PURCHASING', 'PO_CREATE', 'Purchase Order Created', 'New purchase order created', 'HIGH'),
('PURCHASING', 'PO_APPROVE', 'Purchase Order Approved', 'Purchase order approved by authorized person', 'HIGH'),
('PURCHASING', 'PO_REJECT', 'Purchase Order Rejected', 'Purchase order rejected', 'MEDIUM'),
('PURCHASING', 'PO_CANCEL', 'Purchase Order Cancelled', 'Purchase order cancelled', 'HIGH'),
('PURCHASING', 'VENDOR_ADD', 'Vendor Added', 'New vendor/supplier added to system', 'MEDIUM'),
('PURCHASING', 'VENDOR_UPDATE', 'Vendor Updated', 'Vendor information updated', 'LOW'),
('PURCHASING', 'QUOTATION_REQUEST', 'Quotation Requested', 'Quotation requested from vendor', 'MEDIUM'),
('PURCHASING', 'QUOTATION_RECEIVE', 'Quotation Received', 'Quotation received from vendor', 'MEDIUM'),

-- WAREHOUSE Module  
('WAREHOUSE', 'STOCK_IN', 'Stock In', 'Items received into warehouse', 'MEDIUM'),
('WAREHOUSE', 'STOCK_OUT', 'Stock Out', 'Items issued from warehouse', 'MEDIUM'),
('WAREHOUSE', 'STOCK_TRANSFER', 'Stock Transfer', 'Items transferred between locations', 'MEDIUM'),
('WAREHOUSE', 'STOCK_ADJUSTMENT', 'Stock Adjustment', 'Stock quantity adjusted', 'HIGH'),
('WAREHOUSE', 'LOCATION_CREATE', 'Location Created', 'New warehouse location created', 'LOW'),
('WAREHOUSE', 'INVENTORY_COUNT', 'Inventory Count', 'Physical inventory count performed', 'HIGH'),
('WAREHOUSE', 'DAMAGE_REPORT', 'Damage Reported', 'Damaged items reported', 'MEDIUM'),

-- MARKETING Module
('MARKETING', 'LEAD_CREATE', 'Lead Created', 'New sales lead created', 'MEDIUM'),
('MARKETING', 'LEAD_CONVERT', 'Lead Converted', 'Lead converted to opportunity', 'HIGH'),
('MARKETING', 'QUOTE_GENERATE', 'Quote Generated', 'Sales quotation generated', 'MEDIUM'),
('MARKETING', 'CONTRACT_CREATE', 'Contract Created', 'New contract/kontrak created', 'HIGH'),
('MARKETING', 'CONTRACT_APPROVE', 'Contract Approved', 'Contract approved', 'CRITICAL'),
('MARKETING', 'CONTRACT_SIGN', 'Contract Signed', 'Contract signed by customer', 'CRITICAL'),
('MARKETING', 'UNIT_ASSIGN', 'Unit Assigned', 'Unit assigned to contract', 'HIGH'),

-- SERVICE Module
('SERVICE', 'SPK_CREATE', 'SPK Created', 'Service work order (SPK) created', 'HIGH'),
('SERVICE', 'SPK_START', 'SPK Started', 'Work on SPK started', 'MEDIUM'),
('SERVICE', 'SPK_COMPLETE', 'SPK Completed', 'SPK work completed', 'HIGH'),
('SERVICE', 'MAINTENANCE_SCHEDULE', 'Maintenance Scheduled', 'Maintenance scheduled for unit', 'MEDIUM'),
('SERVICE', 'MAINTENANCE_COMPLETE', 'Maintenance Completed', 'Maintenance work completed', 'MEDIUM'),
('SERVICE', 'REPAIR_REQUEST', 'Repair Requested', 'Repair service requested', 'MEDIUM'),
('SERVICE', 'PART_USED', 'Parts Used', 'Spare parts used in service', 'LOW'),

-- OPERATIONAL Module
('OPERATIONAL', 'DI_CREATE', 'Delivery Instruction Created', 'New delivery instruction created', 'HIGH'),
('OPERATIONAL', 'DISPATCH', 'Unit Dispatched', 'Unit dispatched for delivery', 'HIGH'),
('OPERATIONAL', 'DELIVERY_COMPLETE', 'Delivery Completed', 'Unit delivered to customer', 'CRITICAL'),
('OPERATIONAL', 'PICKUP_SCHEDULE', 'Pickup Scheduled', 'Unit pickup scheduled', 'MEDIUM'),
('OPERATIONAL', 'PICKUP_COMPLETE', 'Pickup Completed', 'Unit picked up from customer', 'HIGH'),
('OPERATIONAL', 'ROUTE_OPTIMIZE', 'Route Optimized', 'Delivery route optimized', 'LOW'),

-- ACCOUNTING Module
('ACCOUNTING', 'INVOICE_CREATE', 'Invoice Created', 'New invoice created', 'HIGH'),
('ACCOUNTING', 'INVOICE_SEND', 'Invoice Sent', 'Invoice sent to customer', 'MEDIUM'),
('ACCOUNTING', 'PAYMENT_RECEIVE', 'Payment Received', 'Payment received from customer', 'CRITICAL'),
('ACCOUNTING', 'PAYMENT_OVERDUE', 'Payment Overdue', 'Payment marked as overdue', 'HIGH'),
('ACCOUNTING', 'EXPENSE_RECORD', 'Expense Recorded', 'Business expense recorded', 'MEDIUM'),
('ACCOUNTING', 'JOURNAL_ENTRY', 'Journal Entry', 'Accounting journal entry created', 'MEDIUM'),
('ACCOUNTING', 'RECONCILIATION', 'Bank Reconciliation', 'Bank account reconciled', 'HIGH'),

-- PERIZINAN Module
('PERIZINAN', 'PERMIT_APPLY', 'Permit Application', 'New permit application submitted', 'HIGH'),
('PERIZINAN', 'PERMIT_APPROVE', 'Permit Approved', 'Permit application approved', 'CRITICAL'),
('PERIZINAN', 'PERMIT_REJECT', 'Permit Rejected', 'Permit application rejected', 'HIGH'),
('PERIZINAN', 'PERMIT_RENEW', 'Permit Renewed', 'Existing permit renewed', 'HIGH'),
('PERIZINAN', 'PERMIT_EXPIRE', 'Permit Expired', 'Permit expired', 'CRITICAL'),
('PERIZINAN', 'DOCUMENT_UPLOAD', 'Document Uploaded', 'Supporting document uploaded', 'MEDIUM'),
('PERIZINAN', 'COMPLIANCE_CHECK', 'Compliance Check', 'Regulatory compliance check performed', 'HIGH'),

-- ADMIN Module
('ADMIN', 'USER_CREATE', 'User Created', 'New user account created', 'MEDIUM'),
('ADMIN', 'USER_DEACTIVATE', 'User Deactivated', 'User account deactivated', 'HIGH'),
('ADMIN', 'ROLE_ASSIGN', 'Role Assigned', 'Role assigned to user', 'HIGH'),
('ADMIN', 'PERMISSION_GRANT', 'Permission Granted', 'Permission granted to user/role', 'HIGH'),
('ADMIN', 'SYSTEM_BACKUP', 'System Backup', 'System backup performed', 'CRITICAL'),
('ADMIN', 'CONFIG_CHANGE', 'Configuration Changed', 'System configuration changed', 'HIGH'),

-- DASHBOARD & REPORTS
('DASHBOARD', 'DASHBOARD_VIEW', 'Dashboard Viewed', 'Dashboard page accessed', 'LOW'),
('REPORTS', 'REPORT_GENERATE', 'Report Generated', 'Business report generated', 'MEDIUM'),
('REPORTS', 'REPORT_EXPORT', 'Report Exported', 'Report exported to file', 'MEDIUM'),
('REPORTS', 'REPORT_SCHEDULE', 'Report Scheduled', 'Automatic report scheduled', 'LOW');

-- Create comprehensive views for all modules
CREATE VIEW `v_activity_log_comprehensive` AS
SELECT 
  sal.id,
  sal.module_name,
  sal.feature_name,
  sal.sub_feature,
  sal.table_name,
  sal.record_id,
  sal.action_type,
  sal.action_description,
  sal.business_impact,
  sal.compliance_relevant,
  sal.financial_impact,
  sal.workflow_stage,
  sal.is_critical,
  sal.created_at,
  
  -- User information
  u.username,
  CONCAT(u.first_name, ' ', u.last_name) as user_full_name,
  r.name as user_role,
  
  -- Device and context
  sal.device_type,
  sal.browser_name,
  sal.operating_system,
  sal.ip_address,
  
  -- All related references
  sal.related_kontrak_id,
  sal.related_spk_id,  
  sal.related_di_id,
  sal.related_purchase_order_id,
  sal.related_vendor_id,
  sal.related_customer_id,
  sal.related_invoice_id,
  sal.related_payment_id,
  sal.related_permit_id,
  sal.related_warehouse_id,
  
  -- Performance metrics
  sal.execution_time_ms,
  sal.memory_usage_mb,
  sal.query_count,
  
  -- Activity type details
  at.type_name as activity_type_name,
  at.description as activity_type_description,
  
  -- Change tracking
  JSON_LENGTH(sal.affected_fields) as fields_changed_count,
  sal.old_values,
  sal.new_values

FROM system_activity_log sal
LEFT JOIN users u ON sal.user_id = u.id
LEFT JOIN user_roles ur ON u.id = ur.user_id AND ur.is_active = 1
LEFT JOIN roles r ON ur.role_id = r.id AND r.is_active = 1
LEFT JOIN activity_types at ON sal.module_name = at.module_name AND sal.action_type = at.type_code
ORDER BY sal.created_at DESC;

-- Create module-specific views for performance
CREATE VIEW `v_purchasing_activity` AS
SELECT * FROM v_activity_log_comprehensive WHERE module_name = 'PURCHASING';

CREATE VIEW `v_warehouse_activity` AS  
SELECT * FROM v_activity_log_comprehensive WHERE module_name = 'WAREHOUSE';

CREATE VIEW `v_marketing_activity` AS
SELECT * FROM v_activity_log_comprehensive WHERE module_name = 'MARKETING';

CREATE VIEW `v_service_activity` AS
SELECT * FROM v_activity_log_comprehensive WHERE module_name = 'SERVICE';

CREATE VIEW `v_operational_activity` AS
SELECT * FROM v_activity_log_comprehensive WHERE module_name = 'OPERATIONAL';

CREATE VIEW `v_accounting_activity` AS
SELECT * FROM v_activity_log_comprehensive WHERE module_name = 'ACCOUNTING';

CREATE VIEW `v_perizinan_activity` AS
SELECT * FROM v_activity_log_comprehensive WHERE module_name = 'PERIZINAN';

-- Create analytics view for dashboard
CREATE VIEW `v_activity_analytics` AS
SELECT 
  module_name,
  feature_name,
  action_type,
  business_impact,
  COUNT(*) as activity_count,
  COUNT(DISTINCT user_id) as unique_users,
  AVG(execution_time_ms) as avg_execution_time,
  SUM(CASE WHEN compliance_relevant = 1 THEN 1 ELSE 0 END) as compliance_activities,
  SUM(CASE WHEN is_critical = 1 THEN 1 ELSE 0 END) as critical_activities,
  SUM(COALESCE(financial_impact, 0)) as total_financial_impact,
  MIN(created_at) as first_activity,
  MAX(created_at) as last_activity,
  DATE(created_at) as activity_date
FROM system_activity_log
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY module_name, feature_name, action_type, business_impact, DATE(created_at)
ORDER BY activity_count DESC;

-- Test the comprehensive system
SELECT 'Comprehensive System Ready' as status, 
       COUNT(*) as total_activity_types,
       COUNT(DISTINCT module_name) as total_modules
FROM activity_types;
