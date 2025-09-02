-- Tabel untuk tracking migration yang telah dijalankan
CREATE TABLE IF NOT EXISTS migration_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    migration_name VARCHAR(255) NOT NULL,
    executed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    description TEXT,
    status ENUM('SUCCESS', 'FAILED', 'ROLLBACK') DEFAULT 'SUCCESS',
    error_message TEXT,
    INDEX idx_migration_name (migration_name),
    INDEX idx_executed_at (executed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
