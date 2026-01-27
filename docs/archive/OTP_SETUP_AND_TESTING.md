# OTP Setup dan Testing Guide

## 1. Cara Aktifkan/Nonaktifkan OTP

### ✅ Via Profile Page (Recommended - Mudah)

1. **Login ke aplikasi**
2. **Klik menu Profile** (di sidebar atau dropdown user)
3. **Scroll ke "Security Settings"**
4. **Klik tombol "Aktifkan" atau "Nonaktifkan"** di bagian "Two-Factor Authentication (OTP)"
5. **Konfirmasi** - OTP akan aktif/nonaktif untuk akun Anda

**Fitur:**
- ✅ Toggle button yang jelas (Aktif/Tidak Aktif)
- ✅ Badge status (hijau untuk Aktif, abu untuk Tidak Aktif)
- ✅ Info kapan OTP diaktifkan
- ✅ AJAX tanpa reload page

### Via Database (Manual)

Jika ingin aktifkan/nonaktifkan via database langsung:

```sql
-- Aktifkan OTP untuk user tertentu
UPDATE users 
SET otp_enabled = 1, 
    otp_enabled_at = NOW() 
WHERE id = [user_id];

-- Nonaktifkan OTP untuk user tertentu
UPDATE users 
SET otp_enabled = 0, 
    otp_enabled_at = NULL 
WHERE id = [user_id];

-- Cek status OTP user
SELECT id, username, email, otp_enabled, otp_enabled_at 
FROM users 
WHERE id = [user_id];
```

---

## 2. Setup Email untuk Testing OTP

### Step 1: Edit Email Configuration

Edit file: `app/Config/Email.php`

**Opsi A: Mail (Simple - untuk localhost testing)**

```php
public string $fromEmail  = 'noreply@optima.local';
public string $fromName   = 'OPTIMA System';
public string $protocol = 'mail';
```

**Opsi B: SMTP (Recommended - untuk production atau testing real email)**

```php
public string $fromEmail  = 'your-email@gmail.com';
public string $fromName   = 'OPTIMA System';
public string $protocol = 'smtp';
public string $SMTPHost = 'smtp.gmail.com';
public string $SMTPUser = 'your-email@gmail.com';
public string $SMTPPass = 'your-app-password';  // Gmail App Password, bukan password biasa!
public int $SMTPPort = 587;
public string $SMTPCrypto = 'tls';
public int $SMTPTimeout = 60;
```

**Untuk Gmail:**
1. Enable 2-Step Verification di Google Account
2. Buat App Password: https://myaccount.google.com/apppasswords
3. Gunakan App Password (16 karakter) sebagai `SMTPPass`

**Untuk Mailtrap (Testing):**
```php
public string $SMTPHost = 'smtp.mailtrap.io';
public string $SMTPUser = 'your-mailtrap-username';
public string $SMTPPass = 'your-mailtrap-password';
public int $SMTPPort = 2525;
public string $SMTPCrypto = 'tls';
```

---

## 3. Test OTP Email

### Cara 1: Test Email Page (Paling Mudah)

1. **Akses:** `http://localhost/optima1/public/test_otp_email.php`
2. **Masukkan email** yang ingin di-test
3. **Klik "Send Test Email"**
4. **Cek email inbox** (dan folder spam)

**Fitur:**
- ✅ Generate OTP code otomatis
- ✅ Tampilkan OTP code di halaman (untuk testing)
- ✅ Show email configuration
- ✅ Debug info jika email gagal

### Cara 2: Test via Login (Real Flow)

1. **Aktifkan OTP** untuk user Anda (via profile page)
2. **Logout** dari aplikasi
3. **Login lagi** dengan user tersebut
4. **Setelah password benar**, sistem akan:
   - Redirect ke halaman verifikasi OTP
   - Kirim email OTP ke email user
   - Tampilkan info: "Kode OTP telah dikirim ke email Anda"
5. **Cek email inbox** (dan folder spam)
6. **Masukkan OTP code** dari email
7. **Verifikasi** - Login selesai

---

## 4. Troubleshooting Email

### Email Tidak Terkirim

