-- Create enhanced views for workflow tracking with real user/role data
-- Skip FK constraint for now, focus on getting real data through views

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
  wal.user_id,
  CONCAT(u.first_name, ' ', u.last_name) as user_full_name,
  u.username,
  u.position as user_position,
  u.employee_id,
  
  -- Real role data from roles table (get primary role)
  r.name as user_role_name,
  r.slug as user_role_slug,
  r.description as user_role_description,
  
  -- Fallback to manual fields if FK data not available
  COALESCE(CONCAT(u.first_name, ' ', u.last_name), wal.user_name) as effective_user_name,
  COALESCE(r.name, wal.user_role) as effective_user_role,
  
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
  wal.id,
  wal.entity_type,
  wal.entity_id,
  wal.activity_type,
  wal.activity_description,
  wal.workflow_stage,
  wal.is_milestone,
  wal.created_at,
  
  -- Enhanced user information
  COALESCE(CONCAT(u.first_name, ' ', u.last_name), wal.user_name) as performer_name,
  COALESCE(u.position, 'Unknown Position') as performer_position,
  COALESCE(r.name, wal.user_role) as performer_role,
  
  -- Document numbers for search
  k.no_po_marketing as nomor_po,
  sp.nomor_spk, 
  di.nomor_di,
  CASE 
    WHEN wal.reference_kontrak_id IS NOT NULL THEN k.no_po_marketing
    WHEN wal.reference_spk_id IS NOT NULL THEN sp.nomor_spk
    WHEN wal.reference_di_id IS NOT NULL THEN di.nomor_di
    ELSE NULL
  END as searchable_number,
  
  -- Reference IDs
  wal.reference_kontrak_id,
  wal.reference_spk_id,
  wal.reference_di_id,
  wal.reference_unit_id
  
FROM workflow_activity_log wal
LEFT JOIN users u ON wal.user_id = u.id
LEFT JOIN user_roles ur ON u.id = ur.user_id AND ur.is_active = 1
LEFT JOIN roles r ON ur.role_id = r.id AND r.is_active = 1  
LEFT JOIN kontrak k ON wal.reference_kontrak_id = k.id
LEFT JOIN spk sp ON wal.reference_spk_id = sp.id
LEFT JOIN delivery_instructions di ON wal.reference_di_id = di.id
WHERE wal.is_milestone = TRUE
ORDER BY wal.created_at DESC;

-- Test the enhanced views
SELECT 
  'Enhanced Timeline Test' as test_type,
  COUNT(*) as total_records,
  COUNT(CASE WHEN user_full_name IS NOT NULL THEN 1 END) as records_with_real_user_data,
  COUNT(CASE WHEN effective_user_name IS NOT NULL THEN 1 END) as records_with_user_info,
  COUNT(CASE WHEN user_role_name IS NOT NULL THEN 1 END) as records_with_real_role_data,
  COUNT(CASE WHEN effective_user_role IS NOT NULL THEN 1 END) as records_with_role_info
FROM v_workflow_timeline_enhanced;
