# ✅ Checklist Konfigurasi Email

## 🔍 Perbaikan yang Dilakukan

### ❌ Error yang Diperbaiki:
1. **SMTPHost salah:** 
   - ❌ Sebelum: `'itsupport@sml.co.id'` (ini adalah email, bukan hostname)
   - ✅ Sesudah: `'smtp.gmail.com'` (ini adalah hostname server SMTP Gmail)

---

## 📋 Checklist Konfigurasi

### ✅ 1. From Email & Name
```php
public string $fromEmail  = 'itsupport@sml.co.id';  // ✅ Email pengirim
public string $fromName   = 'OPTIMA System';         // ✅ Nama pengirim
```
**Status:** ✅ Sudah benar

---

### ✅ 2. Protocol
```php
public string $protocol = 'smtp';  // ✅ SMTP untuk Gmail/Google Workspace
```
**Status:** ✅ Sudah benar

---

### ✅ 3. SMTP Configuration

#### A. SMTP Host
```php
public string $SMTPHost = 'smtp.gmail.com';  // ✅ Sudah diperbaiki
```
**Status:** ✅ Sudah benar (diperbaiki dari email ke hostname)

**Catatan:**
- Jika menggunakan **Gmail** atau **Google Workspace** → `'smtp.gmail.com'`
- Jika menggunakan provider lain, sesuaikan hostname SMTP-nya

#### B. SMTP User
```php
public string $SMTPUser = 'itsupport@sml.co.id';  // ✅ Full email address
```
**Status:** ✅ Sudah benar

#### C. SMTP Password (App Password)
```php
public string $SMTPPass = 'roul fztj xlrw xdct';  // ✅ App Password
```
**Status:** ⚠️ Perlu verifikasi

**Catatan:**
- Pastikan ini adalah **App Password** dari Google Account, bukan password biasa
- App Password bisa dengan atau tanpa spasi
- Jika belum punya App Password, ikuti panduan: `docs/GMAIL_APP_PASSWORD_GUIDE.md`

#### D. SMTP Port & Encryption
```php
public int $SMTPPort = 587;  // ✅ Port 587 untuk TLS
public string $SMTPCrypto = 'tls';  // ✅ TLS encryption
```
**Status:** ✅ Sudah benar

**Catatan:**
- Port 587 + TLS → untuk Gmail (recommended)
- Port 465 + SSL → alternatif untuk Gmail
- Port 25 → biasanya untuk server mail lokal (tidak recommended untuk Gmail)

---

## 🔧 Cara Verifikasi Setup

### Step 1: Test Email Configuration

1. **Buka:** `http://localhost/optima1/public/test_otp_email.php`
2. **Masukkan email** untuk testing
3. **Klik "Send Test Email"**
4. **Cek hasil:**
   - ✅ **Berhasil:** Email diterima di inbox/spam
   - ❌ **Gagal:** Lihat error message di halaman test

---

### Step 2: Cek Error Messages

#### ❌ Error: "Username and Password not accepted"
**Penyebab:**
- App Password salah
- 2-Step Verification belum aktif
- Email `itsupport@sml.co.id` belum setup App Password

**Solusi:**
1. Pastikan `itsupport@sml.co.id` menggunakan Google Account
2. Enable 2-Step Verification di Google Account
3. Buat App Password untuk `itsupport@sml.co.id`
4. Copy App Password dan paste ke `$SMTPPass`

---

#### ❌ Error: "Could not connect to SMTP host"
**Penyebab:**
- SMTPHost salah (sudah diperbaiki)
- Firewall block port 587
- Koneksi internet bermasalah

**Solusi:**
1. Pastikan `SMTPHost = 'smtp.gmail.com'` (sudah diperbaiki)
2. Cek firewall (allow port 587)
3. Test koneksi internet

---

#### ❌ Error: "Connection timed out"
**Penyebab:**
- Firewall block SMTP
- Server email down

**Solusi:**
1. Pastikan firewall tidak block port 587
2. Coba lagi beberapa saat kemudian

---

## 📝 Catatan Penting

### Untuk Email Corporate (`itsupport@sml.co.id`)

Jika `itsupport@sml.co.id` adalah:
- ✅ **Google Workspace (G Suite)** → Gunakan konfigurasi Gmail (`smtp.gmail.com`)
- ❌ **Email provider lain** (seperti cPanel, Microsoft 365, dll) → Perlu konfigurasi SMTP yang berbeda

**Cara cek apakah menggunakan Google Workspace:**
1. Coba login ke email via web: `https://mail.google.com`
2. Jika bisa login → menggunakan Google Workspace
3. Jika tidak bisa → menggunakan provider email lain

---

### Jika Bukan Google Workspace

Jika `itsupport@sml.co.id` menggunakan provider email lain, Anda perlu:
1. **Tanya IT admin** untuk:
   - SMTP Host (contoh: `mail.sml.co.id`, `smtp.office365.com`, dll)
   - SMTP Port (biasanya 587 atau 465)
   - SMTP Encryption (TLS atau SSL)
   - Username (bisa email atau username khusus)
   - Password (bisa password biasa atau app password)

2. **Update konfigurasi:**
```php
public string $SMTPHost = 'mail.sml.co.id';  // Sesuaikan dengan SMTP server Anda
public string $SMTPUser = 'itsupport@sml.co.id';  // Email atau username
public string $SMTPPass = 'your-password';  // Password
public int $SMTPPort = 587;  // Sesuaikan (587 untuk TLS, 465 untuk SSL)
public string $SMTPCrypto = 'tls';  // 'tls' untuk port 587, 'ssl' untuk port 465
```

---

## ✅ Next Steps

1. **Test email** via `test_otp_email.php`
2. **Jika berhasil:** OTP email sudah siap digunakan
3. **Jika gagal:** 
   - Cek error message
   - Ikuti troubleshooting di atas
   - Atau tanya IT admin untuk konfigurasi SMTP yang benar

---

**Last Updated:** <?= date('Y-m-d H:i:s') ?>

