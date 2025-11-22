# 📋 Panduan Restore Database di Windows

## ✅ Status Backup

**Backup sudah lengkap dan siap digunakan!**

- ✅ File backup: `optima_db_20251122_231057.sql.gz` (92 KB)
- ✅ Checksum: `optima_db_20251122_231057.sql.gz.md5` (verified OK)
- ✅ Isi backup sudah lengkap:
  - CREATE DATABASE statement
  - CREATE TABLE statements (134 tabel)
  - FOREIGN KEY constraints (74 foreign keys)
  - INSERT INTO statements (semua data)
  - Triggers, Procedures, Functions, Events

---

## 🚀 Langkah-Langkah Restore di Windows

### **Langkah 1: Copy File Backup ke Windows**

Copy file berikut ke Windows:
```
optima_db_20251122_231057.sql.gz
optima_db_20251122_231057.sql.gz.md5 (optional, untuk verifikasi)
```

**Lokasi file di Linux:**
```
/opt/lampp/htdocs/optima1/scripts/backups/
```

---

### **Langkah 2: Install MySQL/MariaDB di Windows**

Jika belum install:
1. Download MySQL dari: https://dev.mysql.com/downloads/installer/
2. Atau MariaDB dari: https://mariadb.org/download/
3. Install dengan default settings
4. Catat password root yang dibuat saat install

---

### **Langkah 3: Restore Database**

#### **Opsi A: Menggunakan Command Prompt (CMD) atau PowerShell**

1. Buka **Command Prompt** atau **PowerShell** sebagai Administrator
2. Navigate ke folder tempat file backup:
   ```cmd
   cd C:\path\to\backup\folder
   ```

3. **Restore database:**
   ```cmd
   # Extract dan restore sekaligus
   gunzip < optima_db_20251122_231057.sql.gz | mysql -u root -p
   ```
   
   Atau jika gunzip tidak tersedia:
   ```cmd
   # Extract dulu (gunakan 7-Zip atau WinRAR)
   # Kemudian restore
   mysql -u root -p < optima_db_20251122_231057.sql
   ```

4. Masukkan password MySQL saat diminta

#### **Opsi B: Menggunakan MySQL Workbench (GUI)**

1. Buka **MySQL Workbench**
2. Connect ke MySQL server
3. Klik menu **Server** → **Data Import**
4. Pilih **"Import from Self-Contained File"**
5. Browse ke file `optima_db_20251122_231057.sql.gz` (akan auto-extract)
6. Pilih **"New"** untuk target schema, atau pilih existing
7. Klik **"Start Import"**

#### **Opsi C: Menggunakan phpMyAdmin (jika ada XAMPP/WAMP)**

1. Buka phpMyAdmin di browser
2. Klik tab **"Import"**
3. Klik **"Choose File"** dan pilih file `.sql.gz`
4. Klik **"Go"**

---

### **Langkah 4: Verifikasi Restore**

Setelah restore, verifikasi dengan query berikut:

```sql
-- Cek database ada
SHOW DATABASES;

-- Gunakan database
USE optima_db;

-- Cek jumlah tabel (harus 134)
SHOW TABLES;

-- Cek foreign keys (harus ada 74)
SELECT 
    COUNT(*) as total_fk
FROM information_schema.KEY_COLUMN_USAGE
WHERE table_schema = 'optima_db'
AND referenced_table_name IS NOT NULL;

-- Cek beberapa data penting
SELECT COUNT(*) as total_users FROM users;
SELECT COUNT(*) as total_roles FROM roles;
SELECT COUNT(*) as total_permissions FROM permissions;
```

---

### **Langkah 5: Update Konfigurasi Aplikasi**

Edit file: `app/Config/Database.php`

```php
public $default = [
    'hostname' => '127.0.0.1',
    'username' => 'root',
    'password' => 'password_windows_anda',  // ⚠️ UPDATE INI!
    'database' => 'optima_db',
    'DBDriver' => 'MySQLi',
    'port'     => 3306,
];
```

---

## ✅ Checklist Migrasi

- [ ] File backup sudah di-copy ke Windows
- [ ] MySQL/MariaDB sudah terinstall di Windows
- [ ] Database sudah di-restore
- [ ] Verifikasi restore berhasil (134 tabel, 74 FK)
- [ ] Konfigurasi aplikasi sudah di-update
- [ ] Test aplikasi berjalan dengan baik

---

## 🔍 Troubleshooting

### **Error: "Unknown database"**
**Solusi:** Backup sudah include CREATE DATABASE, jadi seharusnya tidak perlu create manual. Jika masih error:
```sql
CREATE DATABASE optima_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
```

### **Error: "Access denied"**
**Solusi:** Pastikan user MySQL punya privilege:
```sql
GRANT ALL PRIVILEGES ON optima_db.* TO 'root'@'localhost';
FLUSH PRIVILEGES;
```

### **Error: "File too large"**
**Solusi:** Edit `my.ini` (MySQL) atau `my.cnf` (MariaDB):
```ini
[mysqld]
max_allowed_packet=1G
```
Restart MySQL service.

### **Error: "Foreign key constraint fails"**
**Solusi:** Pastikan restore dilakukan dengan urutan yang benar. Backup sudah include urutan yang benar, jadi pastikan tidak ada error sebelumnya.

---

## 📞 Catatan Penting

1. **Tidak perlu create database manual** - Backup sudah include CREATE DATABASE
2. **Tidak perlu create tabel manual** - Backup sudah include semua CREATE TABLE
3. **Foreign keys sudah include** - Tidak perlu setup ulang
4. **Data sudah lengkap** - Semua data sudah ada di backup

---

**File backup ini sudah lengkap dan siap digunakan!** 🎉

