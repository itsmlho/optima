-- =====================================================
-- Notification System Enhancement: Add notification_style column
-- Migration: Add notification_style to notification_rules table
-- Date: 2026-03-13
-- =====================================================

-- Step 1: Add notification_style column to notification_rules table
ALTER TABLE notification_rules
ADD COLUMN notification_style ENUM('info_only', 'toast') DEFAULT 'info_only' AFTER priority;

-- Step 2: Add related columns for better notification management
ALTER TABLE notification_rules
ADD COLUMN creator_division VARCHAR(100) DEFAULT NULL AFTER notification_style,
ADD COLUMN notes TEXT DEFAULT NULL AFTER message_template;

-- Step 3: Update existing rules to use info_only style
-- Note: All existing rules will use info_only by default (no click/redirect)

-- =====================================================
-- Alternative: If you want to run as a complete fresh setup
-- =====================================================

-- DROP TABLE IF EXISTS notification_rules;

-- CREATE TABLE notification_rules (
--   id int NOT NULL,
--   name varchar(255) NOT NULL,
--   description text,
--   trigger_event varchar(100) NOT NULL COMMENT 'spk_created, spk_approved, di_processed, inventory_low, etc',
--   is_active tinyint(1) DEFAULT '1',
--   conditions longtext COMMENT 'JSON conditions like {"departemen": "DIESEL", "status": "APPROVED"}',
--   target_roles varchar(500) DEFAULT NULL COMMENT 'Comma-separated: head_service,staff_service,supervisor_service',
--   target_divisions varchar(500) DEFAULT NULL COMMENT 'Comma-separated: service,marketing,operational',
--   target_departments varchar(500) DEFAULT NULL COMMENT 'Comma-separated: DIESEL,ELECTRIC,LPG',
--   target_users varchar(500) DEFAULT NULL COMMENT 'Specific user IDs comma-separated',
--   exclude_creator tinyint(1) DEFAULT '0' COMMENT 'Exclude notification creator',
--   title_template varchar(500) NOT NULL COMMENT 'Template with variables like "SPK {{nomor_spk}} untuk {{department}}"',
--   message_template text NOT NULL,
--   category varchar(100) DEFAULT NULL,
--   type enum('info','success','warning','error','critical') DEFAULT 'info',
--   priority tinyint DEFAULT '1',
--   notification_style ENUM('info_only', 'toast') DEFAULT 'info_only' COMMENT 'info_only = no click, toast = popup alert',
--   creator_division varchar(100) DEFAULT NULL COMMENT 'Division that creates this notification',
--   notes TEXT DEFAULT NULL,
--   url_template varchar(500) DEFAULT NULL COMMENT 'URL template - NOT USED for info_only',
--   delay_minutes int DEFAULT '0' COMMENT 'Delay notification by X minutes',
--   expire_days int DEFAULT '30' COMMENT 'Auto-delete after X days',
--   created_by int DEFAULT NULL,
--   created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
--   updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
--   auto_include_superadmin tinyint(1) DEFAULT '1' COMMENT 'Automatically include superadmin in all notifications',
--   target_mixed longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'JSON array for complex multi-targeting: {divisions: [], roles: [], users: [], departments: []}',
--   rule_description text COMMENT 'Detailed description of when and why this rule triggers'
-- );
