-- =====================================================
-- COMPREHENSIVE NOTIFICATION SYSTEM WITH PERMISSION CONTROL
-- =====================================================
-- Date: 2025-09-10
-- Description: Smart notification system with role, division, department targeting

-- 1. Core Notifications Table
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','success','warning','error','critical') DEFAULT 'info',
  `category` varchar(100) DEFAULT NULL COMMENT 'spk, di, inventory, maintenance, etc',
  `icon` varchar(50) DEFAULT NULL,
  `related_table` varchar(100) DEFAULT NULL COMMENT 'Table reference like spk, delivery_instruction',
  `related_id` int(11) DEFAULT NULL COMMENT 'Record ID reference',
  `url` varchar(500) DEFAULT NULL COMMENT 'Action URL for notification',
  `priority` tinyint(4) DEFAULT 1 COMMENT '1=low, 2=medium, 3=high, 4=critical',
  `expires_at` datetime DEFAULT NULL COMMENT 'Auto-delete after this date',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category`),
  KEY `idx_related` (`related_table`, `related_id`),
  KEY `idx_priority` (`priority`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Notification Recipients (Who receives each notification)
DROP TABLE IF EXISTS `notification_recipients`;
CREATE TABLE `notification_recipients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `is_dismissed` tinyint(1) DEFAULT 0,
  `dismissed_at` timestamp NULL DEFAULT NULL,
  `delivery_method` enum('web','email','sms') DEFAULT 'web',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_notification_user` (`notification_id`, `user_id`),
  KEY `idx_user_unread` (`user_id`, `is_read`),
  KEY `idx_notification` (`notification_id`),
  FOREIGN KEY (`notification_id`) REFERENCES `notifications`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Notification Rules (Automatic targeting rules)
DROP TABLE IF EXISTS `notification_rules`;
CREATE TABLE `notification_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `trigger_event` varchar(100) NOT NULL COMMENT 'spk_created, spk_approved, di_processed, inventory_low, etc',
  `is_active` tinyint(1) DEFAULT 1,
  
  -- Conditions (JSON format for flexibility)
  `conditions` longtext DEFAULT NULL COMMENT 'JSON conditions like {"departemen": "DIESEL", "status": "APPROVED"}',
  
  -- Target Recipients Rules
  `target_roles` varchar(500) DEFAULT NULL COMMENT 'Comma-separated: superadmin,manager,supervisor',
  `target_divisions` varchar(500) DEFAULT NULL COMMENT 'Comma-separated: service,marketing,operational',
  `target_departments` varchar(500) DEFAULT NULL COMMENT 'Comma-separated: DIESEL,ELECTRIC,LPG',
  `target_users` varchar(500) DEFAULT NULL COMMENT 'Specific user IDs comma-separated',
  `exclude_creator` tinyint(1) DEFAULT 0 COMMENT 'Exclude notification creator',
  
  -- Notification Template
  `title_template` varchar(500) NOT NULL COMMENT 'Template with variables like "SPK {{nomor_spk}} untuk {{departemen}}"',
  `message_template` text NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `type` enum('info','success','warning','error','critical') DEFAULT 'info',
  `priority` tinyint(4) DEFAULT 1,
  `url_template` varchar(500) DEFAULT NULL COMMENT 'URL template with variables',
  
  -- Settings
  `delay_minutes` int(11) DEFAULT 0 COMMENT 'Delay notification by X minutes',
  `expire_days` int(11) DEFAULT 30 COMMENT 'Auto-delete after X days',
  
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_trigger_event` (`trigger_event`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Notification Logs (Audit trail)
DROP TABLE IF EXISTS `notification_logs`;
CREATE TABLE `notification_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notification_id` int(11) NOT NULL,
  `rule_id` int(11) DEFAULT NULL,
  `total_recipients` int(11) DEFAULT 0,
  `successful_deliveries` int(11) DEFAULT 0,
  `failed_deliveries` int(11) DEFAULT 0,
  `processing_time_ms` int(11) DEFAULT NULL,
  `trigger_data` longtext DEFAULT NULL COMMENT 'JSON data that triggered the notification',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_notification` (`notification_id`),
  KEY `idx_rule` (`rule_id`),
  FOREIGN KEY (`notification_id`) REFERENCES `notifications`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`rule_id`) REFERENCES `notification_rules`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. User Notification Preferences
