"""
Generator SQL import unit non-asset dari CSV.
Membaca databases/Input_Data/unit_non_asset.csv dan menghasilkan
INSERT statements untuk inventory_unit + master data yang missing.
"""
import csv
import re
from pathlib import Path

# ─── Mapping Master Data (sesuai DB optima_ci) ──────────────────────
TIPE_UNIT_MAP = {
    'FORKLIFT CB': 6,
    'REACH TRUCK': 12,
    'HAND PALLET ELECTRIC': 9,
    'HAND PALLET MANUAL': 21,
    'PALLET MOVER': 10,
    'PALLET MOVER CABIN': 10,
    'MINI REACH TRUCK STACKER': 24,
    'THREE WHEEL': 13,
    'TOWING': 14,
}

KAPASITAS_MAP = {
    '1,5TON': 15, '1.5TON': 15,
    '1,8TON': 18,
    '2TON': 19,
    '2,5TON': 22, '2.5TON': 22,
    '3TON': 24,
    '3,5TON': 26,
    '4TON': 28,
    '5TON': 30,
    '10TON': 35,
}

# Existing model_unit IDs (HELI/TOYOTA/JUNGHEINRICH/CAT)
EXISTING_MODEL_MAP = {
    'CBD15J':         124,
    'CBD20J':         125,
    'CBD30J':         126,
    'CPCD25':          90,
    'CPCD30':          91,
    'CPCD50':          95,
    'CPCD100':         98,
    'CPD25-GB6LI':    106,
    'CPD30-GC6LI':    110,
    'CPD30J-RLI':     111,
    'CPD35-GC6LI':    112,
    'CPD40-GB2LI':    113,
    'CPD50-GB2LI':    114,
    'CQDM20J':        120,
    'FDZN25':          64,
    '8FBR18':          81,
    'NRS18CA':         40,
    'ETV-MC320':      181,
}

# Models yang BELUM ada di DB → akan diinsert
NEW_MODELS = [
    # (merk, model)
    ('HELI', 'CBD15-ALIH'),
    ('HELI', 'CBD15-YLI'),
    ('HELI', 'CBD15J-LI-S'),
    ('HELI', 'CBD20J-LI-S'),
    ('HELI', 'CBD20-UGD'),
    ('HELI', 'CPCD25-M1K2'),
    ('HELI', 'CPCD30-M1K2'),
    ('HELI', 'CPCD30-Q22K2'),
    ('HELI', 'CPCD30-WS1K2'),
    ('HELI', 'CPCD50-M4G3'),
    ('HELI', 'CPCD100-W2K2'),
    ('HELI', 'CPD25-GB2LI-M'),
    ('HELI', 'CPD25-GB6LI-S'),
    ('HELI', 'CPD30-GB2LI-M'),
    ('HELI', 'CPD30-GC6LI-S'),
    ('HELI', 'CPD35-GC6LI-S'),
    ('HELI', 'CPD50-A2DLIG3'),
    ('HELI', 'CQD20-A2RLIG2'),
    ('HELI', 'CQDM20J-LI'),
    ('CAT',  'NRS18CB1'),
    ('TOYOTA', '8FBR18-NEW'),  # duplicate CSV row 33 says 8FBR18
    ('JUNGHEINRICH', 'ETV-MC320-1150-10520DZ'),
    ('JUNGHEINRICH', 'ETV-MC320-1150-11510DZ'),
]

# Penomoran auto-increment new models (mulai dari 228)
NEW_MODEL_BASE_ID = 228

# Engine (mesin) — yang sudah ada
EXISTING_MESIN_MAP = {
    ('MITSUBISHI', 'S4S'):   4,
    ('MITSUBISHI', 'S6S'):   5,
    ('ISUZU',      '6BG1'):  8,
    ('ISUZU',      'C240'):  9,   # fallback ISUZU lainnya
    ('QUANCHAI',   'QC490GP'): 10,
    ('TOYOTA',     'I DZ'):  18,  # 1DZ = I DZ
}

# Mast mapping berdasar (mast_type, height_mm)
MAST_MAP = {
    ('M300',       '3000MM'):  31,  # HELI SIMPLEX M
    ('ZSM435',     '4350MM'):  44,
    ('ZSM450',     '4500MM'):  45,
    ('ZSM470',     '4700MM'):  46,
    ('FSV 4700',   '4700MM'):  10,  # TOYOTA TRIPLEX FSV 4700
    ('FSVE61 (6000)', '6000MM'): 14,
    ('11510MM (FFL)', '11510MM'): 66,  # JUNGHEINRICH TRIPLEX DZ
    ('10520MM (FFL)', '10520MM'): 65,
    ('R18M700MS (3 STAGE)', '7000MM'): 16,  # TOYOTA TRIPLEX FSV 7000
    ('ZM300 FFL 2 STAGE', '3000MM'): 40,  # HELI DUPLEX ZM 3000
}

