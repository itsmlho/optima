-- ====================================================================
-- AREA-BASED STAFF MANAGEMENT SYSTEM DATABASE DESIGN
-- ====================================================================
-- Created: 2025-09-25
-- Purpose: Proper hierarchy for Area -> Customer -> Unit -> Staff Assignment
-- ====================================================================

-- 1. AREAS TABLE (Master Areas/Wilayah)
-- ====================================================================
CREATE TABLE IF NOT EXISTS `areas` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `area_code` varchar(10) NOT NULL UNIQUE COMMENT 'A, B, C, etc',
    `area_name` varchar(100) NOT NULL COMMENT 'Jakarta Utara, Bekasi, Cikarang, etc',
    `area_description` text NULL COMMENT 'Detail coverage wilayah',
    `area_coordinates` json NULL COMMENT 'GPS coordinates untuk mapping',
    `is_active` tinyint(1) DEFAULT 1,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_area_code` (`area_code`),
    KEY `idx_area_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Master Areas/Wilayah';

-- Sample data for areas
INSERT INTO `areas` (`area_code`, `area_name`, `area_description`) VALUES
('A', 'Jakarta Utara', 'Meliputi wilayah Jakarta Utara dan sekitarnya'),
('B', 'Bekasi', 'Meliputi wilayah Bekasi, Cikarang, dan sekitarnya'),
('C', 'Tangerang', 'Meliputi wilayah Tangerang dan sekitarnya'),
('D', 'Jakarta Selatan', 'Meliputi wilayah Jakarta Selatan dan sekitarnya');

