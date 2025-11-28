# Panduan Instalasi Tabel SILO

## 📋 File SQL

1. **create_silo_tables.sql** - Script untuk membuat tabel `silo` dan `silo_history`
2. **drop_silo_tables.sql** - Script untuk menghapus tabel (rollback)

---

## 🚀 Cara Menjalankan

### **Metode 1: Via phpMyAdmin**

1. Buka phpMyAdmin
2. Pilih database yang digunakan (misal: `optima_ci`)
3. Klik tab **"SQL"**
4. Copy-paste isi file `create_silo_tables.sql`
5. Klik **"Go"** atau **"Jalankan"**

### **Metode 2: Via MySQL CLI**

```bash
# Login ke MySQL
mysql -u root -p

# Pilih database
USE optima_ci;

# Jalankan script
source /opt/lampp/htdocs/optima1/databases/migrations/create_silo_tables.sql;

# Atau langsung dari command line
mysql -u root -p optima_ci < /opt/lampp/htdocs/optima1/databases/migrations/create_silo_tables.sql
```

### **Metode 3: Via XAMPP MySQL**

```bash
# Jika menggunakan XAMPP
/opt/lampp/bin/mysql -u root -p optima_ci < /opt/lampp/htdocs/optima1/databases/migrations/create_silo_tables.sql
```

---

## ✅ Verifikasi

Setelah menjalankan script, verifikasi dengan query berikut:

```sql
-- Cek apakah tabel sudah dibuat
SHOW TABLES LIKE 'silo%';

-- Cek struktur tabel silo
DESCRIBE silo;

-- Cek struktur tabel silo_history
DESCRIBE silo_history;

-- Cek foreign keys
SELECT 
    TABLE_NAME,
    CONSTRAINT_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM 
    INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE 
    TABLE_SCHEMA = 'optima_ci'
    AND TABLE_NAME IN ('silo', 'silo_history')
    AND REFERENCED_TABLE_NAME IS NOT NULL;
```

---

## 🔄 Rollback (Jika Perlu)

Jika perlu menghapus tabel, jalankan:

```sql
-- Via phpMyAdmin atau MySQL CLI
source /opt/lampp/htdocs/optima1/databases/migrations/drop_silo_tables.sql;
```

---

## 📊 Struktur Tabel

### **Tabel `silo`**
- Primary Key: `id_silo`
- Foreign Key: `unit_id` → `inventory_unit.id_inventory_unit`
- Status: ENUM dengan 8 status workflow
- Index: `unit_id`, `status`, `nomor_silo`, `tanggal_expired_silo`

### **Tabel `silo_history`**
- Primary Key: `id_history`
- Foreign Key: `silo_id` → `silo.id_silo` (CASCADE)
- Tracking: Perubahan status dengan timestamp

---

## ⚠️ Catatan Penting

1. **Pastikan tabel `inventory_unit` sudah ada** sebelum menjalankan script
2. **Pastikan tabel `users` sudah ada** (untuk `created_by` dan `updated_by`)
3. **Backup database** sebelum menjalankan script (jika ada data penting)
4. **Foreign key constraint** akan mencegah penghapusan unit yang memiliki SILO aktif

---

## 🐛 Troubleshooting

### Error: "Table 'inventory_unit' doesn't exist"
- Pastikan tabel `inventory_unit` sudah dibuat terlebih dahulu
- Cek nama database yang digunakan

### Error: "Foreign key constraint fails"
- Pastikan tidak ada data di `silo` yang reference ke unit yang akan dihapus
- Hapus data SILO terlebih dahulu sebelum menghapus unit

### Error: "Duplicate key name"
- Tabel mungkin sudah ada
- Gunakan `DROP TABLE IF EXISTS` terlebih dahulu (sudah ada di script)

---

**Script siap digunakan!** ✅

