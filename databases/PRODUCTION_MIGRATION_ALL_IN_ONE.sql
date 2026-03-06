-- ============================================================
-- PRODUCTION MIGRATION - ALL IN ONE
-- Database: u138256737_optima_db
-- Date: March 6, 2026
-- Purpose: Sync production database with development
-- ============================================================
-- 
-- CARA PAKAI:
-- 1. Backup production database dulu (Export SQL)
-- 2. Copy semua SQL ini
-- 3. Paste di phpMyAdmin SQL tab
-- 4. Klik "Go"
-- 5. Verify hasil dengan verification queries di bawah
--
-- ============================================================

-- Set database (pastikan database benar)
USE u138256737_optima_db;

-- ============================================================
-- MIGRATION 1: Add customer_location_id to kontrak_unit
-- ============================================================

-- Step 1: Add customer_location_id column
SET @dbname = DATABASE();
SET @tablename = 'kontrak_unit';
SET @columnname = 'customer_location_id';
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
     WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) = 0,
    'ALTER TABLE kontrak_unit ADD COLUMN customer_location_id INT UNSIGNED NULL COMMENT ''Lokasi/titik penempatan unit dalam kontrak''',
    'SELECT ''Column customer_location_id already exists'' as status'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 2: Add foreign key constraint
SET @constraintname = 'fk_kontrak_unit_location';
SET @sql_fk = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
     WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND CONSTRAINT_NAME = @constraintname) = 0,
    'ALTER TABLE kontrak_unit ADD CONSTRAINT fk_kontrak_unit_location FOREIGN KEY (customer_location_id) REFERENCES customer_locations(id) ON DELETE SET NULL ON UPDATE CASCADE',
    'SELECT ''FK fk_kontrak_unit_location already exists'' as status'
));
PREPARE stmt FROM @sql_fk;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 3: Add index
CREATE INDEX IF NOT EXISTS idx_kontrak_unit_location ON kontrak_unit(customer_location_id);

SELECT '✅ Migration 1 complete: customer_location_id added to kontrak_unit' as status;

-- ============================================================
-- MIGRATION 2: Contract Model Restructure
-- ============================================================

-- Step 1: Ubah contract_id di invoices jadi nullable
ALTER TABLE invoices MODIFY COLUMN contract_id INT NULL;

-- Step 2: Tambah po_reference di invoices
SET @sql_po = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
     WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'po_reference') = 0,
    'ALTER TABLE invoices ADD COLUMN po_reference VARCHAR(100) NULL COMMENT ''Nomor PO dari customer''',
    'SELECT ''Column po_reference already exists'' as status'
));
PREPARE stmt FROM @sql_po;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 3: Ubah nilai_total di kontrak jadi nullable
ALTER TABLE kontrak MODIFY COLUMN nilai_total DECIMAL(15,2) NULL DEFAULT NULL 
  COMMENT 'Legacy: nilai real dihitung dinamis dari kontrak_unit JOIN inventory_unit';

-- Step 4: Add indexes
CREATE INDEX IF NOT EXISTS idx_invoices_po_reference ON invoices(po_reference);
CREATE INDEX IF NOT EXISTS idx_invoices_contract_id ON invoices(contract_id);

SELECT '✅ Migration 2 complete: Contract model restructured' as status;

-- ============================================================
-- MIGRATION 3: Add harga_sewa & is_spare to kontrak_unit
-- ============================================================

-- Step 1: Add harga_sewa column
SET @sql_harga = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
     WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'kontrak_unit' AND COLUMN_NAME = 'harga_sewa') = 0,
    'ALTER TABLE kontrak_unit ADD COLUMN harga_sewa DECIMAL(15,2) DEFAULT NULL COMMENT ''Override harga sewa per unit per kontrak'' AFTER unit_id',
    'SELECT ''Column harga_sewa already exists'' as status'
));
PREPARE stmt FROM @sql_harga;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 2: Add is_spare flag
SET @sql_spare = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
     WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'kontrak_unit' AND COLUMN_NAME = 'is_spare') = 0,
    'ALTER TABLE kontrak_unit ADD COLUMN is_spare TINYINT(1) NOT NULL DEFAULT 0 COMMENT ''Flag unit spare/backup'' AFTER harga_sewa',
    'SELECT ''Column is_spare already exists'' as status'
));
PREPARE stmt FROM @sql_spare;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 3: Backfill kontrak.customer_id from customer_locations
UPDATE kontrak k
JOIN customer_locations cl ON k.customer_location_id = cl.id
SET k.customer_id = cl.customer_id
WHERE k.customer_id IS NULL AND cl.customer_id IS NOT NULL;

SELECT '✅ Migration 3 complete: harga_sewa & is_spare added' as status;

-- ============================================================
-- MIGRATION 4: Create unit_audit_requests table
-- ============================================================

CREATE TABLE IF NOT EXISTS unit_audit_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    unit_id INT UNSIGNED NOT NULL,
    reported_by_user_id INT NOT NULL,
    approved_by_user_id INT DEFAULT NULL,

    -- Lokasi yang tercatat di sistem
    recorded_location VARCHAR(255) DEFAULT NULL,
    recorded_status VARCHAR(50) DEFAULT NULL,
    recorded_customer_id INT DEFAULT NULL,
    recorded_customer_name VARCHAR(255) DEFAULT NULL,
    recorded_kontrak_id INT DEFAULT NULL,

    -- Lokasi aktual di lapangan
    actual_location VARCHAR(255) NOT NULL,
    actual_customer_id INT DEFAULT NULL,
    actual_customer_name VARCHAR(255) DEFAULT NULL,
    actual_notes TEXT DEFAULT NULL,

    -- Request type
    request_type ENUM('LOCATION_MISMATCH', 'STATUS_MISMATCH', 'DAMAGE_REPORT', 'OTHER') DEFAULT 'LOCATION_MISMATCH',

    -- Status
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

