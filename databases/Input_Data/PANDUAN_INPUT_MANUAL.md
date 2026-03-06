# PANDUAN INPUT KONTRAK_UNIT MANUAL VIA SPREADSHEET

## 1. STRUKTUR SPREADSHEET

### Kolom yang WAJIB ada (minimal):
```
kontrak_id | unit_id | tanggal_mulai | status
```

### Kolom LENGKAP (recommended):
```
kontrak_id | unit_id | harga_sewa | is_spare | tanggal_mulai | tanggal_selesai | status
```

### Format Detail:

| Kolom | Tipe | Format | Wajib | Contoh | Keterangan |
|-------|------|--------|-------|--------|------------|
| kontrak_id | Integer | Angka | ✓ | 1 | ID dari tabel kontrak (lihat referensi) |
| unit_id | Integer | Angka | ✓ | 2722 | ID unit dari inventory_unit |
| harga_sewa | Decimal | Angka (tanpa Rp, tanpa titik) | - | 7150000 | Harga sewa per bulan |
| is_spare | Boolean | 0 atau 1 | - | 0 | 1 = unit spare, 0 = unit utama |
| tanggal_mulai | Date | DD/MM/YYYY | ✓ | 01/07/2021 | Tanggal mulai kontrak unit |
| tanggal_selesai | Date | DD/MM/YYYY | - | 30/06/2026 | Tanggal selesai (bisa kosong) |
| status | Enum | Text | ✓ | ACTIVE | ACTIVE, PULLED, REPLACED, INACTIVE, dll |

### Status yang Valid:
- ACTIVE
- PULLED
- REPLACED
- INACTIVE
- MAINTENANCE
- UNDER_REPAIR
- TEMP_REPLACED
- TEMP_ACTIVE
- TEMP_ENDED

## 2. CARA MENDAPATKAN DATA REFERENSI

### A. Kontrak ID
Gunakan helper script untuk lihat semua kontrak:

```bash
php c:\laragon\www\optima\databases\Input_Data\helper_kontrak_list.php
```

Output akan menampilkan:
```
id=1    | no=193/LGEIN/EESH/IX-21/2020     | customer=PT ABC | location=Plant A
id=2    | no=426/SML/VII/2021             | customer=PT XYZ | location=Cabang B
...
```

Atau filter by customer:
```bash
php helper_kontrak_list.php "ABC"
```

### B. Unit ID
Gunakan script untuk lihat unit yang tersedia:

```bash
php c:\laragon\www\optima\databases\Input_Data\helper_unit_list.php
```

Output:
```
id=2722 | no_unit=2611 | merk=CAT | model=DP25ND | serial=CT18C-86978 | AVAILABLE
id=2892 | no_unit=2612 | merk=CAT | model=DP25ND | serial=CT18C-86979 | AVAILABLE
...
```

Filter by merk/model:
```bash
php helper_unit_list.php "CAT"
```

### C. Customer ID & Location ID
```bash
php c:\laragon\www\optima\databases\Input_Data\helper_customer_list.php
```

Output:
```
customer_id=1 | PT ABC Company | locations: Plant A (id=1), Cabang B (id=2)
customer_id=2 | PT XYZ Corp     | locations: Warehouse C (id=3)
...
```

## 3. TEMPLATE SPREADSHEET

Saya sudah buatkan template Excel/Google Sheets:

**File:** `kontrak_unit_template.csv`

Header:
```csv
kontrak_id;unit_id;harga_sewa;is_spare;tanggal_mulai;tanggal_selesai;status
```

Contoh isi:
```csv
1;2722;7150000;0;01/07/2021;30/06/2026;ACTIVE
1;2892;7150000;0;01/07/2021;30/06/2026;ACTIVE
1;2889;7150000;0;01/07/2021;30/06/2026;ACTIVE
```

## 4. LANGKAH-LANGKAH INPUT

### Step 1: Siapkan Data
1. Copy template `kontrak_unit_template.csv`
2. Buka di Excel/LibreOffice/Google Sheets
3. Set delimiter = **semicolon (;)**

### Step 2: Cari Kontrak ID
```bash
# Cari kontrak by nomor
php helper_kontrak_list.php "426/SML"

# Output:
# id=11 | no=426/SML/VII/2021 | customer=PT Sarana | ...
```

Copy ID = **11**

### Step 3: Cari Unit ID
```bash
# Cari unit by nomor unit
php helper_unit_list.php "2611"

# Output:
# id=2722 | no_unit=2611 | merk=CAT | ...
```

Copy ID = **2722**

### Step 4: Isi Row
```
11;2722;7150000;0;01/07/2021;30/06/2026;ACTIVE
```

### Step 5: Repeat untuk semua unit

