# Panduan Migrasi Database ke Windows

Dokumen ini menjelaskan langkah-langkah untuk audit dan backup database sebelum migrasi ke Windows.

## 📋 Daftar Isi

1. [Audit Database](#audit-database)
2. [Backup Database](#backup-database)
3. [Restore di Windows](#restore-di-windows)
4. [Troubleshooting](#troubleshooting)

---

## 🔍 Audit Database

### Tujuan
Mengidentifikasi database mana yang aktif digunakan aplikasi dan mana yang tidak.

### Cara Menjalankan

```bash
cd /opt/lampp/htdocs/optima1
php scripts/database_audit.php
```

### Output
Script akan menghasilkan:
- **Text Report**: `scripts/database_audit_report_YYYY-MM-DD_HHMMSS.txt`
- **HTML Report**: `scripts/database_audit_report_YYYY-MM-DD_HHMMSS.html`
- **JSON Data**: `scripts/database_audit_result.json`

### Laporan Berisi
- ✅ Database Aktif: Database yang digunakan aplikasi
- ❌ Database Tidak Aktif: Database yang tidak digunakan
- Informasi detail: Jumlah tabel, ukuran, foreign keys, dll

---

## 💾 Backup Database

### Tujuan
Membuat backup lengkap database dengan:
- ✅ Struktur database (CREATE DATABASE)
- ✅ Struktur tabel (CREATE TABLE)
- ✅ Foreign Keys dan Constraints
- ✅ Data lengkap
- ✅ Triggers, Procedures, Functions, Events
- ✅ Views

### Cara Menjalankan

#### Opsi 1: Backup Otomatis (Menggunakan Hasil Audit)
```bash
cd /opt/lampp/htdocs/optima1
bash scripts/database_backup.sh
```

#### Opsi 2: Audit + Backup Sekaligus
```bash
cd /opt/lampp/htdocs/optima1
bash scripts/run_full_audit_and_backup.sh
```

### Output
Backup files akan disimpan di: `scripts/backups/`

Format file:
- `database_name_YYYYMMDD_HHMMSS.sql.gz` (compressed)
- `database_name_YYYYMMDD_HHMMSS.sql.gz.md5` (checksum)

### Verifikasi Backup
```bash
# Check MD5 checksum
md5sum -c database_name_YYYYMMDD_HHMMSS.sql.gz.md5

# Preview backup (tanpa extract)
zcat database_name_YYYYMMDD_HHMMSS.sql.gz | head -100
```

---

## 🔄 Restore di Windows

### Prerequisites
1. Install MySQL/MariaDB di Windows
2. Copy semua file backup (*.sql.gz) ke Windows
3. Pastikan MySQL service berjalan

### Cara Restore

#### Opsi 1: Menggunakan Script (Linux/Windows dengan Git Bash)
```bash
# Edit script untuk set DB_USER dan DB_PASS sesuai Windows
bash scripts/restore_database.sh
```

#### Opsi 2: Manual Restore

**Step 1: Extract backup**
```bash
gunzip database_name_YYYYMMDD_HHMMSS.sql.gz
```

**Step 2: Restore ke MySQL**
```bash
mysql -u root -p < database_name_YYYYMMDD_HHMMSS.sql
```

**Atau langsung (tanpa extract):**
```bash
gunzip < database_name_YYYYMMDD_HHMMSS.sql.gz | mysql -u root -p
```

#### Opsi 3: Menggunakan MySQL Workbench
1. Buka MySQL Workbench
2. Server → Data Import
3. Pilih "Import from Self-Contained File"
4. Pilih file `.sql.gz` (akan auto-extract)
5. Klik "Start Import"

### Verifikasi Restore
```sql
-- Cek database
SHOW DATABASES;

-- Cek tabel
USE optima_db;
SHOW TABLES;

-- Cek foreign keys
SELECT 
    TABLE_NAME,
    CONSTRAINT_NAME,
    REFERENCED_TABLE_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE table_schema = 'optima_db'
AND referenced_table_name IS NOT NULL;

-- Cek jumlah data
SELECT 
    table_name,
    table_rows
FROM information_schema.tables
WHERE table_schema = 'optima_db';
```

---

## ⚙️ Konfigurasi Aplikasi di Windows

Setelah restore, update konfigurasi database di Windows:

**File: `app/Config/Database.php`**
```php
public $default = [
    'hostname' => '127.0.0.1',
    'username' => 'root',
    'password' => 'your_windows_password',  // Update ini
    'database' => 'optima_db',
    'DBDriver' => 'MySQLi',
    'port'     => 3306,
];
```

---

## 🔧 Troubleshooting

### Problem: Backup gagal dengan error "Access denied"
**Solusi:**
- Pastikan user MySQL memiliki privilege untuk backup
- Cek password di script sesuai dengan MySQL server

### Problem: Restore gagal dengan error "Unknown database"
**Solusi:**
- Backup sudah include `CREATE DATABASE`, jadi seharusnya tidak perlu create manual
- Jika masih error, buat database dulu:
  ```sql
  CREATE DATABASE optima_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
  ```

### Problem: Foreign keys hilang setelah restore
**Solusi:**
- Pastikan backup menggunakan opsi `--routines --triggers`
- Cek apakah ada error saat restore
- Verifikasi dengan query di bagian "Verifikasi Restore"

### Problem: Character encoding issue
**Solusi:**
- Pastikan backup menggunakan `--default-character-set=utf8mb4`
- Set database charset saat restore:
  ```sql
  ALTER DATABASE optima_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
  ```

### Problem: File terlalu besar untuk restore
**Solusi:**
- Edit `my.ini` (Windows) atau `my.cnf` (Linux):
  ```ini
  [mysqld]
  max_allowed_packet=1G
  ```
- Restart MySQL service

---

## 📊 Checklist Migrasi

- [ ] Audit database selesai
- [ ] Backup semua database aktif
- [ ] Verifikasi backup files (checksum)
- [ ] Copy backup files ke Windows
- [ ] Install MySQL/MariaDB di Windows
- [ ] Restore database di Windows
- [ ] Verifikasi restore (tabel, data, FK)
- [ ] Update konfigurasi aplikasi
- [ ] Test aplikasi berjalan dengan baik

---

## 📞 Support

Jika ada masalah, cek:
1. Log MySQL: `C:\ProgramData\MySQL\MySQL Server X.X\Data\*.err`
2. Log aplikasi: `writable/logs/`
3. Error PHP: `php.ini` error reporting

---

**Last Updated:** $(date)

