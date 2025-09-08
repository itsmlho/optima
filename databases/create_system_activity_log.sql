-- Create Comprehensive System Activity Log
-- Efficient, Clean, and Fast logging for ALL system activities
-- No redundancy, FK-based, normalized design

DROP TABLE IF EXISTS `system_activity_log`;

CREATE TABLE `system_activity_log` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  
  -- Core Activity Information
  `table_name` VARCHAR(64) NOT NULL COMMENT 'Target table name (kontrak, spk, inventory_unit, etc)',
  `record_id` INT UNSIGNED NOT NULL COMMENT 'ID of the affected record',
  `action_type` ENUM('CREATE','UPDATE','DELETE','ASSIGN','UNASSIGN','APPROVE','REJECT','COMPLETE','CANCEL') NOT NULL COMMENT 'Type of action performed',
  `action_description` VARCHAR(255) NOT NULL COMMENT 'Brief description of what happened',
  
  -- Change Tracking (JSON for efficiency)
  `old_values` JSON NULL COMMENT 'Previous values (only changed fields)',
  `new_values` JSON NULL COMMENT 'New values (only changed fields)',
  `affected_fields` JSON NULL COMMENT 'List of fields that were changed',
  
  -- User Context (FK only - no redundant data)
  `user_id` INT UNSIGNED NULL COMMENT 'FK to users.id',
  `session_id` VARCHAR(128) NULL COMMENT 'Session identifier for tracking',
  
  -- System Context
  `ip_address` VARCHAR(45) NULL COMMENT 'User IP address',
  `user_agent` VARCHAR(500) NULL COMMENT 'Browser/device info (truncated)',
  `request_method` ENUM('GET','POST','PUT','DELETE','PATCH') NULL COMMENT 'HTTP method used',
  `request_url` VARCHAR(255) NULL COMMENT 'Endpoint that triggered this action',
  
  -- Business Context (Optional FKs for cross-reference)
  `related_kontrak_id` INT UNSIGNED NULL COMMENT 'Related kontrak if applicable',
  `related_spk_id` INT UNSIGNED NULL COMMENT 'Related SPK if applicable', 
  `related_di_id` INT UNSIGNED NULL COMMENT 'Related DI if applicable',
  
  -- Workflow & Business Logic
  `workflow_stage` VARCHAR(50) NULL COMMENT 'Current business stage',
  `is_critical` BOOLEAN DEFAULT FALSE COMMENT 'Mark critical business actions',
  `requires_approval` BOOLEAN DEFAULT FALSE COMMENT 'Action needs approval',
  `approval_status` ENUM('PENDING','APPROVED','REJECTED') NULL COMMENT 'If approval required',
  
  -- Performance & Audit
  `execution_time_ms` INT UNSIGNED NULL COMMENT 'Time taken to execute action (milliseconds)',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  -- Indexes for maximum performance
  INDEX `idx_table_record` (`table_name`, `record_id`),
  INDEX `idx_action_type` (`action_type`),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_created_at` (`created_at`),
  INDEX `idx_workflow_stage` (`workflow_stage`),
  INDEX `idx_is_critical` (`is_critical`),
  INDEX `idx_related_kontrak` (`related_kontrak_id`),
  INDEX `idx_related_spk` (`related_spk_id`),
  INDEX `idx_related_di` (`related_di_id`),
  INDEX `idx_approval_status` (`approval_status`),
  
  -- Composite indexes for common queries
  INDEX `idx_user_date` (`user_id`, `created_at`),
  INDEX `idx_table_action_date` (`table_name`, `action_type`, `created_at`)
);

-- Create efficient views for common use cases
CREATE VIEW `v_activity_log_with_user` AS
SELECT 
  sal.id,
  sal.table_name,
  sal.record_id,
  sal.action_type,
  sal.action_description,
  sal.workflow_stage,
  sal.is_critical,
  sal.created_at,
  
  -- User info via FK (efficient join)
  u.username,
  CONCAT(u.first_name, ' ', u.last_name) as user_full_name,
  u.email as user_email,
  
  -- Primary role via FK
  r.name as user_role,
  r.slug as role_slug,
  
  -- Related document numbers (when needed)
  k.no_po_marketing as kontrak_nomor,
  sp.nomor_spk,
  di.nomor_di,
  
  -- Change summary
  JSON_LENGTH(sal.affected_fields) as fields_changed,
  sal.old_values,
  sal.new_values
  
FROM system_activity_log sal
LEFT JOIN users u ON sal.user_id = u.id
LEFT JOIN user_roles ur ON u.id = ur.user_id AND ur.is_active = 1
LEFT JOIN roles r ON ur.role_id = r.id AND r.is_active = 1
LEFT JOIN kontrak k ON sal.related_kontrak_id = k.id
LEFT JOIN spk sp ON sal.related_spk_id = sp.id
LEFT JOIN delivery_instructions di ON sal.related_di_id = di.id
ORDER BY sal.created_at DESC;

-- View for tracking specific workflow
CREATE VIEW `v_workflow_tracking` AS
SELECT 
  sal.id,
  sal.table_name,
  sal.record_id,
  sal.action_type,
  sal.action_description,
  sal.workflow_stage,
  sal.created_at,
  
  -- User info
  u.username,
  CONCAT(u.first_name, ' ', u.last_name) as performer,
  
  -- Document references
  CASE 
    WHEN sal.related_kontrak_id IS NOT NULL THEN k.no_po_marketing
    WHEN sal.related_spk_id IS NOT NULL THEN sp.nomor_spk
    WHEN sal.related_di_id IS NOT NULL THEN di.nomor_di
    ELSE CONCAT(sal.table_name, '#', sal.record_id)
  END as document_reference,
  
  -- Business context
  sal.related_kontrak_id,
  sal.related_spk_id,
  sal.related_di_id
  
FROM system_activity_log sal
LEFT JOIN users u ON sal.user_id = u.id
LEFT JOIN kontrak k ON sal.related_kontrak_id = k.id
LEFT JOIN spk sp ON sal.related_spk_id = sp.id
LEFT JOIN delivery_instructions di ON sal.related_di_id = di.id
WHERE sal.is_critical = TRUE OR sal.workflow_stage IS NOT NULL
ORDER BY sal.created_at DESC;

-- View for audit trail by table
CREATE VIEW `v_audit_trail_by_table` AS
SELECT 
  sal.table_name,
  sal.record_id,
  COUNT(*) as total_activities,
  COUNT(CASE WHEN sal.action_type = 'CREATE' THEN 1 END) as creates,
  COUNT(CASE WHEN sal.action_type = 'UPDATE' THEN 1 END) as updates,
  COUNT(CASE WHEN sal.action_type = 'DELETE' THEN 1 END) as deletes,
  COUNT(DISTINCT sal.user_id) as unique_users,
  MIN(sal.created_at) as first_activity,
  MAX(sal.created_at) as last_activity,
  
  -- Latest activity info
  (SELECT CONCAT(u2.first_name, ' ', u2.last_name) 
   FROM system_activity_log sal2 
   LEFT JOIN users u2 ON sal2.user_id = u2.id 
   WHERE sal2.table_name = sal.table_name AND sal2.record_id = sal.record_id 
   ORDER BY sal2.created_at DESC LIMIT 1) as last_updated_by
   
FROM system_activity_log sal
GROUP BY sal.table_name, sal.record_id
ORDER BY last_activity DESC;

-- Insert sample data to test the system
INSERT INTO system_activity_log (
  table_name, record_id, action_type, action_description,
  user_id, workflow_stage, is_critical,
  related_kontrak_id, affected_fields, new_values
) VALUES
-- Kontrak activities
('kontrak', 44, 'CREATE', 'Kontrak baru dibuat dengan nomor PO-CL-0488', 1, 'KONTRAK', TRUE, 44, 
 JSON_ARRAY('no_po_marketing', 'pelanggan', 'status'), 
 JSON_OBJECT('no_po_marketing', 'PO-CL-0488', 'pelanggan', 'PT Client', 'status', 'ACTIVE')),

-- Unit assignment activities  
('inventory_unit', 1, 'ASSIGN', 'Unit forklift diassign ke kontrak dengan harga Rp 9,000,000/bulan', 1, 'KONTRAK', TRUE, 44,
 JSON_ARRAY('kontrak_id', 'harga_sewa_bulanan', 'status_unit_id'),
 JSON_OBJECT('kontrak_id', 44, 'harga_sewa_bulanan', 9000000, 'status_unit_id', 3)),

('inventory_unit', 2, 'ASSIGN', 'Unit forklift diassign ke kontrak dengan harga Rp 9,000,000/bulan', 1, 'KONTRAK', TRUE, 44,
 JSON_ARRAY('kontrak_id', 'harga_sewa_bulanan', 'status_unit_id'), 
 JSON_OBJECT('kontrak_id', 44, 'harga_sewa_bulanan', 9000000, 'status_unit_id', 3));

-- Test queries
SELECT 'Basic Log Test' as test_name, COUNT(*) as total_records FROM system_activity_log;
SELECT 'User Join Test' as test_name, COUNT(*) as records_with_user FROM v_activity_log_with_user WHERE username IS NOT NULL;
SELECT 'Workflow Test' as test_name, COUNT(*) as workflow_records FROM v_workflow_tracking;