### Step 6: Save as CSV
- **Delimiter:** Semicolon (;)
- **Encoding:** UTF-8
- **Filename:** `kontrak_unit_manual.csv`

## 5. VALIDASI SEBELUM IMPORT

Gunakan script validasi:

```bash
php c:\laragon\www\optima\databases\Input_Data\validate_kontrak_unit.php kontrak_unit_manual.csv
```

Script akan cek:
- ✓ Semua kontrak_id exist di database
- ✓ Semua unit_id exist di inventory_unit
- ✓ Format tanggal valid
- ✓ Status valid
- ✓ Tidak ada duplikasi unit (satu unit tidak bisa di 2 kontrak aktif)
- ✓ harga_sewa numeric
- ⚠️ Warning jika ada anomali

## 6. IMPORT KE DATABASE

Setelah validasi OK:

```bash
php c:\laragon\www\optima\databases\Input_Data\import_kontrak_unit.php kontrak_unit_manual.csv
```

Script akan:
1. Backup kontrak_unit existing
2. **Append** (tidak trucate) - data baru ditambahkan
3. Skip duplikat
4. Report hasil

## 7. TIPS & BEST PRACTICES

### Group by Kontrak
Organizer data per kontrak di spreadsheet:

```csv
# === Kontrak 1: PT ABC - 193/LGEIN/EESH/IX-21/2020 ===
1;2722;7150000;0;01/07/2021;30/06/2026;ACTIVE
1;2892;7150000;0;01/07/2021;30/06/2026;ACTIVE

# === Kontrak 2: PT XYZ - 426/SML/VII/2021 ===
11;2611;7200000;0;13/08/2021;12/08/2023;EXPIRED
11;2612;7200000;0;13/08/2021;12/08/2023;EXPIRED
```

### Freeze Header
Freeze row 1 di Excel supaya header selalu terlihat saat scroll.

### Data Validation di Excel
Set dropdown untuk kolom:
- **status:** ACTIVE, EXPIRED, PULLED, dll
- **is_spare:** 0, 1

### Batch Processing
Input 50-100 rows per batch, import, validasi di aplikasi, lanjut batch berikutnya.

### Backup
Sebelum import batch besar, backup database dulu:
```sql
CREATE TABLE kontrak_unit_backup_before_manual_input 
AS SELECT * FROM kontrak_unit;
```

## 8. TROUBLESHOOTING

### Error: "kontrak_id not found"
Kontrak ID tidak exist di database. Cek dengan:
```bash
php helper_kontrak_list.php
```

### Error: "unit_id not found"
Unit ID tidak exist di inventory_unit. Cek dengan:
```bash
php helper_unit_list.php
```

### Error: "Duplicate unit"
Unit sudah dipakai di kontrak lain (status ACTIVE). Pilih:
1. Tarik unit dari kontrak lama (update status = PULLED)
2. Gunakan unit spare
3. Gunakan unit replacement

### Error: "Invalid date format"
Format harus: **DD/MM/YYYY**
Contoh valid: 01/07/2021
Contoh invalid: 2021-07-01, 1/7/21, 01-07-2021

## 9. HELPER SCRIPTS YANG TERSEDIA

| Script | Fungsi |
|--------|--------|
| helper_kontrak_list.php | List semua kontrak dengan filter |
| helper_unit_list.php | List unit available dengan filter |
| helper_customer_list.php | List customer & locations |
| validate_kontrak_unit.php | Validasi CSV sebelum import |
| import_kontrak_unit_append.php | Import dengan append mode |

## 10. CONTOH WORKFLOW LENGKAP

```bash
# 1. Cari kontrak PT ABC Kogen
php helper_kontrak_list.php "ABC Kogen"
# id=1 | no=193/LGEIN/EESH/IX-21/2020 | ...

# 2. Cari unit CAT yang available
php helper_unit_list.php "CAT" "AVAILABLE"
# id=2722 | no_unit=2611 | ...
# id=2892 | no_unit=2612 | ...

# 3. Buka spreadsheet, isi:
# kontrak_id;unit_id;harga_sewa;is_spare;tanggal_mulai;tanggal_selesai;status
# 1;2722;7150000;0;01/07/2021;30/06/2026;ACTIVE
# 1;2892;7150000;0;01/07/2021;30/06/2026;ACTIVE

# 4. Save as kontrak_unit_abc.csv

# 5. Validasi
php validate_kontrak_unit.php kontrak_unit_abc.csv
# ✓ All checks passed

# 6. Import
php import_kontrak_unit_append.php kontrak_unit_abc.csv
# Imported: 2 rows

# 7. Verify di aplikasi web
# http://localhost/optima/public/marketing/kontrak
```

---

**READY?** Saya siapkan helper scripts-nya sekarang!