SELECT '✅ Migration 4 complete: unit_audit_requests table created' as status;

-- ============================================================
-- MIGRATION 5: Create unit_movements table
-- ============================================================

CREATE TABLE IF NOT EXISTS unit_movements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    movement_number VARCHAR(50) NOT NULL UNIQUE,

    -- Unit yang dipindahkan
    unit_id INT UNSIGNED DEFAULT NULL,
    component_id INT DEFAULT NULL,
    component_type ENUM('FORKLIFT', 'ATTACHMENT', 'CHARGER', 'BATTERY') DEFAULT 'FORKLIFT',

    -- Asal dan Tujuan
    origin_location VARCHAR(100) NOT NULL,
    destination_location VARCHAR(100) NOT NULL,
    origin_type ENUM('POS_1', 'POS_2', 'POS_3', 'POS_4', 'POS_5', 'CUSTOMER_SITE', 'WAREHOUSE', 'OTHER') DEFAULT 'POS_1',
    destination_type ENUM('POS_1', 'POS_2', 'POS_3', 'POS_4', 'POS_5', 'CUSTOMER_SITE', 'WAREHOUSE', 'OTHER') DEFAULT 'POS_1',

    -- Detail perpindahan
    movement_date DATETIME NOT NULL,
    driver_name VARCHAR(100) DEFAULT NULL,
    vehicle_number VARCHAR(50) DEFAULT NULL,
    notes TEXT DEFAULT NULL,

    -- Surat Jalan
    surat_jalan_number VARCHAR(50) DEFAULT NULL,

    -- Status
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

SELECT '✅ Migration 5 complete: unit_movements table created' as status;

-- ============================================================
-- DATA POPULATION: Populate customer_location_id
-- ============================================================

-- Copy customer_location_id dari kontrak ke kontrak_unit
UPDATE kontrak_unit ku
INNER JOIN kontrak k ON ku.kontrak_id = k.id
SET ku.customer_location_id = k.customer_location_id
WHERE ku.customer_location_id IS NULL
  AND k.customer_location_id IS NOT NULL;

SELECT '✅ Data population complete: customer_location_id populated in kontrak_unit' as status;

-- ============================================================
-- VERIFICATION QUERIES
-- ============================================================

-- 1. Check kontrak_unit.customer_location_id exists
SELECT 
    '1. kontrak_unit.customer_location_id column' as check_name,
    CASE WHEN COUNT(*) > 0 THEN '✅ EXISTS' ELSE '❌ NOT FOUND' END as result
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'kontrak_unit' 
  AND COLUMN_NAME = 'customer_location_id';

-- 2. Check kontrak_unit.harga_sewa exists
SELECT 
    '2. kontrak_unit.harga_sewa column' as check_name,
    CASE WHEN COUNT(*) > 0 THEN '✅ EXISTS' ELSE '❌ NOT FOUND' END as result
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'kontrak_unit' 
  AND COLUMN_NAME = 'harga_sewa';

-- 3. Check kontrak_unit.is_spare exists
SELECT 
    '3. kontrak_unit.is_spare column' as check_name,
    CASE WHEN COUNT(*) > 0 THEN '✅ EXISTS' ELSE '❌ NOT FOUND' END as result
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'kontrak_unit' 
  AND COLUMN_NAME = 'is_spare';

-- 4. Check new tables created
SELECT 
    '4. unit_audit_requests table' as check_name,
    CASE WHEN COUNT(*) > 0 THEN '✅ EXISTS' ELSE '❌ NOT FOUND' END as result
FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'unit_audit_requests';

SELECT 
    '5. unit_movements table' as check_name,
    CASE WHEN COUNT(*) > 0 THEN '✅ EXISTS' ELSE '❌ NOT FOUND' END as result
FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'unit_movements';

-- 6. Check data populated
SELECT 
    '6. Data population' as check_name,
    CONCAT(
        '✅ ', 
        COUNT(*), 
        ' kontrak_unit records have customer_location_id'
    ) as result
FROM kontrak_unit 
WHERE customer_location_id IS NOT NULL;

-- 7. Summary
SELECT 
    'MIGRATION SUMMARY' as info,
    (SELECT COUNT(*) FROM kontrak_unit WHERE customer_location_id IS NOT NULL) as units_with_location,
    (SELECT COUNT(*) FROM unit_audit_requests) as audit_requests,
    (SELECT COUNT(*) FROM unit_movements) as movements;

-- ============================================================
-- ✅ MIGRATION COMPLETE!
-- ============================================================
-- 
-- Next steps:
-- 1. Verify all checks show ✅ 
-- 2. Update code files di production (git pull)
-- 3. Test pages:
--    - /marketing/kontrak/edit/1 (customer disabled, location removed)
--    - /service/unit-audit (new page)
--    - /warehouse/movements (new page)
--
-- ============================================================
