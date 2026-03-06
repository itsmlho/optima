-- Unit Audit Request Table
-- Untuk pelaporan unit yang lokasinya tidak sesuai (audit internal)
-- Workflow: SERVICE melapor -> MARKETING approve/reject

CREATE TABLE IF NOT EXISTS unit_audit_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    unit_id INT UNSIGNED NOT NULL,
    reported_by_user_id INT NOT NULL,
    approved_by_user_id INT DEFAULT NULL,

    -- Lokasi yang tercatat di sistem (status saat ini)
    recorded_location VARCHAR(255) DEFAULT NULL,
    recorded_status VARCHAR(50) DEFAULT NULL,
    recorded_customer_id INT DEFAULT NULL,
    recorded_customer_name VARCHAR(255) DEFAULT NULL,
    recorded_kontrak_id INT DEFAULT NULL,

    -- Lokasi aktual di lapangan (temuan audit)
    actual_location VARCHAR(255) NOT NULL,
    actual_customer_id INT DEFAULT NULL,
    actual_customer_name VARCHAR(255) DEFAULT NULL,
    actual_notes TEXT DEFAULT NULL,

    -- Request type: LOCATION_MISMATCH, STATUS_MISMATCH, DAMAGE_REPORT, OTHER
    request_type ENUM('LOCATION_MISMATCH', 'STATUS_MISMATCH', 'DAMAGE_REPORT', 'OTHER') DEFAULT 'LOCATION_MISMATCH',

    -- Status: PENDING, APPROVED, REJECTED, CANCELLED
    status ENUM('PENDING', 'APPROVED', 'REJECTED', 'CANCELLED') DEFAULT 'PENDING',

    -- Approval details
    approved_at DATETIME DEFAULT NULL,
    approval_notes TEXT DEFAULT NULL,

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Foreign keys
    FOREIGN KEY (unit_id) REFERENCES inventory_unit(id_inventory_unit) ON DELETE CASCADE,
    FOREIGN KEY (reported_by_user_id) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (approved_by_user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (recorded_customer_id) REFERENCES customers(id) ON DELETE SET NULL,
    FOREIGN KEY (actual_customer_id) REFERENCES customers(id) ON DELETE SET NULL,

    INDEX idx_unit_id (unit_id),
    INDEX idx_status (status),
    INDEX idx_reported_by (reported_by_user_id),
    INDEX idx_approved_by (approved_by_user_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
