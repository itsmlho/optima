-- OPTIMIZED DATABASE VIEWS FOR MARKETING PERFORMANCE
-- These views pre-compute expensive joins and aggregations

-- 1. Kontrak Dashboard View (replaces complex joins in every DataTable request)
CREATE OR REPLACE VIEW v_kontrak_dashboard AS
SELECT 
    k.id,
    k.no_kontrak,
    k.nilai_total,
    k.status,
    k.dibuat_pada,
    k.diperbarui_pada,
    c.customer_name,
    cl.location_name,
    cl.address as location_address,
    COALESCE(unit_stats.unit_count, 0) as unit_count,
    COALESCE(spec_stats.spec_count, 0) as spec_count,
    COALESCE(di_stats.di_count, 0) as di_count,
    -- Performance indicators
    DATEDIFF(CURDATE(), DATE(k.dibuat_pada)) as days_since_created,
    CASE 
        WHEN k.status = 'draft' AND DATEDIFF(CURDATE(), DATE(k.dibuat_pada)) > 7 THEN 'overdue'
        WHEN k.status = 'pending' AND DATEDIFF(CURDATE(), DATE(k.dibuat_pada)) > 14 THEN 'urgent'
        ELSE 'normal'
    END as priority_status
FROM kontrak k
LEFT JOIN customer_locations cl ON k.customer_location_id = cl.id
LEFT JOIN customers c ON cl.customer_id = c.id
LEFT JOIN (
    SELECT 
        kontrak_id, 
        COUNT(*) as unit_count,
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_units
    FROM inventory_unit 
    GROUP BY kontrak_id
) unit_stats ON k.id = unit_stats.kontrak_id
LEFT JOIN (
    SELECT 
        kontrak_id, 
        COUNT(*) as spec_count
    FROM kontrak_spesifikasi 
    GROUP BY kontrak_id
) spec_stats ON k.id = spec_stats.kontrak_id
LEFT JOIN (
    SELECT 
        kontrak_id, 
        COUNT(*) as di_count,
        MAX(created_at) as last_di_date
    FROM delivery_instruction 
    GROUP BY kontrak_id
) di_stats ON k.id = di_stats.kontrak_id;

-- 2. Unit Availability Summary View
CREATE OR REPLACE VIEW v_unit_availability AS
SELECT 
    iu.id_inventory_unit,
    iu.kode_unit,
    iu.status,
    iu.kondisi_unit,
    iu.kontrak_id,
    iu.lokasi_unit,
    iu.active,
    kt.jenis_armada,
    kt.type_kendaraan,
    kt.merk,
    kt.model,
    kt.tahun_kendaraan,
    kt.warna,
    kt.no_polisi,
    kt.no_mesin,
    kt.no_rangka,
    -- Availability flags
    CASE 
        WHEN iu.kontrak_id IS NULL AND iu.active = 1 THEN 'available'
        WHEN iu.kontrak_id IS NOT NULL THEN 'contracted'
        WHEN iu.active = 0 THEN 'inactive'
        ELSE 'unknown'
    END as availability_status,
    -- Contract info if assigned
    k.no_kontrak,
    k.status as kontrak_status,
    c.customer_name,
    -- Maintenance alerts
    CASE 
        WHEN kt.masa_berlaku_stnk < DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN 'stnk_expiring'
        WHEN kt.masa_berlaku_kir < DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN 'kir_expiring'
        ELSE 'ok'
    END as maintenance_alert
FROM inventory_unit iu
LEFT JOIN kendaraan_tracking kt ON iu.id_inventory_unit = kt.inventory_unit_id
LEFT JOIN kontrak k ON iu.kontrak_id = k.id
LEFT JOIN customer_locations cl ON k.customer_location_id = cl.id
LEFT JOIN customers c ON cl.customer_id = c.id;

