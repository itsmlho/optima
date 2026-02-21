# Quick Production Deployment - Step by Step

Panduan singkat untuk deployment ke production server.

---

## 📋 Ringkasan
- **Jumlah Areas Baru:** 115 (61 DIESEL + 54 ELECTRIC)
- **Employee Assignments:** ~299 records (sesuaikan dengan employee ID production)
- **Production Server:** 147.93.80.45:65002
- **User:** u138256737

---

## 🚀 Cara Cepat (Windows)

### Langkah 1: Upload Files ke Production

Dari folder `C:\laragon\www\optima`, jalankan:

```cmd
databases\upload_to_production.bat
```

Script ini akan upload 4 migration files + 1 deployment script via SCP.

**Atau manual upload via SCP (Command Prompt):**

```cmd
cd C:\laragon\www\optima

scp -P 65002 databases\migrations\2026_02_20_add_central_areas_diesel_electric.sql u138256737@147.93.80.45:~/optima/databases/migrations/

scp -P 65002 databases\migrations\2026_02_20_execute_employee_assignments.sql u138256737@147.93.80.45:~/optima/databases/migrations/

scp -P 65002 databases\migrations\2026_02_20_rollback_central_areas.sql u138256737@147.93.80.45:~/optima/databases/migrations/

scp -P 65002 databases\migrations\2026_02_20_rollback_employee_assignments.sql u138256737@147.93.80.45:~/optima/databases/migrations/

scp -P 65002 databases\deploy_areas_production.sh u138256737@147.93.80.45:~/optima/databases/
```

---

### Langkah 2: Connect ke Production Server

```bash
ssh -p 65002 u138256737@147.93.80.45
```

Masukkan password SSH.

---

### Langkah 3: Navigasi ke Folder Optima

```bash
cd optima
pwd  # Pastikan anda di folder optima
```

---

### Langkah 4: Backup Database (PENTING!)

```bash
# Get database credentials
cat .env | grep database

# Backup database (ganti DB_USER dan DB_NAME sesuai .env)
mysqldump -u DB_USER -p DB_NAME > backups/production_backup_before_areas_$(date +%Y%m%d_%H%M%S).sql
```

Masukkan password database saat diminta.

**Verifikasi backup berhasil:**

```bash
ls -lh backups/production_backup_before_areas_*
```

---

### Langkah 5: Cek Employee IDs Production (PENTING!)

**KRUSIAL:** Employee IDs di production berbeda dengan local!

```bash
# Check database credentials from .env
cat .env | grep database

# Login ke MySQL
mysql -u DB_USER -p DB_NAME
```

**Jalankan query ini di MySQL:**

```sql
-- Cek employee DIESEL yang CENTRAL
SELECT id, staff_name, id_departemen, work_location, staff_role 
FROM employees 
WHERE id_departemen = 1 
  AND work_location = 'CENTRAL' 
  AND deleted_at IS NULL;

-- Cek employee ELECTRIC yang ADMIN
SELECT id, staff_name, id_departemen, work_location, staff_role 
FROM employees 
WHERE id_departemen = 2 
  AND staff_role = 'ADMIN' 
  AND deleted_at IS NULL;

-- Exit MySQL
exit;
```

**Catat employee IDs:**
- DIESEL CENTRAL: ID _____, _____
- ELECTRIC ADMIN: ID _____, _____, _____

---

### Langkah 6: Update Employee IDs di Script (Jika Berbeda)

Jika employee IDs production berbeda dengan local (8,9 untuk DIESEL dan 1,2,18 untuk ELECTRIC), edit script:

```bash
nano databases/migrations/2026_02_20_execute_employee_assignments.sql
```

**Cari dan ganti:**

```sql
-- Baris 8: Ubah employee IDs DIESEL
WHERE id IN (8, 9)  -- Ganti dengan IDs production

-- Baris 31: Ubah employee IDs ELECTRIC  
WHERE id IN (1, 2, 18)  -- Ganti dengan IDs production
```

Simpan: `CTRL+X`, `Y`, `Enter`

---

### Langkah 7: Cek Current State (Opsional)

```bash
mysql -u DB_USER -p DB_NAME
```

```sql
-- Berapa areas CENTRAL yang ada sekarang?
SELECT area_type, COUNT(*) as total 
FROM areas 
WHERE deleted_at IS NULL 
GROUP BY area_type;

-- Ada berapa assignments sekarang?
SELECT COUNT(*) FROM area_employee_assignments WHERE deleted_at IS NULL;

exit;
```

---

