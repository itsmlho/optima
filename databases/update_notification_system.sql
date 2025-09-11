-- Safe update script for notification system
-- This script will only add missing tables and columns without dropping existing data

-- Add user_notification_preferences table if it doesn't exist
CREATE TABLE IF NOT EXISTS `user_notification_preferences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `email_enabled` tinyint(1) DEFAULT 1,
  `push_enabled` tinyint(1) DEFAULT 1,
  `sound_enabled` tinyint(1) DEFAULT 1,
  `email_frequency` enum('instant','daily','weekly') DEFAULT 'instant',
  `quiet_hours_start` time DEFAULT '22:00:00',
  `quiet_hours_end` time DEFAULT '08:00:00',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_prefs` (`user_id`),
  KEY `idx_user_prefs_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add missing columns to existing tables (safe operations)
-- Check and add columns to notifications table
SET @column_exists = 0;
SELECT COUNT(*) INTO @column_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'notifications' 
  AND COLUMN_NAME = 'rule_id';

SET @sql = IF(@column_exists = 0, 
  'ALTER TABLE notifications ADD COLUMN rule_id INT(11) NULL AFTER id, ADD KEY idx_notifications_rule_id (rule_id)',
  'SELECT "Column rule_id already exists"');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add priority column if missing
SET @column_exists = 0;
SELECT COUNT(*) INTO @column_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'notifications' 
  AND COLUMN_NAME = 'priority';

SET @sql = IF(@column_exists = 0, 
  'ALTER TABLE notifications ADD COLUMN priority ENUM(''low'', ''medium'', ''high'', ''urgent'') DEFAULT ''medium'' AFTER type',
  'SELECT "Column priority already exists"');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add metadata column if missing
SET @column_exists = 0;
SELECT COUNT(*) INTO @column_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'notifications' 
  AND COLUMN_NAME = 'metadata';

SET @sql = IF(@column_exists = 0, 
  'ALTER TABLE notifications ADD COLUMN metadata JSON NULL AFTER link',
  'SELECT "Column metadata already exists"');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Insert sample notification rules if they don't exist
INSERT IGNORE INTO notification_rules (
  name, 
  activity_type, 
  title_template, 
  message_template, 
  target_type, 
  target_values, 
  conditions, 
  priority, 
  is_active, 
  created_by,
  created_at
) VALUES 
('SPK DIESEL to Service DIESEL', 'spk_created', 
 'SPK Baru - {departemen} #{spk_id}', 
 'SPK baru telah dibuat untuk departemen {departemen}. Silakan review dan proses sesuai prosedur.',
 'department', '["diesel"]', 
 '{"target_division": "service", "source_department": "diesel"}', 
 'medium', 1, 1, NOW()),

('DI Processing Alert', 'di_created',
 'DI Baru Perlu Diproses - #{di_id}',
 'Delivery Instruction baru telah dibuat dan menunggu pemrosesan dari divisi yang bertanggung jawab.',
 'division', '["service"]',
 '{}',
 'high', 1, 1, NOW()),

('Low Stock Alert', 'inventory_low_stock',
 'Stok Rendah - {item_name}',
 'Item {item_name} memiliki stok rendah ({current_stock} tersisa). Segera lakukan reorder.',
 'role', '["admin", "manager"]',
 '{}',
 'high', 1, 1, NOW()),

('Maintenance Due Alert', 'maintenance_due',
 'Maintenance Terjadwal - Unit {unit_code}',
 'Unit {unit_code} memerlukan maintenance terjadwal. Silakan koordinasi dengan tim maintenance.',
 'division', '["service"]',
 '{}',
 'medium', 1, 1, NOW());

-- Create view for easy notification rule management
CREATE OR REPLACE VIEW notification_rule_summary AS
SELECT 
    nr.id,
    nr.name,
    nr.activity_type,
    nr.target_type,
    nr.target_values,
    nr.priority,
    nr.is_active,
    COUNT(nl.id) as usage_count,
    MAX(nl.created_at) as last_used,
    u.username as created_by_user
FROM notification_rules nr
LEFT JOIN notification_logs nl ON nr.id = nl.rule_id
LEFT JOIN users u ON nr.created_by = u.id
GROUP BY nr.id, nr.name, nr.activity_type, nr.target_type, nr.target_values, nr.priority, nr.is_active, u.username;

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_notifications_user_read ON notifications(user_id, is_read);
CREATE INDEX IF NOT EXISTS idx_notifications_created_at ON notifications(created_at);
CREATE INDEX IF NOT EXISTS idx_notification_recipients_user_status ON notification_recipients(user_id, status);
CREATE INDEX IF NOT EXISTS idx_notification_logs_rule_type ON notification_logs(rule_id, activity_type);

-- Add some helpful triggers for audit logging
DELIMITER $$

CREATE TRIGGER IF NOT EXISTS notification_rules_audit_insert
AFTER INSERT ON notification_rules
FOR EACH ROW
BEGIN
    INSERT INTO notification_logs (rule_id, activity_type, recipients_count, success, metadata, created_at)
    VALUES (NEW.id, 'rule_created', 0, 1, JSON_OBJECT('rule_name', NEW.name, 'created_by', NEW.created_by), NOW());
END$$

CREATE TRIGGER IF NOT EXISTS notification_rules_audit_update
AFTER UPDATE ON notification_rules
FOR EACH ROW
BEGIN
    IF OLD.is_active != NEW.is_active THEN
        INSERT INTO notification_logs (rule_id, activity_type, recipients_count, success, metadata, created_at)
        VALUES (NEW.id, 'rule_status_changed', 0, 1, 
                JSON_OBJECT('rule_name', NEW.name, 'old_status', OLD.is_active, 'new_status', NEW.is_active), NOW());
    END IF;
END$$

DELIMITER ;

SELECT 'Notification system updated successfully!' as status;
