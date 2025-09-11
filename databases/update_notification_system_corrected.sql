-- Safe update script for notification system (corrected)
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

-- Add metadata column if missing (after url since link doesn't exist)
SET @column_exists = 0;
SELECT COUNT(*) INTO @column_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'notifications' 
  AND COLUMN_NAME = 'metadata';

SET @sql = IF(@column_exists = 0, 
  'ALTER TABLE notifications ADD COLUMN metadata JSON NULL AFTER url',
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
CREATE INDEX IF NOT EXISTS idx_notifications_user_read ON notifications(created_by);
CREATE INDEX IF NOT EXISTS idx_notifications_created_at ON notifications(created_at);
CREATE INDEX IF NOT EXISTS idx_notification_recipients_user_status ON notification_recipients(user_id, status);
CREATE INDEX IF NOT EXISTS idx_notification_logs_rule_type ON notification_logs(rule_id, activity_type);

SELECT 'Notification system updated successfully!' as status;
