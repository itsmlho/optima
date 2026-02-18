-- ============================================================================
-- CONTRACT & UNIT DATA IMPORT
-- Generated: 2026-02-17 13:56:18
-- Total contracts: 363
-- ============================================================================

USE optima_ci;

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

-- ====================================================================================
-- Contract: No. 02/PJB/AGN-SML/VI/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-06-19 to 
-- Units: 12
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'No. 02/PJB/AGN-SML/VI/2025',
    'CONTRACT',
    '2025-06-19',
    '',
    'ACTIVE',
    'BULANAN',
    12,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5687: Admira Garuda Nusantara - Palembang (Rp39.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 39000000,
    customer_id = 3,
    customer_location_id = 3,
    kontrak_id = @kontrak_id
WHERE no_unit = 5687;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-19',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5687
LIMIT 1;

-- Unit 5688: Admira Garuda Nusantara - Palembang (Rp39.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 39000000,
    customer_id = 3,
    customer_location_id = 3,
    kontrak_id = @kontrak_id
WHERE no_unit = 5688;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-19',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5688
LIMIT 1;

-- Unit 5689: Admira Garuda Nusantara - Palembang (Rp39.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 39000000,
    customer_id = 3,
    customer_location_id = 3,
    kontrak_id = @kontrak_id
WHERE no_unit = 5689;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-19',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5689
LIMIT 1;

-- Unit 5690: Admira Garuda Nusantara - Palembang (Rp39.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 39000000,
    customer_id = 3,
    customer_location_id = 3,
    kontrak_id = @kontrak_id
WHERE no_unit = 5690;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-19',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5690
LIMIT 1;

-- Unit 5692: Admira Garuda Nusantara - Palembang (Rp39.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 39000000,
    customer_id = 3,
    customer_location_id = 3,
    kontrak_id = @kontrak_id
WHERE no_unit = 5692;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-19',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5692
LIMIT 1;

-- Unit 5693: Admira Garuda Nusantara - Palembang (Rp39.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 39000000,
    customer_id = 3,
    customer_location_id = 3,
    kontrak_id = @kontrak_id
WHERE no_unit = 5693;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-19',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5693
LIMIT 1;

-- Unit 5914: Admira Garuda Nusantara - Palembang (Rp39.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 39000000,
    customer_id = 3,
    customer_location_id = 3,
    kontrak_id = @kontrak_id
WHERE no_unit = 5914;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-09-03',
    '2028-02-09',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5914
LIMIT 1;

-- Unit 5915: Admira Garuda Nusantara - Palembang (Rp39.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 39000000,
    customer_id = 3,
    customer_location_id = 3,
    kontrak_id = @kontrak_id
WHERE no_unit = 5915;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-09-03',
    '2028-02-09',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5915
LIMIT 1;

-- Unit 5691: Admira Garuda Nusantara - Palembang (Rp39.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 39000000,
    customer_id = 3,
    customer_location_id = 3,
    kontrak_id = @kontrak_id
WHERE no_unit = 5691;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-19',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5691
LIMIT 1;

-- Unit 5694: Admira Garuda Nusantara - Palembang (Rp39.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 39000000,
    customer_id = 3,
    customer_location_id = 3,
    kontrak_id = @kontrak_id
WHERE no_unit = 5694;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-19',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5694
LIMIT 1;

-- Unit 5695: Admira Garuda Nusantara - Palembang (Rp39.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 39000000,
    customer_id = 3,
    customer_location_id = 3,
    kontrak_id = @kontrak_id
WHERE no_unit = 5695;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-19',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5695
LIMIT 1;

-- Unit 5696: Admira Garuda Nusantara - Palembang (Rp39.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 39000000,
    customer_id = 3,
    customer_location_id = 3,
    kontrak_id = @kontrak_id
WHERE no_unit = 5696;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-19',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5696
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    3,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: AMD I - 060/LGL-0066/PTIL/WHS-C3/III/2022
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2021-01-06 to 
-- Units: 6
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'AMD I - 060/LGL-0066/PTIL/WHS-C3/III/2022',
    'CONTRACT',
    '2021-01-06',
    '',
    'ACTIVE',
    'BULANAN',
    6,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 2968: AIBM - Indolakto - Curug (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 51,
    customer_location_id = 169,
    kontrak_id = @kontrak_id
WHERE no_unit = 2968;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2021-01-06',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2968
LIMIT 1;

-- Unit 3004: AIBM - Indolakto - Curug (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 51,
    customer_location_id = 169,
    kontrak_id = @kontrak_id
WHERE no_unit = 3004;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2021-01-06',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3004
LIMIT 1;

-- Unit 2828: AIBM - Indolakto - Curug (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 51,
    customer_location_id = 169,
    kontrak_id = @kontrak_id
WHERE no_unit = 2828;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2021-01-06',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2828
LIMIT 1;

-- Unit 2969: AIBM - Indolakto - Curug (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 51,
    customer_location_id = 169,
    kontrak_id = @kontrak_id
WHERE no_unit = 2969;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2021-01-06',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2969
LIMIT 1;

-- Unit 2970: AIBM - Indolakto - Curug (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 51,
    customer_location_id = 169,
    kontrak_id = @kontrak_id
WHERE no_unit = 2970;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2021-01-06',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2970
LIMIT 1;

-- Unit 2971: AIBM - Indolakto - Curug (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 51,
    customer_location_id = 169,
    kontrak_id = @kontrak_id
WHERE no_unit = 2971;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2021-01-06',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2971
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    51,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4212029724
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-01-01 to 2025-31-12
-- Units: 37
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4212029724',
    'PO_ONLY',
    '2025-01-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    37,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3548: AMERTA INDAH OTSUKA - CICURUG (Rp12.050.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12050000,
    customer_id = 4,
    customer_location_id = 5,
    kontrak_id = @kontrak_id
WHERE no_unit = 3548;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3548
LIMIT 1;

-- Unit 3554: AMERTA INDAH OTSUKA - CICURUG (Rp11.850.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11850000,
    customer_id = 4,
    customer_location_id = 5,
    kontrak_id = @kontrak_id
WHERE no_unit = 3554;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3554
LIMIT 1;

-- Unit 3588: AMERTA INDAH OTSUKA - CICURUG (Rp12.050.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12050000,
    customer_id = 4,
    customer_location_id = 5,
    kontrak_id = @kontrak_id
WHERE no_unit = 3588;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3588
LIMIT 1;

-- Unit 3589: AMERTA INDAH OTSUKA - CICURUG (Rp12.050.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12050000,
    customer_id = 4,
    customer_location_id = 5,
    kontrak_id = @kontrak_id
WHERE no_unit = 3589;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3589
LIMIT 1;

-- Unit 3590: AMERTA INDAH OTSUKA - CICURUG (Rp11.850.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11850000,
    customer_id = 4,
    customer_location_id = 5,
    kontrak_id = @kontrak_id
WHERE no_unit = 3590;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3590
LIMIT 1;

-- Unit 3591: AMERTA INDAH OTSUKA - CICURUG (Rp11.850.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11850000,
    customer_id = 4,
    customer_location_id = 5,
    kontrak_id = @kontrak_id
WHERE no_unit = 3591;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3591
LIMIT 1;

-- Unit 3801: AMERTA INDAH OTSUKA - CICURUG (Rp11.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11000000,
    customer_id = 4,
    customer_location_id = 5,
    kontrak_id = @kontrak_id
WHERE no_unit = 3801;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3801
LIMIT 1;

-- Unit 3802: AMERTA INDAH OTSUKA - CICURUG (Rp10.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10800000,
    customer_id = 4,
    customer_location_id = 5,
    kontrak_id = @kontrak_id
WHERE no_unit = 3802;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3802
LIMIT 1;

-- Unit 3803: AMERTA INDAH OTSUKA - CICURUG (Rp11.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11000000,
    customer_id = 4,
    customer_location_id = 5,
    kontrak_id = @kontrak_id
WHERE no_unit = 3803;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3803
LIMIT 1;

-- Unit 3804: AMERTA INDAH OTSUKA - CICURUG (Rp11.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11000000,
    customer_id = 4,
    customer_location_id = 5,
    kontrak_id = @kontrak_id
WHERE no_unit = 3804;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3804
LIMIT 1;

-- Unit 3912: AMERTA INDAH OTSUKA - CICURUG (Rp11.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11000000,
    customer_id = 4,
    customer_location_id = 5,
    kontrak_id = @kontrak_id
WHERE no_unit = 3912;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3912
LIMIT 1;

-- Unit 3913: AMERTA INDAH OTSUKA - CICURUG (Rp11.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11000000,
    customer_id = 4,
    customer_location_id = 5,
    kontrak_id = @kontrak_id
WHERE no_unit = 3913;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3913
LIMIT 1;

-- Unit 3914: AMERTA INDAH OTSUKA - CICURUG (Rp11.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11000000,
    customer_id = 4,
    customer_location_id = 5,
    kontrak_id = @kontrak_id
WHERE no_unit = 3914;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3914
LIMIT 1;

-- Unit 3915: AMERTA INDAH OTSUKA - CICURUG (Rp11.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11000000,
    customer_id = 4,
    customer_location_id = 5,
    kontrak_id = @kontrak_id
WHERE no_unit = 3915;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3915
LIMIT 1;

-- Unit 3916: AMERTA INDAH OTSUKA - CICURUG (Rp11.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11000000,
    customer_id = 4,
    customer_location_id = 5,
    kontrak_id = @kontrak_id
WHERE no_unit = 3916;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3916
LIMIT 1;

-- Unit 3917: AMERTA INDAH OTSUKA - CICURUG (Rp10.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10800000,
    customer_id = 4,
    customer_location_id = 5,
    kontrak_id = @kontrak_id
WHERE no_unit = 3917;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3917
LIMIT 1;

-- Unit 3918: AMERTA INDAH OTSUKA - SENTUL 1 (Rp10.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10800000,
    customer_id = 4,
    customer_location_id = 42,
    kontrak_id = @kontrak_id
WHERE no_unit = 3918;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3918
LIMIT 1;

-- Unit 3919: AMERTA INDAH OTSUKA - CICURUG (Rp11.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11000000,
    customer_id = 4,
    customer_location_id = 5,
    kontrak_id = @kontrak_id
WHERE no_unit = 3919;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3919
LIMIT 1;

-- Unit 3920: AMERTA INDAH OTSUKA - CICURUG (Rp11.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11000000,
    customer_id = 4,
    customer_location_id = 5,
    kontrak_id = @kontrak_id
WHERE no_unit = 3920;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3920
LIMIT 1;

-- Unit 3921: AMERTA INDAH OTSUKA - CICURUG (Rp11.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11000000,
    customer_id = 4,
    customer_location_id = 5,
    kontrak_id = @kontrak_id
WHERE no_unit = 3921;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3921
LIMIT 1;

-- Unit 3986: AMERTA INDAH OTSUKA - CICURUG (Rp11.850.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11850000,
    customer_id = 4,
    customer_location_id = 5,
    kontrak_id = @kontrak_id
WHERE no_unit = 3986;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3986
LIMIT 1;

-- Unit 3988: AMERTA INDAH OTSUKA - CICURUG (Rp11.850.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11850000,
    customer_id = 4,
    customer_location_id = 5,
    kontrak_id = @kontrak_id
WHERE no_unit = 3988;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3988
LIMIT 1;

-- Unit 3989: AMERTA INDAH OTSUKA - SENTUL 2 (Rp11.850.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11850000,
    customer_id = 4,
    customer_location_id = 43,
    kontrak_id = @kontrak_id
WHERE no_unit = 3989;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3989
LIMIT 1;

-- Unit 3990: AMERTA INDAH OTSUKA - CICURUG (Rp11.850.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11850000,
    customer_id = 4,
    customer_location_id = 5,
    kontrak_id = @kontrak_id
WHERE no_unit = 3990;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3990
LIMIT 1;

-- Unit 3991: AMERTA INDAH OTSUKA - CICURUG (Rp10.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10800000,
    customer_id = 4,
    customer_location_id = 5,
    kontrak_id = @kontrak_id
WHERE no_unit = 3991;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3991
LIMIT 1;

-- Unit 3992: AMERTA INDAH OTSUKA - CICURUG (Rp11.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11000000,
    customer_id = 4,
    customer_location_id = 5,
    kontrak_id = @kontrak_id
WHERE no_unit = 3992;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3992
LIMIT 1;

-- Unit 3993: AMERTA INDAH OTSUKA - CICURUG (Rp11.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11000000,
    customer_id = 4,
    customer_location_id = 5,
    kontrak_id = @kontrak_id
WHERE no_unit = 3993;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3993
LIMIT 1;

-- Unit 3995: AMERTA INDAH OTSUKA - CICURUG (Rp11.850.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11850000,
    customer_id = 4,
    customer_location_id = 5,
    kontrak_id = @kontrak_id
WHERE no_unit = 3995;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3995
LIMIT 1;

-- Unit 5122: AMERTA INDAH OTSUKA - LDC PASAR REBO (Rp22.030.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 22030000,
    customer_id = 4,
    customer_location_id = 4,
    kontrak_id = @kontrak_id
WHERE no_unit = 5122;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5122
LIMIT 1;

-- Unit 5123: AMERTA INDAH OTSUKA - LDC BEKASI (Rp22.030.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 22030000,
    customer_id = 4,
    customer_location_id = 20,
    kontrak_id = @kontrak_id
WHERE no_unit = 5123;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5123
LIMIT 1;

-- Unit 5124: AMERTA INDAH OTSUKA - WH-B2 FG (Rp22.030.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 22030000,
    customer_id = 4,
    customer_location_id = 18,
    kontrak_id = @kontrak_id
WHERE no_unit = 5124;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5124
LIMIT 1;

-- Unit 5125: AMERTA INDAH OTSUKA - WH-B3 FG (Rp22.030.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 22030000,
    customer_id = 4,
    customer_location_id = 19,
    kontrak_id = @kontrak_id
WHERE no_unit = 5125;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5125
LIMIT 1;

-- ⚠ WARNING: Location 'WH ENXIM RM' for customer 'AMERTA INDAH OTSUKA' not found (unit 5126)
-- Unit 5126: AMERTA INDAH OTSUKA - WH ENXIM RM (Rp21.030.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 21030000,
    customer_id = 4,
    kontrak_id = @kontrak_id
WHERE no_unit = 5126;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5126
LIMIT 1;

-- Unit 5127: AMERTA INDAH OTSUKA - WH-B1 FG (Rp22.030.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 22030000,
    customer_id = 4,
    customer_location_id = 17,
    kontrak_id = @kontrak_id
WHERE no_unit = 5127;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5127
LIMIT 1;

-- Unit 3517: AMERTA INDAH OTSUKA - CICURUG (Rp12.050.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12050000,
    customer_id = 4,
    customer_location_id = 5,
    kontrak_id = @kontrak_id
WHERE no_unit = 3517;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3517
LIMIT 1;

-- Unit 5128: AMERTA INDAH OTSUKA - WH-A RMPM (Rp22.030.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 22030000,
    customer_id = 4,
    customer_location_id = 14,
    kontrak_id = @kontrak_id
WHERE no_unit = 5128;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5128
LIMIT 1;

-- Unit 5129: AMERTA INDAH OTSUKA - WH-C RMPM (Rp22.030.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 22030000,
    customer_id = 4,
    customer_location_id = 16,
    kontrak_id = @kontrak_id
WHERE no_unit = 5129;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5129
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    4,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4212031112
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2026-02-17 to 2027-02-17
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4212031112',
    'PO_ONLY',
    '2026-02-17',
    '2027-02-17',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5130: AMERTA INDAH OTSUKA - Medan (Rp25.780.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 25780000,
    customer_id = 4,
    customer_location_id = 9,
    kontrak_id = @kontrak_id
WHERE no_unit = 5130;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5130
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    4,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4212030004
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-01-01 to 2025-31-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4212030004',
    'PO_ONLY',
    '2025-01-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5153: AMERTA INDAH OTSUKA - Yogyakarta (Rp13.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 13000000,
    customer_id = 4,
    customer_location_id = 44,
    kontrak_id = @kontrak_id
WHERE no_unit = 5153;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5153
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    4,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: ID1/POR/251000000322
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2026-02-17 to 2027-02-17
-- Units: 8
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'ID1/POR/251000000322',
    'CONTRACT',
    '2026-02-17',
    '2027-02-17',
    'ACTIVE',
    'BULANAN',
    8,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 2579: Astro Technologies Indonesia - WH Cibitung (Rp15.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 15500000,
    customer_id = 8,
    customer_location_id = 59,
    kontrak_id = @kontrak_id
WHERE no_unit = 2579;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2579
LIMIT 1;

-- Unit 5307: Astro Technologies Indonesia - WH Cibitung (Rp15.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 15500000,
    customer_id = 8,
    customer_location_id = 59,
    kontrak_id = @kontrak_id
WHERE no_unit = 5307;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5307
LIMIT 1;

-- Unit 5414: Astro Technologies Indonesia - WH Cibitung (Rp15.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 15500000,
    customer_id = 8,
    customer_location_id = 59,
    kontrak_id = @kontrak_id
WHERE no_unit = 5414;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5414
LIMIT 1;

-- Unit 5727: Astro Technologies Indonesia - WH Cibitung (Rp15.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 15500000,
    customer_id = 8,
    customer_location_id = 59,
    kontrak_id = @kontrak_id
WHERE no_unit = 5727;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5727
LIMIT 1;

-- Unit 5892: Astro Technologies Indonesia - WH Cibitung (Rp15.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 15500000,
    customer_id = 8,
    customer_location_id = 59,
    kontrak_id = @kontrak_id
WHERE no_unit = 5892;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5892
LIMIT 1;

-- Unit 2551: Astro Technologies Indonesia - WH Cibitung (Rp15.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 15500000,
    customer_id = 8,
    customer_location_id = 59,
    kontrak_id = @kontrak_id
WHERE no_unit = 2551;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2551
LIMIT 1;

-- Unit 5275: Astro Technologies Indonesia - WH Cibitung (Rp15.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 15500000,
    customer_id = 8,
    customer_location_id = 59,
    kontrak_id = @kontrak_id
WHERE no_unit = 5275;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5275
LIMIT 1;

-- Unit 5404: Astro Technologies Indonesia - WH Cibitung (Rp15.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 15500000,
    customer_id = 8,
    customer_location_id = 59,
    kontrak_id = @kontrak_id
WHERE no_unit = 5404;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5404
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    8,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 119/LEGASTRO/OPS/VII/2025 Add 1
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-04-30 to 
-- Units: 10
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '119/LEGASTRO/OPS/VII/2025 Add 1',
    'CONTRACT',
    '2025-04-30',
    '',
    'ACTIVE',
    'BULANAN',
    10,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5601: Astro Technologies Indonesia - WH Sentul (Rp15.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 15500000,
    customer_id = 8,
    customer_location_id = 60,
    kontrak_id = @kontrak_id
WHERE no_unit = 5601;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-04-30',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5601
LIMIT 1;

-- Unit 5649: Astro Technologies Indonesia - WH Sentul (Rp15.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 15000000,
    customer_id = 8,
    customer_location_id = 60,
    kontrak_id = @kontrak_id
WHERE no_unit = 5649;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-10',
    '2026-09-05',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5649
LIMIT 1;

-- Unit 5660: Astro Technologies Indonesia - WH Sentul (Rp15.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 15000000,
    customer_id = 8,
    customer_location_id = 60,
    kontrak_id = @kontrak_id
WHERE no_unit = 5660;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-22',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5660
LIMIT 1;

-- Unit 5662: Astro Technologies Indonesia - WH Sentul (Rp15.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 15000000,
    customer_id = 8,
    customer_location_id = 60,
    kontrak_id = @kontrak_id
WHERE no_unit = 5662;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-22',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5662
LIMIT 1;

-- ⚠ WARNING: Location 'WH Kosambi' for customer 'Astro Technologies Indonesia' not found (unit 3492)
-- Unit 3492: Astro Technologies Indonesia - WH Kosambi (Rp24.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 24500000,
    customer_id = 8,
    kontrak_id = @kontrak_id
WHERE no_unit = 3492;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-07-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3492
LIMIT 1;

-- ⚠ WARNING: Location 'WH Kosambi' for customer 'Astro Technologies Indonesia' not found (unit 3493)
-- Unit 3493: Astro Technologies Indonesia - WH Kosambi (Rp21.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 21000000,
    customer_id = 8,
    kontrak_id = @kontrak_id
WHERE no_unit = 3493;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-21',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3493
LIMIT 1;

-- Unit 5600: Astro Technologies Indonesia - WH Sentul (Rp15.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 15500000,
    customer_id = 8,
    customer_location_id = 60,
    kontrak_id = @kontrak_id
WHERE no_unit = 5600;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-04-30',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5600
LIMIT 1;

-- Unit 5661: Astro Technologies Indonesia - WH Sentul (Rp15.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 15000000,
    customer_id = 8,
    customer_location_id = 60,
    kontrak_id = @kontrak_id
WHERE no_unit = 5661;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-22',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5661
LIMIT 1;

-- ⚠ WARNING: Location 'WH Kosambi' for customer 'Astro Technologies Indonesia' not found (unit 5671)
-- Unit 5671: Astro Technologies Indonesia - WH Kosambi (Rp21.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 21000000,
    customer_id = 8,
    kontrak_id = @kontrak_id
WHERE no_unit = 5671;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-28',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5671
LIMIT 1;

-- ⚠ WARNING: Location 'WH Kosambi' for customer 'Astro Technologies Indonesia' not found (unit 5672)
-- Unit 5672: Astro Technologies Indonesia - WH Kosambi (Rp21.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 21000000,
    customer_id = 8,
    kontrak_id = @kontrak_id
WHERE no_unit = 5672;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-28',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5672
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    8,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: ID1/POR/251000000321
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-10-23 to 
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'ID1/POR/251000000321',
    'CONTRACT',
    '2025-10-23',
    '',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5997: Astro Technologies Indonesia - WH Cibitung (Rp14.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 14000000,
    customer_id = 8,
    customer_location_id = 59,
    kontrak_id = @kontrak_id
WHERE no_unit = 5997;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-23',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5997
LIMIT 1;

-- Unit 5978: Astro Technologies Indonesia - WH Cibitung (Rp14.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 14000000,
    customer_id = 8,
    customer_location_id = 59,
    kontrak_id = @kontrak_id
WHERE no_unit = 5978;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-23',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5978
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    8,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: POC125004757
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-08-04 to 2026-05-01
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'POC125004757',
    'CONTRACT',
    '2025-08-04',
    '2026-05-01',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3657: Berlina - Jababeka (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 172,
    customer_location_id = 414,
    kontrak_id = @kontrak_id
WHERE no_unit = 3657;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-04',
    '2026-05-01',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3657
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    172,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: POC125004666
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-08-04 to 2026-05-01
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'POC125004666',
    'CONTRACT',
    '2025-08-04',
    '2026-05-01',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3658: Berlina - Jababeka (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 172,
    customer_location_id = 414,
    kontrak_id = @kontrak_id
WHERE no_unit = 3658;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-04',
    '2026-05-01',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3658
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    172,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 204/PURC.CMBP/8/2024
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2024-08-26 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '204/PURC.CMBP/8/2024',
    'CONTRACT',
    '2024-08-26',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 1428: CATUR SENTOSA ANUGRAH - Tangerang (Rp12.900.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12900000,
    customer_id = 17,
    customer_location_id = 75,
    kontrak_id = @kontrak_id
WHERE no_unit = 1428;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-08-26',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1428
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    17,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 203/PURC.CMBP/8/2024
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2024-10-23 to 2025-22-10
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '203/PURC.CMBP/8/2024',
    'CONTRACT',
    '2024-10-23',
    '2025-22-10',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5192: CATUR SENTOSA ANUGRAH - Tangerang (Rp24.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 24000000,
    customer_id = 17,
    customer_location_id = 75,
    kontrak_id = @kontrak_id
WHERE no_unit = 5192;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-10-23',
    '2025-22-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5192
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    17,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 295/PURRC.CMBP/9/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-08-26 to 
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '295/PURRC.CMBP/9/2025',
    'CONTRACT',
    '2025-08-26',
    '',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 1428: Cipta Multi Buana - Tanggerang (Rp6.450.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6450000,
    customer_id = 105,
    customer_location_id = 306,
    kontrak_id = @kontrak_id
WHERE no_unit = 1428;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-26',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1428
LIMIT 1;

-- Unit 5289: Cipta Multi Buana - Tanggerang (Rp6.450.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6450000,
    customer_id = 105,
    customer_location_id = 306,
    kontrak_id = @kontrak_id
WHERE no_unit = 5289;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-26',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5289
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    105,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 294/PURRC.CMBP/9/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-10-01 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '294/PURRC.CMBP/9/2025',
    'CONTRACT',
    '2025-10-01',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5192: Cipta Multi Buana - Tanggerang (Rp24.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 24000000,
    customer_id = 105,
    customer_location_id = 306,
    kontrak_id = @kontrak_id
WHERE no_unit = 5192;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5192
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    105,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: On Prosess
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-10-17 to 2026-16-10
-- Units: 16
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'On Prosess',
    'CONTRACT',
    '2025-10-17',
    '2026-16-10',
    'ACTIVE',
    'BULANAN',
    16,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5941: Cipta Unggul Lintas Samudera - Painan (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 23,
    customer_location_id = 81,
    kontrak_id = @kontrak_id
WHERE no_unit = 5941;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-17',
    '2026-16-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5941
LIMIT 1;

-- Unit 5942: Cipta Unggul Lintas Samudera - Painan (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 23,
    customer_location_id = 81,
    kontrak_id = @kontrak_id
WHERE no_unit = 5942;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-17',
    '2026-16-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5942
LIMIT 1;

-- Unit 5979: Cipta Unggul Lintas Samudera - Painan (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 23,
    customer_location_id = 81,
    kontrak_id = @kontrak_id
WHERE no_unit = 5979;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-17',
    '2026-16-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5979
LIMIT 1;

-- Unit 5980: Cipta Unggul Lintas Samudera - Painan (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 23,
    customer_location_id = 81,
    kontrak_id = @kontrak_id
WHERE no_unit = 5980;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-17',
    '2026-16-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5980
LIMIT 1;

-- Unit 5981: Cipta Unggul Lintas Samudera - Painan (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 23,
    customer_location_id = 81,
    kontrak_id = @kontrak_id
WHERE no_unit = 5981;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-17',
    '2026-16-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5981
LIMIT 1;

-- Unit 5982: Cipta Unggul Lintas Samudera - Painan (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 23,
    customer_location_id = 81,
    kontrak_id = @kontrak_id
WHERE no_unit = 5982;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-17',
    '2026-16-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5982
LIMIT 1;

-- Unit 5983: Cipta Unggul Lintas Samudera - Painan (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 23,
    customer_location_id = 81,
    kontrak_id = @kontrak_id
WHERE no_unit = 5983;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-17',
    '2026-16-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5983
LIMIT 1;

-- Unit 5984: Cipta Unggul Lintas Samudera - Painan (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 23,
    customer_location_id = 81,
    kontrak_id = @kontrak_id
WHERE no_unit = 5984;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-17',
    '2026-16-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5984
LIMIT 1;

-- Unit 6055: Cipta Unggul Lintas Samudera - Painan (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 23,
    customer_location_id = 81,
    kontrak_id = @kontrak_id
WHERE no_unit = 6055;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-17',
    '2026-16-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 6055
LIMIT 1;

-- Unit 6056: Cipta Unggul Lintas Samudera - Painan (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 23,
    customer_location_id = 81,
    kontrak_id = @kontrak_id
WHERE no_unit = 6056;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-17',
    '2026-16-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 6056
LIMIT 1;

-- Unit 6057: Cipta Unggul Lintas Samudera - Painan (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 23,
    customer_location_id = 81,
    kontrak_id = @kontrak_id
WHERE no_unit = 6057;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-17',
    '2026-16-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 6057
LIMIT 1;

-- Unit 6058: Cipta Unggul Lintas Samudera - Painan (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 23,
    customer_location_id = 81,
    kontrak_id = @kontrak_id
WHERE no_unit = 6058;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-17',
    '2026-16-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 6058
LIMIT 1;

-- Unit 6059: Cipta Unggul Lintas Samudera - Painan (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 23,
    customer_location_id = 81,
    kontrak_id = @kontrak_id
WHERE no_unit = 6059;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-17',
    '2026-16-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 6059
LIMIT 1;

-- Unit 6060: Cipta Unggul Lintas Samudera - Painan (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 23,
    customer_location_id = 81,
    kontrak_id = @kontrak_id
WHERE no_unit = 6060;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-17',
    '2026-16-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 6060
LIMIT 1;

-- Unit 5943: Cipta Unggul Lintas Samudera - Painan (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 23,
    customer_location_id = 81,
    kontrak_id = @kontrak_id
WHERE no_unit = 5943;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-17',
    '2026-16-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5943
LIMIT 1;

-- Unit 5944: Cipta Unggul Lintas Samudera - Painan (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 23,
    customer_location_id = 81,
    kontrak_id = @kontrak_id
WHERE no_unit = 5944;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-17',
    '2026-16-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5944
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    23,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: GIT/25/080599
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-07-09 to 2026-07-07
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'GIT/25/080599',
    'CONTRACT',
    '2025-07-09',
    '2026-07-07',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Sidoarjo' for customer 'CJ LOGISTIC SERVICE INDONESIA' not found (unit 2282)
-- Unit 2282: CJ LOGISTIC SERVICE INDONESIA - Sidoarjo (Rp12.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12500000,
    customer_id = 106,
    kontrak_id = @kontrak_id
WHERE no_unit = 2282;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-07-09',
    '2026-07-07',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2282
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    106,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: GIT/24/090514
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2024-10-08 to 2025-07-09
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'GIT/24/090514',
    'CONTRACT',
    '2024-10-08',
    '2025-07-09',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'CJ LOGISTIC SERVICE INDONESIA' not found (unit 5279)
-- Unit 5279: CJ LOGISTIC SERVICE INDONESIA -  (Rp12.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12500000,
    customer_id = 106,
    kontrak_id = @kontrak_id
WHERE no_unit = 5279;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-10-08',
    '2025-07-09',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5279
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    106,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: GIT/25/110929
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-12-15 to 2026-14-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'GIT/25/110929',
    'CONTRACT',
    '2025-12-15',
    '2026-14-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'CJ LOGISTIC SERVICE INDONESIA' not found (unit 5281)
-- Unit 5281: CJ LOGISTIC SERVICE INDONESIA -  (Rp22.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 22000000,
    customer_id = 106,
    kontrak_id = @kontrak_id
WHERE no_unit = 5281;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-15',
    '2026-14-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5281
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    106,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: GIT/25/020145
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-03-23 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'GIT/25/020145',
    'CONTRACT',
    '2025-03-23',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'CJ LOGISTIC SERVICE INDONESIA' not found (unit 5585)
-- Unit 5585: CJ LOGISTIC SERVICE INDONESIA -  (Rp20.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 20000000,
    customer_id = 106,
    kontrak_id = @kontrak_id
WHERE no_unit = 5585;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-03-23',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5585
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    106,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: GIT/25/020146
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-03-23 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'GIT/25/020146',
    'CONTRACT',
    '2025-03-23',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'CJ LOGISTIC SERVICE INDONESIA' not found (unit 5586)
-- Unit 5586: CJ LOGISTIC SERVICE INDONESIA -  (Rp20.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 20000000,
    customer_id = 106,
    kontrak_id = @kontrak_id
WHERE no_unit = 5586;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-03-23',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5586
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    106,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: GIT/25/00583
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-08-02 to 2026-01-08
-- Units: 3
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'GIT/25/00583',
    'CONTRACT',
    '2025-08-02',
    '2026-01-08',
    'ACTIVE',
    'BULANAN',
    3,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'CJ LOGISTIC SERVICE INDONESIA' not found (unit 5738)
-- Unit 5738: CJ LOGISTIC SERVICE INDONESIA -  (Rp6.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6000000,
    customer_id = 106,
    kontrak_id = @kontrak_id
WHERE no_unit = 5738;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-02',
    '2026-01-08',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5738
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'CJ LOGISTIC SERVICE INDONESIA' not found (unit 5739)
-- Unit 5739: CJ LOGISTIC SERVICE INDONESIA -  (Rp22.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 22500000,
    customer_id = 106,
    kontrak_id = @kontrak_id
WHERE no_unit = 5739;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-02',
    '2026-01-08',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5739
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'CJ LOGISTIC SERVICE INDONESIA' not found (unit 6029)
-- Unit 6029: CJ LOGISTIC SERVICE INDONESIA -  (Rp6.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6000000,
    customer_id = 106,
    kontrak_id = @kontrak_id
WHERE no_unit = 6029;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-02',
    '2026-01-08',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 6029
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    106,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 067/SML/01/2026
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2026-01-29 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '067/SML/01/2026',
    'CONTRACT',
    '2026-01-29',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Sukabumi' for customer 'Daesan Makmur Sentosa' not found (unit 2928)
-- Unit 2928: Daesan Makmur Sentosa - Sukabumi (Rp15.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 15000000,
    customer_id = 158,
    kontrak_id = @kontrak_id
WHERE no_unit = 2928;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-01-29',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2928
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    158,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 709/SML/IV/2024
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2024-04-29 to 
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '709/SML/IV/2024',
    'CONTRACT',
    '2024-04-29',
    '',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5121: Daya Cipta Kemasindo - Cibitung (Rp31.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 31500000,
    customer_id = 27,
    customer_location_id = 87,
    kontrak_id = @kontrak_id
WHERE no_unit = 5121;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-04-29',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5121
LIMIT 1;

-- Unit 5118: Daya Cipta Kemasindo - Cibitung (Rp31.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 31500000,
    customer_id = 27,
    customer_location_id = 87,
    kontrak_id = @kontrak_id
WHERE no_unit = 5118;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-04-29',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5118
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    27,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 782/SML/X/2024
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2024-10-30 to 2027-29-10
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '782/SML/X/2024',
    'CONTRACT',
    '2024-10-30',
    '2027-29-10',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5274: Daya Cipta Kemasindo - Karawang (Rp17.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 17000000,
    customer_id = 27,
    customer_location_id = 89,
    kontrak_id = @kontrak_id
WHERE no_unit = 5274;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-10-30',
    '2027-29-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5274
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    27,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 708/SML/IV/2024
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2024-04-29 to 
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '708/SML/IV/2024',
    'CONTRACT',
    '2024-04-29',
    '',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5120: Daya Cipta Kemasindo - Tangerang (Rp31.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 31500000,
    customer_id = 27,
    customer_location_id = 86,
    kontrak_id = @kontrak_id
WHERE no_unit = 5120;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-04-29',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5120
LIMIT 1;

-- Unit 5119: Daya Cipta Kemasindo - Tangerang (Rp31.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 31500000,
    customer_id = 27,
    customer_location_id = 86,
    kontrak_id = @kontrak_id
WHERE no_unit = 5119;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-04-29',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5119
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    27,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 791/SML/XII/2024
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2024-12-04 to 2027-03-12
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '791/SML/XII/2024',
    'CONTRACT',
    '2024-12-04',
    '2027-03-12',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5305: Daya Cipta Kemasindo - Tangerang (Rp16.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 16500000,
    customer_id = 27,
    customer_location_id = 86,
    kontrak_id = @kontrak_id
WHERE no_unit = 5305;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-12-04',
    '2027-03-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5305
LIMIT 1;

-- Unit 5347: Daya Cipta Kemasindo - Tangerang (Rp16.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 16500000,
    customer_id = 27,
    customer_location_id = 86,
    kontrak_id = @kontrak_id
WHERE no_unit = 5347;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-12-18',
    '2027-17-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5347
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    27,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 751/SML/IX/2024
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2024-09-27 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '751/SML/IX/2024',
    'CONTRACT',
    '2024-09-27',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5342: Daya Cipta Kemasindo - Karawang (Rp10.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10000000,
    customer_id = 27,
    customer_location_id = 89,
    kontrak_id = @kontrak_id
WHERE no_unit = 5342;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-09-27',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5342
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    27,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 810/SML/I/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-01-07 to 2028-06-01
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '810/SML/I/2025',
    'CONTRACT',
    '2025-01-07',
    '2028-06-01',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5348: Daya Cipta Kemasindo - Cibitung (Rp16.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 16500000,
    customer_id = 27,
    customer_location_id = 87,
    kontrak_id = @kontrak_id
WHERE no_unit = 5348;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-07',
    '2028-06-01',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5348
LIMIT 1;

-- Unit 5349: Daya Cipta Kemasindo - Cibitung (Rp16.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 16500000,
    customer_id = 27,
    customer_location_id = 87,
    kontrak_id = @kontrak_id
WHERE no_unit = 5349;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-18',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5349
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    27,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 036/SML/VI/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-06-24 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '036/SML/VI/2025',
    'CONTRACT',
    '2025-06-24',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5657: Daya Cipta Kemasindo - Karawang (Rp10.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10000000,
    customer_id = 27,
    customer_location_id = 89,
    kontrak_id = @kontrak_id
WHERE no_unit = 5657;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-24',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5657
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    27,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 82/DSY/1404/25
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-05-06 to 2026-05-05
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '82/DSY/1404/25',
    'CONTRACT',
    '2025-05-06',
    '2026-05-05',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5319: DUTA SENTOS YASA - Marunda (Rp7.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7000000,
    customer_id = 32,
    customer_location_id = 102,
    kontrak_id = @kontrak_id
WHERE no_unit = 5319;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-06',
    '2026-05-05',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5319
LIMIT 1;

-- Unit 5320: DUTA SENTOS YASA - Marunda (Rp7.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7000000,
    customer_id = 32,
    customer_location_id = 102,
    kontrak_id = @kontrak_id
WHERE no_unit = 5320;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-06',
    '2026-05-05',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5320
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    32,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 130/DSY/1106/25
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-06-17 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '130/DSY/1106/25',
    'CONTRACT',
    '2025-06-17',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5648: DUTA SENTOS YASA - Marunda (Rp14.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 14000000,
    customer_id = 32,
    customer_location_id = 102,
    kontrak_id = @kontrak_id
WHERE no_unit = 5648;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-17',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5648
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    32,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 164/DSY/1907/25
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-07-28 to 
-- Units: 3
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '164/DSY/1907/25',
    'CONTRACT',
    '2025-07-28',
    '',
    'ACTIVE',
    'BULANAN',
    3,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5730: DUTA SENTOS YASA - Marunda (Rp4.700.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 4700000,
    customer_id = 32,
    customer_location_id = 102,
    kontrak_id = @kontrak_id
WHERE no_unit = 5730;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-07-28',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5730
LIMIT 1;

-- Unit 5731: DUTA SENTOS YASA - Marunda (Rp4.700.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 4700000,
    customer_id = 32,
    customer_location_id = 102,
    kontrak_id = @kontrak_id
WHERE no_unit = 5731;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-07-28',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5731
LIMIT 1;

-- Unit 5732: DUTA SENTOS YASA - Marunda (Rp4.700.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 4700000,
    customer_id = 32,
    customer_location_id = 102,
    kontrak_id = @kontrak_id
WHERE no_unit = 5732;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-07-28',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5732
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    32,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: DSY/2508/0008
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-08-11 to 2026-11-08
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'DSY/2508/0008',
    'CONTRACT',
    '2025-08-11',
    '2026-11-08',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5762: DUTA SENTOS YASA - Marunda (Rp4.250.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 4250000,
    customer_id = 32,
    customer_location_id = 102,
    kontrak_id = @kontrak_id
WHERE no_unit = 5762;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-11',
    '2026-11-08',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5762
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    32,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: DSY/2509/003
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-09-16 to 
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'DSY/2509/003',
    'CONTRACT',
    '2025-09-16',
    '',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5818: DUTA SENTOS YASA - Marunda (Rp14.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 14000000,
    customer_id = 32,
    customer_location_id = 102,
    kontrak_id = @kontrak_id
WHERE no_unit = 5818;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-09-16',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5818
LIMIT 1;

-- Unit 5819: DUTA SENTOS YASA - Marunda (Rp14.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 14000000,
    customer_id = 32,
    customer_location_id = 102,
    kontrak_id = @kontrak_id
WHERE no_unit = 5819;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-09-16',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5819
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    32,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: DDY/2509/0016
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-10-15 to 2026-15-10
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'DDY/2509/0016',
    'CONTRACT',
    '2025-10-15',
    '2026-15-10',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5891: DUTA SENTOS YASA - Marunda (Rp14.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 14000000,
    customer_id = 32,
    customer_location_id = 102,
    kontrak_id = @kontrak_id
WHERE no_unit = 5891;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-15',
    '2026-15-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5891
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    32,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 3100015897
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-03-11 to 2026-10-02
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '3100015897',
    'PO_ONLY',
    '2025-03-11',
    '2026-10-02',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3138: ENERGI SEJAHTERA MAS - DUMAI (Rp30.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 30000000,
    customer_id = 33,
    customer_location_id = 103,
    kontrak_id = @kontrak_id
WHERE no_unit = 3138;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-03-11',
    '2026-10-02',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3138
LIMIT 1;

-- Unit 3138: Energi Sejahtera Mas - Dumai (Rp30.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 30000000,
    customer_id = 33,
    customer_location_id = 103,
    kontrak_id = @kontrak_id
WHERE no_unit = 3138;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-03',
    '2026-02-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3138
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    33,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 3100016263
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2024-12-21 to 
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '3100016263',
    'PO_ONLY',
    '2024-12-21',
    '',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3402: ENERGI SEJAHTERA MAS - DUMAI (Rp32.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 32500000,
    customer_id = 33,
    customer_location_id = 103,
    kontrak_id = @kontrak_id
WHERE no_unit = 3402;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-12-21',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3402
LIMIT 1;

-- Unit 3525: ENERGI SEJAHTERA MAS - DUMAI (Rp32.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 32500000,
    customer_id = 33,
    customer_location_id = 103,
    kontrak_id = @kontrak_id
WHERE no_unit = 3525;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-12-15',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3525
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    33,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 3500700208
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2026-01-01 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '3500700208',
    'PO_ONLY',
    '2026-01-01',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Cibitung' for customer 'Fajar Paper' not found (unit 5673)
-- Unit 5673: Fajar Paper - Cibitung (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 34,
    kontrak_id = @kontrak_id
WHERE no_unit = 5673;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-01-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5673
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    34,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 33000014444
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2022-10-16 to 2025-15-10
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '33000014444',
    'PO_ONLY',
    '2022-10-16',
    '2025-15-10',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Cibitung' for customer 'Fajar Paper' not found (unit 3176)
-- Unit 3176: Fajar Paper - Cibitung (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 34,
    kontrak_id = @kontrak_id
WHERE no_unit = 3176;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-10-16',
    '2025-15-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3176
LIMIT 1;

-- ⚠ WARNING: Location 'Cibitung' for customer 'Fajar Paper' not found (unit 3193)
-- Unit 3193: Fajar Paper - Cibitung (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 34,
    kontrak_id = @kontrak_id
WHERE no_unit = 3193;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-10-16',
    '2025-15-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3193
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    34,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 0006/PO/FUN/I/2026
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2026-01-27 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '0006/PO/FUN/I/2026',
    'CONTRACT',
    '2026-01-27',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'Fajar Unggul Nusantara' not found (unit 2461)
-- Unit 2461: Fajar Unggul Nusantara -  (Rp11.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11000000,
    customer_id = 155,
    kontrak_id = @kontrak_id
WHERE no_unit = 2461;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-01-27',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2461
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    155,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: No. 055/SML/VII/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-11-01 to 
-- Units: 17
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'No. 055/SML/VII/2025',
    'CONTRACT',
    '2025-11-01',
    '',
    'ACTIVE',
    'BULANAN',
    17,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5388: Godrej Consumer Product Indonesia - Cileungsi (Rp8.600.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8600000,
    customer_id = 38,
    customer_location_id = 111,
    kontrak_id = @kontrak_id
WHERE no_unit = 5388;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5388
LIMIT 1;

-- Unit 5752: Godrej Consumer Product Indonesia - Cileungsi (Rp8.600.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8600000,
    customer_id = 38,
    customer_location_id = 111,
    kontrak_id = @kontrak_id
WHERE no_unit = 5752;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5752
LIMIT 1;

-- Unit 5763: Godrej Consumer Product Indonesia - Cileungsi (Rp10.050.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10050000,
    customer_id = 38,
    customer_location_id = 111,
    kontrak_id = @kontrak_id
WHERE no_unit = 5763;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5763
LIMIT 1;

-- Unit 5764: Godrej Consumer Product Indonesia - Cileungsi (Rp10.050.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10050000,
    customer_id = 38,
    customer_location_id = 111,
    kontrak_id = @kontrak_id
WHERE no_unit = 5764;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5764
LIMIT 1;

-- Unit 5765: Godrej Consumer Product Indonesia - Cileungsi (Rp10.050.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10050000,
    customer_id = 38,
    customer_location_id = 111,
    kontrak_id = @kontrak_id
WHERE no_unit = 5765;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5765
LIMIT 1;

-- Unit 5766: Godrej Consumer Product Indonesia - Cileungsi (Rp10.050.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10050000,
    customer_id = 38,
    customer_location_id = 111,
    kontrak_id = @kontrak_id
WHERE no_unit = 5766;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5766
LIMIT 1;

-- Unit 5767: Godrej Consumer Product Indonesia - Cileungsi (Rp10.050.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10050000,
    customer_id = 38,
    customer_location_id = 111,
    kontrak_id = @kontrak_id
WHERE no_unit = 5767;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5767
LIMIT 1;

-- Unit 5768: Godrej Consumer Product Indonesia - Cileungsi (Rp10.050.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10050000,
    customer_id = 38,
    customer_location_id = 111,
    kontrak_id = @kontrak_id
WHERE no_unit = 5768;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5768
LIMIT 1;

-- Unit 5769: Godrej Consumer Product Indonesia - Cileungsi (Rp10.050.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10050000,
    customer_id = 38,
    customer_location_id = 111,
    kontrak_id = @kontrak_id
WHERE no_unit = 5769;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5769
LIMIT 1;

-- Unit 5770: Godrej Consumer Product Indonesia - Cileungsi (Rp10.050.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10050000,
    customer_id = 38,
    customer_location_id = 111,
    kontrak_id = @kontrak_id
WHERE no_unit = 5770;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5770
LIMIT 1;

-- Unit 5772: Godrej Consumer Product Indonesia - Cileungsi (Rp8.600.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8600000,
    customer_id = 38,
    customer_location_id = 111,
    kontrak_id = @kontrak_id
WHERE no_unit = 5772;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5772
LIMIT 1;

-- Unit 5773: Godrej Consumer Product Indonesia - Cileungsi (Rp8.600.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8600000,
    customer_id = 38,
    customer_location_id = 111,
    kontrak_id = @kontrak_id
WHERE no_unit = 5773;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5773
LIMIT 1;

-- Unit 5774: Godrej Consumer Product Indonesia - Cileungsi (Rp8.600.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8600000,
    customer_id = 38,
    customer_location_id = 111,
    kontrak_id = @kontrak_id
WHERE no_unit = 5774;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5774
LIMIT 1;

-- Unit 5775: Godrej Consumer Product Indonesia - Cileungsi (Rp8.600.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8600000,
    customer_id = 38,
    customer_location_id = 111,
    kontrak_id = @kontrak_id
WHERE no_unit = 5775;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5775
LIMIT 1;

-- Unit 5776: Godrej Consumer Product Indonesia - Cileungsi (Rp8.600.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8600000,
    customer_id = 38,
    customer_location_id = 111,
    kontrak_id = @kontrak_id
WHERE no_unit = 5776;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5776
LIMIT 1;

-- Unit 5779: Godrej Consumer Product Indonesia - Cileungsi (Rp10.050.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10050000,
    customer_id = 38,
    customer_location_id = 111,
    kontrak_id = @kontrak_id
WHERE no_unit = 5779;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5779
LIMIT 1;

-- Unit 5780: Godrej Consumer Product Indonesia - Cileungsi (Rp10.050.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10050000,
    customer_id = 38,
    customer_location_id = 111,
    kontrak_id = @kontrak_id
WHERE no_unit = 5780;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5780
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    38,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: NO. 134/HPP/RM/MG/X/25
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-09-01 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'NO. 134/HPP/RM/MG/X/25',
    'CONTRACT',
    '2025-09-01',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 1600: Hasta Putera Perkasa - Forisa (Rp20.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 20000000,
    customer_id = 40,
    customer_location_id = 116,
    kontrak_id = @kontrak_id
WHERE no_unit = 1600;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-09-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1600
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    40,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: NO. 013/HPP/RM/MG/II/26
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-12-01 to 2025-31-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'NO. 013/HPP/RM/MG/II/26',
    'CONTRACT',
    '2025-12-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'Hasta Putera Perkasa' not found (unit 1600)
-- Unit 1600: Hasta Putera Perkasa -  (Rp20.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 20000000,
    customer_id = 40,
    kontrak_id = @kontrak_id
WHERE no_unit = 1600;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1600
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    40,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: No. 069/SPj/LEG/HI/X/25
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-11-03 to 2026-02-11
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'No. 069/SPj/LEG/HI/X/25',
    'CONTRACT',
    '2025-11-03',
    '2026-02-11',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3798: Herlina Indah - Jakarta (Rp8.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8500000,
    customer_id = 42,
    customer_location_id = 119,
    kontrak_id = @kontrak_id
WHERE no_unit = 3798;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-03',
    '2026-02-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3798
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    42,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 8080511797
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-11-25 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '8080511797',
    'PO_ONLY',
    '2025-11-25',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5012: ICI PAINTS - Jababeka (Rp8.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8000000,
    customer_id = 112,
    customer_location_id = 315,
    kontrak_id = @kontrak_id
WHERE no_unit = 5012;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-25',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5012
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    112,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 8080488987
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-07-24 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '8080488987',
    'PO_ONLY',
    '2025-07-24',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3867: ICI PAINTS - Jababeka (Rp8.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8500000,
    customer_id = 112,
    customer_location_id = 315,
    kontrak_id = @kontrak_id
WHERE no_unit = 3867;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-07-24',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3867
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    112,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 754/SML/X/2024
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2024-10-12 to 2025-11-10
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '754/SML/X/2024',
    'CONTRACT',
    '2024-10-12',
    '2025-11-10',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5351: IGP INTERNASIONAL - KLATEN (Rp12.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12000000,
    customer_id = 113,
    customer_location_id = 317,
    kontrak_id = @kontrak_id
WHERE no_unit = 5351;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-10-12',
    '2025-11-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5351
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    113,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 52056998
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-01-01 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '52056998',
    'PO_ONLY',
    '2025-01-01',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'Indah Bestari Permai' not found (unit 2965)
-- Unit 2965: Indah Bestari Permai -  (Rp27.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 27500000,
    customer_id = 160,
    kontrak_id = @kontrak_id
WHERE no_unit = 2965;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2965
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    160,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 157/PO/PAG-SML/PSM/VI/2023
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2023-01-06 to 2024-01-06
-- Units: 45
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '157/PO/PAG-SML/PSM/VI/2023',
    'CONTRACT',
    '2023-01-06',
    '2024-01-06',
    'ACTIVE',
    'BULANAN',
    45,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'AM #1' for customer 'Indah Kiat Pulp and Paper' not found (unit 3290)
-- Unit 3290: Indah Kiat Pulp and Paper - AM #1 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3290;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3290
LIMIT 1;

-- ⚠ WARNING: Location 'CARTON BOX' for customer 'Indah Kiat Pulp and Paper' not found (unit 3495)
-- Unit 3495: Indah Kiat Pulp and Paper - CARTON BOX (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3495;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3495
LIMIT 1;

-- ⚠ WARNING: Location 'CS #1' for customer 'Indah Kiat Pulp and Paper' not found (unit 3506)
-- Unit 3506: Indah Kiat Pulp and Paper - CS #1 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3506;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3506
LIMIT 1;

-- ⚠ WARNING: Location 'CS #1' for customer 'Indah Kiat Pulp and Paper' not found (unit 3524)
-- Unit 3524: Indah Kiat Pulp and Paper - CS #1 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3524;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3524
LIMIT 1;

-- ⚠ WARNING: Location 'CS #3' for customer 'Indah Kiat Pulp and Paper' not found (unit 3507)
-- Unit 3507: Indah Kiat Pulp and Paper - CS #3 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3507;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3507
LIMIT 1;

-- ⚠ WARNING: Location 'CS #3' for customer 'Indah Kiat Pulp and Paper' not found (unit 3532)
-- Unit 3532: Indah Kiat Pulp and Paper - CS #3 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3532;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3532
LIMIT 1;

-- ⚠ WARNING: Location 'CS #3' for customer 'Indah Kiat Pulp and Paper' not found (unit 3544)
-- Unit 3544: Indah Kiat Pulp and Paper - CS #3 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3544;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3544
LIMIT 1;

-- ⚠ WARNING: Location 'CS #6' for customer 'Indah Kiat Pulp and Paper' not found (unit 3319)
-- Unit 3319: Indah Kiat Pulp and Paper - CS #6 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3319;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3319
LIMIT 1;

-- ⚠ WARNING: Location 'CS #6' for customer 'Indah Kiat Pulp and Paper' not found (unit 3514)
-- Unit 3514: Indah Kiat Pulp and Paper - CS #6 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3514;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3514
LIMIT 1;

-- ⚠ WARNING: Location 'CS #6' for customer 'Indah Kiat Pulp and Paper' not found (unit 3527)
-- Unit 3527: Indah Kiat Pulp and Paper - CS #6 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3527;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3527
LIMIT 1;

-- ⚠ WARNING: Location 'FS #1' for customer 'Indah Kiat Pulp and Paper' not found (unit 3400)
-- Unit 3400: Indah Kiat Pulp and Paper - FS #1 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3400;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3400
LIMIT 1;

-- ⚠ WARNING: Location 'FS #1' for customer 'Indah Kiat Pulp and Paper' not found (unit 3410)
-- Unit 3410: Indah Kiat Pulp and Paper - FS #1 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3410;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3410
LIMIT 1;

-- ⚠ WARNING: Location 'FS #1' for customer 'Indah Kiat Pulp and Paper' not found (unit 3508)
-- Unit 3508: Indah Kiat Pulp and Paper - FS #1 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3508;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3508
LIMIT 1;

-- ⚠ WARNING: Location 'FS #3' for customer 'Indah Kiat Pulp and Paper' not found (unit 3401)
-- Unit 3401: Indah Kiat Pulp and Paper - FS #3 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3401;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3401
LIMIT 1;

-- ⚠ WARNING: Location 'FS #3' for customer 'Indah Kiat Pulp and Paper' not found (unit 3509)
-- Unit 3509: Indah Kiat Pulp and Paper - FS #3 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3509;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3509
LIMIT 1;

-- ⚠ WARNING: Location 'FS #3' for customer 'Indah Kiat Pulp and Paper' not found (unit 3510)
-- Unit 3510: Indah Kiat Pulp and Paper - FS #3 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3510;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3510
LIMIT 1;

-- ⚠ WARNING: Location 'FS #3' for customer 'Indah Kiat Pulp and Paper' not found (unit 3521)
-- Unit 3521: Indah Kiat Pulp and Paper - FS #3 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3521;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3521
LIMIT 1;

-- ⚠ WARNING: Location 'PPM 1 / PPM 2' for customer 'Indah Kiat Pulp and Paper' not found (unit 3159)
-- Unit 3159: Indah Kiat Pulp and Paper - PPM 1 / PPM 2 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3159;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3159
LIMIT 1;

-- ⚠ WARNING: Location 'PPM 4 / PPM 9' for customer 'Indah Kiat Pulp and Paper' not found (unit 5188)
-- Unit 5188: Indah Kiat Pulp and Paper - PPM 4 / PPM 9 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 5188;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5188
LIMIT 1;

-- ⚠ WARNING: Location 'RH #1' for customer 'Indah Kiat Pulp and Paper' not found (unit 3355)
-- Unit 3355: Indah Kiat Pulp and Paper - RH #1 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3355;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3355
LIMIT 1;

-- ⚠ WARNING: Location 'RH #1' for customer 'Indah Kiat Pulp and Paper' not found (unit 3356)
-- Unit 3356: Indah Kiat Pulp and Paper - RH #1 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3356;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3356
LIMIT 1;

-- ⚠ WARNING: Location 'RH #1' for customer 'Indah Kiat Pulp and Paper' not found (unit 3357)
-- Unit 3357: Indah Kiat Pulp and Paper - RH #1 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3357;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3357
LIMIT 1;

-- ⚠ WARNING: Location 'RH #2' for customer 'Indah Kiat Pulp and Paper' not found (unit 3354)
-- Unit 3354: Indah Kiat Pulp and Paper - RH #2 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3354;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3354
LIMIT 1;

-- ⚠ WARNING: Location 'RH #2' for customer 'Indah Kiat Pulp and Paper' not found (unit 3359)
-- Unit 3359: Indah Kiat Pulp and Paper - RH #2 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3359;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3359
LIMIT 1;

-- ⚠ WARNING: Location 'RH #2' for customer 'Indah Kiat Pulp and Paper' not found (unit 3338)
-- Unit 3338: Indah Kiat Pulp and Paper - RH #2 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3338;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3338
LIMIT 1;

-- ⚠ WARNING: Location 'PPM 4 / PPM 9' for customer 'Indah Kiat Pulp and Paper' not found (unit 3496)
-- Unit 3496: Indah Kiat Pulp and Paper - PPM 4 / PPM 9 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3496;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3496
LIMIT 1;

-- ⚠ WARNING: Location 'PAPER CORE / CUTTING CORE' for customer 'Indah Kiat Pulp and Paper' not found (unit 3498)
-- Unit 3498: Indah Kiat Pulp and Paper - PAPER CORE / CUTTING CORE (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3498;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3498
LIMIT 1;

-- ⚠ WARNING: Location 'PRINTING (PW)' for customer 'Indah Kiat Pulp and Paper' not found (unit 3499)
-- Unit 3499: Indah Kiat Pulp and Paper - PRINTING (PW) (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3499;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3499
LIMIT 1;

-- ⚠ WARNING: Location 'PPM 3 / PPM 5' for customer 'Indah Kiat Pulp and Paper' not found (unit 3500)
-- Unit 3500: Indah Kiat Pulp and Paper - PPM 3 / PPM 5 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3500;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3500
LIMIT 1;

-- ⚠ WARNING: Location 'FS #3' for customer 'Indah Kiat Pulp and Paper' not found (unit 3502)
-- Unit 3502: Indah Kiat Pulp and Paper - FS #3 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3502;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3502
LIMIT 1;

-- ⚠ WARNING: Location 'FS #1' for customer 'Indah Kiat Pulp and Paper' not found (unit 3503)
-- Unit 3503: Indah Kiat Pulp and Paper - FS #1 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3503;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3503
LIMIT 1;

-- ⚠ WARNING: Location 'STT' for customer 'Indah Kiat Pulp and Paper' not found (unit 3504)
-- Unit 3504: Indah Kiat Pulp and Paper - STT (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3504;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3504
LIMIT 1;

-- ⚠ WARNING: Location 'FS #3' for customer 'Indah Kiat Pulp and Paper' not found (unit 3505)
-- Unit 3505: Indah Kiat Pulp and Paper - FS #3 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3505;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3505
LIMIT 1;

-- ⚠ WARNING: Location 'FS #3' for customer 'Indah Kiat Pulp and Paper' not found (unit 3518)
-- Unit 3518: Indah Kiat Pulp and Paper - FS #3 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3518;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3518
LIMIT 1;

-- ⚠ WARNING: Location 'FS #1' for customer 'Indah Kiat Pulp and Paper' not found (unit 3519)
-- Unit 3519: Indah Kiat Pulp and Paper - FS #1 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3519;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3519
LIMIT 1;

-- ⚠ WARNING: Location 'CS #1' for customer 'Indah Kiat Pulp and Paper' not found (unit 3520)
-- Unit 3520: Indah Kiat Pulp and Paper - CS #1 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3520;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3520
LIMIT 1;

-- ⚠ WARNING: Location 'CS #6' for customer 'Indah Kiat Pulp and Paper' not found (unit 3522)
-- Unit 3522: Indah Kiat Pulp and Paper - CS #6 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3522;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3522
LIMIT 1;

-- ⚠ WARNING: Location 'CS #3' for customer 'Indah Kiat Pulp and Paper' not found (unit 3523)
-- Unit 3523: Indah Kiat Pulp and Paper - CS #3 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3523;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3523
LIMIT 1;

-- ⚠ WARNING: Location 'CARTON BOX' for customer 'Indah Kiat Pulp and Paper' not found (unit 3526)
-- Unit 3526: Indah Kiat Pulp and Paper - CARTON BOX (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3526;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3526
LIMIT 1;

-- ⚠ WARNING: Location 'FS #1' for customer 'Indah Kiat Pulp and Paper' not found (unit 3528)
-- Unit 3528: Indah Kiat Pulp and Paper - FS #1 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3528;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3528
LIMIT 1;

-- ⚠ WARNING: Location 'FS #3' for customer 'Indah Kiat Pulp and Paper' not found (unit 3529)
-- Unit 3529: Indah Kiat Pulp and Paper - FS #3 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3529;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3529
LIMIT 1;

-- ⚠ WARNING: Location 'CS #3' for customer 'Indah Kiat Pulp and Paper' not found (unit 3530)
-- Unit 3530: Indah Kiat Pulp and Paper - CS #3 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3530;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3530
LIMIT 1;

-- ⚠ WARNING: Location 'CARTON BOX' for customer 'Indah Kiat Pulp and Paper' not found (unit 3531)
-- Unit 3531: Indah Kiat Pulp and Paper - CARTON BOX (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3531;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3531
LIMIT 1;

-- ⚠ WARNING: Location 'CS #6' for customer 'Indah Kiat Pulp and Paper' not found (unit 3545)
-- Unit 3545: Indah Kiat Pulp and Paper - CS #6 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3545;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3545
LIMIT 1;

-- ⚠ WARNING: Location 'CS #3' for customer 'Indah Kiat Pulp and Paper' not found (unit 3546)
-- Unit 3546: Indah Kiat Pulp and Paper - CS #3 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3546;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3546
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    45,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 158/PO/PAG-SML/PSM/II/2023
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2023-01-02 to 2024-01-02
-- Units: 12
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '158/PO/PAG-SML/PSM/II/2023',
    'CONTRACT',
    '2023-01-02',
    '2024-01-02',
    'ACTIVE',
    'BULANAN',
    12,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'AM #3' for customer 'Indah Kiat Pulp and Paper' not found (unit 3142)
-- Unit 3142: Indah Kiat Pulp and Paper - AM #3 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3142;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-02',
    '2024-01-02',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3142
LIMIT 1;

-- ⚠ WARNING: Location 'CS #6' for customer 'Indah Kiat Pulp and Paper' not found (unit 3325)
-- Unit 3325: Indah Kiat Pulp and Paper - CS #6 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3325;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-02',
    '2024-01-02',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3325
LIMIT 1;

-- ⚠ WARNING: Location 'CS #6' for customer 'Indah Kiat Pulp and Paper' not found (unit 3326)
-- Unit 3326: Indah Kiat Pulp and Paper - CS #6 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3326;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-02',
    '2024-01-02',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3326
LIMIT 1;

-- ⚠ WARNING: Location 'FS #6' for customer 'Indah Kiat Pulp and Paper' not found (unit 3288)
-- Unit 3288: Indah Kiat Pulp and Paper - FS #6 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3288;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-02',
    '2024-01-02',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3288
LIMIT 1;

-- ⚠ WARNING: Location 'FS #6' for customer 'Indah Kiat Pulp and Paper' not found (unit 3320)
-- Unit 3320: Indah Kiat Pulp and Paper - FS #6 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3320;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-02',
    '2024-01-02',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3320
LIMIT 1;

-- ⚠ WARNING: Location 'FS #6' for customer 'Indah Kiat Pulp and Paper' not found (unit 3321)
-- Unit 3321: Indah Kiat Pulp and Paper - FS #6 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3321;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-02',
    '2024-01-02',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3321
LIMIT 1;

-- ⚠ WARNING: Location 'FS #6' for customer 'Indah Kiat Pulp and Paper' not found (unit 3322)
-- Unit 3322: Indah Kiat Pulp and Paper - FS #6 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3322;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-02',
    '2024-01-02',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3322
LIMIT 1;

-- ⚠ WARNING: Location 'PAPER CORE' for customer 'Indah Kiat Pulp and Paper' not found (unit 3285)
-- Unit 3285: Indah Kiat Pulp and Paper - PAPER CORE (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3285;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-02',
    '2024-01-02',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3285
LIMIT 1;

-- ⚠ WARNING: Location 'PPM #6' for customer 'Indah Kiat Pulp and Paper' not found (unit 3239)
-- Unit 3239: Indah Kiat Pulp and Paper - PPM #6 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3239;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-02',
    '2024-01-02',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3239
LIMIT 1;

-- ⚠ WARNING: Location 'PW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3228)
-- Unit 3228: Indah Kiat Pulp and Paper - PW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3228;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-02',
    '2024-01-02',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3228
LIMIT 1;

-- ⚠ WARNING: Location 'PPM #4' for customer 'Indah Kiat Pulp and Paper' not found (unit 3280)
-- Unit 3280: Indah Kiat Pulp and Paper - PPM #4 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3280;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-02',
    '2024-01-02',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3280
LIMIT 1;

-- ⚠ WARNING: Location 'PPM #7 & 8' for customer 'Indah Kiat Pulp and Paper' not found (unit 3283)
-- Unit 3283: Indah Kiat Pulp and Paper - PPM #7 & 8 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3283;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-02',
    '2024-01-02',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3283
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    45,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 155/PO/AW-SML/PSM/XI/2022
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2022-01-11 to 2023-01-11
-- Units: 14
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '155/PO/AW-SML/PSM/XI/2022',
    'CONTRACT',
    '2022-01-11',
    '2023-01-11',
    'ACTIVE',
    'BULANAN',
    14,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 2381)
-- Unit 2381: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 2381;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2381
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3271)
-- Unit 3271: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3271;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3271
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3334)
-- Unit 3334: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3334;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3334
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3336)
-- Unit 3336: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3336;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3336
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3341)
-- Unit 3341: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3341;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3341
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3342)
-- Unit 3342: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3342;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3342
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3343)
-- Unit 3343: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3343;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3343
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3344)
-- Unit 3344: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3344;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3344
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3580)
-- Unit 3580: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3580;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3580
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3600)
-- Unit 3600: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3600;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3600
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 2682)
-- Unit 2682: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 2682;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2682
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 2929)
-- Unit 2929: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 2929;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2929
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3167)
-- Unit 3167: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3167;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3167
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3210)
-- Unit 3210: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3210;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3210
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    45,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 153/PO/AW-SML/PSM/XI/2022
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2022-01-11 to 2023-01-11
-- Units: 11
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '153/PO/AW-SML/PSM/XI/2022',
    'CONTRACT',
    '2022-01-11',
    '2023-01-11',
    'ACTIVE',
    'BULANAN',
    11,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3166)
-- Unit 3166: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3166;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3166
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3340)
-- Unit 3340: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3340;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3340
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3349)
-- Unit 3349: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3349;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3349
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3353)
-- Unit 3353: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3353;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3353
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3169)
-- Unit 3169: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3169;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3169
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3184)
-- Unit 3184: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3184;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3184
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3187)
-- Unit 3187: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3187;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3187
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3189)
-- Unit 3189: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3189;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3189
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3332)
-- Unit 3332: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3332;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3332
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3335)
-- Unit 3335: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3335;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3335
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3337)
-- Unit 3337: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3337;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3337
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    45,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 154/PO/AW-SML/PSM/XI/2022
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2022-01-11 to 2023-01-11
-- Units: 29
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '154/PO/AW-SML/PSM/XI/2022',
    'CONTRACT',
    '2022-01-11',
    '2023-01-11',
    'ACTIVE',
    'BULANAN',
    29,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3183)
-- Unit 3183: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3183;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3183
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3191)
-- Unit 3191: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3191;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3191
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3222)
-- Unit 3222: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3222;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3222
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3223)
-- Unit 3223: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3223;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3223
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3224)
-- Unit 3224: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3224;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3224
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3225)
-- Unit 3225: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3225;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3225
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3227)
-- Unit 3227: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3227;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3227
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3236)
-- Unit 3236: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3236;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3236
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3241)
-- Unit 3241: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3241;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3241
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3244)
-- Unit 3244: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3244;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3244
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3246)
-- Unit 3246: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3246;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3246
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3248)
-- Unit 3248: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3248;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3248
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3250)
-- Unit 3250: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3250;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3250
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3272)
-- Unit 3272: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3272;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3272
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3274)
-- Unit 3274: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3274;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3274
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3275)
-- Unit 3275: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3275;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3275
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3277)
-- Unit 3277: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3277;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3277
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3278)
-- Unit 3278: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3278;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3278
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3279)
-- Unit 3279: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3279;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3279
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3333)
-- Unit 3333: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3333;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3333
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3351)
-- Unit 3351: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3351;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3351
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 4020)
-- Unit 4020: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 4020;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 4020
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3168)
-- Unit 3168: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3168;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3168
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3190)
-- Unit 3190: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3190;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3190
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3192)
-- Unit 3192: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3192;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3192
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3273)
-- Unit 3273: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3273;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3273
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3281)
-- Unit 3281: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3281;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3281
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3282)
-- Unit 3282: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3282;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3282
LIMIT 1;

-- ⚠ WARNING: Location 'AW' for customer 'Indah Kiat Pulp and Paper' not found (unit 3284)
-- Unit 3284: Indah Kiat Pulp and Paper - AW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3284;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3284
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    45,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 288/SPD/PL-SML/PSM/VI/2024
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2024-01-07 to 2025-01-07
-- Units: 4
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '288/SPD/PL-SML/PSM/VI/2024',
    'CONTRACT',
    '2024-01-07',
    '2025-01-07',
    'ACTIVE',
    'BULANAN',
    4,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'PALLET' for customer 'Indah Kiat Pulp and Paper' not found (unit 2681)
-- Unit 2681: Indah Kiat Pulp and Paper - PALLET (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 2681;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-01-07',
    '2025-01-07',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2681
LIMIT 1;

-- ⚠ WARNING: Location 'PALLET' for customer 'Indah Kiat Pulp and Paper' not found (unit 2683)
-- Unit 2683: Indah Kiat Pulp and Paper - PALLET (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 2683;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-01-07',
    '2025-01-07',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2683
LIMIT 1;

-- ⚠ WARNING: Location 'PALLET' for customer 'Indah Kiat Pulp and Paper' not found (unit 2644)
-- Unit 2644: Indah Kiat Pulp and Paper - PALLET (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 2644;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-01-07',
    '2025-01-07',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2644
LIMIT 1;

-- ⚠ WARNING: Location 'PALLET' for customer 'Indah Kiat Pulp and Paper' not found (unit 3616)
-- Unit 3616: Indah Kiat Pulp and Paper - PALLET (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3616;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-01-07',
    '2025-01-07',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3616
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    45,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 299/SPD/EGD-SML/PSM/VIII/2024 (IKPP)
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2024-01-08 to 2025-01-08
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '299/SPD/EGD-SML/PSM/VIII/2024 (IKPP)',
    'CONTRACT',
    '2024-01-08',
    '2025-01-08',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'PB' for customer 'Indah Kiat Pulp and Paper' not found (unit 5517)
-- Unit 5517: Indah Kiat Pulp and Paper - PB (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 5517;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-01-08',
    '2025-01-08',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5517
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    45,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 300/SPD/EGD-SML/PSM/VIII/2024 (PINDO DELI)
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2024-01-08 to 2025-01-08
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '300/SPD/EGD-SML/PSM/VIII/2024 (PINDO DELI)',
    'CONTRACT',
    '2024-01-08',
    '2025-01-08',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'PB' for customer 'Indah Kiat Pulp and Paper' not found (unit 5518)
-- Unit 5518: Indah Kiat Pulp and Paper - PB (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 5518;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-01-08',
    '2025-01-08',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5518
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    45,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 219/PO/RM-SML/PSM/VI/2023
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2023-01-06 to 2024-01-06
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '219/PO/RM-SML/PSM/VI/2023',
    'CONTRACT',
    '2023-01-06',
    '2024-01-06',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'RM' for customer 'Indah Kiat Pulp and Paper' not found (unit 2919)
-- Unit 2919: Indah Kiat Pulp and Paper - RM (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 2919;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2919
LIMIT 1;

-- ⚠ WARNING: Location 'RM' for customer 'Indah Kiat Pulp and Paper' not found (unit 2924)
-- Unit 2924: Indah Kiat Pulp and Paper - RM (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 2924;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-06',
    '2024-01-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2924
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    45,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 342/SPD/RM-SML/PSM/XI/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-01-10 to 2026-01-10
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '342/SPD/RM-SML/PSM/XI/2025',
    'CONTRACT',
    '2025-01-10',
    '2026-01-10',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'RM' for customer 'Indah Kiat Pulp and Paper' not found (unit 3579)
-- Unit 3579: Indah Kiat Pulp and Paper - RM (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3579;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-10',
    '2026-01-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3579
LIMIT 1;

-- ⚠ WARNING: Location 'RM' for customer 'Indah Kiat Pulp and Paper' not found (unit 3595)
-- Unit 3595: Indah Kiat Pulp and Paper - RM (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3595;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-10',
    '2026-01-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3595
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    45,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 093/RM-SML/PSM/VIII/2022 ADD-2
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2022-01-08 to 2023-01-08
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '093/RM-SML/PSM/VIII/2022 ADD-2',
    'CONTRACT',
    '2022-01-08',
    '2023-01-08',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'RM' for customer 'Indah Kiat Pulp and Paper' not found (unit 3596)
-- Unit 3596: Indah Kiat Pulp and Paper - RM (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3596;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-08',
    '2023-01-08',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3596
LIMIT 1;

-- ⚠ WARNING: Location 'RM' for customer 'Indah Kiat Pulp and Paper' not found (unit 3597)
-- Unit 3597: Indah Kiat Pulp and Paper - RM (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 3597;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-08',
    '2023-01-08',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3597
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    45,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: No. 4610022445/22628/IKS-SML/VII/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-08-01 to 
-- Units: 9
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'No. 4610022445/22628/IKS-SML/VII/2025',
    'CONTRACT',
    '2025-08-01',
    '',
    'ACTIVE',
    'BULANAN',
    9,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 1202: Indah Kiat Pulp And Paper - Serang (Rp7.700)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7700,
    customer_id = 45,
    customer_location_id = 123,
    kontrak_id = @kontrak_id
WHERE no_unit = 1202;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1202
LIMIT 1;

-- Unit 3558: Indah Kiat Pulp And Paper - Serang (Rp7.700)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7700,
    customer_id = 45,
    customer_location_id = 123,
    kontrak_id = @kontrak_id
WHERE no_unit = 3558;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3558
LIMIT 1;

-- Unit 5136: Indah Kiat Pulp And Paper - Serang (Rp7.700)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7700,
    customer_id = 45,
    customer_location_id = 123,
    kontrak_id = @kontrak_id
WHERE no_unit = 5136;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5136
LIMIT 1;

-- Unit 5300: Indah Kiat Pulp And Paper - Serang (Rp7.700)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7700,
    customer_id = 45,
    customer_location_id = 123,
    kontrak_id = @kontrak_id
WHERE no_unit = 5300;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5300
LIMIT 1;

-- Unit 5325: Indah Kiat Pulp And Paper - Serang (Rp7.700)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7700,
    customer_id = 45,
    customer_location_id = 123,
    kontrak_id = @kontrak_id
WHERE no_unit = 5325;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5325
LIMIT 1;

-- Unit 5327: Indah Kiat Pulp And Paper - Serang (Rp7.700)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7700,
    customer_id = 45,
    customer_location_id = 123,
    kontrak_id = @kontrak_id
WHERE no_unit = 5327;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5327
LIMIT 1;

-- Unit 5433: Indah Kiat Pulp And Paper - Serang (Rp7.700)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7700,
    customer_id = 45,
    customer_location_id = 123,
    kontrak_id = @kontrak_id
WHERE no_unit = 5433;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5433
LIMIT 1;

-- Unit 5436: Indah Kiat Pulp And Paper - Serang (Rp7.700)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7700,
    customer_id = 45,
    customer_location_id = 123,
    kontrak_id = @kontrak_id
WHERE no_unit = 5436;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5436
LIMIT 1;

-- Unit 5288: Indah Kiat Pulp And Paper - Serang (Rp7.700)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7700,
    customer_id = 45,
    customer_location_id = 123,
    kontrak_id = @kontrak_id
WHERE no_unit = 5288;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5288
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    45,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: TGR-71110905/4610018466
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2023-05-16 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'TGR-71110905/4610018466',
    'CONTRACT',
    '2023-05-16',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 2639: Indah Kiat Pulp And Paper - Tanggerang (Rp11.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11000000,
    customer_id = 45,
    customer_location_id = 126,
    kontrak_id = @kontrak_id
WHERE no_unit = 2639;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-05-16',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2639
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    45,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: TGR-71112862
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-01-01 to 2025-31-12
-- Units: 3
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'TGR-71112862',
    'CONTRACT',
    '2025-01-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    3,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3628: Indah Kiat Pulp And Paper - Tanggerang (Rp10.450.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10450000,
    customer_id = 45,
    customer_location_id = 126,
    kontrak_id = @kontrak_id
WHERE no_unit = 3628;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3628
LIMIT 1;

-- Unit 3856: Indah Kiat Pulp And Paper - Tanggerang (Rp5.750.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 5750000,
    customer_id = 45,
    customer_location_id = 126,
    kontrak_id = @kontrak_id
WHERE no_unit = 3856;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3856
LIMIT 1;

-- Unit 3981: Indah Kiat Pulp And Paper - Tanggerang (Rp6.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6950000,
    customer_id = 45,
    customer_location_id = 126,
    kontrak_id = @kontrak_id
WHERE no_unit = 3981;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3981
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    45,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: TGR-71112861
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-01-01 to 2025-31-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'TGR-71112861',
    'CONTRACT',
    '2025-01-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3650: Indah Kiat Pulp And Paper - Tanggerang (Rp6.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6950000,
    customer_id = 45,
    customer_location_id = 126,
    kontrak_id = @kontrak_id
WHERE no_unit = 3650;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3650
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    45,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: TGR-71112858
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-01-01 to 2025-31-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'TGR-71112858',
    'CONTRACT',
    '2025-01-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3667: Indah Kiat Pulp And Paper - Tanggerang (Rp6.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6950000,
    customer_id = 45,
    customer_location_id = 126,
    kontrak_id = @kontrak_id
WHERE no_unit = 3667;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3667
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    45,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: TGR-71112860
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-01-01 to 2025-31-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'TGR-71112860',
    'CONTRACT',
    '2025-01-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3734: Indah Kiat Pulp And Paper - Tanggerang (Rp6.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6950000,
    customer_id = 45,
    customer_location_id = 126,
    kontrak_id = @kontrak_id
WHERE no_unit = 3734;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3734
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    45,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: TGR-71112863
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-01-01 to 2025-31-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'TGR-71112863',
    'CONTRACT',
    '2025-01-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3848: Indah Kiat Pulp And Paper - Tanggerang (Rp13.250.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 13250000,
    customer_id = 45,
    customer_location_id = 126,
    kontrak_id = @kontrak_id
WHERE no_unit = 3848;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3848
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    45,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: TGR-71112864
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-01-01 to 2025-31-12
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'TGR-71112864',
    'CONTRACT',
    '2025-01-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5048: Indah Kiat Pulp And Paper - Tanggerang (Rp46.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 46000000,
    customer_id = 45,
    customer_location_id = 126,
    kontrak_id = @kontrak_id
WHERE no_unit = 5048;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5048
LIMIT 1;

-- Unit 5408: Indah Kiat Pulp And Paper - Tanggerang (Rp62.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 62000000,
    customer_id = 45,
    customer_location_id = 126,
    kontrak_id = @kontrak_id
WHERE no_unit = 5408;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5408
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    45,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: TGR-71111285
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-01-01 to 2025-31-12
-- Units: 4
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'TGR-71111285',
    'CONTRACT',
    '2025-01-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    4,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5622: Indah Kiat Pulp And Paper - Tanggerang (Rp5.750.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 5750000,
    customer_id = 45,
    customer_location_id = 126,
    kontrak_id = @kontrak_id
WHERE no_unit = 5622;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5622
LIMIT 1;

-- Unit 5623: Indah Kiat Pulp And Paper - Tanggerang (Rp5.750.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 5750000,
    customer_id = 45,
    customer_location_id = 126,
    kontrak_id = @kontrak_id
WHERE no_unit = 5623;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5623
LIMIT 1;

-- Unit 5626: Indah Kiat Pulp And Paper - Tanggerang (Rp5.750.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 5750000,
    customer_id = 45,
    customer_location_id = 126,
    kontrak_id = @kontrak_id
WHERE no_unit = 5626;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5626
LIMIT 1;

-- Unit 5627: Indah Kiat Pulp And Paper - Tanggerang (Rp5.750.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 5750000,
    customer_id = 45,
    customer_location_id = 126,
    kontrak_id = @kontrak_id
WHERE no_unit = 5627;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5627
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    45,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: TGR-71112851
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-01-01 to 2025-31-12
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'TGR-71112851',
    'CONTRACT',
    '2025-01-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5624: Indah Kiat Pulp And Paper - Tanggerang (Rp5.750.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 5750000,
    customer_id = 45,
    customer_location_id = 126,
    kontrak_id = @kontrak_id
WHERE no_unit = 5624;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5624
LIMIT 1;

-- Unit 5625: Indah Kiat Pulp And Paper - Tanggerang (Rp5.750.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 5750000,
    customer_id = 45,
    customer_location_id = 126,
    kontrak_id = @kontrak_id
WHERE no_unit = 5625;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5625
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    45,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: TGR-71112852
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-01-01 to 2025-31-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'TGR-71112852',
    'CONTRACT',
    '2025-01-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5628: Indah Kiat Pulp And Paper - Tanggerang (Rp5.750.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 5750000,
    customer_id = 45,
    customer_location_id = 126,
    kontrak_id = @kontrak_id
WHERE no_unit = 5628;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5628
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    45,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: TGR-71112939
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-01-01 to 2025-31-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'TGR-71112939',
    'CONTRACT',
    '2025-01-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5629: Indah Kiat Pulp And Paper - Tanggerang (Rp5.750.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 5750000,
    customer_id = 45,
    customer_location_id = 126,
    kontrak_id = @kontrak_id
WHERE no_unit = 5629;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5629
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    45,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: TGR-71112849
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-01-01 to 2025-31-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'TGR-71112849',
    'CONTRACT',
    '2025-01-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5630: Indah Kiat Pulp And Paper - Tanggerang (Rp5.750.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 5750000,
    customer_id = 45,
    customer_location_id = 126,
    kontrak_id = @kontrak_id
WHERE no_unit = 5630;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5630
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    45,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: TGR-71112850
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-01-01 to 2025-31-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'TGR-71112850',
    'CONTRACT',
    '2025-01-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5631: Indah Kiat Pulp And Paper - Tanggerang (Rp5.750.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 5750000,
    customer_id = 45,
    customer_location_id = 126,
    kontrak_id = @kontrak_id
WHERE no_unit = 5631;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5631
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    45,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 340/SPD/SCM-SML/PSM/VI/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-01-07 to 2026-01-07
-- Units: 16
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '340/SPD/SCM-SML/PSM/VI/2025',
    'CONTRACT',
    '2025-01-07',
    '2026-01-07',
    'ACTIVE',
    'BULANAN',
    16,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'UW' for customer 'Indah Kiat Pulp and Paper' not found (unit 5052)
-- Unit 5052: Indah Kiat Pulp and Paper - UW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 5052;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-07',
    '2026-01-07',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5052
LIMIT 1;

-- ⚠ WARNING: Location 'UW' for customer 'Indah Kiat Pulp and Paper' not found (unit 5524)
-- Unit 5524: Indah Kiat Pulp and Paper - UW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 5524;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-07',
    '2026-01-07',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5524
LIMIT 1;

-- ⚠ WARNING: Location 'UW' for customer 'Indah Kiat Pulp and Paper' not found (unit 5525)
-- Unit 5525: Indah Kiat Pulp and Paper - UW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 5525;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-07',
    '2026-01-07',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5525
LIMIT 1;

-- ⚠ WARNING: Location 'UW' for customer 'Indah Kiat Pulp and Paper' not found (unit 5526)
-- Unit 5526: Indah Kiat Pulp and Paper - UW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 5526;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-07',
    '2026-01-07',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5526
LIMIT 1;

-- ⚠ WARNING: Location 'UW' for customer 'Indah Kiat Pulp and Paper' not found (unit 5527)
-- Unit 5527: Indah Kiat Pulp and Paper - UW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 5527;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-07',
    '2026-01-07',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5527
LIMIT 1;

-- ⚠ WARNING: Location 'UW' for customer 'Indah Kiat Pulp and Paper' not found (unit 5543)
-- Unit 5543: Indah Kiat Pulp and Paper - UW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 5543;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-07',
    '2026-01-07',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5543
LIMIT 1;

-- ⚠ WARNING: Location 'UW' for customer 'Indah Kiat Pulp and Paper' not found (unit 5544)
-- Unit 5544: Indah Kiat Pulp and Paper - UW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 5544;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-07',
    '2026-01-07',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5544
LIMIT 1;

-- ⚠ WARNING: Location 'UW' for customer 'Indah Kiat Pulp and Paper' not found (unit 5545)
-- Unit 5545: Indah Kiat Pulp and Paper - UW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 5545;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-07',
    '2026-01-07',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5545
LIMIT 1;

-- ⚠ WARNING: Location 'UW' for customer 'Indah Kiat Pulp and Paper' not found (unit 5546)
-- Unit 5546: Indah Kiat Pulp and Paper - UW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 5546;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-07',
    '2026-01-07',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5546
LIMIT 1;

-- ⚠ WARNING: Location 'UW' for customer 'Indah Kiat Pulp and Paper' not found (unit 5547)
-- Unit 5547: Indah Kiat Pulp and Paper - UW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 5547;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-07',
    '2026-01-07',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5547
LIMIT 1;

-- ⚠ WARNING: Location 'UW' for customer 'Indah Kiat Pulp and Paper' not found (unit 5548)
-- Unit 5548: Indah Kiat Pulp and Paper - UW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 5548;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-07',
    '2026-01-07',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5548
LIMIT 1;

-- ⚠ WARNING: Location 'UW' for customer 'Indah Kiat Pulp and Paper' not found (unit 5645)
-- Unit 5645: Indah Kiat Pulp and Paper - UW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 5645;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-07',
    '2026-01-07',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5645
LIMIT 1;

-- ⚠ WARNING: Location 'UW' for customer 'Indah Kiat Pulp and Paper' not found (unit 5646)
-- Unit 5646: Indah Kiat Pulp and Paper - UW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 5646;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-07',
    '2026-01-07',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5646
LIMIT 1;

-- ⚠ WARNING: Location 'UW' for customer 'Indah Kiat Pulp and Paper' not found (unit 5650)
-- Unit 5650: Indah Kiat Pulp and Paper - UW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 5650;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-07',
    '2026-01-07',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5650
LIMIT 1;

-- ⚠ WARNING: Location 'UW' for customer 'Indah Kiat Pulp and Paper' not found (unit 5651)
-- Unit 5651: Indah Kiat Pulp and Paper - UW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 5651;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-07',
    '2026-01-07',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5651
LIMIT 1;

-- ⚠ WARNING: Location 'UW' for customer 'Indah Kiat Pulp and Paper' not found (unit 5513)
-- Unit 5513: Indah Kiat Pulp and Paper - UW (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 5513;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-07',
    '2026-01-07',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5513
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    45,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: TGR-71112854
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-01-01 to 2025-31-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'TGR-71112854',
    'CONTRACT',
    '2025-01-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5321: Indah Kiat Pulp And Paper - Tanggerang (Rp5.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 5950000,
    customer_id = 45,
    customer_location_id = 126,
    kontrak_id = @kontrak_id
WHERE no_unit = 5321;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5321
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    45,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: TGR-71112855
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-01-01 to 2025-31-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'TGR-71112855',
    'CONTRACT',
    '2025-01-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5322: Indah Kiat Pulp And Paper - Tanggerang (Rp5.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 5950000,
    customer_id = 45,
    customer_location_id = 126,
    kontrak_id = @kontrak_id
WHERE no_unit = 5322;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5322
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    45,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 298/SPD/EGD-SML/PSM/VIII/2024 (IKPP)
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2024-01-08 to 2025-01-08
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '298/SPD/EGD-SML/PSM/VIII/2024 (IKPP)',
    'CONTRACT',
    '2024-01-08',
    '2025-01-08',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'PB' for customer 'Indah Kiat Pulp and Paper' not found (unit 5395)
-- Unit 5395: Indah Kiat Pulp and Paper - PB (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 45,
    kontrak_id = @kontrak_id
WHERE no_unit = 5395;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-01-08',
    '2025-01-08',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5395
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    45,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 107/SML-FORKLIFT/IBR/VI/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-06-01 to 
-- Units: 34
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '107/SML-FORKLIFT/IBR/VI/2025',
    'CONTRACT',
    '2025-06-01',
    '',
    'ACTIVE',
    'BULANAN',
    34,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 2217: Indo Bharat Rayon - Purwakarta (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 46,
    customer_location_id = 127,
    kontrak_id = @kontrak_id
WHERE no_unit = 2217;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2217
LIMIT 1;

-- Unit 2986: Indo Bharat Rayon - Purwakarta (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 46,
    customer_location_id = 127,
    kontrak_id = @kontrak_id
WHERE no_unit = 2986;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2986
LIMIT 1;

-- Unit 1270: Indo Bharat Rayon - Purwakarta (Rp13.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 13800000,
    customer_id = 46,
    customer_location_id = 127,
    kontrak_id = @kontrak_id
WHERE no_unit = 1270;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1270
LIMIT 1;

-- Unit 1773: Indo Bharat Rayon - Purwakarta (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 46,
    customer_location_id = 127,
    kontrak_id = @kontrak_id
WHERE no_unit = 1773;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1773
LIMIT 1;

-- Unit 2085: Indo Bharat Rayon - Purwakarta (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 46,
    customer_location_id = 127,
    kontrak_id = @kontrak_id
WHERE no_unit = 2085;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2085
LIMIT 1;

-- Unit 2086: Indo Bharat Rayon - Purwakarta (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 46,
    customer_location_id = 127,
    kontrak_id = @kontrak_id
WHERE no_unit = 2086;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2086
LIMIT 1;

-- Unit 2090: Indo Bharat Rayon - Purwakarta (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 46,
    customer_location_id = 127,
    kontrak_id = @kontrak_id
WHERE no_unit = 2090;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2090
LIMIT 1;

-- Unit 2163: Indo Bharat Rayon - Purwakarta (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 46,
    customer_location_id = 127,
    kontrak_id = @kontrak_id
WHERE no_unit = 2163;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2163
LIMIT 1;

-- Unit 2180: Indo Bharat Rayon - Purwakarta (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 46,
    customer_location_id = 127,
    kontrak_id = @kontrak_id
WHERE no_unit = 2180;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2180
LIMIT 1;

-- Unit 2754: Indo Bharat Rayon - Purwakarta (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 46,
    customer_location_id = 127,
    kontrak_id = @kontrak_id
WHERE no_unit = 2754;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2754
LIMIT 1;

-- Unit 2902: Indo Bharat Rayon - Purwakarta (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 46,
    customer_location_id = 127,
    kontrak_id = @kontrak_id
WHERE no_unit = 2902;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2902
LIMIT 1;

-- Unit 3002: Indo Bharat Rayon - Purwakarta (Rp13.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 13800000,
    customer_id = 46,
    customer_location_id = 127,
    kontrak_id = @kontrak_id
WHERE no_unit = 3002;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3002
LIMIT 1;

-- Unit 3295: Indo Bharat Rayon - Purwakarta (Rp5.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 5500000,
    customer_id = 46,
    customer_location_id = 127,
    kontrak_id = @kontrak_id
WHERE no_unit = 3295;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3295
LIMIT 1;

-- Unit 3373: Indo Bharat Rayon - Purwakarta (Rp13.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 13800000,
    customer_id = 46,
    customer_location_id = 127,
    kontrak_id = @kontrak_id
WHERE no_unit = 3373;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3373
LIMIT 1;

-- Unit 3374: Indo Bharat Rayon - Purwakarta (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 46,
    customer_location_id = 127,
    kontrak_id = @kontrak_id
WHERE no_unit = 3374;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3374
LIMIT 1;

-- Unit 3511: Indo Bharat Rayon - Purwakarta (Rp13.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 13800000,
    customer_id = 46,
    customer_location_id = 127,
    kontrak_id = @kontrak_id
WHERE no_unit = 3511;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3511
LIMIT 1;

-- Unit 3512: Indo Bharat Rayon - Purwakarta (Rp13.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 13800000,
    customer_id = 46,
    customer_location_id = 127,
    kontrak_id = @kontrak_id
WHERE no_unit = 3512;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3512
LIMIT 1;

-- Unit 3513: Indo Bharat Rayon - Purwakarta (Rp13.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 13800000,
    customer_id = 46,
    customer_location_id = 127,
    kontrak_id = @kontrak_id
WHERE no_unit = 3513;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3513
LIMIT 1;

-- Unit 3613: Indo Bharat Rayon - Purwakarta (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 46,
    customer_location_id = 127,
    kontrak_id = @kontrak_id
WHERE no_unit = 3613;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3613
LIMIT 1;

-- Unit 3679: Indo Bharat Rayon - Purwakarta (Rp21.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 21000000,
    customer_id = 46,
    customer_location_id = 127,
    kontrak_id = @kontrak_id
WHERE no_unit = 3679;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3679
LIMIT 1;

-- Unit 3680: Indo Bharat Rayon - Purwakarta (Rp21.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 21000000,
    customer_id = 46,
    customer_location_id = 127,
    kontrak_id = @kontrak_id
WHERE no_unit = 3680;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3680
LIMIT 1;

-- Unit 3681: Indo Bharat Rayon - Purwakarta (Rp21.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 21000000,
    customer_id = 46,
    customer_location_id = 127,
    kontrak_id = @kontrak_id
WHERE no_unit = 3681;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3681
LIMIT 1;

-- Unit 3682: Indo Bharat Rayon - Purwakarta (Rp21.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 21000000,
    customer_id = 46,
    customer_location_id = 127,
    kontrak_id = @kontrak_id
WHERE no_unit = 3682;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3682
LIMIT 1;

-- Unit 3683: Indo Bharat Rayon - Purwakarta (Rp21.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 21000000,
    customer_id = 46,
    customer_location_id = 127,
    kontrak_id = @kontrak_id
WHERE no_unit = 3683;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3683
LIMIT 1;

-- Unit 3684: Indo Bharat Rayon - Purwakarta (Rp21.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 21000000,
    customer_id = 46,
    customer_location_id = 127,
    kontrak_id = @kontrak_id
WHERE no_unit = 3684;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3684
LIMIT 1;

-- Unit 3731: Indo Bharat Rayon - Purwakarta (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 46,
    customer_location_id = 127,
    kontrak_id = @kontrak_id
WHERE no_unit = 3731;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3731
LIMIT 1;

-- Unit 5722: Indo Bharat Rayon - Purwakarta (Rp20.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 20000000,
    customer_id = 46,
    customer_location_id = 127,
    kontrak_id = @kontrak_id
WHERE no_unit = 5722;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5722
LIMIT 1;

-- Unit 5723: Indo Bharat Rayon - Purwakarta (Rp20.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 20000000,
    customer_id = 46,
    customer_location_id = 127,
    kontrak_id = @kontrak_id
WHERE no_unit = 5723;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5723
LIMIT 1;

-- Unit 5724: Indo Bharat Rayon - Purwakarta (Rp20.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 20000000,
    customer_id = 46,
    customer_location_id = 127,
    kontrak_id = @kontrak_id
WHERE no_unit = 5724;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5724
LIMIT 1;

-- Unit 3516: Indo Bharat Rayon - Purwakarta (Rp13.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 13800000,
    customer_id = 46,
    customer_location_id = 127,
    kontrak_id = @kontrak_id
WHERE no_unit = 3516;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3516
LIMIT 1;

-- Unit 5748: Indo Bharat Rayon - Purwakarta (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 46,
    customer_location_id = 127,
    kontrak_id = @kontrak_id
WHERE no_unit = 5748;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5748
LIMIT 1;

-- Unit 5749: Indo Bharat Rayon - Purwakarta (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 46,
    customer_location_id = 127,
    kontrak_id = @kontrak_id
WHERE no_unit = 5749;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5749
LIMIT 1;

-- Unit 2914: Indo Bharat Rayon spare - Purwakarta (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 46,
    customer_location_id = 127,
    kontrak_id = @kontrak_id
WHERE no_unit = 2914;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2914
LIMIT 1;

-- Unit 5747: Indo Bharat Rayon spare - Purwakarta (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 46,
    customer_location_id = 127,
    kontrak_id = @kontrak_id
WHERE no_unit = 5747;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5747
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    46,
    @kontrak_id,
    1
);
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    46,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 017/JASA/IK/2114/X/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-10-01 to 2028-31-10
-- Units: 8
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '017/JASA/IK/2114/X/2025',
    'CONTRACT',
    '2025-10-01',
    '2028-31-10',
    'ACTIVE',
    'BULANAN',
    8,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Citeureup' for customer 'Indokordsa' not found (unit 6000)
-- Unit 6000: Indokordsa - Citeureup (Rp10.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10000000,
    customer_id = 47,
    kontrak_id = @kontrak_id
WHERE no_unit = 6000;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-01',
    '2028-31-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 6000
LIMIT 1;

-- ⚠ WARNING: Location 'Citeureup' for customer 'Indokordsa' not found (unit 6002)
-- Unit 6002: Indokordsa - Citeureup (Rp11.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11000000,
    customer_id = 47,
    kontrak_id = @kontrak_id
WHERE no_unit = 6002;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-01',
    '2028-31-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 6002
LIMIT 1;

-- ⚠ WARNING: Location 'SPARE' for customer 'Indokordsa' not found (unit 6005)
-- Unit 6005: Indokordsa - SPARE (Rp11.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11000000,
    customer_id = 47,
    kontrak_id = @kontrak_id
WHERE no_unit = 6005;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-01',
    '2028-31-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 6005
LIMIT 1;

-- ⚠ WARNING: Location 'Citeureup' for customer 'Indokordsa' not found (unit 6007)
-- Unit 6007: Indokordsa - Citeureup (Rp11.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11000000,
    customer_id = 47,
    kontrak_id = @kontrak_id
WHERE no_unit = 6007;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-01',
    '2028-31-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 6007
LIMIT 1;

-- ⚠ WARNING: Location 'Citeureup' for customer 'Indokordsa' not found (unit 6009)
-- Unit 6009: Indokordsa - Citeureup (Rp11.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11000000,
    customer_id = 47,
    kontrak_id = @kontrak_id
WHERE no_unit = 6009;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-01',
    '2028-31-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 6009
LIMIT 1;

-- ⚠ WARNING: Location 'Citeureup' for customer 'Indokordsa' not found (unit 6014)
-- Unit 6014: Indokordsa - Citeureup (Rp7.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7800000,
    customer_id = 47,
    kontrak_id = @kontrak_id
WHERE no_unit = 6014;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-01',
    '2028-31-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 6014
LIMIT 1;

-- ⚠ WARNING: Location 'Citeureup' for customer 'Indokordsa' not found (unit 6018)
-- Unit 6018: Indokordsa - Citeureup (Rp13.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 13000000,
    customer_id = 47,
    kontrak_id = @kontrak_id
WHERE no_unit = 6018;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-01',
    '2028-31-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 6018
LIMIT 1;

-- ⚠ WARNING: Location 'Citeureup' for customer 'Indokordsa' not found (unit 6015)
-- Unit 6015: Indokordsa - Citeureup (Rp16.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 16000000,
    customer_id = 47,
    kontrak_id = @kontrak_id
WHERE no_unit = 6015;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-01',
    '2028-31-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 6015
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    47,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: No. 003/LGL-0004/PTIL/WHS-CKR/III/2023
-- Type: CONTRACT | Status: ACTIVE
-- Period:  to 
-- Units: 3
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'No. 003/LGL-0004/PTIL/WHS-CKR/III/2023',
    'CONTRACT',
    '',
    '',
    'ACTIVE',
    'BULANAN',
    3,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3383: Indokuat - Deltasilicon (Rp7.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7950000,
    customer_id = 50,
    customer_location_id = 167,
    kontrak_id = @kontrak_id
WHERE no_unit = 3383;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3383
LIMIT 1;

-- Unit 3384: Indokuat - Deltasilicon (Rp24.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 24500000,
    customer_id = 50,
    customer_location_id = 167,
    kontrak_id = @kontrak_id
WHERE no_unit = 3384;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3384
LIMIT 1;

-- Unit 3418: Indokuat - Deltasilicon (Rp10.700.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10700000,
    customer_id = 50,
    customer_location_id = 167,
    kontrak_id = @kontrak_id
WHERE no_unit = 3418;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3418
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    50,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: AMD II - 166/LGL-0193/PTIL/WHS-C1/V1/2024
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2023-04-01 to 
-- Units: 19
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'AMD II - 166/LGL-0193/PTIL/WHS-C1/V1/2024',
    'CONTRACT',
    '2023-04-01',
    '',
    'ACTIVE',
    'BULANAN',
    19,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3046: Indolakto - Cicurug (Rp7.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7950000,
    customer_id = 51,
    customer_location_id = 174,
    kontrak_id = @kontrak_id
WHERE no_unit = 3046;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-04-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3046
LIMIT 1;

-- Unit 3048: Indolakto - Cicurug (Rp9.200.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9200000,
    customer_id = 51,
    customer_location_id = 174,
    kontrak_id = @kontrak_id
WHERE no_unit = 3048;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-04-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3048
LIMIT 1;

-- Unit 3050: Indolakto - Cicurug (Rp7.400.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7400000,
    customer_id = 51,
    customer_location_id = 174,
    kontrak_id = @kontrak_id
WHERE no_unit = 3050;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-04-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3050
LIMIT 1;

-- Unit 3051: Indolakto - Cicurug (Rp7.400.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7400000,
    customer_id = 51,
    customer_location_id = 174,
    kontrak_id = @kontrak_id
WHERE no_unit = 3051;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-04-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3051
LIMIT 1;

-- Unit 3063: Indolakto - Cicurug (Rp7.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7950000,
    customer_id = 51,
    customer_location_id = 174,
    kontrak_id = @kontrak_id
WHERE no_unit = 3063;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-04-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3063
LIMIT 1;

-- Unit 3076: Indolakto - Cicurug (Rp8.600.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8600000,
    customer_id = 51,
    customer_location_id = 174,
    kontrak_id = @kontrak_id
WHERE no_unit = 3076;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-04-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3076
LIMIT 1;

-- Unit 3043: Indolakto - Cicurug (Rp7.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7950000,
    customer_id = 51,
    customer_location_id = 174,
    kontrak_id = @kontrak_id
WHERE no_unit = 3043;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-04-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3043
LIMIT 1;

-- Unit 3044: Indolakto - Cicurug (Rp7.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7950000,
    customer_id = 51,
    customer_location_id = 174,
    kontrak_id = @kontrak_id
WHERE no_unit = 3044;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-04-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3044
LIMIT 1;

-- Unit 3045: Indolakto - Cicurug (Rp7.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7950000,
    customer_id = 51,
    customer_location_id = 174,
    kontrak_id = @kontrak_id
WHERE no_unit = 3045;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-04-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3045
LIMIT 1;

-- Unit 3047: Indolakto - Cicurug (Rp9.200.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9200000,
    customer_id = 51,
    customer_location_id = 174,
    kontrak_id = @kontrak_id
WHERE no_unit = 3047;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-04-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3047
LIMIT 1;

-- Unit 3064: Indolakto - Cicurug (Rp7.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7950000,
    customer_id = 51,
    customer_location_id = 174,
    kontrak_id = @kontrak_id
WHERE no_unit = 3064;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-04-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3064
LIMIT 1;

-- Unit 3066: Indolakto - Cicurug (Rp7.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7950000,
    customer_id = 51,
    customer_location_id = 174,
    kontrak_id = @kontrak_id
WHERE no_unit = 3066;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-04-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3066
LIMIT 1;

-- Unit 3067: Indolakto - Cicurug (Rp7.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7950000,
    customer_id = 51,
    customer_location_id = 174,
    kontrak_id = @kontrak_id
WHERE no_unit = 3067;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-04-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3067
LIMIT 1;

-- Unit 3069: Indolakto - Cicurug (Rp7.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7950000,
    customer_id = 51,
    customer_location_id = 174,
    kontrak_id = @kontrak_id
WHERE no_unit = 3069;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-04-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3069
LIMIT 1;

-- Unit 3070: Indolakto - Cicurug (Rp7.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7950000,
    customer_id = 51,
    customer_location_id = 174,
    kontrak_id = @kontrak_id
WHERE no_unit = 3070;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-04-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3070
LIMIT 1;

-- Unit 3073: Indolakto - Cicurug (Rp7.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7950000,
    customer_id = 51,
    customer_location_id = 174,
    kontrak_id = @kontrak_id
WHERE no_unit = 3073;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-04-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3073
LIMIT 1;

-- Unit 3075: Indolakto - Cicurug (Rp8.600.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8600000,
    customer_id = 51,
    customer_location_id = 174,
    kontrak_id = @kontrak_id
WHERE no_unit = 3075;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-04-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3075
LIMIT 1;

-- Unit 3413: Indolakto - Cicurug (Rp11.300.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11300000,
    customer_id = 51,
    customer_location_id = 174,
    kontrak_id = @kontrak_id
WHERE no_unit = 3413;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-04-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3413
LIMIT 1;

-- Unit 3414: Indolakto - Cicurug (Rp11.300.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11300000,
    customer_id = 51,
    customer_location_id = 174,
    kontrak_id = @kontrak_id
WHERE no_unit = 3414;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-04-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3414
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    51,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: No. 056/LGL-0254/PTIL/WHS-C1/V11/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-07-01 to 
-- Units: 6
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'No. 056/LGL-0254/PTIL/WHS-C1/V11/2025',
    'CONTRACT',
    '2025-07-01',
    '',
    'ACTIVE',
    'BULANAN',
    6,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5393: Indolakto - Cicurug (Rp29.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 29500000,
    customer_id = 51,
    customer_location_id = 174,
    kontrak_id = @kontrak_id
WHERE no_unit = 5393;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5393
LIMIT 1;

-- Unit 5394: Indolakto - Cicurug (Rp29.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 29500000,
    customer_id = 51,
    customer_location_id = 174,
    kontrak_id = @kontrak_id
WHERE no_unit = 5394;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5394
LIMIT 1;

-- Unit 5530: Indolakto - Cicurug (Rp29.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 29500000,
    customer_id = 51,
    customer_location_id = 174,
    kontrak_id = @kontrak_id
WHERE no_unit = 5530;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5530
LIMIT 1;

-- Unit 5636: Indolakto - Cicurug (Rp29.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 29500000,
    customer_id = 51,
    customer_location_id = 174,
    kontrak_id = @kontrak_id
WHERE no_unit = 5636;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5636
LIMIT 1;

-- Unit 5637: Indolakto - Cicurug (Rp29.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 29500000,
    customer_id = 51,
    customer_location_id = 174,
    kontrak_id = @kontrak_id
WHERE no_unit = 5637;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5637
LIMIT 1;

-- Unit 5529: Indolakto - Cicurug (Rp29.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 29500000,
    customer_id = 51,
    customer_location_id = 174,
    kontrak_id = @kontrak_id
WHERE no_unit = 5529;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5529
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    51,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 028/SML/ADD.1/V/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2024-11-02 to 2027-01-11
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '028/SML/ADD.1/V/2025',
    'CONTRACT',
    '2024-11-02',
    '2027-01-11',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5620: INTAN JAYA SOLUSI MEDIKA - JABABEKA (Rp16.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 16500000,
    customer_id = 53,
    customer_location_id = 177,
    kontrak_id = @kontrak_id
WHERE no_unit = 5620;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-11-02',
    '2027-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5620
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    53,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: NO.ADD04-001/RTL/SML-KAL/01/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-05-01 to 2025-31-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'NO.ADD04-001/RTL/SML-KAL/01/2025',
    'CONTRACT',
    '2025-05-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 2018: KADAKA - CILEUNGSI (Rp8.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8000000,
    customer_id = 204,
    customer_location_id = 454,
    kontrak_id = @kontrak_id
WHERE no_unit = 2018;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2018
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    204,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 033/PCH-CORP/KRW-KCS/VIII/2024
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2024-08-26 to 
-- Units: 4
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '033/PCH-CORP/KRW-KCS/VIII/2024',
    'CONTRACT',
    '2024-08-26',
    '',
    'ACTIVE',
    'BULANAN',
    4,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 1977: Kamadjaja - Karawang (Rp16.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 16000000,
    customer_id = 56,
    customer_location_id = 181,
    kontrak_id = @kontrak_id
WHERE no_unit = 1977;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-08-26',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1977
LIMIT 1;

-- Unit 5196: Kamadjaja - Karawang (Rp16.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 16000000,
    customer_id = 56,
    customer_location_id = 181,
    kontrak_id = @kontrak_id
WHERE no_unit = 5196;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-08-26',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5196
LIMIT 1;

-- Unit 5198: Kamadjaja - Karawang (Rp15.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 15500000,
    customer_id = 56,
    customer_location_id = 181,
    kontrak_id = @kontrak_id
WHERE no_unit = 5198;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-08-26',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5198
LIMIT 1;

-- Unit 5314: Kamadjaja - Karawang (Rp7.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7500000,
    customer_id = 56,
    customer_location_id = 181,
    kontrak_id = @kontrak_id
WHERE no_unit = 5314;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-08-26',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5314
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    56,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: PO PERBULAN
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2026-02-17 to 2027-02-17
-- Units: 23
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'PO PERBULAN',
    'PO_ONLY',
    '2026-02-17',
    '2027-02-17',
    'ACTIVE',
    'BULANAN',
    23,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 1885: Karya Perdana Engineering - Jombang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 57,
    customer_location_id = 183,
    kontrak_id = @kontrak_id
WHERE no_unit = 1885;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1885
LIMIT 1;

-- ⚠ WARNING: Location 'Sleman' for customer 'Karya Perdana Engineering' not found (unit 3843)
-- Unit 3843: Karya Perdana Engineering - Sleman (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 57,
    kontrak_id = @kontrak_id
WHERE no_unit = 3843;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3843
LIMIT 1;

-- ⚠ WARNING: Location 'Klaten' for customer 'Karya Perdana Engineering' not found (unit 3852)
-- Unit 3852: Karya Perdana Engineering - Klaten (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 57,
    kontrak_id = @kontrak_id
WHERE no_unit = 3852;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3852
LIMIT 1;

-- ⚠ WARNING: Location 'Sleman' for customer 'Karya Perdana Engineering' not found (unit 3851)
-- Unit 3851: Karya Perdana Engineering - Sleman (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 57,
    kontrak_id = @kontrak_id
WHERE no_unit = 3851;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3851
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'PT LASTANA EXPRESS INDONESIA' not found (unit 3647)
-- Unit 3647: PT LASTANA EXPRESS INDONESIA -  (Rp11.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11000000,
    customer_id = 220,
    kontrak_id = @kontrak_id
WHERE no_unit = 3647;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3647
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'PT LASTANA EXPRESS INDONESIA' not found (unit 3867)
-- Unit 3867: PT LASTANA EXPRESS INDONESIA -  (Rp6.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6500000,
    customer_id = 220,
    kontrak_id = @kontrak_id
WHERE no_unit = 3867;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3867
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'PT LASTANA EXPRESS INDONESIA' not found (unit 1529)
-- Unit 1529: PT LASTANA EXPRESS INDONESIA -  (Rp8.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8500000,
    customer_id = 220,
    kontrak_id = @kontrak_id
WHERE no_unit = 1529;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1529
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'PT LASTANA EXPRESS INDONESIA' not found (unit 2320)
-- Unit 2320: PT LASTANA EXPRESS INDONESIA -  (Rp12.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12000000,
    customer_id = 220,
    kontrak_id = @kontrak_id
WHERE no_unit = 2320;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2320
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'PT LASTANA EXPRESS INDONESIA' not found (unit 3542)
-- Unit 3542: PT LASTANA EXPRESS INDONESIA -  (Rp8.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8500000,
    customer_id = 220,
    kontrak_id = @kontrak_id
WHERE no_unit = 3542;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3542
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'PT LASTANA EXPRESS INDONESIA' not found (unit 3542)
-- Unit 3542: PT LASTANA EXPRESS INDONESIA -  (Rp16.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 16800000,
    customer_id = 220,
    kontrak_id = @kontrak_id
WHERE no_unit = 3542;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3542
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'PT LASTANA EXPRESS INDONESIA' not found (unit 5269)
-- Unit 5269: PT LASTANA EXPRESS INDONESIA -  (Rp16.350.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 16350000,
    customer_id = 220,
    kontrak_id = @kontrak_id
WHERE no_unit = 5269;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5269
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'PT LASTANA EXPRESS INDONESIA' not found (unit 5270)
-- Unit 5270: PT LASTANA EXPRESS INDONESIA -  (Rp16.350.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 16350000,
    customer_id = 220,
    kontrak_id = @kontrak_id
WHERE no_unit = 5270;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5270
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'PT LASTANA EXPRESS INDONESIA' not found (unit 5271)
-- Unit 5271: PT LASTANA EXPRESS INDONESIA -  (Rp16.350.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 16350000,
    customer_id = 220,
    kontrak_id = @kontrak_id
WHERE no_unit = 5271;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5271
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'PT LASTANA EXPRESS INDONESIA' not found (unit 5272)
-- Unit 5272: PT LASTANA EXPRESS INDONESIA -  (Rp16.350.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 16350000,
    customer_id = 220,
    kontrak_id = @kontrak_id
WHERE no_unit = 5272;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5272
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'PT Prokemas Adhikari Kreasi' not found (unit 5062)
-- Unit 5062: PT Prokemas Adhikari Kreasi -  (Rp30.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 30500000,
    customer_id = 79,
    kontrak_id = @kontrak_id
WHERE no_unit = 5062;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-01-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5062
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'PT Prokemas Adhikari Kreasi' not found (unit 5563)
-- Unit 5563: PT Prokemas Adhikari Kreasi -  (Rp12.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12000000,
    customer_id = 79,
    kontrak_id = @kontrak_id
WHERE no_unit = 5563;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-01-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5563
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'PT Prokemas Adhikari Kreasi' not found (unit 5564)
-- Unit 5564: PT Prokemas Adhikari Kreasi -  (Rp12.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12000000,
    customer_id = 79,
    kontrak_id = @kontrak_id
WHERE no_unit = 5564;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-01-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5564
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'PT Prokemas Adhikari Kreasi' not found (unit 5565)
-- Unit 5565: PT Prokemas Adhikari Kreasi -  (Rp12.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12000000,
    customer_id = 79,
    kontrak_id = @kontrak_id
WHERE no_unit = 5565;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-01-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5565
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'PT Prokemas Adhikari Kreasi' not found (unit 5566)
-- Unit 5566: PT Prokemas Adhikari Kreasi -  (Rp12.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12000000,
    customer_id = 79,
    kontrak_id = @kontrak_id
WHERE no_unit = 5566;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-01-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5566
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'PT Prokemas Adhikari Kreasi' not found (unit 5608)
-- Unit 5608: PT Prokemas Adhikari Kreasi -  (Rp30.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 30500000,
    customer_id = 79,
    kontrak_id = @kontrak_id
WHERE no_unit = 5608;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-01-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5608
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'PT Prokemas Adhikari Kreasi' not found (unit 5643)
-- Unit 5643: PT Prokemas Adhikari Kreasi -  (Rp12.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12000000,
    customer_id = 79,
    kontrak_id = @kontrak_id
WHERE no_unit = 5643;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-01-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5643
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'PT Prokemas Adhikari Kreasi' not found (unit 5604)
-- Unit 5604: PT Prokemas Adhikari Kreasi -  (Rp30.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 30500000,
    customer_id = 79,
    kontrak_id = @kontrak_id
WHERE no_unit = 5604;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-01-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5604
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'PT Prokemas Adhikari Kreasi' not found (unit 5606)
-- Unit 5606: PT Prokemas Adhikari Kreasi -  (Rp30.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 30500000,
    customer_id = 79,
    kontrak_id = @kontrak_id
WHERE no_unit = 5606;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-01-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5606
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    57,
    @kontrak_id,
    1
);
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    220,
    @kontrak_id,
    1
);
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    79,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 71112519
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2024-01-08 to 2027-01-08
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '71112519',
    'PO_ONLY',
    '2024-01-08',
    '2027-01-08',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3653: Kati Kartika Murni - Karawaci (Rp41.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 41500000,
    customer_id = 163,
    customer_location_id = 405,
    kontrak_id = @kontrak_id
WHERE no_unit = 3653;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-01-08',
    '2027-01-08',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3653
LIMIT 1;

-- Unit 3653: KKM Karawaci - Karawaci (Rp18.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 18500000,
    customer_id = 205,
    customer_location_id = 455,
    kontrak_id = @kontrak_id
WHERE no_unit = 3653;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-08-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3653
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    163,
    @kontrak_id,
    1
);
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    205,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: Agrement KCC-SML
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2026-01-10 to 2026-31-12
-- Units: 49
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'Agrement KCC-SML',
    'CONTRACT',
    '2026-01-10',
    '2026-31-12',
    'ACTIVE',
    'BULANAN',
    49,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 2473: KCC GLASS - Batang (Rp12.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12500000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 2473;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-01-10',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2473
LIMIT 1;

-- Unit 2686: KCC GLASS - Batang (Rp39.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 39000000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 2686;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2686
LIMIT 1;

-- Unit 707: KCC GLASS - Batang (Rp39.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 39000000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 707;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 707
LIMIT 1;

-- Unit 2044: KCC GLASS - Batang (Rp12.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12500000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 2044;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2044
LIMIT 1;

-- Unit 2081: KCC GLASS - Batang (Rp12.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12500000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 2081;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2081
LIMIT 1;

-- Unit 2416: KCC GLASS - Batang (Rp25.300.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 25300000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 2416;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2416
LIMIT 1;

-- Unit 2476: KCC GLASS - Batang (Rp12.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12500000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 2476;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-11',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2476
LIMIT 1;

-- Unit 2535: KCC GLASS - Batang (Rp25.300.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 25300000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 2535;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2535
LIMIT 1;

-- Unit 2772: KCC GLASS - Batang (Rp12.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12500000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 2772;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2772
LIMIT 1;

-- Unit 3153: KCC GLASS - Batang (Rp25.300.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 25300000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 3153;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-01-13',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3153
LIMIT 1;

-- Unit 3839: KCC GLASS - Batang (Rp6.900.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6900000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 3839;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3839
LIMIT 1;

-- Unit 3935: KCC GLASS - Batang (Rp6.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6500000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 3935;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3935
LIMIT 1;

-- Unit 3979: KCC GLASS - Batang (Rp6.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6500000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 3979;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3979
LIMIT 1;

-- Unit 5036: KCC GLASS - Batang (Rp12.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12500000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 5036;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5036
LIMIT 1;

-- Unit 5038: KCC GLASS - Batang (Rp12.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12500000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 5038;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5038
LIMIT 1;

-- Unit 5234: KCC GLASS - Batang (Rp22.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 22800000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 5234;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5234
LIMIT 1;

-- Unit 5235: KCC GLASS - Batang (Rp22.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 22800000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 5235;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5235
LIMIT 1;

-- Unit 5236: KCC GLASS - Batang (Rp22.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 22800000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 5236;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5236
LIMIT 1;

-- Unit 5237: KCC GLASS - Batang (Rp22.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 22800000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 5237;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5237
LIMIT 1;

-- Unit 5256: KCC GLASS - Batang (Rp21.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 21500000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 5256;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5256
LIMIT 1;

-- Unit 5390: KCC GLASS - Batang (Rp23.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 23800000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 5390;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5390
LIMIT 1;

-- Unit 5410: KCC GLASS - Batang (Rp26.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 26800000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 5410;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5410
LIMIT 1;

-- Unit 5411: KCC GLASS - Batang (Rp26.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 26800000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 5411;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5411
LIMIT 1;

-- Unit 5412: KCC GLASS - Batang (Rp12.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12500000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 5412;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5412
LIMIT 1;

-- Unit 5446: KCC GLASS - Batang (Rp22.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 22800000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 5446;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5446
LIMIT 1;

-- Unit 5448: KCC GLASS - Batang (Rp22.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 22800000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 5448;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5448
LIMIT 1;

-- Unit 5465: KCC GLASS - Batang (Rp22.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 22800000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 5465;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5465
LIMIT 1;

-- Unit 5487: KCC GLASS - Batang (Rp26.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 26800000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 5487;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5487
LIMIT 1;

-- Unit 5488: KCC GLASS - Batang (Rp26.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 26800000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 5488;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5488
LIMIT 1;

-- Unit 5489: KCC GLASS - Batang (Rp26.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 26800000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 5489;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5489
LIMIT 1;

-- Unit 5569: KCC GLASS - Batang (Rp12.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12500000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 5569;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5569
LIMIT 1;

-- Unit 5674: KCC GLASS - Batang (Rp12.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12500000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 5674;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5674
LIMIT 1;

-- Unit 5675: KCC GLASS - Batang (Rp12.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12500000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 5675;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5675
LIMIT 1;

-- Unit 5702: KCC GLASS - Batang (Rp12.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12500000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 5702;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5702
LIMIT 1;

-- Unit 5777: KCC GLASS - Batang (Rp6.900.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6900000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 5777;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5777
LIMIT 1;

-- Unit 5815: KCC GLASS - Batang (Rp12.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12500000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 5815;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5815
LIMIT 1;

-- Unit 5824: KCC GLASS - Batang (Rp12.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12500000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 5824;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5824
LIMIT 1;

-- Unit 5911: KCC GLASS - Batang (Rp12.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12500000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 5911;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5911
LIMIT 1;

-- Unit 5912: KCC GLASS - Batang (Rp12.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12500000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 5912;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5912
LIMIT 1;

-- Unit 5913: KCC GLASS - Batang (Rp12.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12500000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 5913;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5913
LIMIT 1;

-- Unit 5986: KCC GLASS - Batang (Rp27.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 27500000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 5986;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-22',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5986
LIMIT 1;

-- Unit 5996: KCC GLASS - Batang (Rp12.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12500000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 5996;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-05',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5996
LIMIT 1;

-- Unit 6077: KCC GLASS - Batang (Rp10.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10000000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 6077;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-01-29',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 6077
LIMIT 1;

-- Unit 6079: KCC GLASS - Batang (Rp10.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10000000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 6079;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-01-29',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 6079
LIMIT 1;

-- Unit 5329: KCC GLASS - Batang (Rp21.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 21500000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 5329;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5329
LIMIT 1;

-- Unit 5360: KCC GLASS - Batang (Rp25.300.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 25300000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 5360;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5360
LIMIT 1;

-- Unit 5361: KCC GLASS - Batang (Rp25.300.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 25300000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 5361;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5361
LIMIT 1;

-- Unit 5399: KCC GLASS - Batang (Rp12.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12500000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 5399;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-18',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5399
LIMIT 1;

-- Unit 5447: KCC GLASS - Batang (Rp22.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 22800000,
    customer_id = 58,
    customer_location_id = 184,
    kontrak_id = @kontrak_id
WHERE no_unit = 5447;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5447
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    58,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: P0288KPT26
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-12-15 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'P0288KPT26',
    'CONTRACT',
    '2025-12-15',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 2848: Kirana Permata - Muara Angke (Rp16.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 16000000,
    customer_id = 61,
    customer_location_id = 187,
    kontrak_id = @kontrak_id
WHERE no_unit = 2848;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-15',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2848
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    61,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 700/SML/III/2024
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2026-01-01 to 2026-31-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '700/SML/III/2024',
    'CONTRACT',
    '2026-01-01',
    '2026-31-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Deltamas' for customer 'Kohler' not found (unit 1777)
-- Unit 1777: Kohler - Deltamas (Rp37.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 37000000,
    customer_id = 62,
    kontrak_id = @kontrak_id
WHERE no_unit = 1777;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-01-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1777
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    62,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: No. 084/SML/X/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-10-22 to 2027-21-10
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'No. 084/SML/X/2025',
    'CONTRACT',
    '2025-10-22',
    '2027-21-10',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Deltamas' for customer 'Kohler' not found (unit 5985)
-- Unit 5985: Kohler - Deltamas (Rp26.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 26500000,
    customer_id = 62,
    kontrak_id = @kontrak_id
WHERE no_unit = 5985;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-22',
    '2027-21-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5985
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    62,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4500004748
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2024-08-27 to 
-- Units: 4
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4500004748',
    'PO_ONLY',
    '2024-08-27',
    '',
    'ACTIVE',
    'BULANAN',
    4,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 1608: Lamipak -  (Rp10.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10800000,
    customer_id = 64,
    customer_location_id = 190,
    kontrak_id = @kontrak_id
WHERE no_unit = 1608;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-08-27',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1608
LIMIT 1;

-- Unit 2232: Lamipak -  (Rp10.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10800000,
    customer_id = 64,
    customer_location_id = 190,
    kontrak_id = @kontrak_id
WHERE no_unit = 2232;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-08-27',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2232
LIMIT 1;

-- Unit 1608: PT Lami Packaging - Tangerang (Rp10.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10800000,
    customer_id = 64,
    customer_location_id = 191,
    kontrak_id = @kontrak_id
WHERE no_unit = 1608;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-08-27',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1608
LIMIT 1;

-- Unit 2232: PT Lami Packaging - Tangerang (Rp10.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10800000,
    customer_id = 64,
    customer_location_id = 191,
    kontrak_id = @kontrak_id
WHERE no_unit = 2232;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-08-27',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2232
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    64,
    @kontrak_id,
    1
);
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    64,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4500002422
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-05-17 to 
-- Units: 4
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4500002422',
    'PO_ONLY',
    '2025-05-17',
    '',
    'ACTIVE',
    'BULANAN',
    4,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 1634: Lamipak -  (Rp11.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11000000,
    customer_id = 64,
    customer_location_id = 190,
    kontrak_id = @kontrak_id
WHERE no_unit = 1634;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-17',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1634
LIMIT 1;

-- Unit 3878: Lamipak -  (Rp12.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12000000,
    customer_id = 64,
    customer_location_id = 190,
    kontrak_id = @kontrak_id
WHERE no_unit = 3878;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-17',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3878
LIMIT 1;

-- Unit 3878: PT Lami Packaging - Tangerang (Rp12.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12000000,
    customer_id = 64,
    customer_location_id = 191,
    kontrak_id = @kontrak_id
WHERE no_unit = 3878;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-05-17',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3878
LIMIT 1;

-- Unit 1634: PT Lami Packaging - Tangerang (Rp11.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11000000,
    customer_id = 64,
    customer_location_id = 191,
    kontrak_id = @kontrak_id
WHERE no_unit = 1634;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-05-17',
    '2024-16-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1634
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    64,
    @kontrak_id,
    1
);
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    64,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4500017481
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-11-27 to 2026-26-11
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4500017481',
    'PO_ONLY',
    '2025-11-27',
    '2026-26-11',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5085: Lamipak -  (Rp12.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12000000,
    customer_id = 64,
    customer_location_id = 190,
    kontrak_id = @kontrak_id
WHERE no_unit = 5085;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-27',
    '2026-26-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5085
LIMIT 1;

-- Unit 6010: Lamipak -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 64,
    customer_location_id = 190,
    kontrak_id = @kontrak_id
WHERE no_unit = 6010;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-27',
    '2026-26-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 6010
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    64,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 450016829
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2024-10-15 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '450016829',
    'PO_ONLY',
    '2024-10-15',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5164: Lamipak -  (Rp11.200.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11200000,
    customer_id = 64,
    customer_location_id = 190,
    kontrak_id = @kontrak_id
WHERE no_unit = 5164;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-10-15',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5164
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    64,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4500016829
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-12-13 to 2026-12-03
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4500016829',
    'PO_ONLY',
    '2025-12-13',
    '2026-12-03',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5463: Lamipak -  (Rp12.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12000000,
    customer_id = 64,
    customer_location_id = 190,
    kontrak_id = @kontrak_id
WHERE no_unit = 5463;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-13',
    '2026-12-03',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5463
LIMIT 1;

-- Unit 5464: Lamipak -  (Rp12.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12000000,
    customer_id = 64,
    customer_location_id = 190,
    kontrak_id = @kontrak_id
WHERE no_unit = 5464;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-13',
    '2026-12-03',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5464
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    64,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4500011911
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-05-01 to 
-- Units: 12
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4500011911',
    'PO_ONLY',
    '2025-05-01',
    '',
    'ACTIVE',
    'BULANAN',
    12,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5614: Lamipak -  (Rp27.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 27000000,
    customer_id = 64,
    customer_location_id = 190,
    kontrak_id = @kontrak_id
WHERE no_unit = 5614;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5614
LIMIT 1;

-- Unit 5617: Lamipak -  (Rp11.200.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11200000,
    customer_id = 64,
    customer_location_id = 190,
    kontrak_id = @kontrak_id
WHERE no_unit = 5617;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5617
LIMIT 1;

-- Unit 5619: Lamipak -  (Rp11.200.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11200000,
    customer_id = 64,
    customer_location_id = 190,
    kontrak_id = @kontrak_id
WHERE no_unit = 5619;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5619
LIMIT 1;

-- Unit 5213: Lamipak -  (Rp13.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 13500000,
    customer_id = 64,
    customer_location_id = 190,
    kontrak_id = @kontrak_id
WHERE no_unit = 5213;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5213
LIMIT 1;

-- Unit 5224: Lamipak -  (Rp12.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12500000,
    customer_id = 64,
    customer_location_id = 190,
    kontrak_id = @kontrak_id
WHERE no_unit = 5224;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5224
LIMIT 1;

-- Unit 5380: Lamipak -  (Rp12.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12000000,
    customer_id = 64,
    customer_location_id = 190,
    kontrak_id = @kontrak_id
WHERE no_unit = 5380;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5380
LIMIT 1;

-- Unit 5213: PT Lami Packaging - Tangerang (Rp13.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 13500000,
    customer_id = 64,
    customer_location_id = 191,
    kontrak_id = @kontrak_id
WHERE no_unit = 5213;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-04',
    '2026-01-04',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5213
LIMIT 1;

-- Unit 5380: PT Lami Packaging - Tangerang (Rp10.200.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10200000,
    customer_id = 64,
    customer_location_id = 191,
    kontrak_id = @kontrak_id
WHERE no_unit = 5380;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-04',
    '2026-01-04',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5380
LIMIT 1;

-- Unit 5614: PT Lami Packaging - Tangerang (Rp27.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 27000000,
    customer_id = 64,
    customer_location_id = 191,
    kontrak_id = @kontrak_id
WHERE no_unit = 5614;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-04',
    '2026-01-04',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5614
LIMIT 1;

-- Unit 5617: PT Lami Packaging - Tangerang (Rp11.200.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11200000,
    customer_id = 64,
    customer_location_id = 191,
    kontrak_id = @kontrak_id
WHERE no_unit = 5617;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-04',
    '2026-01-04',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5617
LIMIT 1;

-- Unit 5619: PT Lami Packaging - Tangerang (Rp11.200.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11200000,
    customer_id = 64,
    customer_location_id = 191,
    kontrak_id = @kontrak_id
WHERE no_unit = 5619;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-04',
    '2026-01-04',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5619
LIMIT 1;

-- Unit 5224: PT Lami Packaging - Tangerang (Rp12.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12500000,
    customer_id = 64,
    customer_location_id = 191,
    kontrak_id = @kontrak_id
WHERE no_unit = 5224;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-04',
    '2026-01-04',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5224
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    64,
    @kontrak_id,
    1
);
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    64,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: PO/001
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-08-22 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'PO/001',
    'CONTRACT',
    '2025-08-22',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'Lenter Hidup Bersama' not found (unit 540)
-- Unit 540: Lenter Hidup Bersama -  (Rp15.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 15000000,
    customer_id = 153,
    kontrak_id = @kontrak_id
WHERE no_unit = 540;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-22',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 540
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    153,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 298/SML/IV/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-04-23 to 2028-04-23
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '298/SML/IV/2025',
    'CONTRACT',
    '2025-04-23',
    '2028-04-23',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'PT Sailun Manufacturing Indonesia' for customer 'Matahari Rental Teknik (Zhongseng)' not found (unit 3151)
-- Unit 3151: Matahari Rental Teknik (Zhongseng) - PT Sailun Manufacturing Indonesia (Rp8.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8000000,
    customer_id = 134,
    kontrak_id = @kontrak_id
WHERE no_unit = 3151;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-04-23',
    '2028-04-23',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3151
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    134,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 002/1/2026
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2026-01-08 to 2026-07-02
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '002/1/2026',
    'CONTRACT',
    '2026-01-08',
    '2026-07-02',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 2466: Matahari Rental Teknik (Zhongseng) - Wuxi Xin Yuan Chuan Construction Engineering-Karawang (Rp14.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 14000000,
    customer_id = 134,
    customer_location_id = 353,
    kontrak_id = @kontrak_id
WHERE no_unit = 2466;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-01-08',
    '2026-07-02',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2466
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    134,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 007/PUR/MG/IV/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-01-01 to 2027-31-12
-- Units: 4
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '007/PUR/MG/IV/2025',
    'CONTRACT',
    '2025-01-01',
    '2027-31-12',
    'ACTIVE',
    'BULANAN',
    4,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'Mulia Glass' not found (unit 3853)
-- Unit 3853: Mulia Glass -  (Rp10.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10000000,
    customer_id = 69,
    kontrak_id = @kontrak_id
WHERE no_unit = 3853;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2027-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3853
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'Mulia Glass' not found (unit 3855)
-- Unit 3855: Mulia Glass -  (Rp10.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10000000,
    customer_id = 69,
    kontrak_id = @kontrak_id
WHERE no_unit = 3855;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2027-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3855
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'Mulia Glass' not found (unit 5190)
-- Unit 5190: Mulia Glass -  (Rp17.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 17500000,
    customer_id = 69,
    kontrak_id = @kontrak_id
WHERE no_unit = 5190;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2027-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5190
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'Mulia Glass' not found (unit 5391)
-- Unit 5391: Mulia Glass -  (Rp17.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 17500000,
    customer_id = 69,
    kontrak_id = @kontrak_id
WHERE no_unit = 5391;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2027-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5391
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    69,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: Spare
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2026-02-17 to 2027-02-17
-- Units: 3
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'Spare',
    'CONTRACT',
    '2026-02-17',
    '2027-02-17',
    'ACTIVE',
    'BULANAN',
    3,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 1580: NON FERINDO - Tanggerang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 118,
    customer_location_id = 322,
    kontrak_id = @kontrak_id
WHERE no_unit = 1580;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1580
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'Papandayan' not found (unit 3129)
-- Unit 3129: Papandayan -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 76,
    kontrak_id = @kontrak_id
WHERE no_unit = 3129;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3129
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'PT Phoenix Resources International' not found (unit 5523)
-- Unit 5523: PT Phoenix Resources International -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 192,
    kontrak_id = @kontrak_id
WHERE no_unit = 5523;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5523
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    118,
    @kontrak_id,
    1
);
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    76,
    @kontrak_id,
    1
);
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    192,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 26000707
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2026-01-21 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '26000707',
    'PO_ONLY',
    '2026-01-21',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 2062: NON FERINDO - Tanggerang (Rp9.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9000000,
    customer_id = 118,
    customer_location_id = 322,
    kontrak_id = @kontrak_id
WHERE no_unit = 2062;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-01-21',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2062
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    118,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 26000919
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2026-01-01 to 2026-01-02
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '26000919',
    'PO_ONLY',
    '2026-01-01',
    '2026-01-02',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3754: NON FERINDO - Tanggerang (Rp12.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12000000,
    customer_id = 118,
    customer_location_id = 322,
    kontrak_id = @kontrak_id
WHERE no_unit = 3754;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-01-01',
    '2026-01-02',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3754
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    118,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: SPXID-PO-122390-1
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-03-01 to 
-- Units: 3
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'SPXID-PO-122390-1',
    'CONTRACT',
    '2025-03-01',
    '',
    'ACTIVE',
    'BULANAN',
    3,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 2155: Nusantara Ekpres Kilat (Shopee) - Marunda (Rp5.850.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 5850000,
    customer_id = 71,
    customer_location_id = 216,
    kontrak_id = @kontrak_id
WHERE no_unit = 2155;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-03-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2155
LIMIT 1;

-- Unit 2150: Nusantara Ekpres Kilat (Shopee) - Marunda (Rp5.850.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 5850000,
    customer_id = 71,
    customer_location_id = 216,
    kontrak_id = @kontrak_id
WHERE no_unit = 2150;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-03-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2150
LIMIT 1;

-- Unit 2167: Nusantara Ekpres Kilat (Shopee) - Marunda (Rp5.850.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 5850000,
    customer_id = 71,
    customer_location_id = 216,
    kontrak_id = @kontrak_id
WHERE no_unit = 2167;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-03-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2167
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    71,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: SPXID-PO-1223902
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-03-01 to 
-- Units: 3
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'SPXID-PO-1223902',
    'CONTRACT',
    '2025-03-01',
    '',
    'ACTIVE',
    'BULANAN',
    3,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 2155: Nusantara Ekspress Kilat (Shopee) - Marunda (Rp5.850.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 5850000,
    customer_id = 71,
    customer_location_id = 216,
    kontrak_id = @kontrak_id
WHERE no_unit = 2155;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-03-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2155
LIMIT 1;

-- Unit 2167: Nusantara Ekspress Kilat (Shopee) - Marunda (Rp5.850.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 5850000,
    customer_id = 71,
    customer_location_id = 216,
    kontrak_id = @kontrak_id
WHERE no_unit = 2167;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-03-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2167
LIMIT 1;

-- Unit 2150: Nusantara Ekspress Kilat (Shopee) - Marunda (Rp5.850.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 5850000,
    customer_id = 71,
    customer_location_id = 216,
    kontrak_id = @kontrak_id
WHERE no_unit = 2150;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-03-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2150
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    71,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 037//SML/VI/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-06-23 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '037//SML/VI/2025',
    'CONTRACT',
    '2025-06-23',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Pasar Rebo' for customer 'Nusantara Parkerizing' not found (unit 2657)
-- Unit 2657: Nusantara Parkerizing - Pasar Rebo (Rp2.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 2000000,
    customer_id = 72,
    kontrak_id = @kontrak_id
WHERE no_unit = 2657;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-23',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2657
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    72,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 023/SML/IV/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-05-05 to 2027-05-05
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '023/SML/IV/2025',
    'CONTRACT',
    '2025-05-05',
    '2027-05-05',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Pasar Rebo' for customer 'Nusantara Parkerizing' not found (unit 3688)
-- Unit 3688: Nusantara Parkerizing - Pasar Rebo (Rp8.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8500000,
    customer_id = 72,
    kontrak_id = @kontrak_id
WHERE no_unit = 3688;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-05',
    '2027-05-05',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3688
LIMIT 1;

-- ⚠ WARNING: Location 'Pasar Rebo' for customer 'Nusantara Parkerizing' not found (unit 5148)
-- Unit 5148: Nusantara Parkerizing - Pasar Rebo (Rp8.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8500000,
    customer_id = 72,
    kontrak_id = @kontrak_id
WHERE no_unit = 5148;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-05',
    '2027-05-05',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5148
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    72,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: dalam proses
-- Type: CONTRACT | Status: PENDING
-- Period: 2026-01-01 to 2026-31-12
-- Units: 8
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'dalam proses',
    'CONTRACT',
    '2026-01-01',
    '2026-31-12',
    'PENDING',
    'BULANAN',
    8,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 2891: Oji Sinarmas Packaging -  (Rp9.700.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9700000,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 2891;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-01-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2891
LIMIT 1;

-- Unit 2892: Oji Sinarmas Packaging -  (Rp14.700.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 14700000,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 2892;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-01-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2892
LIMIT 1;

-- Unit 3165: Oji Sinarmas Packaging -  (Rp32.700.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 32700000,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 3165;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-01-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3165
LIMIT 1;

-- Unit 2889: Oji Sinarmas Packaging -  (Rp10.700.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10700000,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 2889;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-01-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2889
LIMIT 1;

-- Unit 2890: Oji Sinarmas Packaging -  (Rp9.700.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9700000,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 2890;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-01-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2890
LIMIT 1;

-- Unit 3252: Oji Sinarmas Packaging -  (Rp32.700.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 32700000,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 3252;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-01-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3252
LIMIT 1;

-- Unit 6033: PT DHL Suply Chains - Cileungsi (Rp16.200.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 16200000,
    customer_id = 30,
    customer_location_id = 98,
    kontrak_id = @kontrak_id
WHERE no_unit = 6033;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-13',
    '2025-12-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 6033
LIMIT 1;

-- Unit 5753: PT DHL Suply Chains - Cileungsi (Rp16.200.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 16200000,
    customer_id = 30,
    customer_location_id = 98,
    kontrak_id = @kontrak_id
WHERE no_unit = 5753;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-06',
    '2026-05-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5753
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    73,
    @kontrak_id,
    1
);
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    30,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 134/ADD-X/TBU-SML/XII/2023
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-04-01 to 
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '134/ADD-X/TBU-SML/XII/2023',
    'CONTRACT',
    '2025-04-01',
    '',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 2451: OKI Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 2451;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-04-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2451
LIMIT 1;

-- Unit 2600: OKI Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 2600;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-04-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2600
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    73,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 3FRK/OKI2/HESC-SML/III/2023/ADD-III
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2024-10-15 to 
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '3FRK/OKI2/HESC-SML/III/2023/ADD-III',
    'CONTRACT',
    '2024-10-15',
    '',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 1648: OKI Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 1648;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-10-15',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1648
LIMIT 1;

-- Unit 3017: OKI Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 3017;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-10-15',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3017
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    73,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4610018650/17637,17638/OKI2-SML/V/2023/ADD-II
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2024-11-29 to 
-- Units: 3
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4610018650/17637,17638/OKI2-SML/V/2023/ADD-II',
    'CONTRACT',
    '2024-11-29',
    '',
    'ACTIVE',
    'BULANAN',
    3,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 1967: OKI Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 1967;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-11-29',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1967
LIMIT 1;

-- Unit 2046: OKI Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 2046;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-11-29',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2046
LIMIT 1;

-- Unit 2960: OKI Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 2960;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-11-29',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2960
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    73,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4630000867-20371/OKI-SML/V/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-10-01 to 
-- Units: 6
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4630000867-20371/OKI-SML/V/2025',
    'CONTRACT',
    '2025-10-01',
    '',
    'ACTIVE',
    'BULANAN',
    6,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 2360: OKI Pulp and Paper -  (Rp70.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 70000000,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 2360;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2360
LIMIT 1;

-- Unit 2361: OKI Pulp and Paper -  (Rp70.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 70000000,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 2361;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2361
LIMIT 1;

-- Unit 2362: OKI Pulp and Paper -  (Rp70.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 70000000,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 2362;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2362
LIMIT 1;

-- Unit 2689: OKI Pulp and Paper -  (Rp70.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 70000000,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 2689;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2689
LIMIT 1;

-- Unit 2903: OKI Pulp and Paper -  (Rp51.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 51500000,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 2903;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2903
LIMIT 1;

-- Unit 5621: OKI Pulp and Paper -  (Rp48.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 48500000,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5621;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5621
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    73,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 130/ADD-VIII/TBU-SML/III/2024
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-01-16 to 2026-01-16
-- Units: 3
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '130/ADD-VIII/TBU-SML/III/2024',
    'CONTRACT',
    '2025-01-16',
    '2026-01-16',
    'ACTIVE',
    'BULANAN',
    3,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 2391: OKI Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 2391;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-16',
    '2026-01-16',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2391
LIMIT 1;

-- Unit 3306: OKI Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 3306;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-16',
    '2026-01-16',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3306
LIMIT 1;

-- Unit 3307: OKI Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 3307;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-16',
    '2026-01-16',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3307
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    73,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 016/RCD-SML/PUD-OKI/IX/2022
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2022-09-01 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '016/RCD-SML/PUD-OKI/IX/2022',
    'CONTRACT',
    '2022-09-01',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 2827: OKI Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 2827;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-09-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2827
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    73,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 065/LGD-SML/OUTPUT/III/2024 ADD-I
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-04-01 to 
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '065/LGD-SML/OUTPUT/III/2024 ADD-I',
    'CONTRACT',
    '2025-04-01',
    '',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3152: OKI Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 3152;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-04-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3152
LIMIT 1;

-- Unit 3291: OKI Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 3291;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-04-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3291
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    73,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4610018422/15530/TBU-SML/III/2023 ADD-II
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2024-10-01 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4610018422/15530/TBU-SML/III/2023 ADD-II',
    'CONTRACT',
    '2024-10-01',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3305: OKI Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 3305;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-10-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3305
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    73,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 133/ADD-VII/PDM-SML/III/2024
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2024-10-01 to 2025-10-01
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '133/ADD-VII/PDM-SML/III/2024',
    'CONTRACT',
    '2024-10-01',
    '2025-10-01',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3627: OKI Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 3627;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-10-01',
    '2025-10-01',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3627
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    73,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4630000581/19716/OKI-SML/XI/2024
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2024-12-01 to 2027-30-11
-- Units: 3
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4630000581/19716/OKI-SML/XI/2024',
    'CONTRACT',
    '2024-12-01',
    '2027-30-11',
    'ACTIVE',
    'BULANAN',
    3,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3859: OKI Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 3859;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3859
LIMIT 1;

-- Unit 3980: OKI Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 3980;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3980
LIMIT 1;

-- Unit 3857: OKI Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 3857;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3857
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    73,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4630000537/20874-22741/OKI-SML/IX/24
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-05-01 to 
-- Units: 17
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4630000537/20874-22741/OKI-SML/IX/24',
    'CONTRACT',
    '2025-05-01',
    '',
    'ACTIVE',
    'BULANAN',
    17,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5252: OKI Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5252;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5252
LIMIT 1;

-- Unit 5255: OKI Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5255;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5255
LIMIT 1;

-- Unit 5255: OKI Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5255;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5255
LIMIT 1;

-- Unit 5537: OKI Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5537;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5537
LIMIT 1;

-- Unit 5540: OKI Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5540;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5540
LIMIT 1;

-- Unit 5541: OKI Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5541;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5541
LIMIT 1;

-- Unit 5542: OKI Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5542;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5542
LIMIT 1;

-- Unit 5558: OKI Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5558;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5558
LIMIT 1;

-- Unit 5590: OKI Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5590;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5590
LIMIT 1;

-- Unit 5591: OKI Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5591;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5591
LIMIT 1;

-- Unit 5634: OKI Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5634;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5634
LIMIT 1;

-- Unit 5644: OKI Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5644;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5644
LIMIT 1;

-- Unit 5670: OKI Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5670;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5670
LIMIT 1;

-- Unit 5592: OKI Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5592;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5592
LIMIT 1;

-- Unit 5593: OKI Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5593;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5593
LIMIT 1;

-- Unit 5669: OKI Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5669;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5669
LIMIT 1;

-- Unit 5683: OKI Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5683;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5683
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    73,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4630000537/21633/OKI-SML/IX/2024
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2024-12-01 to 2025-30-11
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4630000537/21633/OKI-SML/IX/2024',
    'CONTRACT',
    '2024-12-01',
    '2025-30-11',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5450: OKI Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5450;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-12-01',
    '2025-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5450
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    73,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4630000537/20948/OKI-SML/IX/2024
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2024-12-01 to 2027-30-11
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4630000537/20948/OKI-SML/IX/2024',
    'CONTRACT',
    '2024-12-01',
    '2027-30-11',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5490: OKI Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5490;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5490
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    73,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4630000537/21146/OKI-SML/IX/2024
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2024-12-01 to 2027-30-11
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4630000537/21146/OKI-SML/IX/2024',
    'CONTRACT',
    '2024-12-01',
    '2027-30-11',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5491: OKI Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5491;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5491
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    73,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 061/ADD-IV/SEPORT-SML/IV/2023/ADD-5
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-02-01 to 
-- Units: 3
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '061/ADD-IV/SEPORT-SML/IV/2023/ADD-5',
    'CONTRACT',
    '2025-02-01',
    '',
    'ACTIVE',
    'BULANAN',
    3,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5510: OKI Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5510;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5510
LIMIT 1;

-- Unit 5511: OKI Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5511;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5511
LIMIT 1;

-- Unit 5512: OKI Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 73,
    customer_location_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5512;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5512
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    73,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4531003793
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-01-01 to 2025-31-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4531003793',
    'PO_ONLY',
    '2025-01-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3763: OTSUKA DISTRIBUTION - Sidoarjo (Rp6.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6500000,
    customer_id = 104,
    customer_location_id = 299,
    kontrak_id = @kontrak_id
WHERE no_unit = 3763;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3763
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    104,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4531003652
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-01-01 to 2025-31-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4531003652',
    'PO_ONLY',
    '2025-01-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3767: OTSUKA DISTRIBUTION - Bandung (Rp6.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6500000,
    customer_id = 104,
    customer_location_id = 294,
    kontrak_id = @kontrak_id
WHERE no_unit = 3767;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3767
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    104,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4531003649
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-01-01 to 2025-31-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4531003649',
    'PO_ONLY',
    '2025-01-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3794: OTSUKA DISTRIBUTION - Bekasi (Rp6.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6500000,
    customer_id = 104,
    customer_location_id = 302,
    kontrak_id = @kontrak_id
WHERE no_unit = 3794;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3794
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    104,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4531003668
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-01-01 to 2025-31-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4531003668',
    'PO_ONLY',
    '2025-01-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3793: OTSUKA DISTRIBUTION - Cikarang (Rp6.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6500000,
    customer_id = 104,
    customer_location_id = 297,
    kontrak_id = @kontrak_id
WHERE no_unit = 3793;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3793
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    104,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4531003648
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-01-01 to 2025-31-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4531003648',
    'PO_ONLY',
    '2025-01-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3769: OTSUKA DISTRIBUTION - CIKUPA (Rp6.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6500000,
    customer_id = 104,
    customer_location_id = 301,
    kontrak_id = @kontrak_id
WHERE no_unit = 3769;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3769
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    104,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4531003783
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-01-01 to 2025-31-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4531003783',
    'PO_ONLY',
    '2025-01-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3765: OTSUKA DISTRIBUTION - Malang (Rp6.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6500000,
    customer_id = 104,
    customer_location_id = 300,
    kontrak_id = @kontrak_id
WHERE no_unit = 3765;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3765
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    104,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4531003784
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-01-01 to 2025-31-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4531003784',
    'PO_ONLY',
    '2025-01-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3770: OTSUKA DISTRIBUTION - Medan (Rp6.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6500000,
    customer_id = 104,
    customer_location_id = 305,
    kontrak_id = @kontrak_id
WHERE no_unit = 3770;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3770
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    104,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4531003651
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-01-01 to 2025-31-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4531003651',
    'PO_ONLY',
    '2025-01-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3795: OTSUKA DISTRIBUTION - Pasar Rebo (Rp6.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6500000,
    customer_id = 104,
    customer_location_id = 303,
    kontrak_id = @kontrak_id
WHERE no_unit = 3795;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3795
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    104,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4531003650
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-01-01 to 2025-31-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4531003650',
    'PO_ONLY',
    '2025-01-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3768: OTSUKA DISTRIBUTION - Pulo Gadung (Rp6.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6500000,
    customer_id = 104,
    customer_location_id = 295,
    kontrak_id = @kontrak_id
WHERE no_unit = 3768;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3768
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    104,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4531003780
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-01-01 to 2025-31-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4531003780',
    'PO_ONLY',
    '2025-01-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3796: OTSUKA DISTRIBUTION - Sawangan (Rp6.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6500000,
    customer_id = 104,
    customer_location_id = 304,
    kontrak_id = @kontrak_id
WHERE no_unit = 3796;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3796
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    104,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4531003792
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-01-01 to 2025-31-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4531003792',
    'PO_ONLY',
    '2025-01-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3764: OTSUKA DISTRIBUTION - Surabaya (Rp6.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6500000,
    customer_id = 104,
    customer_location_id = 298,
    kontrak_id = @kontrak_id
WHERE no_unit = 3764;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3764
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    104,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4531003791
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-01-01 to 2025-31-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4531003791',
    'PO_ONLY',
    '2025-01-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3766: OTSUKA DISTRIBUTION - Surakarta (Rp6.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6500000,
    customer_id = 104,
    customer_location_id = 292,
    kontrak_id = @kontrak_id
WHERE no_unit = 3766;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3766
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    104,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4531003654
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-01-01 to 2025-31-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4531003654',
    'PO_ONLY',
    '2025-01-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3772: OTSUKA DISTRIBUTION - Tangerang (Rp6.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6500000,
    customer_id = 104,
    customer_location_id = 296,
    kontrak_id = @kontrak_id
WHERE no_unit = 3772;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3772
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    104,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4531003653
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-01-01 to 2025-31-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4531003653',
    'PO_ONLY',
    '2025-01-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3771: OTSUKA DISTRIBUTION - Yogyakarta (Rp6.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6500000,
    customer_id = 104,
    customer_location_id = 293,
    kontrak_id = @kontrak_id
WHERE no_unit = 3771;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3771
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    104,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: Service Agreement 01 Maret 2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-03-01 to 
-- Units: 12
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'Service Agreement 01 Maret 2025',
    'CONTRACT',
    '2025-03-01',
    '',
    'ACTIVE',
    'BULANAN',
    12,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'Papandayan' not found (unit 5162)
-- Unit 5162: Papandayan -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 76,
    kontrak_id = @kontrak_id
WHERE no_unit = 5162;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-03-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5162
LIMIT 1;

-- Unit 5186: Papandayan - MENGGER (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 76,
    customer_location_id = 223,
    kontrak_id = @kontrak_id
WHERE no_unit = 5186;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-19',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5186
LIMIT 1;

-- Unit 5609: Papandayan - BINTANG AGUNG (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 76,
    customer_location_id = 225,
    kontrak_id = @kontrak_id
WHERE no_unit = 5609;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-09',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5609
LIMIT 1;

-- ⚠ WARNING: Location 'Ramatex' for customer 'Papandayan' not found (unit 5572)
-- Unit 5572: Papandayan - Ramatex (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 76,
    kontrak_id = @kontrak_id
WHERE no_unit = 5572;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-15',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5572
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'Papandayan' not found (unit 5539)
-- Unit 5539: Papandayan -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 76,
    kontrak_id = @kontrak_id
WHERE no_unit = 5539;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-03-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5539
LIMIT 1;

-- Unit 5611: Papandayan - MENGGER (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 76,
    customer_location_id = 223,
    kontrak_id = @kontrak_id
WHERE no_unit = 5611;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-09',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5611
LIMIT 1;

-- Unit 5615: Papandayan - MENGGER (Rp17.300.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 17300000,
    customer_id = 76,
    customer_location_id = 223,
    kontrak_id = @kontrak_id
WHERE no_unit = 5615;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-20',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5615
LIMIT 1;

-- Unit 5616: Papandayan - MENGGER (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 76,
    customer_location_id = 223,
    kontrak_id = @kontrak_id
WHERE no_unit = 5616;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-20',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5616
LIMIT 1;

-- Unit 5599: Papandayan - MENGGER (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 76,
    customer_location_id = 223,
    kontrak_id = @kontrak_id
WHERE no_unit = 5599;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-15',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5599
LIMIT 1;

-- Unit 5610: Papandayan - BINTANG AGUNG (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 76,
    customer_location_id = 225,
    kontrak_id = @kontrak_id
WHERE no_unit = 5610;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-30',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5610
LIMIT 1;

-- Unit 5612: Papandayan - MENGGER (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 76,
    customer_location_id = 223,
    kontrak_id = @kontrak_id
WHERE no_unit = 5612;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-09',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5612
LIMIT 1;

-- Unit 5613: Papandayan - MENGGER (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 76,
    customer_location_id = 223,
    kontrak_id = @kontrak_id
WHERE no_unit = 5613;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-09',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5613
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    76,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4700450479
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2026-02-08 to 2027-07-02
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4700450479',
    'PO_ONLY',
    '2026-02-08',
    '2027-07-02',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3003: Perfetty Van Melle - Jakarta (Rp10.200.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10200000,
    customer_id = 206,
    customer_location_id = 456,
    kontrak_id = @kontrak_id
WHERE no_unit = 3003;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-08',
    '2027-07-02',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3003
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    206,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 156/PO/PINDO-SML/IV/2023
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2023-01-04 to 2024-01-04
-- Units: 15
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '156/PO/PINDO-SML/IV/2023',
    'CONTRACT',
    '2023-01-04',
    '2024-01-04',
    'ACTIVE',
    'BULANAN',
    15,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'Pindo Deli Pulp and Paper' not found (unit 3143)
-- Unit 3143: Pindo Deli Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 3143;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-04',
    '2024-01-04',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3143
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'Pindo Deli Pulp and Paper' not found (unit 3361)
-- Unit 3361: Pindo Deli Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 3361;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-04',
    '2024-01-04',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3361
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'Pindo Deli Pulp and Paper' not found (unit 3104)
-- Unit 3104: Pindo Deli Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 3104;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-04',
    '2024-01-04',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3104
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'Pindo Deli Pulp and Paper' not found (unit 3226)
-- Unit 3226: Pindo Deli Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 3226;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-04',
    '2024-01-04',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3226
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'Pindo Deli Pulp and Paper' not found (unit 3251)
-- Unit 3251: Pindo Deli Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 3251;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-04',
    '2024-01-04',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3251
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'Pindo Deli Pulp and Paper' not found (unit 3323)
-- Unit 3323: Pindo Deli Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 3323;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-04',
    '2024-01-04',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3323
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'Pindo Deli Pulp and Paper' not found (unit 3324)
-- Unit 3324: Pindo Deli Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 3324;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-04',
    '2024-01-04',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3324
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'Pindo Deli Pulp and Paper' not found (unit 3358)
-- Unit 3358: Pindo Deli Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 3358;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-04',
    '2024-01-04',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3358
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'Pindo Deli Pulp and Paper' not found (unit 3360)
-- Unit 3360: Pindo Deli Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 3360;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-04',
    '2024-01-04',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3360
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'Pindo Deli Pulp and Paper' not found (unit 5018)
-- Unit 5018: Pindo Deli Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5018;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-04',
    '2024-01-04',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5018
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'Pindo Deli Pulp and Paper' not found (unit 5019)
-- Unit 5019: Pindo Deli Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5019;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-04',
    '2024-01-04',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5019
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'Pindo Deli Pulp and Paper' not found (unit 5020)
-- Unit 5020: Pindo Deli Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5020;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-04',
    '2024-01-04',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5020
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'Pindo Deli Pulp and Paper' not found (unit 5021)
-- Unit 5021: Pindo Deli Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5021;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-04',
    '2024-01-04',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5021
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'Pindo Deli Pulp and Paper' not found (unit 5023)
-- Unit 5023: Pindo Deli Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5023;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-04',
    '2024-01-04',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5023
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'Pindo Deli Pulp and Paper' not found (unit 5024)
-- Unit 5024: Pindo Deli Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5024;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-04',
    '2024-01-04',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5024
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    219,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 096/PO/PINDO-SML/PSM/XI/2022
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2022-01-11 to 2023-01-11
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '096/PO/PINDO-SML/PSM/XI/2022',
    'CONTRACT',
    '2022-01-11',
    '2023-01-11',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'Pindo Deli Pulp and Paper' not found (unit 3339)
-- Unit 3339: Pindo Deli Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 3339;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-01-11',
    '2023-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3339
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    219,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 345/SPD/PINDO-SML/XI/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-01-11 to 2026-01-11
-- Units: 3
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '345/SPD/PINDO-SML/XI/2025',
    'CONTRACT',
    '2025-01-11',
    '2026-01-11',
    'ACTIVE',
    'BULANAN',
    3,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'Pindo Deli Pulp and Paper' not found (unit 3345)
-- Unit 3345: Pindo Deli Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 3345;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-11',
    '2026-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3345
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'Pindo Deli Pulp and Paper' not found (unit 3347)
-- Unit 3347: Pindo Deli Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 3347;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-11',
    '2026-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3347
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'Pindo Deli Pulp and Paper' not found (unit 3352)
-- Unit 3352: Pindo Deli Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 3352;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-11',
    '2026-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3352
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    219,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 320/SPD/PINDO/SML/I/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-01-01 to 2026-01-01
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '320/SPD/PINDO/SML/I/2025',
    'CONTRACT',
    '2025-01-01',
    '2026-01-01',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'Pindo Deli Pulp and Paper' not found (unit 3553)
-- Unit 3553: Pindo Deli Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 3553;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2026-01-01',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3553
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    219,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 351/SPD/PINDO-SML/PSM/XI/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-01-10 to 2026-01-10
-- Units: 13
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '351/SPD/PINDO-SML/PSM/XI/2025',
    'CONTRACT',
    '2025-01-10',
    '2026-01-10',
    'ACTIVE',
    'BULANAN',
    13,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'Pindo Deli Pulp and Paper' not found (unit 5697)
-- Unit 5697: Pindo Deli Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5697;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-10',
    '2026-01-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5697
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'Pindo Deli Pulp and Paper' not found (unit 5705)
-- Unit 5705: Pindo Deli Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5705;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-10',
    '2026-01-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5705
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'Pindo Deli Pulp and Paper' not found (unit 5706)
-- Unit 5706: Pindo Deli Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5706;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-10',
    '2026-01-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5706
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'Pindo Deli Pulp and Paper' not found (unit 5707)
-- Unit 5707: Pindo Deli Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5707;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-10',
    '2026-01-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5707
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'Pindo Deli Pulp and Paper' not found (unit 5708)
-- Unit 5708: Pindo Deli Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5708;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-10',
    '2026-01-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5708
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'Pindo Deli Pulp and Paper' not found (unit 5709)
-- Unit 5709: Pindo Deli Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5709;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-10',
    '2026-01-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5709
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'Pindo Deli Pulp and Paper' not found (unit 5710)
-- Unit 5710: Pindo Deli Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5710;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-10',
    '2026-01-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5710
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'Pindo Deli Pulp and Paper' not found (unit 5711)
-- Unit 5711: Pindo Deli Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5711;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-10',
    '2026-01-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5711
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'Pindo Deli Pulp and Paper' not found (unit 5712)
-- Unit 5712: Pindo Deli Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5712;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-10',
    '2026-01-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5712
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'Pindo Deli Pulp and Paper' not found (unit 5715)
-- Unit 5715: Pindo Deli Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5715;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-10',
    '2026-01-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5715
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'Pindo Deli Pulp and Paper' not found (unit 5716)
-- Unit 5716: Pindo Deli Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5716;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-10',
    '2026-01-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5716
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'Pindo Deli Pulp and Paper' not found (unit 5717)
-- Unit 5717: Pindo Deli Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5717;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-10',
    '2026-01-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5717
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'Pindo Deli Pulp and Paper' not found (unit 5720)
-- Unit 5720: Pindo Deli Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5720;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-10',
    '2026-01-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5720
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    219,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 344/SPD/PINDO-SML/VIII/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-01-10 to 2026-01-10
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '344/SPD/PINDO-SML/VIII/2025',
    'CONTRACT',
    '2025-01-10',
    '2026-01-10',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'Pindo Deli Pulp and Paper' not found (unit 5751)
-- Unit 5751: Pindo Deli Pulp and Paper -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 219,
    kontrak_id = @kontrak_id
WHERE no_unit = 5751;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-10',
    '2026-01-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5751
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    219,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 581/SML/II/2023
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2023-02-13 to 2026-12-02
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '581/SML/II/2023',
    'CONTRACT',
    '2023-02-13',
    '2026-12-02',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'Primacorr Mandiri' not found (unit 3230)
-- Unit 3230: Primacorr Mandiri -  (Rp22.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 22000000,
    customer_id = 188,
    kontrak_id = @kontrak_id
WHERE no_unit = 3230;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-02-13',
    '2026-12-02',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3230
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    188,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 365/SML/VIII/2024
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2026-02-17 to 2027-02-17
-- Units: 3
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '365/SML/VIII/2024',
    'CONTRACT',
    '2026-02-17',
    '2027-02-17',
    'ACTIVE',
    'BULANAN',
    3,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 2280: PT ABC Kogen - Bandung (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 136,
    customer_location_id = 355,
    kontrak_id = @kontrak_id
WHERE no_unit = 2280;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2280
LIMIT 1;

-- Unit 2294: PT ABC Kogen - Bandung (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 136,
    customer_location_id = 355,
    kontrak_id = @kontrak_id
WHERE no_unit = 2294;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2294
LIMIT 1;

-- Unit 3622: PT ABC Kogen - Bandung (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 136,
    customer_location_id = 355,
    kontrak_id = @kontrak_id
WHERE no_unit = 3622;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-09-10',
    '2025-09-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3622
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    136,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 0424/AMA/08/24
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2024-09-01 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '0424/AMA/08/24',
    'CONTRACT',
    '2024-09-01',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Semarang' for customer 'PT ADIRA MAKMUR ABADI' not found (unit 3776)
-- Unit 3776: PT ADIRA MAKMUR ABADI - Semarang (Rp8.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8500000,
    customer_id = 2,
    kontrak_id = @kontrak_id
WHERE no_unit = 3776;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-09-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3776
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    2,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 0625/AMA/08/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-09-11 to 2028-08-11
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '0625/AMA/08/2025',
    'CONTRACT',
    '2025-09-11',
    '2028-08-11',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Demak' for customer 'PT ADIRA MAKMUR ABADI' not found (unit 3779)
-- Unit 3779: PT ADIRA MAKMUR ABADI - Demak (Rp8.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8500000,
    customer_id = 2,
    kontrak_id = @kontrak_id
WHERE no_unit = 3779;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-09-11',
    '2028-08-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3779
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    2,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 0095/AMA/02/26
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2026-02-05 to 2029-04-02
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '0095/AMA/02/26',
    'CONTRACT',
    '2026-02-05',
    '2029-04-02',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Demak' for customer 'PT ADIRA MAKMUR ABADI' not found (unit 6085)
-- Unit 6085: PT ADIRA MAKMUR ABADI - Demak (Rp7.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7500000,
    customer_id = 2,
    kontrak_id = @kontrak_id
WHERE no_unit = 6085;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-05',
    '2029-04-02',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 6085
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    2,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 0278/AMA/05/25
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-05-09 to 2028-08-05
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '0278/AMA/05/25',
    'CONTRACT',
    '2025-05-09',
    '2028-08-05',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT ADIRA MAKMUR ABADI' not found (unit 5150)
-- Unit 5150: PT ADIRA MAKMUR ABADI -  (Rp8.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8500000,
    customer_id = 2,
    kontrak_id = @kontrak_id
WHERE no_unit = 5150;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-09',
    '2028-08-05',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5150
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    2,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 0025/AMA/01/26
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2026-01-12 to 2028-11-01
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '0025/AMA/01/26',
    'CONTRACT',
    '2026-01-12',
    '2028-11-01',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT ADIRA MAKMUR ABADI' not found (unit 6074)
-- Unit 6074: PT ADIRA MAKMUR ABADI -  (Rp7.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7500000,
    customer_id = 2,
    kontrak_id = @kontrak_id
WHERE no_unit = 6074;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-01-12',
    '2028-11-01',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 6074
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    2,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: PO On Progres
-- Type: CONTRACT | Status: PENDING
-- Period: 2025-01-12 to 2025-11-12
-- Units: 7
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'PO On Progres',
    'CONTRACT',
    '2025-01-12',
    '2025-11-12',
    'PENDING',
    'BULANAN',
    7,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3886: PT Bima Inti Kertas - Cileungsi (Rp12.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12000000,
    customer_id = 207,
    customer_location_id = 457,
    kontrak_id = @kontrak_id
WHERE no_unit = 3886;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-12',
    '2025-11-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3886
LIMIT 1;

-- Unit 2510: PT YCH INDONESIA - Deltamas (Rp6.250.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6250000,
    customer_id = 101,
    customer_location_id = 288,
    kontrak_id = @kontrak_id
WHERE no_unit = 2510;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-12',
    '2026-11-02',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2510
LIMIT 1;

-- Unit 821: PT YCH INDONESIA - Deltamas (Rp10.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10000000,
    customer_id = 101,
    customer_location_id = 288,
    kontrak_id = @kontrak_id
WHERE no_unit = 821;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-12',
    '2026-11-02',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 821
LIMIT 1;

-- Unit 2493: PT YCH INDONESIA - Deltamas (Rp6.250.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6250000,
    customer_id = 101,
    customer_location_id = 288,
    kontrak_id = @kontrak_id
WHERE no_unit = 2493;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-12',
    '2026-11-02',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2493
LIMIT 1;

-- Unit 2505: PT YCH INDONESIA - Deltamas (Rp6.250.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6250000,
    customer_id = 101,
    customer_location_id = 288,
    kontrak_id = @kontrak_id
WHERE no_unit = 2505;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-12',
    '2026-11-02',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2505
LIMIT 1;

-- Unit 2511: PT YCH INDONESIA - Deltamas (Rp6.250.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6250000,
    customer_id = 101,
    customer_location_id = 288,
    kontrak_id = @kontrak_id
WHERE no_unit = 2511;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-12',
    '2026-11-02',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2511
LIMIT 1;

-- Unit 3705: PT YCH INDONESIA - Deltamas (Rp10.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10000000,
    customer_id = 101,
    customer_location_id = 288,
    kontrak_id = @kontrak_id
WHERE no_unit = 3705;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-12',
    '2026-11-02',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3705
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    207,
    @kontrak_id,
    1
);
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    101,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 688/SMLII/2024
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2024-02-16 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '688/SMLII/2024',
    'CONTRACT',
    '2024-02-16',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3665: PT Bitutek - Bogor (Rp8.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8000000,
    customer_id = 13,
    customer_location_id = 66,
    kontrak_id = @kontrak_id
WHERE no_unit = 3665;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-02-16',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3665
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    13,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 103/SML/Add.1/XII/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-11-15 to 2026-14-11
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '103/SML/Add.1/XII/2025',
    'CONTRACT',
    '2025-11-15',
    '2026-14-11',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 2542: PT Bitutek - Bogor (Rp9.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9000000,
    customer_id = 13,
    customer_location_id = 66,
    kontrak_id = @kontrak_id
WHERE no_unit = 2542;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-15',
    '2026-14-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2542
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    13,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 6190000873
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-07-01 to 
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '6190000873',
    'PO_ONLY',
    '2025-07-01',
    '',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5041: PT Cipta Mapan Logistik - Cibitung (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 19,
    customer_location_id = 77,
    kontrak_id = @kontrak_id
WHERE no_unit = 5041;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5041
LIMIT 1;

-- Unit 5684: PT Cipta Mapan Logistik - Cibitung (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 19,
    customer_location_id = 77,
    kontrak_id = @kontrak_id
WHERE no_unit = 5684;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5684
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    19,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 6190000882
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-07-01 to 2025-31-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '6190000882',
    'PO_ONLY',
    '2025-07-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5168: PT Cipta Mapan Logistik - Cibitung (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 19,
    customer_location_id = 77,
    kontrak_id = @kontrak_id
WHERE no_unit = 5168;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-07-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5168
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    19,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4501607251
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-02-28 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4501607251',
    'PO_ONLY',
    '2025-02-28',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'jogjakarta' for customer 'PT CIPTA NIAGA SEMESTA' not found (unit 2236)
-- Unit 2236: PT CIPTA NIAGA SEMESTA - jogjakarta (Rp8.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8000000,
    customer_id = 22,
    kontrak_id = @kontrak_id
WHERE no_unit = 2236;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-28',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2236
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    22,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4501715413
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-06-01 to 2025-01-05
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4501715413',
    'PO_ONLY',
    '2025-06-01',
    '2025-01-05',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'krian' for customer 'PT CIPTA NIAGA SEMESTA' not found (unit 2868)
-- Unit 2868: PT CIPTA NIAGA SEMESTA - krian (Rp14.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 14500000,
    customer_id = 22,
    kontrak_id = @kontrak_id
WHERE no_unit = 2868;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-01',
    '2025-01-05',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2868
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    22,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4502179559
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2026-01-01 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4502179559',
    'PO_ONLY',
    '2026-01-01',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'tambun' for customer 'PT CIPTA NIAGA SEMESTA' not found (unit 3131)
-- Unit 3131: PT CIPTA NIAGA SEMESTA - tambun (Rp8.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8000000,
    customer_id = 22,
    kontrak_id = @kontrak_id
WHERE no_unit = 3131;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-01-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3131
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    22,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4502084914
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-10-07 to 2026-06-10
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4502084914',
    'PO_ONLY',
    '2025-10-07',
    '2026-06-10',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Tuban' for customer 'PT CIPTA NIAGA SEMESTA' not found (unit 3367)
-- Unit 3367: PT CIPTA NIAGA SEMESTA - Tuban (Rp8.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8000000,
    customer_id = 22,
    kontrak_id = @kontrak_id
WHERE no_unit = 3367;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-07',
    '2026-06-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3367
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    22,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4501945653
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-02-15 to 2025-14-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4501945653',
    'PO_ONLY',
    '2025-02-15',
    '2025-14-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'brebes' for customer 'PT CIPTA NIAGA SEMESTA' not found (unit 2888)
-- Unit 2888: PT CIPTA NIAGA SEMESTA - brebes (Rp8.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8000000,
    customer_id = 22,
    kontrak_id = @kontrak_id
WHERE no_unit = 2888;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-15',
    '2025-14-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2888
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    22,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4502190183
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2026-01-01 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4502190183',
    'PO_ONLY',
    '2026-01-01',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'cirebon' for customer 'PT CIPTA NIAGA SEMESTA' not found (unit 1950)
-- Unit 1950: PT CIPTA NIAGA SEMESTA - cirebon (Rp8.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8000000,
    customer_id = 22,
    kontrak_id = @kontrak_id
WHERE no_unit = 1950;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-01-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1950
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    22,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4501963218
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-09-26 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4501963218',
    'PO_ONLY',
    '2025-09-26',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'grsik' for customer 'PT CIPTA NIAGA SEMESTA' not found (unit 5733)
-- Unit 5733: PT CIPTA NIAGA SEMESTA - grsik (Rp8.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8500000,
    customer_id = 22,
    kontrak_id = @kontrak_id
WHERE no_unit = 5733;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-09-26',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5733
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    22,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4501326057
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-09-26 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4501326057',
    'PO_ONLY',
    '2025-09-26',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'indramayu' for customer 'PT CIPTA NIAGA SEMESTA' not found (unit 1482)
-- Unit 1482: PT CIPTA NIAGA SEMESTA - indramayu (Rp8.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8000000,
    customer_id = 22,
    kontrak_id = @kontrak_id
WHERE no_unit = 1482;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-09-26',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1482
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    22,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4501619672
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-03-25 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4501619672',
    'PO_ONLY',
    '2025-03-25',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'jakutpus' for customer 'PT CIPTA NIAGA SEMESTA' not found (unit 3301)
-- Unit 3301: PT CIPTA NIAGA SEMESTA - jakutpus (Rp8.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8000000,
    customer_id = 22,
    kontrak_id = @kontrak_id
WHERE no_unit = 3301;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-03-25',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3301
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    22,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4501607261
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-12-20 to 2026-19-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4501607261',
    'PO_ONLY',
    '2025-12-20',
    '2026-19-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'kopo' for customer 'PT CIPTA NIAGA SEMESTA' not found (unit 1661)
-- Unit 1661: PT CIPTA NIAGA SEMESTA - kopo (Rp8.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8000000,
    customer_id = 22,
    kontrak_id = @kontrak_id
WHERE no_unit = 1661;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-20',
    '2026-19-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1661
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    22,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4501631334
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-01-20 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4501631334',
    'PO_ONLY',
    '2025-01-20',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Krian' for customer 'PT CIPTA NIAGA SEMESTA' not found (unit 3900)
-- Unit 3900: PT CIPTA NIAGA SEMESTA - Krian (Rp8.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8000000,
    customer_id = 22,
    kontrak_id = @kontrak_id
WHERE no_unit = 3900;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-20',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3900
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    22,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4501829496
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-07-04 to 2025-03-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4501829496',
    'PO_ONLY',
    '2025-07-04',
    '2025-03-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'majalengka' for customer 'PT CIPTA NIAGA SEMESTA' not found (unit 1955)
-- Unit 1955: PT CIPTA NIAGA SEMESTA - majalengka (Rp8.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8000000,
    customer_id = 22,
    kontrak_id = @kontrak_id
WHERE no_unit = 1955;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-07-04',
    '2025-03-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1955
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    22,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4501955707
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-08-26 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4501955707',
    'PO_ONLY',
    '2025-08-26',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'malang utara' for customer 'PT CIPTA NIAGA SEMESTA' not found (unit 3550)
-- Unit 3550: PT CIPTA NIAGA SEMESTA - malang utara (Rp8.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8000000,
    customer_id = 22,
    kontrak_id = @kontrak_id
WHERE no_unit = 3550;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-26',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3550
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    22,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 45013276464
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2024-09-24 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '45013276464',
    'PO_ONLY',
    '2024-09-24',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'padalarang' for customer 'PT CIPTA NIAGA SEMESTA' not found (unit 5160)
-- Unit 5160: PT CIPTA NIAGA SEMESTA - padalarang (Rp8.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8000000,
    customer_id = 22,
    kontrak_id = @kontrak_id
WHERE no_unit = 5160;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-09-24',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5160
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    22,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4501607265
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-02-16 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4501607265',
    'PO_ONLY',
    '2025-02-16',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'pekanbaru' for customer 'PT CIPTA NIAGA SEMESTA' not found (unit 2300)
-- Unit 2300: PT CIPTA NIAGA SEMESTA - pekanbaru (Rp10.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10500000,
    customer_id = 22,
    kontrak_id = @kontrak_id
WHERE no_unit = 2300;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-16',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2300
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    22,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4501631335
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-05-23 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4501631335',
    'PO_ONLY',
    '2025-05-23',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'pekayon' for customer 'PT CIPTA NIAGA SEMESTA' not found (unit 932)
-- Unit 932: PT CIPTA NIAGA SEMESTA - pekayon (Rp8.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8000000,
    customer_id = 22,
    kontrak_id = @kontrak_id
WHERE no_unit = 932;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-23',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 932
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    22,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4501827853
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-07-13 to 2025-12-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4501827853',
    'PO_ONLY',
    '2025-07-13',
    '2025-12-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'semarang' for customer 'PT CIPTA NIAGA SEMESTA' not found (unit 3032)
-- Unit 3032: PT CIPTA NIAGA SEMESTA - semarang (Rp8.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8000000,
    customer_id = 22,
    kontrak_id = @kontrak_id
WHERE no_unit = 3032;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-07-13',
    '2025-12-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3032
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    22,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4501607263
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-02-21 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4501607263',
    'PO_ONLY',
    '2025-02-21',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'soreang' for customer 'PT CIPTA NIAGA SEMESTA' not found (unit 2261)
-- Unit 2261: PT CIPTA NIAGA SEMESTA - soreang (Rp8.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8000000,
    customer_id = 22,
    kontrak_id = @kontrak_id
WHERE no_unit = 2261;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-21',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2261
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    22,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4502190202
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2026-02-17 to 2027-02-17
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4502190202',
    'PO_ONLY',
    '2026-02-17',
    '2027-02-17',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Surabaya - barut' for customer 'PT CIPTA NIAGA SEMESTA' not found (unit 2307)
-- Unit 2307: PT CIPTA NIAGA SEMESTA - Surabaya - barut (Rp8.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8500000,
    customer_id = 22,
    kontrak_id = @kontrak_id
WHERE no_unit = 2307;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2307
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    22,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4501803896
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-06-01 to 2025-01-05
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4501803896',
    'PO_ONLY',
    '2025-06-01',
    '2025-01-05',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'surabaya-barsel' for customer 'PT CIPTA NIAGA SEMESTA' not found (unit 3101)
-- Unit 3101: PT CIPTA NIAGA SEMESTA - surabaya-barsel (Rp8.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8000000,
    customer_id = 22,
    kontrak_id = @kontrak_id
WHERE no_unit = 3101;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-01',
    '2025-01-05',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3101
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    22,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4501803894
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-06-01 to 2025-01-05
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4501803894',
    'PO_ONLY',
    '2025-06-01',
    '2025-01-05',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'surabaya-pustim' for customer 'PT CIPTA NIAGA SEMESTA' not found (unit 3318)
-- Unit 3318: PT CIPTA NIAGA SEMESTA - surabaya-pustim (Rp8.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8000000,
    customer_id = 22,
    kontrak_id = @kontrak_id
WHERE no_unit = 3318;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-01',
    '2025-01-05',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3318
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    22,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4501906925
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-07-29 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4501906925',
    'PO_ONLY',
    '2025-07-29',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'tulungagung' for customer 'PT CIPTA NIAGA SEMESTA' not found (unit 2308)
-- Unit 2308: PT CIPTA NIAGA SEMESTA - tulungagung (Rp8.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8000000,
    customer_id = 22,
    kontrak_id = @kontrak_id
WHERE no_unit = 2308;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-07-29',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2308
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    22,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4501619671
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-01-15 to 2025-14-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4501619671',
    'PO_ONLY',
    '2025-01-15',
    '2025-14-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Madiun' for customer 'PT CIPTA NIAGA SEMESTA' not found (unit 3876)
-- Unit 3876: PT CIPTA NIAGA SEMESTA - Madiun (Rp8.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8000000,
    customer_id = 22,
    kontrak_id = @kontrak_id
WHERE no_unit = 3876;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-15',
    '2025-14-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3876
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    22,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: CS-200110828
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-03-19 to 2026-10-03
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'CS-200110828',
    'CONTRACT',
    '2025-03-19',
    '2026-10-03',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 2696: PT CS2 - Pandaan (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 139,
    customer_location_id = 358,
    kontrak_id = @kontrak_id
WHERE no_unit = 2696;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-03-19',
    '2026-10-03',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2696
LIMIT 1;

-- Unit 1979: PT CS2 - Pandaan (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 139,
    customer_location_id = 358,
    kontrak_id = @kontrak_id
WHERE no_unit = 1979;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-03-22',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1979
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    139,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: IDN10029193
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-11-06 to 2026-05-01
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'IDN10029193',
    'PO_ONLY',
    '2025-11-06',
    '2026-05-01',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Deltasilicon' for customer 'PT DHL Suply Chains' not found (unit 2721)
-- Unit 2721: PT DHL Suply Chains - Deltasilicon (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 30,
    kontrak_id = @kontrak_id
WHERE no_unit = 2721;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-06',
    '2026-05-01',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2721
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    30,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: IDN10025991
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-08-29 to 2025-28-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'IDN10025991',
    'PO_ONLY',
    '2025-08-29',
    '2025-28-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Cikarang' for customer 'PT DHL Suply Chains' not found (unit 3491)
-- Unit 3491: PT DHL Suply Chains - Cikarang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 30,
    kontrak_id = @kontrak_id
WHERE no_unit = 3491;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-29',
    '2025-28-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3491
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    30,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: IDN10029194
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-10-11 to 2025-10-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'IDN10029194',
    'PO_ONLY',
    '2025-10-11',
    '2025-10-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Deltasilicon' for customer 'PT DHL Suply Chains' not found (unit 3706)
-- Unit 3706: PT DHL Suply Chains - Deltasilicon (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 30,
    kontrak_id = @kontrak_id
WHERE no_unit = 3706;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-11',
    '2025-10-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3706
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    30,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: IDN10030658
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-12-05 to 2026-04-11
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'IDN10030658',
    'PO_ONLY',
    '2025-12-05',
    '2026-04-11',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5771: PT DHL Suply Chains - Cileungsi (Rp9.200.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9200000,
    customer_id = 30,
    customer_location_id = 98,
    kontrak_id = @kontrak_id
WHERE no_unit = 5771;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-05',
    '2026-04-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5771
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    30,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: IDN10029192
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-11-03 to 2026-05-01
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'IDN10029192',
    'PO_ONLY',
    '2025-11-03',
    '2026-05-01',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Deltasilicon' for customer 'PT DHL Suply Chains' not found (unit 3551)
-- Unit 3551: PT DHL Suply Chains - Deltasilicon (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 30,
    kontrak_id = @kontrak_id
WHERE no_unit = 3551;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-03',
    '2026-05-01',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3551
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    30,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: IDN10026785
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-09-03 to 2026-02-08
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'IDN10026785',
    'PO_ONLY',
    '2025-09-03',
    '2026-02-08',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Deltasilicon' for customer 'PT DHL Suply Chains' not found (unit 5808)
-- Unit 5808: PT DHL Suply Chains - Deltasilicon (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 30,
    kontrak_id = @kontrak_id
WHERE no_unit = 5808;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-09-03',
    '2026-02-08',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5808
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    30,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: IDN10026993
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-09-03 to 2026-02-02
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'IDN10026993',
    'PO_ONLY',
    '2025-09-03',
    '2026-02-02',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Deltasilicon' for customer 'PT DHL Suply Chains' not found (unit 5809)
-- Unit 5809: PT DHL Suply Chains - Deltasilicon (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 30,
    kontrak_id = @kontrak_id
WHERE no_unit = 5809;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-09-03',
    '2026-02-02',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5809
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    30,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: IDN10002658
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-08-03 to 2026-02-02
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'IDN10002658',
    'PO_ONLY',
    '2025-08-03',
    '2026-02-02',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5461: PT DHL Suply Chains - Karawang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 30,
    customer_location_id = 94,
    kontrak_id = @kontrak_id
WHERE no_unit = 5461;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-03',
    '2026-02-02',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5461
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    30,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: IDN10030620
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-12-02 to 2026-19-11
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'IDN10030620',
    'PO_ONLY',
    '2025-12-02',
    '2026-19-11',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5282: PT DHL Suply Chains - Osowilangun (Rp16.200.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 16200000,
    customer_id = 30,
    customer_location_id = 93,
    kontrak_id = @kontrak_id
WHERE no_unit = 5282;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-02',
    '2026-19-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5282
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    30,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: IDN10030619
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2026-01-05 to 2026-06-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'IDN10030619',
    'PO_ONLY',
    '2026-01-05',
    '2026-06-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5401: PT DHL Suply Chains - Osowilangun (Rp9.200.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9200000,
    customer_id = 30,
    customer_location_id = 93,
    kontrak_id = @kontrak_id
WHERE no_unit = 5401;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-01-05',
    '2026-06-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5401
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    30,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4800192250
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-07-01 to 2025-31-12
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4800192250',
    'PO_ONLY',
    '2025-07-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 1654: PT FLORA FOOD MANUFACTURING - Jababeka (Rp12.700.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12700000,
    customer_id = 202,
    customer_location_id = 452,
    kontrak_id = @kontrak_id
WHERE no_unit = 1654;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-07-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1654
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'PT FLORA FOOD MANUFACTURING' not found (unit 1621)
-- Unit 1621: PT FLORA FOOD MANUFACTURING -  (Rp12.700.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12700000,
    customer_id = 202,
    kontrak_id = @kontrak_id
WHERE no_unit = 1621;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-07-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1621
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    202,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 0741/GMP/11/24
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2024-12-04 to 2027-03-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '0741/GMP/11/24',
    'CONTRACT',
    '2024-12-04',
    '2027-03-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5462: PT GRIYA MANDIRI PERKASA - Tangerang (Rp29.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 29000000,
    customer_id = 208,
    customer_location_id = 458,
    kontrak_id = @kontrak_id
WHERE no_unit = 5462;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-12-04',
    '2027-03-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5462
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    208,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: DO.1-3700-1038-16 Add 15
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2024-06-01 to 
-- Units: 10
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'DO.1-3700-1038-16 Add 15',
    'CONTRACT',
    '2024-06-01',
    '',
    'ACTIVE',
    'BULANAN',
    10,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT Haier Electrical Appliaces Indonesia' not found (unit 1506)
-- Unit 1506: PT Haier Electrical Appliaces Indonesia -  (Rp6.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6000000,
    customer_id = 39,
    kontrak_id = @kontrak_id
WHERE no_unit = 1506;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1506
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'PT Haier Electrical Appliaces Indonesia' not found (unit 2669)
-- Unit 2669: PT Haier Electrical Appliaces Indonesia -  (Rp6.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6000000,
    customer_id = 39,
    kontrak_id = @kontrak_id
WHERE no_unit = 2669;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2669
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'PT Haier Electrical Appliaces Indonesia' not found (unit 1351)
-- Unit 1351: PT Haier Electrical Appliaces Indonesia -  (Rp6.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6000000,
    customer_id = 39,
    kontrak_id = @kontrak_id
WHERE no_unit = 1351;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1351
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'PT Haier Electrical Appliaces Indonesia' not found (unit 1740)
-- Unit 1740: PT Haier Electrical Appliaces Indonesia -  (Rp6.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6000000,
    customer_id = 39,
    kontrak_id = @kontrak_id
WHERE no_unit = 1740;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1740
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'PT Haier Electrical Appliaces Indonesia' not found (unit 1768)
-- Unit 1768: PT Haier Electrical Appliaces Indonesia -  (Rp6.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6000000,
    customer_id = 39,
    kontrak_id = @kontrak_id
WHERE no_unit = 1768;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1768
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'PT Haier Electrical Appliaces Indonesia' not found (unit 1809)
-- Unit 1809: PT Haier Electrical Appliaces Indonesia -  (Rp6.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6000000,
    customer_id = 39,
    kontrak_id = @kontrak_id
WHERE no_unit = 1809;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1809
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'PT Haier Electrical Appliaces Indonesia' not found (unit 1914)
-- Unit 1914: PT Haier Electrical Appliaces Indonesia -  (Rp6.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6000000,
    customer_id = 39,
    kontrak_id = @kontrak_id
WHERE no_unit = 1914;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1914
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'PT Haier Electrical Appliaces Indonesia' not found (unit 2117)
-- Unit 2117: PT Haier Electrical Appliaces Indonesia -  (Rp6.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6000000,
    customer_id = 39,
    kontrak_id = @kontrak_id
WHERE no_unit = 2117;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2117
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'PT Haier Electrical Appliaces Indonesia' not found (unit 2546)
-- Unit 2546: PT Haier Electrical Appliaces Indonesia -  (Rp6.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6000000,
    customer_id = 39,
    kontrak_id = @kontrak_id
WHERE no_unit = 2546;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2546
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'PT Haier Electrical Appliaces Indonesia' not found (unit 3038)
-- Unit 3038: PT Haier Electrical Appliaces Indonesia -  (Rp6.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6000000,
    customer_id = 39,
    kontrak_id = @kontrak_id
WHERE no_unit = 3038;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3038
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    39,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4500105264
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-08-19 to 2025-18-10
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4500105264',
    'PO_ONLY',
    '2025-08-19',
    '2025-18-10',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT Haier Electrical Appliaces Indonesia' not found (unit 2113)
-- Unit 2113: PT Haier Electrical Appliaces Indonesia -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 39,
    kontrak_id = @kontrak_id
WHERE no_unit = 2113;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-19',
    '2025-18-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2113
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'PT Haier Electrical Appliaces Indonesia' not found (unit 2763)
-- Unit 2763: PT Haier Electrical Appliaces Indonesia -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 39,
    kontrak_id = @kontrak_id
WHERE no_unit = 2763;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-19',
    '2025-18-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2763
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    39,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4500105433
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2026-02-17 to 2027-02-17
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4500105433',
    'PO_ONLY',
    '2026-02-17',
    '2027-02-17',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT Haier Electrical Appliaces Indonesia' not found (unit 2655)
-- Unit 2655: PT Haier Electrical Appliaces Indonesia -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 39,
    kontrak_id = @kontrak_id
WHERE no_unit = 2655;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2655
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    39,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: Agreement Heinz
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2023-07-01 to 
-- Units: 3
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'Agreement Heinz',
    'CONTRACT',
    '2023-07-01',
    '',
    'ACTIVE',
    'BULANAN',
    3,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Surabaya' for customer 'PT HEINZ ABC INDONESIA' not found (unit 3602)
-- Unit 3602: PT HEINZ ABC INDONESIA - Surabaya (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 41,
    kontrak_id = @kontrak_id
WHERE no_unit = 3602;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3602
LIMIT 1;

-- ⚠ WARNING: Location 'Surabaya' for customer 'PT HEINZ ABC INDONESIA' not found (unit 3603)
-- Unit 3603: PT HEINZ ABC INDONESIA - Surabaya (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 41,
    kontrak_id = @kontrak_id
WHERE no_unit = 3603;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3603
LIMIT 1;

-- ⚠ WARNING: Location 'Surabaya' for customer 'PT HEINZ ABC INDONESIA' not found (unit 3604)
-- Unit 3604: PT HEINZ ABC INDONESIA - Surabaya (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 41,
    kontrak_id = @kontrak_id
WHERE no_unit = 3604;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3604
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    41,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 594/SML/1/2023
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2023-01-01 to 2025-31-12
-- Units: 5
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '594/SML/1/2023',
    'CONTRACT',
    '2023-01-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    5,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3114: PT Ichikoh Indonesia - MM 2100 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 43,
    customer_location_id = 121,
    kontrak_id = @kontrak_id
WHERE no_unit = 3114;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3114
LIMIT 1;

-- Unit 3118: PT Ichikoh Indonesia - MM 2100 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 43,
    customer_location_id = 121,
    kontrak_id = @kontrak_id
WHERE no_unit = 3118;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3118
LIMIT 1;

-- Unit 3123: PT Ichikoh Indonesia - MM 2100 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 43,
    customer_location_id = 121,
    kontrak_id = @kontrak_id
WHERE no_unit = 3123;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3123
LIMIT 1;

-- Unit 3130: PT Ichikoh Indonesia - MM 2100 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 43,
    customer_location_id = 121,
    kontrak_id = @kontrak_id
WHERE no_unit = 3130;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3130
LIMIT 1;

-- Unit 1052: PT Ichikoh Indonesia - MM 2100 (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 43,
    customer_location_id = 121,
    kontrak_id = @kontrak_id
WHERE no_unit = 1052;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1052
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    43,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 0231/IMLI/04/25
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-05-22 to 
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '0231/IMLI/04/25',
    'CONTRACT',
    '2025-05-22',
    '',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3643: PT INDOBLOCK MITRA LESTARI INDONESIA - CIKANDE (Rp8.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8500000,
    customer_id = 198,
    customer_location_id = 444,
    kontrak_id = @kontrak_id
WHERE no_unit = 3643;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-22',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3643
LIMIT 1;

-- Unit 3671: PT INDOBLOCK MITRA LESTARI INDONESIA - CIKANDE (Rp8.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8500000,
    customer_id = 198,
    customer_location_id = 444,
    kontrak_id = @kontrak_id
WHERE no_unit = 3671;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-04-17',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3671
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    198,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 0748/IMLI/10/25
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-10-10 to 2028-09-10
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '0748/IMLI/10/25',
    'CONTRACT',
    '2025-10-10',
    '2028-09-10',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3780: PT INDOBLOCK MITRA LESTARI INDONESIA - CIKANDE (Rp8.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8500000,
    customer_id = 198,
    customer_location_id = 444,
    kontrak_id = @kontrak_id
WHERE no_unit = 3780;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-10',
    '2028-09-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3780
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    198,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 01050/IMLI/09/24
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2024-01-17 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '01050/IMLI/09/24',
    'CONTRACT',
    '2024-01-17',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3937: PT INDOBLOCK MITRA LESTARI INDONESIA - CIKANDE (Rp8.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8500000,
    customer_id = 198,
    customer_location_id = 444,
    kontrak_id = @kontrak_id
WHERE no_unit = 3937;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-01-17',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3937
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    198,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4506624446
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2024-11-14 to 2025-14-11
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4506624446',
    'PO_ONLY',
    '2024-11-14',
    '2025-14-11',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 837: PT Indofood - Cibitung (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 49,
    customer_location_id = 163,
    kontrak_id = @kontrak_id
WHERE no_unit = 837;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-11-14',
    '2025-14-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 837
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    49,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4506926481
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-08-05 to 2026-04-01
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4506926481',
    'PO_ONLY',
    '2025-08-05',
    '2026-04-01',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 2166: PT Indofood - Cibitung (Rp15.882.231)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 15882231,
    customer_id = 49,
    customer_location_id = 163,
    kontrak_id = @kontrak_id
WHERE no_unit = 2166;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-05',
    '2026-04-01',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2166
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    49,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4506977899
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-10-13 to 2026-12-02
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4506977899',
    'PO_ONLY',
    '2025-10-13',
    '2026-12-02',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Gd Trinitas Cibitung' for customer 'PT Indofood' not found (unit 3020)
-- Unit 3020: PT Indofood - Gd Trinitas Cibitung (Rp15.882.231)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 15882231,
    customer_id = 49,
    kontrak_id = @kontrak_id
WHERE no_unit = 3020;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-13',
    '2026-12-02',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3020
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    49,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4506977779
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-12-01 to 2026-01-04
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4506977779',
    'PO_ONLY',
    '2025-12-01',
    '2026-01-04',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Jababeka' for customer 'PT Indofood' not found (unit 3625)
-- Unit 3625: PT Indofood - Jababeka (Rp15.882.231)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 15882231,
    customer_id = 49,
    kontrak_id = @kontrak_id
WHERE no_unit = 3625;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2026-01-04',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3625
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    49,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4506838103
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2026-02-17 to 2027-02-17
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4506838103',
    'PO_ONLY',
    '2026-02-17',
    '2027-02-17',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3858: PT Indofood - Cibitung (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 49,
    customer_location_id = 163,
    kontrak_id = @kontrak_id
WHERE no_unit = 3858;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3858
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    49,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: AMD I - 141/LGL-039/PTIL/WHS-JKT/VII/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2023-07-01 to 
-- Units: 11
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'AMD I - 141/LGL-039/PTIL/WHS-JKT/VII/2025',
    'CONTRACT',
    '2023-07-01',
    '',
    'ACTIVE',
    'BULANAN',
    11,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 2554: PT INDOLAKTO - Ciracas (Rp30.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 30500000,
    customer_id = 51,
    customer_location_id = 168,
    kontrak_id = @kontrak_id
WHERE no_unit = 2554;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2554
LIMIT 1;

-- Unit 2574: PT INDOLAKTO - Ciracas (Rp30.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 30500000,
    customer_id = 51,
    customer_location_id = 168,
    kontrak_id = @kontrak_id
WHERE no_unit = 2574;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2574
LIMIT 1;

-- Unit 3212: PT INDOLAKTO - Ciracas (Rp11.900.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11900000,
    customer_id = 51,
    customer_location_id = 168,
    kontrak_id = @kontrak_id
WHERE no_unit = 3212;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3212
LIMIT 1;

-- Unit 3645: PT INDOLAKTO - Ciracas (Rp11.200.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11200000,
    customer_id = 51,
    customer_location_id = 168,
    kontrak_id = @kontrak_id
WHERE no_unit = 3645;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3645
LIMIT 1;

-- Unit 3646: PT INDOLAKTO - Ciracas (Rp10.700.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10700000,
    customer_id = 51,
    customer_location_id = 168,
    kontrak_id = @kontrak_id
WHERE no_unit = 3646;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3646
LIMIT 1;

-- Unit 2209: PT INDOLAKTO - Ciracas (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 51,
    customer_location_id = 168,
    kontrak_id = @kontrak_id
WHERE no_unit = 2209;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2209
LIMIT 1;

-- Unit 2227: PT INDOLAKTO - Ciracas (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 51,
    customer_location_id = 168,
    kontrak_id = @kontrak_id
WHERE no_unit = 2227;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2227
LIMIT 1;

-- Unit 3065: PT INDOLAKTO - Ciracas (Rp8.700.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8700000,
    customer_id = 51,
    customer_location_id = 168,
    kontrak_id = @kontrak_id
WHERE no_unit = 3065;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3065
LIMIT 1;

-- Unit 3077: PT INDOLAKTO - Ciracas (Rp8.600.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8600000,
    customer_id = 51,
    customer_location_id = 168,
    kontrak_id = @kontrak_id
WHERE no_unit = 3077;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3077
LIMIT 1;

-- Unit 3078: PT INDOLAKTO - Ciracas (Rp9.200.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9200000,
    customer_id = 51,
    customer_location_id = 168,
    kontrak_id = @kontrak_id
WHERE no_unit = 3078;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3078
LIMIT 1;

-- Unit 3213: PT INDOLAKTO - Ciracas (Rp11.900.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11900000,
    customer_id = 51,
    customer_location_id = 168,
    kontrak_id = @kontrak_id
WHERE no_unit = 3213;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3213
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    51,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: No. AMD 1 - 181/LGL-0396/PTIL/WHS-PAN/VIII/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-08-01 to 
-- Units: 5
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'No. AMD 1 - 181/LGL-0396/PTIL/WHS-PAN/VIII/2025',
    'CONTRACT',
    '2025-08-01',
    '',
    'ACTIVE',
    'BULANAN',
    5,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3081: PT INDOLAKTO - Pandaan (Rp6.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6950000,
    customer_id = 51,
    customer_location_id = 171,
    kontrak_id = @kontrak_id
WHERE no_unit = 3081;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3081
LIMIT 1;

-- Unit 3082: PT INDOLAKTO - Pandaan (Rp6.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6950000,
    customer_id = 51,
    customer_location_id = 171,
    kontrak_id = @kontrak_id
WHERE no_unit = 3082;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3082
LIMIT 1;

-- Unit 3074: PT INDOLAKTO - Pandaan (Rp6.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6950000,
    customer_id = 51,
    customer_location_id = 171,
    kontrak_id = @kontrak_id
WHERE no_unit = 3074;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3074
LIMIT 1;

-- Unit 3079: PT INDOLAKTO - Pandaan (Rp7.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7000000,
    customer_id = 51,
    customer_location_id = 171,
    kontrak_id = @kontrak_id
WHERE no_unit = 3079;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3079
LIMIT 1;

-- Unit 3080: PT INDOLAKTO - Pandaan (Rp7.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7000000,
    customer_id = 51,
    customer_location_id = 171,
    kontrak_id = @kontrak_id
WHERE no_unit = 3080;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3080
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    51,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: No. AMD 1 -157/LGL-0385/PTIL/WHS-PWS/VII/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-08-01 to 
-- Units: 14
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'No. AMD 1 -157/LGL-0385/PTIL/WHS-PWS/VII/2025',
    'CONTRACT',
    '2025-08-01',
    '',
    'ACTIVE',
    'BULANAN',
    14,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3083: PT INDOLAKTO - Purwosari (Rp8.900.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8900000,
    customer_id = 51,
    customer_location_id = 172,
    kontrak_id = @kontrak_id
WHERE no_unit = 3083;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3083
LIMIT 1;

-- Unit 3009: PT INDOLAKTO - Purwosari (Rp11.400.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11400000,
    customer_id = 51,
    customer_location_id = 172,
    kontrak_id = @kontrak_id
WHERE no_unit = 3009;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3009
LIMIT 1;

-- Unit 3061: PT INDOLAKTO - Purwosari (Rp11.400.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11400000,
    customer_id = 51,
    customer_location_id = 172,
    kontrak_id = @kontrak_id
WHERE no_unit = 3061;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3061
LIMIT 1;

-- Unit 3071: PT INDOLAKTO - Purwosari (Rp6.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6950000,
    customer_id = 51,
    customer_location_id = 172,
    kontrak_id = @kontrak_id
WHERE no_unit = 3071;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3071
LIMIT 1;

-- Unit 3072: PT INDOLAKTO - Purwosari (Rp6.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6950000,
    customer_id = 51,
    customer_location_id = 172,
    kontrak_id = @kontrak_id
WHERE no_unit = 3072;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3072
LIMIT 1;

-- Unit 3108: PT INDOLAKTO - Purwosari (Rp11.400.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11400000,
    customer_id = 51,
    customer_location_id = 172,
    kontrak_id = @kontrak_id
WHERE no_unit = 3108;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3108
LIMIT 1;

-- Unit 3113: PT INDOLAKTO - Purwosari (Rp10.900.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10900000,
    customer_id = 51,
    customer_location_id = 172,
    kontrak_id = @kontrak_id
WHERE no_unit = 3113;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3113
LIMIT 1;

-- Unit 3144: PT INDOLAKTO - Purwosari (Rp6.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6500000,
    customer_id = 51,
    customer_location_id = 172,
    kontrak_id = @kontrak_id
WHERE no_unit = 3144;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3144
LIMIT 1;

-- Unit 3145: PT INDOLAKTO - Purwosari (Rp6.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6500000,
    customer_id = 51,
    customer_location_id = 172,
    kontrak_id = @kontrak_id
WHERE no_unit = 3145;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3145
LIMIT 1;

-- Unit 3415: PT INDOLAKTO - Purwosari (Rp10.200.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10200000,
    customer_id = 51,
    customer_location_id = 172,
    kontrak_id = @kontrak_id
WHERE no_unit = 3415;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3415
LIMIT 1;

-- Unit 3416: PT INDOLAKTO - Purwosari (Rp10.200.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10200000,
    customer_id = 51,
    customer_location_id = 172,
    kontrak_id = @kontrak_id
WHERE no_unit = 3416;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3416
LIMIT 1;

-- Unit 3417: PT INDOLAKTO - Purwosari (Rp10.200.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10200000,
    customer_id = 51,
    customer_location_id = 172,
    kontrak_id = @kontrak_id
WHERE no_unit = 3417;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3417
LIMIT 1;

-- Unit 5016: PT INDOLAKTO - Purwosari (Rp26.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 26000000,
    customer_id = 51,
    customer_location_id = 172,
    kontrak_id = @kontrak_id
WHERE no_unit = 5016;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5016
LIMIT 1;

-- Unit 5017: PT INDOLAKTO - Purwosari (Rp26.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 26000000,
    customer_id = 51,
    customer_location_id = 172,
    kontrak_id = @kontrak_id
WHERE no_unit = 5017;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5017
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    51,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 035/SML/VI/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-03-31 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '035/SML/VI/2025',
    'CONTRACT',
    '2025-03-31',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT Indonesia Chemi-con' not found (unit 3631)
-- Unit 3631: PT Indonesia Chemi-con -  (Rp6.750.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6750000,
    customer_id = 52,
    kontrak_id = @kontrak_id
WHERE no_unit = 3631;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-03-31',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3631
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    52,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 099/SML/XII/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-12-03 to 2028-02-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '099/SML/XII/2025',
    'CONTRACT',
    '2025-12-03',
    '2028-02-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT Indonesia Steel Tube' not found (unit 5304)
-- Unit 5304: PT Indonesia Steel Tube -  (Rp14.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 14000000,
    customer_id = 203,
    kontrak_id = @kontrak_id
WHERE no_unit = 5304;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-03',
    '2028-02-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5304
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    203,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 049/SML/VII/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-08-01 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '049/SML/VII/2025',
    'CONTRACT',
    '2025-08-01',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT Indonesia Steel Tube' not found (unit 5735)
-- Unit 5735: PT Indonesia Steel Tube -  (Rp12.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12000000,
    customer_id = 203,
    kontrak_id = @kontrak_id
WHERE no_unit = 5735;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5735
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    203,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 025/SML-IDR/IV/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period:  to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '025/SML-IDR/IV/2025',
    'CONTRACT',
    '',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5603: PT INDORIS PRINTINGDO - CIKUPA (Rp9.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9500000,
    customer_id = 209,
    customer_location_id = 459,
    kontrak_id = @kontrak_id
WHERE no_unit = 5603;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5603
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    209,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 058/SML/VII/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-07-25 to 2028-06-07
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '058/SML/VII/2025',
    'CONTRACT',
    '2025-07-25',
    '2028-06-07',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT Java Seafood' not found (unit 5703)
-- Unit 5703: PT Java Seafood -  (Rp13.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 13000000,
    customer_id = 193,
    kontrak_id = @kontrak_id
WHERE no_unit = 5703;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-07-25',
    '2028-06-07',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5703
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    193,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 086/SML/X/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-01-02 to 2026-01-01
-- Units: 9
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '086/SML/X/2025',
    'CONTRACT',
    '2025-01-02',
    '2026-01-01',
    'ACTIVE',
    'BULANAN',
    9,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 2881: PT Kasmaji - Mojokerto (Rp9.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9800000,
    customer_id = 177,
    customer_location_id = 422,
    kontrak_id = @kontrak_id
WHERE no_unit = 2881;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-02',
    '2026-01-01',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2881
LIMIT 1;

-- Unit 3097: PT Kasmaji - Mojokerto (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 177,
    customer_location_id = 422,
    kontrak_id = @kontrak_id
WHERE no_unit = 3097;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-02',
    '2026-01-01',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3097
LIMIT 1;

-- Unit 3612: PT Kasmaji - Mojokerto (Rp9.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9800000,
    customer_id = 177,
    customer_location_id = 422,
    kontrak_id = @kontrak_id
WHERE no_unit = 3612;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-02',
    '2026-01-01',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3612
LIMIT 1;

-- Unit 1816: PT Kasmaji - Mojokerto (Rp9.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9800000,
    customer_id = 177,
    customer_location_id = 422,
    kontrak_id = @kontrak_id
WHERE no_unit = 1816;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-02',
    '2026-01-01',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1816
LIMIT 1;

-- Unit 2058: PT Kasmaji - Mojokerto (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 177,
    customer_location_id = 422,
    kontrak_id = @kontrak_id
WHERE no_unit = 2058;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-02',
    '2026-01-01',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2058
LIMIT 1;

-- Unit 2162: PT Kasmaji - Mojokerto (Rp9.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9800000,
    customer_id = 177,
    customer_location_id = 422,
    kontrak_id = @kontrak_id
WHERE no_unit = 2162;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-02',
    '2026-01-01',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2162
LIMIT 1;

-- Unit 2176: PT Kasmaji - Mojokerto (Rp9.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9800000,
    customer_id = 177,
    customer_location_id = 422,
    kontrak_id = @kontrak_id
WHERE no_unit = 2176;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-02',
    '2026-01-01',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2176
LIMIT 1;

-- Unit 3534: PT Kasmaji - Mojokerto (Rp9.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9800000,
    customer_id = 177,
    customer_location_id = 422,
    kontrak_id = @kontrak_id
WHERE no_unit = 3534;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-02',
    '2026-01-01',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3534
LIMIT 1;

-- Unit 3654: PT Kasmaji - Mojokerto (Rp9.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9800000,
    customer_id = 177,
    customer_location_id = 422,
    kontrak_id = @kontrak_id
WHERE no_unit = 3654;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-02',
    '2026-01-01',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3654
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    177,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 086/SML/X/2026
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-10-04 to 2026-03-10
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '086/SML/X/2026',
    'CONTRACT',
    '2025-10-04',
    '2026-03-10',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3098: PT Kasmaji - Mojokerto (Rp9.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9800000,
    customer_id = 177,
    customer_location_id = 422,
    kontrak_id = @kontrak_id
WHERE no_unit = 3098;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-04',
    '2026-03-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3098
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    177,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 1160006557
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2026-02-17 to 2027-02-17
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '1160006557',
    'PO_ONLY',
    '2026-02-17',
    '2027-02-17',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT KINO INDONESIA' not found (unit 5698)
-- Unit 5698: PT KINO INDONESIA -  (Rp7.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7000000,
    customer_id = 60,
    kontrak_id = @kontrak_id
WHERE no_unit = 5698;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5698
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    60,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 1160006558
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2026-02-17 to 2027-02-17
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '1160006558',
    'PO_ONLY',
    '2026-02-17',
    '2027-02-17',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT KINO INDONESIA' not found (unit 5701)
-- Unit 5701: PT KINO INDONESIA -  (Rp20.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 20000000,
    customer_id = 60,
    kontrak_id = @kontrak_id
WHERE no_unit = 5701;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5701
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    60,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4018011075
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-07-01 to 2025-31-12
-- Units: 3
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4018011075',
    'PO_ONLY',
    '2025-07-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    3,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT Kokoh Inti Arebama' not found (unit 2823)
-- Unit 2823: PT Kokoh Inti Arebama -  (Rp7.300.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7300000,
    customer_id = 201,
    kontrak_id = @kontrak_id
WHERE no_unit = 2823;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-07-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2823
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'PT Kokoh Inti Arebama' not found (unit 2825)
-- Unit 2825: PT Kokoh Inti Arebama -  (Rp7.300.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7300000,
    customer_id = 201,
    kontrak_id = @kontrak_id
WHERE no_unit = 2825;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-07-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2825
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'PT Kokoh Inti Arebama' not found (unit 3945)
-- Unit 3945: PT Kokoh Inti Arebama -  (Rp7.300.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7300000,
    customer_id = 201,
    kontrak_id = @kontrak_id
WHERE no_unit = 3945;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-07-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3945
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    201,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4500007162
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2024-12-01 to 2025-01-11
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4500007162',
    'PO_ONLY',
    '2024-12-01',
    '2025-01-11',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5463: PT Lami Packaging - Tangerang (Rp12.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12000000,
    customer_id = 64,
    customer_location_id = 191,
    kontrak_id = @kontrak_id
WHERE no_unit = 5463;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-12-01',
    '2025-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5463
LIMIT 1;

-- Unit 5464: PT Lami Packaging - Tangerang (Rp12.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12000000,
    customer_id = 64,
    customer_location_id = 191,
    kontrak_id = @kontrak_id
WHERE no_unit = 5464;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-12-01',
    '2025-01-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5464
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    64,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: ADD II Forklift Agreement
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-12-01 to 2027-30-11
-- Units: 39
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'ADD II Forklift Agreement',
    'CONTRACT',
    '2025-12-01',
    '2027-30-11',
    'ACTIVE',
    'BULANAN',
    39,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 2485: PT LG Electronics Indonesia - Tangerang (Rp9.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9800000,
    customer_id = 66,
    customer_location_id = 208,
    kontrak_id = @kontrak_id
WHERE no_unit = 2485;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2485
LIMIT 1;

-- Unit 2831: PT LG Electronics Indonesia - Tangerang (Rp7.300.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7300000,
    customer_id = 66,
    customer_location_id = 208,
    kontrak_id = @kontrak_id
WHERE no_unit = 2831;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2831
LIMIT 1;

-- Unit 3208: PT LG Electronics Indonesia - Tangerang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 66,
    customer_location_id = 208,
    kontrak_id = @kontrak_id
WHERE no_unit = 3208;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3208
LIMIT 1;

-- Unit 3371: PT LG Electronics Indonesia - Tangerang (Rp9.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9000000,
    customer_id = 66,
    customer_location_id = 208,
    kontrak_id = @kontrak_id
WHERE no_unit = 3371;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3371
LIMIT 1;

-- Unit 3877: PT LG Electronics Indonesia - Tangerang (Rp9.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9000000,
    customer_id = 66,
    customer_location_id = 208,
    kontrak_id = @kontrak_id
WHERE no_unit = 3877;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3877
LIMIT 1;

-- Unit 5110: PT LG Electronics Indonesia - Tangerang (Rp7.300.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7300000,
    customer_id = 66,
    customer_location_id = 208,
    kontrak_id = @kontrak_id
WHERE no_unit = 5110;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5110
LIMIT 1;

-- Unit 5111: PT LG Electronics Indonesia - Tangerang (Rp7.300.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7300000,
    customer_id = 66,
    customer_location_id = 208,
    kontrak_id = @kontrak_id
WHERE no_unit = 5111;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5111
LIMIT 1;

-- Unit 1208: PT LG Electronics Indonesia - Tangerang (Rp9.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9000000,
    customer_id = 66,
    customer_location_id = 208,
    kontrak_id = @kontrak_id
WHERE no_unit = 1208;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1208
LIMIT 1;

-- Unit 1268: PT LG Electronics Indonesia - Tangerang (Rp10.100.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10100000,
    customer_id = 66,
    customer_location_id = 208,
    kontrak_id = @kontrak_id
WHERE no_unit = 1268;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1268
LIMIT 1;

-- Unit 1358: PT LG Electronics Indonesia - Tangerang (Rp7.300.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7300000,
    customer_id = 66,
    customer_location_id = 208,
    kontrak_id = @kontrak_id
WHERE no_unit = 1358;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1358
LIMIT 1;

-- Unit 1359: PT LG Electronics Indonesia - Tangerang (Rp7.300.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7300000,
    customer_id = 66,
    customer_location_id = 208,
    kontrak_id = @kontrak_id
WHERE no_unit = 1359;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1359
LIMIT 1;

-- Unit 1360: PT LG Electronics Indonesia - Tangerang (Rp7.300.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7300000,
    customer_id = 66,
    customer_location_id = 208,
    kontrak_id = @kontrak_id
WHERE no_unit = 1360;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1360
LIMIT 1;

-- Unit 1361: PT LG Electronics Indonesia - Tangerang (Rp7.300.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7300000,
    customer_id = 66,
    customer_location_id = 208,
    kontrak_id = @kontrak_id
WHERE no_unit = 1361;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1361
LIMIT 1;

-- Unit 1410: PT LG Electronics Indonesia - Tangerang (Rp9.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9000000,
    customer_id = 66,
    customer_location_id = 208,
    kontrak_id = @kontrak_id
WHERE no_unit = 1410;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1410
LIMIT 1;

-- Unit 1420: PT LG Electronics Indonesia - Tangerang (Rp9.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9000000,
    customer_id = 66,
    customer_location_id = 208,
    kontrak_id = @kontrak_id
WHERE no_unit = 1420;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1420
LIMIT 1;

-- Unit 1421: PT LG Electronics Indonesia - Tangerang (Rp9.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9000000,
    customer_id = 66,
    customer_location_id = 208,
    kontrak_id = @kontrak_id
WHERE no_unit = 1421;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1421
LIMIT 1;

-- Unit 1466: PT LG Electronics Indonesia - Tangerang (Rp7.300.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7300000,
    customer_id = 66,
    customer_location_id = 208,
    kontrak_id = @kontrak_id
WHERE no_unit = 1466;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1466
LIMIT 1;

-- Unit 1467: PT LG Electronics Indonesia - Tangerang (Rp7.300.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7300000,
    customer_id = 66,
    customer_location_id = 208,
    kontrak_id = @kontrak_id
WHERE no_unit = 1467;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1467
LIMIT 1;

-- Unit 1468: PT LG Electronics Indonesia - Tangerang (Rp7.300.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7300000,
    customer_id = 66,
    customer_location_id = 208,
    kontrak_id = @kontrak_id
WHERE no_unit = 1468;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1468
LIMIT 1;

-- Unit 1469: PT LG Electronics Indonesia - Tangerang (Rp7.300.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7300000,
    customer_id = 66,
    customer_location_id = 208,
    kontrak_id = @kontrak_id
WHERE no_unit = 1469;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1469
LIMIT 1;

-- Unit 1470: PT LG Electronics Indonesia - Tangerang (Rp7.300.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7300000,
    customer_id = 66,
    customer_location_id = 208,
    kontrak_id = @kontrak_id
WHERE no_unit = 1470;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1470
LIMIT 1;

-- Unit 2070: PT LG Electronics Indonesia - Tangerang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 66,
    customer_location_id = 208,
    kontrak_id = @kontrak_id
WHERE no_unit = 2070;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2070
LIMIT 1;

-- Unit 2480: PT LG Electronics Indonesia - Tangerang (Rp9.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9800000,
    customer_id = 66,
    customer_location_id = 208,
    kontrak_id = @kontrak_id
WHERE no_unit = 2480;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2480
LIMIT 1;

-- Unit 2487: PT LG Electronics Indonesia - Tangerang (Rp10.600.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10600000,
    customer_id = 66,
    customer_location_id = 208,
    kontrak_id = @kontrak_id
WHERE no_unit = 2487;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2487
LIMIT 1;

-- Unit 2832: PT LG Electronics Indonesia - Tangerang (Rp7.300.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7300000,
    customer_id = 66,
    customer_location_id = 208,
    kontrak_id = @kontrak_id
WHERE no_unit = 2832;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2832
LIMIT 1;

-- Unit 3160: PT LG Electronics Indonesia - Tangerang (Rp7.300.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7300000,
    customer_id = 66,
    customer_location_id = 208,
    kontrak_id = @kontrak_id
WHERE no_unit = 3160;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3160
LIMIT 1;

-- Unit 3161: PT LG Electronics Indonesia - Tangerang (Rp7.300.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7300000,
    customer_id = 66,
    customer_location_id = 208,
    kontrak_id = @kontrak_id
WHERE no_unit = 3161;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3161
LIMIT 1;

-- Unit 3885: PT LG Electronics Indonesia - Tangerang (Rp9.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9800000,
    customer_id = 66,
    customer_location_id = 208,
    kontrak_id = @kontrak_id
WHERE no_unit = 3885;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3885
LIMIT 1;

-- Unit 3899: PT LG Electronics Indonesia - Tangerang (Rp11.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11800000,
    customer_id = 66,
    customer_location_id = 208,
    kontrak_id = @kontrak_id
WHERE no_unit = 3899;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3899
LIMIT 1;

-- Unit 3957: PT LG Electronics Indonesia - Tangerang (Rp7.300.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7300000,
    customer_id = 66,
    customer_location_id = 208,
    kontrak_id = @kontrak_id
WHERE no_unit = 3957;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3957
LIMIT 1;

-- Unit 3958: PT LG Electronics Indonesia - Tangerang (Rp7.300.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7300000,
    customer_id = 66,
    customer_location_id = 208,
    kontrak_id = @kontrak_id
WHERE no_unit = 3958;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3958
LIMIT 1;

-- Unit 5214: PT LG Electronics Indonesia - Tangerang (Rp9.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9800000,
    customer_id = 66,
    customer_location_id = 208,
    kontrak_id = @kontrak_id
WHERE no_unit = 5214;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5214
LIMIT 1;

-- Unit 5389: PT LG Electronics Indonesia - Tangerang (Rp9.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9000000,
    customer_id = 66,
    customer_location_id = 208,
    kontrak_id = @kontrak_id
WHERE no_unit = 5389;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5389
LIMIT 1;

-- Unit 5519: PT LG Electronics Indonesia - Tangerang (Rp9.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9000000,
    customer_id = 66,
    customer_location_id = 208,
    kontrak_id = @kontrak_id
WHERE no_unit = 5519;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5519
LIMIT 1;

-- Unit 5520: PT LG Electronics Indonesia - Tangerang (Rp11.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11800000,
    customer_id = 66,
    customer_location_id = 208,
    kontrak_id = @kontrak_id
WHERE no_unit = 5520;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5520
LIMIT 1;

-- Unit 5521: PT LG Electronics Indonesia - Tangerang (Rp11.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11800000,
    customer_id = 66,
    customer_location_id = 208,
    kontrak_id = @kontrak_id
WHERE no_unit = 5521;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5521
LIMIT 1;

-- Unit 5639: PT LG Electronics Indonesia - Tangerang (Rp8.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8500000,
    customer_id = 66,
    customer_location_id = 208,
    kontrak_id = @kontrak_id
WHERE no_unit = 5639;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5639
LIMIT 1;

-- Unit 5640: PT LG Electronics Indonesia - Tangerang (Rp9.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9000000,
    customer_id = 66,
    customer_location_id = 208,
    kontrak_id = @kontrak_id
WHERE no_unit = 5640;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5640
LIMIT 1;

-- Unit 5641: PT LG Electronics Indonesia - Tangerang (Rp8.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8500000,
    customer_id = 66,
    customer_location_id = 208,
    kontrak_id = @kontrak_id
WHERE no_unit = 5641;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2027-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5641
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    66,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 193/LGEIN/EESH/IX-21/2020
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2021-07-01 to 
-- Units: 34
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '193/LGEIN/EESH/IX-21/2020',
    'CONTRACT',
    '2021-07-01',
    '',
    'ACTIVE',
    'BULANAN',
    34,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Cibitung' for customer 'PT LG Electronics Indonesia' not found (unit 2793)
-- Unit 2793: PT LG Electronics Indonesia - Cibitung (Rp10.200.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10200000,
    customer_id = 66,
    kontrak_id = @kontrak_id
WHERE no_unit = 2793;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2021-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2793
LIMIT 1;

-- ⚠ WARNING: Location 'Cibitung' for customer 'PT LG Electronics Indonesia' not found (unit 2989)
-- Unit 2989: PT LG Electronics Indonesia - Cibitung (Rp6.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6000000,
    customer_id = 66,
    kontrak_id = @kontrak_id
WHERE no_unit = 2989;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2021-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2989
LIMIT 1;

-- ⚠ WARNING: Location 'Cibitung' for customer 'PT LG Electronics Indonesia' not found (unit 2992)
-- Unit 2992: PT LG Electronics Indonesia - Cibitung (Rp2.850.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 2850000,
    customer_id = 66,
    kontrak_id = @kontrak_id
WHERE no_unit = 2992;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2021-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2992
LIMIT 1;

-- ⚠ WARNING: Location 'Cibitung' for customer 'PT LG Electronics Indonesia' not found (unit 821)
-- Unit 821: PT LG Electronics Indonesia - Cibitung (Rp8.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8000000,
    customer_id = 66,
    kontrak_id = @kontrak_id
WHERE no_unit = 821;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2021-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 821
LIMIT 1;

-- ⚠ WARNING: Location 'Cibitung' for customer 'PT LG Electronics Indonesia' not found (unit 908)
-- Unit 908: PT LG Electronics Indonesia - Cibitung (Rp6.900.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6900000,
    customer_id = 66,
    kontrak_id = @kontrak_id
WHERE no_unit = 908;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2021-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 908
LIMIT 1;

-- ⚠ WARNING: Location 'Cibitung' for customer 'PT LG Electronics Indonesia' not found (unit 1288)
-- Unit 1288: PT LG Electronics Indonesia - Cibitung (Rp7.400.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7400000,
    customer_id = 66,
    kontrak_id = @kontrak_id
WHERE no_unit = 1288;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2021-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1288
LIMIT 1;

-- ⚠ WARNING: Location 'Cibitung' for customer 'PT LG Electronics Indonesia' not found (unit 1381)
-- Unit 1381: PT LG Electronics Indonesia - Cibitung (Rp7.400.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7400000,
    customer_id = 66,
    kontrak_id = @kontrak_id
WHERE no_unit = 1381;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2021-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1381
LIMIT 1;

-- ⚠ WARNING: Location 'Cibitung' for customer 'PT LG Electronics Indonesia' not found (unit 1951)
-- Unit 1951: PT LG Electronics Indonesia - Cibitung (Rp8.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8000000,
    customer_id = 66,
    kontrak_id = @kontrak_id
WHERE no_unit = 1951;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2021-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1951
LIMIT 1;

-- ⚠ WARNING: Location 'Cibitung' for customer 'PT LG Electronics Indonesia' not found (unit 2794)
-- Unit 2794: PT LG Electronics Indonesia - Cibitung (Rp10.200.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10200000,
    customer_id = 66,
    kontrak_id = @kontrak_id
WHERE no_unit = 2794;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2021-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2794
LIMIT 1;

-- ⚠ WARNING: Location 'Cibitung' for customer 'PT LG Electronics Indonesia' not found (unit 2795)
-- Unit 2795: PT LG Electronics Indonesia - Cibitung (Rp10.200.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10200000,
    customer_id = 66,
    kontrak_id = @kontrak_id
WHERE no_unit = 2795;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2021-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2795
LIMIT 1;

-- ⚠ WARNING: Location 'Cibitung' for customer 'PT LG Electronics Indonesia' not found (unit 2796)
-- Unit 2796: PT LG Electronics Indonesia - Cibitung (Rp10.200.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10200000,
    customer_id = 66,
    kontrak_id = @kontrak_id
WHERE no_unit = 2796;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2021-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2796
LIMIT 1;

-- ⚠ WARNING: Location 'Cibitung' for customer 'PT LG Electronics Indonesia' not found (unit 2797)
-- Unit 2797: PT LG Electronics Indonesia - Cibitung (Rp10.200.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10200000,
    customer_id = 66,
    kontrak_id = @kontrak_id
WHERE no_unit = 2797;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2021-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2797
LIMIT 1;

-- ⚠ WARNING: Location 'Cibitung' for customer 'PT LG Electronics Indonesia' not found (unit 2799)
-- Unit 2799: PT LG Electronics Indonesia - Cibitung (Rp10.200.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10200000,
    customer_id = 66,
    kontrak_id = @kontrak_id
WHERE no_unit = 2799;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2021-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2799
LIMIT 1;

-- ⚠ WARNING: Location 'Cibitung' for customer 'PT LG Electronics Indonesia' not found (unit 2800)
-- Unit 2800: PT LG Electronics Indonesia - Cibitung (Rp10.200.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10200000,
    customer_id = 66,
    kontrak_id = @kontrak_id
WHERE no_unit = 2800;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2021-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2800
LIMIT 1;

-- ⚠ WARNING: Location 'Cibitung' for customer 'PT LG Electronics Indonesia' not found (unit 2802)
-- Unit 2802: PT LG Electronics Indonesia - Cibitung (Rp10.200.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10200000,
    customer_id = 66,
    kontrak_id = @kontrak_id
WHERE no_unit = 2802;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2021-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2802
LIMIT 1;

-- ⚠ WARNING: Location 'Cibitung' for customer 'PT LG Electronics Indonesia' not found (unit 2803)
-- Unit 2803: PT LG Electronics Indonesia - Cibitung (Rp10.200.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10200000,
    customer_id = 66,
    kontrak_id = @kontrak_id
WHERE no_unit = 2803;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2021-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2803
LIMIT 1;

-- ⚠ WARNING: Location 'Cibitung' for customer 'PT LG Electronics Indonesia' not found (unit 2804)
-- Unit 2804: PT LG Electronics Indonesia - Cibitung (Rp10.200.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10200000,
    customer_id = 66,
    kontrak_id = @kontrak_id
WHERE no_unit = 2804;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2021-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2804
LIMIT 1;

-- ⚠ WARNING: Location 'Cibitung' for customer 'PT LG Electronics Indonesia' not found (unit 2806)
-- Unit 2806: PT LG Electronics Indonesia - Cibitung (Rp10.200.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10200000,
    customer_id = 66,
    kontrak_id = @kontrak_id
WHERE no_unit = 2806;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2021-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2806
LIMIT 1;

-- ⚠ WARNING: Location 'Cibitung' for customer 'PT LG Electronics Indonesia' not found (unit 2807)
-- Unit 2807: PT LG Electronics Indonesia - Cibitung (Rp10.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10500000,
    customer_id = 66,
    kontrak_id = @kontrak_id
WHERE no_unit = 2807;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2021-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2807
LIMIT 1;

-- ⚠ WARNING: Location 'Cibitung' for customer 'PT LG Electronics Indonesia' not found (unit 2808)
-- Unit 2808: PT LG Electronics Indonesia - Cibitung (Rp10.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10500000,
    customer_id = 66,
    kontrak_id = @kontrak_id
WHERE no_unit = 2808;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2021-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2808
LIMIT 1;

-- ⚠ WARNING: Location 'Cibitung' for customer 'PT LG Electronics Indonesia' not found (unit 2809)
-- Unit 2809: PT LG Electronics Indonesia - Cibitung (Rp7.400.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7400000,
    customer_id = 66,
    kontrak_id = @kontrak_id
WHERE no_unit = 2809;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2021-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2809
LIMIT 1;

-- ⚠ WARNING: Location 'Cibitung' for customer 'PT LG Electronics Indonesia' not found (unit 2810)
-- Unit 2810: PT LG Electronics Indonesia - Cibitung (Rp7.400.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7400000,
    customer_id = 66,
    kontrak_id = @kontrak_id
WHERE no_unit = 2810;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2021-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2810
LIMIT 1;

-- ⚠ WARNING: Location 'Cibitung' for customer 'PT LG Electronics Indonesia' not found (unit 2811)
-- Unit 2811: PT LG Electronics Indonesia - Cibitung (Rp7.400.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7400000,
    customer_id = 66,
    kontrak_id = @kontrak_id
WHERE no_unit = 2811;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2021-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2811
LIMIT 1;

-- ⚠ WARNING: Location 'Cibitung' for customer 'PT LG Electronics Indonesia' not found (unit 2812)
-- Unit 2812: PT LG Electronics Indonesia - Cibitung (Rp7.400.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7400000,
    customer_id = 66,
    kontrak_id = @kontrak_id
WHERE no_unit = 2812;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2021-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2812
LIMIT 1;

-- ⚠ WARNING: Location 'Cibitung' for customer 'PT LG Electronics Indonesia' not found (unit 2813)
-- Unit 2813: PT LG Electronics Indonesia - Cibitung (Rp7.400.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7400000,
    customer_id = 66,
    kontrak_id = @kontrak_id
WHERE no_unit = 2813;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2021-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2813
LIMIT 1;

-- ⚠ WARNING: Location 'Cibitung' for customer 'PT LG Electronics Indonesia' not found (unit 2814)
-- Unit 2814: PT LG Electronics Indonesia - Cibitung (Rp7.400.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7400000,
    customer_id = 66,
    kontrak_id = @kontrak_id
WHERE no_unit = 2814;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2021-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2814
LIMIT 1;

-- ⚠ WARNING: Location 'Cibitung' for customer 'PT LG Electronics Indonesia' not found (unit 2815)
-- Unit 2815: PT LG Electronics Indonesia - Cibitung (Rp7.400.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7400000,
    customer_id = 66,
    kontrak_id = @kontrak_id
WHERE no_unit = 2815;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2021-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2815
LIMIT 1;

-- ⚠ WARNING: Location 'Cibitung' for customer 'PT LG Electronics Indonesia' not found (unit 2816)
-- Unit 2816: PT LG Electronics Indonesia - Cibitung (Rp7.400.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7400000,
    customer_id = 66,
    kontrak_id = @kontrak_id
WHERE no_unit = 2816;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2021-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2816
LIMIT 1;

-- ⚠ WARNING: Location 'Cibitung' for customer 'PT LG Electronics Indonesia' not found (unit 2817)
-- Unit 2817: PT LG Electronics Indonesia - Cibitung (Rp8.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8000000,
    customer_id = 66,
    kontrak_id = @kontrak_id
WHERE no_unit = 2817;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2021-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2817
LIMIT 1;

-- ⚠ WARNING: Location 'Cibitung' for customer 'PT LG Electronics Indonesia' not found (unit 2818)
-- Unit 2818: PT LG Electronics Indonesia - Cibitung (Rp7.400.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7400000,
    customer_id = 66,
    kontrak_id = @kontrak_id
WHERE no_unit = 2818;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2021-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2818
LIMIT 1;

-- ⚠ WARNING: Location 'Cibitung' for customer 'PT LG Electronics Indonesia' not found (unit 2819)
-- Unit 2819: PT LG Electronics Indonesia - Cibitung (Rp7.400.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7400000,
    customer_id = 66,
    kontrak_id = @kontrak_id
WHERE no_unit = 2819;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2021-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2819
LIMIT 1;

-- ⚠ WARNING: Location 'Cibitung' for customer 'PT LG Electronics Indonesia' not found (unit 2822)
-- Unit 2822: PT LG Electronics Indonesia - Cibitung (Rp2.850.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 2850000,
    customer_id = 66,
    kontrak_id = @kontrak_id
WHERE no_unit = 2822;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2021-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2822
LIMIT 1;

-- ⚠ WARNING: Location 'Cibitung' for customer 'PT LG Electronics Indonesia' not found (unit 3041)
-- Unit 3041: PT LG Electronics Indonesia - Cibitung (Rp32.150.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 32150000,
    customer_id = 66,
    kontrak_id = @kontrak_id
WHERE no_unit = 3041;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2021-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3041
LIMIT 1;

-- ⚠ WARNING: Location 'Cibitung' for customer 'PT LG Electronics Indonesia' not found (unit 3042)
-- Unit 3042: PT LG Electronics Indonesia - Cibitung (Rp32.150.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 32150000,
    customer_id = 66,
    kontrak_id = @kontrak_id
WHERE no_unit = 3042;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2021-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3042
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    66,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 74/LGEIN/SHEE/V-06/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2026-02-17 to 2027-02-17
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '74/LGEIN/SHEE/V-06/2025',
    'CONTRACT',
    '2026-02-17',
    '2027-02-17',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Cibitung' for customer 'PT LG Electronics Indonesia' not found (unit 1202)
-- Unit 1202: PT LG Electronics Indonesia - Cibitung (Rp8.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8000000,
    customer_id = 66,
    kontrak_id = @kontrak_id
WHERE no_unit = 1202;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1202
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    66,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: Add No C2023025677
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2023-06-27 to 
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'Add No C2023025677',
    'CONTRACT',
    '2023-06-27',
    '',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Cibitung' for customer 'PT LG Electronics Indonesia R&D' not found (unit 3482)
-- Unit 3482: PT LG Electronics Indonesia R&D - Cibitung (Rp7.900.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7900000,
    customer_id = 66,
    kontrak_id = @kontrak_id
WHERE no_unit = 3482;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-06-27',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3482
LIMIT 1;

-- ⚠ WARNING: Location 'Cibitung' for customer 'PT LG Electronics Indonesia R&D' not found (unit 1671)
-- Unit 1671: PT LG Electronics Indonesia R&D - Cibitung (Rp8.100.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8100000,
    customer_id = 66,
    kontrak_id = @kontrak_id
WHERE no_unit = 1671;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-06-27',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1671
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    66,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4793735445
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2026-01-01 to 2026-31-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4793735445',
    'PO_ONLY',
    '2026-01-01',
    '2026-31-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 1736: PT M-I Production - Jababeka (Rp7.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7000000,
    customer_id = 210,
    customer_location_id = 460,
    kontrak_id = @kontrak_id
WHERE no_unit = 1736;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-01-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1736
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    210,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 007/X/SML/2023
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2026-02-17 to 2027-02-17
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '007/X/SML/2023',
    'CONTRACT',
    '2026-02-17',
    '2027-02-17',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT Misumi Indonesia' not found (unit 1601)
-- Unit 1601: PT Misumi Indonesia -  (Rp13.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 13500000,
    customer_id = 171,
    kontrak_id = @kontrak_id
WHERE no_unit = 1601;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1601
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    171,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 018/SML-R/III/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-03-02 to 2026-01-03
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '018/SML-R/III/2025',
    'CONTRACT',
    '2025-03-02',
    '2026-01-03',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3927: PT Okamoto Logistics Nusantara - Cakung (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 211,
    customer_location_id = 461,
    kontrak_id = @kontrak_id
WHERE no_unit = 3927;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-03-02',
    '2026-01-03',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3927
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    211,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: PO/PAP/23/0107
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2023-06-01 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'PO/PAP/23/0107',
    'CONTRACT',
    '2023-06-01',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3110: PT PAPCOR ASIA PASIFIC - BIC-CIKARANG (Rp9.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9800000,
    customer_id = 212,
    customer_location_id = 462,
    kontrak_id = @kontrak_id
WHERE no_unit = 3110;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3110
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    212,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4600078198
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-08-29 to 2025-28-11
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4600078198',
    'PO_ONLY',
    '2025-08-29',
    '2025-28-11',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT PAPERTECH INDONESIA' not found (unit 3103)
-- Unit 3103: PT PAPERTECH INDONESIA -  (Rp7.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7000000,
    customer_id = 77,
    kontrak_id = @kontrak_id
WHERE no_unit = 3103;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-29',
    '2025-28-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3103
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    77,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4600069789
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2024-07-01 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4600069789',
    'PO_ONLY',
    '2024-07-01',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT PAPERTECH INDONESIA' not found (unit 3466)
-- Unit 3466: PT PAPERTECH INDONESIA -  (Rp7.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7500000,
    customer_id = 77,
    kontrak_id = @kontrak_id
WHERE no_unit = 3466;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3466
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    77,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4600069792
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2024-07-01 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4600069792',
    'PO_ONLY',
    '2024-07-01',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT PAPERTECH INDONESIA' not found (unit 3468)
-- Unit 3468: PT PAPERTECH INDONESIA -  (Rp7.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7500000,
    customer_id = 77,
    kontrak_id = @kontrak_id
WHERE no_unit = 3468;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3468
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    77,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4600069797
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2024-07-01 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4600069797',
    'PO_ONLY',
    '2024-07-01',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT PAPERTECH INDONESIA' not found (unit 3497)
-- Unit 3497: PT PAPERTECH INDONESIA -  (Rp7.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7500000,
    customer_id = 77,
    kontrak_id = @kontrak_id
WHERE no_unit = 3497;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3497
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    77,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4600078213
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-09-06 to 2025-05-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4600078213',
    'PO_ONLY',
    '2025-09-06',
    '2025-05-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT PAPERTECH INDONESIA' not found (unit 2745)
-- Unit 2745: PT PAPERTECH INDONESIA -  (Rp7.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7000000,
    customer_id = 77,
    kontrak_id = @kontrak_id
WHERE no_unit = 2745;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-09-06',
    '2025-05-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2745
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    77,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4600071612
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2024-09-20 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4600071612',
    'PO_ONLY',
    '2024-09-20',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT PAPERTECH INDONESIA' not found (unit 3102)
-- Unit 3102: PT PAPERTECH INDONESIA -  (Rp10.300.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10300000,
    customer_id = 77,
    kontrak_id = @kontrak_id
WHERE no_unit = 3102;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-09-20',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3102
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    77,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4600078212
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-11-01 to 2025-31-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4600078212',
    'PO_ONLY',
    '2025-11-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT PAPERTECH INDONESIA' not found (unit 3185)
-- Unit 3185: PT PAPERTECH INDONESIA -  (Rp7.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7500000,
    customer_id = 77,
    kontrak_id = @kontrak_id
WHERE no_unit = 3185;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3185
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    77,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4600071950
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2024-11-01 to 2025-31-10
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4600071950',
    'PO_ONLY',
    '2024-11-01',
    '2025-31-10',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT PAPERTECH INDONESIA' not found (unit 3186)
-- Unit 3186: PT PAPERTECH INDONESIA -  (Rp7.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7000000,
    customer_id = 77,
    kontrak_id = @kontrak_id
WHERE no_unit = 3186;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-11-01',
    '2025-31-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3186
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    77,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4600069790
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2024-07-01 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4600069790',
    'PO_ONLY',
    '2024-07-01',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT PAPERTECH INDONESIA' not found (unit 3467)
-- Unit 3467: PT PAPERTECH INDONESIA -  (Rp7.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7500000,
    customer_id = 77,
    kontrak_id = @kontrak_id
WHERE no_unit = 3467;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3467
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    77,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 732-SML-VII-2024
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2024-07-01 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '732-SML-VII-2024',
    'CONTRACT',
    '2024-07-01',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT PAPERTECH INDONESIA' not found (unit 3540)
-- Unit 3540: PT PAPERTECH INDONESIA -  (Rp7.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7000000,
    customer_id = 77,
    kontrak_id = @kontrak_id
WHERE no_unit = 3540;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3540
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    77,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 723-SML-VII-2024
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2024-07-01 to 
-- Units: 5
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '723-SML-VII-2024',
    'CONTRACT',
    '2024-07-01',
    '',
    'ACTIVE',
    'BULANAN',
    5,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT PAPERTECH INDONESIA' not found (unit 3575)
-- Unit 3575: PT PAPERTECH INDONESIA -  (Rp8.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8000000,
    customer_id = 77,
    kontrak_id = @kontrak_id
WHERE no_unit = 3575;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3575
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'PT PAPERTECH INDONESIA' not found (unit 3599)
-- Unit 3599: PT PAPERTECH INDONESIA -  (Rp7.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7500000,
    customer_id = 77,
    kontrak_id = @kontrak_id
WHERE no_unit = 3599;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3599
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'PT PAPERTECH INDONESIA' not found (unit 3601)
-- Unit 3601: PT PAPERTECH INDONESIA -  (Rp7.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7500000,
    customer_id = 77,
    kontrak_id = @kontrak_id
WHERE no_unit = 3601;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3601
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'PT PAPERTECH INDONESIA' not found (unit 3614)
-- Unit 3614: PT PAPERTECH INDONESIA -  (Rp8.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8000000,
    customer_id = 77,
    kontrak_id = @kontrak_id
WHERE no_unit = 3614;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3614
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'PT PAPERTECH INDONESIA' not found (unit 3637)
-- Unit 3637: PT PAPERTECH INDONESIA -  (Rp7.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7500000,
    customer_id = 77,
    kontrak_id = @kontrak_id
WHERE no_unit = 3637;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3637
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    77,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4600075415
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-05-21 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4600075415',
    'PO_ONLY',
    '2025-05-21',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT PAPERTECH INDONESIA' not found (unit 3655)
-- Unit 3655: PT PAPERTECH INDONESIA -  (Rp7.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7500000,
    customer_id = 77,
    kontrak_id = @kontrak_id
WHERE no_unit = 3655;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-21',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3655
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    77,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4600074961
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-05-01 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4600074961',
    'PO_ONLY',
    '2025-05-01',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT PAPERTECH INDONESIA' not found (unit 5598)
-- Unit 5598: PT PAPERTECH INDONESIA -  (Rp7.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7500000,
    customer_id = 77,
    kontrak_id = @kontrak_id
WHERE no_unit = 5598;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5598
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    77,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4600079099
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-11-28 to 2028-27-11
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4600079099',
    'PO_ONLY',
    '2025-11-28',
    '2028-27-11',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT PAPERTECH INDONESIA' not found (unit 5896)
-- Unit 5896: PT PAPERTECH INDONESIA -  (Rp7.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7500000,
    customer_id = 77,
    kontrak_id = @kontrak_id
WHERE no_unit = 5896;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-28',
    '2028-27-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5896
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    77,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4600079101
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-11-28 to 2028-27-11
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4600079101',
    'PO_ONLY',
    '2025-11-28',
    '2028-27-11',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT PAPERTECH INDONESIA' not found (unit 5897)
-- Unit 5897: PT PAPERTECH INDONESIA -  (Rp7.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7500000,
    customer_id = 77,
    kontrak_id = @kontrak_id
WHERE no_unit = 5897;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-28',
    '2028-27-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5897
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    77,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4600074960
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-05-01 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4600074960',
    'PO_ONLY',
    '2025-05-01',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT PAPERTECH INDONESIA' not found (unit 5597)
-- Unit 5597: PT PAPERTECH INDONESIA -  (Rp7.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7500000,
    customer_id = 77,
    kontrak_id = @kontrak_id
WHERE no_unit = 5597;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5597
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    77,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4800001899
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2026-01-01 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4800001899',
    'PO_ONLY',
    '2026-01-01',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT Phoenix Resources International' not found (unit 5522)
-- Unit 5522: PT Phoenix Resources International -  (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 192,
    kontrak_id = @kontrak_id
WHERE no_unit = 5522;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-01-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5522
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    192,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: PII/2025/I/AGMT/HRGA/07
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-01-01 to 2025-31-12
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'PII/2025/I/AGMT/HRGA/07',
    'CONTRACT',
    '2025-01-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 1430: PT Piaggio Indonesia Industrial - Cikarang (Rp13.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 13000000,
    customer_id = 168,
    customer_location_id = 410,
    kontrak_id = @kontrak_id
WHERE no_unit = 1430;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1430
LIMIT 1;

-- Unit 3112: PT Piaggio Indonesia Industrial - Cikarang (Rp10.750.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10750000,
    customer_id = 168,
    customer_location_id = 410,
    kontrak_id = @kontrak_id
WHERE no_unit = 3112;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3112
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    168,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 714/SML/V/2024
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2023-04-01 to 2026-01-04
-- Units: 5
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '714/SML/V/2024',
    'CONTRACT',
    '2023-04-01',
    '2026-01-04',
    'ACTIVE',
    'BULANAN',
    5,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 2564: PT Prysmian Cables Indonesia - Cikampek (Rp21.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 21000000,
    customer_id = 80,
    customer_location_id = 230,
    kontrak_id = @kontrak_id
WHERE no_unit = 2564;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-04-01',
    '2026-01-04',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2564
LIMIT 1;

-- Unit 3179: PT Prysmian Cables Indonesia - Cikampek (Rp11.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11500000,
    customer_id = 80,
    customer_location_id = 230,
    kontrak_id = @kontrak_id
WHERE no_unit = 3179;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-04-01',
    '2026-01-04',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3179
LIMIT 1;

-- Unit 3181: PT Prysmian Cables Indonesia - Cikampek (Rp11.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11500000,
    customer_id = 80,
    customer_location_id = 230,
    kontrak_id = @kontrak_id
WHERE no_unit = 3181;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-04-01',
    '2026-01-04',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3181
LIMIT 1;

-- Unit 3229: PT Prysmian Cables Indonesia - Cikampek (Rp21.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 21000000,
    customer_id = 80,
    customer_location_id = 230,
    kontrak_id = @kontrak_id
WHERE no_unit = 3229;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-04-01',
    '2026-01-04',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3229
LIMIT 1;

-- Unit 3331: PT Prysmian Cables Indonesia - Cikampek (Rp11.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11500000,
    customer_id = 80,
    customer_location_id = 230,
    kontrak_id = @kontrak_id
WHERE no_unit = 3331;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-04-01',
    '2026-01-04',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3331
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    80,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 715/SML/V/2024
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2024-05-01 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '715/SML/V/2024',
    'CONTRACT',
    '2024-05-01',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5134: PT Prysmian Cables Indonesia - Cikampek (Rp57.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 57000000,
    customer_id = 80,
    customer_location_id = 230,
    kontrak_id = @kontrak_id
WHERE no_unit = 5134;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-05-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5134
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    80,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 742/SML/VIII/2024
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2024-08-26 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '742/SML/VIII/2024',
    'CONTRACT',
    '2024-08-26',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5308: PT Resik Bumi Perkasa - Cikarang (Rp10.600.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10600000,
    customer_id = 149,
    customer_location_id = 389,
    kontrak_id = @kontrak_id
WHERE no_unit = 5308;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-08-26',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5308
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    149,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 5000000516
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-07-01 to 2025-31-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '5000000516',
    'PO_ONLY',
    '2025-07-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3959: PT SAINT GOBAIN TRADING INDONESIA - Cikande (Rp16.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 16500000,
    customer_id = 213,
    customer_location_id = 463,
    kontrak_id = @kontrak_id
WHERE no_unit = 3959;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-07-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3959
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    213,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 102/SML/XII/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-12-15 to 2026-14-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '102/SML/XII/2025',
    'CONTRACT',
    '2025-12-15',
    '2026-14-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5306: PT Selaras Donlim Indonesia - Cileungsi (Rp9.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9000000,
    customer_id = 214,
    customer_location_id = 464,
    kontrak_id = @kontrak_id
WHERE no_unit = 5306;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-15',
    '2026-14-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5306
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    214,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 0174/SNI/04/25
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-04-15 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '0174/SNI/04/25',
    'CONTRACT',
    '2025-04-15',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3640: PT SUMBER NIAGA INDUSTRI - Tangerang (Rp8.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8500000,
    customer_id = 196,
    customer_location_id = 442,
    kontrak_id = @kontrak_id
WHERE no_unit = 3640;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-04-15',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3640
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    196,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 0742/GMP/08/24
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2024-11-01 to 2027-31-10
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '0742/GMP/08/24',
    'CONTRACT',
    '2024-11-01',
    '2027-31-10',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3668: PT SUMBER NIAGA INDUSTRI - Tangerang (Rp8.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8500000,
    customer_id = 196,
    customer_location_id = 442,
    kontrak_id = @kontrak_id
WHERE no_unit = 3668;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-11-01',
    '2027-31-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3668
LIMIT 1;

-- Unit 3778: PT SUMBER NIAGA INDUSTRI - Tangerang (Rp8.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8500000,
    customer_id = 196,
    customer_location_id = 442,
    kontrak_id = @kontrak_id
WHERE no_unit = 3778;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-11-01',
    '2027-31-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3778
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    196,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 0570/SNI/10/25
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-11-14 to 2028-13-11
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '0570/SNI/10/25',
    'CONTRACT',
    '2025-11-14',
    '2028-13-11',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3738: PT SUMBER NIAGA INDUSTRI - Tangerang (Rp7.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7500000,
    customer_id = 196,
    customer_location_id = 442,
    kontrak_id = @kontrak_id
WHERE no_unit = 3738;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-14',
    '2028-13-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3738
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    196,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 0741/GMP/08/24
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-02-07 to 2028-06-02
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '0741/GMP/08/24',
    'CONTRACT',
    '2025-02-07',
    '2028-06-02',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5557: PT SUMBER NIAGA INDUSTRI - Tangerang (Rp29.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 29000000,
    customer_id = 196,
    customer_location_id = 442,
    kontrak_id = @kontrak_id
WHERE no_unit = 5557;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-07',
    '2028-06-02',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5557
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    196,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: POTOM0013
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-12-08 to 
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'POTOM0013',
    'CONTRACT',
    '2025-12-08',
    '',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5352: PT Tech Onshore MEP Prefabricatior Indonesia - Jababeka (Rp11.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11500000,
    customer_id = 93,
    customer_location_id = 272,
    kontrak_id = @kontrak_id
WHERE no_unit = 5352;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-08',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5352
LIMIT 1;

-- Unit 5354: PT Tech Onshore MEP Prefabricatior Indonesia - Jababeka (Rp11.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11500000,
    customer_id = 93,
    customer_location_id = 272,
    kontrak_id = @kontrak_id
WHERE no_unit = 5354;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-08',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5354
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    93,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: POTOM0004
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-11-12 to 2026-11-02
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'POTOM0004',
    'CONTRACT',
    '2025-11-12',
    '2026-11-02',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3230: PT Tech Onshore MEP Prefabricatior Indonesia - Jababeka (Rp16.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 16000000,
    customer_id = 93,
    customer_location_id = 272,
    kontrak_id = @kontrak_id
WHERE no_unit = 3230;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-12',
    '2026-11-02',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3230
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    93,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: WF-100148208
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-01-01 to 2025-31-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'WF-100148208',
    'CONTRACT',
    '2025-01-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3105: PT Ultra Prima Abadi - Jakarta (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 199,
    customer_location_id = 445,
    kontrak_id = @kontrak_id
WHERE no_unit = 3105;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3105
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    199,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: DL-100151591
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-02-01 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'DL-100151591',
    'CONTRACT',
    '2025-02-01',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 1059: PT Ultra Prima Abadi - Jakarta (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 199,
    customer_location_id = 445,
    kontrak_id = @kontrak_id
WHERE no_unit = 1059;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1059
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    199,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: WF-100152251
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-01-27 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'WF-100152251',
    'CONTRACT',
    '2025-01-27',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 1807: PT Ultra Prima Abadi - Karawang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 199,
    customer_location_id = 446,
    kontrak_id = @kontrak_id
WHERE no_unit = 1807;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-27',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1807
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    199,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: PO/Bulan
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2026-02-17 to 2027-02-17
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'PO/Bulan',
    'CONTRACT',
    '2026-02-17',
    '2027-02-17',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 1843: PT Ultra Prima Abadi - Karawang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 199,
    customer_location_id = 446,
    kontrak_id = @kontrak_id
WHERE no_unit = 1843;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1843
LIMIT 1;

-- Unit 2395: PT Ultra Prima Abadi - Karawang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 199,
    customer_location_id = 446,
    kontrak_id = @kontrak_id
WHERE no_unit = 2395;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2395
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    199,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: WF-100159385
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-01-23 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'WF-100159385',
    'CONTRACT',
    '2025-01-23',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 1258: PT Ultra Prima Abadi - Kerawang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 199,
    customer_location_id = 447,
    kontrak_id = @kontrak_id
WHERE no_unit = 1258;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-23',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1258
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    199,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 426/SML/VII/2021
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2022-08-12 to 2023-08-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '426/SML/VII/2021',
    'CONTRACT',
    '2022-08-12',
    '2023-08-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3156: PT Ultra Prima Abadi - Surabaya (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 199,
    customer_location_id = 449,
    kontrak_id = @kontrak_id
WHERE no_unit = 3156;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-08-12',
    '2023-08-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3156
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    199,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: UPP-310025075
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-07-20 to 2025-19-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'UPP-310025075',
    'CONTRACT',
    '2025-07-20',
    '2025-19-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3881: PT Ultra Prima Plast - Klaten (Rp9.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9500000,
    customer_id = 215,
    customer_location_id = 465,
    kontrak_id = @kontrak_id
WHERE no_unit = 3881;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-07-20',
    '2025-19-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3881
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    215,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 23102015046
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2024-12-28 to 2025-27-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '23102015046',
    'PO_ONLY',
    '2024-12-28',
    '2025-27-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT YCH INDONESIA' not found (unit 5359)
-- Unit 5359: PT YCH INDONESIA -  (Rp6.250.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6250000,
    customer_id = 101,
    kontrak_id = @kontrak_id
WHERE no_unit = 5359;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-12-28',
    '2025-27-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5359
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    101,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 23102017202
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-08-27 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '23102017202',
    'PO_ONLY',
    '2025-08-27',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Cikande' for customer 'PT YCH INDONESIA' not found (unit 3889)
-- Unit 3889: PT YCH INDONESIA - Cikande (Rp12.250.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12250000,
    customer_id = 101,
    kontrak_id = @kontrak_id
WHERE no_unit = 3889;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-27',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3889
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    101,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 074/SML/IX/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-08-27 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '074/SML/IX/2025',
    'CONTRACT',
    '2025-08-27',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Cikande' for customer 'PT YCH INDONESIA' not found (unit 3895)
-- Unit 3895: PT YCH INDONESIA - Cikande (Rp12.250.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12250000,
    customer_id = 101,
    kontrak_id = @kontrak_id
WHERE no_unit = 3895;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-27',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3895
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    101,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 030/BIK/I/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-09-04 to 2026-03-03
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '030/BIK/I/2025',
    'CONTRACT',
    '2025-09-04',
    '2026-03-03',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Cikarang' for customer 'PT YCH INDONESIA' not found (unit 5796)
-- Unit 5796: PT YCH INDONESIA - Cikarang (Rp7.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7500000,
    customer_id = 101,
    kontrak_id = @kontrak_id
WHERE no_unit = 5796;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-09-04',
    '2026-03-03',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5796
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    101,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 23102015037
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-01-02 to 2026-01-01
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '23102015037',
    'PO_ONLY',
    '2025-01-02',
    '2026-01-01',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'MDC - Cikarang' for customer 'PT YCH INDONESIA' not found (unit 5005)
-- Unit 5005: PT YCH INDONESIA - MDC - Cikarang (Rp6.250.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6250000,
    customer_id = 101,
    kontrak_id = @kontrak_id
WHERE no_unit = 5005;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-02',
    '2026-01-01',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5005
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    101,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 23102014742
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2024-10-26 to 2025-25-10
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '23102014742',
    'PO_ONLY',
    '2024-10-26',
    '2025-25-10',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Pondok Ungu' for customer 'PT YCH INDONESIA' not found (unit 2570)
-- Unit 2570: PT YCH INDONESIA - Pondok Ungu (Rp14.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 14500000,
    customer_id = 101,
    kontrak_id = @kontrak_id
WHERE no_unit = 2570;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-10-26',
    '2025-25-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2570
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    101,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 23102015042
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2024-12-27 to 2025-26-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '23102015042',
    'PO_ONLY',
    '2024-12-27',
    '2025-26-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT YCH INDONESIA' not found (unit 5506)
-- Unit 5506: PT YCH INDONESIA -  (Rp6.250.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6250000,
    customer_id = 101,
    kontrak_id = @kontrak_id
WHERE no_unit = 5506;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-12-27',
    '2025-26-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5506
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    101,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 091/SML/X/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-12-01 to 2026-30-11
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '091/SML/X/2025',
    'CONTRACT',
    '2025-12-01',
    '2026-30-11',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Ramatex' for customer 'PT YCH INDONESIA' not found (unit 5573)
-- Unit 5573: PT YCH INDONESIA - Ramatex (Rp10.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10000000,
    customer_id = 101,
    kontrak_id = @kontrak_id
WHERE no_unit = 5573;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2026-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5573
LIMIT 1;

-- ⚠ WARNING: Location 'Ramatex' for customer 'PT YCH INDONESIA' not found (unit 5574)
-- Unit 5574: PT YCH INDONESIA - Ramatex (Rp10.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10000000,
    customer_id = 101,
    kontrak_id = @kontrak_id
WHERE no_unit = 5574;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2026-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5574
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    101,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 23102015049
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2024-12-30 to 2025-29-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '23102015049',
    'PO_ONLY',
    '2024-12-30',
    '2025-29-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT YCH INDONESIA' not found (unit 5515)
-- Unit 5515: PT YCH INDONESIA -  (Rp10.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10000000,
    customer_id = 101,
    kontrak_id = @kontrak_id
WHERE no_unit = 5515;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-12-30',
    '2025-29-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5515
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    101,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 23102015050
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2024-12-30 to 2025-29-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '23102015050',
    'PO_ONLY',
    '2024-12-30',
    '2025-29-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT YCH INDONESIA' not found (unit 5516)
-- Unit 5516: PT YCH INDONESIA -  (Rp10.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10000000,
    customer_id = 101,
    kontrak_id = @kontrak_id
WHERE no_unit = 5516;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-12-30',
    '2025-29-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5516
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    101,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 092/SML/X/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-11-04 to 2030-03-11
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '092/SML/X/2025',
    'CONTRACT',
    '2025-11-04',
    '2030-03-11',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Ramatex' for customer 'PT YCH INDONESIA' not found (unit 6016)
-- Unit 6016: PT YCH INDONESIA - Ramatex (Rp14.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 14000000,
    customer_id = 101,
    kontrak_id = @kontrak_id
WHERE no_unit = 6016;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-04',
    '2030-03-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 6016
LIMIT 1;

-- ⚠ WARNING: Location 'Ramatex' for customer 'PT YCH INDONESIA' not found (unit 6017)
-- Unit 6017: PT YCH INDONESIA - Ramatex (Rp14.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 14000000,
    customer_id = 101,
    kontrak_id = @kontrak_id
WHERE no_unit = 6017;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-04',
    '2030-03-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 6017
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    101,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 23102017855
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-11-12 to 2026-11-02
-- Units: 3
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '23102017855',
    'PO_ONLY',
    '2025-11-12',
    '2026-11-02',
    'ACTIVE',
    'BULANAN',
    3,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT YCH INDONESIA' not found (unit 3441)
-- Unit 3441: PT YCH INDONESIA -  (Rp6.250.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6250000,
    customer_id = 101,
    kontrak_id = @kontrak_id
WHERE no_unit = 3441;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-12',
    '2026-11-02',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3441
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'PT YCH INDONESIA' not found (unit 5008)
-- Unit 5008: PT YCH INDONESIA -  (Rp6.250.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6250000,
    customer_id = 101,
    kontrak_id = @kontrak_id
WHERE no_unit = 5008;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-12',
    '2026-11-02',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5008
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'PT YCH INDONESIA' not found (unit 5082)
-- Unit 5082: PT YCH INDONESIA -  (Rp6.250.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6250000,
    customer_id = 101,
    kontrak_id = @kontrak_id
WHERE no_unit = 5082;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-12',
    '2026-11-02',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5082
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    101,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 23102016769
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-09-10 to 2025-09-10
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '23102016769',
    'PO_ONLY',
    '2025-09-10',
    '2025-09-10',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT YCH INDONESIA' not found (unit 3890)
-- Unit 3890: PT YCH INDONESIA -  (Rp14.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 14500000,
    customer_id = 101,
    kontrak_id = @kontrak_id
WHERE no_unit = 3890;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-09-10',
    '2025-09-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3890
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    101,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 23102015047
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2024-12-27 to 2025-26-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '23102015047',
    'PO_ONLY',
    '2024-12-27',
    '2025-26-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT YCH INDONESIA' not found (unit 3987)
-- Unit 3987: PT YCH INDONESIA -  (Rp10.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10000000,
    customer_id = 101,
    kontrak_id = @kontrak_id
WHERE no_unit = 3987;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-12-27',
    '2025-26-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3987
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    101,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 23102015044
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2024-12-28 to 2025-27-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '23102015044',
    'PO_ONLY',
    '2024-12-28',
    '2025-27-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT YCH INDONESIA' not found (unit 5357)
-- Unit 5357: PT YCH INDONESIA -  (Rp6.250.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6250000,
    customer_id = 101,
    kontrak_id = @kontrak_id
WHERE no_unit = 5357;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-12-28',
    '2025-27-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5357
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    101,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 23102015045
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2024-12-28 to 2025-27-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '23102015045',
    'PO_ONLY',
    '2024-12-28',
    '2025-27-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT YCH INDONESIA' not found (unit 5358)
-- Unit 5358: PT YCH INDONESIA -  (Rp6.250.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6250000,
    customer_id = 101,
    kontrak_id = @kontrak_id
WHERE no_unit = 5358;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-12-28',
    '2025-27-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5358
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    101,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 23102014975
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2024-12-23 to 2025-22-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '23102014975',
    'PO_ONLY',
    '2024-12-23',
    '2025-22-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT YCH INDONESIA' not found (unit 5500)
-- Unit 5500: PT YCH INDONESIA -  (Rp6.250.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6250000,
    customer_id = 101,
    kontrak_id = @kontrak_id
WHERE no_unit = 5500;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-12-23',
    '2025-22-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5500
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    101,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 23102015039
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2024-12-27 to 2025-26-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '23102015039',
    'PO_ONLY',
    '2024-12-27',
    '2025-26-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT YCH INDONESIA' not found (unit 5503)
-- Unit 5503: PT YCH INDONESIA -  (Rp6.250.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6250000,
    customer_id = 101,
    kontrak_id = @kontrak_id
WHERE no_unit = 5503;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-12-27',
    '2025-26-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5503
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    101,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 23102015040
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2024-12-27 to 2025-26-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '23102015040',
    'PO_ONLY',
    '2024-12-27',
    '2025-26-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT YCH INDONESIA' not found (unit 5504)
-- Unit 5504: PT YCH INDONESIA -  (Rp6.250.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6250000,
    customer_id = 101,
    kontrak_id = @kontrak_id
WHERE no_unit = 5504;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-12-27',
    '2025-26-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5504
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    101,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 23102015041
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2024-12-27 to 2025-26-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '23102015041',
    'PO_ONLY',
    '2024-12-27',
    '2025-26-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT YCH INDONESIA' not found (unit 5505)
-- Unit 5505: PT YCH INDONESIA -  (Rp6.250.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6250000,
    customer_id = 101,
    kontrak_id = @kontrak_id
WHERE no_unit = 5505;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-12-27',
    '2025-26-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5505
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    101,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 23102015048
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2024-12-30 to 2025-29-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '23102015048',
    'PO_ONLY',
    '2024-12-30',
    '2025-29-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT YCH INDONESIA' not found (unit 5514)
-- Unit 5514: PT YCH INDONESIA -  (Rp10.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10000000,
    customer_id = 101,
    kontrak_id = @kontrak_id
WHERE no_unit = 5514;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-12-30',
    '2025-29-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5514
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    101,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 104/SML/XII/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-12-22 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '104/SML/XII/2025',
    'CONTRACT',
    '2025-12-22',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 1003: PT Zhi Xing Indonesia - Jatake (Rp15.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 15000000,
    customer_id = 156,
    customer_location_id = 397,
    kontrak_id = @kontrak_id
WHERE no_unit = 1003;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-22',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1003
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    156,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: CPL/012/AMG/295/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-11-29 to 2026-10-05
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'CPL/012/AMG/295/2025',
    'CONTRACT',
    '2025-11-29',
    '2026-10-05',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Cikampek' for customer 'PT. Asahimas Flat Glass Tbk' not found (unit 2098)
-- Unit 2098: PT. Asahimas Flat Glass Tbk - Cikampek (Rp100.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 100000000,
    customer_id = 5,
    kontrak_id = @kontrak_id
WHERE no_unit = 2098;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-29',
    '2026-10-05',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2098
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    5,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 148/SML/X/2019 Addendum I
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2026-02-17 to 2027-02-17
-- Units: 19
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '148/SML/X/2019 Addendum I',
    'CONTRACT',
    '2026-02-17',
    '2027-02-17',
    'ACTIVE',
    'BULANAN',
    19,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 2547: PT. Asahimas Flat Glass Tbk - Lok. Planning (Rp7.150.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7150000,
    customer_id = 5,
    customer_location_id = 53,
    kontrak_id = @kontrak_id
WHERE no_unit = 2547;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2547
LIMIT 1;

-- Unit 2612: PT. Asahimas Flat Glass Tbk - Lok. Depo Ancol (Rp7.150.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7150000,
    customer_id = 5,
    customer_location_id = 47,
    kontrak_id = @kontrak_id
WHERE no_unit = 2612;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2612
LIMIT 1;

-- Unit 2614: PT. Asahimas Flat Glass Tbk - Lok. Logistic (Rp7.150.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7150000,
    customer_id = 5,
    customer_location_id = 51,
    kontrak_id = @kontrak_id
WHERE no_unit = 2614;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2614
LIMIT 1;

-- Unit 2651: PT. Asahimas Flat Glass Tbk - Lok. Planning (Rp8.750.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8750000,
    customer_id = 5,
    customer_location_id = 53,
    kontrak_id = @kontrak_id
WHERE no_unit = 2651;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2651
LIMIT 1;

-- Unit 3057: PT. Asahimas Flat Glass Tbk - Lok. KIM - Junbiki Karawang (Rp11.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11000000,
    customer_id = 5,
    customer_location_id = 55,
    kontrak_id = @kontrak_id
WHERE no_unit = 3057;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-10-14',
    '2027-13-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3057
LIMIT 1;

-- Unit 3059: PT. Asahimas Flat Glass Tbk - Lok. Depo Sunter (Rp11.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11000000,
    customer_id = 5,
    customer_location_id = 54,
    kontrak_id = @kontrak_id
WHERE no_unit = 3059;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-10-14',
    '2027-13-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3059
LIMIT 1;

-- Unit 2613: PT. Asahimas Flat Glass Tbk - Lok, Prod C (Rp7.150.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7150000,
    customer_id = 5,
    customer_location_id = 50,
    kontrak_id = @kontrak_id
WHERE no_unit = 2613;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2613
LIMIT 1;

-- Unit 3147: PT. Asahimas Flat Glass Tbk - Lok. BIC - Purchase CKP (Rp12.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12000000,
    customer_id = 5,
    customer_location_id = 56,
    kontrak_id = @kontrak_id
WHERE no_unit = 3147;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-10-14',
    '2027-13-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3147
LIMIT 1;

-- Unit 3485: PT. Asahimas Flat Glass Tbk - Lok. Cikampek (Rp7.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7500000,
    customer_id = 5,
    customer_location_id = 52,
    kontrak_id = @kontrak_id
WHERE no_unit = 3485;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-10-14',
    '2027-13-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3485
LIMIT 1;

-- Unit 2611: PT. Asahimas Flat Glass Tbk - Lok. Depo Ancol (Rp7.150.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7150000,
    customer_id = 5,
    customer_location_id = 47,
    kontrak_id = @kontrak_id
WHERE no_unit = 2611;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2611
LIMIT 1;

-- Unit 2766: PT. Asahimas Flat Glass Tbk - Lok. Depo Ancol (Rp7.150.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7150000,
    customer_id = 5,
    customer_location_id = 47,
    kontrak_id = @kontrak_id
WHERE no_unit = 2766;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2766
LIMIT 1;

-- Unit 3010: PT. Asahimas Flat Glass Tbk - Lok. KIM - Junbiki Karawang (Rp11.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11000000,
    customer_id = 5,
    customer_location_id = 55,
    kontrak_id = @kontrak_id
WHERE no_unit = 3010;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-10-14',
    '2027-13-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3010
LIMIT 1;

-- Unit 2548: PT. Asahimas Flat Glass Tbk - Lok. Logistic (Rp7.150.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7150000,
    customer_id = 5,
    customer_location_id = 51,
    kontrak_id = @kontrak_id
WHERE no_unit = 2548;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2548
LIMIT 1;

-- Unit 2615: PT. Asahimas Flat Glass Tbk - Lok. Logistic (Rp7.150.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7150000,
    customer_id = 5,
    customer_location_id = 51,
    kontrak_id = @kontrak_id
WHERE no_unit = 2615;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2615
LIMIT 1;

-- Unit 2233: PT. Asahimas Flat Glass Tbk - Lok. Planning (Rp9.900.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9900000,
    customer_id = 5,
    customer_location_id = 53,
    kontrak_id = @kontrak_id
WHERE no_unit = 2233;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2233
LIMIT 1;

-- Unit 2550: PT. Asahimas Flat Glass Tbk - Lok. Prod A (Rp7.150.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7150000,
    customer_id = 5,
    customer_location_id = 48,
    kontrak_id = @kontrak_id
WHERE no_unit = 2550;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2550
LIMIT 1;

-- Unit 2827: PT. Asahimas Flat Glass Tbk - Lok. Prod A (Rp10.400.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10400000,
    customer_id = 5,
    customer_location_id = 48,
    kontrak_id = @kontrak_id
WHERE no_unit = 2827;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2827
LIMIT 1;

-- Unit 2262: PT. Asahimas Flat Glass Tbk - Lok. Prod B (Rp9.900.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9900000,
    customer_id = 5,
    customer_location_id = 49,
    kontrak_id = @kontrak_id
WHERE no_unit = 2262;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2262
LIMIT 1;

-- Unit 2549: PT. Asahimas Flat Glass Tbk - Lok. Prod B (Rp7.150.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7150000,
    customer_id = 5,
    customer_location_id = 49,
    kontrak_id = @kontrak_id
WHERE no_unit = 2549;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2549
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    5,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: TIDAK ADA PO
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2026-02-17 to 2027-02-17
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'TIDAK ADA PO',
    'CONTRACT',
    '2026-02-17',
    '2027-02-17',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'PT. LX Phantos Jakarta' not found (unit 2805)
-- Unit 2805: PT. LX Phantos Jakarta -  (Rp8.600.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8600000,
    customer_id = 221,
    kontrak_id = @kontrak_id
WHERE no_unit = 2805;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2805
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'PT. LX Phantos Jakarta' not found (unit 2805)
-- Unit 2805: PT. LX Phantos Jakarta -  (Rp3.250.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 3250000,
    customer_id = 221,
    kontrak_id = @kontrak_id
WHERE no_unit = 2805;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2805
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    221,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 026/MAPIN/ADD.4/GA/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-07-12 to 2026-11-07
-- Units: 19
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '026/MAPIN/ADD.4/GA/2025',
    'CONTRACT',
    '2025-07-12',
    '2026-11-07',
    'ACTIVE',
    'BULANAN',
    19,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 2593: PT. Musashi Auto Part - Karawang (Rp12.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12500000,
    customer_id = 70,
    customer_location_id = 215,
    kontrak_id = @kontrak_id
WHERE no_unit = 2593;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-07-12',
    '2026-11-07',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2593
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'PT. Musashi Auto Part' not found (unit 3484)
-- Unit 3484: PT. Musashi Auto Part -  (Rp8.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8500000,
    customer_id = 70,
    kontrak_id = @kontrak_id
WHERE no_unit = 3484;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-07-12',
    '2026-11-07',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3484
LIMIT 1;

-- ⚠ WARNING: Location 'EJIP-Cikarang' for customer 'PT. Musashi Auto Part' not found (unit 3555)
-- Unit 3555: PT. Musashi Auto Part - EJIP-Cikarang (Rp13.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 13500000,
    customer_id = 70,
    kontrak_id = @kontrak_id
WHERE no_unit = 3555;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-12-01',
    '2028-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3555
LIMIT 1;

-- Unit 3629: PT. Musashi Auto Part - Karawang (Rp13.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 13000000,
    customer_id = 70,
    customer_location_id = 215,
    kontrak_id = @kontrak_id
WHERE no_unit = 3629;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3629
LIMIT 1;

-- ⚠ WARNING: Location 'EJIP-Cikarang' for customer 'PT. Musashi Auto Part' not found (unit 3809)
-- Unit 3809: PT. Musashi Auto Part - EJIP-Cikarang (Rp12.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12000000,
    customer_id = 70,
    kontrak_id = @kontrak_id
WHERE no_unit = 3809;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-12-01',
    '2028-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3809
LIMIT 1;

-- Unit 3810: PT. Musashi Auto Part - Karawang (Rp12.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12000000,
    customer_id = 70,
    customer_location_id = 215,
    kontrak_id = @kontrak_id
WHERE no_unit = 3810;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-12-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3810
LIMIT 1;

-- ⚠ WARNING: Location 'EJIP-Cikarang' for customer 'PT. Musashi Auto Part' not found (unit 2369)
-- Unit 2369: PT. Musashi Auto Part - EJIP-Cikarang (Rp13.200.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 13200000,
    customer_id = 70,
    kontrak_id = @kontrak_id
WHERE no_unit = 2369;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-07-12',
    '2026-11-07',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2369
LIMIT 1;

-- ⚠ WARNING: Location 'EJIP-Cikarang' for customer 'PT. Musashi Auto Part' not found (unit 3484)
-- Unit 3484: PT. Musashi Auto Part - EJIP-Cikarang (Rp8.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8500000,
    customer_id = 70,
    kontrak_id = @kontrak_id
WHERE no_unit = 3484;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-07-12',
    '2026-11-07',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3484
LIMIT 1;

-- ⚠ WARNING: Location 'EJIP-Cikarang' for customer 'PT. Musashi Auto Part' not found (unit 3805)
-- Unit 3805: PT. Musashi Auto Part - EJIP-Cikarang (Rp12.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12000000,
    customer_id = 70,
    kontrak_id = @kontrak_id
WHERE no_unit = 3805;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-12-01',
    '2028-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3805
LIMIT 1;

-- ⚠ WARNING: Location 'EJIP-Cikarang' for customer 'PT. Musashi Auto Part' not found (unit 3806)
-- Unit 3806: PT. Musashi Auto Part - EJIP-Cikarang (Rp12.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12000000,
    customer_id = 70,
    kontrak_id = @kontrak_id
WHERE no_unit = 3806;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-12-01',
    '2028-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3806
LIMIT 1;

-- ⚠ WARNING: Location 'EJIP-Cikarang' for customer 'PT. Musashi Auto Part' not found (unit 3807)
-- Unit 3807: PT. Musashi Auto Part - EJIP-Cikarang (Rp12.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12000000,
    customer_id = 70,
    kontrak_id = @kontrak_id
WHERE no_unit = 3807;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-12-01',
    '2028-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3807
LIMIT 1;

-- ⚠ WARNING: Location 'EJIP-Cikarang' for customer 'PT. Musashi Auto Part' not found (unit 3808)
-- Unit 3808: PT. Musashi Auto Part - EJIP-Cikarang (Rp12.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12000000,
    customer_id = 70,
    kontrak_id = @kontrak_id
WHERE no_unit = 3808;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-12-01',
    '2028-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3808
LIMIT 1;

-- ⚠ WARNING: Location 'EJIP-Cikarang' for customer 'PT. Musashi Auto Part' not found (unit 5654)
-- Unit 5654: PT. Musashi Auto Part - EJIP-Cikarang (Rp14.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 14500000,
    customer_id = 70,
    kontrak_id = @kontrak_id
WHERE no_unit = 5654;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5654
LIMIT 1;

-- Unit 2438: PT. Musashi Auto Part - Karawang (Rp8.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8500000,
    customer_id = 70,
    customer_location_id = 215,
    kontrak_id = @kontrak_id
WHERE no_unit = 2438;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-07-12',
    '2026-11-07',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2438
LIMIT 1;

-- Unit 3621: PT. Musashi Auto Part - Karawang (Rp14.700.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 14700000,
    customer_id = 70,
    customer_location_id = 215,
    kontrak_id = @kontrak_id
WHERE no_unit = 3621;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-07-12',
    '2026-11-07',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3621
LIMIT 1;

-- Unit 3714: PT. Musashi Auto Part - Karawang (Rp13.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 13000000,
    customer_id = 70,
    customer_location_id = 215,
    kontrak_id = @kontrak_id
WHERE no_unit = 3714;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3714
LIMIT 1;

-- Unit 3901: PT. Musashi Auto Part - Karawang (Rp13.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 13000000,
    customer_id = 70,
    customer_location_id = 215,
    kontrak_id = @kontrak_id
WHERE no_unit = 3901;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-06-06',
    '2026-05-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3901
LIMIT 1;

-- Unit 3984: PT. Musashi Auto Part - Karawang (Rp13.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 13000000,
    customer_id = 70,
    customer_location_id = 215,
    kontrak_id = @kontrak_id
WHERE no_unit = 3984;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-06-06',
    '2026-05-06',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3984
LIMIT 1;

-- ⚠ WARNING: Location 'Karawang pindah ke ejip tgl 23.06.20' for customer 'PT. Musashi Auto Part' not found (unit 1215)
-- Unit 1215: PT. Musashi Auto Part - Karawang pindah ke ejip tgl 23.06.20 (Rp12.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12500000,
    customer_id = 70,
    kontrak_id = @kontrak_id
WHERE no_unit = 1215;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-07-12',
    '2026-11-07',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1215
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    70,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 9.0134/PO/SMI/IX/26
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-12-22 to 
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '9.0134/PO/SMI/IX/26',
    'CONTRACT',
    '2025-12-22',
    '',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5187: PT. Sankeikid - Karawang (Rp9.900.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9900000,
    customer_id = 128,
    customer_location_id = 339,
    kontrak_id = @kontrak_id
WHERE no_unit = 5187;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-22',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5187
LIMIT 1;

-- Unit 5345: PT. Sankeikid - Karawang (Rp9.900.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9900000,
    customer_id = 128,
    customer_location_id = 339,
    kontrak_id = @kontrak_id
WHERE no_unit = 5345;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-22',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5345
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    128,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 081/SML/X/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-02-01 to 
-- Units: 19
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '081/SML/X/2025',
    'CONTRACT',
    '2025-02-01',
    '',
    'ACTIVE',
    'BULANAN',
    19,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5508: PT. Sritrang Lingga Indonesia - Palembang (Rp12.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12950000,
    customer_id = 123,
    customer_location_id = 334,
    kontrak_id = @kontrak_id
WHERE no_unit = 5508;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5508
LIMIT 1;

-- Unit 5595: PT. Sritrang Lingga Indonesia - Palembang (Rp13.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 13950000,
    customer_id = 123,
    customer_location_id = 334,
    kontrak_id = @kontrak_id
WHERE no_unit = 5595;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-01',
    '2028-05-01',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5595
LIMIT 1;

-- Unit 5596: PT. Sritrang Lingga Indonesia - Palembang (Rp13.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 13950000,
    customer_id = 123,
    customer_location_id = 334,
    kontrak_id = @kontrak_id
WHERE no_unit = 5596;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-01',
    '2028-05-01',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5596
LIMIT 1;

-- Unit 3965: PT. Sritrang Lingga Indonesia - Palembang (Rp13.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 13950000,
    customer_id = 123,
    customer_location_id = 334,
    kontrak_id = @kontrak_id
WHERE no_unit = 3965;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3965
LIMIT 1;

-- Unit 5103: PT. Sritrang Lingga Indonesia - Palembang (Rp13.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 13950000,
    customer_id = 123,
    customer_location_id = 334,
    kontrak_id = @kontrak_id
WHERE no_unit = 5103;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5103
LIMIT 1;

-- Unit 5413: PT. Sritrang Lingga Indonesia - Palembang (Rp13.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 13950000,
    customer_id = 123,
    customer_location_id = 334,
    kontrak_id = @kontrak_id
WHERE no_unit = 5413;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5413
LIMIT 1;

-- Unit 5417: PT. Sritrang Lingga Indonesia - Palembang (Rp13.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 13950000,
    customer_id = 123,
    customer_location_id = 334,
    kontrak_id = @kontrak_id
WHERE no_unit = 5417;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5417
LIMIT 1;

-- Unit 5418: PT. Sritrang Lingga Indonesia - Palembang (Rp13.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 13950000,
    customer_id = 123,
    customer_location_id = 334,
    kontrak_id = @kontrak_id
WHERE no_unit = 5418;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-01',
    '2028-08-01',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5418
LIMIT 1;

-- Unit 5419: PT. Sritrang Lingga Indonesia - Palembang (Rp13.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 13950000,
    customer_id = 123,
    customer_location_id = 334,
    kontrak_id = @kontrak_id
WHERE no_unit = 5419;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5419
LIMIT 1;

-- Unit 5424: PT. Sritrang Lingga Indonesia - Palembang (Rp13.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 13950000,
    customer_id = 123,
    customer_location_id = 334,
    kontrak_id = @kontrak_id
WHERE no_unit = 5424;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5424
LIMIT 1;

-- Unit 5427: PT. Sritrang Lingga Indonesia - Palembang (Rp12.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12950000,
    customer_id = 123,
    customer_location_id = 334,
    kontrak_id = @kontrak_id
WHERE no_unit = 5427;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5427
LIMIT 1;

-- Unit 5507: PT. Sritrang Lingga Indonesia - Palembang (Rp12.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12950000,
    customer_id = 123,
    customer_location_id = 334,
    kontrak_id = @kontrak_id
WHERE no_unit = 5507;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5507
LIMIT 1;

-- Unit 5638: PT. Sritrang Lingga Indonesia - Palembang (Rp13.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 13950000,
    customer_id = 123,
    customer_location_id = 334,
    kontrak_id = @kontrak_id
WHERE no_unit = 5638;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-01',
    '2028-05-01',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5638
LIMIT 1;

-- Unit 5740: PT. Sritrang Lingga Indonesia - Palembang (Rp17.600.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 17600000,
    customer_id = 123,
    customer_location_id = 334,
    kontrak_id = @kontrak_id
WHERE no_unit = 5740;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-01',
    '2028-08-01',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5740
LIMIT 1;

-- Unit 5741: PT. Sritrang Lingga Indonesia - Palembang (Rp17.600.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 17600000,
    customer_id = 123,
    customer_location_id = 334,
    kontrak_id = @kontrak_id
WHERE no_unit = 5741;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-01',
    '2028-08-01',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5741
LIMIT 1;

-- Unit 5742: PT. Sritrang Lingga Indonesia - Palembang (Rp17.600.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 17600000,
    customer_id = 123,
    customer_location_id = 334,
    kontrak_id = @kontrak_id
WHERE no_unit = 5742;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-01',
    '2028-08-01',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5742
LIMIT 1;

-- Unit 5743: PT. Sritrang Lingga Indonesia - Palembang (Rp17.600.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 17600000,
    customer_id = 123,
    customer_location_id = 334,
    kontrak_id = @kontrak_id
WHERE no_unit = 5743;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-01',
    '2028-08-01',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5743
LIMIT 1;

-- Unit 5745: PT. Sritrang Lingga Indonesia - Palembang (Rp17.600.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 17600000,
    customer_id = 123,
    customer_location_id = 334,
    kontrak_id = @kontrak_id
WHERE no_unit = 5745;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-01',
    '2028-08-01',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5745
LIMIT 1;

-- Unit 5744: PT. Sritrang Lingga Indonesia - Palembang (Rp17.600.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 17600000,
    customer_id = 123,
    customer_location_id = 334,
    kontrak_id = @kontrak_id
WHERE no_unit = 5744;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-08-01',
    '2028-08-01',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5744
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    123,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 007/SML/II/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-03-17 to 
-- Units: 4
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '007/SML/II/2025',
    'CONTRACT',
    '2025-03-17',
    '',
    'ACTIVE',
    'BULANAN',
    4,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5284: PT. Tirta Sukses Perkasa - Pandaan (Rp11.750.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11750000,
    customer_id = 191,
    customer_location_id = 436,
    kontrak_id = @kontrak_id
WHERE no_unit = 5284;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-03-17',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5284
LIMIT 1;

-- Unit 5286: PT. Tirta Sukses Perkasa - Pandaan (Rp11.750.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11750000,
    customer_id = 191,
    customer_location_id = 436,
    kontrak_id = @kontrak_id
WHERE no_unit = 5286;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-03-17',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5286
LIMIT 1;

-- Unit 5293: PT. Tirta Sukses Perkasa - Pandaan (Rp11.750.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11750000,
    customer_id = 191,
    customer_location_id = 436,
    kontrak_id = @kontrak_id
WHERE no_unit = 5293;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-03-17',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5293
LIMIT 1;

-- Unit 5293: PT. Tirta Sukses Perkasa - Pandaan (Rp11.750.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11750000,
    customer_id = 191,
    customer_location_id = 436,
    kontrak_id = @kontrak_id
WHERE no_unit = 5293;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-03-17',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5293
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    191,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4507148105
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2026-02-03 to 2026-02-05
-- Units: 3
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4507148105',
    'PO_ONLY',
    '2026-02-03',
    '2026-02-05',
    'ACTIVE',
    'BULANAN',
    3,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5655: PT. Tirta Sukses Perkasa - Pandaan (Rp11.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11500000,
    customer_id = 191,
    customer_location_id = 436,
    kontrak_id = @kontrak_id
WHERE no_unit = 5655;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-03',
    '2026-02-05',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5655
LIMIT 1;

-- Unit 5656: PT. Tirta Sukses Perkasa - Pandaan (Rp11.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11500000,
    customer_id = 191,
    customer_location_id = 436,
    kontrak_id = @kontrak_id
WHERE no_unit = 5656;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-03',
    '2026-03-05',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5656
LIMIT 1;

-- Unit 5658: PT. Tirta Sukses Perkasa - Pandaan (Rp11.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11500000,
    customer_id = 191,
    customer_location_id = 436,
    kontrak_id = @kontrak_id
WHERE no_unit = 5658;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-03',
    '2026-04-05',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5658
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    191,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 747/SML/IX/2024
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2026-01-01 to 2026-31-12
-- Units: 3
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '747/SML/IX/2024',
    'CONTRACT',
    '2026-01-01',
    '2026-31-12',
    'ACTIVE',
    'BULANAN',
    3,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5309: PT. Tuffindo Nittoku Autoneum - Karawang (Rp11.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11000000,
    customer_id = 131,
    customer_location_id = 344,
    kontrak_id = @kontrak_id
WHERE no_unit = 5309;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-01-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5309
LIMIT 1;

-- Unit 5567: PT. Tuffindo Nittoku Autoneum - Karawang (Rp9.250.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9250000,
    customer_id = 131,
    customer_location_id = 344,
    kontrak_id = @kontrak_id
WHERE no_unit = 5567;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-01-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5567
LIMIT 1;

-- Unit 5568: PT. Tuffindo Nittoku Autoneum - Karawang (Rp9.250.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9250000,
    customer_id = 131,
    customer_location_id = 344,
    kontrak_id = @kontrak_id
WHERE no_unit = 5568;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-01-01',
    '2026-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5568
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    131,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: PO04­251200117
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2026-01-19 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'PO04­251200117',
    'CONTRACT',
    '2026-01-19',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 6072: PT. Zeus Kimiatama Indonesia - Jababeka (Rp7.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7500000,
    customer_id = 103,
    customer_location_id = 291,
    kontrak_id = @kontrak_id
WHERE no_unit = 6072;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-01-19',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 6072
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    103,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4610021742/21914,21906/PEP-SML
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-06-01 to 
-- Units: 3
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4610021742/21914,21906/PEP-SML',
    'CONTRACT',
    '2025-06-01',
    '',
    'ACTIVE',
    'BULANAN',
    3,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Subang' for customer 'Purinusa Eka Persada' not found (unit 3180)
-- Unit 3180: Purinusa Eka Persada - Subang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 81,
    kontrak_id = @kontrak_id
WHERE no_unit = 3180;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-06-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3180
LIMIT 1;

-- ⚠ WARNING: Location 'Subang' for customer 'Purinusa Eka Persada' not found (unit 3924)
-- Unit 3924: Purinusa Eka Persada - Subang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 81,
    kontrak_id = @kontrak_id
WHERE no_unit = 3924;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-11-01',
    '2027-30-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3924
LIMIT 1;

-- ⚠ WARNING: Location 'Subang' for customer 'Purinusa Eka Persada' not found (unit 5260)
-- Unit 5260: Purinusa Eka Persada - Subang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 81,
    kontrak_id = @kontrak_id
WHERE no_unit = 5260;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-11-01',
    '2027-30-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5260
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    81,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4610021743/21877/PEP-SML/X/2024
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-12-01 to 2028-12-01
-- Units: 3
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4610021743/21877/PEP-SML/X/2024',
    'CONTRACT',
    '2025-12-01',
    '2028-12-01',
    'ACTIVE',
    'BULANAN',
    3,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3930: Purinusa Eka Persada - Bandung (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 81,
    customer_location_id = 232,
    kontrak_id = @kontrak_id
WHERE no_unit = 3930;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2028-12-01',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3930
LIMIT 1;

-- Unit 3931: Purinusa Eka Persada - Bandung (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 81,
    customer_location_id = 232,
    kontrak_id = @kontrak_id
WHERE no_unit = 3931;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2028-12-01',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3931
LIMIT 1;

-- Unit 3934: Purinusa Eka Persada - Bandung (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 81,
    customer_location_id = 232,
    kontrak_id = @kontrak_id
WHERE no_unit = 3934;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2028-12-01',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3934
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    81,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: BDG-71113942
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-12-01 to 2028-12-01
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'BDG-71113942',
    'CONTRACT',
    '2025-12-01',
    '2028-12-01',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 6044: Purinusa Eka Persada - Bandung (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 81,
    customer_location_id = 232,
    kontrak_id = @kontrak_id
WHERE no_unit = 6044;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-01',
    '2028-12-01',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 6044
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    81,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4610021370/21532/SML/X/2024
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2026-02-17 to 2027-02-17
-- Units: 3
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4610021370/21532/SML/X/2024',
    'CONTRACT',
    '2026-02-17',
    '2027-02-17',
    'ACTIVE',
    'BULANAN',
    3,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Bawen' for customer 'Purinusa Eka Persada' not found (unit 2621)
-- Unit 2621: Purinusa Eka Persada - Bawen (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 81,
    kontrak_id = @kontrak_id
WHERE no_unit = 2621;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2621
LIMIT 1;

-- ⚠ WARNING: Location 'Bawen' for customer 'Purinusa Eka Persada' not found (unit 3854)
-- Unit 3854: Purinusa Eka Persada - Bawen (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 81,
    kontrak_id = @kontrak_id
WHERE no_unit = 3854;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-11-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3854
LIMIT 1;

-- ⚠ WARNING: Location 'Bawen' for customer 'Purinusa Eka Persada' not found (unit 3955)
-- Unit 3955: Purinusa Eka Persada - Bawen (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 81,
    kontrak_id = @kontrak_id
WHERE no_unit = 3955;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-11-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3955
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    81,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4610022263/22646/SDJ-SML/V/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-05-01 to 
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4610022263/22646/SDJ-SML/V/2025',
    'CONTRACT',
    '2025-05-01',
    '',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Sidoarjo' for customer 'Purinusa Eka Persada' not found (unit 3641)
-- Unit 3641: Purinusa Eka Persada - Sidoarjo (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 81,
    kontrak_id = @kontrak_id
WHERE no_unit = 3641;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3641
LIMIT 1;

-- ⚠ WARNING: Location 'Sidoarjo' for customer 'Purinusa Eka Persada' not found (unit 3860)
-- Unit 3860: Purinusa Eka Persada - Sidoarjo (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 81,
    kontrak_id = @kontrak_id
WHERE no_unit = 3860;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-05-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3860
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    81,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4300128490
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2026-01-29 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4300128490',
    'PO_ONLY',
    '2026-01-29',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'Rapid Plast' not found (unit 2694)
-- Unit 2694: Rapid Plast -  (Rp15.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 15000000,
    customer_id = 178,
    kontrak_id = @kontrak_id
WHERE no_unit = 2694;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-01-29',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2694
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    178,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: Service Agreement
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2023-01-22 to 
-- Units: 20
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'Service Agreement',
    'CONTRACT',
    '2023-01-22',
    '',
    'ACTIVE',
    'BULANAN',
    20,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3121: Reckit Benckiser Cileungsi - Cileungsi (Rp13.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 13500000,
    customer_id = 195,
    customer_location_id = 441,
    kontrak_id = @kontrak_id
WHERE no_unit = 3121;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-22',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3121
LIMIT 1;

-- Unit 3129: Reckit Benckiser Cileungsi - Cileungsi (Rp13.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 13500000,
    customer_id = 195,
    customer_location_id = 441,
    kontrak_id = @kontrak_id
WHERE no_unit = 3129;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-22',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3129
LIMIT 1;

-- Unit 3133: Reckit Benckiser Cileungsi - Cileungsi (Rp13.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 13500000,
    customer_id = 195,
    customer_location_id = 441,
    kontrak_id = @kontrak_id
WHERE no_unit = 3133;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-22',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3133
LIMIT 1;

-- Unit 3261: Reckit Benckiser Cileungsi - Cileungsi (Rp5.850.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 5850000,
    customer_id = 195,
    customer_location_id = 441,
    kontrak_id = @kontrak_id
WHERE no_unit = 3261;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-22',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3261
LIMIT 1;

-- Unit 3262: Reckit Benckiser Cileungsi - Cileungsi (Rp5.850.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 5850000,
    customer_id = 195,
    customer_location_id = 441,
    kontrak_id = @kontrak_id
WHERE no_unit = 3262;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-22',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3262
LIMIT 1;

-- Unit 3405: Reckit Benckiser Cileungsi - Cileungsi (Rp12.300.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12300000,
    customer_id = 195,
    customer_location_id = 441,
    kontrak_id = @kontrak_id
WHERE no_unit = 3405;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-22',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3405
LIMIT 1;

-- Unit 5341: Reckit Benckiser Cileungsi - Cileungsi (Rp27.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 27000000,
    customer_id = 195,
    customer_location_id = 441,
    kontrak_id = @kontrak_id
WHERE no_unit = 5341;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-22',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5341
LIMIT 1;

-- Unit 3115: Reckit Benckiser Cileungsi - Cileungsi (Rp13.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 13500000,
    customer_id = 195,
    customer_location_id = 441,
    kontrak_id = @kontrak_id
WHERE no_unit = 3115;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-22',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3115
LIMIT 1;

-- Unit 3116: Reckit Benckiser Cileungsi - Cileungsi (Rp13.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 13500000,
    customer_id = 195,
    customer_location_id = 441,
    kontrak_id = @kontrak_id
WHERE no_unit = 3116;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-22',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3116
LIMIT 1;

-- Unit 3255: Reckit Benckiser Cileungsi - Cileungsi (Rp5.850.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 5850000,
    customer_id = 195,
    customer_location_id = 441,
    kontrak_id = @kontrak_id
WHERE no_unit = 3255;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-22',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3255
LIMIT 1;

-- Unit 3256: Reckit Benckiser Cileungsi - Cileungsi (Rp5.850.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 5850000,
    customer_id = 195,
    customer_location_id = 441,
    kontrak_id = @kontrak_id
WHERE no_unit = 3256;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-22',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3256
LIMIT 1;

-- Unit 3257: Reckit Benckiser Cileungsi - Cileungsi (Rp5.850.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 5850000,
    customer_id = 195,
    customer_location_id = 441,
    kontrak_id = @kontrak_id
WHERE no_unit = 3257;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-22',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3257
LIMIT 1;

-- Unit 3258: Reckit Benckiser Cileungsi - Cileungsi (Rp5.850.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 5850000,
    customer_id = 195,
    customer_location_id = 441,
    kontrak_id = @kontrak_id
WHERE no_unit = 3258;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-22',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3258
LIMIT 1;

-- Unit 3259: Reckit Benckiser Cileungsi - Cileungsi (Rp5.850.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 5850000,
    customer_id = 195,
    customer_location_id = 441,
    kontrak_id = @kontrak_id
WHERE no_unit = 3259;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-22',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3259
LIMIT 1;

-- Unit 3260: Reckit Benckiser Cileungsi - Cileungsi (Rp5.850.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 5850000,
    customer_id = 195,
    customer_location_id = 441,
    kontrak_id = @kontrak_id
WHERE no_unit = 3260;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-22',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3260
LIMIT 1;

-- Unit 3263: Reckit Benckiser Cileungsi - Cileungsi (Rp5.850.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 5850000,
    customer_id = 195,
    customer_location_id = 441,
    kontrak_id = @kontrak_id
WHERE no_unit = 3263;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-22',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3263
LIMIT 1;

-- Unit 3390: Reckit Benckiser Cileungsi - Cileungsi (Rp12.300.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12300000,
    customer_id = 195,
    customer_location_id = 441,
    kontrak_id = @kontrak_id
WHERE no_unit = 3390;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-22',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3390
LIMIT 1;

-- Unit 3393: Reckit Benckiser Cileungsi - Cileungsi (Rp12.300.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12300000,
    customer_id = 195,
    customer_location_id = 441,
    kontrak_id = @kontrak_id
WHERE no_unit = 3393;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-22',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3393
LIMIT 1;

-- Unit 3404: Reckit Benckiser Cileungsi - Cileungsi (Rp12.300.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12300000,
    customer_id = 195,
    customer_location_id = 441,
    kontrak_id = @kontrak_id
WHERE no_unit = 3404;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-22',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3404
LIMIT 1;

-- Unit 3543: Reckit Benckiser Cileungsi - Cileungsi (Rp13.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 13800000,
    customer_id = 195,
    customer_location_id = 441,
    kontrak_id = @kontrak_id
WHERE no_unit = 3543;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-01-22',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3543
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    195,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 671/SML/XII/2023
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2024-05-01 to 
-- Units: 4
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '671/SML/XII/2023',
    'CONTRACT',
    '2024-05-01',
    '',
    'ACTIVE',
    'BULANAN',
    4,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'Sains Logistics' not found (unit 3706)
-- Unit 3706: Sains Logistics -  (Rp10.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10500000,
    customer_id = 200,
    kontrak_id = @kontrak_id
WHERE no_unit = 3706;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-05-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3706
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'Sains Logistics' not found (unit 3762)
-- Unit 3762: Sains Logistics -  (Rp10.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10500000,
    customer_id = 200,
    kontrak_id = @kontrak_id
WHERE no_unit = 3762;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-05-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3762
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'Sains Logistics' not found (unit 5201)
-- Unit 5201: Sains Logistics -  (Rp10.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10500000,
    customer_id = 200,
    kontrak_id = @kontrak_id
WHERE no_unit = 5201;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-05-05',
    '2026-04-05',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5201
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'Sains Logistics' not found (unit 5273)
-- Unit 5273: Sains Logistics -  (Rp10.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10500000,
    customer_id = 200,
    kontrak_id = @kontrak_id
WHERE no_unit = 5273;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-05-05',
    '2026-04-05',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5273
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    200,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 038/SML/VI/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-03-24 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '038/SML/VI/2025',
    'CONTRACT',
    '2025-03-24',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'Saitama Stamping Indonesia' not found (unit 2952)
-- Unit 2952: Saitama Stamping Indonesia -  (Rp10.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10000000,
    customer_id = 216,
    kontrak_id = @kontrak_id
WHERE no_unit = 2952;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-03-24',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2952
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    216,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 059/SENFU-SML/ADD/V/VII/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-09-01 to 2026-01-03
-- Units: 6
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '059/SENFU-SML/ADD/V/VII/2025',
    'CONTRACT',
    '2025-09-01',
    '2026-01-03',
    'ACTIVE',
    'BULANAN',
    6,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3299: Senopati Fuji Trans - DELTAMAS (Rp11.350.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11350000,
    customer_id = 121,
    customer_location_id = 328,
    kontrak_id = @kontrak_id
WHERE no_unit = 3299;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-09-01',
    '2026-01-03',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3299
LIMIT 1;

-- Unit 5664: Senopati Fuji Trans - DELTAMAS (Rp14.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 14500000,
    customer_id = 121,
    customer_location_id = 328,
    kontrak_id = @kontrak_id
WHERE no_unit = 5664;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-09-01',
    '2026-01-03',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5664
LIMIT 1;

-- Unit 2995: Senopati Fuji Trans - DELTAMAS (Rp11.350.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11350000,
    customer_id = 121,
    customer_location_id = 328,
    kontrak_id = @kontrak_id
WHERE no_unit = 2995;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-09-01',
    '2026-01-03',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2995
LIMIT 1;

-- Unit 2996: Senopati Fuji Trans - DELTAMAS (Rp11.350.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11350000,
    customer_id = 121,
    customer_location_id = 328,
    kontrak_id = @kontrak_id
WHERE no_unit = 2996;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-09-01',
    '2026-01-03',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2996
LIMIT 1;

-- Unit 2997: Senopati Fuji Trans - DELTAMAS (Rp11.350.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11350000,
    customer_id = 121,
    customer_location_id = 328,
    kontrak_id = @kontrak_id
WHERE no_unit = 2997;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-09-01',
    '2026-01-03',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2997
LIMIT 1;

-- Unit 2999: Senopati Fuji Trans - DELTAMAS (Rp15.850.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 15850000,
    customer_id = 121,
    customer_location_id = 328,
    kontrak_id = @kontrak_id
WHERE no_unit = 2999;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-09-01',
    '2026-01-03',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2999
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    121,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 080/SML/X/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-10-01 to 2025-31-12
-- Units: 5
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '080/SML/X/2025',
    'CONTRACT',
    '2025-10-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    5,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'CAINIAO Warehouse' for customer 'Senopati Fuji Trans' not found (unit 5642)
-- Unit 5642: Senopati Fuji Trans - CAINIAO Warehouse (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 121,
    kontrak_id = @kontrak_id
WHERE no_unit = 5642;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5642
LIMIT 1;

-- ⚠ WARNING: Location 'CAINIAO Warehouse' for customer 'Senopati Fuji Trans' not found (unit 5655)
-- Unit 5655: Senopati Fuji Trans - CAINIAO Warehouse (Rp8.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8500000,
    customer_id = 121,
    kontrak_id = @kontrak_id
WHERE no_unit = 5655;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5655
LIMIT 1;

-- ⚠ WARNING: Location 'CAINIAO Warehouse' for customer 'Senopati Fuji Trans' not found (unit 5656)
-- Unit 5656: Senopati Fuji Trans - CAINIAO Warehouse (Rp8.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8500000,
    customer_id = 121,
    kontrak_id = @kontrak_id
WHERE no_unit = 5656;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5656
LIMIT 1;

-- ⚠ WARNING: Location 'CAINIAO Warehouse' for customer 'Senopati Fuji Trans' not found (unit 5658)
-- Unit 5658: Senopati Fuji Trans - CAINIAO Warehouse (Rp8.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8500000,
    customer_id = 121,
    kontrak_id = @kontrak_id
WHERE no_unit = 5658;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5658
LIMIT 1;

-- ⚠ WARNING: Location 'CAINIAO Warehouse' for customer 'Senopati Fuji Trans' not found (unit 5659)
-- Unit 5659: Senopati Fuji Trans - CAINIAO Warehouse (Rp8.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8500000,
    customer_id = 121,
    kontrak_id = @kontrak_id
WHERE no_unit = 5659;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5659
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    121,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: PO00043925
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2026-02-17 to 2027-02-17
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'PO00043925',
    'CONTRACT',
    '2026-02-17',
    '2027-02-17',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3141: Sika Indonesia - Cileungsi (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 85,
    customer_location_id = 237,
    kontrak_id = @kontrak_id
WHERE no_unit = 3141;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3141
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    85,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: PO00060404
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2026-02-17 to 2027-02-17
-- Units: 7
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'PO00060404',
    'CONTRACT',
    '2026-02-17',
    '2027-02-17',
    'ACTIVE',
    'BULANAN',
    7,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3215: Sika Indonesia - Cileungsi (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 85,
    customer_location_id = 237,
    kontrak_id = @kontrak_id
WHERE no_unit = 3215;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3215
LIMIT 1;

-- Unit 3216: Sika Indonesia - Cileungsi (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 85,
    customer_location_id = 237,
    kontrak_id = @kontrak_id
WHERE no_unit = 3216;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3216
LIMIT 1;

-- Unit 3218: Sika Indonesia - Cileungsi (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 85,
    customer_location_id = 237,
    kontrak_id = @kontrak_id
WHERE no_unit = 3218;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3218
LIMIT 1;

-- Unit 3140: Sika Indonesia - Cileungsi (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 85,
    customer_location_id = 237,
    kontrak_id = @kontrak_id
WHERE no_unit = 3140;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3140
LIMIT 1;

-- Unit 3219: Sika Indonesia - Cileungsi (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 85,
    customer_location_id = 237,
    kontrak_id = @kontrak_id
WHERE no_unit = 3219;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3219
LIMIT 1;

-- Unit 5137: Sika Indonesia - Cileungsi (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 85,
    customer_location_id = 237,
    kontrak_id = @kontrak_id
WHERE no_unit = 5137;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5137
LIMIT 1;

-- Unit 5141: Sika Indonesia - Cileungsi (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 85,
    customer_location_id = 237,
    kontrak_id = @kontrak_id
WHERE no_unit = 5141;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5141
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    85,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: PO00045988
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2026-02-17 to 2027-02-17
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'PO00045988',
    'CONTRACT',
    '2026-02-17',
    '2027-02-17',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 2904: Sika Indonesia - Gresik (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 85,
    customer_location_id = 240,
    kontrak_id = @kontrak_id
WHERE no_unit = 2904;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2904
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    85,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: PO00046180
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2026-02-17 to 2027-02-17
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'PO00046180',
    'CONTRACT',
    '2026-02-17',
    '2027-02-17',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3375: Sika Indonesia - Gresik (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 85,
    customer_location_id = 240,
    kontrak_id = @kontrak_id
WHERE no_unit = 3375;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3375
LIMIT 1;

-- Unit 3906: Sika Indonesia - Gresik (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 85,
    customer_location_id = 240,
    kontrak_id = @kontrak_id
WHERE no_unit = 3906;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3906
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    85,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: PO00045192
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2026-02-17 to 2027-02-17
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'PO00045192',
    'CONTRACT',
    '2026-02-17',
    '2027-02-17',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3162: Sika Indonesia - Cileungsi (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 85,
    customer_location_id = 237,
    kontrak_id = @kontrak_id
WHERE no_unit = 3162;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3162
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    85,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: PO00055394
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2026-02-17 to 2027-02-17
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'PO00055394',
    'CONTRACT',
    '2026-02-17',
    '2027-02-17',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3561: Sika Indonesia - Jababeka (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 85,
    customer_location_id = 241,
    kontrak_id = @kontrak_id
WHERE no_unit = 3561;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3561
LIMIT 1;

-- Unit 3563: Sika Indonesia - Jababeka (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 85,
    customer_location_id = 241,
    kontrak_id = @kontrak_id
WHERE no_unit = 3563;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3563
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    85,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: PO00048274
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2026-02-17 to 2027-02-17
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'PO00048274',
    'CONTRACT',
    '2026-02-17',
    '2027-02-17',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3902: Sika Indonesia - Cileungsi (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 85,
    customer_location_id = 237,
    kontrak_id = @kontrak_id
WHERE no_unit = 3902;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3902
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    85,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: PO00060397
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2026-02-17 to 2027-02-17
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'PO00060397',
    'CONTRACT',
    '2026-02-17',
    '2027-02-17',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5139: Sika Indonesia - Cileungsi (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 85,
    customer_location_id = 237,
    kontrak_id = @kontrak_id
WHERE no_unit = 5139;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5139
LIMIT 1;

-- Unit 5140: Sika Indonesia - Cileungsi (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 85,
    customer_location_id = 237,
    kontrak_id = @kontrak_id
WHERE no_unit = 5140;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5140
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    85,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: PO00046232
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2026-02-17 to 2027-02-17
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'PO00046232',
    'CONTRACT',
    '2026-02-17',
    '2027-02-17',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 6070: Sika Indonesia - Cibitung (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 85,
    customer_location_id = 239,
    kontrak_id = @kontrak_id
WHERE no_unit = 6070;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 6070
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    85,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: PO. 1789/VI/SML/25/P2
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-07-12 to 2025-11-12
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'PO. 1789/VI/SML/25/P2',
    'CONTRACT',
    '2025-07-12',
    '2025-11-12',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'SNF Polymers' not found (unit 2609)
-- Unit 2609: SNF Polymers -  (Rp16.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 16500000,
    customer_id = 194,
    kontrak_id = @kontrak_id
WHERE no_unit = 2609;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-07-12',
    '2025-11-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2609
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    194,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 44966961
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-01-01 to 2025-31-12
-- Units: 42
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '44966961',
    'PO_ONLY',
    '2025-01-01',
    '2025-31-12',
    'ACTIVE',
    'BULANAN',
    42,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3369: South Pacific Viscose - Purwakarta (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 3369;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3369
LIMIT 1;

-- Unit 3577: South Pacific Viscose - Purwakarta (Rp7.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7950000,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 3577;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3577
LIMIT 1;

-- Unit 3707: South Pacific Viscose - Purwakarta (Rp12.450.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12450000,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 3707;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3707
LIMIT 1;

-- Unit 5330: South Pacific Viscose - Purwakarta (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 5330;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5330
LIMIT 1;

-- Unit 5362: South Pacific Viscose - Purwakarta (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 5362;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5362
LIMIT 1;

-- Unit 5363: South Pacific Viscose - Purwakarta (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 5363;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5363
LIMIT 1;

-- Unit 5368: South Pacific Viscose - Purwakarta (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 5368;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5368
LIMIT 1;

-- Unit 2342: South Pacific Viscose - Purwakarta (Rp14.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 14500000,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 2342;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2342
LIMIT 1;

-- Unit 2866: South Pacific Viscose - Purwakarta (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 2866;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2866
LIMIT 1;

-- Unit 3304: South Pacific Viscose - Purwakarta (Rp9.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9000000,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 3304;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3304
LIMIT 1;

-- Unit 3578: South Pacific Viscose - Purwakarta (Rp7.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7950000,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 3578;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3578
LIMIT 1;

-- Unit 3581: South Pacific Viscose - Purwakarta (Rp9.450.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9450000,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 3581;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3581
LIMIT 1;

-- Unit 3634: South Pacific Viscose - Purwakarta (Rp9.450.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9450000,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 3634;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3634
LIMIT 1;

-- Unit 3715: South Pacific Viscose - Purwakarta (Rp12.450.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12450000,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 3715;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3715
LIMIT 1;

-- Unit 3745: South Pacific Viscose - Purwakarta (Rp7.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7950000,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 3745;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3745
LIMIT 1;

-- Unit 3832: South Pacific Viscose - Purwakarta (Rp10.900.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10900000,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 3832;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3832
LIMIT 1;

-- Unit 3833: South Pacific Viscose - Purwakarta (Rp10.900.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10900000,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 3833;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3833
LIMIT 1;

-- Unit 3834: South Pacific Viscose - Purwakarta (Rp10.350.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10350000,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 3834;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3834
LIMIT 1;

-- Unit 3835: South Pacific Viscose - Purwakarta (Rp7.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7950000,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 3835;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3835
LIMIT 1;

-- Unit 3836: South Pacific Viscose - Purwakarta (Rp10.350.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10350000,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 3836;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3836
LIMIT 1;

-- Unit 3841: South Pacific Viscose - Purwakarta (Rp7.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7950000,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 3841;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3841
LIMIT 1;

-- Unit 3846: South Pacific Viscose - Purwakarta (Rp7.950.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7950000,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 3846;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3846
LIMIT 1;

-- Unit 3849: South Pacific Viscose - Purwakarta (Rp7.450.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7450000,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 3849;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3849
LIMIT 1;

-- Unit 3928: South Pacific Viscose - Purwakarta (Rp9.450.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9450000,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 3928;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3928
LIMIT 1;

-- Unit 5157: South Pacific Viscose - Purwakarta (Rp12.450.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12450000,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 5157;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5157
LIMIT 1;

-- Unit 5158: South Pacific Viscose - Purwakarta (Rp12.450.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12450000,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 5158;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5158
LIMIT 1;

-- Unit 5159: South Pacific Viscose - Purwakarta (Rp12.450.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12450000,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 5159;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5159
LIMIT 1;

-- Unit 5161: South Pacific Viscose - Purwakarta (Rp12.450.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12450000,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 5161;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5161
LIMIT 1;

-- Unit 5209: South Pacific Viscose - Purwakarta (Rp12.450.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12450000,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 5209;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5209
LIMIT 1;

-- Unit 5210: South Pacific Viscose - Purwakarta (Rp12.450.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12450000,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 5210;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5210
LIMIT 1;

-- Unit 5244: South Pacific Viscose - Purwakarta (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 5244;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5244
LIMIT 1;

-- Unit 5245: South Pacific Viscose - Purwakarta (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 5245;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5245
LIMIT 1;

-- Unit 5280: South Pacific Viscose - Purwakarta (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 5280;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5280
LIMIT 1;

-- Unit 5364: South Pacific Viscose - Purwakarta (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 5364;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5364
LIMIT 1;

-- Unit 5365: South Pacific Viscose - Purwakarta (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 5365;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5365
LIMIT 1;

-- Unit 5366: South Pacific Viscose - Purwakarta (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 5366;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5366
LIMIT 1;

-- Unit 5367: South Pacific Viscose - Purwakarta (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 5367;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5367
LIMIT 1;

-- Unit 5369: South Pacific Viscose - Purwakarta (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 5369;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5369
LIMIT 1;

-- Unit 5370: South Pacific Viscose - Purwakarta (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 5370;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5370
LIMIT 1;

-- Unit 5371: South Pacific Viscose - Purwakarta (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 5371;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5371
LIMIT 1;

-- Unit 5373: South Pacific Viscose - Purwakarta (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 5373;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5373
LIMIT 1;

-- Unit 5409: South Pacific Viscose - Purwakarta (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 87,
    customer_location_id = 243,
    kontrak_id = @kontrak_id
WHERE no_unit = 5409;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-01-01',
    '2025-31-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5409
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    87,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 3800774659
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2026-02-01 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '3800774659',
    'PO_ONLY',
    '2026-02-01',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'Star Rubber' not found (unit 5704)
-- Unit 5704: Star Rubber -  (Rp17.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 17500000,
    customer_id = 126,
    kontrak_id = @kontrak_id
WHERE no_unit = 5704;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5704
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    126,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 101/SML/XI/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-04-14 to 
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '101/SML/XI/2025',
    'CONTRACT',
    '2025-04-14',
    '',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5295: Summit seoyon automotive indonesia - Deltamas (Rp11.400.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11400000,
    customer_id = 125,
    customer_location_id = 336,
    kontrak_id = @kontrak_id
WHERE no_unit = 5295;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-04-14',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5295
LIMIT 1;

-- Unit 5296: Summit seoyon automotive indonesia - Deltamas (Rp11.400.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11400000,
    customer_id = 125,
    customer_location_id = 336,
    kontrak_id = @kontrak_id
WHERE no_unit = 5296;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-04-14',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5296
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    125,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: PORD-25P1-00005755
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-11-13 to 2025-30-11
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'PORD-25P1-00005755',
    'CONTRACT',
    '2025-11-13',
    '2025-30-11',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3005: Superior Porcelain Sukses - Subang (Rp7.200.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7200000,
    customer_id = 90,
    customer_location_id = 262,
    kontrak_id = @kontrak_id
WHERE no_unit = 3005;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-13',
    '2025-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3005
LIMIT 1;

-- Unit 3838: Superior Porcelain Sukses - Subang (Rp7.200.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7200000,
    customer_id = 90,
    customer_location_id = 262,
    kontrak_id = @kontrak_id
WHERE no_unit = 3838;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-13',
    '2025-30-11',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3838
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    90,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: TAC-POLC-2510-00250
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-09-29 to 2025-13-10
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'TAC-POLC-2510-00250',
    'CONTRACT',
    '2025-09-29',
    '2025-13-10',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3049: Taco Anugrah Corporindo - Cikande (Rp14.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 14000000,
    customer_id = 217,
    customer_location_id = 467,
    kontrak_id = @kontrak_id
WHERE no_unit = 3049;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-09-29',
    '2025-13-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3049
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    217,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: KBN-52056742
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-02-01 to 
-- Units: 6
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'KBN-52056742',
    'CONTRACT',
    '2025-02-01',
    '',
    'ACTIVE',
    'BULANAN',
    6,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 2973: The Univenus - Serang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 278,
    kontrak_id = @kontrak_id
WHERE no_unit = 2973;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2973
LIMIT 1;

-- Unit 2974: The Univenus - Serang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 278,
    kontrak_id = @kontrak_id
WHERE no_unit = 2974;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2974
LIMIT 1;

-- Unit 2975: The Univenus - Serang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 278,
    kontrak_id = @kontrak_id
WHERE no_unit = 2975;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2975
LIMIT 1;

-- Unit 2972: The Univenus - Serang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 278,
    kontrak_id = @kontrak_id
WHERE no_unit = 2972;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2972
LIMIT 1;

-- Unit 2976: The Univenus - Serang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 278,
    kontrak_id = @kontrak_id
WHERE no_unit = 2976;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2976
LIMIT 1;

-- Unit 5587: The Univenus - Serang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 278,
    kontrak_id = @kontrak_id
WHERE no_unit = 5587;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5587
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    95,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: KBN-52056736
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-02-01 to 
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'KBN-52056736',
    'CONTRACT',
    '2025-02-01',
    '',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 2978: The Univenus - Serang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 278,
    kontrak_id = @kontrak_id
WHERE no_unit = 2978;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2978
LIMIT 1;

-- Unit 5588: The Univenus - Serang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 278,
    kontrak_id = @kontrak_id
WHERE no_unit = 5588;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5588
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    95,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: KBN-52056777
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-02-01 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'KBN-52056777',
    'CONTRACT',
    '2025-02-01',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 2980: The Univenus - Serang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 278,
    kontrak_id = @kontrak_id
WHERE no_unit = 2980;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2980
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    95,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: KBN-52056762
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-02-01 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'KBN-52056762',
    'CONTRACT',
    '2025-02-01',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 2981: The Univenus - Serang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 278,
    kontrak_id = @kontrak_id
WHERE no_unit = 2981;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2981
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    95,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: KBN-52056706
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-02-01 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'KBN-52056706',
    'CONTRACT',
    '2025-02-01',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3556: The Univenus - Serang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 278,
    kontrak_id = @kontrak_id
WHERE no_unit = 3556;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3556
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    95,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: KBN-52056713
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-02-01 to 
-- Units: 6
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'KBN-52056713',
    'CONTRACT',
    '2025-02-01',
    '',
    'ACTIVE',
    'BULANAN',
    6,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5387: The Univenus - Serang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 278,
    kontrak_id = @kontrak_id
WHERE no_unit = 5387;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5387
LIMIT 1;

-- Unit 5550: The Univenus - Serang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 278,
    kontrak_id = @kontrak_id
WHERE no_unit = 5550;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5550
LIMIT 1;

-- Unit 5554: The Univenus - Serang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 278,
    kontrak_id = @kontrak_id
WHERE no_unit = 5554;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5554
LIMIT 1;

-- Unit 5538: The Univenus - Serang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 278,
    kontrak_id = @kontrak_id
WHERE no_unit = 5538;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5538
LIMIT 1;

-- Unit 5551: The Univenus - Serang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 278,
    kontrak_id = @kontrak_id
WHERE no_unit = 5551;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5551
LIMIT 1;

-- Unit 5552: The Univenus - Serang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 278,
    kontrak_id = @kontrak_id
WHERE no_unit = 5552;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5552
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    95,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: KBN-52056741
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-02-01 to 
-- Units: 4
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'KBN-52056741',
    'CONTRACT',
    '2025-02-01',
    '',
    'ACTIVE',
    'BULANAN',
    4,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5533: The Univenus - Serang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 278,
    kontrak_id = @kontrak_id
WHERE no_unit = 5533;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5533
LIMIT 1;

-- Unit 5534: The Univenus - Serang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 278,
    kontrak_id = @kontrak_id
WHERE no_unit = 5534;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5534
LIMIT 1;

-- Unit 5535: The Univenus - Serang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 278,
    kontrak_id = @kontrak_id
WHERE no_unit = 5535;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5535
LIMIT 1;

-- Unit 5536: The Univenus - Serang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 278,
    kontrak_id = @kontrak_id
WHERE no_unit = 5536;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5536
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    95,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: KBN-52056768
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-02-01 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'KBN-52056768',
    'CONTRACT',
    '2025-02-01',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5553: The Univenus - Serang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 278,
    kontrak_id = @kontrak_id
WHERE no_unit = 5553;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5553
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    95,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: CKP-71112937
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-03-01 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'CKP-71112937',
    'CONTRACT',
    '2025-03-01',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Cikupa' for customer 'The Univenus' not found (unit 5240)
-- Unit 5240: The Univenus - Cikupa (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    kontrak_id = @kontrak_id
WHERE no_unit = 5240;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-03-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5240
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    95,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: CKP-71112944
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-03-01 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'CKP-71112944',
    'CONTRACT',
    '2025-03-01',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Cikupa' for customer 'The Univenus' not found (unit 5253)
-- Unit 5253: The Univenus - Cikupa (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    kontrak_id = @kontrak_id
WHERE no_unit = 5253;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-03-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5253
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    95,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: CKP-71112936
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-03-01 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'CKP-71112936',
    'CONTRACT',
    '2025-03-01',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Cikupa' for customer 'The Univenus' not found (unit 5377)
-- Unit 5377: The Univenus - Cikupa (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    kontrak_id = @kontrak_id
WHERE no_unit = 5377;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-03-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5377
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    95,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: CKP-71112943
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-03-01 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'CKP-71112943',
    'CONTRACT',
    '2025-03-01',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Cikupa' for customer 'The Univenus' not found (unit 5378)
-- Unit 5378: The Univenus - Cikupa (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    kontrak_id = @kontrak_id
WHERE no_unit = 5378;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-03-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5378
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    95,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 123/PO/Univ-SML/PSM/VII/2022
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2022-10-04 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '123/PO/Univ-SML/PSM/VII/2022',
    'CONTRACT',
    '2022-10-04',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3139: The Univenus - Perawang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 279,
    kontrak_id = @kontrak_id
WHERE no_unit = 3139;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2022-10-04',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3139
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    95,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: KBN-52057757
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-04-01 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'KBN-52057757',
    'CONTRACT',
    '2025-04-01',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 1625: The Univenus - Serang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 278,
    kontrak_id = @kontrak_id
WHERE no_unit = 1625;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-04-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1625
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    95,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: KBN-52056717
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-02-01 to 
-- Units: 4
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'KBN-52056717',
    'CONTRACT',
    '2025-02-01',
    '',
    'ACTIVE',
    'BULANAN',
    4,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3925: The Univenus - Serang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 278,
    kontrak_id = @kontrak_id
WHERE no_unit = 3925;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3925
LIMIT 1;

-- Unit 3926: The Univenus - Serang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 278,
    kontrak_id = @kontrak_id
WHERE no_unit = 3926;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3926
LIMIT 1;

-- Unit 3933: The Univenus - Serang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 278,
    kontrak_id = @kontrak_id
WHERE no_unit = 3933;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3933
LIMIT 1;

-- Unit 3936: The Univenus - Serang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 278,
    kontrak_id = @kontrak_id
WHERE no_unit = 3936;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3936
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    95,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: KBN-52056781
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-02-01 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'KBN-52056781',
    'CONTRACT',
    '2025-02-01',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3929: The Univenus - Serang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 278,
    kontrak_id = @kontrak_id
WHERE no_unit = 3929;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3929
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    95,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: KBN-52056760
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-02-01 to 
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'KBN-52056760',
    'CONTRACT',
    '2025-02-01',
    '',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5040: The Univenus - Serang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 278,
    kontrak_id = @kontrak_id
WHERE no_unit = 5040;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5040
LIMIT 1;

-- Unit 5407: The Univenus - Serang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 278,
    kontrak_id = @kontrak_id
WHERE no_unit = 5407;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5407
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    95,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: KBN-52056749
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-02-01 to 
-- Units: 3
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'KBN-52056749',
    'CONTRACT',
    '2025-02-01',
    '',
    'ACTIVE',
    'BULANAN',
    3,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5426: The Univenus - Serang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 278,
    kontrak_id = @kontrak_id
WHERE no_unit = 5426;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5426
LIMIT 1;

-- Unit 5555: The Univenus - Serang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 278,
    kontrak_id = @kontrak_id
WHERE no_unit = 5555;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5555
LIMIT 1;

-- Unit 5556: The Univenus - Serang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 278,
    kontrak_id = @kontrak_id
WHERE no_unit = 5556;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5556
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    95,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: KBN-52056709
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-02-01 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'KBN-52056709',
    'CONTRACT',
    '2025-02-01',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5466: The Univenus - Serang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 278,
    kontrak_id = @kontrak_id
WHERE no_unit = 5466;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5466
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    95,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 52048488
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2024-03-01 to 2027-03-01
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '52048488',
    'PO_ONLY',
    '2024-03-01',
    '2027-03-01',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 2585: The Univenus - Surabaya (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 276,
    kontrak_id = @kontrak_id
WHERE no_unit = 2585;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-03-01',
    '2027-03-01',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2585
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    95,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 71113680
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-09-17 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '71113680',
    'PO_ONLY',
    '2025-09-17',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3672: The Univenus - Surabaya (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 276,
    kontrak_id = @kontrak_id
WHERE no_unit = 3672;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-09-17',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3672
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    95,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 71112524
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2024-11-01 to 2027-31-10
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '71112524',
    'PO_ONLY',
    '2024-11-01',
    '2027-31-10',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5170: The Univenus - Surabaya (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 276,
    kontrak_id = @kontrak_id
WHERE no_unit = 5170;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-11-01',
    '2027-31-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5170
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    95,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 71112528
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2024-11-01 to 2027-31-10
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '71112528',
    'PO_ONLY',
    '2024-11-01',
    '2027-31-10',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5239: The Univenus - Surabaya (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 276,
    kontrak_id = @kontrak_id
WHERE no_unit = 5239;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-11-01',
    '2027-31-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5239
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    95,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 71112530
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2024-11-01 to 2027-31-10
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '71112530',
    'PO_ONLY',
    '2024-11-01',
    '2027-31-10',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5241: The Univenus - Surabaya (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 276,
    kontrak_id = @kontrak_id
WHERE no_unit = 5241;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-11-01',
    '2027-31-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5241
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    95,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 71112533
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2024-10-01 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '71112533',
    'PO_ONLY',
    '2024-10-01',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5251: The Univenus - Surabaya (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 276,
    kontrak_id = @kontrak_id
WHERE no_unit = 5251;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-10-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5251
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    95,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 71113679
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-09-17 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '71113679',
    'PO_ONLY',
    '2025-09-17',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5817: The Univenus - Surabaya (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 95,
    customer_location_id = 276,
    kontrak_id = @kontrak_id
WHERE no_unit = 5817;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-09-17',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5817
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    95,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4540078045
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-11-14 to 
-- Units: 4
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4540078045',
    'PO_ONLY',
    '2025-11-14',
    '',
    'ACTIVE',
    'BULANAN',
    4,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3709: Tirta Alam Segar - Cibitung (Rp11.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11000000,
    customer_id = 96,
    customer_location_id = 280,
    kontrak_id = @kontrak_id
WHERE no_unit = 3709;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-14',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3709
LIMIT 1;

-- Unit 3713: Tirta Alam Segar - Cibitung (Rp11.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11000000,
    customer_id = 96,
    customer_location_id = 280,
    kontrak_id = @kontrak_id
WHERE no_unit = 3713;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-13',
    '2026-12-02',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3713
LIMIT 1;

-- Unit 1515: Tirta Alam Segar - Cibitung (Rp11.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11000000,
    customer_id = 96,
    customer_location_id = 280,
    kontrak_id = @kontrak_id
WHERE no_unit = 1515;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-13',
    '2026-12-02',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1515
LIMIT 1;

-- Unit 3398: Tirta Alam Segar - Cibitung (Rp11.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11000000,
    customer_id = 96,
    customer_location_id = 280,
    kontrak_id = @kontrak_id
WHERE no_unit = 3398;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-13',
    '2026-12-02',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3398
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    96,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 4540076745
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-09-25 to 2025-25-12
-- Units: 3
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '4540076745',
    'PO_ONLY',
    '2025-09-25',
    '2025-25-12',
    'ACTIVE',
    'BULANAN',
    3,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5283: Tirta Alam Segar - Cibitung (Rp11.300.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11300000,
    customer_id = 96,
    customer_location_id = 280,
    kontrak_id = @kontrak_id
WHERE no_unit = 5283;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-09-25',
    '2025-25-12',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5283
LIMIT 1;

-- Unit 5501: Tirta Alam Segar - Cibitung (Rp11.300.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11300000,
    customer_id = 96,
    customer_location_id = 280,
    kontrak_id = @kontrak_id
WHERE no_unit = 5501;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-09-30',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5501
LIMIT 1;

-- Unit 5502: Tirta Alam Segar - Cibitung (Rp11.300.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11300000,
    customer_id = 96,
    customer_location_id = 280,
    kontrak_id = @kontrak_id
WHERE no_unit = 5502;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-09-30',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5502
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    96,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: TAJVPO26010012
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2026-01-21 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'TAJVPO26010012',
    'CONTRACT',
    '2026-01-21',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5220: Topla Abadi Jaya - Cibitung (Rp13.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 13500000,
    customer_id = 97,
    customer_location_id = 283,
    kontrak_id = @kontrak_id
WHERE no_unit = 5220;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-01-21',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5220
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    97,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: TAJVPO26010001
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2026-01-09 to 2026-08-02
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'TAJVPO26010001',
    'CONTRACT',
    '2026-01-09',
    '2026-08-02',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5386: Topla Abadi Jaya - MM 2100 (Rp12.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 12000000,
    customer_id = 97,
    customer_location_id = 282,
    kontrak_id = @kontrak_id
WHERE no_unit = 5386;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-01-09',
    '2026-08-02',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5386
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    97,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 136/PO/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-11-01 to 2026-31-10
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '136/PO/2025',
    'CONTRACT',
    '2025-11-01',
    '2026-31-10',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3175: TORRECID INDONESIA - Cibitung (Rp5.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 5500000,
    customer_id = 98,
    customer_location_id = 285,
    kontrak_id = @kontrak_id
WHERE no_unit = 3175;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-11-01',
    '2026-31-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3175
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    98,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 156/PO/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2023-10-02 to 2026-01-10
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '156/PO/2025',
    'CONTRACT',
    '2023-10-02',
    '2026-01-10',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3692: TORRECID INDONESIA - Cibitung (Rp7.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7500000,
    customer_id = 98,
    customer_location_id = 285,
    kontrak_id = @kontrak_id
WHERE no_unit = 3692;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2023-10-02',
    '2026-01-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3692
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    98,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: TIM/2403/049
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2024-01-28 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'TIM/2403/049',
    'CONTRACT',
    '2024-01-28',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'Trimitra Indoplast' not found (unit 1613)
-- Unit 1613: Trimitra Indoplast -  (Rp8.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8500000,
    customer_id = 186,
    kontrak_id = @kontrak_id
WHERE no_unit = 1613;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2024-01-28',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 1613
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    186,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 804/SML/I/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-02-01 to 
-- Units: 30
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '804/SML/I/2025',
    'CONTRACT',
    '2025-02-01',
    '',
    'ACTIVE',
    'BULANAN',
    30,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 2680: Ultra Jaya Milk - Padalarang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 132,
    customer_location_id = 345,
    kontrak_id = @kontrak_id
WHERE no_unit = 2680;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2680
LIMIT 1;

-- Unit 2990: Ultra Jaya Milk - Padalarang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 132,
    customer_location_id = 345,
    kontrak_id = @kontrak_id
WHERE no_unit = 2990;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2990
LIMIT 1;

-- Unit 3666: Ultra Jaya Milk - Padalarang (Rp6.900.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6900000,
    customer_id = 132,
    customer_location_id = 345,
    kontrak_id = @kontrak_id
WHERE no_unit = 3666;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3666
LIMIT 1;

-- Unit 3830: Ultra Jaya Milk - Padalarang (Rp5.200.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 5200000,
    customer_id = 132,
    customer_location_id = 345,
    kontrak_id = @kontrak_id
WHERE no_unit = 3830;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3830
LIMIT 1;

-- Unit 3861: Ultra Jaya Milk - Padalarang (Rp5.200.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 5200000,
    customer_id = 132,
    customer_location_id = 345,
    kontrak_id = @kontrak_id
WHERE no_unit = 3861;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3861
LIMIT 1;

-- Unit 3863: Ultra Jaya Milk - Padalarang (Rp5.200.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 5200000,
    customer_id = 132,
    customer_location_id = 345,
    kontrak_id = @kontrak_id
WHERE no_unit = 3863;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3863
LIMIT 1;

-- Unit 3864: Ultra Jaya Milk - Padalarang (Rp5.200.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 5200000,
    customer_id = 132,
    customer_location_id = 345,
    kontrak_id = @kontrak_id
WHERE no_unit = 3864;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3864
LIMIT 1;

-- Unit 5579: Ultra Jaya Milk - Cimanggis (Rp7.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7500000,
    customer_id = 132,
    customer_location_id = 346,
    kontrak_id = @kontrak_id
WHERE no_unit = 5579;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5579
LIMIT 1;

-- Unit 5559: Ultra Jaya Milk - Malang (Rp7.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7500000,
    customer_id = 132,
    customer_location_id = 347,
    kontrak_id = @kontrak_id
WHERE no_unit = 5559;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5559
LIMIT 1;

-- Unit 5560: Ultra Jaya Milk - Malang (Rp7.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7500000,
    customer_id = 132,
    customer_location_id = 347,
    kontrak_id = @kontrak_id
WHERE no_unit = 5560;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5560
LIMIT 1;

-- Unit 2317: Ultra Jaya Milk - Padalarang (Rp7.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7000000,
    customer_id = 132,
    customer_location_id = 345,
    kontrak_id = @kontrak_id
WHERE no_unit = 2317;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2317
LIMIT 1;

-- Unit 2643: Ultra Jaya Milk - Padalarang (Rp0)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 0,
    customer_id = 132,
    customer_location_id = 345,
    kontrak_id = @kontrak_id
WHERE no_unit = 2643;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 2643
LIMIT 1;

-- Unit 3582: Ultra Jaya Milk - Padalarang (Rp6.900.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6900000,
    customer_id = 132,
    customer_location_id = 345,
    kontrak_id = @kontrak_id
WHERE no_unit = 3582;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3582
LIMIT 1;

-- Unit 3609: Ultra Jaya Milk - Padalarang (Rp6.900.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6900000,
    customer_id = 132,
    customer_location_id = 345,
    kontrak_id = @kontrak_id
WHERE no_unit = 3609;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3609
LIMIT 1;

-- Unit 3610: Ultra Jaya Milk - Padalarang (Rp6.900.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6900000,
    customer_id = 132,
    customer_location_id = 345,
    kontrak_id = @kontrak_id
WHERE no_unit = 3610;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3610
LIMIT 1;

-- Unit 3633: Ultra Jaya Milk - Padalarang (Rp6.900.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6900000,
    customer_id = 132,
    customer_location_id = 345,
    kontrak_id = @kontrak_id
WHERE no_unit = 3633;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3633
LIMIT 1;

-- Unit 3669: Ultra Jaya Milk - Padalarang (Rp7.100.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7100000,
    customer_id = 132,
    customer_location_id = 345,
    kontrak_id = @kontrak_id
WHERE no_unit = 3669;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3669
LIMIT 1;

-- Unit 3673: Ultra Jaya Milk - Padalarang (Rp6.900.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6900000,
    customer_id = 132,
    customer_location_id = 345,
    kontrak_id = @kontrak_id
WHERE no_unit = 3673;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3673
LIMIT 1;

-- Unit 3686: Ultra Jaya Milk - Padalarang (Rp6.900.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6900000,
    customer_id = 132,
    customer_location_id = 345,
    kontrak_id = @kontrak_id
WHERE no_unit = 3686;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3686
LIMIT 1;

-- Unit 3777: Ultra Jaya Milk - Padalarang (Rp7.100.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7100000,
    customer_id = 132,
    customer_location_id = 345,
    kontrak_id = @kontrak_id
WHERE no_unit = 3777;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3777
LIMIT 1;

-- Unit 3862: Ultra Jaya Milk - Padalarang (Rp5.200.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 5200000,
    customer_id = 132,
    customer_location_id = 345,
    kontrak_id = @kontrak_id
WHERE no_unit = 3862;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3862
LIMIT 1;

-- Unit 5034: Ultra Jaya Milk - Padalarang (Rp7.100.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7100000,
    customer_id = 132,
    customer_location_id = 345,
    kontrak_id = @kontrak_id
WHERE no_unit = 5034;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5034
LIMIT 1;

-- Unit 5343: Ultra Jaya Milk - Padalarang (Rp8.400.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8400000,
    customer_id = 132,
    customer_location_id = 345,
    kontrak_id = @kontrak_id
WHERE no_unit = 5343;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5343
LIMIT 1;

-- Unit 5346: Ultra Jaya Milk - Padalarang (Rp8.400.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8400000,
    customer_id = 132,
    customer_location_id = 345,
    kontrak_id = @kontrak_id
WHERE no_unit = 5346;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5346
LIMIT 1;

-- Unit 5381: Ultra Jaya Milk - Padalarang (Rp8.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8000000,
    customer_id = 132,
    customer_location_id = 345,
    kontrak_id = @kontrak_id
WHERE no_unit = 5381;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5381
LIMIT 1;

-- Unit 5383: Ultra Jaya Milk - Padalarang (Rp8.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8000000,
    customer_id = 132,
    customer_location_id = 345,
    kontrak_id = @kontrak_id
WHERE no_unit = 5383;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5383
LIMIT 1;

-- Unit 5570: Ultra Jaya Milk - Padalarang (Rp8.900.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8900000,
    customer_id = 132,
    customer_location_id = 345,
    kontrak_id = @kontrak_id
WHERE no_unit = 5570;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5570
LIMIT 1;

-- Unit 5571: Ultra Jaya Milk - Padalarang (Rp8.900.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8900000,
    customer_id = 132,
    customer_location_id = 345,
    kontrak_id = @kontrak_id
WHERE no_unit = 5571;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5571
LIMIT 1;

-- Unit 5580: Ultra Jaya Milk - Purwakarta (Rp7.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7500000,
    customer_id = 132,
    customer_location_id = 350,
    kontrak_id = @kontrak_id
WHERE no_unit = 5580;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5580
LIMIT 1;

-- Unit 5561: Ultra Jaya Milk - Yogyakarta (Rp7.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 7500000,
    customer_id = 132,
    customer_location_id = 349,
    kontrak_id = @kontrak_id
WHERE no_unit = 5561;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-02-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5561
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    132,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 432600047
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-12-18 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '432600047',
    'PO_ONLY',
    '2025-12-18',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 3515: Ultra Jaya Milk - Cibitung (Rp9.250.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9250000,
    customer_id = 132,
    customer_location_id = 351,
    kontrak_id = @kontrak_id
WHERE no_unit = 3515;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-12-18',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3515
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    132,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 097/SML/XI/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-10-22 to 2026-31-10
-- Units: 2
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '097/SML/XI/2025',
    'CONTRACT',
    '2025-10-22',
    '2026-31-10',
    'ACTIVE',
    'BULANAN',
    2,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- Unit 5994: Ultra Jaya Milk - Cibitung (Rp6.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6800000,
    customer_id = 132,
    customer_location_id = 351,
    kontrak_id = @kontrak_id
WHERE no_unit = 5994;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-22',
    '2026-31-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5994
LIMIT 1;

-- Unit 5995: Ultra Jaya Milk - Cibitung (Rp6.800.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 6800000,
    customer_id = 132,
    customer_location_id = 351,
    kontrak_id = @kontrak_id
WHERE no_unit = 5995;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-10-22',
    '2026-31-10',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5995
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    132,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 110/SML-D/I/2026
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2026-01-02 to 2027-01-01
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '110/SML-D/I/2026',
    'CONTRACT',
    '2026-01-02',
    '2027-01-01',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Deltasilicon' for customer 'UPE Power' not found (unit 5355)
-- Unit 5355: UPE Power - Deltasilicon (Rp8.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 8500000,
    customer_id = 133,
    kontrak_id = @kontrak_id
WHERE no_unit = 5355;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-01-02',
    '2027-01-01',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5355
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    133,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 25301184
-- Type: PO_ONLY | Status: ACTIVE
-- Period: 2025-07-14 to 
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '25301184',
    'PO_ONLY',
    '2025-07-14',
    '',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'Wahana Citra Nabati' not found (unit 5133)
-- Unit 5133: Wahana Citra Nabati -  (Rp23.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 23500000,
    customer_id = 218,
    kontrak_id = @kontrak_id
WHERE no_unit = 5133;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-07-14',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5133
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    218,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: WFT/PROC/AGR/I/2025/001
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2025-07-01 to 
-- Units: 5
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    'WFT/PROC/AGR/I/2025/001',
    'CONTRACT',
    '2025-07-01',
    '',
    'ACTIVE',
    'BULANAN',
    5,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location '' for customer 'Wira Insani' not found (unit 3652)
-- Unit 3652: Wira Insani -  (Rp9.700.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 9700000,
    customer_id = 197,
    kontrak_id = @kontrak_id
WHERE no_unit = 3652;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3652
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'Wira Insani' not found (unit 5681)
-- Unit 5681: Wira Insani -  (Rp32.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 32500000,
    customer_id = 197,
    kontrak_id = @kontrak_id
WHERE no_unit = 5681;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5681
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'Wira Insani' not found (unit 5682)
-- Unit 5682: Wira Insani -  (Rp50.200.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 50200000,
    customer_id = 197,
    kontrak_id = @kontrak_id
WHERE no_unit = 5682;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5682
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'Wira Insani' not found (unit 3638)
-- Unit 3638: Wira Insani -  (Rp15.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 15000000,
    customer_id = 197,
    kontrak_id = @kontrak_id
WHERE no_unit = 3638;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3638
LIMIT 1;

-- ⚠ WARNING: Location '' for customer 'Wira Insani' not found (unit 3687)
-- Unit 3687: Wira Insani -  (Rp10.500.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 10500000,
    customer_id = 197,
    kontrak_id = @kontrak_id
WHERE no_unit = 3687;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2025-07-01',
    '',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 3687
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    197,
    @kontrak_id,
    1
);

-- ====================================================================================
-- Contract: 088/YIDN-05/2025
-- Type: CONTRACT | Status: ACTIVE
-- Period: 2026-02-17 to 2027-02-17
-- Units: 1
-- ====================================================================================

-- Step 1: Insert kontrak master
INSERT INTO kontrak (
    no_kontrak,
    rental_type,
    tanggal_mulai,
    tanggal_berakhir,
    status,
    jenis_sewa,
    total_units,
    dibuat_pada
) VALUES (
    '088/YIDN-05/2025',
    'CONTRACT',
    '2026-02-17',
    '2027-02-17',
    'ACTIVE',
    'BULANAN',
    1,
    NOW()
);
SET @kontrak_id = LAST_INSERT_ID();

-- Step 2: Update inventory_unit and link to contract
-- ⚠ WARNING: Location 'Jababeka' for customer 'Yoowon Electronics Jababeka' not found (unit 5653)
-- Unit 5653: Yoowon Electronics Jababeka - Jababeka (Rp11.000.000)
UPDATE inventory_unit SET
    harga_sewa_bulanan = 11000000,
    customer_id = 222,
    kontrak_id = @kontrak_id
WHERE no_unit = 5653;

INSERT IGNORE INTO kontrak_unit (
    kontrak_id,
    unit_id,
    tanggal_mulai,
    tanggal_selesai,
    status
) SELECT
    @kontrak_id,
    id_inventory_unit,
    '2026-02-17',
    '2027-02-17',
    'ACTIVE'
FROM inventory_unit
WHERE no_unit = 5653
LIMIT 1;

-- Step 3: Link customers to contract
INSERT IGNORE INTO customer_contracts (
    customer_id,
    kontrak_id,
    is_active
) VALUES (
    222,
    @kontrak_id,
    1
);

-- ============================================================================
-- SUMMARY
-- ============================================================================
-- Contracts inserted: 363
-- Units updated: 1231
-- Kontrak-unit links created: 1231
-- Customer-contract links created: 374
-- ============================================================================

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
