#!/usr/bin/env python3
"""
Optima: sparepart_used.csv -> work_order_spareparts (+ optional work_order_sparepart_usage).

Usage (generate SQL only — needs sparepart kode list):
  mysql -u root -N optima_ci -e "SELECT kode FROM sparepart" > sparepart_kodes.txt
  python generate_sparepart_usage_import.py --codes-file sparepart_kodes.txt

Execute directly (loads kodes + WO map from DB):
  python generate_sparepart_usage_import.py --execute \\
    --mysql-host 127.0.0.1 --mysql-user root --mysql-database optima_ci

work_order_sparepart_usage: quantity_used is INT in schema; fractional CSV qty uses
int(round(qty)) with insert skipped when result is 0 (source of truth stays wosp.quantity_used).

HM column is ignored. Unknown / empty sparepart code -> BEKAS, is_from_warehouse=0.

See header comment in generated .sql for --skip-existing vs UPDATE work_orders caveat.
"""

from __future__ import annotations

import argparse
import csv
import math
import os
import sys
from datetime import datetime
from typing import Any, Optional, Set, Tuple

INPUT_DEFAULT = "sparepart_used.csv"
OUTPUT_DEFAULT = "import_sparepart_usage.sql"
CODES_FILE_DEFAULT = "sparepart_kodes.txt"
PLACEHOLDER_NO_CODE = "IMPORT-NOCODE"


def sql_str(val: Any) -> str:
    if val is None or str(val).strip() == "":
        return "NULL"
    v = str(val)
    v = v.replace("\\", "\\\\")
    v = v.replace("'", "\\'")
    v = v.replace("\r\n", " ").replace("\r", " ").replace("\n", " ")
    return "'" + v + "'"


def parse_qty(raw: str) -> Optional[float]:
    s = (raw or "").strip().replace(",", ".")
    if not s:
        return None
    try:
        q = float(s)
        return q
    except ValueError:
        return None


def normalize_satuan(s: str) -> str:
    u = (s or "").strip().upper()
    if u == "PC":
        return "PCS"
    return (s or "").strip() or "PCS"


def load_kodes_from_file(path: str) -> Set[str]:
    codes: Set[str] = set()
    with open(path, "r", encoding="utf-8", errors="replace") as f:
        for line in f:
            k = line.strip()
            if k:
                codes.add(k)
                codes.add(k.upper())
    return codes


def code_in_master(codes: Set[str], code: str) -> bool:
    c = (code or "").strip()
    if not c:
        return False
    return c in codes or c.upper() in codes


def build_notes(perbaikan: str, mekanik: str) -> str:
    parts = []
    if perbaikan.strip():
        parts.append("Perbaikan: " + perbaikan.strip()[:400])
    if mekanik.strip():
        parts.append("Mekanik: " + mekanik.strip()[:200])
    if not parts:
        return ""
    return "[CSV import] " + " | ".join(parts)


def row_values(
    codes: Set[str],
    perbaikan: str,
    mekanik: str,
    kode: str,
    nama: str,
    qty: float,
    satuan: str,
) -> Tuple[str, str, str, int, str, str, int, str, float]:
    """Returns (code, name, item_type, qty_brought, satuan, notes, is_from_wh, source_type, qty_used)."""
    nama = (nama or "").strip() or "-"
    kode_stripped = (kode or "").strip()
    in_master = code_in_master(codes, kode_stripped)
    if in_master:
        sparepart_code = kode_stripped
        source_type = "WAREHOUSE"
        is_from_wh = 1
    else:
        sparepart_code = PLACEHOLDER_NO_CODE if not kode_stripped else kode_stripped
        source_type = "BEKAS"
        is_from_wh = 0
    item_type = "sparepart"
    qty_brought = max(1, int(math.ceil(qty))) if qty > 0 else 0
    notes = build_notes(perbaikan, mekanik)
    sat = normalize_satuan(satuan)
    return (
        sparepart_code,
        nama,
        item_type,
        qty_brought,
        sat,
        notes,
        is_from_wh,
        source_type,
        qty,
    )


