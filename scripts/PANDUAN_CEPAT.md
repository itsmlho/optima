# 🚀 Panduan Cepat Audit & Backup Database

## Langkah 1: Audit Database

Jalankan script audit untuk melihat database mana yang aktif:

```bash
cd /opt/lampp/htdocs/optima1
php scripts/database_audit.php
```

**Hasil:**
- File laporan akan dibuat di folder `scripts/`
- Lihat file HTML untuk laporan yang lebih mudah dibaca

## Langkah 2: Backup Database

Setelah audit, backup semua database aktif:

```bash
bash scripts/database_backup.sh
```

**Atau jalankan audit + backup sekaligus:**

```bash
bash scripts/run_full_audit_and_backup.sh
```

**Hasil:**
- File backup akan disimpan di `scripts/backups/`
- Format: `database_name_YYYYMMDD_HHMMSS.sql.gz`

## Langkah 3: Restore di Windows

### Di Windows (setelah install MySQL):

1. **Copy file backup** ke Windows
2. **Extract dan restore:**
   ```bash
   # Extract
   gunzip database_name_YYYYMMDD_HHMMSS.sql.gz
   
   # Restore
   mysql -u root -p < database_name_YYYYMMDD_HHMMSS.sql
   ```

   **Atau langsung:**
   ```bash
   gunzip < database_name_YYYYMMDD_HHMMSS.sql.gz | mysql -u root -p
   ```

3. **Update konfigurasi** di `app/Config/Database.php`:
   - Update `password` sesuai MySQL di Windows
   - Pastikan `hostname` dan `port` sesuai

## ✅ Checklist

- [ ] Audit database selesai
- [ ] Backup semua database aktif
- [ ] Copy file backup ke Windows
- [ ] Restore database di Windows
- [ ] Update konfigurasi aplikasi
- [ ] Test aplikasi berjalan

## 📁 File Penting

- `scripts/database_audit.php` - Script audit
- `scripts/database_backup.sh` - Script backup
- `scripts/restore_database.sh` - Script restore
- `scripts/backups/` - Folder backup
- `scripts/README_DATABASE_MIGRATION.md` - Dokumentasi lengkap

## ⚠️ Catatan Penting

1. **Password MySQL**: Pastikan password di config sesuai dengan MySQL server
2. **Ukuran Backup**: File backup bisa besar, pastikan ada space cukup
3. **Foreign Keys**: Backup sudah include semua foreign keys dan constraints
4. **Character Set**: Backup menggunakan utf8mb4 untuk support emoji dan karakter khusus

## 🆘 Troubleshooting

**Backup gagal?**
- Cek password MySQL di config
- Pastikan MySQL service berjalan
- Cek space disk tersedia

**Restore gagal?**
- Pastikan MySQL service berjalan
- Cek error message dari MySQL
- Pastikan user MySQL punya privilege CREATE DATABASE

**Aplikasi error setelah restore?**
- Cek konfigurasi database di `app/Config/Database.php`
- Verifikasi semua tabel ada: `SHOW TABLES;`
- Cek foreign keys: lihat dokumentasi lengkap

---

**Untuk dokumentasi lengkap, lihat:** `README_DATABASE_MIGRATION.md`

