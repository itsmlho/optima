#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Baca data_baterai.csv -> SQL:

  1) import_baterai_master.sql
     INSERT kombinasi (merk, tipe, jenis) ke `baterai` jika belum ada.

  2) import_inventory_batteries.sql
     - Mengosongkan referensi & seluruh `inventory_batteries`, lalu
     - INSERT ulang dari CSV (satu-satunya sumber untuk inventory baterai).

Aturan item_number (sesuai permintaan):
  - Lead Acid  -> B + 4 digit urut dalam urutan baris CSV (B0001, B0002, ...)
  - Lithium*   -> BL + 4 digit urut dalam urutan baris CSV (BL0001, ...)

*Deteksi lithium: kolom Jenis mengandung Lithium / Li-ion / LiFePO4 / LFP, dll.
  Selain itu dianggap Lead Acid (termasuk teks "Lead Acid").

Jalankan:
  python databases/Input_Data/build_battery_import_sql.py
"""

from __future__ import annotations

import csv
import re
from collections import Counter
from datetime import datetime
from pathlib import Path

CSV_NAME = "data_baterai.csv"
OUT_MASTER = "import_baterai_master.sql"
OUT_INV = "import_inventory_batteries.sql"
OUT_README = "import_battery_README.txt"

STATUS_MAP = {
    "TERPASANG": "IN_USE",
    "AVAILABLE": "AVAILABLE",
    "DIJUAL": "SOLD",
    "SPARE": "SPARE",
}

MONTH_ID = {
    "januari": 1,
    "februari": 2,
    "maret": 3,
    "april": 4,
    "mei": 5,
    "juni": 6,
    "juli": 7,
    "agustus": 8,
    "september": 9,
    "oktober": 10,
    "november": 11,
    "desember": 12,
}


def sql_str(s: str | None) -> str:
    if s is None:
        return "NULL"
    s = s.replace("\\", "\\\\").replace("'", "''")
    return f"'{s}'"


def sql_esc(s: str) -> str:
    return s.replace("\\", "\\\\").replace("'", "''")


def clean_cell(v: str | None) -> str:
    if v is None:
        return ""
    return " ".join(v.strip().split())


def norm_master_field(s: str, max_len: int, default: str = "-") -> str:
    s = clean_cell(s)
    if s in ("", "-", "—", "–", "N/A", "n/a", ".", "#VALUE!", "#REF!", "#N/A"):
        return default[:max_len]
    return s[:max_len]


def is_lithium_jenis(jenis_raw: str) -> bool:
    """Selaras logika filter chemistry di InventoryBatteryModel (lead vs lithium)."""
    u = clean_cell(jenis_raw).upper()
    if not u:
        return False
    if "LEAD" in u and "ACID" in u:
        return False
    lithium_markers = (
        "LITHIUM",
        "LI-ION",
        "LI ION",
        "LIION",
        "LIFEPO",
        "LIFEPO4",
        "LFP",
        "NMC",
        "NCA",
    )
    compact = u.replace(" ", "").replace("-", "")
    for m in lithium_markers:
        if m.replace("-", "") in compact or m in u:
            return True
    return False


def parse_voltage_ah(aset: str, tipe_batre: str) -> tuple[str | None, str | None]:
    blob = f"{aset} {tipe_batre}".upper()
    v = None
    m = re.search(r"(\d+)\s*V\b", blob)
    if m:
        v = f"{int(m.group(1))}.0"
    ah = None
    m2 = re.search(r"(\d+)\s*AH", blob)
    if m2:
        ah = str(int(m2.group(1)))
    return v, ah


def parse_date(raw: str) -> tuple[str | None, str | None]:
    raw = clean_cell(raw)
    if not raw:
        return None, None
    for fmt in ("%d/%m/%Y", "%d-%m-%Y", "%Y-%m-%d"):
        try:
            d = datetime.strptime(raw, fmt)
            return d.strftime("%Y-%m-%d 00:00:00"), None
        except ValueError:
            pass
    m = re.match(r"^([A-Za-z]+)\s+(\d{4})$", raw, re.I)
    if m:
        mon = MONTH_ID.get(m.group(1).lower())
        if mon:
            y = int(m.group(2))
            return f"{y:04d}-{mon:02d}-01 00:00:00", None
    return None, raw


def choose_received_at(row: dict) -> tuple[str | None, str]:
    notes_extra = []
    for key in ("Tgl Dtg", "Tgl Pertama Kali Kirim"):
        dt, bad = parse_date(row.get(key, ""))
        if dt:
            return dt, ""
        if bad:
            notes_extra.append(f"{key}={bad}")
    return None, "; ".join(notes_extra)


def nb_col_key(sample_row: dict) -> str:
    for k in sample_row:
        if "No Batt" in k.replace("\ufeff", ""):
            return k
    raise SystemExit("Kolom No Batt tidak ditemukan")


def main() -> None:
    base = Path(__file__).resolve().parent
    csv_path = base / CSV_NAME
    rows: list[dict] = list(
        csv.DictReader(open(csv_path, encoding="utf-8-sig", newline=""), delimiter=";")
    )
    if not rows:
        raise SystemExit("CSV kosong")

    k_nb = nb_col_key(rows[0])

    master_keys: set[tuple[str, str, str]] = set()
    issues: list[str] = []

    la_seq = 0
    li_seq = 0
    prepared: list[dict] = []

    for i, r in enumerate(rows, start=2):
        item_raw = clean_cell(r.get(k_nb, ""))

        jenis = norm_master_field(r.get("Jenis", ""), 50, "Lead Acid")
        lithium = is_lithium_jenis(r.get("Jenis", "") or "")
        if lithium:
            li_seq += 1
            item_number = f"BL{li_seq:04d}"
        else:
            la_seq += 1
            item_number = f"B{la_seq:04d}"

        merk = norm_master_field(r.get("Aset battery", ""), 100, "-")
        tipe = norm_master_field(r.get("TYPE BATRE", ""), 100, "-")
        master_keys.add((merk, tipe, jenis))

        st_raw = clean_cell(r.get("status", "")).upper()
        status = STATUS_MAP.get(st_raw)
        if not status:
            status = "AVAILABLE"
            issues.append(f"Baris ~{i}: status tidak dikenal {st_raw!r} -> AVAILABLE")

        sn = clean_cell(r.get("SN BATRE", ""))
        if sn in ("", "-", "—", "–", ".", "#VALUE!", "#REF!", "#N/A"):
            sn_sql = "NULL"
        else:
            sn_sql = sql_str(sn[:100])

        no_unit = clean_cell(r.get("NO UNIT", ""))
        if no_unit.isdigit():
            unit_sub = (
                "(SELECT id_inventory_unit FROM inventory_unit "
                f"WHERE no_unit = {int(no_unit)} LIMIT 1)"
            )
        elif not no_unit:
            unit_sub = "NULL"
        else:
            unit_sub = (
                "(SELECT id_inventory_unit FROM inventory_unit "
                f"WHERE CAST(no_unit AS CHAR) = {sql_str(no_unit)} LIMIT 1)"
            )

        lokasi = clean_cell(r.get("LOKASI", ""))
        storage_sql = sql_str(lokasi[:255]) if lokasi else "NULL"

        v, ah = parse_voltage_ah(
            r.get("Aset battery", "") or "", r.get("TYPE BATRE", "") or ""
        )
        v_sql = sql_str(v) if v else "NULL"
        ah_sql = ah if ah else "NULL"

        recv, recv_note = choose_received_at(r)
        recv_sql = sql_str(recv) if recv else "NULL"

        note_parts = []
        if item_raw:
            note_parts.append(f"No Batt (CSV): {item_raw}")
        if recv_note:
            note_parts.append(recv_note)
        notes_sql = sql_str("; ".join(note_parts)) if note_parts else "NULL"

        if item_raw.upper() in ("#VALUE!", "#REF!", "#N/A"):
            issues.append(
                f"Baris ~{i}: No Batt CSV invalid {item_raw!r} -> item_number sistem {item_number}"
            )

        prepared.append(
            {
                "item_number": item_number,
                "merk": merk,
                "tipe": tipe,
                "jenis": jenis,
                "sn_sql": sn_sql,
                "unit_sub": unit_sub,
                "storage_sql": storage_sql,
                "status": status,
                "v_sql": v_sql,
                "ah_sql": ah_sql,
                "recv_sql": recv_sql,
                "notes_sql": notes_sql,
            }
        )

    # --- SQL master ---
    master_lines = [
        "-- Generated by build_battery_import_sql.py",
        "-- Menambah baris master `baterai` yang belum ada (merk + tipe + jenis).",
        "SET NAMES utf8mb4;",
        "SET FOREIGN_KEY_CHECKS=0;",
        "",
    ]
    for merk, tipe, jenis in sorted(master_keys):
        m, t, j = sql_esc(merk), sql_esc(tipe), sql_esc(jenis)
        master_lines.append(
            "INSERT INTO baterai (merk_baterai, tipe_baterai, jenis_baterai)\n"
            f"SELECT '{m}', '{t}', '{j}'\n"
            "WHERE NOT EXISTS (\n"
            "  SELECT 1 FROM baterai b\n"
            f"  WHERE b.merk_baterai = '{m}' AND b.tipe_baterai = '{t}' AND b.jenis_baterai = '{j}'\n"
            ");"
        )
    master_lines.extend(["", "SET FOREIGN_KEY_CHECKS=1;", ""])

    # --- SQL inventory: reset penuh + insert ---
    inv_lines = [
        "-- Generated by build_battery_import_sql.py",
        "-- GANTI SELURUH inventory_batteries dengan data CSV.",
        "-- WAJIB backup DB. Jalankan `import_baterai_master.sql` dulu.",
        "",
        "SET NAMES utf8mb4;",
        "SET FOREIGN_KEY_CHECKS=0;",
        "",
        "START TRANSACTION;",
        "",
        "-- Opsional: hanya jika tabel ada di DB Anda (banyak skema lama tidak punya component_timeline).",
        "-- DELETE FROM component_timeline WHERE component_type = 'BATTERY';",
        "-- DELETE FROM unit_movements WHERE component_type = 'BATTERY';",
        "",
        "-- Opsional: kosongkan referensi SPK ke ID baterai lama (uncomment jika tabel + kolom ada).",
        "-- UPDATE spk_unit_stages SET battery_inventory_attachment_id = NULL",
        "-- WHERE battery_inventory_attachment_id IS NOT NULL;",
        "",
        "DELETE FROM inventory_batteries;",
        "",
    ]

    for p in prepared:
        m, t, j = sql_esc(p["merk"]), sql_esc(p["tipe"]), sql_esc(p["jenis"])
        inv_lines.append(
            "INSERT INTO inventory_batteries (\n"
            "  item_number, battery_type_id, serial_number, voltage, ampere_hour,\n"
            "  purchase_order_id, inventory_unit_id, physical_condition, completeness,\n"
            "  physical_notes, storage_location, warehouse_location_id, status, received_at, notes\n"
            ") SELECT\n"
            f"  {sql_str(p['item_number'])},\n"
            "  (SELECT id FROM baterai b WHERE b.merk_baterai = '"
            + m
            + "' AND b.tipe_baterai = '"
            + t
            + "' AND b.jenis_baterai = '"
            + j
            + "' LIMIT 1),\n"
            f"  {p['sn_sql']},\n"
            f"  {p['v_sql']},\n"
            f"  {p['ah_sql']},\n"
            "  NULL,\n"
            f"  {p['unit_sub']},\n"
            "  'GOOD',\n"
            "  'COMPLETE',\n"
            "  NULL,\n"
            f"  {p['storage_sql']},\n"
            "  NULL,\n"
            f"  '{p['status']}',\n"
            f"  {p['recv_sql']},\n"
            f"  {p['notes_sql']}\n"
            "WHERE (SELECT id FROM baterai b WHERE b.merk_baterai = '"
            + m
            + "' AND b.tipe_baterai = '"
            + t
            + "' AND b.jenis_baterai = '"
            + j
            + "' LIMIT 1) IS NOT NULL;"
        )

    inv_lines.extend(
        [
            "",
            "COMMIT;",
            "SET FOREIGN_KEY_CHECKS=1;",
            "",
        ]
    )

    (base / OUT_MASTER).write_text("\n".join(master_lines), encoding="utf-8")
    (base / OUT_INV).write_text("\n".join(inv_lines), encoding="utf-8")

    stc = Counter(p["status"] for p in prepared)
    li_count = sum(1 for p in prepared if p["item_number"].startswith("BL"))
    la_count = len(prepared) - li_count

    readme = [
        "Impor / reset data baterai dari CSV",
        "===============================",
        f"Sumber: {CSV_NAME}",
        f"Total baris inventory: {len(prepared)}",
        f"  Lead Acid (item_number B####): {la_count}",
        f"  Lithium     (item_number BL####): {li_count}",
        f"Kombinasi master unik `baterai`: {len(master_keys)}",
        "",
        "Urutan eksekusi MySQL:",
        f"  1) {OUT_MASTER}   -- lengkapi master",
        f"  2) {OUT_INV}       -- hapus semua inventory baterai + isi ulang dari CSV",
        "",
        "Peringatan:",
        "- File (2) akan DELETE semua baris inventory_batteries lalu INSERT dari CSV.",
        "- Baris DELETE timeline / unit_movements / UPDATE SPK di file SQL dalam bentuk COMMENT;",
        "  uncomment manual jika tabel/kolom tersebut ada di DB Anda.",
        "- Backup database sebelum menjalankan.",
        "",
        "Distribusi status:",
    ]
    for k, v in stc.most_common():
        readme.append(f"  {k}: {v}")
    readme.extend(
        [
            "",
            "Mapping status CSV -> inventory_batteries.status:",
        ]
    )
    for a, b in STATUS_MAP.items():
        readme.append(f"  {a} -> {b}")
    readme.extend(
        [
            "",
            "Catatan teknis:",
            "- item_number tidak lagi mengikuti kolom No Batt CSV; nomor urut per jenis (B/BL) mengikuti urutan baris file.",
            "- Kolom notes berisi 'No Batt (CSV): ...' untuk lacak balik.",
            "- MERK/TIPE kosong di CSV -> '-' (NOT NULL di master).",
            "",
            "Peringatan otomatis skrip:",
        ]
    )
    readme.extend(issues[:200])
    if len(issues) > 200:
        readme.append(f"... dan {len(issues) - 200} lainnya")
    (base / OUT_README).write_text("\n".join(readme), encoding="utf-8")

    print(f"Wrote {base / OUT_MASTER}")
    print(f"Wrote {base / OUT_INV}")
    print(f"Wrote {base / OUT_README}")
    print(f"Lead Acid rows: {la_count}, Lithium rows: {li_count}, issues: {len(issues)}")


if __name__ == "__main__":
    main()
