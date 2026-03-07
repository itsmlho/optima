-- ═══════════════════════════════════════════════════════════════
-- EXAMPLE: Grant View-Only Access Warehouse Inventory Unit
--          untuk Admin Marketing (User ID: 5)
-- ═══════════════════════════════════════════════════════════════
-- 
-- Scenario:
-- - Role Default: Admin Marketing hanya akses module Marketing
-- - Custom Grant: Bisa VIEW Warehouse → Inventory Unit (read-only)
-- - Custom Grant: Bisa PRINT laporan inventory
-- - Custom DENY: Tidak bisa Create/Edit/Delete
-- 
-- Behavior Tombol:
-- ✅ Tombol Print      → MUNCUL (karena ada permission)
-- ❌ Tombol Tambah     → DISABLED dengan tooltip (show disabled)
-- ❌ Tombol Edit       → DISABLED dengan tooltip  
-- ❌ Tombol Hapus      → DISABLED dengan tooltip
-- ❌ Tombol Export     → HIDDEN (export default hidden jika tidak ada permission)
-- ═══════════════════════════════════════════════════════════════

-- STEP 1: Pastikan permissions untuk Warehouse Inventory Unit sudah ada
INSERT INTO permissions (module, page, action, key_name, display_name, description)
VALUES 
    ('warehouse', 'inventory_unit', 'navigation', 'warehouse.inventory_unit.navigation', 'Warehouse - Inventory Unit - Navigation', 'Akses menu Inventory Unit'),
    ('warehouse', 'inventory_unit', 'view', 'warehouse.inventory_unit.view', 'Warehouse - Inventory Unit - View', 'Lihat data inventory unit'),
    ('warehouse', 'inventory_unit', 'create', 'warehouse.inventory_unit.create', 'Warehouse - Inventory Unit - Create', 'Tambah data inventory unit'),
    ('warehouse', 'inventory_unit', 'edit', 'warehouse.inventory_unit.edit', 'Warehouse - Inventory Unit - Edit', 'Edit data inventory unit'),
    ('warehouse', 'inventory_unit', 'delete', 'warehouse.inventory_unit.delete', 'Warehouse - Inventory Unit - Delete', 'Hapus data inventory unit'),
    ('warehouse', 'inventory_unit', 'print', 'warehouse.inventory_unit.print', 'Warehouse - Inventory Unit - Print', 'Print laporan inventory'),
    ('warehouse', 'inventory_unit', 'export', 'warehouse.inventory_unit.export', 'Warehouse - Inventory Unit - Export', 'Export data inventory')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name);

-- STEP 2: Grant User-Specific Permissions untuk User ID 5 (Admin Marketing)
-- Ganti user_id = 5 dengan ID user yang sebenarnya!

-- 2a. Grant NAVIGATION (menu muncul di sidebar)
INSERT INTO user_permissions (user_id, permission_id, granted, reason, assigned_by, assigned_at)
SELECT 
    5, -- USER ID Admin Marketing (GANTI SESUAI DATABASE!)
    p.id,
    1, -- Granted = 1 (allow)
    'Cross-module access untuk koordinasi dengan warehouse',
    1, -- Assigned by (USER ID yang grant, biasanya Super Admin)
    NOW()
FROM permissions p
WHERE p.key_name = 'warehouse.inventory_unit.navigation'
ON DUPLICATE KEY UPDATE granted = 1, reason = VALUES(reason);

-- 2b. Grant VIEW (bisa buka halaman dan lihat data)
INSERT INTO user_permissions (user_id, permission_id, granted, reason, assigned_by, assigned_at)
SELECT 
    5,
    p.id,
    1,
    'Read-only access untuk monitoring stock unit',
    1,
    NOW()
FROM permissions p
WHERE p.key_name = 'warehouse.inventory_unit.view'
ON DUPLICATE KEY UPDATE granted = 1, reason = VALUES(reason);

-- 2c. Grant PRINT (bisa print laporan)
INSERT INTO user_permissions (user_id, permission_id, granted, reason, assigned_by, assigned_at)
SELECT 
    5,
    p.id,
    1,
    'Bisa print laporan untuk koordinasi marketing campaign',
    1,
    NOW()
FROM permissions p
WHERE p.key_name = 'warehouse.inventory_unit.print'
ON DUPLICATE KEY UPDATE granted = 1, reason = VALUES(reason);

-- 2d. DENY CREATE (explicitly revoke, meskipun role tidak punya)
-- Granted = 0 artinya EXPLICITLY DENY (lebih kuat dari role grant)
INSERT INTO user_permissions (user_id, permission_id, granted, reason, assigned_by, assigned_at)
SELECT 
    5,
    p.id,
    0, -- Granted = 0 (DENY)
    'Read-only user, tidak boleh tambah data',
    1,
    NOW()
FROM permissions p
WHERE p.key_name = 'warehouse.inventory_unit.create'
ON DUPLICATE KEY UPDATE granted = 0, reason = VALUES(reason);

