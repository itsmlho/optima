-- Final safe update script for notification system
-- This script will only add missing tables and work with existing structure

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

-- Add metadata column to notifications if missing
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

-- Insert sample notification rules using existing schema
INSERT IGNORE INTO notification_rules (
  name, 
  description,
  trigger_event, 
  title_template, 
  message_template, 
  target_divisions, 
  target_departments,
  conditions, 
  priority, 
  is_active, 
  created_by,
  created_at
) VALUES 
('SPK DIESEL to Service DIESEL', 
 'Notifikasi untuk SPK departemen DIESEL ke divisi Service',
 'spk_created', 
 'SPK Baru - {departemen} #{spk_id}', 
 'SPK baru telah dibuat untuk departemen {departemen}. Silakan review dan proses sesuai prosedur.',
 'service', 'diesel',
 '{"source_department": "diesel", "target_division": "service"}', 
 2, 1, 1, NOW()),

('DI Processing Alert',
 'Alert untuk DI yang perlu diproses',
 'di_created',
 'DI Baru Perlu Diproses - #{di_id}',
 'Delivery Instruction baru telah dibuat dan menunggu pemrosesan dari divisi yang bertanggung jawab.',
 'service', '',
 '{}',
 3, 1, 1, NOW()),

('Low Stock Alert',
 'Alert untuk stok rendah',
 'inventory_low_stock',
 'Stok Rendah - {item_name}',
 'Item {item_name} memiliki stok rendah ({current_stock} tersisa). Segera lakukan reorder.',
 '', '',
 '{}',
 3, 1, 1, NOW()),

('Maintenance Due Alert',
 'Alert untuk maintenance yang jatuh tempo',
 'maintenance_due',
 'Maintenance Terjadwal - Unit {unit_code}',
 'Unit {unit_code} memerlukan maintenance terjadwal. Silakan koordinasi dengan tim maintenance.',
 'service', '',
 '{}',
 2, 1, 1, NOW());

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_notifications_created_by ON notifications(created_by);
CREATE INDEX IF NOT EXISTS idx_notifications_created_at ON notifications(created_at);
CREATE INDEX IF NOT EXISTS idx_notification_recipients_user_status ON notification_recipients(user_id, status);

-- Check what notification rules exist now
SELECT 'Notification system updated successfully!' as status;
SELECT COUNT(*) as 'Total Rules', COUNT(CASE WHEN is_active = 1 THEN 1 END) as 'Active Rules' FROM notification_rules;
