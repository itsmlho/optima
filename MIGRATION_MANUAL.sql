-- ============================================
-- MANUAL MIGRATION SQL FOR SECURITY FEATURES
-- ============================================
-- Run these SQL queries manually in phpMyAdmin or MySQL client
-- if php spark migrate fails
-- ============================================

-- 1. Create user_otp table
CREATE TABLE IF NOT EXISTS `user_otp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `otp_code` varchar(6) NOT NULL,
  `email` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempts` int(11) NOT NULL DEFAULT 0,
  `max_attempts` int(11) NOT NULL DEFAULT 3,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `expires_at` datetime NOT NULL,
  `verified_at` datetime NULL DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_email` (`email`),
  KEY `idx_expires_at` (`expires_at`),
  KEY `idx_is_verified` (`is_verified`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Create login_attempts table
CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `identifier` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text,
  `attempts` int(11) NOT NULL DEFAULT 0,
  `last_attempt_at` datetime NOT NULL,
  `locked_until` datetime NULL DEFAULT NULL,
  `is_successful` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_identifier` (`identifier`),
  KEY `idx_ip_address` (`ip_address`),
  KEY `idx_locked_until` (`locked_until`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. Create user_sessions table
CREATE TABLE IF NOT EXISTS `user_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `session_id` varchar(128) NOT NULL,
  `device_id` varchar(64) NOT NULL,
  `device_name` varchar(255) NOT NULL,
  `device_type` enum('desktop','mobile','tablet') NOT NULL DEFAULT 'desktop',
  `browser` varchar(255) NOT NULL,
  `os` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_activity` datetime NOT NULL,
  `login_at` datetime NOT NULL,
  `logout_at` datetime NULL DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_session_id` (`session_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_device_id` (`device_id`),
  KEY `idx_is_active` (`is_active`),
  KEY `idx_last_activity` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 4. Create password_resets table
CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text,
  `attempts` int(11) NOT NULL DEFAULT 0,
  `max_attempts` int(11) NOT NULL DEFAULT 5,
  `is_used` tinyint(1) NOT NULL DEFAULT 0,
  `expires_at` datetime NOT NULL,
  `used_at` datetime NULL DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_token` (`token`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_email` (`email`),
  KEY `idx_expires_at` (`expires_at`),
  KEY `idx_is_used` (`is_used`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 5. Add OTP columns to users table
ALTER TABLE `users` 
ADD COLUMN IF NOT EXISTS `otp_enabled` tinyint(1) NOT NULL DEFAULT 0 AFTER `remember_token`,
ADD COLUMN IF NOT EXISTS `otp_enabled_at` datetime NULL DEFAULT NULL AFTER `otp_enabled`;

-- Add indexes if they don't exist
ALTER TABLE `users` 
ADD INDEX IF NOT EXISTS `idx_otp_enabled` (`otp_enabled`);

-- ============================================
-- VERIFICATION QUERIES
-- ============================================
-- Run these to verify all tables are created:

-- Check if tables exist
SELECT 
    TABLE_NAME as 'Table Name',
    TABLE_ROWS as 'Rows',
    CREATE_TIME as 'Created'
FROM 
    information_schema.TABLES 
WHERE 
    TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME IN ('user_otp', 'login_attempts', 'user_sessions', 'password_resets')
ORDER BY 
    TABLE_NAME;

-- Check if OTP columns exist in users table
SELECT 
    COLUMN_NAME as 'Column Name',
    COLUMN_TYPE as 'Type',
    IS_NULLABLE as 'Nullable',
    COLUMN_DEFAULT as 'Default'
FROM 
    information_schema.COLUMNS 
WHERE 
    TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'users' 
    AND COLUMN_NAME IN ('otp_enabled', 'otp_enabled_at');

-- ============================================
-- NOTES
-- ============================================
-- 1. Replace 'IF NOT EXISTS' and 'IF EXISTS' syntax if your MySQL version doesn't support it
-- 2. For older MySQL versions, check if tables/columns exist before creating/adding
-- 3. Make sure you're connected to the correct database (optima_ci)
-- 4. All timestamps are in DATETIME format (adjust timezone as needed)