RODA_MAP = {
    'SOLID TIRE':   1,  # DRIVE WHEEL (default for solid tire forklift)
    'PNEUMATIC':    1,  # DRIVE WHEEL
    'LOAD WHEEL':   2,
    'CASTER WHEEL': 3,
}

BAN_MAP = {
    'SOLID TIRE': 1,  # Solid (Ban Mati)
    'PNEUMATIC':  2,  # Pneumatic (Ban Angin)
    # LOAD WHEEL & CASTER WHEEL → ban_id NULL
}

VALVE_MAP = {
    '2': 1, '3': 2, '4': 3, '5': 4,
}

STATUS_NON_ASSET = 2  # NON_ASSET_STOCK

# Nomor stok berbentuk STOCK-XXXX (4 digit).
# Prod sudah ada STOCK-0001 & STOCK-0002, jadi import mulai dari 3.
STOCK_START_SEQ = 3


def lookup_model(merk_hint: str, model_csv: str, new_models_idx: dict) -> int | None:
    """Cari id_model_unit. Return id atau None kalau tidak ketemu."""
    # 1. cek existing
    if model_csv in EXISTING_MODEL_MAP:
        return EXISTING_MODEL_MAP[model_csv]
    # 2. cek new models (berdasar nama persis)
    for (merk, model), idx in new_models_idx.items():
        if model == model_csv:
            return idx
    # 3. fallback: cari prefix
    for key, val in EXISTING_MODEL_MAP.items():
        if model_csv.startswith(key):
            return val
    return None


def lookup_mast(mast_type: str, mast_height: str) -> int | None:
    key = (mast_type.strip(), mast_height.strip())
    return MAST_MAP.get(key)


def lookup_mesin(merk_engine: str, sn_engine: str) -> int | None:
    if merk_engine in ('-', '', None):
        return None
    merk = merk_engine.strip().upper()
    # TOYOTA 1DZ-xxx detection
    if merk == 'TOYOTA' and sn_engine and sn_engine.startswith('1DZ'):
        return EXISTING_MESIN_MAP[('TOYOTA', 'I DZ')]
    # MITSUBISHI by SN prefix
    if merk == 'MITSUBISHI':
        if sn_engine and sn_engine.startswith('S4S'):
            return 4
        if sn_engine and sn_engine.startswith('S6S'):
            return 5
        return 4  # default
    # ISUZU
    if merk == 'ISUZU':
        if sn_engine and sn_engine.startswith('6BG1'):
            return 8
        return 9  # generic ISUZU
    # QUANCHAI
    if merk == 'QUANCHAI':
        return 10
    return None


def parse_kap(raw: str) -> int | None:
    raw = raw.strip().upper().replace(' ', '')
    return KAPASITAS_MAP.get(raw)


def parse_valve(raw: str) -> int | None:
    raw = raw.strip()
    if raw in ('-', '', None):
        return None
    return VALVE_MAP.get(raw)


def sql_str(val) -> str:
    """Escape & quote untuk SQL string. NULL kalau kosong/dash."""
    if val is None:
        return 'NULL'
    s = str(val).strip()
    if s in ('', '-'):
        return 'NULL'
    s = s.replace("'", "''")
    return f"'{s}'"


def sql_int(val) -> str:
    if val is None or val == '':
        return 'NULL'
    return str(val)


