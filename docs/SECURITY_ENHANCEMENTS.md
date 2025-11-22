# Rekomendasi Peningkatan Keamanan Sistem Login

## Ringkasan Sistem Saat Ini
- ✅ Username/Email + Password
- ✅ Password Hashing (bcrypt)
- ✅ Remember Me Token
- ✅ Activity Logging
- ✅ Password Reset via Email

---

## Opsi Peningkatan Keamanan

### 1. **OTP (One-Time Password)** ⭐⭐⭐
**Level Kesulitan:** Sedang  
**Biaya:** Rendah-Sedang  
**Keselamatan:** Tinggi

#### A. OTP via Email
- ✅ Implementasi termudah
- ✅ Tidak perlu integrasi eksternal
- ✅ Cocok untuk internal company system
- ❌ Kurang aman dari SMS (email bisa di-hack)

#### B. OTP via SMS
- ✅ Lebih aman dari email
- ✅ User familiar dengan SMS
- ❌ Perlu integrasi provider (Twilio, Nexmo, dll)
- ❌ Ada biaya per SMS

#### C. OTP via WhatsApp
- ✅ Populer di Indonesia
- ✅ Gratis untuk notifikasi bisnis
- ❌ Perlu integrasi WhatsApp Business API
- ❌ Setup lebih kompleks

**Rekomendasi untuk OPTIMA:**
- **Tahap 1:** OTP via Email (implementasi cepat)
- **Tahap 2:** OTP via SMS (jika budget memungkinkan)

---

### 2. **TOTP (Time-based One-Time Password)** ⭐⭐⭐⭐⭐
**Level Kesulitan:** Sedang-Tinggi  
**Biaya:** Gratis  
**Keselamatan:** Sangat Tinggi

#### Aplikasi Authenticator:
- Google Authenticator
- Microsoft Authenticator
- Authy
- 1Password
- LastPass

**Keuntungan:**
- ✅ Tidak perlu internet untuk generate code
- ✅ Gratis (tidak ada biaya per transaksi)
- ✅ Sangat aman (kode berubah setiap 30 detik)
- ✅ Standard industri (RFC 6238)
- ✅ Bisa digunakan offline

**Cara Kerja:**
1. User enable 2FA di profile
2. Sistem generate QR code dengan secret key
3. User scan QR code dengan aplikasi authenticator
4. Setiap login, user masukkan kode 6 digit dari aplikasi

**Rekomendasi untuk OPTIMA:**
- ✅ **Paling direkomendasikan** untuk internal company system
- ✅ Balance antara keamanan dan kemudahan
- ✅ Tidak ada biaya berulang

---

### 3. **Security Features Lainnya**

#### A. Rate Limiting
- Batasi percobaan login (contoh: 5x dalam 15 menit)
- Lock akun sementara setelah banyak percobaan gagal
- Mencegah brute force attack

#### B. IP Whitelisting
- Untuk admin/super admin
- Hanya allow login dari IP tertentu
- Sangat aman tapi kurang fleksibel

#### C. Device Fingerprinting
- Track device yang digunakan login
- Notifikasi saat login dari device baru
- Optional: force 2FA untuk device baru

#### D. Session Management
- Multiple sessions (bisa login dari beberapa device)
- Force logout dari semua device
- Auto logout setelah idle

#### E. Password Policy Enhancement
- Minimum 8 karakter (sudah ada)
- Harus mengandung huruf besar, kecil, angka, simbol
- Password history (tidak boleh sama dengan 5 password terakhir)
- Force change password setiap X hari

---

## Rekomendasi Implementasi (Prioritas)

### **Fase 1: Quick Wins** (1-2 minggu)
1. ✅ **Rate Limiting** untuk login
2. ✅ **Password Policy** yang lebih ketat
3. ✅ **OTP via Email** (optional, bisa skip ke TOTP)

### **Fase 2: 2FA Core** (2-3 minggu) ⭐ **PRIORITAS TINGGI**
1. ✅ **TOTP (Google Authenticator)**
   - Enable/disable 2FA di profile
   - QR code setup
   - Backup codes
   - Force 2FA untuk role tertentu

