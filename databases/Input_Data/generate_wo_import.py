#!/usr/bin/env python3
"""
Optima Work Orders CSV -> SQL Import Generator
Jalankan : python generate_wo_import.py
Output   : import_workorders.sql (di folder yang sama)

Kolom notes berisi nama staff asli dari CSV:
  [Import] Admin: Novi | Foreman: YOGA | Mek/Help: KURNIA-BAGUS | Tipe Unit: Diesel
"""

import csv
import os
from datetime import datetime

INPUT_FILE  = 'workorders.csv'
OUTPUT_FILE = 'import_workorders.sql'

# Kategori (lowercase, token pertama sebelum koma) -> category_id
CATEGORY_MAP = {
    'attachments & accessories': 1,
    'braking / pengereman'     : 2,
    'chassis & body'           : 3,
    'engine / mesin'           : 4,
    'hidrolik'                 : 5,
    'kelistrikan'              : 6,
    'pengapian / bahan bakar'  : 7,
    'roda dan ban'             : 8,
    'safety'                   : 9,
    'transmisi'                : 10,
    'pelumas & fluida'         : 11,
}

VALID_ORDER_TYPES = {'COMPLAINT', 'PMPS', 'FABRIKASI', 'PERSIAPAN'}


def parse_date(s):
    """DD/MM/YYYY HH:MM  ->  'YYYY-MM-DD HH:MM:SS'  atau NULL"""
    s = (s or '').strip()
    if not s:
        return 'NULL'
    for fmt in ('%d/%m/%Y %H:%M', '%d/%m/%Y'):
        try:
            return "'" + datetime.strptime(s, fmt).strftime('%Y-%m-%d %H:%M:%S') + "'"
        except ValueError:
            pass
    return 'NULL'


def sql_str(val):
    """Escape string untuk MySQL, return NULL jika kosong."""
    if val is None or str(val).strip() == '':
        return 'NULL'
    v = str(val)
    v = v.replace('\\', '\\\\')
    v = v.replace("'", "\\'")
    v = v.replace('\r\n', ' ').replace('\r', ' ').replace('\n', ' ')
    return "'" + v + "'"


def get_category_id(cat_str):
    if not cat_str or not cat_str.strip():
        return 1  # default: Attachments & Accessories
    first = cat_str.split(',')[0].strip().lower()
    return CATEGORY_MAP.get(first, 1)


