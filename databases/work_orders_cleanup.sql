-- Clean up work_orders table - Remove unused user_id columns
-- Keep only staff_id columns for work order staff assignments

USE optima_db;

-- 1. Drop foreign key constraints for user_id columns
ALTER TABLE `work_orders` 
DROP FOREIGN KEY `fk_wo_admin`,
DROP FOREIGN KEY `fk_wo_foreman`, 
DROP FOREIGN KEY `fk_wo_mechanic`,
DROP FOREIGN KEY `fk_wo_helper`;

-- 2. Drop indexes for user_id columns
ALTER TABLE `work_orders`
DROP KEY `fk_wo_admin`,
DROP KEY `fk_wo_foreman`,
DROP KEY `fk_wo_mechanic`,
DROP KEY `fk_wo_helper`;

-- 3. Drop the user_id columns
ALTER TABLE `work_orders`
DROP COLUMN `admin_user_id`,
DROP COLUMN `foreman_user_id`,
DROP COLUMN `mechanic_user_id`,
DROP COLUMN `helper_user_id`;

-- 4. Show final structure
DESCRIBE work_orders;