**Cek:**
1. ✅ Email configuration di `app/Config/Email.php`
2. ✅ SMTP credentials (jika menggunakan SMTP)
3. ✅ Firewall tidak block SMTP port (587/465/25)
4. ✅ Gmail App Password (jika menggunakan Gmail)
5. ✅ Email masuk ke spam folder

**Debug:**
```php
// Check di test_otp_email.php atau di controller
$email = \Config\Services::email();
$email->send();

// Debug output
print_r($email->printDebugger(['headers', 'subject', 'body']));
```

**Check Logs:**
- Lihat di `writable/logs/log-*.php`
- Cari error messages terkait email

### OTP Tidak Diterima

**Cek:**
1. ✅ Email address benar di database
2. ✅ Email configuration sudah setup
3. ✅ OTP table sudah dibuat (migration sudah run)
4. ✅ Check spam folder
5. ✅ Email server tidak down

**Test:**
- Gunakan `test_otp_email.php` untuk test email sending
- Cek apakah email configuration benar

---

## 5. Testing Checklist

### ✅ Setup
- [ ] Email configuration diisi (`app/Config/Email.php`)
- [ ] Test email sending via `test_otp_email.php`
- [ ] Email diterima (cek inbox dan spam)

### ✅ Enable OTP
- [ ] Aktifkan OTP via profile page
- [ ] Status OTP berubah menjadi "Aktif"
- [ ] Badge hijau muncul

### ✅ Test Login dengan OTP
- [ ] Logout dari aplikasi
- [ ] Login dengan user yang OTP enabled
- [ ] Setelah password benar, redirect ke verify OTP page
- [ ] Email OTP diterima
- [ ] Masukkan OTP code
- [ ] Verifikasi berhasil
- [ ] Login selesai, redirect ke welcome page

### ✅ Test OTP Features
- [ ] OTP expire setelah 5 menit
- [ ] Max 3 percobaan OTP salah
- [ ] Resend OTP dengan cooldown 60 detik
- [ ] Paste OTP code (auto-submit)
- [ ] Nonaktifkan OTP via profile page
- [ ] Login normal tanpa OTP setelah dinonaktifkan

---

## 6. Quick Commands

### Enable OTP untuk User via SQL

```sql
-- Enable OTP untuk user ID 1
UPDATE users 
SET otp_enabled = 1, 
    otp_enabled_at = NOW() 
WHERE id = 1;

-- Enable OTP untuk semua admin users
UPDATE users 
SET otp_enabled = 1, 
    otp_enabled_at = NOW() 
WHERE is_super_admin = 1;

-- Disable OTP untuk semua users
UPDATE users 
SET otp_enabled = 0, 
    otp_enabled_at = NULL;
```

### Check OTP Status

```sql
-- List users dengan OTP enabled
SELECT id, username, email, otp_enabled, otp_enabled_at 
FROM users 
WHERE otp_enabled = 1;

-- List semua users dengan status OTP
SELECT id, username, email, 
       CASE WHEN otp_enabled = 1 THEN 'Enabled' ELSE 'Disabled' END as otp_status,
       otp_enabled_at
FROM users;
```

---

## 7. Email Configuration Examples

### Gmail SMTP

```php
public string $fromEmail  = 'yourname@gmail.com';
public string $fromName   = 'OPTIMA System';
public string $protocol = 'smtp';
public string $SMTPHost = 'smtp.gmail.com';
public string $SMTPUser = 'yourname@gmail.com';
public string $SMTPPass = 'xxxx xxxx xxxx xxxx';  // App Password dari Google
public int $SMTPPort = 587;
public string $SMTPCrypto = 'tls';
```

### Mailtrap (Testing)

```php
public string $fromEmail  = 'test@optima.local';
public string $fromName   = 'OPTIMA System';
public string $protocol = 'smtp';
public string $SMTPHost = 'smtp.mailtrap.io';
public string $SMTPUser = 'your-username';
public string $SMTPPass = 'your-password';
public int $SMTPPort = 2525;
public string $SMTPCrypto = 'tls';
```

### Local Mail (Development)

```php
public string $fromEmail  = 'noreply@optima.local';
public string $fromName   = 'OPTIMA System';
public string $protocol = 'mail';
public string $mailPath = '/usr/sbin/sendmail';
```

---

**Last Updated:** <?= date('Y-m-d H:i:s') ?>