def main():
    base     = os.path.dirname(os.path.abspath(__file__))
    in_path  = os.path.join(base, INPUT_FILE)
    out_path = os.path.join(base, OUTPUT_FILE)

    if not os.path.exists(in_path):
        print(f'[ERROR] File tidak ditemukan: {in_path}')
        return

    inserted            = 0
    skipped_no_wo       = 0
    skipped_non_numeric = []
    total_rows          = 0

    with open(in_path, 'r', encoding='utf-8-sig', newline='') as f, \
         open(out_path, 'w', encoding='utf-8') as out:

        def w(line=''):
            out.write(line + '\n')

        w('-- ================================================================')
        w('-- Optima Work Orders Import')
        w(f'-- Generated : {datetime.now().strftime("%Y-%m-%d %H:%M:%S")}')
        w(f'-- Source    : {INPUT_FILE}')
        w('-- Strategy  : INSERT IGNORE + SELECT FROM inventory_unit')
        w('--             (baris dgn unit tidak ada di DB otomatis di-skip)')
        w('-- Notes col : menyimpan nama staff asli + tipe unit + sub kategori')
        w('-- ================================================================')
        w()
        w('SET NAMES utf8mb4;')
        w('SET foreign_key_checks = 0;')
        w()

        reader = csv.reader(f, delimiter=';', quotechar='"')

        # Skip header baris pertama
        next(reader)

        for row in reader:
            total_rows += 1

            # Pad ke minimal 17 kolom (jaga-jaga baris pendek)
            while len(row) < 17:
                row.append('')

            # Kolom CSV:
            # 0  NO WO
            # 1  Tanggal Pelaporan
            # 2  Nomor Unit
            # 3  Tipe Unit
            # 4  Tipe Order
            # 5  Request Waktu Perbaikan
            # 6  Kategori
            # 7  Sub Kategori
            # 8  Keluhan Unit & sparepart
            # 9  Status
            # 10 Admin
            # 11 Foreman
            # 12 Mek - help
            # 13 Perbaikan (nama pelaksana / deskripsi tambahan)
            # 14 Keterangan
            # 15 TTR
            # 16 Tanggal/Sparepart (SKIP - import terpisah)

            wo_number    = row[0].strip()
            report_date  = row[1].strip()
            unit_number  = row[2].strip()
            tipe_unit    = row[3].strip()
            order_type   = row[4].strip().upper()
            req_time     = row[5].strip()
            category_raw = row[6].strip()
            subcat_raw   = row[7].strip()
            complaint    = row[8].strip()
            admin_name   = row[10].strip()
            foreman_name = row[11].strip()
            mek_help     = row[12].strip()
            perbaikan    = row[13].strip()
            keterangan   = row[14].strip()
            ttr_raw      = row[15].strip()

            # Validasi WO number wajib ada
            if not wo_number:
                skipped_no_wo += 1
                continue

            # Skip unit non-numerik (B9076FCA, BL165, 05030DS7405, dsb)
            if not unit_number.isdigit():
                skipped_non_numeric.append(f'WO {wo_number}: unit="{unit_number}"')
                w(f'-- SKIP WO {wo_number}: unit non-numerik "{unit_number}"')
                continue

            # Normalisasi order_type
            if order_type not in VALID_ORDER_TYPES:
                order_type = 'COMPLAINT'

            # category_id (ambil kategori pertama jika ada koma)
            category_id = get_category_id(category_raw)

            # complaint_description (NOT NULL) — fallback chain
            if not complaint:
                complaint = keterangan or perbaikan or '-'

            # repair_description
            repair_desc = keterangan or ''

            # TTR decimal
            ttr_sql = 'NULL'
            if ttr_raw:
                try:
                    ttr_sql = str(float(ttr_raw))
                except ValueError:
                    pass

            # ---- Bangun notes: simpan semua info yang tidak masuk kolom DB ----
            parts = []
            if admin_name:   parts.append(f'Admin: {admin_name}')
            if foreman_name: parts.append(f'Foreman: {foreman_name}')
            if mek_help:     parts.append(f'Mek/Help: {mek_help}')
            if perbaikan:    parts.append(f'Pelaksana: {perbaikan}')
            if tipe_unit:    parts.append(f'Tipe Unit: {tipe_unit}')
            if subcat_raw:   parts.append(f'Sub Kategori: {subcat_raw}')
            # Simpan semua kategori jika ada lebih dari satu
            if ',' in category_raw:
                parts.append(f'Semua Kategori: {category_raw}')
            notes_val = '[Import] ' + ' | '.join(parts) if parts else ''

            # ---- Konversi tanggal ----
            report_sql = parse_date(report_date)
            req_sql    = parse_date(req_time)
            unit_int   = int(unit_number)

            # ---- Generate INSERT IGNORE ... SELECT ----
            # Menggunakan SELECT FROM inventory_unit sehingga:
            # - Jika no_unit ditemukan -> INSERT berhasil
            # - Jika tidak ditemukan   -> SELECT 0 rows -> INSERT IGNORE skip otomatis
            sql = (
                "INSERT IGNORE INTO `work_orders` "
                "(`work_order_number`,`report_date`,`unit_id`,`order_type`,`priority_id`,"
                "`requested_repair_time`,`category_id`,`subcategory_id`,`complaint_description`,"
                "`status_id`,`admin_id`,`foreman_id`,`mechanic_id`,`helper_id`,"
                "`repair_description`,`notes`,`time_to_repair`,`created_by`,`created_at`) "
                "SELECT "
                f"{sql_str(wo_number)},"
                f"{report_sql},"
                f"iu.`id_inventory_unit`,"
                f"{sql_str(order_type)},"
                f"3,"
                f"{req_sql},"
                f"{category_id},"
                f"NULL,"
                f"{sql_str(complaint)},"
                f"7,"
                f"NULL,NULL,NULL,NULL,"
                f"{sql_str(repair_desc)},"
                f"{sql_str(notes_val)},"
                f"{ttr_sql},"
                f"1,"
                f"{report_sql} "
                f"FROM `inventory_unit` iu "
                f"WHERE iu.`no_unit`={unit_int} "
                f"LIMIT 1;"
            )

            out.write(sql + '\n')
            inserted += 1

        # ---- Footer summary ----
        w()
        w('SET foreign_key_checks = 1;')
        w()
        w('-- ================================================================')
        w(f'-- SUMMARY')
        w(f'--   Total baris CSV diproses     : {total_rows}')
        w(f'--   INSERT generated             : {inserted}')
        w(f'--   Skipped (unit non-numerik)   : {len(skipped_non_numeric)}')
        if skipped_non_numeric:
            for s in skipped_non_numeric:
                w(f'--     {s}')
        w(f'--   Skipped (WO number kosong)   : {skipped_no_wo}')
        w('-- ================================================================')

    print()
    print(f'[OK] Output   : {out_path}')
    print(f'     INSERT    : {inserted} rows')
    print(f'     Skipped (unit non-numerik) : {len(skipped_non_numeric)}')
    if skipped_non_numeric:
        for s in skipped_non_numeric:
            print(f'       - {s}')
    print(f'     Skipped (WO kosong)        : {skipped_no_wo}')
    print()
    print('Langkah selanjutnya:')
    print('  Import file import_workorders.sql ke phpMyAdmin')
    print()


if __name__ == '__main__':
    main()