### Langkah 8: Eksekusi Migration Areas

```bash
mysql -u DB_USER -p DB_NAME < databases/migrations/2026_02_20_add_central_areas_diesel_electric.sql
```

**Verifikasi:**

```bash
mysql -u DB_USER -p DB_NAME -e "SELECT area_type, COUNT(*) as total FROM areas WHERE deleted_at IS NULL GROUP BY area_type;"
```

Harusnya muncul **+115 areas CENTRAL**.

---

### Langkah 9: Eksekusi Employee Assignments

```bash
mysql -u DB_USER -p DB_NAME < databases/migrations/2026_02_20_execute_employee_assignments.sql
```

**Verifikasi:**

```bash
mysql -u DB_USER -p DB_NAME
```

```sql
-- Total assignments
SELECT COUNT(*) as total_assignments 
FROM area_employee_assignments 
WHERE deleted_at IS NULL;

-- Per employee
SELECT 
    e.staff_name,
    COUNT(aea.id) as assigned_areas
FROM area_employee_assignments aea
JOIN employees e ON aea.employee_id = e.id
WHERE aea.deleted_at IS NULL
  AND e.deleted_at IS NULL
GROUP BY e.staff_name
ORDER BY e.staff_name;

-- Per departemen
SELECT 
    d.departemen_name,
    COUNT(aea.id) as total_assignments
FROM area_employee_assignments aea
JOIN employees e ON aea.employee_id = e.id
JOIN departemen d ON e.id_departemen = d.id_departemen
WHERE aea.deleted_at IS NULL
  AND e.deleted_at IS NULL
  AND d.deleted_at IS NULL
GROUP BY d.departemen_name;

exit;
```

---

### Langkah 10: Verifikasi Final

```bash
mysql -u DB_USER -p DB_NAME
```

```sql
-- Sample areas
SELECT area_code, area_name, area_type, status 
FROM areas 
WHERE area_code IN ('D-BANDUNG', 'E-BANDUNG', 'D-JAKARTA', 'E-MEDAN')
  AND deleted_at IS NULL;

-- Check specific assignments
SELECT 
    a.area_code,
    a.area_name,
    e.staff_name,
    d.departemen_name
FROM area_employee_assignments aea
JOIN areas a ON aea.area_id = a.id
JOIN employees e ON aea.employee_id = e.id
JOIN departemen d ON e.id_departemen = d.id_departemen
WHERE a.area_code IN ('D-BANDUNG', 'E-BANDUNG', 'D-JAKARTA', 'E-MEDAN')
  AND aea.deleted_at IS NULL
  AND a.deleted_at IS NULL
  AND e.deleted_at IS NULL
LIMIT 10;

exit;
```

---

## ✅ Selesai!

Jika semua verifikasi berhasil:
- 115 areas baru sudah masuk
- Employee assignments sudah sesuai
- Sample queries menunjukkan data benar

Deployment berhasil! 🎉

---

## 🔄 Rollback (Jika Ada Masalah)

Jika terjadi error atau data tidak sesuai:

```bash
# Rollback employee assignments dulu
mysql -u DB_USER -p DB_NAME < databases/migrations/2026_02_20_rollback_employee_assignments.sql

# Rollback areas
mysql -u DB_USER -p DB_NAME < databases/migrations/2026_02_20_rollback_central_areas.sql

# Restore dari backup
mysql -u DB_USER -p DB_NAME < backups/production_backup_before_areas_[timestamp].sql
```

---

## 📞 Troubleshooting

### Error: "Duplicate entry for key 'area_code'"
Artinya area_code sudah ada. Cek dengan:
```sql
SELECT area_code, COUNT(*) FROM areas GROUP BY area_code HAVING COUNT(*) > 1;
```

### Error: "Cannot add foreign key constraint"
Employee ID tidak valid. Cek lagi Step 5.

### Script tidak bisa dijalankan
Pastikan files sudah di-upload (Step 1) dan anda ada di folder `optima`.

---

## 📚 Referensi Lengkap

Untuk panduan detail dan troubleshooting lanjutan, baca:
- `databases/PRODUCTION_AREA_CENTRAL_DEPLOYMENT.md` - Panduan lengkap
- `databases/deploy_areas_production.sh` - Automated script (alternative)

---

**Created:** February 21, 2026  
**Migration Files:**
- 2026_02_20_add_central_areas_diesel_electric.sql
- 2026_02_20_execute_employee_assignments.sql
- 2026_02_20_rollback_central_areas.sql
- 2026_02_20_rollback_employee_assignments.sql
