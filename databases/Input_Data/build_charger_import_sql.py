#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Generate SQL import untuk data charger mentah (data_charger.csv).

Output:
  - import_charger_master.sql
  - import_inventory_chargers.sql
  - import_charger_README.txt
"""

from __future__ import annotations

import csv
import re
from collections import Counter
from datetime import datetime
from pathlib import Path

CSV_NAME = "data_charger.csv"
OUT_MASTER = "import_charger_master.sql"
OUT_INV = "import_inventory_chargers.sql"
OUT_README = "import_charger_README.txt"

STATUS_MAP = {
    "TERPASANG": "IN_USE",
    "AVAILABLE": "AVAILABLE",
    "SPARE": "SPARE",
    "DIJUAL": "SOLD",
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

KNOWN_BRANDS = [
    "INDUSTRIAL BATTERY CHARGING",
    "SMART BATTERY CHARGER",
    "LITHIUM CHARGER",
    "CHARGER ENERPLUSE",
    "CHARGER ENERPULSE",
    "CHARGER SDE T",
    "JUNGHEINRICH",
    "SEGI ELECTRONIC",
    "BEIJING DELONG PRL",
    "MICRO POWER",
    "SE CONTROL",
    "ASIA RG-T",
    "GS YUASA",
    "ENERGIC PLUS",
    "EASYCON PLUS",
    "YALE MICOM",
    "STILL ECOTON XMP",
    "STILL ECOTRON",
    "RG-T",
    "BAST RG",
    "BAST",
    "GREEN",
    "ECOTRON",
    "SUMITOMO",
    "ENERPULSE",
    "ENERPLUS",
    "TIANNENG",
    "JUNGHENRICH",
    "YALE",
    "BPI DX",
    "BPI",
    "SE",
    "SDC ECO",
    "SDC",
    "ADY",
    "ASIA",
    "MARS",
    "BAS",
    "RG",
    "LI-ION",
    "LITHIUM",
    "LITIUM",
    "LI",
]


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


def normalize_hyphen(s: str) -> str:
    return s.replace("–", "-").replace("—", "-")


def normalize_item_no(raw: str, seq: int) -> tuple[str, str | None]:
    raw_u = clean_cell(raw).upper().replace(" ", "")
    if not raw_u:
        return f"C-ROW-{seq:04d}", f"No charger kosong -> C-ROW-{seq:04d}"
    m = re.match(r"^C0*(\d+)$", raw_u)
    if m:
        return f"C{int(m.group(1)):04d}", None
    m = re.match(r"^0*(\d+)C$", raw_u)
    if m:
        return f"C{int(m.group(1)):04d}", f"Format NO CHARGER {raw!r} dinormalisasi"
    m = re.match(r"^CH0*(\d+)$", raw_u)
    if m:
        return f"C{int(m.group(1)):04d}", f"Format NO CHARGER {raw!r} dinormalisasi"
    return raw_u[:50], f"Format NO CHARGER tidak standar: {raw!r}"


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


def extract_serial(raw: str) -> str | None:
    u = normalize_hyphen(clean_cell(raw)).upper()
    m = re.search(r"\b(?:SN|S/N|SERIAL(?:\s*NO)?)\s*[:\-]?\s*(.+)$", u)
    if not m:
        return None
    tail = m.group(1)
    tail = re.split(r"\b(?:TYPE|TY)\b\s*[:\-]?", tail, maxsplit=1)[0]
    tail = clean_cell(tail.strip("-:;,."))
    if not tail or tail in {"-", "N/A", "#VALUE!"}:
        return None
    return tail[:100]


def extract_type_code(raw: str) -> str | None:
    u = normalize_hyphen(clean_cell(raw)).upper()
    m = re.search(r"\b(?:TYPE|TY)\b\s*[:\-]?\s*([A-Z0-9][A-Z0-9\-/\. ]{1,80})", u)
    if not m:
        return None
    code = clean_cell(m.group(1).strip("-:;,."))
    if not code or code == "-":
        return None
    return code[:80]


def remove_serial_and_type_text(raw: str) -> str:
    u = normalize_hyphen(clean_cell(raw)).upper()
    u = re.sub(r"\b(?:SN|S/N|SERIAL(?:\s*NO)?)\b\s*[:\-]?.*$", "", u)
    u = re.sub(r"\b(?:TYPE|TY)\b\s*[:\-]?.*$", "", u)
    u = clean_cell(u.strip("-:;,."))
    return u


def extract_voltage_amp(raw: str) -> tuple[str | None, str | None, str | None]:
    u = normalize_hyphen(clean_cell(raw)).upper()
    volt_vals = [float(v) for v in re.findall(r"\b(\d{2,3}(?:\.\d+)?)\s*V\b", u)]
    amp_nums = [int(a) for a in re.findall(r"\b(\d{2,4})\s*A(?:H)?\b", u)]

    out_v = None
    in_v = None
    if volt_vals:
        low = [v for v in volt_vals if v <= 100]
        hi = [v for v in volt_vals if v > 100]
        fmt = lambda n: str(int(n)) if float(n).is_integer() else str(n)
        if low:
            out_v = f"{fmt(low[0])}V"
        else:
            out_v = f"{fmt(volt_vals[0])}V"
        if hi:
            in_v = f"{fmt(hi[0])}V AC"

    out_a = f"{amp_nums[0]}A" if amp_nums else None
    return in_v, out_v, out_a


def normalize_brand(brand: str) -> str:
    b = clean_cell(brand).upper()
    if b in {"LI", "LITIUM", "LI-ION", "LITHIUM CHARGER"}:
        return "LITHIUM"
    if b == "CHARGER":
        return "UNKNOWN"
    return b


def infer_brand_and_type(raw: str, type_code: str | None) -> tuple[str, str]:
    desc = remove_serial_and_type_text(raw)
    if not desc:
        return "UNKNOWN", "-"

    brand = ""
    tipe = ""

    for cand in KNOWN_BRANDS:
        if desc == cand or desc.startswith(cand + " "):
            brand = cand
            tipe = clean_cell(desc[len(cand):])
            break

    if not brand:
        m_v = re.search(r"\b\d{2,3}(?:\.\d+)?\s*V\b", desc)
        if m_v:
            left = clean_cell(desc[: m_v.start()].strip("- /"))
            right = clean_cell(desc[m_v.start() :])
            brand = left if left else "UNKNOWN"
            tipe = right
        else:
            m_n = re.search(r"\b\d{2,5}\b", desc)
            if m_n:
                left = clean_cell(desc[: m_n.start()].strip("- /"))
                right = clean_cell(desc[m_n.start() :])
                brand = left if left else "UNKNOWN"
                tipe = right if right else "-"
            else:
                toks = desc.split()
                if len(toks) == 1:
                    brand = toks[0]
                    tipe = "-"
                else:
                    brand = toks[0]
                    tipe = clean_cell(" ".join(toks[1:]))

    brand = normalize_brand(brand)[:100]
    tipe = (tipe or "-")[:100]

    if type_code and type_code not in tipe:
        if tipe == "-":
            tipe = type_code[:100]
        else:
            tipe = clean_cell(f"{tipe} {type_code}")[:100]

    return brand or "UNKNOWN", tipe or "-"


def main() -> None:
    base = Path(__file__).resolve().parent
    rows = list(
        csv.DictReader(open(base / CSV_NAME, encoding="utf-8-sig", newline=""), delimiter=";")
    )
    if not rows:
        raise SystemExit("CSV kosong")

    issues: list[str] = []
    master_keys: set[tuple[str, str]] = set()
    prepared: list[dict] = []
    seen_item: dict[str, int] = {}

    for i, r in enumerate(rows, start=2):
        item, issue_item = normalize_item_no(r.get("NO CHARGER", ""), i - 1)
        if issue_item:
            issues.append(f"Baris ~{i}: {issue_item}")

        seen_item[item] = seen_item.get(item, 0) + 1
        if seen_item[item] > 1:
            item = f"{item}-DUP{seen_item[item]}"
            issues.append(f"Baris ~{i}: item_number duplikat -> {item}")

        raw_desc = clean_cell(r.get("CHARGER ", ""))
        type_code = extract_type_code(raw_desc)
        merk, tipe = infer_brand_and_type(raw_desc, type_code)
        master_keys.add((merk, tipe))

        in_v, out_v, out_a = extract_voltage_amp(raw_desc)
        serial = extract_serial(raw_desc)

        status_raw = clean_cell(r.get("STATUS", "")).upper()
        status = STATUS_MAP.get(status_raw)
        if not status:
            status = "AVAILABLE"
            issues.append(f"Baris ~{i}: status tidak dikenal {status_raw!r} -> AVAILABLE")

        no_unit = clean_cell(r.get("NO UNIT", ""))
        if no_unit.isdigit():
            unit_sub = (
                f"(SELECT id_inventory_unit FROM inventory_unit WHERE no_unit = {int(no_unit)} LIMIT 1)"
            )
        elif no_unit:
            unit_sub = (
                f"(SELECT id_inventory_unit FROM inventory_unit WHERE CAST(no_unit AS CHAR) = {sql_str(no_unit)} LIMIT 1)"
            )
        else:
            unit_sub = "NULL"

        lokasi = clean_cell(r.get("LOKASI", ""))
        storage_sql = sql_str(lokasi[:255]) if lokasi else "NULL"

        recv, recv_note = choose_received_at(r)
        recv_sql = sql_str(recv) if recv else "NULL"

        note_parts = []
        if raw_desc:
            note_parts.append(f"Charger Raw: {raw_desc}")
        if recv_note:
            note_parts.append(recv_note)
        notes_sql = sql_str("; ".join(note_parts)) if note_parts else "NULL"

        prepared.append(
            {
                "item_number": item,
                "merk": merk,
                "tipe": tipe,
                "serial_sql": sql_str(serial) if serial else "NULL",
                "in_v_sql": sql_str(in_v) if in_v else "NULL",
                "out_v_sql": sql_str(out_v) if out_v else "NULL",
                "out_a_sql": sql_str(out_a) if out_a else "NULL",
                "status": status,
                "unit_sub": unit_sub,
                "storage_sql": storage_sql,
                "recv_sql": recv_sql,
                "notes_sql": notes_sql,
            }
        )

    master_lines = [
        "-- Generated by build_charger_import_sql.py",
        "SET NAMES utf8mb4;",
        "SET FOREIGN_KEY_CHECKS=0;",
        "",
    ]
    for merk, tipe in sorted(master_keys):
        m, t = sql_esc(merk), sql_esc(tipe)
        master_lines.append(
            "INSERT INTO charger (merk_charger, tipe_charger)\n"
            f"SELECT '{m}', '{t}'\n"
            "WHERE NOT EXISTS (\n"
            "  SELECT 1 FROM charger c\n"
            f"  WHERE c.merk_charger = '{m}' AND c.tipe_charger = '{t}'\n"
            ");"
        )
    master_lines.extend(["", "SET FOREIGN_KEY_CHECKS=1;", ""])

    inv_lines = [
        "-- Generated by build_charger_import_sql.py",
        "-- GANTI SELURUH inventory_chargers dengan data CSV.",
        "-- Jalankan import_charger_master.sql terlebih dahulu.",
        "",
        "SET NAMES utf8mb4;",
        "SET FOREIGN_KEY_CHECKS=0;",
        "",
        "START TRANSACTION;",
        "",
        "-- Opsional bila tabel tersedia di DB Anda:",
        "-- DELETE FROM component_timeline WHERE component_type = 'CHARGER';",
        "-- DELETE FROM unit_movements WHERE component_type = 'CHARGER';",
        "-- UPDATE spk_unit_stages SET charger_inventory_attachment_id = NULL WHERE charger_inventory_attachment_id IS NOT NULL;",
        "",
        "DELETE FROM inventory_chargers;",
        "",
    ]

    for p in prepared:
        m, t = sql_esc(p["merk"]), sql_esc(p["tipe"])
        inv_lines.append(
            "INSERT INTO inventory_chargers (\n"
            "  item_number, charger_type_id, serial_number, input_voltage, output_voltage, output_ampere,\n"
            "  purchase_order_id, inventory_unit_id, physical_condition, completeness, physical_notes,\n"
            "  storage_location, warehouse_location_id, status, received_at, notes\n"
            ") SELECT\n"
            f"  {sql_str(p['item_number'])},\n"
            "  (SELECT id_charger FROM charger c WHERE c.merk_charger = '"
            + m
            + "' AND c.tipe_charger = '"
            + t
            + "' LIMIT 1),\n"
            f"  {p['serial_sql']},\n"
            f"  {p['in_v_sql']},\n"
            f"  {p['out_v_sql']},\n"
            f"  {p['out_a_sql']},\n"
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
            "WHERE (SELECT id_charger FROM charger c WHERE c.merk_charger = '"
            + m
            + "' AND c.tipe_charger = '"
            + t
            + "' LIMIT 1) IS NOT NULL;"
        )

    inv_lines.extend(["", "COMMIT;", "SET FOREIGN_KEY_CHECKS=1;", ""])

    (base / OUT_MASTER).write_text("\n".join(master_lines), encoding="utf-8")
    (base / OUT_INV).write_text("\n".join(inv_lines), encoding="utf-8")

    st = Counter(p["status"] for p in prepared)
    with_sn = sum(1 for p in prepared if p["serial_sql"] != "NULL")
    with_unit = sum(1 for p in prepared if p["unit_sub"] != "NULL")

    readme = [
        "Impor / reset data charger dari CSV",
        "===============================",
        f"Sumber: {CSV_NAME}",
        f"Total baris: {len(prepared)}",
        f"Master unik (merk+tipe): {len(master_keys)}",
        f"Baris dengan serial terdeteksi: {with_sn}",
        f"Baris dengan unit terdeteksi: {with_unit}",
        "",
        "Urutan eksekusi:",
        "  1) import_charger_master.sql",
        "  2) import_inventory_chargers.sql",
        "",
        "Mapping status:",
    ]
    for k, v in STATUS_MAP.items():
        readme.append(f"  {k} -> {v}")
    readme.append("")
    readme.append("Distribusi status hasil:")
    for k, v in st.most_common():
        readme.append(f"  {k}: {v}")
    readme.extend(
        [
            "",
            "Catatan:",
            "- `CHARGER ` mentah diparse menjadi merk, tipe, serial, voltage, ampere.",
            "- Data sulit diparse tetap disimpan di kolom notes (Charger Raw).",
            "- Format NO CHARGER non-standar dinormalisasi + dicatat di warning.",
            "",
            "Warning (maks 250):",
        ]
    )
    readme.extend(issues[:250])
    if len(issues) > 250:
        readme.append(f"... dan {len(issues) - 250} warning lainnya")

    (base / OUT_README).write_text("\n".join(readme), encoding="utf-8")

    print(f"Wrote {base / OUT_MASTER}")
    print(f"Wrote {base / OUT_INV}")
    print(f"Wrote {base / OUT_README}")
    print(f"Rows={len(prepared)} master={len(master_keys)} warnings={len(issues)}")


if __name__ == "__main__":
    main()
