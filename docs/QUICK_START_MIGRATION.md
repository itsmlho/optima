# Quick Start - Migration Guide

## ⚠️ ERROR: Table 'user_sessions' doesn't exist

Jika Anda melihat error ini, berarti **migration belum dijalankan**. 

---

## 🚀 SOLUSI CEPAT: Manual Migration via phpMyAdmin

### Step 1: Buka phpMyAdmin

1. Buka browser: `http://localhost/phpmyadmin`
2. Login dengan:
   - Username: `root`
   - Password: `root` (atau sesuai setting Anda)
3. Pilih database: `optima_db` (kiri sidebar)

### Step 2: Import SQL File

**Cara Termudah:**

1. Klik tab **"Import"** di phpMyAdmin
2. Klik **"Choose File"** atau **"Browse"**
3. Pilih file: `MIGRATION_MANUAL.sql`
   - File ada di: `/opt/lampp/htdocs/optima1/MIGRATION_MANUAL.sql`
   - Atau di root folder project Anda
4. Klik tombol **"Go"** atau **"Import"**
5. Tunggu hingga selesai (akan muncul pesan sukses)

### Step 3: Verifikasi

Setelah import selesai, jalankan query berikut di tab **"SQL"**:

```sql
SHOW TABLES LIKE '%otp%';
SHOW TABLES LIKE '%session%';
SHOW TABLES LIKE '%login%';
SHOW TABLES LIKE '%password%';
```

**Expected Result:**
- ✅ `user_otp`
- ✅ `login_attempts`
- ✅ `user_sessions`
- ✅ `password_resets`

### Step 4: Refresh Aplikasi

Setelah migration berhasil:
1. Refresh halaman aplikasi (F5)
2. Error akan hilang
3. Aplikasi berfungsi normal

---

## 🔧 Fallback Applied

Saya sudah menambahkan **graceful fallback** di code, jadi:

- ✅ Aplikasi **tidak akan error** jika tabel belum ada
- ✅ Session tracking **dipanggil** jika tabel sudah ada
- ✅ Jika tabel belum ada, tracking **di-skip** dengan aman
- ✅ Log debug message jika terjadi error

**Tapi tetap perlu jalankan migration** untuk:
- ✅ Rate limiting berfungsi
- ✅ OTP berfungsi
- ✅ Session management berfungsi
- ✅ Forgot password berfungsi

---

## 📋 Checklist Migration

- [ ] Buka phpMyAdmin
- [ ] Pilih database: `optima_db`
- [ ] Import file: `MIGRATION_MANUAL.sql`
- [ ] Verifikasi 4 tables created
- [ ] Refresh aplikasi
- [ ] Test login (rate limiting)
- [ ] Test forgot password (jika email sudah setup)

---

## ⚡ Alternative: Copy-Paste SQL

Jika import file gagal, bisa copy-paste SQL:

1. Buka file `MIGRATION_MANUAL.sql` dengan text editor
2. Copy semua isi SQL (Ctrl+A, Ctrl+C)
3. Di phpMyAdmin, klik tab **"SQL"**
4. Paste SQL (Ctrl+V)
5. Klik **"Go"**

---

## 🆘 Troubleshooting

### Error: Table Already Exists

Jika ada error "Table already exists", berarti:
- ✅ Table sudah dibuat sebelumnya
- ✅ Bisa abaikan error ini
- ✅ Atau skip bagian yang error dan lanjutkan

### Error: Column Already Exists

Jika ada error "Column already exists" untuk OTP columns:
- ✅ Column sudah ada di users table
- ✅ Bisa abaikan error ini
- ✅ Migration akan skip bagian yang sudah ada

### Error: Syntax Error

Jika ada error syntax:
- ✅ Cek MySQL version (minimal MySQL 5.7)
- ✅ Atau gunakan query yang lebih sederhana (lihat MIGRATION_MANUAL.sql)

---

## 📚 Dokumentasi Lengkap

Untuk panduan lengkap, lihat:
- `docs/MIGRATION_GUIDE.md` - Panduan lengkap migration
- `docs/SETUP_GUIDE_SECURITY.md` - Setup guide lengkap

---

**Last Updated:** <?= date('Y-m-d H:i:s') ?>

