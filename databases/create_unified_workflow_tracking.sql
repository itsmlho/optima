-- Create unified workflow tracking table
-- This will replace multiple tracking tables with one centralized system

DROP TABLE IF EXISTS `workflow_activity_log`;

CREATE TABLE `workflow_activity_log` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `entity_type` ENUM('KONTRAK','SPK','DI','UNIT','USER') NOT NULL COMMENT 'Type of entity being tracked',
  `entity_id` INT UNSIGNED NOT NULL COMMENT 'ID of the entity',
  `activity_type` VARCHAR(50) NOT NULL COMMENT 'Type of activity (CREATED, UPDATED, ASSIGNED, etc)',
  `activity_description` TEXT NOT NULL COMMENT 'Human readable description of what happened',
  `old_values` JSON NULL COMMENT 'Previous values before change',
  `new_values` JSON NULL COMMENT 'New values after change', 
  `metadata` JSON NULL COMMENT 'Additional context data',
  `user_id` INT UNSIGNED NULL COMMENT 'User who performed the action',
  `user_name` VARCHAR(100) NULL COMMENT 'Name of user for faster queries',
  `user_role` VARCHAR(50) NULL COMMENT 'Role of user when action was performed',
  `ip_address` VARCHAR(45) NULL COMMENT 'IP address of user',
  `user_agent` TEXT NULL COMMENT 'Browser/device info',
  `workflow_stage` ENUM('KONTRAK','SPK_CREATED','UNIT_PREPARED','FABRICATION','PAINTING','PDI','DI_CREATED','DISPATCHED','DELIVERED','RETURNED','COMPLETED') NULL COMMENT 'Current stage in workflow',
  `status_before` VARCHAR(50) NULL COMMENT 'Status before this activity',
  `status_after` VARCHAR(50) NULL COMMENT 'Status after this activity',
  `reference_kontrak_id` INT UNSIGNED NULL COMMENT 'Related kontrak for cross-reference',
  `reference_spk_id` INT UNSIGNED NULL COMMENT 'Related SPK for cross-reference',
  `reference_di_id` INT UNSIGNED NULL COMMENT 'Related DI for cross-reference',
  `reference_unit_id` INT UNSIGNED NULL COMMENT 'Related unit for cross-reference',
  `is_milestone` BOOLEAN DEFAULT FALSE COMMENT 'Mark important workflow milestones',
  `estimated_date` DATETIME NULL COMMENT 'When this activity was estimated to complete',
  `actual_date` DATETIME NULL COMMENT 'When this activity actually completed',
  `delay_hours` INT NULL COMMENT 'Hours delayed (negative = early)',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  INDEX `idx_entity` (`entity_type`, `entity_id`),
  INDEX `idx_activity_type` (`activity_type`),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_workflow_stage` (`workflow_stage`),
  INDEX `idx_reference_kontrak` (`reference_kontrak_id`),
  INDEX `idx_reference_spk` (`reference_spk_id`),
  INDEX `idx_reference_di` (`reference_di_id`),
  INDEX `idx_reference_unit` (`reference_unit_id`),
  INDEX `idx_is_milestone` (`is_milestone`),
  INDEX `idx_created_at` (`created_at`)
);

-- Create function to automatically log workflow activities
DELIMITER $$

CREATE FUNCTION `log_workflow_activity`(
  p_entity_type VARCHAR(10),
  p_entity_id INT,
  p_activity_type VARCHAR(50),
  p_description TEXT,
  p_user_id INT,
  p_user_name VARCHAR(100),
  p_old_values JSON,
  p_new_values JSON,
  p_kontrak_id INT,
  p_spk_id INT,
  p_di_id INT,
  p_unit_id INT,
  p_workflow_stage VARCHAR(50),
  p_is_milestone BOOLEAN
) RETURNS INT
READS SQL DATA
DETERMINISTIC
BEGIN
  INSERT INTO workflow_activity_log (
    entity_type, entity_id, activity_type, activity_description,
    old_values, new_values, user_id, user_name,
    reference_kontrak_id, reference_spk_id, reference_di_id, reference_unit_id,
    workflow_stage, is_milestone
  ) VALUES (
    p_entity_type, p_entity_id, p_activity_type, p_description,
    p_old_values, p_new_values, p_user_id, p_user_name,
    p_kontrak_id, p_spk_id, p_di_id, p_unit_id,
    p_workflow_stage, p_is_milestone
  );
  
  RETURN LAST_INSERT_ID();
END$$

DELIMITER ;

-- Insert sample data for existing workflow activities
INSERT INTO workflow_activity_log (
  entity_type, entity_id, activity_type, activity_description,
  user_name, user_role, workflow_stage, is_milestone,
  reference_kontrak_id, reference_unit_id, created_at
) VALUES
('KONTRAK', 44, 'KONTRAK_CREATED', 'Kontrak baru dibuat dengan nomor PO-CL-0488', 'Admin', 'Marketing', 'KONTRAK', TRUE, 44, NULL, '2024-08-15 10:00:00'),
('UNIT', 1, 'UNIT_ASSIGNED', 'Unit forklift diassign ke kontrak PO-CL-0488 dengan harga Rp 9,000,000/bulan', 'Admin', 'Marketing', 'KONTRAK', TRUE, 44, 1, '2024-08-15 11:00:00'),
('UNIT', 2, 'UNIT_ASSIGNED', 'Unit forklift diassign ke kontrak PO-CL-0488 dengan harga Rp 9,000,000/bulan', 'Admin', 'Marketing', 'KONTRAK', TRUE, 44, 2, '2024-08-15 11:05:00');

-- Views for easy querying
CREATE VIEW `v_workflow_timeline` AS
SELECT 
  wal.id,
  wal.entity_type,
  wal.entity_id,
  wal.activity_type,
  wal.activity_description,
  wal.workflow_stage,
  wal.user_name,
  wal.user_role,
  wal.reference_kontrak_id,
  wal.reference_spk_id,
  wal.reference_di_id,
  wal.reference_unit_id,
  wal.is_milestone,
  wal.created_at,
  
  -- Additional context from related tables
  k.nomor_po as kontrak_nomor,
  k.pelanggan as kontrak_pelanggan,
  sp.nomor_spk as spk_nomor,
  di.nomor_di as di_nomor,
  iu.nomor_unit as unit_nomor
  
FROM workflow_activity_log wal
LEFT JOIN kontrak k ON wal.reference_kontrak_id = k.id
LEFT JOIN spk_service sp ON wal.reference_spk_id = sp.id  
LEFT JOIN delivery_instructions di ON wal.reference_di_id = di.id
LEFT JOIN inventory_unit iu ON wal.reference_unit_id = iu.id_inventory_unit
ORDER BY wal.created_at DESC;

-- View for tracking search specifically
CREATE VIEW `v_tracking_search` AS
SELECT 
  wal.*,
  k.nomor_po,
  sp.nomor_spk, 
  di.nomor_di,
  CASE 
    WHEN wal.reference_kontrak_id IS NOT NULL THEN k.nomor_po
    WHEN wal.reference_spk_id IS NOT NULL THEN sp.nomor_spk
    WHEN wal.reference_di_id IS NOT NULL THEN di.nomor_di
    ELSE NULL
  END as searchable_number
FROM workflow_activity_log wal
LEFT JOIN kontrak k ON wal.reference_kontrak_id = k.id
LEFT JOIN spk_service sp ON wal.reference_spk_id = sp.id
LEFT JOIN delivery_instructions di ON wal.reference_di_id = di.id
WHERE wal.is_milestone = TRUE
ORDER BY wal.created_at DESC;
