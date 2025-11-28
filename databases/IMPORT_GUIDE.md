# 📘 PANDUAN IMPORT DATABASE - OPTIMA DB

**File:** `optima_ci_24-11-25_FINAL.sql`  
**Ukuran:** ~7,819 baris  
**Tabel:** 106 tabel  
**Views:** 27 views  
**Procedures:** 21 procedures

---

## ✅ FILE SUDAH DIPERSIAPKAN

File SQL sudah direorganize dan difinalisasi dengan:

1. ✅ **Urutan Tabel Benar** - Tabel dibuat berdasarkan dependensi foreign key
2. ✅ **Foreign Key Checks** - Dinonaktifkan saat CREATE, diaktifkan kembali setelah semua constraints ditambahkan
3. ✅ **Syntax Diperbaiki** - Semua view dan procedure sudah diperbaiki
4. ✅ **Line Endings** - CRLF (Windows compatible)
5. ✅ **Database Name** - `optima_ci` (bukan optima_ci_test)
6. ✅ **COMMIT** - Ada di akhir untuk memastikan transaksi selesai

---

## 🚀 CARA IMPORT

### **Metode 1: phpMyAdmin (Recommended untuk Windows)**

1. Buka phpMyAdmin di browser
2. Klik tab **"Import"**
3. Klik **"Choose File"** dan pilih `optima_ci_24-11-25_FINAL.sql`
4. Pastikan:
   - Format: **SQL**
   - Character set: **utf8mb4**
   - Partial import: **TIDAK** dicentang
5. Klik **"Go"** atau **"Import"**
6. Tunggu sampai selesai (bisa memakan waktu beberapa menit)

### **Metode 2: MySQL Command Line**

```bash
# Windows (Command Prompt)
mysql -u root -p < "C:\path\to\optima_ci_24-11-25_FINAL.sql"

# Atau dengan source command
mysql -u root -p
mysql> CREATE DATABASE IF NOT EXISTS optima_ci;
mysql> USE optima_ci;
mysql> source C:/path/to/optima_ci_24-11-25_FINAL.sql;
```

### **Metode 3: MySQL Workbench**

1. Buka MySQL Workbench
2. Connect ke server MySQL
3. File → Open SQL Script → Pilih `optima_ci_24-11-25_FINAL.sql`
4. Klik tombol **Execute** (⚡)
5. Tunggu sampai selesai

---

## ⚠️ PENTING SEBELUM IMPORT

### 1. **Backup Database yang Ada**
```sql
-- Backup database yang ada (jika ada)
mysqldump -u root -p optima_ci > backup_before_import.sql
```

### 2. **Cek Privileges User MySQL**
Pastikan user MySQL memiliki privileges:
- CREATE
- DROP
- ALTER
- INSERT
- SELECT
- REFERENCES (untuk foreign keys)

### 3. **Cek Space Disk**
Pastikan ada cukup space disk (file SQL cukup besar)

### 4. **Cek MySQL Version**
File ini dibuat untuk MariaDB 10.4.32, tapi seharusnya kompatibel dengan:
- MariaDB 10.3+
- MySQL 5.7+
- MySQL 8.0+

---

## 🔍 STRUKTUR FILE SQL

File diorganisir dengan urutan berikut:

```
1. Header & Configuration
   - SQL mode settings
   - Character set settings
   - Database creation

2. SET FOREIGN_KEY_CHECKS = 0
   (Menonaktifkan FK checks untuk CREATE TABLE)

3. TABLES (106 tabel)
   - Diurutkan berdasarkan dependensi
   - Parent tables dibuat dulu
   - Child tables dibuat setelah parent

4. VIEWS (27 views)
   - Semua views dibuat setelah semua tabel

5. PROCEDURES & FUNCTIONS (21 procedures)
   - Semua stored procedures dan functions

6. FOREIGN KEY CONSTRAINTS
   - Semua ALTER TABLE untuk menambahkan FK

7. SET FOREIGN_KEY_CHECKS = 1
   (Mengaktifkan kembali FK checks)

8. COMMIT
   (Menyelesaikan transaksi)
```

---

## 🐛 TROUBLESHOOTING

### **Error: "Unknown column"**
- ✅ **Sudah diperbaiki** - Semua kolom yang tidak ada sudah diperbaiki di views/procedures

### **Error: "Foreign key constraint fails"**
- ✅ **Sudah diperbaiki** - FOREIGN_KEY_CHECKS dinonaktifkan saat CREATE TABLE

### **Error: "Syntax error"**
- ✅ **Sudah diperbaiki** - Semua syntax error di views sudah diperbaiki

### **Error: "Table already exists"**
- File menggunakan `CREATE TABLE IF NOT EXISTS`, jadi aman
- Jika ingin replace, hapus database dulu:
  ```sql
  DROP DATABASE IF EXISTS optima_ci;
  CREATE DATABASE optima_ci;
  ```

### **Error: "Access denied"**
- Pastikan user MySQL memiliki privileges yang cukup
- Gunakan user `root` atau user dengan full privileges

### **Error: "File too large"**
- Jika import via phpMyAdmin, cek setting `upload_max_filesize` dan `post_max_size` di PHP
- Atau gunakan command line MySQL

---

## ✅ VERIFIKASI SETELAH IMPORT

Setelah import selesai, verifikasi dengan:

```sql
-- Cek jumlah tabel
SELECT COUNT(*) as total_tables 
FROM information_schema.tables 
WHERE table_schema = 'optima_ci';

-- Harusnya: 106 tabel

-- Cek jumlah views
SELECT COUNT(*) as total_views 
FROM information_schema.views 
WHERE table_schema = 'optima_ci';

-- Harusnya: 27 views

-- Cek foreign keys
SELECT COUNT(*) as total_fk 
FROM information_schema.key_column_usage 
WHERE table_schema = 'optima_ci' 
AND referenced_table_name IS NOT NULL;

-- Cek beberapa tabel penting
SELECT COUNT(*) FROM users;
SELECT COUNT(*) FROM kontrak;
SELECT COUNT(*) FROM inventory_unit;
SELECT COUNT(*) FROM spk;
```

---

## 📋 CHECKLIST SEBELUM IMPORT

- [ ] Backup database yang ada (jika ada)
- [ ] Cek space disk cukup
- [ ] Cek MySQL/MariaDB version kompatibel
- [ ] Cek user MySQL memiliki privileges cukup
- [ ] File `optima_ci_24-11-25_FINAL.sql` sudah ada
- [ ] Siap untuk import

---

## 🎯 SETELAH IMPORT BERHASIL

1. ✅ Verifikasi jumlah tabel, views, dan procedures
2. ✅ Test beberapa query penting
3. ✅ Test aplikasi untuk memastikan semua berfungsi
4. ✅ Hapus file backup lama jika tidak diperlukan

---

## 📞 SUPPORT

Jika ada masalah saat import:
1. Cek error message lengkap
2. Cek log MySQL/MariaDB
3. Pastikan semua prerequisites sudah terpenuhi
4. Coba import dengan metode lain (command line jika phpMyAdmin gagal)

---

**File siap untuk import!** 🚀