-- 2. CUSTOMERS TABLE (PT/Perusahaan Client)
-- ====================================================================
CREATE TABLE IF NOT EXISTS `customers` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `customer_code` varchar(20) NOT NULL UNIQUE COMMENT 'SML001, ABC002, etc',
    `customer_name` varchar(255) NOT NULL COMMENT 'Sarana Mitra Luas, PT ABC, etc',
    `area_id` int(11) NOT NULL COMMENT 'FK to areas table',
    `primary_address` text NOT NULL COMMENT 'Alamat utama perusahaan',
    `secondary_address` text NULL COMMENT 'Alamat cabang jika ada',
    `city` varchar(100) NOT NULL,
    `province` varchar(100) NOT NULL,
    `postal_code` varchar(10) NULL,
    `pic_name` varchar(100) NULL COMMENT 'Person in Charge',
    `pic_phone` varchar(20) NULL,
    `pic_email` varchar(100) NULL,
    `contract_type` enum('RENTAL_HARIAN','RENTAL_BULANAN','JUAL') DEFAULT 'RENTAL_BULANAN',
    `is_active` tinyint(1) DEFAULT 1,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_customer_code` (`customer_code`),
    KEY `idx_area_id` (`area_id`),
    KEY `idx_customer_active` (`is_active`),
    KEY `idx_customer_name` (`customer_name`),
    CONSTRAINT `fk_customers_area` FOREIGN KEY (`area_id`) REFERENCES `areas` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Master Customers/PT Client';

-- 3. CUSTOMER_LOCATIONS TABLE (Multiple locations per customer)
-- ====================================================================
CREATE TABLE IF NOT EXISTS `customer_locations` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `customer_id` int(11) NOT NULL,
    `location_name` varchar(100) NOT NULL COMMENT 'Kantor Pusat, Pabrik 1, Gudang A, etc',
    `address` text NOT NULL,
    `city` varchar(100) NOT NULL,
    `province` varchar(100) NOT NULL,
    `postal_code` varchar(10) NULL,
    `gps_latitude` decimal(10,8) NULL,
    `gps_longitude` decimal(11,8) NULL,
    `is_primary` tinyint(1) DEFAULT 0 COMMENT 'Primary location for this customer',
    `is_active` tinyint(1) DEFAULT 1,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_customer_id` (`customer_id`),
    KEY `idx_location_active` (`is_active`),
    CONSTRAINT `fk_customer_locations_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Multiple locations per customer';

-- 4. RENAME work_order_staff to general_staff (more appropriate)
-- ====================================================================
CREATE TABLE IF NOT EXISTS `staff` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `staff_code` varchar(20) NOT NULL UNIQUE COMMENT 'STF001, STF002, etc',
    `staff_name` varchar(100) NOT NULL,
    `staff_role` enum('ADMIN','FOREMAN','MECHANIC','HELPER','SUPERVISOR') NOT NULL,
    `phone` varchar(20) NULL,
    `email` varchar(100) NULL,
    `address` text NULL,
    `hire_date` date NULL,
    `is_active` tinyint(1) DEFAULT 1,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_staff_code` (`staff_code`),
    KEY `idx_staff_role` (`staff_role`),
    KEY `idx_staff_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Master Staff/Karyawan';

-- 5. AREA_STAFF_ASSIGNMENTS (Key table for area-based assignments)
-- ====================================================================
CREATE TABLE IF NOT EXISTS `area_staff_assignments` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `area_id` int(11) NOT NULL,
    `staff_id` int(11) NOT NULL,
    `assignment_type` enum('PRIMARY','BACKUP','TEMPORARY') DEFAULT 'PRIMARY',
    `start_date` date NOT NULL,
    `end_date` date NULL COMMENT 'NULL for permanent assignment',
    `is_active` tinyint(1) DEFAULT 1,
    `notes` text NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_area_staff` (`area_id`, `staff_id`),
    KEY `idx_staff_area` (`staff_id`, `area_id`),
    KEY `idx_assignment_active` (`is_active`),
    CONSTRAINT `fk_area_staff_area` FOREIGN KEY (`area_id`) REFERENCES `areas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_area_staff_staff` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY `uk_area_staff_assignment` (`area_id`, `staff_id`, `assignment_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Staff assignments per area';

-- 6. CUSTOMER_CONTRACTS TABLE (Relationship between customers and kontrak)
-- ====================================================================
CREATE TABLE IF NOT EXISTS `customer_contracts` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `customer_id` int(11) NOT NULL,
    `kontrak_id` int(11) NOT NULL COMMENT 'FK to existing kontrak table',
    `is_active` tinyint(1) DEFAULT 1,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_customer_kontrak` (`customer_id`, `kontrak_id`),
    KEY `idx_customer_id` (`customer_id`),
    KEY `idx_kontrak_id` (`kontrak_id`),
    CONSTRAINT `fk_customer_contracts_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_customer_contracts_kontrak` FOREIGN KEY (`kontrak_id`) REFERENCES `kontrak` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Link customers to kontrak (many-to-many)';

-- 7. UPDATE inventory_unit table to link with customers
-- ====================================================================
-- Note: This should be done carefully with existing data
ALTER TABLE `inventory_unit` 
ADD COLUMN `customer_id` int(11) NULL AFTER `kontrak_id`,
ADD COLUMN `customer_location_id` int(11) NULL AFTER `customer_id`,
ADD KEY `idx_customer_id` (`customer_id`),
ADD KEY `idx_customer_location_id` (`customer_location_id`);

-- Add foreign keys (after data migration)
-- ALTER TABLE `inventory_unit` 
-- ADD CONSTRAINT `fk_inventory_unit_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
-- ADD CONSTRAINT `fk_inventory_unit_customer_location` FOREIGN KEY (`customer_location_id`) REFERENCES `customer_locations` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- 8. VIEWS FOR EASY DATA ACCESS
-- ====================================================================

-- View: Complete unit information with area, staff, and kontrak
CREATE OR REPLACE VIEW `vw_unit_complete_info` AS
SELECT 
    iu.id_inventory_unit,
    iu.no_unit,
    iu.serial_number,
    iu.lokasi_unit,
    
    -- Customer info
    c.customer_code,
    c.customer_name,
    cl.location_name as customer_location,
    cl.address as customer_address,
    
    -- Area info  
    a.area_code,
    a.area_name,
    
    -- Kontrak info (from original kontrak table)
    k.no_kontrak,
    k.jenis_sewa as kontrak_jenis_sewa,
    k.status as kontrak_status,
    k.tanggal_mulai as kontrak_mulai,
    k.tanggal_berakhir as kontrak_berakhir,
    k.nilai_total as kontrak_nilai,
    
    -- Assigned staff per role
    GROUP_CONCAT(CASE WHEN s.staff_role = 'ADMIN' THEN s.staff_name END) as admin_staff,
    GROUP_CONCAT(CASE WHEN s.staff_role = 'FOREMAN' THEN s.staff_name END) as foreman_staff,
    GROUP_CONCAT(CASE WHEN s.staff_role = 'MECHANIC' THEN s.staff_name END) as mechanic_staff,
    GROUP_CONCAT(CASE WHEN s.staff_role = 'HELPER' THEN s.staff_name END) as helper_staff,
    
    -- Staff IDs for work order assignment
    GROUP_CONCAT(CASE WHEN s.staff_role = 'ADMIN' THEN s.id END) as admin_staff_ids,
    GROUP_CONCAT(CASE WHEN s.staff_role = 'FOREMAN' THEN s.id END) as foreman_staff_ids,
    GROUP_CONCAT(CASE WHEN s.staff_role = 'MECHANIC' THEN s.id END) as mechanic_staff_ids,
    GROUP_CONCAT(CASE WHEN s.staff_role = 'HELPER' THEN s.id END) as helper_staff_ids
    
FROM inventory_unit iu
LEFT JOIN customers c ON iu.customer_id = c.id
LEFT JOIN customer_locations cl ON iu.customer_location_id = cl.id
LEFT JOIN areas a ON c.area_id = a.id
LEFT JOIN customer_contracts cc ON c.id = cc.customer_id AND cc.is_active = 1
LEFT JOIN kontrak k ON cc.kontrak_id = k.id
LEFT JOIN area_staff_assignments asa ON a.id = asa.area_id AND asa.is_active = 1
LEFT JOIN staff s ON asa.staff_id = s.id AND s.is_active = 1
WHERE iu.is_active = 1
GROUP BY iu.id_inventory_unit;

-- View: Area staff summary
CREATE OR REPLACE VIEW `vw_area_staff_summary` AS
SELECT 
    a.area_code,
    a.area_name,
    COUNT(DISTINCT c.id) as total_customers,
    COUNT(DISTINCT iu.id_inventory_unit) as total_units,
    COUNT(DISTINCT CASE WHEN s.staff_role = 'ADMIN' THEN s.id END) as admin_count,
    COUNT(DISTINCT CASE WHEN s.staff_role = 'FOREMAN' THEN s.id END) as foreman_count,
    COUNT(DISTINCT CASE WHEN s.staff_role = 'MECHANIC' THEN s.id END) as mechanic_count,
    COUNT(DISTINCT CASE WHEN s.staff_role = 'HELPER' THEN s.id END) as helper_count
FROM areas a
LEFT JOIN customers c ON a.id = c.area_id AND c.is_active = 1
LEFT JOIN inventory_unit iu ON c.id = iu.customer_id AND iu.is_active = 1
LEFT JOIN area_staff_assignments asa ON a.id = asa.area_id AND asa.is_active = 1
LEFT JOIN staff s ON asa.staff_id = s.id AND s.is_active = 1
WHERE a.is_active = 1
GROUP BY a.id;

-- ====================================================================
-- SAMPLE DATA MIGRATION QUERIES WITH KONTRAK INTEGRATION
-- ====================================================================

-- Sample: Migrate existing data from kontrak to customers  
-- INSERT INTO customers (customer_code, customer_name, area_id, primary_address, city, province, pic_name, pic_phone, contract_type)
-- SELECT 
--     CONCAT('CUST', LPAD(id, 3, '0')) as customer_code,
--     pelanggan as customer_name,
--     CASE 
--         WHEN lokasi LIKE '%Jakarta Utara%' OR lokasi LIKE '%Sunter%' THEN 1
--         WHEN lokasi LIKE '%Bekasi%' OR lokasi LIKE '%Cikarang%' THEN 2  
--         WHEN lokasi LIKE '%Tangerang%' THEN 3
--         ELSE 4 -- Default Jakarta Selatan
--     END as area_id,
--     lokasi as primary_address,
--     CASE 
--         WHEN lokasi LIKE '%Jakarta%' THEN 'Jakarta'
--         WHEN lokasi LIKE '%Bekasi%' THEN 'Bekasi'
--         WHEN lokasi LIKE '%Tangerang%' THEN 'Tangerang'
--         ELSE 'Jakarta'
--     END as city,
--     'DKI Jakarta' as province,
--     pic as pic_name,
--     kontak as pic_phone,
--     CASE 
--         WHEN jenis_sewa = 'HARIAN' THEN 'RENTAL_HARIAN'
--         WHEN jenis_sewa = 'BULANAN' THEN 'RENTAL_BULANAN'
--         ELSE 'RENTAL_BULANAN'
--     END as contract_type
-- FROM kontrak 
-- WHERE status = 'Aktif' AND pelanggan IS NOT NULL;

-- Sample: Create primary locations for customers
-- INSERT INTO customer_locations (customer_id, location_name, address, city, province, is_primary)
-- SELECT 
--     c.id as customer_id,
--     'Kantor Utama' as location_name,
--     c.primary_address as address,
--     c.city,
--     c.province,
--     1 as is_primary
-- FROM customers c;

-- Sample: Create customer_contracts relationship
-- INSERT INTO customer_contracts (customer_id, kontrak_id, is_active)
-- SELECT 
--     c.id as customer_id,
--     k.id as kontrak_id,
--     CASE WHEN k.status = 'Aktif' THEN 1 ELSE 0 END as is_active
-- FROM customers c
-- JOIN kontrak k ON CONCAT('CUST', LPAD(k.id, 3, '0')) = c.customer_code;

-- Sample: Migrate work_order_staff to new staff table
-- INSERT INTO staff (staff_code, staff_name, staff_role, is_active)
-- SELECT 
--     CONCAT('STF', LPAD(id, 3, '0')) as staff_code,
--     staff_name,
--     staff_role,
--     is_active
-- FROM work_order_staff;

-- ====================================================================
-- WORK ORDER AUTO-ASSIGNMENT FUNCTION
-- ====================================================================

DELIMITER //

CREATE FUNCTION GetAreaStaffByRole(p_unit_id INT, p_role VARCHAR(20)) 
RETURNS INT
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE staff_id INT DEFAULT NULL;
    
    SELECT s.id INTO staff_id
    FROM inventory_unit iu
    JOIN customers c ON iu.customer_id = c.id
    JOIN areas a ON c.area_id = a.id
    JOIN area_staff_assignments asa ON a.id = asa.area_id
    JOIN staff s ON asa.staff_id = s.id
    WHERE iu.id_inventory_unit = p_unit_id
        AND s.staff_role = p_role
        AND s.is_active = 1
        AND asa.is_active = 1
        AND asa.assignment_type = 'PRIMARY'
    ORDER BY asa.start_date DESC
    LIMIT 1;
    
    RETURN staff_id;
END//

DELIMITER ;

-- ====================================================================
-- INDEXES FOR PERFORMANCE
-- ====================================================================

-- Additional indexes for better performance
CREATE INDEX idx_areas_active ON areas(is_active);
CREATE INDEX idx_customers_area_active ON customers(area_id, is_active);
CREATE INDEX idx_customer_locations_customer_active ON customer_locations(customer_id, is_active);
CREATE INDEX idx_staff_role_active ON staff(staff_role, is_active);
CREATE INDEX idx_area_staff_assignments_active ON area_staff_assignments(area_id, is_active);

-- ====================================================================
-- END OF AREA-BASED STAFF SYSTEM DATABASE DESIGN
-- ====================================================================