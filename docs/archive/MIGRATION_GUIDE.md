# Migration Guide - Security Features

## Problem

Jika `php spark migrate` gagal karena error MySQLi/PDO extensions, gunakan **manual migration via phpMyAdmin**.

---

## Solusi: Manual Migration via phpMyAdmin

### Step 1: Buka phpMyAdmin

1. Buka browser: `http://localhost/phpmyadmin`
2. Login dengan credentials:
   - Username: `root`
   - Password: `root` (atau sesuai setting Anda)
3. Pilih database: `optima_ci`

### Step 2: Import SQL File

**Opsi A: Import File (Recommended)**

1. Klik tab **"Import"** di phpMyAdmin
2. Klik **"Choose File"**
3. Pilih file: `MIGRATION_MANUAL.sql` (di root project)
4. Klik **"Go"** atau **"Import"**
5. Tunggu hingga selesai

**Opsi B: Copy-Paste SQL**

1. Buka file `MIGRATION_MANUAL.sql` dengan text editor
2. Copy semua isi SQL
3. Di phpMyAdmin, klik tab **"SQL"**
4. Paste SQL yang sudah dicopy
5. Klik **"Go"**
6. Tunggu hingga selesai

### Step 3: Verifikasi Tables Created

Jalankan query berikut di phpMyAdmin untuk verifikasi:

```sql
-- Check if tables exist
SELECT 
    TABLE_NAME as 'Table Name',
    TABLE_ROWS as 'Rows',
    CREATE_TIME as 'Created'
FROM 
    information_schema.TABLES 
WHERE 
    TABLE_SCHEMA = 'optima_ci'
    AND TABLE_NAME IN ('user_otp', 'login_attempts', 'user_sessions', 'password_resets')
ORDER BY 
    TABLE_NAME;
```

**Expected Result:**
- ✅ `user_otp` - Table for OTP codes
- ✅ `login_attempts` - Table for rate limiting
- ✅ `user_sessions` - Table for session management
- ✅ `password_resets` - Table for password reset tokens

### Step 4: Verifikasi OTP Columns in Users Table

Jalankan query berikut:

```sql
-- Check if OTP columns exist in users table
SELECT 
    COLUMN_NAME as 'Column Name',
    COLUMN_TYPE as 'Type',
    IS_NULLABLE as 'Nullable',
    COLUMN_DEFAULT as 'Default'
FROM 
    information_schema.COLUMNS 
WHERE 
    TABLE_SCHEMA = 'optima_ci'
    AND TABLE_NAME = 'users' 
    AND COLUMN_NAME IN ('otp_enabled', 'otp_enabled_at');
```

**Expected Result:**
- ✅ `otp_enabled` (tinyint(1), default: 0)
- ✅ `otp_enabled_at` (datetime, nullable)

---

## Alternative: Fix PHP Extensions (Advanced)

Jika ingin fix PHP extensions untuk bisa menggunakan `php spark migrate`:

### For LAMPP/XAMPP:

1. **Edit php.ini:**

   ```bash
   sudo nano /opt/lampp/etc/php.ini
   ```

2. **Uncomment extensions:**

   Cari dan pastikan baris berikut tidak di-comment (tidak ada `;` di depan):
   
   ```ini
   extension=mysqli
   extension=pdo_mysql
   ```

3. **Restart Apache:**

   ```bash
   sudo /opt/lampp/lampp restart
   ```

4. **Verify extensions:**

   ```bash
   php -m | grep -i mysql
   ```

   Expected output:
   ```
   mysqli
   pdo_mysql
   ```

5. **Run migration:**

   ```bash
   php spark migrate
   ```

---

## Troubleshooting

### Error: Table Already Exists

Jika ada error "Table already exists", berarti table sudah dibuat sebelumnya. Ini tidak masalah, migration akan skip table yang sudah ada (karena menggunakan `IF NOT EXISTS`).

**Solusi:** Abaikan error ini atau hapus table yang bermasalah jika ingin re-create.

### Error: Column Already Exists

Jika ada error "Column already exists" untuk OTP columns di users table, berarti column sudah ada.

**Solusi:** Abaikan error ini atau gunakan query:

```sql
-- Check if columns exist first, then add if not exists
ALTER TABLE `users` 
ADD COLUMN IF NOT EXISTS `otp_enabled` tinyint(1) NOT NULL DEFAULT 0 AFTER `remember_token`,
ADD COLUMN IF NOT EXISTS `otp_enabled_at` datetime NULL DEFAULT NULL AFTER `otp_enabled`;
```

Jika MySQL version tidak support `IF NOT EXISTS`, gunakan query ini:

```sql
-- Check if column exists
SELECT COUNT(*) as exists_count
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'optima_ci'
  AND TABLE_NAME = 'users'
  AND COLUMN_NAME = 'otp_enabled';

-- If exists_count = 0, then run:
ALTER TABLE `users` 
ADD COLUMN `otp_enabled` tinyint(1) NOT NULL DEFAULT 0 AFTER `remember_token`,
ADD COLUMN `otp_enabled_at` datetime NULL DEFAULT NULL AFTER `otp_enabled`;
```

### Error: Syntax Error

Jika ada error syntax, mungkin karena:
1. MySQL version terlalu lama (minimal MySQL 5.7 atau MariaDB 10.2)
2. Ada keyword yang reserved di MySQL version Anda

**Solusi:** Update MySQL/MariaDB atau sesuaikan SQL syntax dengan version Anda.

---

## Verification After Migration

Setelah migration selesai, verifikasi dengan query berikut:

### 1. Check All Tables

```sql
SHOW TABLES LIKE '%otp%';
SHOW TABLES LIKE '%session%';
SHOW TABLES LIKE '%login%';
SHOW TABLES LIKE '%password%';
```

### 2. Check Table Structure

```sql
-- Check user_otp structure
DESCRIBE user_otp;

-- Check login_attempts structure
DESCRIBE login_attempts;

-- Check user_sessions structure
DESCRIBE user_sessions;

-- Check password_resets structure
DESCRIBE password_resets;
```

### 3. Check Users Table Columns

```sql
-- Check OTP columns in users table
DESCRIBE users;
-- Look for: otp_enabled, otp_enabled_at
```

---

## Next Steps After Migration

Setelah migration berhasil:

1. ✅ **Test Application:**
   - Login ke aplikasi
   - Test rate limiting (5 failed attempts)
   - Test forgot password (jika email config sudah setup)
   - Test session management (login dari multiple devices)

2. ✅ **Enable OTP (Optional):**
   ```sql
   -- Enable OTP for specific user
   UPDATE users 
   SET otp_enabled = 1, 
       otp_enabled_at = NOW() 
   WHERE id = [user_id];
   ```

3. ✅ **Configure Email:**
   - Edit `app/Config/Email.php`
   - Setup SMTP atau mail configuration
   - Test email sending

4. ✅ **Test Security Features:**
   - Follow testing checklist: `docs/TESTING_CHECKLIST_SECURITY.md`

---

## Summary

✅ **File created:** `MIGRATION_MANUAL.sql`  
✅ **Tables to create:** 4 tables (user_otp, login_attempts, user_sessions, password_resets)  
✅ **Columns to add:** 2 columns (otp_enabled, otp_enabled_at) di users table  

**Recommended:** Gunakan manual migration via phpMyAdmin jika `php spark migrate` error.

---

**Last Updated:** <?= date('Y-m-d H:i:s') ?>