DROP TABLE IF EXISTS `user_notification_preferences`;
CREATE TABLE `user_notification_preferences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `category` varchar(100) NOT NULL COMMENT 'spk, di, inventory, etc',
  `web_enabled` tinyint(1) DEFAULT 1,
  `email_enabled` tinyint(1) DEFAULT 0,
  `sms_enabled` tinyint(1) DEFAULT 0,
  `min_priority` tinyint(4) DEFAULT 1 COMMENT 'Minimum priority to receive',
  `quiet_hours_start` time DEFAULT NULL COMMENT 'No notifications during quiet hours',
  `quiet_hours_end` time DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_category` (`user_id`, `category`),
  KEY `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- SAMPLE NOTIFICATION RULES FOR COMMON WORKFLOWS
-- =====================================================

-- Rule 1: SPK Created - Notify Service Division
INSERT INTO `notification_rules` (
  `name`, `description`, `trigger_event`, 
  `conditions`, `target_divisions`, `target_roles`,
  `title_template`, `message_template`, `category`, `type`, `priority`,
  `url_template`, `exclude_creator`
) VALUES (
  'SPK Created - Service Notification',
  'Notify service division when new SPK is created',
  'spk_created',
  '{}',
  'service',
  'manager,supervisor,technician',
  'SPK Baru: {{nomor_spk}} - {{departemen}}',
  'SPK baru telah dibuat untuk {{pelanggan}} dengan departemen {{departemen}}. Silakan periksa dan proses sesuai prosedur.',
  'spk',
  'info',
  2,
  '/service/spk/detail/{{id}}',
  1
);

-- Rule 2: SPK DIESEL - Specific to DIESEL Department
INSERT INTO `notification_rules` (
  `name`, `description`, `trigger_event`,
  `conditions`, `target_divisions`, `target_departments`,
  `title_template`, `message_template`, `category`, `type`, `priority`,
  `url_template`
) VALUES (
  'SPK DIESEL - Service DIESEL Team',
  'Notify DIESEL service team for DIESEL SPK',
  'spk_created',
  '{"departemen": "DIESEL"}',
  'service',
  'DIESEL',
  'SPK DIESEL: {{nomor_spk}} - {{pelanggan}}',
  'SPK DIESEL baru memerlukan perhatian tim service DIESEL. Unit: {{unit_info}}, Lokasi: {{lokasi}}',
  'spk',
  'warning',
  3,
  '/service/spk/detail/{{id}}'
);

-- Rule 3: DI Ready for Operational
INSERT INTO `notification_rules` (
  `name`, `description`, `trigger_event`,
  `conditions`, `target_divisions`,
  `title_template`, `message_template`, `category`, `type`, `priority`,
  `url_template`
) VALUES (
  'DI Ready - Operational Team',
  'Notify operational when DI is ready for processing',
  'di_submitted',
  '{}',
  'operational',
  'DI Siap Diproses: {{nomor_di}}',
  'Delivery Instruction {{nomor_di}} untuk {{pelanggan}} siap diproses. Lokasi: {{lokasi}}',
  'di',
  'info',
  2,
  '/operational/delivery'
);

-- Rule 4: Inventory Low Stock
INSERT INTO `notification_rules` (
  `name`, `description`, `trigger_event`,
  `conditions`, `target_divisions`, `target_roles`,
  `title_template`, `message_template`, `category`, `type`, `priority`,
  `url_template`
) VALUES (
  'Low Stock Alert',
  'Notify warehouse managers when inventory is low',
  'inventory_low_stock',
  '{"stock_level": "below_minimum"}',
  'warehouse,purchasing',
  'manager,supervisor',
  'Stok Rendah: {{item_name}}',
  'Item {{item_name}} memiliki stok di bawah minimum. Stok saat ini: {{current_stock}}, Minimum: {{minimum_stock}}',
  'inventory',
  'warning',
  3,
  '/warehouse/inventory'
);

-- Rule 5: Maintenance Due
INSERT INTO `notification_rules` (
  `name`, `description`, `trigger_event`,
  `conditions`, `target_divisions`, `target_departments`,
  `title_template`, `message_template`, `category`, `type`, `priority`,
  `url_template`
) VALUES (
  'Maintenance Due Alert',
  'Notify service team when unit maintenance is due',
  'maintenance_due',
  '{}',
  'service',
  'DIESEL,ELECTRIC,LPG',
  'Maintenance Due: {{unit_no}}',
  'Unit {{unit_no}} memerlukan maintenance {{maintenance_type}}. Due date: {{due_date}}',
  'maintenance',
  'warning',
  3,
  '/service/maintenance'
);

-- =====================================================
-- HELPER VIEWS FOR EASY QUERYING
-- =====================================================

-- View: User Unread Notifications
CREATE OR REPLACE VIEW `v_user_notifications` AS
SELECT 
  n.id,
  n.title,
  n.message,
  n.type,
  n.category,
  n.icon,
  n.url,
  n.priority,
  n.created_at,
  nr.user_id,
  nr.is_read,
  nr.read_at,
  nr.is_dismissed,
  CASE 
    WHEN n.created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR) THEN 'just_now'
    WHEN n.created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY) THEN 'today'
    WHEN n.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 'this_week'
    ELSE 'older'
  END as time_category
FROM notifications n
JOIN notification_recipients nr ON n.id = nr.notification_id
WHERE (n.expires_at IS NULL OR n.expires_at > NOW())
  AND nr.is_dismissed = 0;

-- View: Notification Statistics
CREATE OR REPLACE VIEW `v_notification_stats` AS
SELECT 
  user_id,
  COUNT(*) as total_notifications,
  SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) as unread_count,
  SUM(CASE WHEN priority >= 3 THEN 1 ELSE 0 END) as high_priority_count,
  MAX(created_at) as latest_notification
FROM v_user_notifications
GROUP BY user_id;

-- =====================================================
-- INDEXES FOR PERFORMANCE
-- =====================================================
CREATE INDEX idx_notifications_category_type ON notifications(category, type);
CREATE INDEX idx_notifications_priority_created ON notifications(priority DESC, created_at DESC);
CREATE INDEX idx_recipients_user_read_created ON notification_recipients(user_id, is_read, created_at);
CREATE INDEX idx_rules_event_active ON notification_rules(trigger_event, is_active);

-- =====================================================
-- SAMPLE TEST DATA
-- =====================================================

-- Create a test notification
INSERT INTO `notifications` (
  `title`, `message`, `type`, `category`, `icon`,
  `related_table`, `related_id`, `url`, `priority`
) VALUES (
  'Test Notification',
  'This is a test notification to verify the system is working correctly.',
  'info',
  'system',
  'fas fa-bell',
  'test',
  1,
  '/dashboard',
  1
);

-- Assign to user 1 (assuming admin user exists)
INSERT INTO `notification_recipients` (`notification_id`, `user_id`) 
SELECT id, 1 FROM `notifications` WHERE title = 'Test Notification';

COMMIT;