def emit_insert_sql(
    out,
    wo_number: str,
    sparepart_code: str,
    sparepart_name: str,
    item_type: str,
    quantity_brought: int,
    satuan: str,
    notes: str,
    is_from_warehouse: int,
    source_type: str,
    quantity_used: float,
    skip_existing: bool,
    legacy_schema: bool,
) -> None:
    notes_sql = sql_str(notes) if notes else "NULL"
    skip_clause = ""
    if skip_existing:
        skip_clause = (
            " AND NOT EXISTS (SELECT 1 FROM `work_order_spareparts` e "
            "WHERE e.`work_order_id` = wo.`id`)"
        )
    if legacy_schema:
        # DB tanpa kolom source_type / source_unit_id / source_notes (gunakan is_from_warehouse saja)
        out.write(
            "INSERT INTO `work_order_spareparts` ("
            "`work_order_id`,`sparepart_code`,`sparepart_name`,`item_type`,"
            "`quantity_brought`,`satuan`,`notes`,`created_at`,`updated_at`,"
            "`quantity_used`,`is_additional`,`is_from_warehouse`,"
            "`sparepart_validated`,"
            "`quantity_returned`,`return_status`,`return_confirmed_by`,`return_confirmed_at`"
            ")\n"
            "SELECT wo.`id`,"
            f"{sql_str(sparepart_code)},{sql_str(sparepart_name)},{sql_str(item_type)},"
            f"{int(quantity_brought)},{sql_str(satuan)},{notes_sql},"
            "NOW(),NOW(),"
            f"{quantity_used:.2f},"
            "0,"
            f"{int(is_from_warehouse)},"
            "1,"
            "0,'NONE',NULL,NULL\n"
            "FROM `work_orders` wo\n"
            f"WHERE wo.`work_order_number` = {sql_str(wo_number)} "
            "AND wo.`deleted_at` IS NULL"
            f"{skip_clause}\n"
            "LIMIT 1;\n"
        )
        return
    out.write(
        "INSERT INTO `work_order_spareparts` ("
        "`work_order_id`,`sparepart_code`,`sparepart_name`,`item_type`,"
        "`quantity_brought`,`satuan`,`notes`,`created_at`,`updated_at`,"
        "`quantity_used`,`is_additional`,`is_from_warehouse`,`source_type`,"
        "`source_unit_id`,`source_notes`,`sparepart_validated`,"
        "`quantity_returned`,`return_status`,`return_confirmed_by`,`return_confirmed_at`"
        ")\n"
        "SELECT wo.`id`,"
        f"{sql_str(sparepart_code)},{sql_str(sparepart_name)},{sql_str(item_type)},"
        f"{int(quantity_brought)},{sql_str(satuan)},{notes_sql},"
        "NOW(),NOW(),"
        f"{quantity_used:.2f},"
        "0,"
        f"{int(is_from_warehouse)},{sql_str(source_type)},"
        "NULL,NULL,"
        "1,"
        "0,'NONE',NULL,NULL\n"
        "FROM `work_orders` wo\n"
        f"WHERE wo.`work_order_number` = {sql_str(wo_number)} "
        "AND wo.`deleted_at` IS NULL"
        f"{skip_clause}\n"
        "LIMIT 1;\n"
    )


def try_pymysql():
    try:
        import pymysql  # type: ignore

        return pymysql
    except ImportError:
        return None