-- 3. Daily Performance Stats View
CREATE OR REPLACE VIEW v_daily_performance_stats AS
SELECT 
    DATE(created_date) as date,
    'contracts' as metric_type,
    COUNT(*) as count,
    SUM(nilai_total) as total_value,
    AVG(nilai_total) as avg_value,
    COUNT(DISTINCT customer_location_id) as unique_customers
FROM kontrak
WHERE created_date >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)
GROUP BY DATE(created_date)
UNION ALL
SELECT 
    DATE(created_at) as date,
    'units_added' as metric_type,
    COUNT(*) as count,
    0 as total_value,
    0 as avg_value,
    0 as unique_customers
FROM inventory_unit
WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)
GROUP BY DATE(created_at)
UNION ALL
SELECT 
    DATE(created_at) as date,
    'delivery_instructions' as metric_type,
    COUNT(*) as count,
    0 as total_value,
    0 as avg_value,
    0 as unique_customers
FROM delivery_instruction
WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)
GROUP BY DATE(created_at);

-- 4. Customer Activity Summary View
CREATE OR REPLACE VIEW v_customer_activity AS
SELECT 
    c.id as customer_id,
    c.customer_name,
    c.email,
    c.phone,
    COUNT(DISTINCT cl.id) as location_count,
    COUNT(DISTINCT k.id) as contract_count,
    COALESCE(SUM(k.nilai_total), 0) as total_contract_value,
    MAX(k.dibuat_pada) as last_contract_date,
    COUNT(DISTINCT CASE WHEN k.status = 'active' THEN k.id END) as active_contracts,
    COUNT(DISTINCT iu.id_inventory_unit) as total_units,
    -- Customer scoring
    CASE 
        WHEN COALESCE(SUM(k.nilai_total), 0) > 10000000 THEN 'premium'
        WHEN COALESCE(SUM(k.nilai_total), 0) > 5000000 THEN 'gold'
        WHEN COALESCE(SUM(k.nilai_total), 0) > 1000000 THEN 'silver'
        ELSE 'bronze'
    END as customer_tier,
    -- Activity indicators
    DATEDIFF(CURDATE(), MAX(k.dibuat_pada)) as days_since_last_contract
FROM customers c
LEFT JOIN customer_locations cl ON c.id = cl.customer_id
LEFT JOIN kontrak k ON cl.id = k.customer_location_id
LEFT JOIN inventory_unit iu ON k.id = iu.kontrak_id
GROUP BY c.id, c.customer_name, c.email, c.phone;

-- 5. Indexes for optimal view performance
CREATE INDEX IF NOT EXISTS idx_kontrak_status_date ON kontrak(status, dibuat_pada);
CREATE INDEX IF NOT EXISTS idx_kontrak_customer_location ON kontrak(customer_location_id);
CREATE INDEX IF NOT EXISTS idx_inventory_unit_kontrak ON inventory_unit(kontrak_id);
CREATE INDEX IF NOT EXISTS idx_inventory_unit_status ON inventory_unit(status, active);
CREATE INDEX IF NOT EXISTS idx_kendaraan_tracking_unit ON kendaraan_tracking(inventory_unit_id);
CREATE INDEX IF NOT EXISTS idx_kontrak_spek_kontrak ON kontrak_spesifikasi(kontrak_id);
CREATE INDEX IF NOT EXISTS idx_delivery_instruction_kontrak ON delivery_instruction(kontrak_id);
CREATE INDEX IF NOT EXISTS idx_customer_locations_customer ON customer_locations(customer_id);

-- 6. Performance monitoring functions
DELIMITER $$

