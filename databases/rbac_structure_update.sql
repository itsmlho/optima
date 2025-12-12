-- ============================================================================
-- OPTIMA PRO - RBAC STRUCTURE UPDATE SCRIPT
-- Created: December 10, 2025
-- Purpose: Implement optimal Service Area RBAC structure
-- ============================================================================

-- Backup existing data before making changes
-- CREATE TABLE divisions_backup AS SELECT * FROM divisions;
-- CREATE TABLE roles_backup AS SELECT * FROM roles;

-- ============================================================================
-- 1. UPDATE DIVISIONS TABLE
-- ============================================================================

-- Add unified Service Division
INSERT INTO `divisions` (`id`, `name`, `code`, `description`, `is_active`, `created_at`, `updated_at`) 
VALUES (9, 'Service', 'SERVICE', 'Service Division - Unified Service Operations (All Areas & Departments)', 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE 
    name = VALUES(name), 
    description = VALUES(description),
    updated_at = NOW();

-- Deactivate old separate service divisions (keep for data integrity)
UPDATE `divisions` 
SET 
    name = CONCAT(name, ' (Legacy)'),
    description = CONCAT(COALESCE(description, ''), ' - DEPRECATED: Use unified Service Division'),
    is_active = 0,
    updated_at = NOW()
WHERE code IN ('SERVICE_DIESEL', 'SERVICE_ELECTRIC');

-- ============================================================================
-- 2. ADD NEW SERVICE ROLES  
-- ============================================================================

-- Level 2: Division Head
INSERT INTO `roles` (`name`, `slug`, `description`, `is_preset`, `division_id`, `is_system_role`, `is_active`, `created_at`, `updated_at`) VALUES

-- Division Head
('Head Service', 'head_service', 'Head of Service Division - Strategic oversight of all service operations, areas, and departments', 1, 9, 0, 1, NOW(), NOW()),

-- Level 3A: Service Pusat (Central Office) - Department Specific
('Admin Service Pusat Electric', 'admin_service_pusat_electric', 'Central Service Administrator - Electric Department Only (Jakarta Pusat, Jakarta Selatan)', 1, 9, 0, 1, NOW(), NOW()),
('Admin Service Pusat Diesel', 'admin_service_pusat_diesel', 'Central Service Administrator - Diesel & Gasoline Departments (Jakarta Pusat, Jakarta Selatan)', 1, 9, 0, 1, NOW(), NOW()),
('Manager Service Pusat', 'manager_service_pusat', 'Central Service Manager - All Departments, Strategic Planning & Oversight', 1, 9, 0, 1, NOW(), NOW()),

-- Level 3B: Service Area (Branch/Regional) - Geographic Specific  
('Admin Service Area', 'admin_service_area', 'Area Service Administrator - All Departments within assigned geographic areas', 1, 9, 0, 1, NOW(), NOW()),
('Supervisor Service Area', 'supervisor_service_area', 'Area Service Supervisor - Operational management and daily coordination', 1, 9, 0, 1, NOW(), NOW()),
('Staff Service Area', 'staff_service_area', 'Area Service Staff - Field operations and customer service execution', 1, 9, 0, 1, NOW(), NOW()),

-- Level 3C: Field Operations - Role Specific
('Foreman Service', 'foreman_service', 'Service Team Leader - Lead technician and team coordination in assigned areas', 1, 9, 0, 1, NOW(), NOW()),
('Mechanic Service', 'mechanic_service', 'Service Technician - Skilled mechanic for equipment maintenance and repair', 1, 9, 0, 1, NOW(), NOW()),
('Helper Service', 'helper_service', 'Service Assistant - Support staff for service operations and logistics', 1, 9, 0, 1, NOW(), NOW());

-- ============================================================================
-- 3. UPDATE EXISTING SERVICE ROLES (Mark as legacy)
-- ============================================================================

-- Mark existing service roles as legacy
UPDATE `roles` 
SET 
    name = CONCAT(name, ' (Legacy)'),
    description = CONCAT(COALESCE(description, ''), ' - DEPRECATED: Use new unified service roles'),
    is_active = 0,
    updated_at = NOW()
WHERE name IN (
    'Head Service Diesel', 
    'Staff Service Diesel', 
    'Head Service Electric', 
    'Staff Service Electric'
);

-- ============================================================================
-- 4. CREATE EXTENDED AREA-DEPARTMENT ACCESS TABLE
-- ============================================================================

-- Create table for fine-grained access control
CREATE TABLE IF NOT EXISTS `user_area_department_access` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` int(11) UNSIGNED NOT NULL,
    `area_id` int(11) DEFAULT NULL COMMENT 'NULL = All Areas',
    `department_id` int(11) DEFAULT NULL COMMENT 'NULL = All Departments',
    `access_type` enum('FULL', 'READ_ONLY', 'WRITE_ONLY') DEFAULT 'FULL',
    `is_active` tinyint(1) DEFAULT 1,
    `created_at` timestamp NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `idx_user_area_dept` (`user_id`, `area_id`, `department_id`),
    KEY `idx_user_active` (`user_id`, `is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Fine-grained area and department access control for users';

-- ============================================================================
-- 5. SAMPLE ACCESS ASSIGNMENTS (for testing)
-- ============================================================================

-- Example: Admin Service Pusat Electric (User ID 1)
-- INSERT INTO `user_area_department_access` (`user_id`, `area_id`, `department_id`, `access_type`) VALUES
-- (1, 1, 2, 'FULL'),  -- Jakarta Pusat, Electric
-- (1, 4, 2, 'FULL');  -- Jakarta Selatan, Electric

-- Example: Admin Service Area (User ID 2) 
-- INSERT INTO `user_area_department_access` (`user_id`, `area_id`, `department_id`, `access_type`) VALUES
-- (2, 5, NULL, 'FULL'); -- Tangerang, All Departments

-- ============================================================================
-- 6. CREATE PERMISSION GROUPS TABLE
-- ============================================================================

CREATE TABLE IF NOT EXISTS `permission_groups` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `slug` varchar(100) NOT NULL,
    `module` varchar(50) NOT NULL COMMENT 'Module name (e.g., service, inventory, marketing)',
    `description` text DEFAULT NULL,
    `is_active` tinyint(1) DEFAULT 1,
    `created_at` timestamp NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug_module` (`slug`, `module`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample permission groups for service module
INSERT INTO `permission_groups` (`name`, `slug`, `module`, `description`) VALUES
('Service Area Management', 'area_management', 'service', 'Permissions for managing service areas and assignments'),
('Service Operations', 'service_operations', 'service', 'Permissions for daily service operations and work orders'),
('Service Reporting', 'service_reporting', 'service', 'Permissions for service reports and analytics'),
('Service Administration', 'service_admin', 'service', 'Administrative permissions for service module');

-- ============================================================================
-- 7. CREATE ROLE PERMISSION MAPPINGS
-- ============================================================================

CREATE TABLE IF NOT EXISTS `role_permissions` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `role_id` int(11) UNSIGNED NOT NULL,
    `permission_group_id` int(11) UNSIGNED NOT NULL,
    `permissions` json NOT NULL COMMENT 'JSON array of specific permissions',
    `is_active` tinyint(1) DEFAULT 1,
    `created_at` timestamp NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `role_permission` (`role_id`, `permission_group_id`),
    KEY `idx_role_active` (`role_id`, `is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 8. VERIFICATION QUERIES
-- ============================================================================

-- Check new divisions
-- SELECT id, name, code, description, is_active FROM divisions WHERE code = 'SERVICE' OR name LIKE '%Legacy%';

-- Check new roles  
-- SELECT id, name, slug, description, division_id, is_active FROM roles WHERE division_id = 9 OR name LIKE '%Service%';

-- Check role distribution by division
-- SELECT d.name as division, COUNT(r.id) as role_count 
-- FROM divisions d 
-- LEFT JOIN roles r ON d.id = r.division_id AND r.is_active = 1
-- GROUP BY d.id, d.name 
-- ORDER BY role_count DESC;

-- ============================================================================
-- 9. CLEANUP PROCEDURES (Run after testing)
-- ============================================================================

-- Remove completely deprecated roles (after user migration)
-- DELETE FROM roles WHERE name LIKE '%Legacy%' AND is_active = 0;

-- Remove deprecated divisions (after data migration)  
-- DELETE FROM divisions WHERE name LIKE '%Legacy%' AND is_active = 0;

-- ============================================================================
-- END OF SCRIPT
-- ============================================================================

-- Notes:
-- 1. Test thoroughly in development environment first
-- 2. Backup existing data before running in production
-- 3. Update application code to use new role structure
-- 4. Migrate existing users to new roles gradually
-- 5. Monitor system performance after implementation

COMMIT;