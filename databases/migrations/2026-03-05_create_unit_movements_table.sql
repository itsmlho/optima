-- Unit Movement Table
-- Untuk record perpindahan unit (forklift, attachment, charger, baterai) antar workshop (POS 1-5)
-- Atau antar lokasi perusahaan

CREATE TABLE IF NOT EXISTS unit_movements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    movement_number VARCHAR(50) NOT NULL UNIQUE,

    -- Unit yang dipindahkan (bisa unit utama, attachment, charger, baterai)
    unit_id INT UNSIGNED DEFAULT NULL,
    component_id INT DEFAULT NULL,
    component_type ENUM('FORKLIFT', 'ATTACHMENT', 'CHARGER', 'BATTERY') DEFAULT 'FORKLIFT',

    -- Asal dan Tujuan
    origin_location VARCHAR(100) NOT NULL,
    destination_location VARCHAR(100) NOT NULL,

    -- Predefined locations (POS = Point of Service / Workshop)
    origin_type ENUM('POS_1', 'POS_2', 'POS_3', 'POS_4', 'POS_5', 'CUSTOMER_SITE', 'WAREHOUSE', 'OTHER') DEFAULT 'POS_1',
    destination_type ENUM('POS_1', 'POS_2', 'POS_3', 'POS_4', 'POS_5', 'CUSTOMER_SITE', 'WAREHOUSE', 'OTHER') DEFAULT 'POS_1',

    -- Detail perpindahan
    movement_date DATETIME NOT NULL,
    driver_name VARCHAR(100) DEFAULT NULL,
    vehicle_number VARCHAR(50) DEFAULT NULL,
    notes TEXT DEFAULT NULL,

    -- Surat Jalan Number
    surat_jalan_number VARCHAR(50) DEFAULT NULL,

    -- Status: DRAFT, IN_TRANSIT, ARRIVED, CANCELLED
    status ENUM('DRAFT', 'IN_TRANSIT', 'ARRIVED', 'CANCELLED') DEFAULT 'DRAFT',

    -- User info
    created_by_user_id INT NOT NULL,
    confirmed_by_user_id INT DEFAULT NULL,
    confirmed_at DATETIME DEFAULT NULL,

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Foreign keys
    FOREIGN KEY (unit_id) REFERENCES inventory_unit(id_inventory_unit) ON DELETE SET NULL,
    FOREIGN KEY (created_by_user_id) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (confirmed_by_user_id) REFERENCES users(id) ON DELETE SET NULL,

    INDEX idx_movement_number (movement_number),
    INDEX idx_surat_jalan_number (surat_jalan_number),
    INDEX idx_unit_id (unit_id),
    INDEX idx_movement_date (movement_date),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
