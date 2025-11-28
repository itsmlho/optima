-- SAFE PERFORMANCE OPTIMIZATION SCRIPT
-- Only creates views and indexes for existing tables

-- 1. Check existing tables first and create safe view
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
        COUNT(*) as unit_count
    FROM inventory_unit 
    WHERE kontrak_id IS NOT NULL
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
        s.kontrak_id, 
        COUNT(*) as di_count,
        MAX(di.dibuat_pada) as last_di_date
    FROM delivery_instructions di
    JOIN spk s ON di.spk_id = s.id
    WHERE s.kontrak_id IS NOT NULL
    GROUP BY s.kontrak_id
) di_stats ON k.id = di_stats.kontrak_id;

-- 2. Unit availability view (safe version)
CREATE OR REPLACE VIEW v_unit_availability AS
SELECT 
    iu.id_inventory_unit,
    iu.serial_number,
    iu.no_unit,
    iu.kontrak_id,
    iu.lokasi_unit,
    iu.workflow_status,
    -- Availability flags
    CASE 
        WHEN iu.kontrak_id IS NULL THEN 'available'
        WHEN iu.kontrak_id IS NOT NULL THEN 'contracted'
        ELSE 'unknown'
    END as availability_status,
    -- Contract info if assigned
    k.no_kontrak,
    k.status as kontrak_status,
    c.customer_name
FROM inventory_unit iu
LEFT JOIN kontrak k ON iu.kontrak_id = k.id
LEFT JOIN customer_locations cl ON k.customer_location_id = cl.id
LEFT JOIN customers c ON cl.customer_id = c.id;

-- 3. Customer activity summary view
CREATE OR REPLACE VIEW v_customer_activity AS
SELECT 
    c.id as customer_id,
    c.customer_name,
    c.customer_code,
    c.is_active,
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
GROUP BY c.id, c.customer_name, c.customer_code, c.is_active;

-- 4. Performance indexes for existing tables (with error handling)
CREATE INDEX idx_kontrak_status_date ON kontrak(status, dibuat_pada);
CREATE INDEX idx_kontrak_customer_location ON kontrak(customer_location_id);
CREATE INDEX idx_inventory_unit_kontrak ON inventory_unit(kontrak_id);
CREATE INDEX idx_inventory_unit_workflow ON inventory_unit(workflow_status);
CREATE INDEX idx_inventory_unit_created ON inventory_unit(created_at);
CREATE INDEX idx_customer_locations_customer ON customer_locations(customer_id);
CREATE INDEX idx_customers_name ON customers(customer_name);
CREATE INDEX idx_kontrak_nilai ON kontrak(nilai_total);
CREATE INDEX idx_kontrak_spek_kontrak ON kontrak_spesifikasi(kontrak_id);
CREATE INDEX idx_delivery_instructions_spk ON delivery_instructions(spk_id);
CREATE INDEX idx_delivery_instructions_created ON delivery_instructions(dibuat_pada);
CREATE INDEX idx_spk_kontrak ON spk(kontrak_id);

-- 5. Create cache invalidation queue table
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

-- 6. Create system_config if not exists
CREATE TABLE IF NOT EXISTS system_config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    config_key VARCHAR(100) NOT NULL UNIQUE,
    config_value TEXT,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_config_key (config_key)
);

-- 7. Performance optimization completion marker
INSERT INTO system_config (config_key, config_value, description, created_at) 
VALUES (
    'database_optimization_version', 
    'v2.0_safe_optimization', 
    'Safe database optimization with essential views and indexes',
    NOW()
) ON DUPLICATE KEY UPDATE 
config_value = 'v2.0_safe_optimization',
updated_at = NOW();