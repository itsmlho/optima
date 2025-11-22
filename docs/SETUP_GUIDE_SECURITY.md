# Setup Guide - Security Features

## Overview
Panduan setup untuk fitur keamanan yang sudah diimplementasikan.

---

## 1. Perbedaan OTP vs Email Verification

### OTP saat Login (Yang Sudah Diimplementasikan)
- **Status:** ✅ Optional (per user)
- **Cara kerja:** Jika user memiliki `otp_enabled = 1`, setelah masuk password benar, sistem kirim OTP ke email
- **Default:** User **TIDAK** wajib menggunakan OTP (kecuali diaktifkan)
- **Enable OTP:** Set `otp_enabled = 1` di database untuk user tertentu

### Email Verification saat Registrasi
- **Status:** ❌ Belum diimplementasikan
- **Cara kerja:** Setelah register, user harus klik link verifikasi di email sebelum bisa login
- **Ini berbeda** dengan OTP saat login

---

## 2. OTP - Optional atau Wajib?

### Current Implementation: **OPTIONAL** (per user)

**Cara kerja saat ini:**
- Default: User login **tanpa OTP** (normal login)
- Jika `otp_enabled = 1`: User login **dengan OTP** (2-step verification)

**Enable OTP untuk user tertentu:**
```sql
UPDATE users SET otp_enabled = 1, otp_enabled_at = NOW() WHERE id = [user_id];
```

**Enable OTP untuk semua user (jika ingin wajibkan):**
```sql
UPDATE users SET otp_enabled = 1, otp_enabled_at = NOW() WHERE otp_enabled = 0;
```

### Jika Ingin Wajibkan OTP untuk Semua User

Kita bisa modifikasi login flow untuk **wajibkan OTP** untuk semua user. Tapi perlu pertimbangan:
- ✅ **Keuntungan:** Lebih secure
- ❌ **Kekurangan:** User harus check email setiap login (kurang user-friendly)
- 💡 **Rekomendasi:** Gunakan OTP **optional**, aktifkan hanya untuk user penting (admin, finance, dll)

---

## 3. Setup Steps

### Step 1: Fix Database Configuration (Jika Error PDO)

Jika ada error `Undefined constant PDO::MYSQL_ATTR_INIT_COMMAND`, cek PHP version:
```bash
php -v
```

Pastikan PHP >= 7.4 dan PDO MySQL extension enabled:
```bash
php -m | grep pdo_mysql
```

Jika tidak ada, install:
```bash
# Ubuntu/Debian
sudo apt-get install php-mysql

# CentOS/RHEL
sudo yum install php-mysql
```

### Step 2: Konfigurasi Email

Edit `app/Config/Email.php`:

```php
<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    public string $fromEmail  = 'itsupport@sml.co.id';  // Ganti dengan email Anda
    public string $fromName   = 'OPTIMA System';           // Nama pengirim
    public string $recipients = '';
    public string $userAgent = 'CodeIgniter';
    
    // Pilih salah satu metode:
    
    // // Opsi 1: Mail (Default - menggunakan mail() PHP)
    // public string $protocol = 'mail';
    // public string $mailPath = '/usr/sbin/sendmail';
    
   |
}
```

### Step 3: Run Migrations

**Check migration status:**
```bash
php spark migrate:status
```

**Run all migrations:**
```bash
php spark migrate
```

**Atau run specific migration:**
```bash
php spark migrate -version 20251122100000  # OTP table
php spark migrate -version 20251122110000  # Login attempts table
php spark migrate -version 20251122120000  # User sessions table
php spark migrate -version 20251122130000  # Password resets table
php spark migrate -version 20251122140000  # Add OTP columns to users
```

**Manual migration (jika spark migrate error):**

Buka file SQL migration di `app/Database/Migrations/` dan run langsung di phpMyAdmin atau MySQL client.

### Step 4: Test Email Configuration

**Test email sending:**
```bash
php spark email:test your-email@example.com
```

Atau buat file test: `public/test_email.php`:

```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';

$email = \Config\Services::email();

$email->setFrom('noreply@yourdomain.com', 'OPTIMA Test');
$email->setTo('your-email@example.com');
$email->setSubject('Test Email from OPTIMA');
$email->setMessage('This is a test email. If you receive this, email configuration is working!');

if ($email->send()) {
    echo "Email sent successfully!";
} else {
    echo "Email failed: " . $email->printDebugger(['headers']);
}
```