CREATE FUNCTION GetContractPerformanceScore(contract_id INT) 
RETURNS DECIMAL(3,2)
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE score DECIMAL(3,2) DEFAULT 0.0;
    DECLARE days_old INT;
    DECLARE unit_count INT;
    DECLARE spec_count INT;
    
    SELECT 
        DATEDIFF(CURDATE(), DATE(dibuat_pada)),
        (SELECT COUNT(*) FROM inventory_unit WHERE kontrak_id = contract_id),
        (SELECT COUNT(*) FROM kontrak_spesifikasi WHERE kontrak_id = contract_id)
    INTO days_old, unit_count, spec_count
    FROM kontrak WHERE id = contract_id;
    
    -- Base score
    SET score = 1.0;
    
    -- Penalties
    IF days_old > 30 THEN SET score = score - 0.2; END IF;
    IF unit_count = 0 THEN SET score = score - 0.3; END IF;
    IF spec_count = 0 THEN SET score = score - 0.1; END IF;
    
    -- Ensure score is between 0 and 1
    IF score < 0 THEN SET score = 0.0; END IF;
    
    RETURN score;
END$$

DELIMITER ;

-- 7. Stored procedures for common operations
DELIMITER $$

CREATE PROCEDURE GetContractSummary(IN customer_id INT, IN date_from DATE, IN date_to DATE)
BEGIN
    SELECT 
        COUNT(*) as total_contracts,
        SUM(nilai_total) as total_value,
        COUNT(CASE WHEN status = 'active' THEN 1 END) as active_contracts,
        COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_contracts,
        AVG(nilai_total) as average_contract_value
    FROM v_kontrak_dashboard 
    WHERE customer_name = (SELECT customer_name FROM customers WHERE id = customer_id)
    AND DATE(dibuat_pada) BETWEEN date_from AND date_to;
END$$

CREATE PROCEDURE GetAvailableUnitsByCategory(IN category_filter VARCHAR(100))
BEGIN
    IF category_filter IS NULL OR category_filter = '' THEN
        SELECT * FROM v_unit_availability WHERE availability_status = 'available';
    ELSE
        SELECT * FROM v_unit_availability 
        WHERE availability_status = 'available' 
        AND jenis_armada = category_filter;
    END IF;
END$$

DELIMITER ;

-- 8. Triggers untuk cache invalidation
DELIMITER $$

CREATE TRIGGER after_kontrak_update
AFTER UPDATE ON kontrak
FOR EACH ROW
BEGIN
    -- Log the cache invalidation need
    INSERT INTO cache_invalidation_queue (cache_group, triggered_by, triggered_at)
    VALUES ('contracts', CONCAT('kontrak:', NEW.id), NOW());
END$$

CREATE TRIGGER after_inventory_unit_update
AFTER UPDATE ON inventory_unit
FOR EACH ROW
BEGIN
    INSERT INTO cache_invalidation_queue (cache_group, triggered_by, triggered_at)
    VALUES ('inventory', CONCAT('unit:', NEW.id_inventory_unit), NOW());
    
    IF OLD.kontrak_id != NEW.kontrak_id THEN
        INSERT INTO cache_invalidation_queue (cache_group, triggered_by, triggered_at)
        VALUES ('contracts', CONCAT('kontrak:', COALESCE(NEW.kontrak_id, OLD.kontrak_id)), NOW());
    END IF;
END$$

DELIMITER ;

-- 9. Create cache invalidation queue table
CREATE TABLE IF NOT EXISTS cache_invalidation_queue (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cache_group VARCHAR(50) NOT NULL,
    triggered_by VARCHAR(100) NOT NULL,
    triggered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    processed_at TIMESTAMP NULL,
    INDEX idx_cache_group (cache_group),
    INDEX idx_triggered_at (triggered_at),
    INDEX idx_processed (processed_at)
);

-- 10. Performance optimization completion marker
INSERT INTO system_config (config_key, config_value, description, created_at) 
VALUES (
    'database_optimization_version', 
    'v2.0_advanced_views_procedures', 
    'Database optimization with materialized views and performance procedures',
    NOW()
) ON DUPLICATE KEY UPDATE 
config_value = 'v2.0_advanced_views_procedures',
updated_at = NOW();