def main() -> int:
    base = os.path.dirname(os.path.abspath(__file__))
    ap = argparse.ArgumentParser(description="Import sparepart_used.csv into work_order_spareparts")
    ap.add_argument("--input", default=os.path.join(base, INPUT_DEFAULT))
    ap.add_argument("--output", default=os.path.join(base, OUTPUT_DEFAULT))
    ap.add_argument("--codes-file", default=os.path.join(base, CODES_FILE_DEFAULT),
                    help="Text file: one sparepart.kode per line (from MySQL export)")
    ap.add_argument("--dry-run", action="store_true", help="Parse only; no output file / no DB writes")
    ap.add_argument("--skip-existing", action="store_true",
                    help="Skip WOs that already have any work_order_spareparts row. "
                         "If set with SQL output, distinct-WO UPDATE block is omitted (see .sql header).")
    ap.add_argument("--execute", action="store_true", help="Run inserts via PyMySQL (requires pymysql)")
    ap.add_argument("--mysql-host", default="127.0.0.1")
    ap.add_argument("--mysql-port", type=int, default=3306)
    ap.add_argument("--mysql-user", default="root")
    ap.add_argument("--mysql-password", default="")
    ap.add_argument("--mysql-database", default="optima_ci")
    ap.add_argument("--with-usage-table", action="store_true",
                    help="Also insert work_order_sparepart_usage (INT qty = round(qty), skip if 0)")
    ap.add_argument("--batch-commit", type=int, default=500, help="Commit every N rows (--execute)")
    ap.add_argument(
        "--legacy-schema",
        action="store_true",
        help="Omit source_type, source_unit_id, source_notes (for DB without migration 20260314_add_sparepart_source_tracking)",
    )
    args = ap.parse_args()

    pymysql = try_pymysql() if args.execute else None
    if args.execute and pymysql is None:
        print("ERROR: pymysql required for --execute. pip install pymysql", file=sys.stderr)
        return 1

    codes: Set[str] = set()
    if args.dry_run:
        pass  # classify as BEKAS unless --codes-file provided
    elif args.execute:
        conn = pymysql.connect(
            host=args.mysql_host,
            port=args.mysql_port,
            user=args.mysql_user,
            password=args.mysql_password,
            database=args.mysql_database,
            charset="utf8mb4",
            cursorclass=pymysql.cursors.DictCursor,
        )
        try:
            with conn.cursor() as cur:
                cur.execute("SELECT `kode` FROM `sparepart`")
                for row in cur.fetchall():
                    k = (row.get("kode") or "").strip()
                    if k:
                        codes.add(k)
                        codes.add(k.upper())
        finally:
            conn.close()
    else:
        if not os.path.isfile(args.codes_file):
            print(
                f"ERROR: codes file not found: {args.codes_file}\n"
                f"Export with: mysql -u USER -N {args.mysql_database} -e \"SELECT kode FROM sparepart\" > {args.codes_file}",
                file=sys.stderr,
            )
            return 1
        codes = load_kodes_from_file(args.codes_file)

    if args.dry_run and os.path.isfile(args.codes_file):
        codes = load_kodes_from_file(args.codes_file)

    if not os.path.isfile(args.input):
        print(f"ERROR: input CSV not found: {args.input}", file=sys.stderr)
        return 1

    distinct_wo: Set[str] = set()
    stats = {
        "rows": 0,
        "skipped_empty": 0,
        "skipped_qty": 0,
        "skipped_no_name": 0,
        "skipped_missing_wo": 0,
        "skipped_existing_wo": 0,
        "inserts": 0,
    }

    pending_rows: list[Tuple] = []

    with open(args.input, "r", encoding="utf-8-sig", newline="") as f:
        reader = csv.reader(f, delimiter=";", quotechar='"')
        header = next(reader, None)
        if not header:
            print("ERROR: empty CSV", file=sys.stderr)
            return 1
        for row in reader:
            stats["rows"] += 1
            while len(row) < 11:
                row.append("")
            wo_num = row[0].strip()
            perbaikan = row[3].strip()
            mekanik = row[6].strip()
            kode = row[7].strip()
            nama = row[8].strip()
            qty_raw = row[9]
            satuan = row[10].strip()

            if not wo_num:
                stats["skipped_empty"] += 1
                continue
            if not nama:
                stats["skipped_no_name"] += 1
                continue
            qty = parse_qty(qty_raw)
            if qty is None or qty <= 0:
                stats["skipped_qty"] += 1
                continue

            distinct_wo.add(wo_num)
            vals = row_values(codes, perbaikan, mekanik, kode, nama, qty, satuan)
            pending_rows.append((wo_num, vals))

    if args.dry_run:
        print(
            f"Dry run: parsed rows={stats['rows']}, pending inserts={len(pending_rows)}, "
            f"distinct WO={len(distinct_wo)}, skip empty={stats['skipped_empty']}, "
            f"skip qty={stats['skipped_qty']}, skip no name={stats['skipped_no_name']}"
        )
        return 0

    if not pending_rows:
        print("No rows to import after filtering.", file=sys.stderr)
        return 1

    if args.execute:
        return run_execute(
            pymysql,
            args,
            pending_rows,
            stats,
            distinct_wo,
        )

    out_path = args.output
    with open(out_path, "w", encoding="utf-8") as out:
        w = out.write
        w("-- " + "=" * 64 + "\n")
        w("-- Sparepart usage import from CSV\n")
        w(f"-- Generated: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}\n")
        w(f"-- Source: {os.path.basename(args.input)}\n")
        w("-- HM column ignored. Unknown code -> BEKAS.\n")
        if args.legacy_schema:
            w("-- legacy-schema: no source_type / source_unit_id / source_notes (BEKAS = is_from_warehouse=0).\n")
        if args.skip_existing:
            w("-- skip-existing: each INSERT uses NOT EXISTS on work_order_spareparts.\n")
            w("-- Omitted: bulk UPDATE work_orders.sparepart_validated (run --execute or update manually).\n")
        else:
            w("-- After import, work_orders.sparepart_validated is set for distinct WO in this file.\n")
        w("-- work_order_sparepart_usage: use --execute --with-usage-table, or migrate INT->DECIMAL.\n")
        w("-- " + "=" * 64 + "\n\n")
        w("SET NAMES utf8mb4;\n")
        w("SET foreign_key_checks = 0;\n\n")

        for wo_num, vals in pending_rows:
            (
                sparepart_code,
                sparepart_name,
                item_type,
                quantity_brought,
                satuan,
                notes,
                is_from_wh,
                source_type,
                quantity_used,
            ) = vals
            emit_insert_sql(
                out,
                wo_num,
                sparepart_code,
                sparepart_name,
                item_type,
                quantity_brought,
                satuan,
                notes,
                is_from_wh,
                source_type,
                quantity_used,
                args.skip_existing,
                args.legacy_schema,
            )
            stats["inserts"] += 1

        w("\nSET foreign_key_checks = 1;\n\n")

        if not args.skip_existing and distinct_wo:
            nums = sorted(distinct_wo, key=lambda x: (len(x), x))
            in_list = ",".join(sql_str(n) for n in nums)
            w(
                "-- Activate grouped tab on sparepart-usage for these work orders\n"
                "UPDATE `work_orders` SET "
                "`sparepart_validated` = 1, "
                "`sparepart_validated_at` = NOW() "
                f"WHERE `work_order_number` IN ({in_list}) "
                "AND `deleted_at` IS NULL;\n"
            )

    print(
        f"Wrote {out_path}: statements={stats['inserts']}, distinct WO={len(distinct_wo)} "
        f"(skipped_qty={stats['skipped_qty']}, skipped_no_name={stats['skipped_no_name']})"
    )
    return 0