def main():
    base = Path(__file__).resolve().parent.parent / 'databases' / 'Input_Data'
    csv_path = base / 'unit_non_asset.csv'
    out_path = Path(__file__).resolve().parent.parent / 'databases' / 'migrations' / '2026_04_23_import_unit_non_asset.sql'

    # New models index (auto increment ID alloc)
    new_models_idx = {}
    for i, (merk, model) in enumerate(NEW_MODELS):
        new_models_idx[(merk, model)] = NEW_MODEL_BASE_ID + i

    rows_out = []
    skipped = []
    with csv_path.open(encoding='utf-8-sig') as f:
        reader = csv.DictReader(f, delimiter=';')
        for row in reader:
            no = row['NO. URUT']
            sn = row['SERIAL NUMBER'].strip()
            model = row['MODEL'].strip()
            type_unit = row['TYPE UNIT'].strip()
            year = row['YEAR'].strip()
            spec = row['SPECIFICATION'].strip()
            engine_model = row['ENGINE MODEL'].strip()
            sn_engine = row['SN ENGINE'].strip()
            kap = row['KAP'].strip()
            fuel = row['FUEL TYPE'].strip()
            mast_type = row['MAST TYPE'].strip()
            mast_height = row['MAST HEIGHT'].strip()
            sn_mast = row['SN MAST'].strip()
            type_tire = row['TYPE TIRE'].strip()
            valve = row['VALVE'].strip()

            tipe_unit_id = TIPE_UNIT_MAP.get(type_unit)
            if not tipe_unit_id:
                skipped.append((no, sn, f'tipe_unit "{type_unit}" tidak dikenal'))
                continue

            model_id = lookup_model('', model, new_models_idx)
            kap_id = parse_kap(kap)
            mast_id = lookup_mast(mast_type, mast_height)
            mesin_id = lookup_mesin(engine_model, sn_engine)
            roda_id = RODA_MAP.get(type_tire)
            ban_id = BAN_MAP.get(type_tire)
            valve_id = parse_valve(valve)
            # Nomor stok: STOCK-XXXX (4 digit) mulai dari STOCK_START_SEQ
            # Prod sudah ada STOCK-0001 & STOCK-0002, jadi default start = 3
            seq = STOCK_START_SEQ + len(rows_out)
            na_num = f'STOCK-{seq:04d}'

            fuel_clean = fuel.upper() if fuel and fuel != '-' else None

            cols = [
                'serial_number', 'no_unit_na', 'tahun_unit', 'status_unit_id',
                'tipe_unit_id', 'model_unit_id', 'kapasitas_unit_id',
                'model_mast_id', 'tinggi_mast', 'sn_mast',
                'model_mesin_id', 'sn_mesin',
                'roda_id', 'ban_id', 'valve_id',
                'fuel_type', 'aksesoris', 'created_at', 'updated_at'
            ]
            vals = [
                sql_str(sn),
                sql_str(na_num),
                sql_str(year),
                str(STATUS_NON_ASSET),
                str(tipe_unit_id),
                sql_int(model_id),
                sql_int(kap_id),
                sql_int(mast_id),
                sql_str(mast_height),
                sql_str(sn_mast),
                sql_int(mesin_id),
                sql_str(sn_engine),
                sql_int(roda_id),
                sql_int(ban_id),
                sql_int(valve_id),
                sql_str(fuel_clean),
                sql_str(spec),
                'NOW()', 'NOW()'
            ]
            line = f"INSERT INTO `inventory_unit` ({', '.join('`'+c+'`' for c in cols)}) VALUES ({', '.join(vals)});"
            rows_out.append((no, sn, model, line))

    # Generate output SQL
    sql_lines = []
    sql_lines.append("-- ═══════════════════════════════════════════════════════════════════════════════")
    sql_lines.append("-- MIGRATION: Import 132 Unit Non-Asset (status_unit_id = 2)")
    sql_lines.append("-- Date: 2026-04-23")
    sql_lines.append("-- Source: databases/Input_Data/unit_non_asset.csv")
    sql_lines.append("--")
    sql_lines.append("-- Pre-requisite:")
    sql_lines.append("--   1. Backup database dulu!")
    sql_lines.append("--   2. Pastikan SN tidak ada yang duplikat dengan data existing")
    sql_lines.append("-- ═══════════════════════════════════════════════════════════════════════════════")
    sql_lines.append("")
    sql_lines.append("START TRANSACTION;")
    sql_lines.append("")
    sql_lines.append("-- ─────────────────────────────────────────────")
    sql_lines.append("-- 1. Insert master model_unit yang belum ada")
    sql_lines.append("-- ─────────────────────────────────────────────")
    for (merk, model), idx in new_models_idx.items():
        sql_lines.append(
            f"INSERT INTO `model_unit` (`id_model_unit`, `merk_unit`, `model_unit`, `departemen_id`) "
            f"VALUES ({idx}, {sql_str(merk)}, {sql_str(model)}, 2) "
            f"ON DUPLICATE KEY UPDATE `model_unit` = VALUES(`model_unit`);"
        )
    sql_lines.append("")
    sql_lines.append("-- ─────────────────────────────────────────────")
    first_seq = STOCK_START_SEQ
    last_seq = STOCK_START_SEQ + len(rows_out) - 1
    sql_lines.append(f"-- 2. Insert {len(rows_out)} unit (STOCK-{first_seq:04d} .. STOCK-{last_seq:04d})")
    sql_lines.append("-- ─────────────────────────────────────────────")
    for no, sn, model, line in rows_out:
        sql_lines.append(f"-- Row {no}: {model} | SN: {sn}")
        sql_lines.append(line)
    sql_lines.append("")
    sql_lines.append("COMMIT;")
    sql_lines.append("")
    sql_lines.append("-- ─────────────────────────────────────────────")
    sql_lines.append("-- 3. Verifikasi")
    sql_lines.append("-- ─────────────────────────────────────────────")
    sql_lines.append(f"-- SELECT COUNT(*) FROM inventory_unit WHERE no_unit_na LIKE 'STOCK-%' AND status_unit_id = 2;  -- expect (local): {len(rows_out) + 2}")
    sql_lines.append("-- SELECT no_unit_na, serial_number, tahun_unit, fuel_type FROM inventory_unit WHERE no_unit_na LIKE 'STOCK-%' ORDER BY no_unit_na LIMIT 10;")

    out_path.write_text('\n'.join(sql_lines), encoding='utf-8')
    print(f"✅ Generated: {out_path}")
    print(f"   Total inserted: {len(rows_out)}")
    print(f"   New master models: {len(new_models_idx)}")
    if skipped:
        print(f"\n⚠ Skipped {len(skipped)} rows:")
        for no, sn, reason in skipped:
            print(f"   Row {no} (SN={sn}): {reason}")


if __name__ == '__main__':
    main()
