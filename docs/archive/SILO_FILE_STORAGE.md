# SILO File Storage Documentation

## 📁 Lokasi Penyimpanan File

File dokumen SILO (PJK3 dan SILO) disimpan di **filesystem**, bukan di database.

### Struktur Direktori:
```
public/
└── uploads/
    └── silo/
        ├── pjk3/          # File Surat Keterangan PJK3
        │   ├── pjk3_1_1234567890.pdf
        │   ├── pjk3_2_1234567891.jpg
        │   └── ...
        └── silo/          # File SILO
            ├── silo_1_1234567890.pdf
            ├── silo_2_1234567891.jpg
            └── ...
```

## 💾 Mengapa Filesystem, Bukan Database?

### ✅ **Keuntungan Filesystem:**
1. **Performance Lebih Baik**
   - File besar (hingga 15MB) tidak membebani database
   - Query database tetap cepat
   - Backup database tidak terlalu besar

2. **Mudah Dikelola**
   - File bisa diakses langsung via URL
   - Mudah untuk backup/restore file
   - Bisa menggunakan CDN untuk file besar

3. **Efisien Storage**
   - Database hanya menyimpan path/URL file (string pendek)
   - File disimpan di filesystem yang bisa di-scale terpisah

### ❌ **Jika Disimpan di Database (BLOB):**
- Database akan sangat besar dan lambat
- Backup database memakan waktu lama
- Query menjadi lambat karena harus load file besar
- Tidak efisien untuk file > 5MB

## 🔧 Konfigurasi Saat Ini

### **Max File Size:**
- **15 MB** (15,360 KB) per file
- Dikonfigurasi di: `app/Controllers/Perizinan.php` line 430

### **Format File yang Diizinkan:**
- PDF (`.pdf`)
- Image: JPG, JPEG, PNG (`.jpg`, `.jpeg`, `.png`)

### **Nama File:**
Format: `{type}_{silo_id}_{timestamp}.{ext}`
- Contoh: `pjk3_1_1703123456.pdf`
- Contoh: `silo_1_1703123456.jpg`

## 📊 Database Schema

Database hanya menyimpan **path/URL** file, bukan file itu sendiri:

```sql
-- Kolom di tabel `silo`
file_surat_keterangan_pjk3 VARCHAR(255)  -- Path: 'uploads/silo/pjk3/pjk3_1_1234567890.pdf'
file_silo VARCHAR(255)                   -- Path: 'uploads/silo/silo/silo_1_1234567890.pdf'
```

## 🔐 Security

### **.htaccess Protection:**
File `.htaccess` di `public/uploads/silo/` mencegah:
- Eksekusi PHP langsung
- Directory listing
- Akses tidak sah

```apache
# public/uploads/silo/.htaccess
<FilesMatch "\.php$">
    Order Allow,Deny
    Deny from all
</FilesMatch>
```

## 📥 Upload Process

1. User upload file via form
2. File divalidasi (type, size)
3. File disimpan ke: `public/uploads/silo/{type}/`
4. Path file disimpan ke database
5. File bisa diakses via URL: `/perizinan/download-file/{id}/{type}`

## 🔄 Backup Strategy

### **Database Backup:**
- Backup tabel `silo` (termasuk path file)
- Backup kecil dan cepat

### **File Backup:**
- Backup folder `public/uploads/silo/`
- Bisa dilakukan terpisah dari database
- Bisa menggunakan rsync, cloud storage, dll

## 📈 Scaling Considerations

### **Jika File Semakin Banyak:**
1. **Cloud Storage** (S3, Google Cloud Storage)
   - Pindahkan file ke cloud
   - Update path di database ke URL cloud

2. **CDN** (Content Delivery Network)
   - Serve file via CDN untuk akses cepat
   - Reduce server load

3. **File Compression**
   - Compress PDF/image sebelum upload
   - Reduce storage space

## 🛠️ Maintenance

### **Cleanup Old Files:**
Jika perlu menghapus file lama:
```bash
# Hapus file yang tidak ada di database
# (Script cleanup bisa dibuat terpisah)
```

### **Check File Integrity:**
```php
// Check apakah file masih ada
if (!file_exists(FCPATH . $silo['file_silo'])) {
    // File hilang, handle error
}
```

## 📝 Summary

- ✅ File disimpan di **filesystem** (`public/uploads/silo/`)
- ✅ Database hanya menyimpan **path** file
- ✅ Max size: **15 MB** per file
- ✅ Format: PDF, JPG, JPEG, PNG
- ✅ Secure dengan `.htaccess`
- ✅ Mudah di-backup dan di-scale

**Kesimpulan:** Penyimpanan di filesystem adalah pilihan yang tepat untuk file besar seperti dokumen SILO (hingga 15MB). Database tetap ringan dan performa tetap optimal.

