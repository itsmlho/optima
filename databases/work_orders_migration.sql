-- Work Orders Table Migration Script
-- This script updates existing work_orders table structure to match our new design

USE optima_db;

-- 1. Create work_order_staff table if not exists
CREATE TABLE IF NOT EXISTS `work_order_staff` (
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

-- 2. Update order_type enum in work_orders table
ALTER TABLE `work_orders` 
MODIFY COLUMN `order_type` enum('COMPLAINT','PMPS','FABRIKASI','PERSIAPAN') NOT NULL DEFAULT 'COMPLAINT';

-- 3. Add new staff columns to work_orders table
ALTER TABLE `work_orders` 
ADD COLUMN `admin_staff_id` int(11) DEFAULT NULL AFTER `admin_user_id`,
ADD COLUMN `foreman_staff_id` int(11) DEFAULT NULL AFTER `foreman_user_id`,
ADD COLUMN `mechanic_staff_id` int(11) DEFAULT NULL AFTER `mechanic_user_id`,
ADD COLUMN `helper_staff_id` int(11) DEFAULT NULL AFTER `helper_user_id`;

-- 4. Add foreign key constraints for staff
ALTER TABLE `work_orders`
ADD CONSTRAINT `fk_wo_admin_staff` FOREIGN KEY (`admin_staff_id`) REFERENCES `work_order_staff` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
ADD CONSTRAINT `fk_wo_foreman_staff` FOREIGN KEY (`foreman_staff_id`) REFERENCES `work_order_staff` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
ADD CONSTRAINT `fk_wo_mechanic_staff` FOREIGN KEY (`mechanic_staff_id`) REFERENCES `work_order_staff` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
ADD CONSTRAINT `fk_wo_helper_staff` FOREIGN KEY (`helper_staff_id`) REFERENCES `work_order_staff` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- 5. Add indexes for new staff columns
ALTER TABLE `work_orders`
ADD KEY `fk_wo_admin_staff` (`admin_staff_id`),
ADD KEY `fk_wo_foreman_staff` (`foreman_staff_id`),
ADD KEY `fk_wo_mechanic_staff` (`mechanic_staff_id`),
ADD KEY `fk_wo_helper_staff` (`helper_staff_id`);