Akses: `http://localhost/optima1/public/test_email.php`

### Step 5: Enable OTP untuk User (Optional)

**Enable untuk user tertentu:**
```sql
UPDATE users 
SET otp_enabled = 1, 
    otp_enabled_at = NOW() 
WHERE id = [user_id];
```

**Enable untuk semua user (jika ingin wajibkan):**
```sql
UPDATE users 
SET otp_enabled = 1, 
    otp_enabled_at = NOW() 
WHERE otp_enabled IS NULL OR otp_enabled = 0;
```

**Check user yang sudah enable OTP:**
```sql
SELECT id, username, email, otp_enabled, otp_enabled_at 
FROM users 
WHERE otp_enabled = 1;
```

### Step 6: Test Security Features

Ikuti testing checklist di `docs/TESTING_CHECKLIST_SECURITY.md`:

1. ✅ Test Rate Limiting
2. ✅ Test OTP (jika enabled)
3. ✅ Test Forgot Password
4. ✅ Test Session Management

---

## 4. Configuration Options

### AuthSecurity Configuration

Edit `app/Config/AuthSecurity.php` untuk customize:

```php
// OTP Settings
public int $otpLength = 6;                    // Panjang OTP code (default: 6)
public int $otpExpireMinutes = 5;             // OTP expire time (default: 5 menit)
public int $otpMaxAttempts = 3;               // Max percobaan OTP (default: 3)
public int $otpResendCooldownSeconds = 60;    // Cooldown resend OTP (default: 60 detik)

// Rate Limiting Settings
public int $maxLoginAttempts = 5;             // Max percobaan login (default: 5)
public int $lockDurationMinutes = 15;         // Lock duration (default: 15 menit)
public bool $attemptsResetAfterSuccess = true; // Reset counter setelah login sukses

// Session Settings
public int $sessionIdleTimeoutHours = 2;      // Idle timeout (default: 2 jam)
public bool $allowMultipleSessions = true;    // Allow multiple sessions (default: true)
public int $maxActiveSessions = 10;           // Max active sessions (default: 10)
public bool $autoLogoutIdleSessions = true;   // Auto logout idle sessions (default: true)
public bool $trackDevices = true;             // Track device info (default: true)
```

---

## 5. Troubleshooting

### Error: Email Not Sending

**Cek:**
1. Email configuration di `app/Config/Email.php`
2. SMTP credentials (jika menggunakan SMTP)
3. Firewall blocking SMTP port
4. Check logs: `writable/logs/log-*.php`

**Debug email:**
```php
$email = \Config\Services::email();
$email->setFrom('noreply@yourdomain.com', 'Test');
$email->setTo('test@example.com');
$email->setSubject('Test');
$email->setMessage('Test message');
$email->send();

// Debug output
print_r($email->printDebugger(['headers', 'subject', 'body']));
```

### Error: OTP Not Received

**Cek:**
1. Email configuration
2. Email masuk ke spam folder
3. OTP table di database (run migration)
4. Check logs untuk error

### Error: Session Not Tracking

**Cek:**
1. `trackDevices = true` di `app/Config/AuthSecurity.php`
2. `user_sessions` table exists (run migration)
3. Check logs untuk error

---

## 6. Production Deployment Checklist

- [ ] Email configuration setup (SMTP recommended)
- [ ] Run all migrations
- [ ] Test email sending
- [ ] Enable OTP untuk admin users (recommended)
- [ ] Test all security features
- [ ] Configure rate limiting (adjust if needed)
- [ ] Monitor logs
- [ ] Backup database before migration

---

## 7. Best Practices

### OTP Usage
- ✅ Enable OTP untuk admin, finance, dan user dengan akses penting
- ❌ Jangan wajibkan OTP untuk semua user (kurang user-friendly)
- ✅ Gunakan OTP sebagai optional 2FA

### Rate Limiting
- ✅ Monitor failed login attempts
- ✅ Adjust rate limiting jika terlalu strict/longgar
- ✅ Consider whitelist IP untuk admin

### Session Management
- ✅ Monitor active sessions
- ✅ Review session activity regularly
- ✅ Logout inactive sessions automatically

---

**Last Updated:** <?= date('Y-m-d H:i:s') ?>