def run_execute(
    pymysql: Any,
    args: argparse.Namespace,
    pending_rows: list,
    stats: dict,
    distinct_wo: Set[str],
) -> int:
    conn = pymysql.connect(
        host=args.mysql_host,
        port=args.mysql_port,
        user=args.mysql_user,
        password=args.mysql_password,
        database=args.mysql_database,
        charset="utf8mb4",
        cursorclass=pymysql.cursors.DictCursor,
    )
    wo_ids_touched: Set[int] = set()
    wo_ids_skip_existing: Set[int] = set()
    try:
        conn.autocommit(False)
        with conn.cursor() as cur:
            cur.execute(
                "SELECT `id`, `work_order_number` FROM `work_orders` WHERE `deleted_at` IS NULL"
            )
            wo_map = {str(r["work_order_number"]).strip(): int(r["id"]) for r in cur.fetchall()}

            if args.skip_existing:
                cur.execute("SELECT DISTINCT `work_order_id` FROM `work_order_spareparts`")
                wo_ids_skip_existing = {int(r["work_order_id"]) for r in cur.fetchall()}
        conn.commit()

        n = 0
        conn.begin()
        try:
            with conn.cursor() as cur:
                for wo_num, vals in pending_rows:
                    wo_id = wo_map.get(wo_num)
                    if not wo_id:
                        stats["skipped_missing_wo"] += 1
                        continue
                    if args.skip_existing and wo_id in wo_ids_skip_existing:
                        stats["skipped_existing_wo"] += 1
                        continue

                    (
                        sparepart_code,
                        sparepart_name,
                        item_type,
                        quantity_brought,
                        satuan,
                        notes,
                        is_from_wh,
                        source_type,
                        quantity_used,
                    ) = vals

                    if args.legacy_schema:
                        sql = (
                            "INSERT INTO `work_order_spareparts` ("
                            "`work_order_id`,`sparepart_code`,`sparepart_name`,`item_type`,"
                            "`quantity_brought`,`satuan`,`notes`,`created_at`,`updated_at`,"
                            "`quantity_used`,`is_additional`,`is_from_warehouse`,"
                            "`sparepart_validated`,"
                            "`quantity_returned`,`return_status`,`return_confirmed_by`,`return_confirmed_at`"
                            ") VALUES ("
                            "%s,%s,%s,%s,%s,%s,%s,NOW(),NOW(),%s,0,%s,1,0,'NONE',NULL,NULL)"
                        )
                        cur.execute(
                            sql,
                            (
                                wo_id,
                                sparepart_code,
                                sparepart_name,
                                item_type,
                                quantity_brought,
                                satuan,
                                notes or None,
                                quantity_used,
                                is_from_wh,
                            ),
                        )
                    else:
                        sql = (
                            "INSERT INTO `work_order_spareparts` ("
                            "`work_order_id`,`sparepart_code`,`sparepart_name`,`item_type`,"
                            "`quantity_brought`,`satuan`,`notes`,`created_at`,`updated_at`,"
                            "`quantity_used`,`is_additional`,`is_from_warehouse`,`source_type`,"
                            "`source_unit_id`,`source_notes`,`sparepart_validated`,"
                            "`quantity_returned`,`return_status`,`return_confirmed_by`,`return_confirmed_at`"
                            ") VALUES ("
                            "%s,%s,%s,%s,%s,%s,%s,NOW(),NOW(),%s,0,%s,%s,NULL,NULL,1,0,'NONE',NULL,NULL)"
                        )
                        cur.execute(
                            sql,
                            (
                                wo_id,
                                sparepart_code,
                                sparepart_name,
                                item_type,
                                quantity_brought,
                                satuan,
                                notes or None,
                                quantity_used,
                                is_from_wh,
                                source_type,
                            ),
                        )
                    new_id = cur.lastrowid
                    stats["inserts"] += 1
                    wo_ids_touched.add(wo_id)

                    if args.with_usage_table and new_id:
                        uqty = int(round(float(quantity_used)))
                        if uqty > 0:
                            cur.execute(
                                "INSERT INTO `work_order_sparepart_usage` ("
                                "`work_order_sparepart_id`,`work_order_id`,`quantity_used`,"
                                "`quantity_returned`,`usage_notes`,`used_at`,`created_at`,`updated_at`"
                                ") VALUES (%s,%s,%s,0,%s,NOW(),NOW(),NOW())",
                                (
                                    new_id,
                                    wo_id,
                                    uqty,
                                    notes or None,
                                ),
                            )

                    n += 1
                    if n % args.batch_commit == 0:
                        conn.commit()
                        conn.begin()

                if wo_ids_touched:
                    placeholders = ",".join(["%s"] * len(wo_ids_touched))
                    cur.execute(
                        f"UPDATE `work_orders` SET `sparepart_validated` = 1, "
                        f"`sparepart_validated_at` = NOW() WHERE `id` IN ({placeholders})",
                        tuple(wo_ids_touched),
                    )
            conn.commit()
        except Exception:
            conn.rollback()
            raise
    finally:
        conn.close()

    print(
        f"Execute OK: inserts={stats['inserts']}, WO sparepart_validated updated={len(wo_ids_touched)}, "
        f"with_usage_table={args.with_usage_table}, "
        f"skipped_missing_wo_rows={stats['skipped_missing_wo']}, "
        f"skipped_existing_wo_rows={stats['skipped_existing_wo']}"
    )
    return 0


if __name__ == "__main__":
    sys.exit(main())
