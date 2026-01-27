# Panduan Step-by-Step: Setup Gmail App Password untuk OTP Email

## 📧 Apa itu Gmail App Password?

Gmail App Password adalah password khusus yang digunakan untuk aplikasi yang ingin mengakses Gmail Anda. Password ini berbeda dari password Gmail biasa Anda dan lebih aman.

---

## 🎯 Step-by-Step: Dapatkan Gmail App Password

### **Step 1: Login ke Google Account**

1. Buka browser dan kunjungi: **https://myaccount.google.com**
2. **Login** dengan akun Gmail Anda

---

### **Step 2: Enable 2-Step Verification**

**Penting:** App Password hanya bisa dibuat jika 2-Step Verification sudah aktif.

1. Di halaman Google Account, klik **"Security"** (di sidebar kiri)
2. Scroll ke bagian **"How you sign in to Google"**
3. Cari **"2-Step Verification"**
4. Jika status masih **"Off"**:
   - Klik **"2-Step Verification"**
   - Klik **"Get Started"**
   - Ikuti langkah-langkah untuk setup 2-Step Verification
   - Anda akan diminta:
     - Verifikasi nomor telepon
     - Masukkan kode verifikasi yang dikirim via SMS
     - Konfirmasi setup

**Note:** Pastikan 2-Step Verification sudah **"On"** sebelum lanjut ke step berikutnya.

---

### **Step 3: Buat App Password**

Setelah 2-Step Verification aktif:

1. Kembali ke halaman **"Security"** di Google Account
2. Scroll ke bagian **"How you sign in to Google"**
3. Cari **"2-Step Verification"** → klik
4. Scroll ke bawah, cari **"App passwords"**
5. Klik **"App passwords"**
6. Anda akan diminta login lagi untuk verifikasi
7. Di halaman **"App passwords"**:
   - Pilih **"Mail"** dari dropdown "Select app"
   - Pilih **"Other (Custom name)"** dari dropdown "Select device"
   - Masukkan nama: **"OPTIMA System"** (atau nama lain)
   - Klik **"Generate"**
8. **Gmail akan menampilkan App Password 16 karakter**
   - Format: `xxxx xxxx xxxx xxxx` (dengan spasi) atau `xxxxxxxxxxxxxxxx` (tanpa spasi)
   - **COPY PASSWORD INI SEKARANG!** (Anda tidak akan bisa melihatnya lagi setelah ini)

---

### **Step 4: Setup di Aplikasi OPTIMA**

1. **Buka file:** `app/Config/Email.php`

2. **Edit konfigurasi email:**

```php
<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    // From Email (Gmail Anda)
    public string $fromEmail  = 'your-email@gmail.com';  // Ganti dengan Gmail Anda
    public string $fromName   = 'OPTIMA System';         // Nama pengirim
    public string $recipients = '';

    // Protocol
    public string $protocol = 'smtp';  // Gunakan SMTP

    // SMTP Configuration untuk Gmail
    public string $SMTPHost = 'smtp.gmail.com';  // Gmail SMTP host
    public string $SMTPUser = 'your-email@gmail.com';  // Gmail Anda (sama dengan fromEmail)
    public string $SMTPPass = 'xxxx xxxx xxxx xxxx';  // App Password 16 karakter (tanpa spasi atau dengan spasi)
    public int $SMTPPort = 587;  // Gmail SMTP port
    public string $SMTPCrypto = 'tls';  // TLS encryption
    public int $SMTPTimeout = 60;  // Timeout dalam detik
    
    // Other settings
    public string $userAgent = 'CodeIgniter';
    public string $mailPath = '/usr/sbin/sendmail';
    public bool $SMTPKeepAlive = false;
    
    // Email validation
    public bool $validateEmail = true;
    
    // Word wrap
    public int $wordWrap = 76;
    public string $mailType = 'html';  // HTML email
    public string $charset = 'utf-8';
    public bool $BCCBatchMode = false;
    public int $BCCBatchSize = 200;
    public string $priority = '3';  // Normal priority
    
    // ... rest of config
}
```

3. **Ganti nilai berikut:**
   - `$fromEmail` → Gmail Anda (contoh: `admin@optima.com` jika menggunakan Gmail)
   - `$SMTPUser` → Gmail Anda (sama dengan fromEmail)
   - `$SMTPPass` → App Password 16 karakter yang sudah Anda copy

**Contoh:**
```php
public string $fromEmail  = 'admin.optima@gmail.com';
public string $SMTPUser = 'admin.optima@gmail.com';
public string $SMTPPass = 'abcd efgh ijkl mnop';  // App Password (bisa dengan atau tanpa spasi)
```

---

### **Step 5: Test Email**

1. **Buka:** `http://localhost/optima1/public/test_otp_email.php`
2. **Masukkan email** yang ingin di-test
3. **Klik "Send Test Email"**
4. **Cek email inbox** (dan folder spam)

**Jika berhasil:**
- ✅ Anda akan menerima email OTP
- ✅ OTP code akan tampil di halaman test

**Jika gagal:**
- ❌ Cek kembali App Password (pastikan sudah copy dengan benar)
- ❌ Cek apakah 2-Step Verification sudah aktif
- ❌ Pastikan firewall tidak block port 587
- ❌ Cek logs di `writable/logs/` untuk error details

---

## ⚠️ Troubleshooting

### **Error: "Username and Password not accepted"**

**Penyebab:**
- App Password salah atau belum dibuat
- 2-Step Verification belum aktif

**Solusi:**
1. Pastikan 2-Step Verification sudah aktif
2. Buat ulang App Password
3. Copy App Password dengan benar (16 karakter)
4. Pastikan tidak ada spasi ekstra di awal/akhir

---

### **Error: "Connection timed out"**

**Penyebab:**
- Firewall block port 587
- Koneksi internet bermasalah
- Gmail SMTP server down

**Solusi:**
1. Cek firewall settings (allow port 587)
2. Test koneksi internet
3. Coba lagi beberapa saat kemudian

---

### **Error: "Could not authenticate"**

**Penyebab:**
- App Password salah
- Gmail account di-lock

**Solusi:**
1. Pastikan App Password benar
2. Cek status Gmail account di https://myaccount.google.com/security
3. Buat App Password baru

---

## 📝 Catatan Penting

1. **Jangan share App Password** dengan siapa pun
2. **Jangan commit App Password** ke Git (gunakan `.env` untuk production)
3. **App Password hanya tampil sekali** saat dibuat - copy segera!
4. **Jika lupa App Password**, hapus yang lama dan buat yang baru
5. **App Password berbeda dari password Gmail** biasa Anda

---

## 🎉 Selesai!

Setelah setup selesai:
- ✅ OTP email bisa dikirim via Gmail
- ✅ User bisa aktifkan OTP di profile page
- ✅ User akan menerima email OTP saat login

**Next Steps:**
1. Test email via `test_otp_email.php`
2. Aktifkan OTP di profile page (`/profile`)
3. Test login dengan OTP

---

**Last Updated:** <?= date('Y-m-d H:i:s') ?>