### **Fase 3: Advanced Security** (1-2 minggu)
1. ✅ **Device Management**
   - Track devices
   - Notifikasi device baru
2. ✅ **Session Management**
   - Multiple sessions
   - Force logout all devices
3. ✅ **IP Tracking & Logging**
   - Log IP address setiap login
   - Alert untuk IP mencurigakan

### **Fase 4: Optional** (jika diperlukan)
1. ✅ **SMS OTP** (jika budget memungkinkan)
2. ✅ **IP Whitelisting** (untuk admin)

---

## Database Schema Changes

### Table: `users`
```sql
ALTER TABLE `users` 
ADD COLUMN `two_factor_enabled` TINYINT(1) DEFAULT 0,
ADD COLUMN `two_factor_secret` VARCHAR(255) NULL,
ADD COLUMN `two_factor_backup_codes` TEXT NULL,
ADD COLUMN `two_factor_enabled_at` DATETIME NULL;
```

### Table: `user_devices` (baru)
```sql
CREATE TABLE `user_devices` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `device_id` VARCHAR(255) NOT NULL,
  `device_name` VARCHAR(255) NULL,
  `device_type` VARCHAR(50) NULL, -- 'desktop', 'mobile', 'tablet'
  `user_agent` TEXT NULL,
  `ip_address` VARCHAR(45) NULL,
  `last_used_at` DATETIME NULL,
  `is_trusted` TINYINT(1) DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_device` (`user_id`, `device_id`),
  INDEX `idx_user_id` (`user_id`)
);
```

### Table: `login_attempts` (baru, untuk rate limiting)
```sql
CREATE TABLE `login_attempts` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `identifier` VARCHAR(255) NOT NULL, -- username atau email
  `ip_address` VARCHAR(45) NOT NULL,
  `attempts` INT UNSIGNED DEFAULT 1,
  `locked_until` DATETIME NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_identifier` (`identifier`),
  INDEX `idx_ip` (`ip_address`)
);
```

---

## Library/Packages yang Dibutuhkan

### Untuk TOTP:
```bash
composer require sonata-project/google-authenticator
# atau
composer require pragmarx/google2fa
```

### Untuk SMS OTP (jika diperlukan):
```bash
composer require twilio/sdk
# atau
composer require vonage/client
```

---

## Estimasi Waktu & Biaya

| Fitur | Waktu | Biaya | Prioritas |
|-------|-------|-------|-----------|
| Rate Limiting | 3-5 hari | Gratis | Tinggi |
| Password Policy | 2-3 hari | Gratis | Tinggi |
| TOTP (2FA) | 5-7 hari | Gratis | Sangat Tinggi |
| Device Management | 3-5 hari | Gratis | Sedang |
| Session Management | 2-3 hari | Gratis | Sedang |
| SMS OTP | 5-7 hari | ~Rp 150/SMS | Rendah |

**Total Estimasi:** 2-3 minggu untuk implementasi core features

---

## Pertanyaan untuk Diskusi

1. **Prioritas Fitur:**
   - Apakah TOTP (Google Authenticator) sudah cukup?
   - Apakah perlu SMS OTP juga?

2. **Budget:**
   - Apakah ada budget untuk SMS provider?
   - Atau prefer solusi gratis (TOTP)?

3. **Kebijakan:**
   - Apakah 2FA wajib untuk semua user?
   - Atau optional tapi recommended?
   - Force 2FA untuk admin/super admin saja?

4. **User Experience:**
   - Apakah user internal sudah familiar dengan authenticator app?
   - Apakah perlu training untuk user?

---

## Langkah Selanjutnya

Setelah diskusi dan keputusan, saya bisa mulai implementasi:
1. ✅ Setup database schema
2. ✅ Implementasi TOTP library
3. ✅ Create UI untuk enable/disable 2FA
4. ✅ Modify login flow untuk support 2FA
5. ✅ Testing & refinement

