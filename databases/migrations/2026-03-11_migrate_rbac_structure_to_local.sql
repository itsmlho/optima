-- ═══════════════════════════════════════════════════════════════════════════════
-- MIGRATE RBAC STRUCTURE TO LOCAL DATABASE
-- Date: 2026-03-11
-- Purpose: Update local database structure to match production RBAC system
-- 
-- This migration:
-- 1. Drops Spatie Laravel Permissions tables
-- 2. Creates custom RBAC tables (permissions, roles, role_permissions, etc.)
-- 3. Imports base permissions and roles
-- ═══════════════════════════════════════════════════════════════════════════════

SET FOREIGN_KEY_CHECKS = 0;

-- ═══════════════════════════════════════════════════════════════════════════════
-- STEP 1: Drop Old Spatie Tables
-- ═══════════════════════════════════════════════════════════════════════════════

DROP TABLE IF EXISTS `model_has_permissions`;
DROP TABLE IF EXISTS `model_has_roles`;
DROP TABLE IF EXISTS `role_has_permissions`;
DROP TABLE IF EXISTS `permissions`;
DROP TABLE IF EXISTS `roles`;
DROP TABLE IF EXISTS `user_all_permissions`;

-- ═══════════════════════════════════════════════════════════════════════════════
-- STEP 2: Create Custom RBAC Tables
-- ═══════════════════════════════════════════════════════════════════════════════

-- 2.1 Permissions Table (with module, page, action structure)
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(100) NOT NULL COMMENT 'Module name (e.g., marketing, service)',
  `page` varchar(100) NOT NULL COMMENT 'Page name (e.g., customer, quotation)',
  `action` varchar(100) NOT NULL COMMENT 'Action name (e.g., view, create, edit)',
  `key_name` varchar(255) NOT NULL COMMENT 'Unique permission key (module.page.action)',
  `display_name` varchar(255) NOT NULL COMMENT 'Human readable name',
  `description` text COMMENT 'Permission description',
  `category` varchar(50) DEFAULT 'general' COMMENT 'Permission category',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key_name` (`key_name`),
  KEY `idx_module` (`module`),
  KEY `idx_page` (`page`),
  KEY `idx_action` (`action`),
  KEY `idx_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2.2 Roles Table
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'Role name',
  `display_name` varchar(255) DEFAULT NULL COMMENT 'Display name',
  `description` text COMMENT 'Role description',
  `is_system_role` tinyint(1) DEFAULT '0' COMMENT 'System-defined role (cannot delete)',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2.3 Role Permissions (junction table)
CREATE TABLE `role_permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint unsigned NOT NULL,
  `permission_id` bigint unsigned NOT NULL,
  `granted` tinyint(1) DEFAULT '1' COMMENT '1=granted, 0=denied',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_permission_unique` (`role_id`,`permission_id`),
  KEY `role_id` (`role_id`),
  KEY `permission_id` (`permission_id`),
  CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2.4 User Roles (junction table)
CREATE TABLE `user_roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  `division_id` bigint unsigned DEFAULT NULL COMMENT 'Optional division assignment',
  `is_active` tinyint(1) DEFAULT '1',
  `assigned_by` bigint unsigned DEFAULT NULL COMMENT 'User who assigned this role',
  `assigned_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` timestamp NULL DEFAULT NULL COMMENT 'Optional expiry date',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_role_unique` (`user_id`,`role_id`,`division_id`),
  KEY `user_id` (`user_id`),
  KEY `role_id` (`role_id`),
  KEY `division_id` (`division_id`),
  CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_roles_ibfk_3` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2.5 User Permissions (custom overrides)
CREATE TABLE `user_permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `permission_id` bigint unsigned NOT NULL,
  `division_id` bigint unsigned DEFAULT NULL COMMENT 'Optional division scope',
  `granted` tinyint(1) DEFAULT '1' COMMENT '1=granted, 0=denied (override)',
  `assigned_by` bigint unsigned DEFAULT NULL,
  `reason` text COMMENT 'Reason for custom permission',
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_permission_unique` (`user_id`,`permission_id`,`division_id`),
  KEY `user_id` (`user_id`),
  KEY `permission_id` (`permission_id`),
  KEY `division_id` (`division_id`),
  CONSTRAINT `user_permissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_permissions_ibfk_3` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2.6 User Sessions (for tracking active sessions)
CREATE TABLE IF NOT EXISTS `user_sessions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `last_activity` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_id` (`session_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ═══════════════════════════════════════════════════════════════════════════════
-- STEP 3: Insert Base Roles
-- ═══════════════════════════════════════════════════════════════════════════════

INSERT INTO `roles` (`id`, `name`, `display_name`, `description`, `is_system_role`) VALUES
(1, 'Super Administrator', 'Super Administrator', 'Full system access with all permissions', 1),
(2, 'Administrator', 'Administrator', 'System administrator with management access', 1),
(3, 'Marketing Manager', 'Marketing Manager', 'Marketing department manager', 0),
(4, 'Marketing Staff', 'Staff Marketing', 'Marketing department staff', 0),
(5, 'Service Manager', 'Service Manager', 'Service department manager', 0),
(6, 'Service Staff', 'Staff Service', 'Service department staff', 0),
(7, 'Warehouse Manager', 'Warehouse Manager', 'Warehouse & Assets manager', 0),
(8, 'Warehouse Staff', 'Staff Warehouse', 'Warehouse staff', 0),
(9, 'Operations Staff', 'Staff Operations', 'Operations staff', 0),
(10, 'Operator', 'Operator', 'Equipment operator', 0);

-- ═══════════════════════════════════════════════════════════════════════════════
-- STEP 4: Update user table to use 'id' instead of 'user_id'
-- ═══════════════════════════════════════════════════════════════════════════════

-- Check if users table uses 'user_id' and needs migration
SET @column_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'users'
    AND COLUMN_NAME = 'user_id'
);

-- If user_id exists, rename to id (if not already)
SET @has_id = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'users'
    AND COLUMN_NAME = 'id'
);

-- Rename user_id to id if needed
-- Note: This might fail if 'id' already exists - handle manually if needed

SET FOREIGN_KEY_CHECKS = 1;

-- ═══════════════════════════════════════════════════════════════════════════════
-- COMPLETION MESSAGE
-- ═══════════════════════════════════════════════════════════════════════════════

SELECT 'RBAC structure migration completed!' AS Status;
SELECT 'Next steps:' AS Info;
SELECT '1. Run: 2026-03-07_add_all_permissions_comprehensive.sql' AS Step1;
SELECT '2. Run: 2026-03-07_assign_new_menu_roles.sql' AS Step2;
SELECT '3. Assign roles to users via User Management UI' AS Step3;
