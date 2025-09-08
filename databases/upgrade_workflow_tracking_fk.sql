-- Upgrade workflow_activity_log to use proper foreign keys for users/roles
-- This will ensure real data from users and roles tables

-- First, add user_id as proper foreign key (if not already exists)
ALTER TABLE `workflow_activity_log` 
ADD COLUMN `user_id` INT(11) NULL COMMENT 'FK to users table' AFTER `metadata`,
ADD INDEX `idx_user_id` (`user_id`),
ADD CONSTRAINT `fk_workflow_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Create enhanced view with real user and role data
DROP VIEW IF EXISTS `v_workflow_timeline_enhanced`;

CREATE VIEW `v_workflow_timeline_enhanced` AS
SELECT 
  wal.id,
  wal.entity_type,
  wal.entity_id,
  wal.activity_type,
  wal.activity_description,
  wal.workflow_stage,
  wal.reference_kontrak_id,
  wal.reference_spk_id,
  wal.reference_di_id,
  wal.reference_unit_id,
  wal.is_milestone,
  wal.created_at,
  
  -- Real user data from users table
  u.id as user_id,
  CONCAT(u.first_name, ' ', u.last_name) as user_full_name,
  u.username,
  u.position as user_position,
  u.employee_id,
  
  -- Real role data from roles table (get primary role)
  r.name as user_role_name,
  r.slug as user_role_slug,
  r.description as user_role_description,
  
  -- Additional context from related tables  
  k.no_po_marketing as kontrak_nomor,
  k.pelanggan as kontrak_pelanggan,
  sp.nomor_spk as spk_nomor,
  di.nomor_di as di_nomor,
  iu.no_unit as unit_nomor,
  
  -- Enhanced search capability
  CASE 
    WHEN wal.reference_kontrak_id IS NOT NULL THEN k.no_po_marketing
    WHEN wal.reference_spk_id IS NOT NULL THEN sp.nomor_spk
    WHEN wal.reference_di_id IS NOT NULL THEN di.nomor_di
    ELSE NULL
  END as searchable_number
  
FROM workflow_activity_log wal
LEFT JOIN users u ON wal.user_id = u.id
LEFT JOIN user_roles ur ON u.id = ur.user_id AND ur.is_active = 1
LEFT JOIN roles r ON ur.role_id = r.id AND r.is_active = 1
LEFT JOIN kontrak k ON wal.reference_kontrak_id = k.id
LEFT JOIN spk sp ON wal.reference_spk_id = sp.id  
LEFT JOIN delivery_instructions di ON wal.reference_di_id = di.id
LEFT JOIN inventory_unit iu ON wal.reference_unit_id = iu.id_inventory_unit
ORDER BY wal.created_at DESC;

-- Create view specifically for tracking search with enhanced user data
DROP VIEW IF EXISTS `v_tracking_search_enhanced`;

CREATE VIEW `v_tracking_search_enhanced` AS
SELECT 
  wal.*,
  CONCAT(u.first_name, ' ', u.last_name) as performer_name,
  u.position as performer_position,
  r.name as performer_role,
  k.no_po_marketing as nomor_po,
  sp.nomor_spk, 
  di.nomor_di,
  CASE 
    WHEN wal.reference_kontrak_id IS NOT NULL THEN k.no_po_marketing
    WHEN wal.reference_spk_id IS NOT NULL THEN sp.nomor_spk
    WHEN wal.reference_di_id IS NOT NULL THEN di.nomor_di
    ELSE NULL
  END as searchable_number
FROM workflow_activity_log wal
LEFT JOIN users u ON wal.user_id = u.id
LEFT JOIN user_roles ur ON u.id = ur.user_id AND ur.is_active = 1
LEFT JOIN roles r ON ur.role_id = r.id AND r.is_active = 1  
LEFT JOIN kontrak k ON wal.reference_kontrak_id = k.id
LEFT JOIN spk sp ON wal.reference_spk_id = sp.id
LEFT JOIN delivery_instructions di ON wal.reference_di_id = di.id
WHERE wal.is_milestone = TRUE
ORDER BY wal.created_at DESC;

-- Sample query to test the enhanced views
SELECT 
  'Enhanced Timeline Test' as test_type,
  COUNT(*) as total_records,
  COUNT(CASE WHEN user_full_name IS NOT NULL THEN 1 END) as records_with_user_data,
  COUNT(CASE WHEN user_role_name IS NOT NULL THEN 1 END) as records_with_role_data
FROM v_workflow_timeline_enhanced;
