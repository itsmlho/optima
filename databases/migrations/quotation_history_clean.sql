-- ============================================================
-- QUOTATION HISTORY TABLE - Clean Migration
-- Only creates missing table quotation_history
-- ============================================================

-- Drop existing if any (cleanup)
DROP TABLE IF EXISTS `quotation_history`;
DROP VIEW IF EXISTS `vw_quotation_history_detail`;

-- Create quotation history table
CREATE TABLE `quotation_history` (
  `id_history` INT NOT NULL AUTO_INCREMENT,
  `quotation_id` INT NOT NULL COMMENT 'Reference to quotation',
  `version` INT NOT NULL COMMENT 'Version at time of change',
  `action_type` ENUM('CREATED', 'UPDATED', 'SENT', 'REVISED', 'APPROVED', 'REJECTED', 'DELETED') NOT NULL COMMENT 'Type of action performed',
  `changed_by` INT NULL COMMENT 'User ID who made the change',
  `changed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp of change',
  `changes_summary` TEXT NULL COMMENT 'Human-readable summary of changes',
  `old_values` JSON NULL COMMENT 'Previous values before change',
  `new_values` JSON NULL COMMENT 'New values after change',
  `ip_address` VARCHAR(45) NULL COMMENT 'IP address of user making change',
  `user_agent` VARCHAR(255) NULL COMMENT 'Browser/device info',
  PRIMARY KEY (`id_history`),
  INDEX `idx_quotation_id` (`quotation_id`),
  INDEX `idx_changed_by` (`changed_by`),
  INDEX `idx_changed_at` (`changed_at`),
  INDEX `idx_action_type` (`action_type`),
  CONSTRAINT `fk_quotation_history_quotation` 
    FOREIGN KEY (`quotation_id`) 
    REFERENCES `quotations` (`id_quotation`) 
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_quotation_history_user` 
    FOREIGN KEY (`changed_by`) 
    REFERENCES `users` (`id`) 
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Audit trail for all quotation changes';

-- Create view for quotation history with user details
CREATE VIEW `vw_quotation_history_detail` AS
SELECT 
    qh.id_history,
    qh.quotation_id,
    q.quotation_number,
    qh.version,
    qh.action_type,
    qh.changed_at,
    qh.changes_summary,
    qh.old_values,
    qh.new_values,
    qh.ip_address,
    u.id as changed_by_id,
    u.username as changed_by_username,
    u.email as changed_by_email,
    CONCAT(u.first_name, ' ', u.last_name) as changed_by_name
FROM quotation_history qh
LEFT JOIN quotations q ON qh.quotation_id = q.id_quotation
LEFT JOIN users u ON qh.changed_by = u.id
ORDER BY qh.changed_at DESC;

-- Insert initial history records for existing quotations
INSERT INTO quotation_history (
    quotation_id,
    version,
    action_type,
    changed_by,
    changed_at,
    changes_summary,
    old_values,
    new_values
)
SELECT 
    id_quotation,
    COALESCE(version, 1),
    'CREATED',
    created_by,
    created_at,
    'Initial quotation creation (migrated from existing data)',
    NULL,
    JSON_OBJECT(
        'quotation_number', quotation_number,
        'total_amount', total_amount,
        'valid_until', valid_until,
        'workflow_stage', workflow_stage
    )
FROM quotations;

-- Success message
SELECT 'Migration completed successfully!' as status;
