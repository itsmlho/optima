-- Work Orders Database Schema
-- Created: September 23, 2025
-- This schema supports comprehensive work order management with normalized structure

-- ========================================
-- 1. Categories for work orders
-- ========================================

-- Main categories for work order classification
DROP TABLE IF EXISTS `work_order_categories`;
CREATE TABLE `work_order_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) NOT NULL,
  `category_code` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT current_timestamp(),
  `updated_at` timestamp DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_category_code` (`category_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Sub-categories for detailed classification
DROP TABLE IF EXISTS `work_order_subcategories`;
CREATE TABLE `work_order_subcategories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `subcategory_name` varchar(100) NOT NULL,
  `subcategory_code` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT current_timestamp(),
  `updated_at` timestamp DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_subcategory_code` (`subcategory_code`),
  KEY `fk_subcategory_category` (`category_id`),
  CONSTRAINT `fk_subcategory_category` FOREIGN KEY (`category_id`) REFERENCES `work_order_categories` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ========================================
-- 2. Work Order Status Management
-- ========================================

DROP TABLE IF EXISTS `work_order_statuses`;
CREATE TABLE `work_order_statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status_name` varchar(50) NOT NULL,
  `status_code` varchar(20) NOT NULL,
  `status_color` varchar(20) DEFAULT 'secondary',
  `description` text DEFAULT NULL,
  `is_final_status` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_status_code` (`status_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ========================================
-- 3. Work Order Priority Levels
-- ========================================

DROP TABLE IF EXISTS `work_order_priorities`;
CREATE TABLE `work_order_priorities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `priority_name` varchar(50) NOT NULL,
  `priority_code` varchar(20) NOT NULL,
  `priority_level` int(11) NOT NULL, -- Higher number = higher priority
  `priority_color` varchar(20) DEFAULT 'info',
  `description` text DEFAULT NULL,
  `sla_hours` int(11) DEFAULT NULL, -- Service Level Agreement in hours
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_priority_code` (`priority_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ========================================
-- 4. Staff Roles for Work Orders
-- ========================================

DROP TABLE IF EXISTS `work_order_staff`;
CREATE TABLE `work_order_staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_name` varchar(100) NOT NULL,
  `staff_role` enum('ADMIN','FOREMAN','MECHANIC','HELPER') NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT current_timestamp(),
  `updated_at` timestamp DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_staff_role` (`staff_role`),
  KEY `idx_staff_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ========================================
-- 5. Main Work Orders Table
-- ========================================

DROP TABLE IF EXISTS `work_orders`;
CREATE TABLE `work_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `work_order_number` varchar(50) NOT NULL,
  `report_date` datetime NOT NULL DEFAULT current_timestamp(),
  `unit_id` int(10) UNSIGNED NOT NULL,
  `order_type` enum('COMPLAINT','PMPS','FABRIKASI','PERSIAPAN') NOT NULL DEFAULT 'COMPLAINT',
  `priority_id` int(11) NOT NULL,
  `requested_repair_time` datetime DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `subcategory_id` int(11) DEFAULT NULL,
  `complaint_description` text NOT NULL,
  `status_id` int(11) NOT NULL,
  `admin_staff_id` int(11) DEFAULT NULL,
  `foreman_staff_id` int(11) DEFAULT NULL,
  `mechanic_staff_id` int(11) DEFAULT NULL,
  `helper_staff_id` int(11) DEFAULT NULL,
  `repair_description` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `sparepart_used` text DEFAULT NULL,
  `time_to_repair` decimal(5,2) DEFAULT NULL, -- in hours
  `completion_date` datetime DEFAULT NULL,
  `area` varchar(100) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp DEFAULT current_timestamp(),
  `updated_at` timestamp DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_work_order_number` (`work_order_number`),
  KEY `fk_wo_unit` (`unit_id`),
  KEY `fk_wo_priority` (`priority_id`),
  KEY `fk_wo_category` (`category_id`),
  KEY `fk_wo_subcategory` (`subcategory_id`),
  KEY `fk_wo_status` (`status_id`),
  KEY `fk_wo_admin_staff` (`admin_staff_id`),
  KEY `fk_wo_foreman_staff` (`foreman_staff_id`),
  KEY `fk_wo_mechanic_staff` (`mechanic_staff_id`),
  KEY `fk_wo_helper_staff` (`helper_staff_id`),
  KEY `fk_wo_created_by` (`created_by`),
  KEY `idx_wo_report_date` (`report_date`),
  KEY `idx_wo_order_type` (`order_type`),
  CONSTRAINT `fk_wo_unit` FOREIGN KEY (`unit_id`) REFERENCES `inventory_unit` (`id_inventory_unit`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_wo_priority` FOREIGN KEY (`priority_id`) REFERENCES `work_order_priorities` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_wo_category` FOREIGN KEY (`category_id`) REFERENCES `work_order_categories` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_wo_subcategory` FOREIGN KEY (`subcategory_id`) REFERENCES `work_order_subcategories` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_wo_status` FOREIGN KEY (`status_id`) REFERENCES `work_order_statuses` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_wo_admin_staff` FOREIGN KEY (`admin_staff_id`) REFERENCES `work_order_staff` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_wo_foreman_staff` FOREIGN KEY (`foreman_staff_id`) REFERENCES `work_order_staff` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_wo_mechanic_staff` FOREIGN KEY (`mechanic_staff_id`) REFERENCES `work_order_staff` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_wo_helper_staff` FOREIGN KEY (`helper_staff_id`) REFERENCES `work_order_staff` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_wo_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ========================================
-- 6. Work Order Status History
-- ========================================

DROP TABLE IF EXISTS `work_order_status_history`;
CREATE TABLE `work_order_status_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `work_order_id` int(11) NOT NULL,
  `from_status_id` int(11) DEFAULT NULL,
  `to_status_id` int(11) NOT NULL,
  `changed_by` int(11) NOT NULL,
  `change_reason` text DEFAULT NULL,
  `changed_at` timestamp DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_wosh_work_order` (`work_order_id`),
  KEY `fk_wosh_from_status` (`from_status_id`),
  KEY `fk_wosh_to_status` (`to_status_id`),
  KEY `fk_wosh_changed_by` (`changed_by`),
  CONSTRAINT `fk_wosh_work_order` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_wosh_from_status` FOREIGN KEY (`from_status_id`) REFERENCES `work_order_statuses` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_wosh_to_status` FOREIGN KEY (`to_status_id`) REFERENCES `work_order_statuses` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_wosh_changed_by` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ========================================
-- 7. Work Order Comments/Notes
-- ========================================

DROP TABLE IF EXISTS `work_order_comments`;
CREATE TABLE `work_order_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `work_order_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `comment_type` enum('PROGRESS','ISSUE','SOLUTION','GENERAL') DEFAULT 'GENERAL',
  `is_internal` tinyint(1) DEFAULT 0, -- Internal comments vs customer-visible
  `created_by` int(11) NOT NULL,
  `created_at` timestamp DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_woc_work_order` (`work_order_id`),
  KEY `fk_woc_created_by` (`created_by`),
  CONSTRAINT `fk_woc_work_order` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_woc_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ========================================
-- 8. Work Order Attachments/Files
-- ========================================

DROP TABLE IF EXISTS `work_order_attachments`;
CREATE TABLE `work_order_attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `work_order_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `file_size` bigint(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `attachment_type` enum('PHOTO','DOCUMENT','VIDEO','OTHER') DEFAULT 'PHOTO',
  `uploaded_by` int(11) NOT NULL,
  `uploaded_at` timestamp DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_woa_work_order` (`work_order_id`),
  KEY `fk_woa_uploaded_by` (`uploaded_by`),
  CONSTRAINT `fk_woa_work_order` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_woa_uploaded_by` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;