-- 2e. DENY EDIT
INSERT INTO user_permissions (user_id, permission_id, granted, reason, assigned_by, assigned_at)
SELECT 
    5,
    p.id,
    0,
    'Read-only user, tidak boleh edit data',
    1,
    NOW()
FROM permissions p
WHERE p.key_name = 'warehouse.inventory_unit.edit'
ON DUPLICATE KEY UPDATE granted = 0, reason = VALUES(reason);

-- 2f. DENY DELETE
INSERT INTO user_permissions (user_id, permission_id, granted, reason, assigned_by, assigned_at)
SELECT 
    5,
    p.id,
    0,
    'Read-only user, tidak boleh hapus data',
    1,
    NOW()
FROM permissions p
WHERE p.key_name = 'warehouse.inventory_unit.delete'
ON DUPLICATE KEY UPDATE granted = 0, reason = VALUES(reason);

-- 2g. DENY EXPORT (tidak kasih akses export)
INSERT INTO user_permissions (user_id, permission_id, granted, reason, assigned_by, assigned_at)
SELECT 
    5,
    p.id,
    0,
    'Export restricted untuk data security',
    1,
    NOW()
FROM permissions p
WHERE p.key_name = 'warehouse.inventory_unit.export'
ON DUPLICATE KEY UPDATE granted = 0, reason = VALUES(reason);

-- ═══════════════════════════════════════════════════════════════
-- VERIFICATION QUERIES
-- ═══════════════════════════════════════════════════════════════

-- Check user permissions untuk User ID 5
SELECT 
    p.key_name,
    p.display_name,
    CASE 
        WHEN up.granted = 1 THEN '✅ GRANTED'
        WHEN up.granted = 0 THEN '❌ DENIED'
        ELSE '⚪ NOT SET (fallback to role)'
    END as status,
    up.reason,
    up.assigned_at
FROM user_permissions up
INNER JOIN permissions p ON up.permission_id = p.id
WHERE up.user_id = 5
AND p.key_name LIKE 'warehouse.inventory_unit.%'
ORDER BY p.key_name;

-- Expected Result:
-- | key_name                                | status    | reason                            |
-- |-----------------------------------------|-----------|-----------------------------------|
-- | warehouse.inventory_unit.navigation     | ✅ GRANTED | Cross-module access...            |
-- | warehouse.inventory_unit.view           | ✅ GRANTED | Read-only access...               |
-- | warehouse.inventory_unit.print          | ✅ GRANTED | Bisa print laporan...             |
-- | warehouse.inventory_unit.create         | ❌ DENIED  | Read-only user...                 |
-- | warehouse.inventory_unit.edit           | ❌ DENIED  | Read-only user...                 |
-- | warehouse.inventory_unit.delete         | ❌ DENIED  | Read-only user...                 |
-- | warehouse.inventory_unit.export         | ❌ DENIED  | Export restricted...              |

-- ═══════════════════════════════════════════════════════════════
-- USAGE IN VIEW (Contoh implementasi di halaman)
-- ═══════════════════════════════════════════════════════════════
/*
<!-- Tombol Print - MUNCUL karena ada permission -->
<?= renderPrintButton('warehouse.inventory_unit.print', 'printInventory()') ?>

<!-- Tombol Tambah - DISABLED dengan tooltip (tidak bisa klik) -->
<?= renderCreateButton('warehouse.inventory_unit.create', 'addNewUnit()') ?>

<!-- Tombol Edit - DISABLED dengan tooltip -->
<?= renderEditButton('warehouse.inventory_unit.edit', 'editUnit(123)') ?>

<!-- Tombol Hapus - DISABLED dengan tooltip -->
<?= renderDeleteButton('warehouse.inventory_unit.delete', 'deleteUnit(123)') ?>

<!-- Tombol Export - HIDDEN (tidak muncul sama sekali) -->
<?= renderExportButton('warehouse.inventory_unit.export', 'exportData()') ?>
*/

-- ═══════════════════════════════════════════════════════════════
-- TEMPORARY PERMISSION (Optional - Contoh untuk permission sementara)
-- ═══════════════════════════════════════════════════════════════
/*
-- Misal: Kasih akses EDIT untuk 7 hari saja (training period)
INSERT INTO user_permissions (user_id, permission_id, granted, reason, assigned_by, expires_at, is_temporary)
SELECT 
    5,
    p.id,
    1,
    'Temporary access for training - 7 days',
    1,
    DATE_ADD(NOW(), INTERVAL 7 DAY), -- Expired after 7 days
    1 -- is_temporary flag
FROM permissions p
WHERE p.key_name = 'warehouse.inventory_unit.edit'
ON DUPLICATE KEY UPDATE granted = 1, expires_at = VALUES(expires_at), is_temporary = 1;

-- Auto cleanup dengan cron job:
-- php spark cron:cleanup-permissions (jalankan cleanupExpiredPermissions() helper)
*/
